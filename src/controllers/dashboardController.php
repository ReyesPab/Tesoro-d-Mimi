<?php
namespace App\controllers;

use App\config\responseHTTP;
use App\models\DashboardModel;

class DashboardController {
    private $model;
    private $response;

    public function __construct() {
        $this->model = new DashboardModel();
        $this->response = new ResponseHTTP();
    }

    public function obtenerEstadisticas() {
        try {
            $estadisticas = $this->model->obtenerEstadisticasGenerales();
            
            if ($estadisticas) {
                $this->response->success($estadisticas, "Estadísticas obtenidas correctamente");
            } else {
                $this->response->error("No se pudieron obtener las estadísticas");
            }
        } catch (\Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            $this->response->error("Error interno del servidor");
        }
    }

    public function obtenerDetalleUsuarios() {
        try {
            $detalle = $this->model->obtenerDetalleUsuarios();
            
            if ($detalle !== false) {
                $this->response->success($detalle, "Detalle de usuarios obtenido correctamente");
            } else {
                $this->response->error("No se pudo obtener el detalle de usuarios");
            }
        } catch (\Exception $e) {
            error_log("Error en obtenerDetalleUsuarios: " . $e->getMessage());
            $this->response->error("Error interno del servidor");
        }
    }

    public function obtenerDetalleUsuariosModal() {
        try {
            $detalle = $this->model->obtenerDetalleUsuarios();
            $this->response->success($detalle, "Detalle de usuarios obtenido correctamente");
        } catch (\Exception $e) {
            error_log("Error en obtenerDetalleUsuariosModal: " . $e->getMessage());
            $this->response->error("Error al obtener el detalle de usuarios");
        }
    }

    public function obtenerSesionesActivasModal() {
        try {
            $sesiones = $this->model->obtenerSesionesActivas24h();
            $this->response->success($sesiones, "Sesiones activas obtenidas correctamente");
        } catch (\Exception $e) {
            error_log("Error en obtenerSesionesActivasModal: " . $e->getMessage());
            $this->response->error("Error al obtener las sesiones activas");
        }
    }

    // Agrega estos métodos a tu DashboardController.php
public function obtenerAlertasDetalladasModal() {
    try {
        $alertas = $this->model->obtenerAlertasSistema();
        $this->response->success($alertas, "Alertas detalladas obtenidas correctamente");
    } catch (\Exception $e) {
        error_log("Error en obtenerAlertasDetalladasModal: " . $e->getMessage());
        $this->response->error("Error al obtener las alertas detalladas");
    }
}
public function obtenerAlertasSistema() {
    try {
        $alertas = $this->model->obtenerAlertasSistema();
        $this->response->success($alertas, "Alertas del sistema obtenidas correctamente");
    } catch (\Exception $e) {
        error_log("Error en obtenerAlertasSistema: " . $e->getMessage());
        $this->response->error("Error al obtener las alertas del sistema");
    }
}

public function obtenerEstadisticasAlertas() {
    try {
        $estadisticas = $this->model->obtenerEstadisticasAlertas();
        $this->response->success($estadisticas, "Estadísticas de alertas obtenidas correctamente");
    } catch (\Exception $e) {
        error_log("Error en obtenerEstadisticasAlertas: " . $e->getMessage());
        $this->response->error("Error al obtener las estadísticas de alertas");
    }
}

public function marcarAlertaLeida() {
    try {
        $idAlerta = $_POST['id_alerta'] ?? null;
        
        if (!$idAlerta) {
            $this->response->error("ID de alerta no proporcionado");
            return;
        }
        
        $resultado = $this->model->marcarAlertaLeida($idAlerta);
        
        if ($resultado) {
            $this->response->success([], "Alerta marcada como leída");
        } else {
            $this->response->error("Error al marcar la alerta como leída");
        }
    } catch (\Exception $e) {
        error_log("Error en marcarAlertaLeida: " . $e->getMessage());
        $this->response->error("Error al marcar la alerta como leída");
    }
}

public function obtenerAlertasPorTipo() {
    try {
        $tipoAlerta = $_GET['tipo'] ?? null;
        
        if (!$tipoAlerta) {
            $this->response->error("Tipo de alerta no proporcionado");
            return;
        }
        
        $alertas = $this->model->obtenerAlertasDetalladasPorTipo($tipoAlerta);
        $this->response->success($alertas, "Alertas obtenidas correctamente");
    } catch (\Exception $e) {
        error_log("Error en obtenerAlertasPorTipo: " . $e->getMessage());
        $this->response->error("Error al obtener las alertas");
    }
}

    public function obtenerAlertasMateriaPrimaModal() {
        try {
            $alertas = $this->model->obtenerAlertasMateriaPrimaDetalladas();
            $this->response->success($alertas, "Alertas de materia prima obtenidas correctamente");
        } catch (\Exception $e) {
            error_log("Error en obtenerAlertasMateriaPrimaModal: " . $e->getMessage());
            $this->response->error("Error al obtener las alertas de materia prima");
        }
    }

    public function obtenerReporteFinanciero() {
        try {
            $tipoPeriodo = $_GET['periodo'] ?? 'dia';
            
            $analisis = $this->model->obtenerAnalisisFinanciero($tipoPeriodo);
            $historial = $this->model->obtenerHistorialFinanciero(7);
            
            $response = [
                'analisis' => $analisis,
                'historial' => $historial
            ];
            
            $this->response->success($response, "Reporte financiero obtenido correctamente");
            
        } catch (\Exception $e) {
            error_log("Error en obtenerReporteFinanciero: " . $e->getMessage());
            $this->response->error("Error al obtener el reporte financiero");
        }
    }

    public function obtenerReporteFinancieroCompleto() {
        try {
            $reporte = $this->model->obtenerReporteFinancieroCompleto();
            $this->response->success($reporte, "Reporte financiero obtenido correctamente");
        } catch (\Exception $e) {
            error_log("Error en obtenerReporteFinancieroCompleto: " . $e->getMessage());
            $this->response->error("Error al obtener el reporte financiero");
        }
    }

    public function obtenerComprasDebug() {
    try {
        $compras = $this->model->obtenerComprasDebug();
        $this->response->success($compras, "Debug de compras obtenido");
    } catch (\Exception $e) {
        error_log("Error en obtenerComprasDebug: " . $e->getMessage());
        $this->response->error("Error en debug de compras");
    }
}

public function obtenerAlertasTiempoReal() {
    try {
        // Ejecutar el procedimiento para generar alertas actualizadas
        $stmt = $this->conn->prepare("CALL sp_generar_alertas_sistema()");
        $stmt->execute();
        
        // Obtener alertas activas recientes (últimas 2 horas)
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
                 AND a.FECHA_CREACION >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
                 ORDER BY prioridad DESC, a.FECHA_CREACION DESC
                 LIMIT 50";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->response->success($alertas, "Alertas en tiempo real obtenidas correctamente");
        
    } catch (\Exception $e) {
        error_log("Error en obtenerAlertasTiempoReal: " . $e->getMessage());
        $this->response->error("Error al obtener alertas en tiempo real");
    }
}

// En DashboardController.php - agregar este método
public function obtenerReporteComprasPeriodo() {
    try {
        $periodo = $_GET['periodo'] ?? 'hoy';
        
        // Validar que el período sea válido
        $periodosPermitidos = ['hoy', 'semana', 'quincena', 'mes'];
        if (!in_array($periodo, $periodosPermitidos)) {
            $periodo = 'hoy';
        }
        
        $reporte = $this->model->obtenerReporteComprasPorPeriodo($periodo);
        
        $this->response->success($reporte, "Reporte de compras obtenido correctamente");
        
    } catch (\Exception $e) {
        error_log("Error en obtenerReporteComprasPeriodo: " . $e->getMessage());
        $this->response->error("Error al obtener el reporte de compras");
    }
}

// En DashboardController.php - agregar este método
public function obtenerReporteVentasPeriodo() {
    try {
        $periodo = $_GET['periodo'] ?? 'hoy';
        
        // Validar que el período sea válido
        $periodosPermitidos = ['hoy', 'semana', 'quincena', 'mes'];
        if (!in_array($periodo, $periodosPermitidos)) {
            $periodo = 'hoy';
        }
        
        $reporte = $this->model->obtenerReporteVentasPorPeriodo($periodo);
        
        $this->response->success($reporte, "Reporte de ventas obtenido correctamente");
        
    } catch (\Exception $e) {
        error_log("Error en obtenerReporteVentasPeriodo: " . $e->getMessage());
        $this->response->error("Error al obtener el reporte de ventas");
    }
}

    public function obtenerDetalleVentasModal() {
        try {
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $ventas = $this->model->obtenerVentasRecientes($limit);
            $this->response->success($ventas, "Ventas recientes obtenidas correctamente");
        } catch (\Exception $e) {
            error_log("Error en obtenerDetalleVentasModal: " . $e->getMessage());
            $this->response->error("Error al obtener las ventas recientes");
        }
    }
}

// Manejo de solicitudes AJAX - FUERA DE LA CLASE
// En tu DashboardController, actualiza el switch completo:
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $controller = new DashboardController();
    
    switch ($_GET['action']) {
        case 'estadisticas':
            $controller->obtenerEstadisticas();
            break;
        case 'detalle-usuarios':
            $controller->obtenerDetalleUsuarios();
            break;
        case 'detalle-usuarios-modal':
            $controller->obtenerDetalleUsuariosModal();
            break;
        case 'sesiones-activas-modal':
            $controller->obtenerSesionesActivasModal();
            break;
        case 'alertas-detalladas-modal':
            $controller->obtenerAlertasDetalladasModal();
            break;
        case 'alertas-materia-prima-modal':
            $controller->obtenerAlertasMateriaPrimaModal();
            break;
        case 'reporte-financiero':
            $controller->obtenerReporteFinanciero();
            break;
        case 'reporte-financiero-completo':
            $controller->obtenerReporteFinancieroCompleto();
            break;
        case 'alertas-sistema':
            $controller->obtenerAlertasSistema();
            break;
        case 'estadisticas-alertas':
            $controller->obtenerEstadisticasAlertas();
            break;
        case 'alertas-por-tipo':
            $controller->obtenerAlertasPorTipo();
            break;
            // En el switch GET de DashboardController
case 'reporte-compras-periodo':
    $controller->obtenerReporteComprasPeriodo();
    break;
        case 'detalle-ventas-modal':
            $controller->obtenerDetalleVentasModal();
            break;
            case 'debug-compras':
    $controller->obtenerComprasDebug();
    break;
    
    case 'reporte-ventas-periodo':
    $controller->obtenerReporteVentasPeriodo();
    break;
    }
    exit;
}

// Para POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new DashboardController();
    
    switch ($_POST['action']) {
        case 'marcar-alerta-leida':
            $controller->marcarAlertaLeida();
            break;
        default:
            if (method_exists('App\config\responseHTTP', 'error')) {
                App\config\responseHTTP::error("Acción no válida");
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Acción no válida'
                ]);
                exit;
            }
            break;
    }
    exit;
}
?>