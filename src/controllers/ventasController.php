<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\ventasModel;
use PDO;

class ventasController {

    private $method;
    private $data;

    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }



    /**
     * Procesar venta
     */
    public function procesarVenta() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener datos del usuario desde la sesión
        $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? null;
        $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';

        // Si la petición viene como multipart/form-data con un archivo, recoger los datos desde $_POST['venta']
        $hasFile = false;
        if (!empty($_FILES) && isset($_FILES['foto_comprobante'])) {
            $hasFile = true;
            $ventaRaw = $_POST['venta'] ?? null;
            if ($ventaRaw) {
                $decoded = json_decode($ventaRaw, true);
                if (is_array($decoded)) {
                    // Reemplazar data con lo recibido (pero no perder sanitización mínima)
                    $this->data = Security::sanitizeInput($decoded);
                }
            }
        }

        // Validar datos requeridos
        $camposRequeridos = ['productos', 'total', 'id_metodo_pago'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("Campo requerido faltante: $campo"));
                return;
            }
        }

        if (empty($id_usuario)) {
            echo json_encode(responseHTTP::status400('Usuario no autenticado'));
            return;
        }

        // Validar que haya productos
        if (empty($this->data['productos']) || !is_array($this->data['productos'])) {
            echo json_encode(responseHTTP::status400('Debe seleccionar al menos un producto'));
            return;
        }

        // Validar total
        if ($this->data['total'] <= 0) {
            echo json_encode(responseHTTP::status400('El total debe ser mayor a 0'));
            return;
        }

        // Preparar datos para el modelo
        $datosVenta = [
            'id_usuario' => (int)$id_usuario,
            'productos' => $this->data['productos'],
            'total' => (float)$this->data['total'],
            'id_metodo_pago' => (int)$this->data['id_metodo_pago'],
            'id_cliente' => $this->data['id_cliente'] ?? 1, // Cliente genérico
            'creado_por' => $creado_por
        ];

        // Procesar la venta
        $resultado = ventasModel::procesarVenta($datosVenta);

        if ($resultado['success']) {
            $responseData = [
                'id_venta' => $resultado['id_venta'],
                'id_factura' => $resultado['id_factura']
            ];

            // Si se subió un comprobante, moverlo a la carpeta correspondiente
            if ($hasFile && isset($_FILES['foto_comprobante']) && $_FILES['foto_comprobante']['error'] === UPLOAD_ERR_OK) {
                try {
                    // Ruta base a public/uploads/ventas
                    $publicPath = realpath(__DIR__ . '/../../public');
                    if ($publicPath === false) $publicPath = __DIR__ . '/../../public';
                    $baseUploads = rtrim($publicPath, '\\/');
                    $weekFolder = date('Y') . '_sem_' . date('W');
                    $targetDir = $baseUploads . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . $weekFolder . DIRECTORY_SEPARATOR;

                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }

                    $origName = $_FILES['foto_comprobante']['name'];
                    $ext = pathinfo($origName, PATHINFO_EXTENSION);
                    $idForName = !empty($resultado['id_factura']) ? $resultado['id_factura'] : $resultado['id_venta'];
                    $timestamp = date('Ymd_His');
                    $safeExt = $ext ? preg_replace('/[^a-zA-Z0-9]/', '', $ext) : 'jpg';
                    $fileName = 'recibo_' . $idForName . '_' . $timestamp . '.' . $safeExt;
                    $destination = $targetDir . $fileName;

                    if (move_uploaded_file($_FILES['foto_comprobante']['tmp_name'], $destination)) {
                        // Ruta relativa accesible desde public
                        $relativePath = 'uploads/ventas/' . $weekFolder . '/' . $fileName;
                        $responseData['comprobante_path'] = $relativePath;
                    } else {
                        // No se pudo mover el archivo; registrar error pero no fallar la venta
                        error_log("ventasController::procesarVenta - no se pudo mover comprobante a $destination");
                        $responseData['comprobante_error'] = 'No se pudo guardar el comprobante en el servidor';
                    }
                } catch (\Exception $e) {
                    error_log('ventasController::procesarVenta guardar comprobante -> ' . $e->getMessage());
                    $responseData['comprobante_error'] = 'Error al procesar el comprobante';
                }
            }

            echo json_encode([
                'status' => 200,
                'data' => $responseData,
                'message' => $resultado['message']
            ]);
        } else {
            echo json_encode(responseHTTP::status400($resultado['message']));
        }
    }

    /**
     * Obtener métodos de pago
     */
    public function obtenerMetodosPago() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        try {
            $metodos = ventasModel::obtenerMetodosPago();

            echo json_encode([
                'status' => 200,
                'data' => ['metodos_pago' => $metodos],
                'message' => 'Métodos de pago obtenidos correctamente'
            ]);

        } catch (\Exception $e) {
            error_log("ventasController::obtenerMetodosPago -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener métodos de pago'));
        }
    }
}
?>
