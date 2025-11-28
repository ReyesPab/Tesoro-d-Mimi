<?php
// Asegurar que el autoload de Composer esté incluido cuando este archivo
// es solicitado directamente desde el navegador (sin pasar por public/index.php)
if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    // Intento alternativo por si la estructura difiere
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
    }
} else {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

use App\controllers\modulo_ventas\ventasController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$ventas = new ventasController($method, $data);
// Caso especial: servir comprobante (devuelve bytes, no JSON)
if (($caso ?? '') === 'servirComprobante') {
    $ventas->servirComprobante();
    exit;
}

// RUTAS API (siempre retornan JSON)
$apiCases = [
    'obtenerCategorias',
    'obtenerTodosLosProductos',
    'obtenerProductosPorCategoria',
    'buscarClientePorDNI',
    'buscarClientes',
    'buscarClientesActivos',
    'crearClienteNuevo',
    'obtenerMetodosPago',
    'crearVenta',
    'guardarComprobantePago',
    'obtenerDetallesFactura',
    'listarFacturas',
    'obtenerStockProducto'
];

if (in_array($caso, $apiCases)) {
    // Para API, establecer headers JSON
    header('Content-Type: application/json');
    
    switch ($caso) {
        case 'obtenerCategorias':
            $ventas->obtenerCategorias();
            break;
        
        case 'obtenerTodosLosProductos':
            $ventas->obtenerTodosLosProductos();
            break;
            
        case 'obtenerProductosPorCategoria':
            $ventas->obtenerProductosPorCategoria();
            break;
        
        case 'buscarClientePorDNI':
            $ventas->buscarClientePorDNI();
            break;
            
        case 'buscarClientes':
            $ventas->buscarClientes();
            break;

        case 'buscarClientesActivos':
            $ventas->buscarClientesActivos();
            break;
            
        case 'crearClienteNuevo':
            $ventas->crearClienteNuevo();
            break;
        
        case 'obtenerMetodosPago':
            $ventas->obtenerMetodosPago();
            break;
        
        case 'crearVenta':
            $ventas->crearVenta();
            break;
        
        case 'guardarComprobantePago':
            $ventas->guardarComprobantePago();
            break;
            
        case 'obtenerStockProducto':
            $ventas->obtenerStockProducto();
            break;
            
        case 'obtenerDetallesFactura':
            $ventas->obtenerDetallesFactura();
            break;
            
        case 'listarFacturas':
            $ventas->listarFacturas();
            break;
        
        default:
            http_response_code(404);
            echo json_encode(responseHTTP::status404('Endpoint de API no encontrado'));
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(responseHTTP::status404('Caso no definido'));
}
?>
