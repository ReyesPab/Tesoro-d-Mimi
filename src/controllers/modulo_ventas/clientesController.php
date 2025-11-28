<?php
namespace App\controllers\modulo_ventas;

use App\models\modulo_ventas\clienteModel;
use App\config\responseHTTP;
use App\config\Security;
use App\config\SessionHelper;
use App\config\PermisosHelper;

class clientesController {
        public function editarCliente() {
            // Permisos
            PermisosHelper::checkPermissionAjax('modulo_ventas/gestion-clientes.php', 'ACTUALIZAR');

            if ($this->method !== 'POST') {
                echo json_encode(responseHTTP::status405());
                return;
            }

            $id_cliente = intval($this->data['id_cliente'] ?? 0);
            $id_usuario = $_SESSION['id_usuario'] ?? null;
            $nombre = trim($this->data['nombre'] ?? '');
            $apellido = trim($this->data['apellido'] ?? '');
            $dni = trim($this->data['dni'] ?? '');
            $telefono = trim($this->data['telefono'] ?? '');
            $correo = trim($this->data['email'] ?? $this->data['correo'] ?? '');
            $direccion = trim($this->data['direccion'] ?? '');
            $estado = strtoupper(trim($this->data['estado'] ?? 'ACTIVO'));
            $id_usuario = $_SESSION['id_usuario'] ?? null;

            // Validaciones
            if (empty($id_cliente) || empty($nombre) || empty($apellido) || empty($dni)) {
                echo json_encode(responseHTTP::status400('ID, Nombre, Apellido y DNI son requeridos'));
                return;
            }

            // Normalizar DNI y teléfono (solo dígitos)
            $dni_digits = preg_replace('/\D/', '', $dni);
            $telefono_digits = preg_replace('/\D/', '', $telefono);

            $nombre_ok = preg_match('/^[A-Za-zÀ-ÿ\s]{2,50}$/u', $nombre);
            $apellido_ok = preg_match('/^[A-Za-zÀ-ÿ\s]{2,50}$/u', $apellido);
            $dni_ok = preg_match('/^[0-9]{4,13}$/', $dni_digits);
            $telefono_ok = empty($telefono_digits) ? true : preg_match('/^[0-9]{8}$/', $telefono_digits);
            $correo_ok = empty($correo) ? true : filter_var($correo, FILTER_VALIDATE_EMAIL);
            $estado_ok = in_array($estado, ['ACTIVO', 'INACTIVO']);
            $errors = [];
            if (!$nombre_ok) $errors[] = 'Nombre: solo letras y espacios (2-50)';
            if (!$apellido_ok) $errors[] = 'Apellido: solo letras y espacios (2-50)';
            if (!$dni_ok) $errors[] = 'DNI: solo números (4-15 dígitos)';
            if (!$telefono_ok) $errors[] = 'Teléfono: formato inválido (6-20)';
            if (!$correo_ok) $errors[] = 'Email inválido';
            if (!$estado_ok) $errors[] = 'Estado inválido';
            if (!empty($errors)) {
                echo json_encode(responseHTTP::status400(implode('; ', $errors)));
                return;
            }

            $datos = [
                'ID_CLIENTE' => $id_cliente,
                'NOMBRE' => $nombre,
                'APELLIDO' => $apellido,
                // pasar versiones normalizadas al modelo
                'DNI' => $dni_digits,
                'TELEFONO' => $telefono_digits,
                'CORREO' => $correo,
                'DIRECCION' => $direccion,
                'ESTADO' => $estado
            ];

            try {
                $res = \App\models\modulo_ventas\clienteModel::actualizar($datos, $id_usuario);
                if ($res['success']) {
                    echo json_encode(responseHTTP::status200('Cliente actualizado correctamente'));
                } else {
                    echo json_encode(responseHTTP::status400($res['message'] ?? 'Error al actualizar'));
                }
            } catch (\Exception $e) {
                error_log("clientesController::editarCliente -> " . $e->getMessage());
                echo json_encode(responseHTTP::status500('Error al editar cliente'));
            }
        }

        public function eliminarCliente() {
            // Permisos
            PermisosHelper::checkPermissionAjax('modulo_ventas/gestion-clientes.php', 'ELIMINAR');

            if ($this->method !== 'POST') {
                echo json_encode(responseHTTP::status405());
                return;
            }

            // Trazas para depuración
            error_log("clientesController::eliminarCliente - entrada. Method={$this->method}");
            error_log("clientesController::eliminarCliente - payload: " . json_encode($this->data));

            $id_cliente = intval($this->data['id_cliente'] ?? 0);
            // Obtener usuario de sesión (puede ser null)
            $id_usuario = $_SESSION['id_usuario'] ?? null;
            if (empty($id_cliente)) {
                error_log("clientesController::eliminarCliente - id_cliente inválido: {$id_cliente}");
                echo json_encode(responseHTTP::status400('ID de cliente requerido'));
                return;
            }

            try {
                error_log("clientesController::eliminarCliente - llamando a modelo eliminar para id={$id_cliente}, id_usuario={$id_usuario}");
                $res = \App\models\modulo_ventas\clienteModel::eliminar($id_cliente, $id_usuario);
                error_log("clientesController::eliminarCliente - resultado modelo: " . json_encode($res));
                if ($res['success']) {
                    echo json_encode(responseHTTP::status200('Cliente eliminado correctamente'));
                } else {
                    echo json_encode(responseHTTP::status400($res['message'] ?? 'Error al eliminar cliente'));
                }
            } catch (\Exception $e) {
                error_log("clientesController::eliminarCliente -> " . $e->getMessage());
                echo json_encode(responseHTTP::status500('Error al eliminar cliente'));
            }
        }
    private $method;
    private $data;

    public function __construct($method, $data = []) {
        $this->method = strtoupper($method);
        $this->data = Security::sanitizeInput($data);
        SessionHelper::startSession();
    }


        public function listarClientes() {
            if ($this->method !== 'GET' && $this->method !== 'POST') {
                echo json_encode(responseHTTP::status405());
                return;
            }
            try {
                $clientes = clienteModel::listarTodos();
                echo json_encode([
                    'status' => 200,
                    'data' => $clientes,
                    'message' => 'Clientes listados correctamente'
                ]);
            } catch (\Exception $e) {
                error_log("clientesController::listarClientes -> " . $e->getMessage());
                echo json_encode([
                    'status' => 500,
                    'data' => [],
                    'message' => 'Error al listar clientes: ' . $e->getMessage()
                ]);
            }
        }

        public function buscarClientes() {
            if ($this->method !== 'POST') {
                echo json_encode(responseHTTP::status405());
                return;
            }
            $busqueda = trim($this->data['busqueda'] ?? '');
            try {
                if ($busqueda === '') {
                    // Si no hay filtro, devolver solo clientes ACTIVO (no mostrar inactivos tras eliminación)
                    $clientes = clienteModel::listar();
                    echo json_encode(responseHTTP::status200('Clientes listados', $clientes));
                } else {
                    $clientes = clienteModel::buscar($busqueda);
                    echo json_encode(responseHTTP::status200('Clientes encontrados', $clientes));
                }
            } catch (\Exception $e) {
                error_log("clientesController::buscarClientes -> " . $e->getMessage());
                echo json_encode([
                    'status' => 500,
                    'data' => [],
                    'message' => 'Error al buscar clientes'
                ]);
            }
        }

    public function crearCliente() {
        if ($this->method !== 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        $nombre = trim($this->data['nombre'] ?? '');
        $apellido = trim($this->data['apellido'] ?? '');
        $dni = trim($this->data['dni'] ?? '');
        $telefono = trim($this->data['telefono'] ?? '');
        $correo = trim($this->data['email'] ?? '');
        $direccion = trim($this->data['direccion'] ?? '');
        $id_usuario = $_SESSION['id_usuario'] ?? null;

        // Validaciones estrictas
        if (empty($nombre) || empty($apellido) || empty($dni)) {
            echo json_encode([
                'status' => 400,
                'data' => [],
                'message' => 'Nombre, Apellido y DNI son requeridos'
            ]);
            return;
        }
        // Normalizar campos: eliminar separadores de DNI/teléfono
        $dni_digits = preg_replace('/\D/', '', $dni);
        $telefono_digits = preg_replace('/\D/', '', $telefono);

        $nombre_ok = preg_match('/^[A-Za-zÀ-ÿ\s]{2,50}$/u', $nombre);
        $apellido_ok = preg_match('/^[A-Za-zÀ-ÿ\s]{2,50}$/u', $apellido);
        $dni_ok = preg_match('/^[0-9]{4,13}$/', $dni_digits);
        $telefono_ok = empty($telefono_digits) ? true : preg_match('/^[0-9]{8}$/', $telefono_digits);
        $correo_ok = empty($correo) ? true : filter_var($correo, FILTER_VALIDATE_EMAIL);
        $errors = [];
        if (!$nombre_ok) $errors[] = 'Nombre: solo letras y espacios (2-50)';
        if (!$apellido_ok) $errors[] = 'Apellido: solo letras y espacios (2-50)';
        if (!$dni_ok) $errors[] = 'DNI: solo números (4-13 dígitos)';
        if (!$telefono_ok) $errors[] = 'Teléfono: solo números (exactamente 8 dígitos)';
        if (!$correo_ok) $errors[] = 'Email inválido';
        if (!empty($errors)) {
            echo json_encode(['status' => 400, 'data' => [], 'message' => implode('; ', $errors)]);
            return;
        }
        $datos = [
            'NOMBRE' => $nombre,
            'APELLIDO' => $apellido,
            // pasar versiones normalizadas
            'DNI' => $dni_digits,
            'TELEFONO' => $telefono_digits,
            'CORREO' => $correo,
            'DIRECCION' => $direccion
        ];
        try {
            $res = clienteModel::crear($datos, $id_usuario);
            if ($res['success']) {
                echo json_encode([
                    'status' => 201,
                    'data' => $res,
                    'message' => 'Cliente creado correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 400,
                    'data' => [],
                    'message' => $res['message']
                ]);
            }
        } catch (\Exception $e) {
            error_log("clientesController::crearCliente -> " . $e->getMessage());
            echo json_encode([
                'status' => 500,
                'data' => [],
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ]);
        }
    }

    // Métodos para editar/eliminar pueden agregarse aquí
}
