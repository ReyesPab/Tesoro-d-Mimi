<?php
// src/routes/role.php

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cargar las clases necesarias
require_once __DIR__ . '/../config/responseHTTP.php';
require_once __DIR__ . '/../config/Security.php';
require_once __DIR__ . '/../controllers/roleController.php';

use App\controllers\roleController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$caso = $_GET['caso'] ?? '';

error_log("🎯 INICIANDO role.php - Method: $method, Caso: $caso");

try {
    $role = new roleController($method, $data);

    // Rutas para gestión de roles
    switch ($caso) {
        case 'listar':
            $role->listarRoles();
            break;
            
        case 'obtener':
            $role->obtenerRol();
            break;
            
        case 'crear':
            $role->crearRol();
            break;
            
        case 'actualizar':
            $role->actualizarRol();
            break;
            
        case 'eliminar':
            $role->eliminarRol();
            break;
            
        case 'verificar-rol':
            $role->verificarRol();
            break;
            
        default:
            error_log("❌ Endpoint de roles no encontrado: " . $caso);
            echo json_encode(responseHTTP::status404('Endpoint de roles no encontrado: ' . $caso));
            break;
    }
} catch (Exception $e) {
    error_log("💥 ERROR CRÍTICO en role.php: " . $e->getMessage());
    echo json_encode([
        'status' => 500,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>