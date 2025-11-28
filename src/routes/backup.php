<?php
// C:\xampp\htdocs\sistema\src\routes\backup.php

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers primero para evitar problemas
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=UTF-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar que los archivos existan
$controllerPath = __DIR__ . '/../controllers/backupController.php';
$responsePath = __DIR__ . '/../config/responseHTTP.php';

// Función para manejar errores
function sendError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'status' => (string)$code,
        'message' => $message,
        'data' => []
    ], JSON_PRETTY_PRINT);
    exit;
}

if (!file_exists($controllerPath)) {
    sendError('Controlador no encontrado: ' . $controllerPath, 500);
}

if (!file_exists($responsePath)) {
    sendError('Configuración de respuesta no encontrada: ' . $responsePath, 500);
}

// Incluir archivos
try {
    require_once $responsePath;
    require_once $controllerPath;
} catch (Throwable $e) {
    sendError('Error al incluir archivos: ' . $e->getMessage(), 500);
}

try {
    $caso = $_GET['caso'] ?? '';
    
    if (empty($caso)) {
        sendError('Caso no especificado', 400);
    }

    error_log("Backup route - Caso: " . $caso);

    switch ($caso) {
        case 'crear-backup-manual':
            $result = \App\controllers\backupController::crearBackupManual();
            echo $result;
            break;
            
        case 'obtener-ultimos-backups':
            $result = \App\controllers\backupController::obtenerUltimosBackups();
            echo $result;
            break;
            
        case 'programar-backup-automatico':
            $result = \App\controllers\backupController::programarBackupAutomatico();
            echo $result;
            break;

        case 'obtener-backups':
            $result = \App\controllers\backupController::obtenerBackups();
            echo $result;
            break;

        case 'eliminar-backups-antiguos':
            $result = \App\controllers\backupController::eliminarBackupsAntiguos();
            echo $result;
            break;

        case 'estadisticas':
            $result = \App\controllers\backupController::obtenerEstadisticas();
            echo $result;
            break;

        case 'restaurar-backup':
            $result = \App\controllers\backupController::restaurarBackup();
            echo $result;
            break;

        case 'subir-backup':
            $result = \App\controllers\backupController::subirBackupArchivo();
            echo $result;
            break;

        case 'descargar-backup':
            $result = \App\controllers\backupController::descargarBackup();
            if ($result && is_string($result)) {
                echo $result;
            }
            break;

        case 'verificar-bloqueo-completo':
            $result = \App\controllers\backupController::verificarBloqueoCompleto();
            echo json_encode($result);
            break;

        case 'verificar-estado-sistema':
            $result = \App\controllers\backupController::verificarBloqueo();
            echo json_encode($result);
            break;

 case 'obtener-proximo-backup':
            $result = \App\controllers\backupController::obtenerProximoBackup();
            echo $result;
            break;

        default:
            sendError('Caso no válido para backups: ' . $caso, 400);
            break;
    } 
} catch (Throwable $e) {
    error_log("Error crítico en backup route: " . $e->getMessage());
    sendError('Error interno del servidor: ' . $e->getMessage(), 500);
}
?>