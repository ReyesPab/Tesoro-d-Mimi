<?php
namespace App\models;

use App\db\connectionDB;
use PDO;
use PDOException;

class DashboardModel {
    private $conn;

    public function __construct() {
        try {
            $this->conn = connectionDB::getConnection();
        } catch (\Exception $e) {
            error_log("Error en constructor DashboardModel: " . $e->getMessage());
            throw new \Exception("No se pudo conectar a la base de datos");
        }
    }

    // Obtener estadísticas generales del sistema
    public function obtenerEstadisticasGenerales() {
    try {
        $stats = [];

        // Total de usuarios - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_USUARIOS WHERE ESTADO_USUARIO != 'ELIMINADO' AND ESTADO_USUARIO IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Usuarios activos (con sesión reciente en las últimas 24 horas) - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_USUARIOS 
                 WHERE (ESTADO_USUARIO = 'ACTIVO' OR ESTADO_USUARIO = 'Activo')
                 AND FECHA_ULTIMA_CONEXION >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 AND FECHA_ULTIMA_CONEXION IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['sesiones_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Usuarios bloqueados - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_USUARIOS 
                 WHERE UPPER(TRIM(ESTADO_USUARIO)) = 'BLOQUEADO' 
                 OR UPPER(TRIM(ESTADO_USUARIO)) = 'BLOQUEO'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['usuarios_bloqueados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Usuarios nuevos - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_USUARIOS 
                 WHERE UPPER(TRIM(ESTADO_USUARIO)) = 'NUEVO' 
                 OR PRIMER_INGRESO = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['usuarios_nuevos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Actividades de hoy (bitácora del día actual) - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_BITACORA 
                 WHERE DATE(FECHA) = CURDATE() 
                 AND FECHA IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['actividades_hoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de registros en bitácora - CORREGIDO
        $query = "SELECT COUNT(*) as total FROM TBL_MS_BITACORA WHERE FECHA IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_bitacora'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Alertas de stock bajo (materia prima) - CORREGIDO (sin usar MINIMO, MAXIMO de materia_prima)
        $query = "SELECT COUNT(*) as total 
                 FROM TBL_INVENTARIO_MATERIA_PRIMA inv
                 JOIN TBL_MATERIA_PRIMA mp ON inv.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                 WHERE inv.CANTIDAD <= inv.MINIMO 
                 AND mp.ESTADO = 'ACTIVO'
                 AND inv.MINIMO IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['alertas_stock_bajo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Alertas de stock excesivo (materia prima) - CORREGIDO
        $query = "SELECT COUNT(*) as total 
                 FROM TBL_INVENTARIO_MATERIA_PRIMA inv
                 JOIN TBL_MATERIA_PRIMA mp ON inv.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                 WHERE inv.CANTIDAD >= inv.MAXIMO 
                 AND mp.ESTADO = 'ACTIVO'
                 AND inv.MAXIMO IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['alertas_stock_excesivo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de compras del último mes - CORREGIDO
       $query = "SELECT COUNT(*) as total 
         FROM TBL_COMPRA 
         WHERE ESTADO_COMPRA = 'COMPLETADA'
         AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
         AND TOTAL_COMPRA > 0";
$stmt = $this->conn->prepare($query);
$stmt->execute();
$stats['total_compras'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total de productos en inventario
        $query = "SELECT COUNT(*) as total FROM TBL_PRODUCTO WHERE ESTADO = 'ACTIVO'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Órdenes de producción activas
        $query = "SELECT COUNT(*) as total FROM TBL_PRODUCCION 
                 WHERE ID_ESTADO_PRODUCCION IN (1, 4)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['ordenes_produccion_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Ventas del mes actual - INCLUIR TODAS LAS VENTAS (sin cliente y con cliente)
        $query = "SELECT COUNT(*) as total FROM TBL_FACTURA 
                 WHERE MONTH(FECHA_VENTA) = MONTH(CURDATE()) 
                 AND YEAR(FECHA_VENTA) = YEAR(CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['ventas_mes_actual'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Actividad reciente del sistema (últimos 7 días)
        $query = "SELECT DATE(FECHA) as fecha, COUNT(*) as total
                 FROM TBL_MS_BITACORA 
                 WHERE FECHA >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 AND FECHA IS NOT NULL
                 GROUP BY DATE(FECHA)
                 ORDER BY fecha DESC
                 LIMIT 7";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['actividad_reciente'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Uso de módulos (conteo por tipo de acción en bitácora)
        $query = "SELECT ACCION, COUNT(*) as total
                 FROM TBL_MS_BITACORA 
                 WHERE FECHA >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 AND FECHA IS NOT NULL
                 GROUP BY ACCION
                 ORDER BY total DESC
                 LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['uso_modulos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;

    } catch (PDOException $e) {
        error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
        // Retornar valores por defecto en caso de error
        return [
            'total_usuarios' => 0,
            'sesiones_activas' => 0,
            'usuarios_bloqueados' => 0,
            'usuarios_nuevos' => 0,
            'actividades_hoy' => 0,
            'total_bitacora' => 0,
            'alertas_stock_bajo' => 0,
            'alertas_stock_excesivo' => 0,
            'total_compras' => 0,
            'total_productos' => 0,
            'ordenes_produccion_activas' => 0,
            'ventas_mes_actual' => 0,
            'actividad_reciente' => [],
            'uso_modulos' => []
        ];
    }
}

    /**
     * Obtener ventas recientes para mostrar en modal (últimas N facturas activas)
     * @param int $limit
     * @return array
     */
    public function obtenerVentasRecientes($limit = 10) {
        try {
            $sql = "SELECT f.ID_FACTURA, f.FECHA_VENTA, f.TOTAL_VENTA, f.ESTADO_FACTURA,
                           c.NOMBRE as cliente_nombre, c.APELLIDO as cliente_apellido,
                           mp.METODO_PAGO, u.NOMBRE_USUARIO as usuario_nombre
                    FROM tbl_factura f
                    LEFT JOIN tbl_cliente c ON f.ID_CLIENTE = c.ID_CLIENTE
                    LEFT JOIN tbl_metodo_pago mp ON f.ID_METODO_PAGO = mp.ID_METODO_PAGO
                    LEFT JOIN tbl_ms_usuarios u ON f.ID_USUARIO = u.ID_USUARIO
                    ORDER BY f.FECHA_VENTA DESC
                    LIMIT :limit";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Normalize client full name
            foreach ($rows as &$r) {
                $r['cliente'] = trim(($r['cliente_nombre'] ?? '') . ' ' . ($r['cliente_apellido'] ?? '')) ?: 'Sin Cliente';
                unset($r['cliente_nombre'], $r['cliente_apellido']);
                $r['TOTAL_VENTA'] = isset($r['TOTAL_VENTA']) ? (float)$r['TOTAL_VENTA'] : 0.0;
            }

            return $rows;
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasRecientes: " . $e->getMessage());
            return [];
        }
    }

    // Obtener detalles de usuarios por estado
    public function obtenerDetalleUsuarios() {
    try {
        $query = "SELECT 
                     UPPER(TRIM(ESTADO_USUARIO)) as estado,
                     COUNT(*) as total,
                     COUNT(CASE WHEN PRIMER_INGRESO = 0 THEN 1 END) as primer_ingreso
                 FROM TBL_MS_USUARIOS 
                 WHERE UPPER(TRIM(ESTADO_USUARIO)) != 'ELIMINADO'
                 GROUP BY UPPER(TRIM(ESTADO_USUARIO))";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerDetalleUsuarios: " . $e->getMessage());
        return [];
    }
}

    public function obtenerSesionesActivas24h() {
    try {
        $query = "SELECT 
                     USUARIO,
                     NOMBRE_USUARIO,
                     FECHA_ULTIMA_CONEXION,
                     ESTADO_USUARIO
                 FROM TBL_MS_USUARIOS 
                 WHERE FECHA_ULTIMA_CONEXION >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 AND UPPER(TRIM(ESTADO_USUARIO)) = 'ACTIVO'
                 AND FECHA_ULTIMA_CONEXION IS NOT NULL
                 ORDER BY FECHA_ULTIMA_CONEXION DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerSesionesActivas24h: " . $e->getMessage());
        return [];
    }
}

// En tu DashboardModel.php, agrega este método:
public function obtenerTotalSesionesActivas24h() {
    try {
        $query = "SELECT COUNT(*) as total 
                 FROM TBL_MS_USUARIOS 
                 WHERE FECHA_ULTIMA_CONEXION >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 AND UPPER(TRIM(ESTADO_USUARIO)) = 'ACTIVO'
                 AND FECHA_ULTIMA_CONEXION IS NOT NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['total'] : 0;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerTotalSesionesActivas24h: " . $e->getMessage());
        return 0;
    }
}

    // Método para obtener todas las alertas del sistema
public function obtenerAlertasSistema() {
    try {
        // Primero ejecutar el procedimiento para generar alertas
        $stmt = $this->conn->prepare("CALL sp_generar_alertas_sistema()");
        $stmt->execute();
        
        // Obtener alertas activas
        $query = "SELECT 
                     a.*,
                     CASE 
                         WHEN a.NIVEL_URGENCIA = 'CRITICA' THEN 4
                         WHEN a.NIVEL_URGENCIA = 'ALTA' THEN 3
                         WHEN a.NIVEL_URGENCIA = 'MEDIA' THEN 2
                         ELSE 1
                     END as prioridad
                 FROM tbl_alertas_sistema a
                 WHERE a.ESTADO = 'ACTIVA'
                 AND a.FECHA_EXPIRACION > NOW()
                 ORDER BY prioridad DESC, a.FECHA_CREACION DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerAlertasSistema: " . $e->getMessage());
        return [];
    }
}

// Método para obtener estadísticas de alertas
public function obtenerEstadisticasAlertas() {
    try {
        $query = "SELECT 
                     TIPO_ALERTA,
                     COUNT(*) as total,
                     SUM(CASE WHEN NIVEL_URGENCIA = 'CRITICA' THEN 1 ELSE 0 END) as criticas,
                     SUM(CASE WHEN NIVEL_URGENCIA = 'ALTA' THEN 1 ELSE 0 END) as altas,
                     SUM(CASE WHEN NIVEL_URGENCIA = 'MEDIA' THEN 1 ELSE 0 END) as medias,
                     SUM(CASE WHEN NIVEL_URGENCIA = 'BAJA' THEN 1 ELSE 0 END) as bajas
                 FROM tbl_alertas_sistema 
                 WHERE ESTADO = 'ACTIVA'
                 AND FECHA_EXPIRACION > NOW()
                 GROUP BY TIPO_ALERTA";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerEstadisticasAlertas: " . $e->getMessage());
        return [];
    }
}

// Método para marcar alerta como leída
public function marcarAlertaLeida($idAlerta) {
    try {
        $query = "UPDATE tbl_alertas_sistema 
                 SET LEIDA = 1 
                 WHERE ID_ALERTA = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$idAlerta]);
        
    } catch (PDOException $e) {
        error_log("Error en marcarAlertaLeida: " . $e->getMessage());
        return false;
    }
}

// Método para obtener alertas detalladas por tipo
public function obtenerAlertasDetalladasPorTipo($tipoAlerta) {
    try {
        $query = "SELECT 
                     a.*,
                     DATE_FORMAT(a.FECHA_CREACION, '%d/%m/%Y %H:%i') as fecha_formateada,
                     TIMESTAMPDIFF(HOUR, a.FECHA_CREACION, NOW()) as horas_transcurridas
                 FROM tbl_alertas_sistema a
                 WHERE a.TIPO_ALERTA = ?
                 AND a.ESTADO = 'ACTIVA'
                 AND a.FECHA_EXPIRACION > NOW()
                 ORDER BY a.FECHA_CREACION DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tipoAlerta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerAlertasDetalladasPorTipo: " . $e->getMessage());
        return [];
    }
}

// Método para obtener el total de alertas activas del sistema
public function obtenerTotalAlertasSistema() {
    try {
        $query = "SELECT COUNT(*) as total 
                 FROM tbl_alertas_sistema 
                 WHERE ESTADO = 'ACTIVA'
                 AND FECHA_EXPIRACION > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['total'] : 0;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerTotalAlertasSistema: " . $e->getMessage());
        return 0;
    }
}
    // Método para alertas de materia prima detalladas
    public function obtenerAlertasMateriaPrimaDetalladas() {
        try {
            // Alertas de stock bajo (por debajo del mínimo)
            $queryBajo = "SELECT 
                             mp.ID_MATERIA_PRIMA,
                             mp.NOMBRE,
                             mp.DESCRIPCION,
                             inv.CANTIDAD as stock_actual,
                             mp.MINIMO as stock_minimo,
                             mp.MAXIMO as stock_maximo,
                             (mp.MINIMO - inv.CANTIDAD) as faltante,
                             um.UNIDAD,
                             p.NOMBRE as PROVEEDOR,
                             CASE 
                                 WHEN inv.CANTIDAD <= mp.MINIMO * 0.5 THEN 'CRITICO'
                                 WHEN inv.CANTIDAD <= mp.MINIMO THEN 'BAJO'
                                 ELSE 'NORMAL'
                             END as nivel_alerta
                          FROM TBL_INVENTARIO_MATERIA_PRIMA inv
                          JOIN TBL_MATERIA_PRIMA mp ON inv.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                          JOIN TBL_UNIDAD_MEDIDA um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                          LEFT JOIN TBL_PROVEEDOR p ON mp.ID_PROVEEDOR = p.ID_PROVEEDOR
                          WHERE inv.CANTIDAD <= mp.MINIMO 
                          AND mp.ESTADO = 'ACTIVO'
                          ORDER BY nivel_alerta, faltante DESC";
            
            $stmt = $this->conn->prepare($queryBajo);
            $stmt->execute();
            $stockBajo = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Alertas de stock excesivo (por encima del máximo)
            $queryExcesivo = "SELECT 
                                 mp.ID_MATERIA_PRIMA,
                                 mp.NOMBRE,
                                 mp.DESCRIPCION,
                                 inv.CANTIDAD as stock_actual,
                                 mp.MAXIMO as stock_maximo,
                                 (inv.CANTIDAD - mp.MAXIMO) as exceso,
                                 um.UNIDAD,
                                 p.NOMBRE as PROVEEDOR,
                                 'EXCESIVO' as nivel_alerta
                              FROM TBL_INVENTARIO_MATERIA_PRIMA inv
                              JOIN TBL_MATERIA_PRIMA mp ON inv.ID_MATERIA_PRIMA = mp.ID_MATERIA_PRIMA
                              JOIN TBL_UNIDAD_MEDIDA um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                              LEFT JOIN TBL_PROVEEDOR p ON mp.ID_PROVEEDOR = p.ID_PROVEEDOR
                              WHERE inv.CANTIDAD >= mp.MAXIMO 
                              AND mp.ESTADO = 'ACTIVO'
                              ORDER BY exceso DESC";
            
            $stmt = $this->conn->prepare($queryExcesivo);
            $stmt->execute();
            $stockExcesivo = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'stock_bajo' => $stockBajo,
                'stock_excesivo' => $stockExcesivo,
                'total_alertas' => count($stockBajo) + count($stockExcesivo)
            ];
            
        } catch (PDOException $e) {
            error_log("Error en obtenerAlertasMateriaPrimaDetalladas: " . $e->getMessage());
            return [
                'stock_bajo' => [],
                'stock_excesivo' => [],
                'total_alertas' => 0
            ];
        }
    }

    // Método para debug de compras
public function obtenerComprasDebug() {
    try {
        $query = "SELECT 
                     ID_COMPRA,
                     FECHA_COMPRA,
                     TOTAL_COMPRA,
                     ESTADO_COMPRA,
                     PROVEEDOR.NOMBRE as PROVEEDOR_NOMBRE
                 FROM TBL_COMPRA 
                 LEFT JOIN TBL_PROVEEDOR ON TBL_COMPRA.ID_PROVEEDOR = TBL_PROVEEDOR.ID_PROVEEDOR
                 WHERE FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                 ORDER BY FECHA_COMPRA DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerComprasDebug: " . $e->getMessage());
        return [];
    }
}

    // Método para reporte financiero completo
    // En tu DashboardModel.php, modifica el método obtenerReporteFinancieroCompleto:

// Método para reporte financiero completo - ACTUALIZADO
public function obtenerReporteFinancieroCompleto() {
    try {
        $reporte = [];
        
        // VENTAS - HOY
        $queryHoyVentas = "SELECT COALESCE(SUM(TOTAL_VENTA), 0) as total 
                    FROM TBL_FACTURA 
                    WHERE ESTADO_FACTURA = 'ACTIVA'
                    AND DATE(FECHA_VENTA) = CURDATE()";
        $stmt = $this->conn->prepare($queryHoyVentas);
        $stmt->execute();
        $reporte['ventas_hoy'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // COMPRAS - HOY
        $queryHoyCompras = "SELECT COALESCE(SUM(TOTAL_COMPRA), 0) as total 
                    FROM TBL_COMPRA 
                    WHERE (ESTADO_COMPRA = 'ACTIVA' OR ESTADO_COMPRA IS NULL OR ESTADO_COMPRA = '')
                    AND DATE(FECHA_COMPRA) = CURDATE()
                    AND TOTAL_COMPRA > 0";
        $stmt = $this->conn->prepare($queryHoyCompras);
        $stmt->execute();
        $reporte['compras_hoy'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // VENTAS - ÚLTIMA SEMANA
        $querySemanaVentas = "SELECT COALESCE(SUM(TOTAL_VENTA), 0) as total 
                           FROM TBL_FACTURA 
                           WHERE ESTADO_FACTURA = 'ACTIVA'
                           AND FECHA_VENTA >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($querySemanaVentas);
        $stmt->execute();
        $reporte['ventas_semana'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // COMPRAS - ÚLTIMA SEMANA
        $querySemanaCompras = "SELECT COALESCE(SUM(TOTAL_COMPRA), 0) as total 
                           FROM TBL_COMPRA 
                           WHERE (ESTADO_COMPRA = 'ACTIVA' OR ESTADO_COMPRA IS NULL OR ESTADO_COMPRA = '')
                           AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                           AND TOTAL_COMPRA > 0";
        $stmt = $this->conn->prepare($querySemanaCompras);
        $stmt->execute();
        $reporte['compras_semana'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // VENTAS - ÚLTIMOS 15 DÍAS
        $queryQuincenaVentas = "SELECT COALESCE(SUM(TOTAL_VENTA), 0) as total 
                             FROM TBL_FACTURA 
                             WHERE ESTADO_FACTURA = 'ACTIVA'
                             AND FECHA_VENTA >= DATE_SUB(NOW(), INTERVAL 15 DAY)";
        $stmt = $this->conn->prepare($queryQuincenaVentas);
        $stmt->execute();
        $reporte['ventas_quincena'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // COMPRAS - ÚLTIMOS 15 DÍAS
        $queryQuincenaCompras = "SELECT COALESCE(SUM(TOTAL_COMPRA), 0) as total 
                             FROM TBL_COMPRA 
                             WHERE (ESTADO_COMPRA = 'ACTIVA' OR ESTADO_COMPRA IS NULL OR ESTADO_COMPRA = '')
                             AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 15 DAY)
                             AND TOTAL_COMPRA > 0";
        $stmt = $this->conn->prepare($queryQuincenaCompras);
        $stmt->execute();
        $reporte['compras_quincena'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // VENTAS - ÚLTIMO MES
        $queryMesVentas = "SELECT COALESCE(SUM(TOTAL_VENTA), 0) as total 
                        FROM TBL_FACTURA 
                        WHERE ESTADO_FACTURA = 'ACTIVA'
                        AND FECHA_VENTA >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $this->conn->prepare($queryMesVentas);
        $stmt->execute();
        $reporte['ventas_mes'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // COMPRAS - ÚLTIMO MES
        $queryMesCompras = "SELECT COALESCE(SUM(TOTAL_COMPRA), 0) as total 
                        FROM TBL_COMPRA 
                        WHERE (ESTADO_COMPRA = 'ACTIVA' OR ESTADO_COMPRA IS NULL OR ESTADO_COMPRA = '')
                        AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                        AND TOTAL_COMPRA > 0";
        $stmt = $this->conn->prepare($queryMesCompras);
        $stmt->execute();
        $reporte['compras_mes'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // CÁLCULOS DE UTILIDAD
        $reporte['utilidad_hoy'] = $reporte['ventas_hoy'] - $reporte['compras_hoy'];
        $reporte['utilidad_semana'] = $reporte['ventas_semana'] - $reporte['compras_semana'];
        $reporte['utilidad_quincena'] = $reporte['ventas_quincena'] - $reporte['compras_quincena'];
        $reporte['utilidad_mes'] = $reporte['ventas_mes'] - $reporte['compras_mes'];
        
        return $reporte;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerReporteFinancieroCompleto: " . $e->getMessage());
        return [
            'ventas_hoy' => 0,
            'compras_hoy' => 0,
            'ventas_semana' => 0,
            'compras_semana' => 0,
            'ventas_quincena' => 0,
            'compras_quincena' => 0,
            'ventas_mes' => 0,
            'compras_mes' => 0,
            'utilidad_hoy' => 0,
            'utilidad_semana' => 0,
            'utilidad_quincena' => 0,
            'utilidad_mes' => 0
        ];
    }
}

// Método para reporte financiero sencillo de compras
// Método para reporte financiero sencillo de compras - CON PROCEDIMIENTO ALMACENADO
// En DashboardModel.php - agregar este método
// En DashboardModel.php - método corregido
public function obtenerReporteComprasPorPeriodo($periodo = 'hoy') {
    try {
        $query = "";
        
        switch($periodo) {
            case 'hoy':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             COALESCE(SUM(TOTAL_COMPRA), 0) as monto_total
                          FROM TBL_COMPRA 
                          WHERE ESTADO_COMPRA = 'COMPLETADA'
                          AND DATE(FECHA_COMPRA) = CURDATE()
                          AND TOTAL_COMPRA > 0";
                break;
                
            case 'semana':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             COALESCE(SUM(TOTAL_COMPRA), 0) as monto_total
                          FROM TBL_COMPRA 
                          WHERE ESTADO_COMPRA = 'COMPLETADA'
                          AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                          AND TOTAL_COMPRA > 0";
                break;
                
            case 'quincena':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             COALESCE(SUM(TOTAL_COMPRA), 0) as monto_total
                          FROM TBL_COMPRA 
                          WHERE ESTADO_COMPRA = 'COMPLETADA'
                          AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 15 DAY)
                          AND TOTAL_COMPRA > 0";
                break;
                
            case 'mes':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             COALESCE(SUM(TOTAL_COMPRA), 0) as monto_total
                          FROM TBL_COMPRA 
                          WHERE ESTADO_COMPRA = 'COMPLETADA'
                          AND FECHA_COMPRA >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
                          AND TOTAL_COMPRA > 0";
                break;
                
            default:
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             COALESCE(SUM(TOTAL_COMPRA), 0) as monto_total
                          FROM TBL_COMPRA 
                          WHERE ESTADO_COMPRA = 'COMPLETADA'
                          AND DATE(FECHA_COMPRA) = CURDATE()
                          AND TOTAL_COMPRA > 0";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'cantidad' => (int)$result['cantidad'],
            'monto_total' => (float)$result['monto_total']
        ];
        
    } catch (PDOException $e) {
        error_log("Error en obtenerReporteComprasPorPeriodo: " . $e->getMessage());
        return [
            'cantidad' => 0,
            'monto_total' => 0
        ];
    }
}

public function obtenerReporteVentasPorPeriodo($periodo = 'hoy') {
    try {
        $query = "";
        
        switch($periodo) {
            case 'hoy':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE DATE(f.FECHA_VENTA) = CURDATE()
                          AND f.TOTAL_VENTA IS NOT NULL";
                break;
                
            case 'semana':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE f.FECHA_VENTA >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                          AND f.TOTAL_VENTA IS NOT NULL";
                break;
                
            case 'quincena':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE f.FECHA_VENTA >= DATE_SUB(CURDATE(), INTERVAL 15 DAY)
                          AND f.TOTAL_VENTA IS NOT NULL";
                break;
                
            case 'mes':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE f.FECHA_VENTA >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          AND f.TOTAL_VENTA IS NOT NULL";
                break;
                
            case 'totales':
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE f.TOTAL_VENTA IS NOT NULL";
                break;
                
            default:
                $query = "SELECT 
                             COUNT(*) as cantidad,
                             IFNULL(SUM(IFNULL(f.TOTAL_VENTA, 0)), 0) as monto_total
                          FROM tbl_factura f
                          WHERE DATE(f.FECHA_VENTA) = CURDATE()
                          AND f.TOTAL_VENTA IS NOT NULL";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'cantidad' => (int)($result['cantidad'] ?? 0),
            'monto_total' => (float)($result['monto_total'] ?? 0)
        ];
        
    } catch (PDOException $e) {
        error_log("Error en obtenerReporteVentasPorPeriodo: " . $e->getMessage());
        return [
            'cantidad' => 0,
            'monto_total' => 0
        ];
    }
}

    // Los otros métodos existentes los mantienes igual...
    public function obtenerDatosFinancieros() {
        try {
            $datos = [];

            // Total de usuarios (ya lo tienes, pero lo incluyo para completitud)
            $query = "SELECT COUNT(*) as total FROM TBL_MS_USUARIOS WHERE ESTADO_USUARIO != 'ELIMINADO'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $datos['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // En el método obtenerDatosFinancieros(), cambia las consultas:

// Total de compras (suma de todas las compras activas) - ESTA FUNCIONA BIEN
$query = "SELECT COALESCE(SUM(TOTAL_COMPRA), 0) as total 
         FROM TBL_COMPRA 
         WHERE ESTADO_COMPRA = 'ACTIVA'";
$stmt = $this->conn->prepare($query);
$stmt->execute();
$datos['total_compras_monto'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de ventas (suma de todas las ventas - sin cliente y con cliente) - INCLUIR TODAS
$query = "SELECT COALESCE(SUM(TOTAL_VENTA), 0) as total 
         FROM TBL_FACTURA";
$stmt = $this->conn->prepare($query);
$stmt->execute();
$datos['total_ventas_monto'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total de producción finalizada (suma de cantidades producidas) - CORREGIDA
$query = "SELECT COALESCE(SUM(DP.CANTIDAD), 0) as total 
         FROM TBL_DETALLE_PRODUCCION DP
         JOIN TBL_PRODUCCION P ON DP.ID_PRODUCCION = P.ID_PRODUCCION
         WHERE P.ID_ESTADO_PRODUCCION IN (2, 6)"; // 2 y 6 = COMPLETADA
$stmt = $this->conn->prepare($query);
$stmt->execute();
$datos['total_produccion_cantidad'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total de registros en bitácora
            $query = "SELECT COUNT(*) as total FROM TBL_MS_BITACORA";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $datos['total_bitacora'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Cálculo de utilidad (Ventas - Compras)
            $datos['utilidad'] = $datos['total_ventas_monto'] - $datos['total_compras_monto'];
            
            // Porcentaje de utilidad
            if ($datos['total_ventas_monto'] > 0) {
                $datos['porcentaje_utilidad'] = ($datos['utilidad'] / $datos['total_ventas_monto']) * 100;
            } else {
                $datos['porcentaje_utilidad'] = 0;
            }

            return $datos;

        } catch (PDOException $e) {
            error_log("Error en obtenerDatosFinancieros: " . $e->getMessage());
            return [
                'total_usuarios' => 0,
                'total_compras_monto' => 0,
                'total_ventas_monto' => 0,
                'total_produccion_cantidad' => 0,
                'total_bitacora' => 0,
                'utilidad' => 0,
                'porcentaje_utilidad' => 0
            ];
        }
    }

    public function obtenerTendenciaMensual() {
        try {
            $query = "SELECT 
                        MONTH(FECHA_VENTA) as mes,
                        COALESCE(SUM(TOTAL_VENTA), 0) as ventas,
                        (SELECT COALESCE(SUM(TOTAL_COMPRA), 0) 
                         FROM TBL_COMPRA 
                         WHERE MONTH(FECHA_COMPRA) = MONTH(FECHA_VENTA)
                         AND YEAR(FECHA_COMPRA) = YEAR(FECHA_VENTA)
                         AND ESTADO_COMPRA = 'ACTIVA') as compras
                     FROM TBL_FACTURA 
                     WHERE YEAR(FECHA_VENTA) = YEAR(CURDATE())
                     GROUP BY MONTH(FECHA_VENTA)
                     ORDER BY mes";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en obtenerTendenciaMensual: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerVentasPorPeriodo($tipoPeriodo = 'dia') {
    try {
        if ($tipoPeriodo === 'dia') {
            $query = "SELECT 
                         DATE(FECHA_VENTA) as fecha,
                         SUM(TOTAL_VENTA) as total_ventas
                      FROM TBL_FACTURA 
                      WHERE DATE(FECHA_VENTA) = CURDATE()
                      GROUP BY DATE(FECHA_VENTA)";
        } else {
            $query = "SELECT 
                         DATE_FORMAT(FECHA_VENTA, '%Y-%m') as mes,
                         SUM(TOTAL_VENTA) as total_ventas
                      FROM TBL_FACTURA 
                      WHERE YEAR(FECHA_VENTA) = YEAR(CURDATE())
                      AND MONTH(FECHA_VENTA) = MONTH(CURDATE())
                      GROUP BY DATE_FORMAT(FECHA_VENTA, '%Y-%m')";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['total_ventas'] : 0;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerVentasPorPeriodo: " . $e->getMessage());
        return 0;
    }
}

    public function obtenerComprasPorPeriodo($tipoPeriodo = 'dia') {
    try {
        if ($tipoPeriodo === 'dia') {
            $query = "SELECT 
                         DATE(FECHA_COMPRA) as fecha,
                         SUM(TOTAL_COMPRA) as total_compras
                      FROM TBL_COMPRA 
                      WHERE ESTADO_COMPRA = 'ACTIVA'
                      AND DATE(FECHA_COMPRA) = CURDATE()
                      GROUP BY DATE(FECHA_COMPRA)";
        } else {
            $query = "SELECT 
                         DATE_FORMAT(FECHA_COMPRA, '%Y-%m') as mes,
                         SUM(TOTAL_COMPRA) as total_compras
                      FROM TBL_COMPRA 
                      WHERE ESTADO_COMPRA = 'ACTIVA'
                      AND YEAR(FECHA_COMPRA) = YEAR(CURDATE())
                      AND MONTH(FECHA_COMPRA) = MONTH(CURDATE())
                      GROUP BY DATE_FORMAT(FECHA_COMPRA, '%Y-%m')";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['total_compras'] : 0;
        
    } catch (PDOException $e) {
        error_log("Error en obtenerComprasPorPeriodo: " . $e->getMessage());
        return 0;
    }
}


    public function obtenerAnalisisFinanciero($tipoPeriodo = 'dia') {
    try {
        $ventas = $this->obtenerVentasPorPeriodo($tipoPeriodo);
        $compras = $this->obtenerComprasPorPeriodo($tipoPeriodo);
        
        $utilidad = $ventas - $compras;
        $porcentaje_utilidad = ($ventas > 0) ? ($utilidad / $ventas) * 100 : 0;
        
        return [
            'ventas' => $ventas,
            'compras' => $compras,
            'utilidad' => $utilidad,
            'porcentaje_utilidad' => $porcentaje_utilidad,
            'tipo_periodo' => $tipoPeriodo,
            'estado' => $utilidad >= 0 ? 'GANANCIA' : 'PERDIDA'
        ];
        
    } catch (PDOException $e) {
        error_log("Error en obtenerAnalisisFinanciero: " . $e->getMessage());
        return [
            'ventas' => 0,
            'compras' => 0,
            'utilidad' => 0,
            'porcentaje_utilidad' => 0,
            'tipo_periodo' => $tipoPeriodo,
            'estado' => 'SIN_DATOS'
        ];
    }
}

    public function obtenerHistorialFinanciero($dias = 30) {
    try {
        // Ventas de los últimos días
        $queryVentas = "SELECT 
                           DATE(FECHA_VENTA) as fecha,
                           SUM(TOTAL_VENTA) as monto
                        FROM TBL_FACTURA 
                        WHERE ESTADO_FACTURA = 'ACTIVA'
                        AND FECHA_VENTA >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                        GROUP BY DATE(FECHA_VENTA)
                        ORDER BY fecha";
        
        $stmt = $this->conn->prepare($queryVentas);
        $stmt->execute([$dias]);
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Compras de los últimos días
        $queryCompras = "SELECT 
                            DATE(FECHA_COMPRA) as fecha,
                            SUM(TOTAL_COMPRA) as monto
                         FROM TBL_COMPRA 
                         WHERE ESTADO_COMPRA = 'ACTIVA'
                         AND FECHA_COMPRA >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                         GROUP BY DATE(FECHA_COMPRA)
                         ORDER BY fecha";
        
        $stmt = $this->conn->prepare($queryCompras);
        $stmt->execute([$dias]);
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'ventas' => $ventas,
            'compras' => $compras
        ];
        
    } catch (PDOException $e) {
        error_log("Error en obtenerHistorialFinanciero: " . $e->getMessage());
        return [
            'ventas' => [],
            'compras' => []
        ];
    }
}
}
?>