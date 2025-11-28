<?php
use App\controllers\DashboardController;
use App\config\responseHTTP;

// Crear instancia del controlador de dashboard
$dashboardController = new DashboardController();

// Manejo de solicitudes AJAX para el dashboard
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'estadisticas':
            $dashboardController->obtenerEstadisticas();
            break;
        // En tu archivo de rutas (dashboard.php)
case 'detalle-usuarios-modal':
    error_log("Ejecutando acción: detalle-usuarios-modal"); // Debug
    $dashboardController->obtenerDetalleUsuariosModal();
    break;
        case 'sesiones-activas-modal':
            $dashboardController->obtenerSesionesActivasModal();
            break;
        case 'alertas-materia-prima-modal':
            $dashboardController->obtenerAlertasMateriaPrimaModal();
            break;
        case 'reporte-financiero-completo':
            $dashboardController->obtenerReporteFinancieroCompleto();
            break;
            
        default:
            responseHTTP::error("Acción no válida");
            break;

            // Agrega estas rutas a tu archivo de rutas (dashboard.php)
case 'alertas-sistema':
    $dashboardController->obtenerAlertasSistema();
    break;
case 'estadisticas-alertas':
    $dashboardController->obtenerEstadisticasAlertas();
    break;
case 'marcar-alerta-leida':
    $dashboardController->marcarAlertaLeida();
    break;
case 'alertas-por-tipo':
    $dashboardController->obtenerAlertasPorTipo();
    break;
    case 'alertas-tiempo-real':
    $dashboardController->obtenerAlertasTiempoReal();
    break;
    }
    exit;
}

// Si no hay acción específica, mostrar la vista del dashboard
require_once 'src/Views/inicio.php';
?>