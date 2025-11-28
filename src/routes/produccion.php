<?php
use App\controllers\produccionController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

// Crear instancia del controlador
$produccion = new produccionController($method, $data);

// RUTAS API
$apiCases = [
    'verificarStock',
    'obtenerReceta',
    'obtenerProductosProduccion',
    'obtenerRecetaExistente',
    'crearOrdenProduccion',
    'obtenerOrdenesProduccion',  // NUEVO
    'iniciarProduccion',  // NUEVO
    'obtenerDetalleProduccion',  // NUEVO
     'finalizarProduccion',  // 🆕 NUEVO
    'crearReceta',
    'obtenerMateriasPrimas',
    'verificarRecetaExistente',  // 🆕 NUEVO 
    'obtenerTodasLasRecetas',      // 🆕 NUEVO
    'obtenerRecetaPorProducto',     // 🆕 NUEVO 
    'sobreescribirReceta',  // 🆕 NUEVO   
    'obtenerProductos',
    'ingresarProductoInventario',
    'obtenerProductoPorId',     // 🆕 NUEVO
    'actualizarProducto',        // 🆕 NUEVO  
    'registrarPerdidasProduccion',  // 🆕 NUEVO
    'obtenerMotivosPerdida',        // 🆕 NUEVO
    'obtenerPerdidasPorProduccion',  // 🆕 NUEVO
'crearProductoConRecetaCompleto',  // 🆕 NUEVO
'obtenerMateriasPrimasParaReceta', // 🆕 NUEVO (nombre actualizado)
    'obtenerUnidadesMedida',            // 🆕 NUEVO
        'obtenerProductoRecetaPorId',  // 🆕 NUEVO
    'editarProductoConRecetaCompleto'  // 🆕 NUEVO
];

if (in_array($caso, $apiCases)) {
    header('Content-Type: application/json');
    
    switch ($caso) {
        case 'verificarStock':
            $produccion->verificarStock();
            break;
        case 'obtenerReceta':
            $produccion->obtenerReceta();
            break;
        case 'obtenerProductosProduccion':
            $produccion->obtenerProductosProduccion();
            break;
        case 'obtenerRecetaExistente':
            $produccion->obtenerRecetaExistente();
            break;
        case 'crearOrdenProduccion':
            $produccion->crearOrdenProduccion(); // <- MÉTODO DE INSTANCIA
            break;
        default:
            echo json_encode(responseHTTP::status404('Endpoint de API no encontrado'));
            break;
        case 'obtenerOrdenesProduccion':
            $produccion->obtenerOrdenesProduccion();
            break;
        case 'iniciarProduccion':
            $produccion->iniciarProduccion();
            break;   
        case 'obtenerDetalleProduccion':
            $produccion->obtenerDetalleProduccion();
            break;   
        case 'finalizarProduccion':
            $produccion->finalizarProduccion();
            break;    
        case 'crearReceta':
            $produccion->crearReceta();
            break;
        case 'obtenerMateriasPrimas':
            $produccion->obtenerMateriasPrimas();
            break;
        case 'verificarRecetaExistente':  // 🆕 NUEVO
            $produccion->verificarRecetaExistente();
        break;      
        case 'obtenerTodasLasRecetas':
            $produccion->obtenerTodasLasRecetas();
            break;
        case 'obtenerRecetaPorProducto':
            $produccion->obtenerRecetaPorProducto();
            break;
        case 'sobreescribirReceta':
            $produccion->sobreescribirReceta();
            break;
        case 'obtenerProductos':  // 🆕 NUEVO
            $produccion->obtenerProductos();
            break; 
        case 'ingresarProductoInventario':  // 🆕 NUEVO
            $produccion->ingresarProductoInventario();
            break;  
        case 'obtenerProductoPorId':  // 🆕 NUEVO
            $produccion->obtenerProductoPorId();
            break;
        case 'actualizarProducto':    // 🆕 NUEVO
            $produccion->actualizarProducto();
            break;
       case 'registrarPerdidasProduccion':  // 🆕 NUEVO
        $produccion->registrarPerdidasProduccion();
        break;
    case 'obtenerMotivosPerdida':        // 🆕 NUEVO
        $produccion->obtenerMotivosPerdida();
        break;
    case 'obtenerPerdidasPorProduccion': // 🆕 NUEVO
        $produccion->obtenerPerdidasPorProduccion();
        break;   
case 'crearProductoConRecetaCompleto':
        $produccion->crearProductoConRecetaCompleto();
        break;
    case 'obtenerUnidadesMedida':
        $produccion->obtenerUnidadesMedida();
        break;    
    case 'obtenerMateriasPrimasParaReceta':
        $produccion->obtenerMateriasPrimasParaReceta();
        break;     
      case 'obtenerProductoRecetaPorId':  // 🆕 NUEVO
        $produccion->obtenerProductoRecetaPorId();
        break;
    case 'editarProductoConRecetaCompleto':  // 🆕 NUEVO
        $produccion->editarProductoConRecetaCompleto();
        break;                    
    }
    
    exit;
}

// Si llegamos aquí, es porque no es una API call
header('Content-Type: application/json');
echo json_encode(responseHTTP::status404('Ruta no encontrada'));