<?php
use App\controllers\permisosController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$permisos = new permisosController($method, $data);

// RUTAS API (siempre retornan JSON)
$apiCases = [
    'verificarPermiso',
    'obtenerMenuUsuario',
    'obtenerObjetos',
    'obtenerPermisosRol',
    'gestionarPermiso',
    'obtenerRoles',
    'obtenerParametrosSeguridad',
    'actualizarParametroSeguridad',
    'obtenerParametrosSistema',
    'obtenerParametrosGenerales',
    'debug'
];

if (in_array($caso, $apiCases)) {
    // Para API, establecer headers JSON
    header('Content-Type: application/json');
    
    switch ($caso) {
        case 'verificarPermiso':
            $permisos->verificarPermiso();
            break;
        case 'obtenerMenuUsuario':
            $permisos->obtenerMenuUsuario();
            break;
        case 'obtenerObjetos':
            $permisos->obtenerObjetos();
            break;
        case 'obtenerPermisosRol':
            $permisos->obtenerPermisosRol();
            break;
        case 'gestionarPermiso':
            $permisos->gestionarPermiso();
            break;
        case 'obtenerRoles':
            $permisos->obtenerRoles();
            break;
        case 'obtenerParametrosSeguridad':
            $permisos->obtenerParametrosSeguridad();
            break;
        case 'actualizarParametroSeguridad':
            $permisos->actualizarParametroSeguridad();
            break;
        case 'obtenerParametrosSistema':
            $permisos->obtenerParametrosSistema();
            break;
        case 'obtenerParametrosGenerales':
            $permisos->obtenerParametrosGenerales();
            break;
        case 'debug':
            $permisos->debug();
            break;
        default:
            echo json_encode(responseHTTP::status404('Endpoint de API no encontrado'));
            break;
    }
    exit; // Importante: terminar ejecución después de API
}

// Si llegamos aquí, es porque no es una API call
// Las vistas ya fueron manejadas por el index.php, así que solo mostramos error
header('Content-Type: application/json');
echo json_encode(responseHTTP::status404('Ruta no encontrada'));
?>