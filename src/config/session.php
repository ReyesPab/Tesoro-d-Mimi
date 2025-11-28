<?php
// COMENTA TEMPORALMENTE TODO ESTO:

// // Ruta correcta para SessionHelper
// require_once __DIR__ . '/SessionHelper.php';

// use App\config\SessionHelper;

// // Inicia la sesión usando SessionHelper
// SessionHelper::startSession();

// // Evita problemas con headers y redirecciones
// ob_start();

// // Reforzar seguridad de sesión
// if (!isset($_SESSION['iniciada'])) {
//     session_regenerate_id(true);
//     $_SESSION['iniciada'] = true;
// }

// // Validar si el usuario está autenticado usando SessionHelper
// if (!SessionHelper::isLoggedIn()) {
//     // Destruir la sesión incompleta
//     SessionHelper::destroySession();
//     header('Location: /sistema/public/index.php?route=login');
//     exit;
// }

// ob_end_flush();

// EN VEZ DE ESO, SOLO INICIA SESIÓN SIMPLEMENTE:
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>