<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use PDO;

class comprasModel {
public static function registrarOrdenCompra($datos) {
    try {
        $con = connectionDB::getConnection();
        
        // Preparar detalles como JSON
        $detalles = $datos['detalles'];
        if (is_string($detalles)) {
            $detalles = json_decode($detalles, true);
        }
        
        // Convertir a formato JSON para el procedimiento almacenado
        $detallesJson = json_encode($detalles);
        
        $sql = "CALL SP_REGISTRAR_ORDEN_COMPRA(:id_proveedor, :id_usuario, :observaciones, :creado_por, :detalles)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor' => $datos['id_proveedor'],
            'id_usuario' => $datos['id_usuario'],
            'observaciones' => $datos['observaciones'] ?? null,
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA',
            'detalles' => $detallesJson
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['ID_RECEPCION'])) {
            return [
                'success' => true,
                'message' => $result['MENSAJE'] ?? 'Orden de compra registrada exitosamente',
                'id_recepcion' => $result['ID_RECEPCION']
            ];
        } else {
            return [
                'success' => false,
                'message' => $result['MENSAJE'] ?? 'Error desconocido al registrar orden de compra'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarOrdenCompra -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }
}
   
/**
 * Registrar productos para un proveedor recién creado
 */
public static function registrarProductosProveedor($idProveedor, $productos, $usuario) {
    try {
        $con = connectionDB::getConnection();
        
        // Iniciar transacción
        $con->beginTransaction();
        
        // Insertar relaciones de productos
        foreach ($productos as $idProducto) {
            $sqlInsertar = "INSERT INTO tbl_proveedor_productos_relacion 
                           (ID_PROVEEDOR, ID_PROVEEDOR_PRODUCTO, CREADO_POR)
                           VALUES (:id_proveedor, :id_producto, :creado_por)";
            
            $queryInsertar = $con->prepare($sqlInsertar);
            $queryInsertar->execute([
                'id_proveedor' => $idProveedor,
                'id_producto' => $idProducto,
                'creado_por' => $usuario
            ]);
        }
        
        $con->commit();
        return ['success' => true, 'message' => 'Productos asignados correctamente al proveedor'];
        
    } catch (\PDOException $e) {
        $con->rollBack();
        error_log("comprasModel::registrarProductosProveedor -> " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al asignar productos al proveedor'];
    }
}
public static function registrarProductoProveedorCompleto($datos) {
    try {
        $con = connectionDB::getConnection();
        
        // Primero validar que la unidad de medida existe (SIN ESTADO)
        $sqlValidarUnidad = "SELECT COUNT(*) as existe FROM tbl_unidad_medida WHERE ID_UNIDAD_MEDIDA = ?";
        $stmtValidar = $con->prepare($sqlValidarUnidad);
        $stmtValidar->execute([$datos['id_unidad_medida']]);
        $resultValidar = $stmtValidar->fetch(PDO::FETCH_ASSOC);
        
        if ($resultValidar['existe'] == 0) {
            return [
                'success' => false,
                'message' => 'La unidad de medida seleccionada no existe'
            ];
        }
        
        // Validar que el proveedor existe (si se proporciona) - también SIN ESTADO
        if (!empty($datos['id_proveedor']) && $datos['id_proveedor'] > 0) {
            $sqlValidarProveedor = "SELECT COUNT(*) as existe FROM tbl_proveedor WHERE ID_PROVEEDOR = ?";
            $stmtValidarProv = $con->prepare($sqlValidarProveedor);
            $stmtValidarProv->execute([$datos['id_proveedor']]);
            $resultValidarProv = $stmtValidarProv->fetch(PDO::FETCH_ASSOC);
            
            if ($resultValidarProv['existe'] == 0) {
                return [
                    'success' => false,
                    'message' => 'El proveedor seleccionado no existe'
                ];
            }
        }
        
        // CORREGIDO: Solo 9 parámetros de entrada en el orden correcto
        $sql = "CALL SP_REGISTRAR_PRODUCTO_PROVEEDOR_COMPLETO(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($sql);
        $stmt->execute([
            $datos['nombre_producto'],                // parámetro 1
            $datos['descripcion'] ?? null,            // parámetro 2
            $datos['id_proveedor'] ?? null,           // parámetro 3 (puede ser NULL)
            $datos['id_unidad_medida'],               // parámetro 4
            $datos['precio_unitario'],                // parámetro 5
            $datos['minimo'],                         // parámetro 6
            $datos['maximo'],                         // parámetro 7
            $datos['creado_por'] ?? 'SISTEMA',        // parámetro 8
            $datos['id_usuario'] ?? 1                 // parámetro 9
        ]);
        
        // Obtener el resultado del procedimiento
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            if (isset($result['mensaje']) && isset($result['id_proveedor_producto'])) {
                return [
                    'success' => true,
                    'message' => $result['mensaje'],
                    'data' => [
                        'id_proveedor_producto' => $result['id_proveedor_producto'],
                        'id_materia_prima' => $result['id_materia_prima'] ?? null
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['mensaje'] ?? 'Error al registrar el producto'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo obtener respuesta del procedimiento'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarProductoProveedorCompleto -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error de base de datos: ' . $e->getMessage();
        if (strpos($e->getMessage(), 'Ya existe un producto con ese nombre') !== false) {
            $mensajeError = 'Ya existe un producto con ese nombre';
        } elseif (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            if (strpos($e->getMessage(), 'ID_UNIDAD_MEDIDA') !== false) {
                $mensajeError = 'La unidad de medida seleccionada no existe';
            } elseif (strpos($e->getMessage(), 'ID_PROVEEDOR') !== false) {
                $mensajeError = 'El proveedor seleccionado no existe';
            }
        } elseif (strpos($e->getMessage(), "Unknown column 'ESTADO'") !== false) {
            $mensajeError = 'Error en la estructura de la base de datos: columna ESTADO no encontrada';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}


public static function obtenerProductoProveedorCompleto($id_producto_proveedor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PRODUCTO_PROVEEDOR_COMPLETO(:id_producto_proveedor)";
        $query = $con->prepare($sql);
        $query->execute(['id_producto_proveedor' => $id_producto_proveedor]);
        
        $producto = $query->fetch(PDO::FETCH_ASSOC);
        
        return $producto;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProductoProveedorCompleto -> " . $e->getMessage());
        return null;
    }
}

/**
 * Listar productos de proveedores con información completa
 */
public static function listarProductosProveedoresCompleto($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_LISTAR_PRODUCTOS_PROVEEDORES_COMPLETO(:filtro_nombre, :filtro_proveedor, :filtro_estado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
            'filtro_proveedor' => $filtros['filtro_proveedor'] ?? null,
            'filtro_estado' => $filtros['filtro_estado'] ?? null
        ]);
        
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $productos
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::listarProductosProveedoresCompleto -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener productos de proveedores',
            'data' => []
        ];
    }
}
    /**
     * Finalizar una compra (actualizar inventario)
     */
    public static function finalizarCompra($datos) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_FINALIZAR_COMPRA(:id_recepcion, :id_usuario, :modificado_por)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_recepcion' => $datos['id_recepcion'],
                'id_usuario' => $datos['id_usuario'],
                'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
            ]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['ID_COMPRA'])) {
                return [
                    'success' => true,
                    'message' => $result['MENSAJE'] ?? 'Compra finalizada exitosamente',
                    'id_compra' => $result['ID_COMPRA']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['MENSAJE'] ?? 'Error desconocido al finalizar compra'
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("comprasModel::finalizarCompra -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }
/**
 * Obtener productos por proveedor usando la nueva relación
 */
public static function obtenerProductosPorProveedorRelacion($idProveedor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PRODUCTOS_PROVEEDOR_RELACION(:id_proveedor)";
        $query = $con->prepare($sql);
        $query->execute(['id_proveedor' => $idProveedor]);
        
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // DEBUG: Log para verificar estructura
        error_log("DEBUG - Productos obtenidos para proveedor $idProveedor (nueva relación): " . count($productos));
        if (!empty($productos)) {
            error_log("Primer producto (nueva relación): " . print_r($productos[0], true));
        }
        
        return $productos;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProductosPorProveedorRelacion -> " . $e->getMessage());
        return [];
    }
}
    /**
     * Obtener detalles de una recepción
     */
    public static function obtenerDetalleRecepcion($idRecepcion) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_DETALLE_RECEPCION(:id_recepcion)";
            $query = $con->prepare($sql);
            $query->execute(['id_recepcion' => $idRecepcion]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerDetalleRecepcion -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todas las recepciones pendientes
     */
    public static function obtenerRecepcionesPendientes() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT 
                        rc.ID_RECEPCION,
                        p.NOMBRE AS PROVEEDOR,
                        u.NOMBRE_USUARIO,
                        rc.TOTAL_ORDEN,
                        rc.FECHA_RECEPCION,
                        rc.ESTADO_RECEPCION,
                        rc.OBSERVACIONES
                    FROM tbl_recepcion_compra rc
                    INNER JOIN tbl_proveedor p ON rc.ID_PROVEEDOR = p.ID_PROVEEDOR
                    INNER JOIN tbl_ms_usuarios u ON rc.ID_USUARIO = u.ID_USUARIO
                    WHERE rc.ESTADO_RECEPCION = 'PENDIENTE'
                    ORDER BY rc.FECHA_RECEPCION DESC";
            
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerRecepcionesPendientes -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todas las recepciones finalizadas
     */
    public static function obtenerRecepcionesFinalizadas() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT 
                        rc.ID_RECEPCION,
                        p.NOMBRE AS PROVEEDOR,
                        u.NOMBRE_USUARIO,
                        rc.TOTAL_ORDEN,
                        rc.FECHA_RECEPCION,
                        rc.ESTADO_RECEPCION,
                        rc.OBSERVACIONES,
                        c.ID_COMPRA
                    FROM tbl_recepcion_compra rc
                    INNER JOIN tbl_proveedor p ON rc.ID_PROVEEDOR = p.ID_PROVEEDOR
                    INNER JOIN tbl_ms_usuarios u ON rc.ID_USUARIO = u.ID_USUARIO
                    LEFT JOIN tbl_compra c ON rc.ID_RECEPCION = c.ID_RECEPCION
                    WHERE rc.ESTADO_RECEPCION = 'FINALIZADA'
                    ORDER BY rc.FECHA_RECEPCION DESC";
            
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerRecepcionesFinalizadas -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener lista de proveedores activos
     */
    public static function obtenerProveedores() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT 
                        ID_PROVEEDOR,
                        NOMBRE,
                        CONTACTO,
                        TELEFONO,
                        CORREO
                    FROM tbl_proveedor 
                    WHERE ESTADO = 'ACTIVO'
                    ORDER BY NOMBRE";
            
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerProveedores -> " . $e->getMessage());
            return [];
        }
    }

/**
 * Obtener recepciones con filtros
 */
public static function obtenerRecepcionesFiltradas($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    rc.ID_RECEPCION,
                    p.NOMBRE AS PROVEEDOR,
                    u.NOMBRE_USUARIO,
                    rc.TOTAL_ORDEN,
                    rc.FECHA_RECEPCION,
                    rc.ESTADO_RECEPCION,
                    rc.OBSERVACIONES
                FROM tbl_recepcion_compra rc
                INNER JOIN tbl_proveedor p ON rc.ID_PROVEEDOR = p.ID_PROVEEDOR
                INNER JOIN tbl_ms_usuarios u ON rc.ID_USUARIO = u.ID_USUARIO
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(rc.FECHA_RECEPCION) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(rc.FECHA_RECEPCION) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['id_proveedor'])) {
            $sql .= " AND rc.ID_PROVEEDOR = :id_proveedor";
            $params['id_proveedor'] = $filtros['id_proveedor'];
        }
        
        // Por defecto mostrar solo pendientes
        $sql .= " AND rc.ESTADO_RECEPCION = 'PENDIENTE'";
        
        $sql .= " ORDER BY rc.FECHA_RECEPCION DESC";
        
        $query = $con->prepare($sql);
        $query->execute($params);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerRecepcionesFiltradas -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener compras con filtros
 */
/**
 * Obtener compras con filtros
 */
public static function obtenerComprasFiltradas($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    c.ID_COMPRA,
                    p.NOMBRE AS PROVEEDOR,
                    u.NOMBRE_USUARIO AS USUARIO,
                    mp.NOMBRE AS MATERIA_PRIMA,
                    dc.CANTIDAD,
                    um.UNIDAD,
                    dc.PRECIO_UNITARIO,
                    dc.SUBTOTAL,
                    c.FECHA_COMPRA,
                    c.ESTADO_COMPRA,
                    c.TOTAL_COMPRA
                FROM tbl_compra c
                INNER JOIN tbl_proveedor p ON c.ID_PROVEEDOR = p.ID_PROVEEDOR
                INNER JOIN tbl_ms_usuarios u ON c.ID_USUARIO = u.ID_USUARIO
                INNER JOIN tbl_detalle_compra dc ON c.ID_COMPRA = dc.ID_COMPRA
                INNER JOIN tbl_materia_prima mp ON dc.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                INNER JOIN tbl_unidad_medida um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(c.FECHA_COMPRA) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(c.FECHA_COMPRA) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['id_proveedor'])) {
            $sql .= " AND c.ID_PROVEEDOR = :id_proveedor";
            $params['id_proveedor'] = $filtros['id_proveedor'];
        }
        
        if (!empty($filtros['estado_compra'])) {
            $sql .= " AND c.ESTADO_COMPRA = :estado_compra";
            $params['estado_compra'] = $filtros['estado_compra'];
        }
        
        $sql .= " ORDER BY c.FECHA_COMPRA DESC, c.ID_COMPRA DESC";
        
        $query = $con->prepare($sql);
        $query->execute($params);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerComprasFiltradas -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener detalle completo de una compra
 */
public static function obtenerDetalleCompra($idCompra) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_DETALLE_COMPRA(:id_compra)";
        $query = $con->prepare($sql);
        $query->execute(['id_compra' => $idCompra]);
        
        // Obtener información de la compra (primer resultado)
        $compra = $query->fetch(PDO::FETCH_ASSOC);
        
        // Obtener detalles (segundo resultado)
        $query->nextRowset();
        $detalles = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if ($compra) {
            return [
                'success' => true,
                'data' => [
                    'compra' => $compra,
                    'detalles' => $detalles
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Compra no encontrada'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerDetalleCompra -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener detalle de compra: ' . $e->getMessage()
        ];
    }
}

/**
 * Cancelar una orden de compra
 */
public static function cancelarOrdenCompra($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_CANCELAR_ORDEN_COMPRA(:id_recepcion, :motivo_cancelacion, :id_usuario, :modificado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_recepcion' => $datos['id_recepcion'],
            'motivo_cancelacion' => $datos['motivo_cancelacion'] ?? 'Cancelado por el usuario',
            'id_usuario' => $datos['id_usuario'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['MENSAJE'])) {
            return [
                'success' => true,
                'message' => $result['MENSAJE']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error desconocido al cancelar la orden'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::cancelarOrdenCompra -> " . $e->getMessage());
        
        // Manejar errores específicos de MySQL
        $mensajeError = 'Error al cancelar la orden de compra';
        if (strpos($e->getMessage(), 'La orden de compra no existe') !== false) {
            $mensajeError = 'La orden de compra no existe';
        } elseif (strpos($e->getMessage(), 'ya está cancelada') !== false) {
            $mensajeError = 'La orden de compra ya está cancelada';
        } elseif (strpos($e->getMessage(), 'ya finalizada') !== false) {
            $mensajeError = 'No se puede cancelar una orden ya finalizada';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}

/**
 * Obtener todas las recepciones canceladas
 */
public static function obtenerRecepcionesCanceladas($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    rc.ID_RECEPCION,
                    p.NOMBRE AS PROVEEDOR,
                    u.NOMBRE_USUARIO,
                    rc.TOTAL_ORDEN,
                    rc.FECHA_RECEPCION,
                    rc.ESTADO_RECEPCION,
                    rc.OBSERVACIONES,
                    rc.MOTIVO_CANCELACION,
                    rc.FECHA_MODIFICACION,
                    rc.MODIFICADO_POR
                FROM tbl_recepcion_compra rc
                INNER JOIN tbl_proveedor p ON rc.ID_PROVEEDOR = p.ID_PROVEEDOR
                INNER JOIN tbl_ms_usuarios u ON rc.ID_USUARIO = u.ID_USUARIO
                WHERE rc.ESTADO_RECEPCION = 'CANCELADA'";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(rc.FECHA_MODIFICACION) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(rc.FECHA_MODIFICACION) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['id_proveedor'])) {
            $sql .= " AND rc.ID_PROVEEDOR = :id_proveedor";
            $params['id_proveedor'] = $filtros['id_proveedor'];
        }
        
        $sql .= " ORDER BY rc.FECHA_MODIFICACION DESC";
        
        $query = $con->prepare($sql);
        $query->execute($params);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerRecepcionesCanceladas -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener recepciones finalizadas con filtros
 */
public static function obtenerRecepcionesFinalizadasFiltradas($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_RECEPCIONES_FINALIZADAS(:fecha_inicio, :fecha_fin, :id_proveedor)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'fecha_inicio' => $filtros['fecha_inicio'] ?? null,
            'fecha_fin' => $filtros['fecha_fin'] ?? null,
            'id_proveedor' => $filtros['id_proveedor'] ?? null
        ]);
        
        $recepciones = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $recepciones;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerRecepcionesFinalizadasFiltradas -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener toda la materia prima con niveles de inventario
 */
public static function obtenerMateriaPrima() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_MATERIA_PRIMA()";
        $query = $con->prepare($sql);
        $query->execute();
        
        $materiaPrima = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear fechas
        foreach ($materiaPrima as &$item) {
            if (!empty($item['FECHA_CREACION'])) {
                $item['FECHA_CREACION_FORMATEADA'] = date('d-m-Y H:i', strtotime($item['FECHA_CREACION']));
            }
            if (!empty($item['FECHA_MODIFICACION'])) {
                $item['FECHA_MODIFICACION_FORMATEADA'] = date('d-m-Y H:i', strtotime($item['FECHA_MODIFICACION']));
            }
        }
        
        return $materiaPrima;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerMateriaPrima -> " . $e->getMessage());
        return [];
    }
}
    

/**
 * Ingresar materia prima al inventario
 */
public static function ingresarInventario($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_INGRESAR_INVENTARIO(:id_materia_prima, :cantidad, :descripcion, :id_usuario, :creado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_materia_prima' => $datos['id_materia_prima'],
            'cantidad' => $datos['cantidad'],
            'descripcion' => $datos['descripcion'] ?? '', // Usar valor por defecto si no existe
            'id_usuario' => $datos['id_usuario'],
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA'
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['MENSAJE'])) {
            return [
                'success' => true,
                'message' => $result['MENSAJE'],
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error desconocido al ingresar al inventario'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::ingresarInventario -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al ingresar al inventario';
        if (strpos($e->getMessage(), 'Cantidad a ingresar excede el stock disponible') !== false) {
            $mensajeError = 'La cantidad a ingresar excede el stock disponible en materia prima';
        } elseif (strpos($e->getMessage(), 'Materia prima no encontrada') !== false) {
            $mensajeError = 'La materia prima no existe o está inactiva';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}



/**
 * Obtener proveedores con filtros
 */
public static function obtenerProveedoresFiltrados($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PROVEEDORES(:filtro_nombre, :filtro_estado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
            'filtro_estado' => $filtros['filtro_estado'] ?? null
        ]);
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $proveedores;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedoresFiltrados -> " . $e->getMessage());
        return [];
    }
}

/**
 * Cambiar estado de un proveedor
 */
public static function cambiarEstadoProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "UPDATE tbl_proveedor 
                SET ESTADO = :estado,
                    FECHA_MODIFICACION = NOW(),
                    MODIFICADO_POR = :modificado_por
                WHERE ID_PROVEEDOR = :id_proveedor";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor' => $datos['id_proveedor'],
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ]);
        
        if ($query->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Estado del proveedor actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar el estado del proveedor'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::cambiarEstadoProveedor -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al cambiar el estado del proveedor: ' . $e->getMessage()
        ];
    }
}

/**
 * Exportar proveedores a PDF (para el reporte)
 */
public static function obtenerProveedoresParaPDF($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PROVEEDORES(:filtro_nombre, :filtro_estado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
            'filtro_estado' => $filtros['filtro_estado'] ?? null
        ]);
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $proveedores;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedoresParaPDF -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener proveedor por ID
 */
public static function obtenerProveedorPorId($idProveedor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    ID_PROVEEDOR,
                    NOMBRE,
                    CONTACTO,
                    TELEFONO,
                    CORREO,
                    DIRECCION,
                    ESTADO,
                    FECHA_CREACION,
                    CREADO_POR,
                    FECHA_MODIFICACION,
                    MODIFICADO_POR
                FROM tbl_proveedor 
                WHERE ID_PROVEEDOR = :id_proveedor";
        
        $query = $con->prepare($sql);
        $query->execute(['id_proveedor' => $idProveedor]);
        
        $proveedor = $query->fetch(PDO::FETCH_ASSOC);
        
        return $proveedor;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedorPorId -> " . $e->getMessage());
        return null;
    }
}

/**
 * Editar proveedor
 */
public static function editarProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_EDITAR_PROVEEDOR(:id_proveedor, :nombre, :contacto, :telefono, :correo, :direccion, :estado, :modificado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor' => $datos['id_proveedor'],
            'nombre' => $datos['nombre'],
            'contacto' => $datos['contacto'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['MENSAJE'])) {
            return [
                'success' => true,
                'message' => $result['MENSAJE'],
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error desconocido al editar el proveedor'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::editarProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al editar el proveedor';
        if (strpos($e->getMessage(), 'Ya existe un proveedor con ese nombre') !== false) {
            $mensajeError = 'Ya existe un proveedor con ese nombre';
        } elseif (strpos($e->getMessage(), 'El proveedor no existe') !== false) {
            $mensajeError = 'El proveedor no existe';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}

public static function registrarProductoProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_REGISTRAR_PRODUCTO_PROVEEDOR(:nombre_producto, :descripcion, :id_unidad_medida, :id_proveedor, :precio_unitario, :creado_por, :id_usuario, @id_proveedor_producto, @mensaje)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'nombre_producto' => $datos['nombre_producto'],
            'descripcion' => $datos['descripcion'] ?? null,
            'id_unidad_medida' => $datos['id_unidad_medida'],
            'id_proveedor' => $datos['id_proveedor'],
            'precio_unitario' => $datos['precio_unitario'],
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA',
            'id_usuario' => $datos['id_usuario'] ?? 1 // ID de usuario por defecto
        ]);
        
        // Obtener los parámetros de salida
        $outputQuery = $con->query("SELECT @id_proveedor_producto as id_proveedor_producto, @mensaje as mensaje");
        $result = $outputQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['mensaje'])) {
            if ($result['id_proveedor_producto'] > 0) {
                return [
                    'success' => true,
                    'message' => $result['mensaje'],
                    'data' => [
                        'id_proveedor_producto' => $result['id_proveedor_producto']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['mensaje']
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Error desconocido al registrar el producto'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarProductoProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al registrar el producto';
        if (strpos($e->getMessage(), 'Ya existe un producto con este nombre') !== false) {
            $mensajeError = 'Ya existe un producto con este nombre para el proveedor seleccionado';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}

public static function obtenerUnidadesMedida() {
    try {
        $con = connectionDB::getConnection();
        
        // CORREGIDO: Sin referencia a ESTADO
        $sql = "SELECT ID_UNIDAD_MEDIDA, UNIDAD, DESCRIPCION 
                FROM TBL_UNIDAD_MEDIDA 
                ORDER BY UNIDAD"; 
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $unidades
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerUnidadesMedida -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener unidades de medida',
            'data' => []
        ];
    }
}
public static function obtenerProveedoresActivos() {
    try {
        $con = connectionDB::getConnection();
        
        // CORREGIDO: Sin referencia a ESTADO
        $sql = "SELECT ID_PROVEEDOR, NOMBRE 
                FROM TBL_PROVEEDOR 
                ORDER BY NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $proveedores
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedoresActivos -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener proveedores',
            'data' => []
        ];
    }
}
// Agregar al modelo comprasModel
public static function obtenerProveedoresActivosRegistroProductos() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PROVEEDORES_ACTIVOS_REGISTRO()";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $proveedores
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedoresActivosRegistroProductos -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener proveedores activos',
            'data' => []
        ];
    }
}

public static function listarProductosProveedores($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_LISTAR_PRODUCTOS_PROVEEDORES(:filtro_nombre, :filtro_proveedor, :filtro_estado)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'filtro_nombre' => $filtros['filtro_nombre'] ?? null,
            'filtro_proveedor' => $filtros['filtro_proveedor'] ?? null,
            'filtro_estado' => $filtros['filtro_estado'] ?? null
        ]);
        
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $productos
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::listarProductosProveedores -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener productos de proveedores',
            'data' => []
        ];
    }
}
/**
 * Obtener productos activos para selección
 */
public static function obtenerProductosActivos() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    pp.ID_PROVEEDOR_PRODUCTO,
                    pp.NOMBRE_PRODUCTO,
                    pp.PRECIO_UNITARIO,
                    um.UNIDAD
                FROM tbl_proveedor_productos pp
                INNER JOIN tbl_unidad_medida um ON pp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE pp.ESTADO = 'ACTIVO'
                ORDER BY pp.NOMBRE_PRODUCTO";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProductosActivos -> " . $e->getMessage());
        return [];
    }
}

/**
 * Obtener productos por proveedor
 */
public static function obtenerProductosPorProveedor($idProveedor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    ppr.ID_PROVEEDOR_PRODUCTO,
                    pp.NOMBRE_PRODUCTO,
                    pp.PRECIO_UNITARIO,
                    um.UNIDAD
                FROM tbl_proveedor_productos_relacion ppr
                INNER JOIN tbl_proveedor_productos pp ON ppr.ID_PROVEEDOR_PRODUCTO = pp.ID_PROVEEDOR_PRODUCTO
                INNER JOIN tbl_unidad_medida um ON pp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE ppr.ID_PROVEEDOR = :id_proveedor 
                AND ppr.ESTADO = 'ACTIVO'
                AND pp.ESTADO = 'ACTIVO'";
        
        $query = $con->prepare($sql);
        $query->execute(['id_proveedor' => $idProveedor]);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProductosPorProveedor -> " . $e->getMessage());
        return [];
    }
}

/**
 * Actualizar relación de productos del proveedor
 */
public static function actualizarProductosProveedor($idProveedor, $productos, $usuario) {
    try {
        $con = connectionDB::getConnection();
        
        // Iniciar transacción
        $con->beginTransaction();
        
        // Desactivar todas las relaciones existentes
        $sqlDesactivar = "UPDATE tbl_proveedor_productos_relacion 
                         SET ESTADO = 'INACTIVO', 
                             FECHA_MODIFICACION = NOW(),
                             MODIFICADO_POR = :modificado_por
                         WHERE ID_PROVEEDOR = :id_proveedor";
        
        $queryDesactivar = $con->prepare($sqlDesactivar);
        $queryDesactivar->execute([
            'id_proveedor' => $idProveedor,
            'modificado_por' => $usuario
        ]);
        
        // Activar o insertar nuevas relaciones
        foreach ($productos as $idProducto) {
            // Verificar si la relación ya existe
            $sqlVerificar = "SELECT COUNT(*) as existe 
                            FROM tbl_proveedor_productos_relacion 
                            WHERE ID_PROVEEDOR = :id_proveedor 
                            AND ID_PROVEEDOR_PRODUCTO = :id_producto";
            
            $queryVerificar = $con->prepare($sqlVerificar);
            $queryVerificar->execute([
                'id_proveedor' => $idProveedor,
                'id_producto' => $idProducto
            ]);
            
            $result = $queryVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($result['existe'] > 0) {
                // Reactivar relación existente
                $sqlReactivar = "UPDATE tbl_proveedor_productos_relacion 
                                SET ESTADO = 'ACTIVO',
                                    FECHA_MODIFICACION = NOW(),
                                    MODIFICADO_POR = :modificado_por
                                WHERE ID_PROVEEDOR = :id_proveedor 
                                AND ID_PROVEEDOR_PRODUCTO = :id_producto";
                
                $queryReactivar = $con->prepare($sqlReactivar);
                $queryReactivar->execute([
                    'id_proveedor' => $idProveedor,
                    'id_producto' => $idProducto,
                    'modificado_por' => $usuario
                ]);
            } else {
                // Insertar nueva relación
                $sqlInsertar = "INSERT INTO tbl_proveedor_productos_relacion 
                               (ID_PROVEEDOR, ID_PROVEEDOR_PRODUCTO, CREADO_POR)
                               VALUES (:id_proveedor, :id_producto, :creado_por)";
                
                $queryInsertar = $con->prepare($sqlInsertar);
                $queryInsertar->execute([
                    'id_proveedor' => $idProveedor,
                    'id_producto' => $idProducto,
                    'creado_por' => $usuario
                ]);
            }
        }
        
        $con->commit();
        return ['success' => true, 'message' => 'Productos actualizados correctamente'];
        
    } catch (\PDOException $e) {
        $con->rollBack();
        error_log("comprasModel::actualizarProductosProveedor -> " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar productos del proveedor'];
    }
}
public static function obtenerProductoProveedorPorId($id_producto_proveedor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PRODUCTO_PROVEEDOR_POR_ID(:id_producto_proveedor)";
        $query = $con->prepare($sql);
        $query->execute(['id_producto_proveedor' => $id_producto_proveedor]);
        
        $producto = $query->fetch(PDO::FETCH_ASSOC);
        
        return $producto;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProductoProveedorPorId -> " . $e->getMessage());
        return null;
    }
}

public static function obtenerUnidadesMedidaProductosProveedores() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_UNIDADES_MEDIDA_PRODUCTOS_PROVEEDORES()";
        $query = $con->prepare($sql);
        $query->execute();
        
        $unidades = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $unidades;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerUnidadesMedidaProductosProveedores -> " . $e->getMessage());
        return [];
    }
}

public static function cambiarEstadoProductoProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        // CORREGIDO: Usar el nombre correcto de la tabla
        $sql = "UPDATE tbl_proveedor_productos 
                SET ESTADO = :estado,
                    FECHA_MODIFICACION = NOW(),
                    MODIFICADO_POR = :modificado_por
                WHERE ID_PROVEEDOR_PRODUCTO = :id_proveedor_producto";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor_producto' => $datos['id_proveedor_producto'],
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ]);
        
        if ($query->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Estado del producto actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar el estado del producto'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::cambiarEstadoProductoProveedor -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al cambiar el estado del producto: ' . $e->getMessage()
        ];
    }
}


public static function editarProductoProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_EDITAR_PRODUCTOS_PROVEEDORES(
            :p_ID_PROVEEDOR_PRODUCTO, 
            :p_NOMBRE_PRODUCTO, 
            :p_ID_UNIDAD_MEDIDA, 
            :p_DESCRIPCION, 
            :p_PRECIO_UNITARIO, 
            :p_MINIMO,
            :p_MAXIMO,
            :p_ESTADO, 
            :p_MODIFICADO_POR,
            :p_ID_USUARIO
        )";
        
        $query = $con->prepare($sql);
        $query->execute([
            'p_ID_PROVEEDOR_PRODUCTO' => $datos['id_proveedor_producto'],
            'p_NOMBRE_PRODUCTO' => $datos['nombre_producto'],
            'p_ID_UNIDAD_MEDIDA' => $datos['id_unidad_medida'],
            'p_DESCRIPCION' => $datos['descripcion'] ?? null,
            'p_PRECIO_UNITARIO' => $datos['precio_unitario'],
            'p_MINIMO' => $datos['minimo'] ?? 0,
            'p_MAXIMO' => $datos['maximo'] ?? 100,
            'p_ESTADO' => $datos['estado'],
            'p_MODIFICADO_POR' => $datos['modificado_por'],
            'p_ID_USUARIO' => $datos['id_usuario'] ?? 1
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['mensaje'])) {
            return ['status' => 200, 'message' => $result['mensaje']];
        } else {
            return ['status' => 500, 'message' => 'Error desconocido al actualizar el producto'];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::editarProductoProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al actualizar el producto: ' . $e->getMessage();
        if (strpos($e->getMessage(), 'El producto no existe') !== false) {
            $mensajeError = 'El producto no existe';
        } elseif (strpos($e->getMessage(), 'El nombre del producto no puede estar vacío') !== false) {
            $mensajeError = 'El nombre del producto no puede estar vacío';
        } elseif (strpos($e->getMessage(), 'El precio unitario debe ser mayor a 0') !== false) {
            $mensajeError = 'El precio unitario debe ser mayor a 0';
        } elseif (strpos($e->getMessage(), 'La unidad de medida seleccionada no existe') !== false) {
            $mensajeError = 'La unidad de medida seleccionada no existe';
        }
        
        return ['status' => 500, 'message' => $mensajeError];
    }
}


// Agregar al modelo existente
public static function obtenerMateriaPrimaPorId($id_materia_prima) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT 
                    mp.ID_MATERIA_PRIMA,
                    mp.NOMBRE,
                    mp.DESCRIPCION,
                    mp.ID_UNIDAD_MEDIDA,
                    um.UNIDAD,
                    um.DESCRIPCION as DESC_UNIDAD,
                    -- SE ELIMINARON: CANTIDAD, MINIMO, MAXIMO
                    mp.PRECIO_PROMEDIO,
                    mp.ESTADO,
                    mp.FECHA_CREACION,
                    mp.CREADO_POR,
                    mp.FECHA_MODIFICACION,
                    mp.MODIFICADO_POR,
                    DATE_FORMAT(mp.FECHA_CREACION, '%d-%m-%Y %H:%i') as FECHA_CREACION_FORMATEADA
                FROM TBL_MATERIA_PRIMA mp
                INNER JOIN TBL_UNIDAD_MEDIDA um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE mp.ID_MATERIA_PRIMA = :id_materia_prima";
        
        $query = $con->prepare($sql);
        $query->execute(['id_materia_prima' => $id_materia_prima]);
        
        $materiaPrima = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($materiaPrima) {
            return [
                'success' => true,
                'data' => $materiaPrima
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Materia prima no encontrada'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerMateriaPrimaPorId -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener la materia prima',
            'data' => null
        ];
    }
}

public static function editarMateriaPrima($datos) {
    try {
        $con = connectionDB::getConnection();
        
        // Actualizado: eliminados minimo y maximo del CALL
        $sql = "CALL SP_EDITAR_MATERIA_PRIMA(:id_materia_prima, :nombre, :descripcion, :id_unidad_medida, :estado, :modificado_por, :id_usuario, @mensaje)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_materia_prima' => $datos['id_materia_prima'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'id_unidad_medida' => $datos['id_unidad_medida'],
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA',
            'id_usuario' => $datos['id_usuario'] ?? 1
        ]);
        
        // Obtener el mensaje de salida
        $outputQuery = $con->query("SELECT @mensaje as mensaje");
        $result = $outputQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['mensaje'])) {
            if (strpos($result['mensaje'], 'exitosamente') !== false) {
                return [
                    'success' => true,
                    'message' => $result['mensaje']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['mensaje']
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Error desconocido al editar la materia prima'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::editarMateriaPrima -> " . $e->getMessage());
        
        $mensajeError = 'Error al editar la materia prima';
        if (strpos($e->getMessage(), 'Ya existe otra materia prima') !== false) {
            $mensajeError = 'Ya existe otra materia prima con este nombre';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}
public static function guardarRelacionProductoProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $idProveedor = $datos['id_proveedor'];
        $productos = $datos['productos'] ?? [];
        $usuario = $_SESSION['usuario']['username'] ?? 'SISTEMA';
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? 1;
        
        // Convertir array de productos a JSON
        $productosJson = json_encode($productos);
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_GUARDAR_RELACION_PRODUCTO_PROVEEDOR(:id_proveedor, :productos_json, :id_usuario, :usuario)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor' => $idProveedor,
            'productos_json' => $productosJson,
            'id_usuario' => $idUsuario,
            'usuario' => $usuario
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true, 
            'message' => $result['MENSAJE'] ?? 'Productos actualizados correctamente',
            'data' => [
                'proveedor' => $result['PROVEEDOR'] ?? '',
                'total_productos' => $result['TOTAL_PRODUCTOS'] ?? count($productos)
            ]
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::guardarRelacionProductoProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al actualizar productos del proveedor';
        if (strpos($e->getMessage(), 'Proveedor no encontrado') !== false) {
            $mensajeError = 'El proveedor no existe';
        }
        
        return ['success' => false, 'message' => $mensajeError . ': ' . $e->getMessage()];
    }
}
/**
 * Obtener relaciones existentes con información completa
 */
public static function obtenerRelacionesProductoProveedor() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "
            SELECT 
                p.ID_PROVEEDOR,
                p.NOMBRE AS PROVEEDOR,
                p.CORREO,
                p.ESTADO,
                COUNT(ppr.ID_PROVEEDOR_PRODUCTO_RELACION) AS CANTIDAD_PRODUCTOS,
                GROUP_CONCAT(
                    DISTINCT CONCAT(pp.NOMBRE_PRODUCTO, '|', pp.PRECIO_UNITARIO) 
                    SEPARATOR ';'
                ) AS PRODUCTOS_STRING
            FROM tbl_proveedor p
            LEFT JOIN tbl_proveedor_productos_relacion ppr ON p.ID_PROVEEDOR = ppr.ID_PROVEEDOR AND ppr.ESTADO = 'ACTIVO'
            LEFT JOIN tbl_proveedor_productos pp ON ppr.ID_PROVEEDOR_PRODUCTO = pp.ID_PROVEEDOR_PRODUCTO AND pp.ESTADO = 'ACTIVO'
            WHERE p.ESTADO = 'ACTIVO'
            GROUP BY p.ID_PROVEEDOR, p.NOMBRE, p.CORREO, p.ESTADO
            HAVING CANTIDAD_PRODUCTOS > 0
            ORDER BY p.NOMBRE
        ";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        $relaciones = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear productos
        foreach ($relaciones as &$relacion) {
            $relacion['PRODUCTOS'] = [];
            if ($relacion['PRODUCTOS_STRING']) {
                $productosArray = explode(';', $relacion['PRODUCTOS_STRING']);
                foreach ($productosArray as $productoStr) {
                    list($nombre, $precio) = explode('|', $productoStr);
                    $relacion['PRODUCTOS'][] = [
                        'NOMBRE' => $nombre,
                        'PRECIO' => $precio
                    ];
                }
            }
            unset($relacion['PRODUCTOS_STRING']);
        }
        
        return $relaciones;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerRelacionesProductoProveedor -> " . $e->getMessage());
        return [];
    }
}

public static function eliminarRelacionProductoProveedor($idProveedor) {
    try {
        $con = connectionDB::getConnection();
        $usuario = $_SESSION['usuario']['username'] ?? 'SISTEMA';
        $idUsuario = $_SESSION['usuario']['id_usuario'] ?? 1;
        
        // Llamar al procedimiento almacenado
        $sql = "CALL SP_ELIMINAR_RELACION_PRODUCTO_PROVEEDOR(:id_proveedor, :id_usuario, :usuario)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_proveedor' => $idProveedor,
            'id_usuario' => $idUsuario,
            'usuario' => $usuario
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true, 
            'message' => $result['MENSAJE'] ?? 'Relaciones eliminadas correctamente',
            'data' => [
                'proveedor' => $result['PROVEEDOR'] ?? '',
                'relaciones_eliminadas' => $result['RELACIONES_ELIMINADAS'] ?? 0
            ]
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::eliminarRelacionProductoProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos
        $mensajeError = 'Error al eliminar las relaciones';
        if (strpos($e->getMessage(), 'Proveedor no encontrado') !== false) {
            $mensajeError = 'El proveedor no existe';
        }
        
        return ['success' => false, 'message' => $mensajeError . ': ' . $e->getMessage()];
    }
}
public static function registrarProveedor($datos) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_REGISTRAR_PROVEEDOR(:nombre, :contacto, :telefono, :correo, :direccion, :creado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'nombre' => $datos['nombre'],
            'contacto' => $datos['contacto'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA'
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['ID_PROVEEDOR'])) {
            return [
                'success' => true,
                'message' => 'Proveedor registrado exitosamente',
                'data' => $result // Esto incluye ID_PROVEEDOR
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al registrar el proveedor'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarProveedor -> " . $e->getMessage());
        
        // Manejar errores específicos de MySQL
        $mensajeError = 'Error al registrar el proveedor';
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            if (strpos($e->getMessage(), 'NOMBRE') !== false) {
                $mensajeError = 'Ya existe un proveedor con este nombre';
            } elseif (strpos($e->getMessage(), 'CORREO') !== false) {
                $mensajeError = 'Ya existe un proveedor con este correo';
            } elseif (strpos($e->getMessage(), 'CONTACTO') !== false) {
                $mensajeError = 'Ya existe un proveedor con este contacto';
            }
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    }
}

public static function validarProveedorUnico($campo, $valor) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT COUNT(*) as existe 
                FROM TBL_PROVEEDOR 
                WHERE $campo = :valor 
                AND ESTADO = 'ACTIVO'";
        
        $query = $con->prepare($sql);
        $query->execute(['valor' => $valor]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'disponible' => ($result['existe'] == 0)
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::validarProveedorUnico -> " . $e->getMessage());
        return [
            'success' => false,
            'disponible' => false
        ];
    }
}
/**
 * Anular una compra y revertir inventario
 */
public static function anularCompra($datos) {
    $con = null;
    try {
        $con = connectionDB::getConnection();
        
        error_log("Ejecutando SP_ANULAR_COMPRA con: " . print_r($datos, true));
        
        $sql = "CALL SP_ANULAR_COMPRA(:id_compra, :motivo_anulacion, :id_usuario, :modificado_por)";
        
        $query = $con->prepare($sql);
        $resultado = $query->execute([
            'id_compra' => $datos['id_compra'],
            'motivo_anulacion' => $datos['motivo_anulacion'],
            'id_usuario' => $datos['id_usuario'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ]);
        
        error_log("Resultado de execute: " . ($resultado ? 'true' : 'false'));
        
        if ($resultado) {
            $result = $query->fetch(PDO::FETCH_ASSOC);
            error_log("Resultado del fetch: " . print_r($result, true));
            
            if ($result && isset($result['MENSAJE'])) {
                return [
                    'success' => true,
                    'message' => $result['MENSAJE']
                ];
            } else {
                // Intentar obtener información de error
                $errorInfo = $query->errorInfo();
                error_log("Error info: " . print_r($errorInfo, true));
                
                return [
                    'success' => false,
                    'message' => 'No se pudo obtener respuesta del procedimiento almacenado'
                ];
            }
        } else {
            $errorInfo = $query->errorInfo();
            error_log("Error en execute: " . print_r($errorInfo, true));
            
            return [
                'success' => false,
                'message' => 'Error al ejecutar el procedimiento almacenado: ' . $errorInfo[2]
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("PDOException en comprasModel::anularCompra: " . $e->getMessage());
        error_log("Código de error: " . $e->getCode());
        error_log("SQL State: " . $e->errorInfo[0] ?? 'N/A');
        
        // Manejar errores específicos
        $mensajeError = 'Error al anular la compra: ' . $e->getMessage();
        if (strpos($e->getMessage(), 'La compra no existe') !== false) {
            $mensajeError = 'La compra no existe';
        } elseif (strpos($e->getMessage(), 'Solo se pueden anular compras completadas') !== false) {
            $mensajeError = 'Solo se pueden anular compras completadas';
        } elseif (strpos($e->getMessage(), 'stock insuficiente') !== false) {
            $mensajeError = 'No hay suficiente stock para revertir la compra';
        }
        
        return [
            'success' => false,
            'message' => $mensajeError
        ];
    } catch (\Exception $e) {
        error_log("Exception general en comprasModel::anularCompra: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error general: ' . $e->getMessage()
        ];
    }
}
}