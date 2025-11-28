<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use PDO;

class inventarioModel {
    
    // Obtener inventario completo
    public static function obtenerInventarioCompleto() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_INVENTARIO_ACTUAL()";
            $query = $con->prepare($sql);
            $query->execute();
            
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // DEBUG: Log para verificar datos
            error_log("DEBUG - Inventario obtenido: " . count($inventario) . " registros");
            if (!empty($inventario)) {
                error_log("DEBUG - Primer registro: " . print_r($inventario[0], true));
            }
            
            return $inventario;
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerInventarioCompleto -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener item específico del inventario
    public static function obtenerItemInventario($idInventario) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM TBL_INVENTARIO_MATERIA_PRIMA WHERE ID_INVENTARIO_MP = :id_inventario";
            $query = $con->prepare($sql);
            $query->execute(['id_inventario' => $idInventario]);
            
            if ($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            
            return null;
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerItemInventario -> " . $e->getMessage());
            return null;
        }
    }
    
    // Actualizar inventario
public static function actualizarInventario($datos) {
    try {
        error_log("🎯 inventarioModel::actualizarInventario - Datos: " . print_r($datos, true));
        
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_ACTUALIZAR_INVENTARIO(:id_materia_prima, :cantidad, :tipo_movimiento, :id_usuario, :descripcion, :actualizado_por)";
        
        error_log("📝 Ejecutando SP: " . $sql);
        
        $query = $con->prepare($sql);
        $success = $query->execute([
            'id_materia_prima' => $datos['id_materia_prima'],
            'cantidad' => $datos['cantidad'],
            'tipo_movimiento' => $datos['tipo_movimiento'],
            'id_usuario' => $datos['id_usuario'],
            'descripcion' => $datos['descripcion'],
            'actualizado_por' => $datos['actualizado_por']
        ]);
        
        error_log("✅ Ejecución SQL exitosa: " . ($success ? 'Sí' : 'No'));
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        error_log("📊 Resultado del SP: " . print_r($result, true));
        
        if ($result && $result['STATUS'] === 'success') {
            return [
                'success' => true, 
                'message' => $result['MESSAGE'],
                'nuevo_stock' => $result['NUEVO_STOCK'] ?? null
            ];
        } else {
            $errorMessage = $result['MESSAGE'] ?? 'Error desconocido al actualizar inventario';
            error_log("❌ Error del SP: " . $errorMessage);
            return ['success' => false, 'message' => $errorMessage];
        }
        
    } catch (\PDOException $e) {
        error_log("💥 PDOException en actualizarInventario: " . $e->getMessage());
        error_log("💥 Código de error: " . $e->getCode());
        
        return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
    }
}
    
    // Obtener historial de inventario
    public static function obtenerHistorialInventario($filtros = []) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_HISTORIAL_INVENTARIO(:id_materia_prima, :fecha_inicio, :fecha_fin)";
            $query = $con->prepare($sql);
            $query->execute([
                'id_materia_prima' => $filtros['id_materia_prima'] ?? null,
                'fecha_inicio' => $filtros['fecha_inicio'] ?? null,
                'fecha_fin' => $filtros['fecha_fin'] ?? null
            ]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerHistorialInventario -> " . $e->getMessage());
            return [];
        }
    }
    
    // Exportar inventario para PDF
    public static function exportarInventarioPDF() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_EXPORTAR_INVENTARIO_PDF()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::exportarInventarioPDF -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener alertas de inventario
    public static function obtenerAlertasInventario() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_ALERTAS_INVENTARIO()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerAlertasInventario -> " . $e->getMessage());
            return [];
        }
    }
    
    // Registrar en bitácora
    public static function registrarBitacora($idUsuario, $accion, $descripcion, $creadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            $sql = "INSERT INTO TBL_MS_BITACORA (ID_USUARIO, ACCION, DESCRIPCION, CREADO_POR) 
                    VALUES (:id_usuario, :accion, :descripcion, :creado_por)";
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $idUsuario,
                'accion' => $accion,
                'descripcion' => $descripcion,
                'creado_por' => $creadoPor
            ]);
        } catch (\PDOException $e) {
            error_log("inventarioModel::registrarBitacora -> " . $e->getMessage());
        }
    }

    /**
     * Obtener inventario de productos
     */
    public static function obtenerInventarioProductos($filtros = []) {
        try {
            $con = connectionDB::getConnection();
            
            error_log("🎯 INICIANDO OBTENER INVENTARIO PRODUCTOS");
            error_log("📦 Filtros recibidos: " . print_r($filtros, true));
            
            // Llamar al procedimiento almacenado
            $sql = "CALL SP_OBTENER_INVENTARIO_PRODUCTOS(:filtro_nombre, :filtro_estado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
                'filtro_estado' => $filtros['filtro_estado'] ?? null
            ]);
            
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("📦 Productos en inventario encontrados: " . count($inventario));
            
            return [
                'success' => true,
                'data' => $inventario
            ];
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR en inventarioProductoModel::obtenerInventarioProductos: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener producto específico del inventario
     */
    public static function obtenerProductoInventario($id_producto) {
        try {
            $con = connectionDB::getConnection();
            
            error_log("🎯 INICIANDO OBTENER PRODUCTO INVENTARIO: " . $id_producto);
            
            $sql = "CALL SP_OBTENER_INVENTARIO_PRODUCTOS(NULL, NULL)";
            
            $query = $con->prepare($sql);
            $query->execute();
            
            $productos = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Filtrar por ID específico
            $producto = array_filter($productos, function($item) use ($id_producto) {
                return $item['ID_PRODUCTO'] == $id_producto;
            });
            
            $producto = reset($producto); // Obtener el primer elemento
            
            if ($producto) {
                error_log("✅ Producto encontrado en inventario: " . $producto['NOMBRE']);
                return [
                    'success' => true,
                    'data' => $producto
                ];
            } else {
                error_log("❌ Producto no encontrado en inventario - ID: " . $id_producto);
                return [
                    'success' => false,
                    'message' => 'Producto no encontrado en el inventario'
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR en inventarioProductoModel::obtenerProductoInventario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ajustar inventario de producto
     */
    public static function ajustarInventarioProducto($datos) {
        try {
            $con = connectionDB::getConnection();
            
            error_log("🎯 INICIANDO AJUSTAR INVENTARIO PRODUCTO");
            error_log("📦 Datos recibidos: " . print_r($datos, true));
            
            // Validar datos requeridos
            $required_fields = ['id_producto', 'cantidad', 'tipo_movimiento', 'descripcion', 'id_usuario', 'actualizado_por'];
            foreach ($required_fields as $field) {
                if (empty($datos[$field])) {
                    return [
                        'success' => false,
                        'message' => "El campo $field es obligatorio"
                    ];
                }
            }
            
            // Validar que la cantidad sea positiva
            if ($datos['cantidad'] <= 0) {
                return [
                    'success' => false,
                    'message' => 'La cantidad debe ser mayor a 0'
                ];
            }
            
            // Validar tipo de movimiento
            $tipos_validos = ['ENTRADA', 'SALIDA', 'AJUSTE'];
            if (!in_array($datos['tipo_movimiento'], $tipos_validos)) {
                return [
                    'success' => false,
                    'message' => 'Tipo de movimiento no válido. Valores permitidos: ' . implode(', ', $tipos_validos)
                ];
            }
            
            // Llamar al procedimiento almacenado para ajuste
            $sql = "CALL SP_AJUSTAR_INVENTARIO_PRODUCTO(:id_producto, :cantidad, :tipo_movimiento, :descripcion, :id_usuario, :actualizado_por, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_producto' => $datos['id_producto'],
                'cantidad' => $datos['cantidad'],
                'tipo_movimiento' => $datos['tipo_movimiento'],
                'descripcion' => $datos['descripcion'],
                'id_usuario' => $datos['id_usuario'],
                'actualizado_por' => $datos['actualizado_por']
            ]);
            
            // Obtener resultado
            $result = $con->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
            
            error_log("📦 Resultado del procedimiento: " . $result['resultado']);
            
            if (strpos($result['resultado'], 'OK:') === 0) {
                return [
                    'success' => true,
                    'message' => $result['resultado']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['resultado']
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR en inventarioProductoModel::ajustarInventarioProducto: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener historial de movimientos de producto
     */
    public static function obtenerHistorialProducto($filtros = []) {
        try {
            $con = connectionDB::getConnection();
            
            error_log("🎯 INICIANDO OBTENER HISTORIAL PRODUCTO");
            error_log("📦 Filtros recibidos: " . print_r($filtros, true));
            
            $sql = "SELECT 
                        cp.ID_CARDEX_PRODUCTO,
                        cp.ID_PRODUCTO,
                        p.NOMBRE,
                        cp.CANTIDAD,
                        cp.TIPO_MOVIMIENTO,
                        cp.ID_USUARIO,
                        u.NOMBRE_USUARIO as USUARIO,
                        cp.DESCRIPCION,
                        cp.FECHA_MOVIMIENTO,
                        DATE_FORMAT(cp.FECHA_MOVIMIENTO, '%d/%m/%Y %H:%i') AS FECHA_MOVIMIENTO_FORMATEADA,
                        cp.CREADO_POR
                    FROM tbl_cardex_producto cp
                    INNER JOIN tbl_producto p ON cp.ID_PRODUCTO = p.ID_PRODUCTO
                    LEFT JOIN tbl_ms_usuarios u ON cp.ID_USUARIO = u.ID_USUARIO
                    WHERE cp.ID_PRODUCTO = :id_producto";
            
            $params = ['id_producto' => $filtros['id_producto']];
            
            // Aplicar filtros de fecha
            if (!empty($filtros['fecha_inicio'])) {
                $sql .= " AND DATE(cp.FECHA_MOVIMIENTO) >= :fecha_inicio";
                $params['fecha_inicio'] = $filtros['fecha_inicio'];
            }
            
            if (!empty($filtros['fecha_fin'])) {
                $sql .= " AND DATE(cp.FECHA_MOVIMIENTO) <= :fecha_fin";
                $params['fecha_fin'] = $filtros['fecha_fin'];
            }
            
            $sql .= " ORDER BY cp.FECHA_MOVIMIENTO DESC";
            
            $query = $con->prepare($sql);
            $query->execute($params);
            
            $historial = $query->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("📦 Movimientos en historial encontrados: " . count($historial));
            
            return [
                'success' => true,
                'data' => $historial
            ];
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR en inventarioProductoModel::obtenerHistorialProducto: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }


    /**
 * Editar inventario de producto
 */
public static function editarInventarioProducto($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO EDITAR INVENTARIO PRODUCTO");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_inventario_producto', 'cantidad', 'precio', 'actualizado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_EDITAR_INVENTARIO_PRODUCTO(:id_inventario_producto, :cantidad, :precio, :actualizado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_inventario_producto' => $datos['id_inventario_producto'],
            'cantidad' => $datos['cantidad'],
            'precio' => $datos['precio'],
            'actualizado_por' => $datos['actualizado_por']
        ]);
        
        // Obtener resultado
        $result = $con->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
        
        error_log("📦 Resultado del procedimiento: " . $result['resultado']);
        
        if (strpos($result['resultado'], 'OK:') === 0) {
            return [
                'success' => true,
                'message' => $result['resultado']
            ];
        } else {
            return [
                'success' => false,
                'message' => $result['resultado']
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en inventarioModel::editarInventarioProducto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
}
?>