<?php
use App\controllers\comprasController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$compras = new comprasController($method, $data);
 
// RUTAS API (siempre retornan JSON)
$apiCases = [
        'guardarRelacionProductoProveedor',
    'obtenerRelacionesProductoProveedor',
    'eliminarRelacionProductoProveedor',
     'obtenerProductosActivos',
    'obtenerProductosProveedor',
    'registrarOrdenCompra',
    'finalizarCompra', 
    'obtenerProductosProveedor',
    'obtenerDetalleRecepcion',
    'obtenerRecepcionesPendientes',
    'obtenerRecepcionesFinalizadas',
    'obtenerProveedores',
    'obtenerProductosProveedorRelacion',
    'obtenerRecepcionesFiltradas',
    'listarCompras',
    'cancelarOrdenCompra',
    'obtenerRecepcionesCanceladas',
    'obtenerRecepcionesFinalizadasFiltradas',
    'listarMateriaPrima',
    'ingresarInventario',
    'listarProveedores',
    'cambiarEstadoProveedor',
    'exportarProveedoresPDF',
    'obtenerProveedorPorId',
    'editarProveedor',
    'obtenerMateriaPrimaPorId',
    'editarMateriaPrima',
    'obtenerUnidadesMedida',
    'obtenerProveedoresActivosRegistroProductos',
    'registrarProductoProveedor',
    'listarProductosProveedores',
    'obtenerProductoProveedorPorId',
    'obtenerUnidadesMedidaProductosProveedores',
    'editarProductoProveedor',
    'registrarProveedor',
    'validarProveedor',
    'registrarProductoProveedorCompleto', // NUEVO - AGREGAR ESTA LÍNEA
    'obtenerProductoProveedorCompleto',   // NUEVO - AGREGAR ESTA LÍNEA
'cambiarEstadoProductoProveedor',
    'anularCompra',

      'debug'
    
];

if (in_array($caso, $apiCases)) {
    // Para API, establecer headers JSON
    header('Content-Type: application/json');
    
    switch ($caso) {
       
       case 'obtenerProductosActivos':
    $compras->obtenerProductosActivos();
    break;
    case 'obtenerProductosProveedorRelacion':
    $compras->obtenerProductosProveedorRelacion();
    break;
    case 'anularCompra':
    $compras->anularCompra();
    break;
case 'obtenerProductosProveedor':
    $compras->obtenerProductosProveedor();
    break;
case 'guardarRelacionProductoProveedor':
    $compras->guardarRelacionProductoProveedor();
    break;
case 'obtenerRelacionesProductoProveedor':
    $compras->obtenerRelacionesProductoProveedor();
    break;
case 'eliminarRelacionProductoProveedor':
    $compras->eliminarRelacionProductoProveedor();
    break;        
        case 'registrarOrdenCompra':
            $compras->registrarOrdenCompra();
            break;
        case 'obtenerRecepcionesFiltradas':
            $compras->obtenerRecepcionesFiltradas();
            break;
        case 'finalizarCompra':
            $compras->finalizarCompra();
            break;
        case 'obtenerProductosProveedor':
            if ($method == 'get') {
                $compras->obtenerProductosProveedor();
            } else {
                echo json_encode(responseHTTP::status405());
            }
            break;
        case 'obtenerDetalleRecepcion':
            $compras->obtenerDetalleRecepcion();
            break;
        case 'obtenerRecepcionesPendientes':
            $compras->obtenerRecepcionesPendientes();
            break;
        case 'obtenerRecepcionesFinalizadas':
            $compras->obtenerRecepcionesFinalizadas();
            break;
        case 'obtenerProveedores':
            $compras->obtenerProveedores();
            break;
        case 'listarCompras':
            $compras->listarCompras();
            break;
        case 'debug':
            $compras->debug();
            break;
        case 'cancelarOrdenCompra':
            $compras->cancelarOrdenCompra();
            break;
        case 'obtenerRecepcionesCanceladas':
            $compras->obtenerRecepcionesCanceladas();
            break;
        case 'obtenerRecepcionesFinalizadasFiltradas':
            $compras->obtenerRecepcionesFinalizadasFiltradas();
            break;
        case 'listarMateriaPrima':
            $compras->listarMateriaPrima();
            break;
        case 'ingresarInventario':
            $compras->ingresarInventario();
            break;
        case 'listarProveedores':
            $compras->listarProveedores();
            break;
case 'cambiarEstadoProductoProveedor':
    $compras->cambiarEstadoProductoProveedor();
    break;
        case 'exportarProveedoresPDF':
            $compras->exportarProveedoresPDF();
            break;
        case 'obtenerProveedorPorId':
            $compras->obtenerProveedorPorId();
            break;
        case 'editarProveedor':
            $compras->editarProveedor();
            break;
        case 'obtenerMateriaPrimaPorId':
            $compras->obtenerMateriaPrimaPorId();
            break;
        case 'editarMateriaPrima':
            $compras->editarMateriaPrima();
            break;
        case 'obtenerUnidadesMedida':
            $compras->obtenerUnidadesMedida();
            break;
        case 'obtenerProveedoresActivosRegistroProductos':
            $compras->obtenerProveedoresActivosRegistroProductos();
            break;
        case 'registrarProductoProveedor':
            $compras->registrarProductoProveedor();
            break;
        case 'listarProductosProveedores':
            $compras->listarProductosProveedores();
            break;
        case 'obtenerProductoProveedorPorId':
            $compras->obtenerProductoProveedorPorId();
            break;
        case 'obtenerUnidadesMedidaProductosProveedores':
            $compras->obtenerUnidadesMedidaProductosProveedores();
            break;
        case 'editarProductoProveedor':
            $compras->editarProductoProveedor();
            break;
        case 'registrarProveedor':
            $compras->registrarProveedor();
            break;
        case 'validarProveedor':
            $compras->validarProveedor();
            break;
        // NUEVOS CASOS - AGREGAR ESTOS
        case 'registrarProductoProveedorCompleto':
            $compras->registrarProductoProveedorCompleto();
            break;
        case 'obtenerProductoProveedorCompleto':
            $compras->obtenerProductoProveedorCompleto();
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