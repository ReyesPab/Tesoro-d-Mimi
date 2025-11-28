<?php

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

use App\controllers\userController;
use App\config\responseHTTP;

 

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
// Merge GET params into data so controllers can access query string values (p.ej. id_usuario)
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$user = new userController($method, $data);

// Rutas para gestión de usuarios
switch ($caso) {
    case 'crear':
        if ($method == 'post') {
            $user->crearUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'listar':
        if ($method == 'get') {
            $user->listarUsuarios();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener':
        if ($method == 'get') {
            $user->obtenerUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'actualizar':
    case 'actualizar-usuario': // Agregué este alias
        if ($method == 'put' || $method == 'post') {
            $user->actualizarUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'resetear-password':
        if ($method == 'post') {
            $user->resetearPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-roles':
        if ($method == 'get') {
            $user->obtenerRoles();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'generar-password':
        if ($method == 'get') {
            $user->generarPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-parametros':
        if ($method == 'get') {
            $user->obtenerParametros();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'verificar-usuario':
        if ($method == 'post') {
            $user->verificarUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'verificar-identidad':
        if ($method == 'post') {
            $user->verificarIdentidad();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'verificar-correo': // NUEVO: Agregué este caso
        if ($method == 'post') {
            $user->verificarCorreo();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'cambiar-estado':
        if ($method == 'post') {
            $user->cambiarEstado();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-usuario-completo':
        if ($method == 'get') {
            $user->obtenerUsuarioCompleto();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'obtener-estado-2fa':
        if ($method == 'post') {
            $user->obtenerEstado2FA();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'toggle-2fa':
        if ($method == 'post') {
            $user->toggle2FA();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'cambiar-password':
        if ($method == 'post') {
            $user->cambiarPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

        case 'subir-foto-perfil':
        if ($method == 'post') {
            // Para subida de archivos usamos $_FILES
            $user->subirFotoPerfil();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        case 'perfil':
    require_once 'src/Views/perfil.php';
    break;
    
    case 'obtener-perfil':
    if ($method == 'get') {
        $user->obtenerPerfil();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
    
    case 'obtener-foto-perfil':
        if ($method == 'get') {
            $user->obtenerFotoPerfil();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'eliminar-foto-perfil':
        if ($method == 'post') {
            $user->eliminarFotoPerfil();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'actualizar-foto-perfil':
        if ($method == 'post') {
            $user->actualizarFotoPerfil();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

        // En user.php, agrega este nuevo caso:
case 'obtener-usuario-edicion':
    if ($method == 'get') {
        $user->obtenerUsuarioEdicion();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;

    case 'resetear-contrasena-admin':
    if ($method == 'post') {
        $user->resetearContrasenaAdmin();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;

    

    // En user.php, agrega este caso al switch:

case 'exportar-pdf':
    if ($method == 'get') {
        $user->exportarUsuariosPDF();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;

    case 'registro':
    if ($method == 'post') {
        $user->registro();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;

    // En tu archivo principal de rutas
case 'dashboard':
case 'inicio':
    require_once 'src/Views/inicio.php';
    break;

    default:
        echo json_encode(responseHTTP::status404('Endpoint de usuario no encontrado: ' . $caso));
        break;
}