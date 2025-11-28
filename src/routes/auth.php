<?php
use App\controllers\authController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$data = json_decode(file_get_contents("php://input"), true) ?? [];
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$auth = new authController($method, $data);

// Rutas para autenticación
switch ($caso) {
    case 'login':
        if ($method == 'post') {
            $auth->login();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'recuperar-password':
        if ($method == 'post') {
            $auth->recuperarPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'verificar-preguntas':
        if ($method == 'post') {
            $auth->verificarPreguntas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'cambiar-password':
        if ($method == 'post') {
            $auth->cambiarPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'cambiar-password-dashboard':
        if ($method == 'post') {
            $auth->cambiarPasswordDashboard();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-preguntas':
        if ($method == 'get') {
            $auth->obtenerPreguntas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-preguntas-usuario':
        if ($method == 'get') {
            $auth->obtenerPreguntasUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'configurar-preguntas':
        if ($method == 'post') {
            $auth->configurarPreguntas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    // RUTAS PARA RECUPERACIÓN
    case 'recuperar-password-avanzado':
        if ($method == 'post') {
            $auth->recuperarPasswordAvanzado();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'validar-respuestas-recuperacion':
        if ($method == 'post') {
            $auth->validarRespuestasRecuperacion();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'cambiar-password-recuperacion':
        if ($method == 'post') {
            $auth->cambiarPasswordRecuperacion();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    // NUEVA RUTA: Recuperación completa por preguntas
    case 'recuperacion-completa-preguntas':
        if ($method == 'post') {
            $auth->recuperacionCompletaPorPreguntas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    // CASO TEMPORAL PARA DEBUG
    case 'debug-preguntas':
        if ($method == 'post') {
            $auth->debugPreguntasUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

        // Agrega estos casos a tu switch existente en auth.php

case 'iniciar-2fa':
    if ($method == 'post') {
        $auth->iniciar2FA();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
    
case 'verificar-2fa':
    if ($method == 'post') {
        $auth->verificar2FA();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
    
case 'reenviar-codigo-2fa':
    if ($method == 'post') {
        $auth->reenviarCodigo2FA();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
     
    default:
        echo json_encode(responseHTTP::status404('Endpoint de autenticación no encontrado'));
        break;

}
