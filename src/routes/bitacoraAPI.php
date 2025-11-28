<?php

use App\controllers\bitacoraController;
use App\models\bitacoraModel; // ✅ Añadir esta importación
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$data = json_decode(file_get_contents("php://input"), true) ?? [];
$caso = $_GET['caso'] ?? '';

$bitacora = new bitacoraController($method, $data);

switch ($caso) {
    case 'obtener':
        if ($method == 'get') {
            $bitacora->obtenerBitacora();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'estadisticas':
        if ($method == 'get') {
            $bitacora->obtenerEstadisticas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'registrar-navegacion':
        if ($method == 'post') {
            session_start();
            
            $idUsuario = $_SESSION['user_id'] ?? 0;
            
            if ($idUsuario == 0) {
                echo json_encode(responseHTTP::status401('No autenticado'));
                break;
            }
            
            $pagina = $data['pagina'] ?? '';
            $accion = $data['accion'] ?? 'NAVEGACION';
            $descripcion = $data['descripcion'] ?? '';
            $idObjeto = $data['id_objeto'] ?? null;
            
            if (!empty($pagina)) {
                // ✅ CORRECTO: Usar bitacoraModel para registrar acciones
                if (!empty($descripcion) && $idObjeto) {
                    bitacoraModel::registrarAccion($idUsuario, $idObjeto, $accion, $descripcion);
                } else {
                    bitacoraController::registrarNavegacion($idUsuario, $pagina, $accion);
                }
                echo json_encode(responseHTTP::status200('Navegación registrada'));
            } else {
                echo json_encode(responseHTTP::status400('Página no especificada'));
            }
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    default:
        echo json_encode(responseHTTP::status404('Endpoint de bitácora no encontrado'));
        break;
}