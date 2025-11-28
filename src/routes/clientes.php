<?php
use App\controllers\modulo_ventas\clientesController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$clientes = new clientesController($method, $data);

switch ($caso) {
    case 'listarClientes':
        $clientes->listarClientes();
        break;
    case 'buscarClientes':
        $clientes->buscarClientes();
        break;
    case 'crearCliente':
        $clientes->crearCliente();
        break;
    case 'editarCliente':
        $clientes->editarCliente();
        break;
    case 'eliminarCliente':
        $clientes->eliminarCliente();
        break;
    default:
        echo json_encode(responseHTTP::status404('Endpoint de clientes no encontrado'));
        break;
}
