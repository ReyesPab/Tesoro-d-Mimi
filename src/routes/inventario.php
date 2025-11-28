<?php
use App\controllers\inventarioController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$inventario = new inventarioController($method, $data);

// Rutas para gestión de inventario
switch ($caso) {
    // MATERIA PRIMA
    case 'listar':
        if ($method == 'get') {
            $inventario->listarInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener':
        if ($method == 'get') {
            $inventario->obtenerItemInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'actualizar':
        if ($method == 'post') {
            $inventario->actualizarInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'historial':
        if ($method == 'get') {
            $inventario->obtenerHistorial();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'exportar-pdf':
        if ($method == 'get') {
            $inventario->exportarInventarioPDF();  // Materia prima
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'alertas':
        if ($method == 'get') {
            $inventario->obtenerAlertas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    // PRODUCTOS - casos específicos
    case 'listarProductos':
        if ($method == 'get') {
            $inventario->listarInventarioProductos();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtenerProducto':
        if ($method == 'get') {
            $inventario->obtenerProductoInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'ajustarProducto':
        if ($method == 'post') {
            $inventario->ajustarInventarioProducto();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'historialProducto':
        if ($method == 'get') {
            $inventario->obtenerHistorialProducto();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'exportarPdfProductos':
        if ($method == 'get') {
            $inventario->exportarInventarioProductosPDF();  // Productos - NUEVO NOMBRE
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;        

    default:
        echo json_encode(responseHTTP::status404('Endpoint de inventario no encontrado: ' . $caso));
        break;

     case 'editarInventarioProducto':
    if ($method == 'post') {
        $inventario->editarInventarioProducto();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;   
}
?>