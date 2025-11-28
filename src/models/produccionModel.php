<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use PDO;

class produccionModel {
    
    /**
     * Verificar stock disponible para producción
     */
    public static function verificarStockProduccion($id_producto, $cantidad_planificada) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_VERIFICAR_STOCK_PRODUCCION(:id_producto, :cantidad_planificada, @stock_suficiente, @mensaje)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_producto' => $id_producto,
                'cantidad_planificada' => $cantidad_planificada
            ]);
            
            $result = $con->query("SELECT @stock_suficiente as stock_suficiente, @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'stock_suficiente' => (bool)$result['stock_suficiente'],
                'mensaje' => $result['mensaje']
            ];
            
        } catch (\PDOException $e) {
            error_log("produccionModel::verificarStockProduccion -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Crear orden de producción
     */
    /**
 * Crear orden de producción
 */
public static function crearOrdenProduccion($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO CREAR ORDEN PRODUCCIÓN");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_usuario', 'id_producto', 'cantidad_planificada', 'creado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Verificar que el producto existe
        $sql_verificar = "SELECT COUNT(*) as count, NOMBRE FROM tbl_producto WHERE ID_PRODUCTO = ?";
        $query_verificar = $con->prepare($sql_verificar);
        $query_verificar->execute([$datos['id_producto']]);
        $result_verificar = $query_verificar->fetch(PDO::FETCH_ASSOC);
        
        if ($result_verificar['count'] == 0) {
            return [
                'success' => false,
                'message' => 'ERROR: Producto no encontrado'
            ];
        }
        
        $nombre_producto = $result_verificar['NOMBRE'];
        
        // INSERT con la columna ID_PRODUCTO (que ahora debe existir)
        $sql_insert = "INSERT INTO tbl_produccion (
                    ID_USUARIO, 
                    ID_ESTADO_PRODUCCION, 
                    ID_PRODUCTO,
                    CANTIDAD_PLANIFICADA, 
                    OBSERVACION, 
                    CREADO_POR
                ) VALUES (?, 1, ?, ?, ?, ?)";
        
        $query_insert = $con->prepare($sql_insert);
        $resultado_insert = $query_insert->execute([
            $datos['id_usuario'],
            $datos['id_producto'],
            $datos['cantidad_planificada'],
            $datos['observacion'] ?? '',
            $datos['creado_por']
        ]);
        
        if (!$resultado_insert) {
            $error_info = $query_insert->errorInfo();
            return [
                'success' => false,
                'message' => 'Error al insertar orden: ' . ($error_info[2] ?? 'Error desconocido')
            ];
        }
        
        $id_produccion = $con->lastInsertId();
        
        // Registrar en bitácora
        $sql_bitacora = "INSERT INTO TBL_MS_BITACORA (
                            FECHA, ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR
                         ) VALUES (NOW(), ?, 23, 'CREAR_PRODUCCION', ?, ?)";
        
        $descripcion = "Creó orden de producción #" . $id_produccion . " - Producto: " . $nombre_producto . " - Cantidad: " . $datos['cantidad_planificada'];
        
        $query_bitacora = $con->prepare($sql_bitacora);
        $query_bitacora->execute([
            $datos['id_usuario'],
            $descripcion,
            $datos['creado_por']
        ]);
        
        return [
            'success' => true,
            'id_produccion' => $id_produccion,
            'message' => "OK: Orden de producción #" . $id_produccion . " creada exitosamente para " . $nombre_producto
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::crearOrdenProduccion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}



/**
 * Obtener detalles de una orden de producción
 */
/**
 * Obtener órdenes de producción con filtros
 */
public static function obtenerOrdenesProduccion($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL sp_obtener_ordenes_produccion(:estado, :fecha_desde, :fecha_hasta, :id_producto)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'estado' => $filtros['estado'] ?? null,
            'fecha_desde' => $filtros['fecha_desde'] ?? null,
            'fecha_hasta' => $filtros['fecha_hasta'] ?? null,
            'id_producto' => $filtros['id_producto'] ?? null
        ]);
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerOrdenesProduccion -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
    
    /**
 * Iniciar producción (FASE 3)
 */
public static function iniciarProduccion($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO INICIAR PRODUCCIÓN - FASE 3");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_produccion', 'id_usuario', 'modificado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Llamar al procedimiento almacenado
        $sql = "CALL sp_iniciar_produccion(:id_produccion, :id_usuario, :modificado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_produccion' => $datos['id_produccion'],
            'id_usuario' => $datos['id_usuario'],
            'modificado_por' => $datos['modificado_por']
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
        error_log("💥 ERROR en produccionModel::iniciarProduccion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener detalle completo de una producción
 */
public static function obtenerDetalleProduccion($id_produccion) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO OBTENER DETALLE PRODUCCIÓN - ID: " . $id_produccion);
        
        // Llamar al procedimiento almacenado
        $sql = "CALL sp_obtener_detalle_produccion(:id_produccion)";
        
        $query = $con->prepare($sql);
        $query->execute(['id_produccion' => $id_produccion]);
        
        // Obtener los múltiples resultados
        $produccion = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$produccion) {
            return [
                'success' => false,
                'message' => 'Producción no encontrada'
            ];
        }
        
        $query->nextRowset();
        $materias_primas = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $query->nextRowset();
        $movimientos_cardex = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $query->nextRowset();
        $bitacora = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular estadísticas
        $costo_total_materiales = array_sum(array_column($materias_primas, 'SUBTOTAL'));
        $eficiencia = $produccion['CANTIDAD_REAL'] && $produccion['CANTIDAD_PLANIFICADA'] 
            ? round(($produccion['CANTIDAD_REAL'] / $produccion['CANTIDAD_PLANIFICADA']) * 100, 2)
            : 0;
        
        $data = [
            'produccion' => $produccion,
            'materias_primas' => $materias_primas,
            'movimientos_cardex' => $movimientos_cardex,
            'bitacora' => $bitacora,
            'estadisticas' => [
                'total_materiales' => count($materias_primas),
                'costo_total_materiales' => $costo_total_materiales,
                'eficiencia' => $eficiencia,
                'dias_produccion' => $produccion['FECHA_FIN'] 
                    ? round((strtotime($produccion['FECHA_FIN']) - strtotime($produccion['FECHA_INICIO'])) / (60 * 60 * 24), 1)
                    : null
            ]
        ];
        
        error_log("✅ Detalle obtenido exitosamente - Total materiales: " . count($materias_primas));
        
        return [
            'success' => true,
            'data' => $data
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerDetalleProduccion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
 
    
    /**
     * Obtener receta de un producto
     */
    public static function obtenerReceta($id_producto) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT r.ID_RECETA, r.ID_MATERIA_PRIMA, mp.NOMBRE as NOMBRE_MP, 
                           r.CANTIDAD_NECESARIA, um.UNIDAD, mp.PRECIO_PROMEDIO
                    FROM tbl_receta r
                    JOIN tbl_materia_prima mp ON r.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                    JOIN tbl_unidad_medida um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                    WHERE r.ID_PRODUCTO = :id_producto
                    AND mp.ESTADO = 'ACTIVO'";
            
            $query = $con->prepare($sql);
            $query->execute(['id_producto' => $id_producto]);
            
            return [
                'success' => true,
                'data' => $query->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (\PDOException $e) {
            error_log("produccionModel::obtenerReceta -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener producciones con filtros
     */
    public static function obtenerProducciones($filtros = []) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT p.ID_PRODUCCION, p.FECHA_INICIO, p.FECHA_FIN, p.OBSERVACION,
                           p.CANTIDAD_PLANIFICADA, p.CANTIDAD_REAL, p.COSTO_TOTAL,
                           ep.ESTADO, pr.NOMBRE as PRODUCTO, u.NOMBRE_USUARIO,
                           pr.PRECIO as PRECIO_VENTA
                    FROM tbl_produccion p
                    JOIN tbl_estado_produccion ep ON p.ID_ESTADO_PRODUCCION = ep.ID_ESTADO_PRODUCCION
                    JOIN tbl_producto pr ON p.ID_PRODUCTO = pr.ID_PRODUCTO
                    JOIN tbl_ms_usuarios u ON p.ID_USUARIO = u.ID_USUARIO
                    WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['estado'])) {
                $sql .= " AND ep.ESTADO = :estado";
                $params['estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND DATE(p.FECHA_CREACION) >= :fecha_desde";
                $params['fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND DATE(p.FECHA_CREACION) <= :fecha_hasta";
                $params['fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            $sql .= " ORDER BY p.FECHA_CREACION DESC";
            
            $query = $con->prepare($sql);
            $query->execute($params);
            
            return [
                'success' => true,
                'data' => $query->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (\PDOException $e) {
            error_log("produccionModel::obtenerProducciones -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }
    
   
    
    /**
     * Obtener productos disponibles para producción
     */
    /**
  
 * Obtener productos disponibles para producción
 */
public static function obtenerProductosProduccion() {
    try {
        $con = connectionDB::getConnection();
        
        // Consulta corregida - ELIMINADA la columna CANTIDAD
        $sql = "SELECT 
                    ID_PRODUCTO, 
                    NOMBRE, 
                    DESCRIPCION, 
                    PRECIO,
                    -- SE ELIMINÓ: CANTIDAD, (ya no existe en la tabla)
                    ESTADO
                FROM tbl_producto 
                WHERE ESTADO = 'ACTIVO'
                ORDER BY NOMBRE";
        
        error_log("🔍 Ejecutando consulta: " . $sql);
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Log para debugging
        error_log("📦 Productos encontrados: " . count($productos));
        if (count($productos) > 0) {
            error_log("🎯 Primer producto: " . print_r($productos[0], true));
        }
        
        return [
            'success' => true,
            'data' => $productos
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 produccionModel::obtenerProductosProduccion -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}



/**
 * Finalizar producción (FASE 4)
 */
public static function finalizarProduccion($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO FINALIZAR PRODUCCIÓN - FASE 4");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_produccion', 'id_usuario', 'cantidad_buena', 'modificado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Convertir pérdidas a JSON si existen
        $perdidas_json = '[]';
        if (!empty($datos['perdidas']) && is_array($datos['perdidas'])) {
            $perdidas_json = json_encode($datos['perdidas']);
            error_log("📦 Pérdidas a procesar: " . $perdidas_json);
        }
        
        // Decidir qué procedimiento usar
        if (!empty($datos['perdidas']) && is_array($datos['perdidas']) && count($datos['perdidas']) > 0) {
            // Usar procedimiento con pérdidas detalladas
            error_log("🔍 Usando procedimiento con pérdidas detalladas");
            
            $sql = "CALL sp_registrar_perdidas_detalladas(:id_produccion, :id_usuario, :cantidad_buena, :perdidas_json, :modificado_por, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_produccion' => $datos['id_produccion'],
                'id_usuario' => $datos['id_usuario'],
                'cantidad_buena' => $datos['cantidad_buena'],
                'perdidas_json' => $perdidas_json,
                'modificado_por' => $datos['modificado_por']
            ]);
        } else {
            // Usar procedimiento simple (pérdidas automáticas)
            error_log("🔍 Usando procedimiento simple con pérdidas automáticas");
            
            $sql = "CALL sp_finalizar_produccion(:id_produccion, :cantidad_buena, :id_usuario, :modificado_por, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_produccion' => $datos['id_produccion'],
                'cantidad_buena' => $datos['cantidad_buena'],
                'id_usuario' => $datos['id_usuario'],
                'modificado_por' => $datos['modificado_por']
            ]);
        }
        
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
        error_log("💥 ERROR en produccionModel::finalizarProduccion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

 

/**
 * Obtener receta existente de un producto
 */
public static function obtenerRecetaExistente($id_producto) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT r.*, mp.NOMBRE as MATERIA_PRIMA, um.UNIDAD
                FROM tbl_receta r
                JOIN tbl_materia_prima mp ON r.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                JOIN tbl_unidad_medida um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE r.ID_PRODUCTO = :id_producto
                ORDER BY mp.NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute(['id_producto' => $id_producto]);
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerRecetaExistente -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}


  /**
 * Crear o actualizar receta de un producto
 */
public static function crearReceta($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO CREAR RECETA");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_producto', 'detalles', 'creado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Verificar que hay al menos un detalle
        if (empty($datos['detalles']) || !is_array($datos['detalles']) || count($datos['detalles']) === 0) {
            return [
                'success' => false,
                'message' => 'Debe agregar al menos un ingrediente a la receta'
            ];
        }
        
        // PARÁMETRO PARA SOBREESCRIBIR (por defecto false)
        $sobreescribir = isset($datos['sobreescribir']) ? (bool)$datos['sobreescribir'] : false;
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_CREAR_RECETA(:id_producto, :detalles, :creado_por, :sobreescribir, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_producto' => $datos['id_producto'],
            'detalles' => json_encode($datos['detalles']),
            'creado_por' => $datos['creado_por'],
            'sobreescribir' => $sobreescribir
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
        error_log("💥 ERROR en produccionModel::crearReceta: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * VERIFICAR SI EXISTE RECETA PARA UN PRODUCTO
 */
public static function verificarRecetaExistente($id_producto) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    COUNT(*) as existe_receta,
                    p.NOMBRE as nombre_producto,
                    COUNT(r.ID_RECETA) as total_ingredientes
                FROM tbl_producto p
                LEFT JOIN tbl_receta r ON p.ID_PRODUCTO = r.ID_PRODUCTO
                WHERE p.ID_PRODUCTO = :id_producto
                GROUP BY p.ID_PRODUCTO, p.NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute(['id_producto' => $id_producto]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'success' => true,
                'existe_receta' => $result['existe_receta'] > 0,
                'nombre_producto' => $result['nombre_producto'],
                'total_ingredientes' => $result['total_ingredientes']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("produccionModel::verificarRecetaExistente -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener materias primas activas
 */
public static function obtenerMateriasPrimas() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    mp.ID_MATERIA_PRIMA,
                    mp.NOMBRE,
                    mp.DESCRIPCION,
                    um.UNIDAD,
                    mp.CANTIDAD as STOCK_ACTUAL,
                    mp.PRECIO_PROMEDIO
                FROM tbl_materia_prima mp
                JOIN tbl_unidad_medida um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE mp.ESTADO = 'ACTIVO'
                ORDER BY mp.NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerMateriasPrimas -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener todas las recetas con detalles
 */
public static function obtenerTodasLasRecetas() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_TODAS_LAS_RECETAS()";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $recetas = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Agrupar por producto para una mejor estructura
        $recetasAgrupadas = [];
        foreach ($recetas as $receta) {
            $idProducto = $receta['ID_PRODUCTO'];
            
            if (!isset($recetasAgrupadas[$idProducto])) {
                $recetasAgrupadas[$idProducto] = [
                    'ID_PRODUCTO' => $receta['ID_PRODUCTO'],
                    'NOMBRE_PRODUCTO' => $receta['NOMBRE_PRODUCTO'],
                    'DESCRIPCION_PRODUCTO' => $receta['DESCRIPCION_PRODUCTO'],
                    'FECHA_CREACION' => $receta['FECHA_CREACION'],
                    'CREADO_POR' => $receta['CREADO_POR'],
                    'INGREDIENTES' => [],
                    'COSTO_TOTAL' => 0,
                    'TOTAL_INGREDIENTES' => 0
                ];
            }
            
            $recetasAgrupadas[$idProducto]['INGREDIENTES'][] = [
                'ID_RECETA' => $receta['ID_RECETA'],
                'ID_MATERIA_PRIMA' => $receta['ID_MATERIA_PRIMA'],
                'NOMBRE_MATERIA_PRIMA' => $receta['NOMBRE_MATERIA_PRIMA'],
                'UNIDAD' => $receta['UNIDAD'],
                'CANTIDAD_NECESARIA' => $receta['CANTIDAD_NECESARIA'],
                'PRECIO_PROMEDIO' => $receta['PRECIO_PROMEDIO'],
                'COSTO_INGREDIENTE' => $receta['COSTO_INGREDIENTE']
            ];
            
            // Calcular costo total
            $recetasAgrupadas[$idProducto]['COSTO_TOTAL'] += $receta['COSTO_INGREDIENTE'];
            $recetasAgrupadas[$idProducto]['TOTAL_INGREDIENTES'] = count($recetasAgrupadas[$idProducto]['INGREDIENTES']);
        }
        
        return [
            'success' => true,
            'data' => array_values($recetasAgrupadas), // Convertir a array indexado
            'total_recetas' => count($recetasAgrupadas)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerTodasLasRecetas -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener receta específica por ID de producto
 */
public static function obtenerRecetaPorProducto($id_producto) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_TODAS_LAS_RECETAS()";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $recetas = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Filtrar por producto específico
        $recetaProducto = [];
        $costoTotal = 0;
        
        foreach ($recetas as $receta) {
            if ($receta['ID_PRODUCTO'] == $id_producto) {
                if (empty($recetaProducto)) {
                    $recetaProducto = [
                        'ID_PRODUCTO' => $receta['ID_PRODUCTO'],
                        'NOMBRE_PRODUCTO' => $receta['NOMBRE_PRODUCTO'],
                        'DESCRIPCION_PRODUCTO' => $receta['DESCRIPCION_PRODUCTO'],
                        'INGREDIENTES' => []
                    ];
                }
                
                $recetaProducto['INGREDIENTES'][] = [
                    'ID_RECETA' => $receta['ID_RECETA'],
                    'ID_MATERIA_PRIMA' => $receta['ID_MATERIA_PRIMA'],
                    'NOMBRE_MATERIA_PRIMA' => $receta['NOMBRE_MATERIA_PRIMA'],
                    'UNIDAD' => $receta['UNIDAD'],
                    'CANTIDAD_NECESARIA' => $receta['CANTIDAD_NECESARIA'],
                    'PRECIO_PROMEDIO' => $receta['PRECIO_PROMEDIO'],
                    'COSTO_INGREDIENTE' => $receta['COSTO_INGREDIENTE']
                ];
                
                $costoTotal += $receta['COSTO_INGREDIENTE'];
            }
        }
        
        if (!empty($recetaProducto)) {
            $recetaProducto['COSTO_TOTAL'] = $costoTotal;
            $recetaProducto['TOTAL_INGREDIENTES'] = count($recetaProducto['INGREDIENTES']);
            
            return [
                'success' => true,
                'data' => $recetaProducto
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró receta para el producto especificado'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerRecetaPorProducto -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Sobreescribir receta existente
 */
public static function sobreescribirReceta($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO SOBREESCRIBIR RECETA");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_producto', 'detalles', 'creado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Verificar que hay al menos un detalle
        if (empty($datos['detalles']) || !is_array($datos['detalles']) || count($datos['detalles']) === 0) {
            return [
                'success' => false,
                'message' => 'Debe agregar al menos un ingrediente a la receta'
            ];
        }
        
        // Llamar al procedimiento almacenado de sobreescritura
        $sql = "CALL SP_SOBREESCRIBIR_RECETA(:id_producto, :detalles, :creado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_producto' => $datos['id_producto'],
            'detalles' => json_encode($datos['detalles']),
            'creado_por' => $datos['creado_por']
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
        error_log("💥 ERROR en produccionModel::sobreescribirReceta: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener todos los productos con filtros
 */
public static function obtenerProductos($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO OBTENER PRODUCTOS");
        error_log("📦 Filtros recibidos: " . print_r($filtros, true));
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_OBTENER_PRODUCTOS(:filtro_nombre, :filtro_estado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
            'filtro_estado' => $filtros['filtro_estado'] ?? null
        ]);
        
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📦 Productos encontrados: " . count($productos));
        
        return [
            'success' => true,
            'data' => $productos
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerProductos: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Ingresar producto al inventario
 */
public static function ingresarProductoInventario($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO INGRESAR PRODUCTO INVENTARIO");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_producto', 'cantidad', 'id_usuario', 'creado_por'];
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
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_INGRESAR_PRODUCTO_INVENTARIO(:id_producto, :cantidad, :id_usuario, :creado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_producto' => $datos['id_producto'],
            'cantidad' => $datos['cantidad'],
            'id_usuario' => $datos['id_usuario'],
            'creado_por' => $datos['creado_por']
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
        error_log("💥 ERROR en produccionModel::ingresarProductoInventario: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener producto por ID
 */
public static function obtenerProductoPorId($id_producto) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO OBTENER PRODUCTO POR ID: " . $id_producto);
        
        $sql = "SELECT 
                    p.ID_PRODUCTO,
                    p.NOMBRE,
                    p.DESCRIPCION,
                    p.PRECIO,
                    p.ID_UNIDAD_MEDIDA,
                    um.UNIDAD,
                    um.DESCRIPCION as DESC_UNIDAD,
                    p.ESTADO,
                    p.FECHA_CREACION,
                    DATE_FORMAT(p.FECHA_CREACION, '%d/%m/%Y %H:%i') AS FECHA_CREACION_FORMATEADA,
                    p.CREADO_POR,
                    p.FECHA_MODIFICACION,
                    DATE_FORMAT(p.FECHA_MODIFICACION, '%d/%m/%Y %H:%i') AS FECHA_MODIFICACION_FORMATEADA,
                    p.MODIFICADO_POR
                FROM tbl_producto p
                LEFT JOIN tbl_unidad_medida um ON p.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE p.ID_PRODUCTO = :id_producto";
        
        $query = $con->prepare($sql);
        $query->execute(['id_producto' => $id_producto]);
        
        $producto = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            error_log("✅ Producto encontrado: " . $producto['NOMBRE']);
            return [
                'success' => true,
                'data' => $producto
            ];
        } else {
            error_log("❌ Producto no encontrado - ID: " . $id_producto);
            return [
                'success' => false,
                'message' => 'Producto no encontrado'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerProductoPorId: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Actualizar producto
 */
public static function actualizarProducto($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO ACTUALIZAR PRODUCTO");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos (sin minimo y maximo)
        $required_fields = [
            'id_producto', 'nombre', 'precio', 'id_unidad_medida', 
            'estado', 'modificado_por'
        ];
        
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Llamar al procedimiento almacenado modificado (sin minimo y maximo)
        $sql = "CALL SP_ACTUALIZAR_PRODUCTO(:id_producto, :nombre, :descripcion, :precio, :id_unidad_medida, :estado, :modificado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_producto' => $datos['id_producto'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio'],
            'id_unidad_medida' => $datos['id_unidad_medida'],
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por']
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
        error_log("💥 ERROR en produccionModel::actualizarProducto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Registrar pérdidas de producción
 */
public static function registrarPerdidasProduccion($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO REGISTRAR PÉRDIDAS PRODUCCIÓN");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_produccion', 'id_usuario', 'perdidas', 'modificado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Validar que haya al menos una pérdida
        if (empty($datos['perdidas']) || !is_array($datos['perdidas']) || count($datos['perdidas']) === 0) {
            return [
                'success' => false,
                'message' => 'Debe registrar al menos una pérdida'
            ];
        }
        
        // Convertir pérdidas a JSON
        $perdidas_json = json_encode($datos['perdidas']);
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_PERDIDAS_DE_PRODUCCION(:id_produccion, :id_usuario, :perdidas_json, :modificado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_produccion' => $datos['id_produccion'],
            'id_usuario' => $datos['id_usuario'],
            'perdidas_json' => $perdidas_json,
            'modificado_por' => $datos['modificado_por']
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
        error_log("💥 ERROR en produccionModel::registrarPerdidasProduccion: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener catálogo de motivos de pérdida
 */
public static function obtenerMotivosPerdida() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT ID_MOTIVO, CODIGO, MOTIVO, DESCRIPCION 
                FROM tbl_catalogo_motivos_perdida 
                WHERE ESTADO = 'ACTIVO'
                ORDER BY MOTIVO";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerMotivosPerdida -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

 /**
 * Obtener pérdidas por producción
 */
 public static function obtenerPerdidasPorProduccion($id_produccion) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    pp.ID_PERDIDA,
                    pp.CANTIDAD_PERDIDA,
                    pp.MOTIVO_PERDIDA,
                    pp.DESCRIPCION,
                    pp.FECHA_PERDIDA,
                    u.NOMBRE_USUARIO as REGISTRADO_POR
                FROM tbl_perdidas_produccion pp
                JOIN tbl_ms_usuarios u ON pp.ID_USUARIO = u.ID_USUARIO
                WHERE pp.ID_PRODUCCION = :id_produccion
                ORDER BY pp.FECHA_PERDIDA DESC";
        
        $query = $con->prepare($sql);
        $query->execute(['id_produccion' => $id_produccion]);
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerPerdidasPorProduccion -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener todas las pérdidas de producción
 */
public static function obtenerTodasPerdidasProduccion() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    pp.ID_PERDIDA,
                    pp.ID_PRODUCCION,
                    pp.CANTIDAD_PERDIDA,
                    pp.MOTIVO_PERDIDA,
                    pp.DESCRIPCION,
                    pp.FECHA_PERDIDA,
                    u.NOMBRE_USUARIO AS REGISTRADO_POR
                FROM tbl_perdidas_produccion pp
                JOIN tbl_ms_usuarios u ON pp.ID_USUARIO = u.ID_USUARIO
                ORDER BY pp.FECHA_PERDIDA DESC";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return [
            'success' => true,
            'data' => $query->fetchAll(PDO::FETCH_ASSOC)
        ];
        
    } catch (\PDOException $e) {
        error_log("produccionModel::obtenerTodasPerdidasProduccion -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
/**
 * Crear producto completo con receta
 */
/**
 * Crear producto completo con receta
 */
public static function crearProductoConRecetaCompleto($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO CREAR PRODUCTO CON RECETA COMPLETO");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['nombre', 'precio', 'id_unidad_medida', 'creado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_CREAR_PRODUCTO_CON_RECETA(:nombre, :descripcion, :precio, :id_unidad_medida, :detalles, :creado_por, @resultado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? '',
            'precio' => $datos['precio'],
            'id_unidad_medida' => $datos['id_unidad_medida'],
            'detalles' => !empty($datos['detalles']) ? json_encode($datos['detalles']) : null,
            'creado_por' => $datos['creado_por']
        ]);
        
        // Obtener resultado
        $result = $con->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
        
        error_log("📦 Resultado del procedimiento: " . $result['resultado']);
        
        if (strpos($result['resultado'], 'OK:') === 0) {
            // Extraer el ID del producto del mensaje
            preg_match('/ID (\d+)/', $result['resultado'], $matches);
            $id_producto = isset($matches[1]) ? (int)$matches[1] : null;
            
            return [
                'success' => true,
                'id_producto' => $id_producto,
                'message' => $result['resultado']
            ];
        } else {
            return [
                'success' => false,
                'message' => $result['resultado']
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::crearProductoConRecetaCompleto: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener materias primas activas con información completa para recetas
 */
public static function obtenerMateriasPrimasParaReceta() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    mp.ID_MATERIA_PRIMA,
                    mp.NOMBRE,
                    mp.DESCRIPCION,
                    mp.ID_UNIDAD_MEDIDA,
                    um.UNIDAD,
                    mp.PRECIO_PROMEDIO,
                    COALESCE((
                        SELECT SUM(CANTIDAD) 
                        FROM tbl_inventario_materia_prima 
                        WHERE ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                    ), 0) as STOCK_ACTUAL
                FROM tbl_materia_prima mp
                JOIN tbl_unidad_medida um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE mp.ESTADO = 'ACTIVO'
                ORDER BY mp.NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $materiasPrimas = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📦 Materias primas para receta encontradas: " . count($materiasPrimas));
        
        return [
            'success' => true,
            'data' => $materiasPrimas
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerMateriasPrimasParaReceta: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
/**
 * Obtener unidades de medida activas
 */
public static function obtenerUnidadesMedida() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    ID_UNIDAD_MEDIDA, 
                    UNIDAD, 
                    DESCRIPCION,
                    FECHA_CREACION,
                    CREADO_POR
                FROM tbl_unidad_medida 
                ORDER BY UNIDAD";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📦 Unidades de medida encontradas: " . count($unidades));
        
        return [
            'success' => true,
            'data' => $unidades
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerUnidadesMedida: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Obtener producto y receta por ID para edición
 */
public static function obtenerProductoRecetaPorId($id_producto) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO OBTENER PRODUCTO RECETA POR ID: " . $id_producto);
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_OBTENER_PRODUCTO_RECETA_POR_ID(:id_producto)";
        
        $query = $con->prepare($sql);
        $query->execute(['id_producto' => $id_producto]);
        
        // Obtener información del producto (primer resultado)
        $producto = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            return [
                'success' => false,
                'message' => 'Producto no encontrado o está inactivo'
            ];
        }
        
        // Obtener siguiente resultado (detalles de receta)
        $query->nextRowset();
        $receta = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📦 Producto encontrado: " . $producto['NOMBRE'] . ", ingredientes: " . count($receta));
        
        return [
            'success' => true,
            'data' => [
                'producto' => $producto,
                'receta' => $receta
            ]
        ];
        
    } catch (\PDOException $e) {
        error_log("💥 ERROR en produccionModel::obtenerProductoRecetaPorId: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}

/**
 * Editar producto completo con receta
 */
/**
 * Editar producto completo con receta
 */
public static function editarProductoConRecetaCompleto($datos) {
    try {
        $con = connectionDB::getConnection();
        
        error_log("🎯 INICIANDO EDITAR PRODUCTO CON RECETA COMPLETO");
        error_log("📦 Datos recibidos: " . print_r($datos, true));
        
        // Validar datos requeridos
        $required_fields = ['id_producto', 'nombre', 'precio', 'id_unidad_medida', 'actualizado_por'];
        foreach ($required_fields as $field) {
            if (empty($datos[$field])) {
                return [
                    'success' => false,
                    'message' => "El campo $field es obligatorio"
                ];
            }
        }
        
        // Preparar detalles para JSON
        $detalles_json = null;
        if (!empty($datos['detalles']) && is_array($datos['detalles'])) {
            $detalles_json = json_encode($datos['detalles']);
            error_log("📦 JSON detalles preparado: " . $detalles_json);
        }
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_EDITAR_PRODUCTO_CON_RECETA(:id_producto, :nombre, :descripcion, :precio, :id_unidad_medida, :detalles, :actualizado_por, @resultado)";
        
        error_log("📦 Ejecutando SQL: " . $sql);
        
        $query = $con->prepare($sql);
        $params = [
            'id_producto' => $datos['id_producto'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? '',
            'precio' => $datos['precio'],
            'id_unidad_medida' => $datos['id_unidad_medida'],
            'detalles' => $detalles_json,
            'actualizado_por' => $datos['actualizado_por']
        ];
        
        error_log("📦 Parámetros: " . print_r($params, true));
        
        $query->execute($params);
        
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
        error_log("💥 ERROR PDO en produccionModel::editarProductoConRecetaCompleto: " . $e->getMessage());
        error_log("💥 Código de error: " . $e->getCode());
        error_log("💥 Info de error: " . $e->errorInfo);
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
}
?>