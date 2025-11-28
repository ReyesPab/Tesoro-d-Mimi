<?php

namespace App\models\modulo_ventas;

use App\db\connectionDB;

class ventasModel
{
    /**
     * Obtener TODOS los productos de la BD (para filtrado en frontend)
     * Sincroniza con tbl_inventario_producto para obtener stock REAL
     * @return array
     */
    public static function obtenerTodosLosProductos()
    {
        try {
            $con = connectionDB::getConnection();
            
            // SINCRONIZAR: Crear registros de inventario para productos nuevos sin stock registrado
            self::sincronizarProductosSinInventario($con);
            
            // OBTENER TODOS usando tbl_inventario_producto como fuente de verdad
            $sql = "SELECT p.ID_PRODUCTO, 
                           p.NOMBRE, 
                           p.DESCRIPCION, 
                           p.PRECIO,
                           p.ID_UNIDAD_MEDIDA,
                           p.ESTADO,
                           COALESCE(ip.CANTIDAD, 0) as CANTIDAD,
                           COALESCE(ip.MINIMO, 0) as MINIMO,
                           COALESCE(ip.MAXIMO, 0) as MAXIMO
                    FROM tbl_producto p
                    INNER JOIN tbl_inventario_producto ip ON p.ID_PRODUCTO = ip.ID_PRODUCTO
                    WHERE p.ESTADO = 'ACTIVO'
                    ORDER BY p.NOMBRE ASC";
            
            $query = $con->prepare($sql);
            $query->execute();
            $productos = $query->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log("ventasModel::obtenerTodosLosProductos -> Productos cargados: " . count($productos));
            
            return $productos;
        } catch (\PDOException $e) {
            error_log("Error ventasModel::obtenerTodosLosProductos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * SINCRONIZAR: Crear registros de inventario para productos activos sin inventario registrado
     * @param \PDO $con - Conexión a BD
     * @return int - Cantidad de productos sincronizados
     */
    private static function sincronizarProductosSinInventario($con)
    {
        try {
            $sql = "INSERT INTO tbl_inventario_producto (ID_PRODUCTO, CANTIDAD, MINIMO, MAXIMO)
                    SELECT p.ID_PRODUCTO, 
                           COALESCE((SELECT CANTIDAD FROM SP_OBTENER_INVENTARIO_PRODUCTOS() sp WHERE sp.ID_PRODUCTO = p.ID_PRODUCTO LIMIT 1), 0) as CANTIDAD,
                           0, 0
                    FROM tbl_producto p
                    WHERE p.ESTADO = 'ACTIVO'
                    AND NOT EXISTS (
                        SELECT 1 FROM tbl_inventario_producto ip 
                        WHERE ip.ID_PRODUCTO = p.ID_PRODUCTO
                    )
                    LIMIT 1000";
            
            $query = $con->prepare($sql);
            $result = $query->execute();
            $count = $query->rowCount();
            
            if ($count > 0) {
                error_log("ventasModel::sincronizarProductosSinInventario -> Sincronizados $count productos nuevos sin inventario");
            }
            
            return $count;
        } catch (\PDOException $e) {
            // INTENTO 2: Si falla el SP, intentar sincronizar desde el SP directamente
            try {
                error_log("ventasModel::sincronizarProductosSinInventario -> SP falló, intentando sincronización directa desde SP_OBTENER_INVENTARIO_PRODUCTOS");
                
                $sql_sp = "CALL SP_OBTENER_INVENTARIO_PRODUCTOS(NULL, NULL)";
                $query_sp = $con->prepare($sql_sp);
                $query_sp->execute();
                $inventario_sp = $query_sp->fetchAll(\PDO::FETCH_ASSOC);
                
                $sincronizados = 0;
                foreach ($inventario_sp as $item) {
                    $id_producto = intval($item['ID_PRODUCTO'] ?? 0);
                    $cantidad = floatval($item['CANTIDAD'] ?? 0);
                    
                    if ($id_producto <= 0) continue;
                    
                    // Upsert: insertar si no existe, actualizar si existe
                    $check_sql = "SELECT COUNT(*) FROM tbl_inventario_producto WHERE ID_PRODUCTO = :id";
                    $check_q = $con->prepare($check_sql);
                    $check_q->execute([':id' => $id_producto]);
                    $existe = intval($check_q->fetchColumn()) > 0;
                    
                    if (!$existe) {
                        $insert_sql = "INSERT INTO tbl_inventario_producto (ID_PRODUCTO, CANTIDAD, MINIMO, MAXIMO) VALUES (:id, :cantidad, 0, 0)";
                        $insert_q = $con->prepare($insert_sql);
                        $insert_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
                        $sincronizados++;
                    } else {
                        $update_sql = "UPDATE tbl_inventario_producto SET CANTIDAD = :cantidad WHERE ID_PRODUCTO = :id";
                        $update_q = $con->prepare($update_sql);
                        $update_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
                        if ($update_q->rowCount() > 0) $sincronizados++;
                    }
                }
                
                error_log("ventasModel::sincronizarProductosSinInventario -> SP sincronización exitosa: $sincronizados productos");
                return $sincronizados;
                
            } catch (\Exception $e2) {
                // FALLBACK 3: Si todo falla, crear registros vacíos en tbl_inventario_producto para productos sin inventario
                try {
                    $sql = "INSERT INTO tbl_inventario_producto (ID_PRODUCTO, CANTIDAD, MINIMO, MAXIMO)
                            SELECT p.ID_PRODUCTO, 0, 0, 0
                            FROM tbl_producto p
                            WHERE p.ESTADO = 'ACTIVO'
                            AND NOT EXISTS (
                                SELECT 1 FROM tbl_inventario_producto ip 
                                WHERE ip.ID_PRODUCTO = p.ID_PRODUCTO
                            )";
                    
                    $query = $con->prepare($sql);
                    $result = $query->execute();
                    $count = $query->rowCount();
                    
                    error_log("ventasModel::sincronizarProductosSinInventario (fallback 3) -> Sincronizados $count productos");
                    return $count;
                } catch (\Exception $e3) {
                    error_log("ventasModel::sincronizarProductosSinInventario -> Todos los intentos fallaron: " . $e3->getMessage());
                    return 0;
                }
            }
        }
    }

    /**
     * Obtener productos por categoría (usando tbl_inventario_producto como fuente de verdad)
     * @param string $categoria - Productos de Maiz | Golosinas | Bebidas
     * @return array
     */
    public static function obtenerProductosPorCategoria($categoria)
    {
        try {
            $con = connectionDB::getConnection();
            
            // SINCRONIZAR: Asegurar que todos los productos tengan inventario
            self::sincronizarProductosSinInventario($con);
            
            // Mapear categoría a LIKE pattern (case-insensitive)
            $patrones = [
                'MAIZ' => '%maiz%',
                'GOLOSINAS' => '%golosina%',
                'BEBIDAS' => '%bebida%'
            ];
            
            $pattern = $patrones[strtoupper($categoria)] ?? '%';
            
            // USAR INNER JOIN para obtener solo productos CON inventario registrado
            $sql = "SELECT p.ID_PRODUCTO, 
                           p.NOMBRE, 
                           p.DESCRIPCION, 
                           p.PRECIO,
                           p.ID_UNIDAD_MEDIDA,
                           p.ESTADO,
                           COALESCE(ip.CANTIDAD, 0) as CANTIDAD,
                           COALESCE(ip.MINIMO, 0) as MINIMO,
                           COALESCE(ip.MAXIMO, 0) as MAXIMO
                    FROM tbl_producto p
                    INNER JOIN tbl_inventario_producto ip ON p.ID_PRODUCTO = ip.ID_PRODUCTO
                    WHERE p.ESTADO = 'ACTIVO' 
                    AND LOWER(p.NOMBRE) LIKE LOWER(:pattern)
                    ORDER BY p.NOMBRE ASC";
            
            $query = $con->prepare($sql);
            $query->execute([':pattern' => $pattern]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error ventasModel::obtenerProductosPorCategoria: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todas las categorías disponibles
     * @return array
     */
    public static function obtenerCategorias()
    {
        return [
            ['id' => 'MAIZ', 'nombre' => 'Productos de Maíz'],
            ['id' => 'GOLOSINAS', 'nombre' => 'Golosinas'],
            ['id' => 'BEBIDAS', 'nombre' => 'Bebidas']
        ];
    }

    /**
     * Obtener métodos de pago activos
     * @return array
     */
    public static function obtenerMetodosPago()
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT ID_METODO_PAGO, METODO_PAGO, DESCRIPCION 
                    FROM tbl_metodo_pago 
                    WHERE ESTADO = 'ACTIVO'
                    ORDER BY METODO_PAGO ASC";
            
            $query = $con->prepare($sql);
            $query->execute();
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error ventasModel::obtenerMetodosPago: " . $e->getMessage());
            return [];
        }
    }

    /**
     * PASO 1: Crear factura
     * @param array $datos - ID_USUARIO, ID_CLIENTE, ID_METODO_PAGO, TOTAL
     * @return array ['success' => bool, 'id_factura' => int|null, 'message' => string]
     */
    public static function crearFactura($datos)
    {
        try {
            $con = connectionDB::getConnection();
            // Determinar estado de la factura
            $estado = 'PAGADA';
            // Si el método de pago es tarjeta o transferencia y no hay comprobante, dejar como ACTIVA
            // Se espera que $datos['METODO_PAGO'] o $datos['ID_METODO_PAGO'] esté presente
            $metodo = strtolower($datos['METODO_PAGO'] ?? '');
            $id_metodo = $datos['ID_METODO_PAGO'] ?? null;
            // Si no hay comprobante (comprobante_subido = false/null)
            $comprobante_subido = $datos['COMPROBANTE_SUBIDO'] ?? false;
            // Métodos válidos (ajusta según tu BD: 'tarjeta', 'transferencia')
            if ((($metodo === 'tarjeta') || ($metodo === 'transferencia')) && !$comprobante_subido) {
                $estado = 'ACTIVA';
            }
            $sql = "INSERT INTO tbl_factura (ID_USUARIO, ID_CLIENTE, ID_METODO_PAGO, TOTAL_VENTA, FECHA_VENTA, ESTADO_FACTURA, CREADO_POR, FECHA_CREACION)
                    VALUES (:id_usuario, :id_cliente, :id_metodo_pago, :total, NOW(), :estado, :creado_por, NOW())";

            $query = $con->prepare($sql);
            $resultado = $query->execute([
                ':id_usuario' => $datos['ID_USUARIO'],
                ':id_cliente' => $datos['ID_CLIENTE'] ?? null,
                ':id_metodo_pago' => $datos['ID_METODO_PAGO'],
                ':total' => $datos['TOTAL'],
                ':estado' => $estado,
                ':creado_por' => $datos['CREADO_POR'] ?? $datos['ID_USUARIO']
            ]);

            if ($resultado) {
                return [
                    'success' => true,
                    'id_factura' => $con->lastInsertId(),
                    'message' => 'Factura creada'
                ];
            }
            return ['success' => false, 'message' => 'Error al crear factura'];

        } catch (\PDOException $e) {
            error_log("Error ventasModel::crearFactura: " . $e->getMessage());
            error_log("Datos enviados: " . print_r($datos, true));
            error_log("Error code: " . $e->getCode());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    /**
     * PASO 2: Verificar stock del producto
     * @param int $id_producto
     * @param int $cantidad
     * @return array ['disponible' => bool, 'cantidad_actual' => int, 'message' => string]
     */
    public static function verificarStock($id_producto, $cantidad)
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT CANTIDAD FROM tbl_inventario_producto WHERE ID_PRODUCTO = :id_producto";
            $query = $con->prepare($sql);
            $query->execute([':id_producto' => $id_producto]);
            $resultado = $query->fetch(\PDO::FETCH_ASSOC);

            if (!$resultado) {
                return ['disponible' => false, 'cantidad_actual' => 0, 'message' => 'Producto no existe en inventario'];
            }

            $cantidad_disponible = $resultado['CANTIDAD'];
            
            if ($cantidad_disponible < $cantidad) {
                return [
                    'disponible' => false,
                    'cantidad_actual' => $cantidad_disponible,
                    'message' => "Stock insuficiente. Disponible: {$cantidad_disponible}"
                ];
            }

            return [
                'disponible' => true,
                'cantidad_actual' => $cantidad_disponible,
                'message' => 'Stock disponible'
            ];

        } catch (\PDOException $e) {
            error_log("Error ventasModel::verificarStock: " . $e->getMessage());
            return ['disponible' => false, 'cantidad_actual' => 0, 'message' => 'Error al verificar stock'];
        }
    }

    /**
     * PASO 3: Registrar detalle de factura
     * @param array $datos - ID_FACTURA, ID_PRODUCTO, CANTIDAD, PRECIO_VENTA
     * @return array ['success' => bool, 'message' => string]
     */
    public static function registrarDetalleFactura($datos)
    {
        try {
            $con = connectionDB::getConnection();
            
            $subtotal = $datos['CANTIDAD'] * $datos['PRECIO_VENTA'];
            
            $sql = "INSERT INTO tbl_detalle_factura (ID_FACTURA, ID_PRODUCTO, CANTIDAD, PRECIO_VENTA, SUBTOTAL)
                    VALUES (:id_factura, :id_producto, :cantidad, :precio_venta, :subtotal)";
            
            $query = $con->prepare($sql);
            $resultado = $query->execute([
                ':id_factura' => $datos['ID_FACTURA'],
                ':id_producto' => $datos['ID_PRODUCTO'],
                ':cantidad' => $datos['CANTIDAD'],
                ':precio_venta' => $datos['PRECIO_VENTA'],
                ':subtotal' => $subtotal
            ]);

            return [
                'success' => $resultado,
                'message' => $resultado ? 'Detalle registrado' : 'Error al registrar detalle'
            ];

        } catch (\PDOException $e) {
            error_log("Error ventasModel::registrarDetalleFactura: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }

    /**
     * PASO 4: Registrar movimiento en cardex (SALIDA)
     * @param array $datos - ID_PRODUCTO, CANTIDAD, ID_FACTURA
     * @return array ['success' => bool, 'message' => string]
     */
    public static function registrarCardex($datos)
    {
        try {
            $con = connectionDB::getConnection();
            // Validar que el usuario provisto exista; si no existe, usar usuario por defecto (1)
            $id_usuario = isset($datos['ID_USUARIO']) ? intval($datos['ID_USUARIO']) : 1;
            try {
                $qUser = $con->prepare("SELECT COUNT(*) FROM tbl_ms_usuarios WHERE ID_USUARIO = :id");
                $qUser->execute([':id' => $id_usuario]);
                $count = intval($qUser->fetchColumn());
                if ($count === 0) {
                    error_log("ventasModel::registrarCardex -> Usuario $id_usuario no existe en tbl_ms_usuarios, usando fallback ID 1");
                    $id_usuario = 1;
                }
            } catch (\Exception $e) {
                // Si falla la verificación, seguir con el id que venga
                error_log("ventasModel::registrarCardex -> error verificando usuario: " . $e->getMessage());
            }

            // Usar columna DESCRIPCION para guardar la referencia (crédito factura)
            $descripcion = 'Venta - Factura #' . ($datos['ID_FACTURA'] ?? 'N/A');
            
            $sql = "INSERT INTO tbl_cardex_producto (ID_PRODUCTO, CANTIDAD, TIPO_MOVIMIENTO, DESCRIPCION, FECHA_MOVIMIENTO, ID_USUARIO)
                    VALUES (:id_producto, :cantidad, 'SALIDA', :descripcion, NOW(), :id_usuario)";
            
            $query = $con->prepare($sql);
            $resultado = $query->execute([
                ':id_producto' => $datos['ID_PRODUCTO'],
                ':cantidad' => $datos['CANTIDAD'],
                ':descripcion' => $descripcion,
                ':id_usuario' => $id_usuario
            ]);

            if (!$resultado) {
                $err = $query->errorInfo();
                error_log("ventasModel::registrarCardex -> execute returned false. errorInfo: " . print_r($err, true));
                error_log("ventasModel::registrarCardex -> datos: " . print_r($datos, true));
            }

            return [
                'success' => $resultado,
                'message' => $resultado ? 'Cardex registrado' : 'Error al registrar cardex'
            ];

        } catch (\PDOException $e) {
            error_log("Error ventasModel::registrarCardex: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }

    /**
     * PASO 5: Actualizar inventario (disminuir stock)
     * @param int $id_producto
     * @param int $cantidad
     * @return array ['success' => bool, 'message' => string]
     */
    public static function actualizarInventario($id_producto, $cantidad, $id_usuario = null)
    {
        try {
            $con = connectionDB::getConnection();
            
            // Actualizar solo CANTIDAD (sin campos de auditoría que podrían no existir)
            $sql = "UPDATE tbl_inventario_producto 
                    SET CANTIDAD = CANTIDAD - :cantidad
                    WHERE ID_PRODUCTO = :id_producto";
            
            $query = $con->prepare($sql);
            $resultado = $query->execute([
                ':cantidad' => $cantidad,
                ':id_producto' => $id_producto
            ]);

            return [
                'success' => $resultado,
                'message' => $resultado ? 'Inventario actualizado' : 'Error al actualizar inventario'
            ];

        } catch (\PDOException $e) {
            error_log("Error ventasModel::actualizarInventario: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }

    /**
     * Función principal: Crear venta completa (5 pasos)
     * @param array $venta - ID_USUARIO, ID_CLIENTE, ID_METODO_PAGO, TOTAL, ITEMS[]
     * @return array ['success' => bool, 'id_factura' => int|null, 'message' => string]
     */
    public static function crearVenta($venta)
    {
        try {
            $con = connectionDB::getConnection();
            $con->beginTransaction();

            // PASO 1: Crear factura
            $resultFactura = self::crearFactura([
                'ID_USUARIO' => $venta['ID_USUARIO'],
                'ID_CLIENTE' => $venta['ID_CLIENTE'],
                'ID_METODO_PAGO' => $venta['ID_METODO_PAGO'],
                'TOTAL' => $venta['TOTAL'],
                'CREADO_POR' => $venta['CREADO_POR'] ?? $venta['ID_USUARIO']
            ]);

            if (!$resultFactura['success']) {
                $con->rollBack();
                return ['success' => false, 'message' => 'Error creando factura'];
            }

            $id_factura = $resultFactura['id_factura'];

            // PASO 2, 3, 4, 5: Procesar cada item
            foreach ($venta['ITEMS'] as $item) {
                // Verificar stock
                $stockCheck = self::verificarStock($item['ID_PRODUCTO'], $item['CANTIDAD']);
                if (!$stockCheck['disponible']) {
                    $con->rollBack();
                    return ['success' => false, 'message' => 'Stock insuficiente para: ' . $item['NOMBRE']];
                }

                // Registrar detalle factura
                $resultDetalle = self::registrarDetalleFactura([
                    'ID_FACTURA' => $id_factura,
                    'ID_PRODUCTO' => $item['ID_PRODUCTO'],
                    'CANTIDAD' => $item['CANTIDAD'],
                    'PRECIO_VENTA' => $item['PRECIO']
                ]);

                if (!$resultDetalle['success']) {
                    $con->rollBack();
                    return ['success' => false, 'message' => 'Error registrando detalle de factura'];
                }

                // Registrar cardex (SALIDA)
                $resultCardex = self::registrarCardex([
                    'ID_PRODUCTO' => $item['ID_PRODUCTO'],
                    'CANTIDAD' => $item['CANTIDAD'],
                    'ID_FACTURA' => $id_factura,
                    'ID_USUARIO' => $venta['ID_USUARIO']
                ]);

                if (!$resultCardex['success']) {
                    $con->rollBack();
                    return ['success' => false, 'message' => 'Error registrando movimiento de inventario'];
                }

                // Actualizar inventario
                $resultInventario = self::actualizarInventario($item['ID_PRODUCTO'], $item['CANTIDAD']);

                if (!$resultInventario['success']) {
                    $con->rollBack();
                    return ['success' => false, 'message' => 'Error actualizando inventario'];
                }
            }

            // Commit de la transacción
            $con->commit();

            return [
                'success' => true,
                'id_factura' => $id_factura,
                'message' => 'Venta registrada exitosamente'
            ];

        } catch (\PDOException $e) {
            $con->rollBack();
            error_log("Error ventasModel::crearVenta: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la transacción de venta'];
        }
    }

    /**
     * Obtener detalles de una factura
     * @param int $id_factura
     * @return array
     */
    public static function obtenerDetallesFactura($id_factura)
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT df.*, p.NOMBRE as PRODUCTO_NOMBRE
                    FROM tbl_detalle_factura df
                    JOIN tbl_producto p ON df.ID_PRODUCTO = p.ID_PRODUCTO
                    WHERE df.ID_FACTURA = :id_factura
                    ORDER BY df.ID_DETALLE_FACTURA ASC";
            
            $query = $con->prepare($sql);
            $query->execute([':id_factura' => $id_factura]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error ventasModel::obtenerDetallesFactura: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener factura completa
     * @param int $id_factura
     * @return array|null
     */
    public static function obtenerFactura($id_factura)
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT f.*, 
                           COALESCE(c.NOMBRE, 'Sin Cliente') as CLIENTE_NOMBRE,
                           COALESCE(c.APELLIDO, '') as CLIENTE_APELLIDO,
                           COALESCE(c.DNI, 'N/A') as CLIENTE_DNI,
                           mp.METODO_PAGO,
                           u.NOMBRE_USUARIO as USUARIO_NOMBRE
                    FROM tbl_factura f
                    LEFT JOIN tbl_cliente c ON f.ID_CLIENTE = c.ID_CLIENTE
                    JOIN tbl_metodo_pago mp ON f.ID_METODO_PAGO = mp.ID_METODO_PAGO
                    JOIN tbl_ms_usuarios u ON f.ID_USUARIO = u.ID_USUARIO
                    WHERE f.ID_FACTURA = :id_factura";
            
            $query = $con->prepare($sql);
            $query->execute([':id_factura' => $id_factura]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error ventasModel::obtenerFactura: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar facturas recientes
     * @param int $limite - cantidad de registros a traer
     * @return array
     */
    public static function listarFacturasRecientes($limite = 20, $busqueda = null, $fecha = null)
    {
        try {
            $con = connectionDB::getConnection();
            $where = [];
            $params = [];

            if (!empty($busqueda)) {
                // Buscar por ID_FACTURA numérico o por nombre de cliente
                if (is_numeric($busqueda)) {
                    $where[] = 'f.ID_FACTURA = :idfactura';
                    $params[':idfactura'] = intval($busqueda);
                } else {
                    $where[] = 'LOWER(c.NOMBRE) LIKE :busqueda';
                    $params[':busqueda'] = '%' . strtolower($busqueda) . '%';
                }
            }

            if (!empty($fecha)) {
                // Esperamos fecha en formato YYYY-MM-DD
                $where[] = "DATE(f.FECHA_VENTA) = :fecha";
                $params[':fecha'] = $fecha;
            }

            $whereSql = '';
            if (!empty($where)) {
                $whereSql = 'WHERE ' . implode(' AND ', $where);
            }

                 $sql = "SELECT f.ID_FACTURA, 
                          f.TOTAL_VENTA,
                          f.FECHA_VENTA,
                          f.ESTADO_FACTURA,
                          COALESCE(c.NOMBRE, 'Sin Cliente') as CLIENTE_NOMBRE,
                          mp.METODO_PAGO as METODO_PAGO,
                          u.NOMBRE_USUARIO as USUARIO_NOMBRE,
                          COUNT(df.ID_DETALLE_FACTURA) as CANTIDAD_ITEMS
                      FROM tbl_factura f
                      LEFT JOIN tbl_cliente c ON f.ID_CLIENTE = c.ID_CLIENTE
                      JOIN tbl_metodo_pago mp ON f.ID_METODO_PAGO = mp.ID_METODO_PAGO
                      JOIN tbl_ms_usuarios u ON f.ID_USUARIO = u.ID_USUARIO
                      LEFT JOIN tbl_detalle_factura df ON f.ID_FACTURA = df.ID_FACTURA
                      $whereSql
                      GROUP BY f.ID_FACTURA, mp.METODO_PAGO
                      ORDER BY f.FECHA_VENTA DESC
                      LIMIT :limite";

            $query = $con->prepare($sql);
            foreach ($params as $k => $v) {
                $query->bindValue($k, $v);
            }
            $query->bindValue(':limite', $limite, \PDO::PARAM_INT);
            $query->execute();
            $facturas = $query->fetchAll(\PDO::FETCH_ASSOC);
            // Agregar flag comprobante_subido a cada factura
            foreach ($facturas as &$factura) {
                $ruta = self::buscarComprobantePorFactura($factura['ID_FACTURA']);
                $factura['COMPROBANTE_SUBIDO'] = $ruta !== null && is_file($ruta);
            }
            unset($factura);
            return $facturas;
        } catch (\PDOException $e) {
            error_log("Error ventasModel::listarFacturasRecientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registrar acción en bitácora del sistema
     * Integración con tbl_ms_bitacora para auditoría completa
     * @param int $idUsuario - ID del usuario que realiza la acción
     * @param string $accion - Tipo de acción (CREAR_VENTA, REGISTRAR_CLIENTE, etc.)
     * @param string $descripcion - Descripción detallada de la acción
     * @param int $idObjeto - ID_OBJETO de tbl_ms_objetos (VENTAS = correspondiente ID)
     * @param string $creadoPor - Usuario o sistema que registra la acción
     * @return bool - true si se registró exitosamente
     */
    public static function registrarBitacora($idUsuario, $accion, $descripcion, $idObjeto = null, $creadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            
            // Si no se especifica ID_OBJETO, obtener el de VENTAS
            if (!$idObjeto) {
                $sql_objeto = "SELECT ID_OBJETO FROM tbl_ms_objetos WHERE OBJETO = 'VENTAS' LIMIT 1";
                $query_objeto = $con->prepare($sql_objeto);
                $query_objeto->execute();
                $resultado_objeto = $query_objeto->fetch(\PDO::FETCH_ASSOC);
                $idObjeto = $resultado_objeto['ID_OBJETO'] ?? null;
            }
            
            // Registrar en bitácora
            $sql = "INSERT INTO TBL_MS_BITACORA (FECHA, ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR, FECHA_CREACION) 
                    VALUES (NOW(), :id_usuario, :id_objeto, :accion, :descripcion, :creado_por, NOW())";
            $query = $con->prepare($sql);
            $query->execute([
                ':id_usuario' => $idUsuario,
                ':id_objeto' => $idObjeto,
                ':accion' => $accion,
                ':descripcion' => $descripcion,
                ':creado_por' => $creadoPor
            ]);
            
            return true;
        } catch (\PDOException $e) {
            error_log("ventasModel::registrarBitacora -> " . $e->getMessage());
            // No lanzar excepción, solo registrar el error. La bitácora no debe afectar el flujo principal
            return false;
        }
    }

    /**
     * Generar nombre para el comprobante basado en ID factura, timestamp e ID cliente
     * Formato: {ID_FACTURA}_{YYYY-MM-DD_HH-MM-SS}_{ID_CLIENTE}.{ext}
     * @param int $id_factura
     * @param int $id_cliente
     * @param string $originalName
     * @return string
     */
    public static function generarNombreComprobante($id_factura, $id_cliente, $originalName)
    {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = date('Y-m-d_H-i-s');
        $filename = sprintf('%s_%s_%s.%s', $id_factura, $timestamp, $id_cliente, $ext);
        return $filename;
    }

    /**
     * Asegurar que exista el directorio donde guardaremos comprobantes y devolver la ruta
     * @return string|null
     */
    public static function ensureFacturasDir()
    {
        // Crear estructura semanal: facturas/{YYYY}-W{WW}
        $week = date('o') . '-W' . date('W');
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'facturas';
        $dir = $base . DIRECTORY_SEPARATOR . $week;

        if (!is_dir($dir)) {
            try {
                if (!is_dir($base)) mkdir($base, 0755, true);
                mkdir($dir, 0755, true);
            } catch (\Exception $e) {
                error_log('ventasModel::ensureFacturasDir -> No se pudo crear directorio: ' . $e->getMessage());
                return null;
            }
        }
        return $dir;
    }

    /**
     * Buscar un comprobante asociado a una factura (por prefijo ID_FACTURA_*)
     * Busca recursivamente en subdirectorios dentro de la carpeta 'facturas'
     * Retorna la ruta absoluta del primer archivo encontrado o null si no existe
     * @param int $id_factura
     * @return string|null
     */
    public static function buscarComprobantePorFactura($id_factura)
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'facturas';
        if (!is_dir($base)) return null;
        $exts = ['png', 'jpg', 'jpeg', 'pdf'];
        // Buscar en todos los subdirectorios de facturas
        foreach (glob($base . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $id_factura . '_*.*') as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $exts) && is_file($file)) {
                return $file;
            }
        }
        // También buscar en la raíz por si hay archivos sueltos
        foreach (glob($base . DIRECTORY_SEPARATOR . $id_factura . '_*.*') as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $exts) && is_file($file)) {
                return $file;
            }
        }
        return null;
    }
}
