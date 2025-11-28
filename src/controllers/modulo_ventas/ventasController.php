<?php

namespace App\controllers\modulo_ventas;

use App\models\modulo_ventas\ventasModel;
use App\models\modulo_ventas\clienteModel;
use App\config\Security;
use App\config\SessionHelper;
use App\config\responseHTTP;

class ventasController
{
    private $method;
    private $data;

    public function __construct($method, $data = [])
    {
        $this->method = strtoupper($method);
        $this->data = Security::sanitizeInput($data);
        // Asegurar que la sesión esté iniciada en cada petición (AJAX incluido)
        SessionHelper::startSession();
    }

    /**
     * Obtener TODOS los productos para filtrado en frontend
     */
    public function obtenerTodosLosProductos()
    {
        try {
            $productos = ventasModel::obtenerTodosLosProductos();
            echo json_encode([
                'status' => 200,
                'data' => [
                    'productos' => $productos,
                    'total' => count($productos)
                ],
                'message' => 'Productos obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            error_log("ventasController::obtenerTodosLosProductos -> " . $e->getMessage());
            echo json_encode([
                'status' => 500,
                'data' => [],
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener stock actualizado de un producto específico (útil para refresh en tiempo real)
     */
    public function obtenerStockProducto()
    {
        if ($this->method != 'GET' && $this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $id_producto = intval($this->data['id_producto'] ?? 0);

        if (empty($id_producto)) {
            echo json_encode(responseHTTP::status400('id_producto es requerido'));
            return;
        }

        try {
            $con = \App\db\connectionDB::getConnection();
            $sql = "SELECT ip.ID_PRODUCTO, ip.CANTIDAD, ip.MINIMO, ip.MAXIMO, 
                           p.NOMBRE, p.PRECIO
                    FROM tbl_inventario_producto ip
                    JOIN tbl_producto p ON ip.ID_PRODUCTO = p.ID_PRODUCTO
                    WHERE ip.ID_PRODUCTO = :id_producto";
            
            $query = $con->prepare($sql);
            $query->execute([':id_producto' => $id_producto]);
            $stock = $query->fetch(\PDO::FETCH_ASSOC);

            if ($stock) {
                echo json_encode(responseHTTP::status200('Stock obtenido', $stock));
            } else {
                echo json_encode(responseHTTP::status404('Producto no encontrado'));
            }
        } catch (\Exception $e) {
            error_log("ventasController::obtenerStockProducto -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener stock'));
        }
    }

    /**
     * Obtener productos por categoría
     */
    public function obtenerProductosPorCategoria()
    {
        if ($this->method != 'GET' && $this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $categoria = $this->data['categoria'] ?? 'MAIZ';
        $productos = ventasModel::obtenerProductosPorCategoria($categoria);

        echo json_encode(responseHTTP::status200('Productos obtenidos', [
            'productos' => $productos,
            'categoria' => $categoria,
            'total' => count($productos)
        ]));
    }

    /**
     * Obtener todas las categorías
     */
    public function obtenerCategorias()
    {
        if ($this->method != 'GET') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $categorias = ventasModel::obtenerCategorias();
        echo json_encode(responseHTTP::status200('Categorías obtenidas', $categorias));
    }

    /**
     * Buscar cliente por DNI
     */
    public function buscarClientePorDNI()
    {
        if ($this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $dni = trim($this->data['dni'] ?? '');

        if (empty($dni)) {
            echo json_encode(responseHTTP::status400('DNI es requerido'));
            return;
        }

        $cliente = clienteModel::obtenerPorDNI($dni);

        if ($cliente) {
            echo json_encode(responseHTTP::status200('Cliente encontrado', $cliente));
        } else {
            echo json_encode(responseHTTP::status404('Cliente no encontrado'));
        }
    }

    /**
     * Buscar clientes por nombre
     */
    public function buscarClientes()
    {
        if ($this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $busqueda = trim($this->data['busqueda'] ?? '');

        if (empty($busqueda)) {
            echo json_encode(responseHTTP::status400('Búsqueda es requerida'));
            return;
        }

        $clientes = clienteModel::buscar($busqueda);
        echo json_encode(responseHTTP::status200('Clientes encontrados', $clientes));
    }

    /**
     * Buscar clientes activos por nombre para uso exclusivo en registrar-venta
     */
    public function buscarClientesActivos()
    {
        if ($this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $busqueda = trim($this->data['busqueda'] ?? '');

        if (empty($busqueda)) {
            echo json_encode(responseHTTP::status400('Búsqueda es requerida'));
            return;
        }

        $clientes = clienteModel::buscarActivos($busqueda);
        echo json_encode(responseHTTP::status200('Clientes activos encontrados', $clientes));
    }

    /**
     * Crear nuevo cliente
     */
    public function crearClienteNuevo()
    {
        try {
            $nombre = trim($this->data['nombre'] ?? '');
            $apellido = trim($this->data['apellido'] ?? '');
            $dni = trim($this->data['dni'] ?? '');
            $telefono = trim($this->data['telefono'] ?? '');
            $email = trim($this->data['email'] ?? '');

            if (empty($nombre) || empty($apellido) || empty($dni)) {
                echo json_encode([
                    'status' => 400,
                    'data' => [],
                    'message' => 'Nombre, Apellido y DNI son requeridos'
                ]);
                return;
            }

            // Validaciones ESTRICTAS: solo A-Za-z 0-9, SIN NINGÚN carácter especial
            $nombre_ok = preg_match('/^[A-Za-z\s]+$/', $nombre);
            $apellido_ok = preg_match('/^[A-Za-z\s]+$/', $apellido);
            $dni_ok = preg_match('/^[0-9]{4,15}$/', $dni);
            $telefono_ok = empty($telefono) ? true : preg_match('/^[0-9]{6,20}$/', $telefono);
            $email_ok = empty($email) ? true : filter_var($email, FILTER_VALIDATE_EMAIL);

            $errors = [];
            if (!$nombre_ok) $errors[] = 'Nombre: solo letras y espacios';
            if (!$apellido_ok) $errors[] = 'Apellido: solo letras y espacios';
            if (!$dni_ok) $errors[] = 'DNI: solo números (4-15 dígitos)';
            if (!$telefono_ok) $errors[] = 'Teléfono: solo números (6-20 dígitos)';
            if (!$email_ok) $errors[] = 'Email inválido';

            if (!empty($errors)) {
                echo json_encode(['status' => 400, 'data' => [], 'message' => implode('; ', $errors)]);
                return;
            }

            // Combinar nombre y apellido
            $nombre_completo = $nombre . ' ' . $apellido;
            $cliente = clienteModel::crearCliente($nombre_completo, $dni, $telefono, $email);

            if ($cliente) {
                echo json_encode([
                    'status' => 201,
                    'data' => $cliente,
                    'message' => 'Cliente creado correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 400,
                    'data' => [],
                    'message' => 'No se pudo crear el cliente. Verifique que el DNI no exista'
                ]);
            }
        } catch (\Exception $e) {
            error_log("ventasController::crearClienteNuevo -> " . $e->getMessage());
            echo json_encode([
                'status' => 500,
                'data' => [],
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener métodos de pago
     */
    public function obtenerMetodosPago()
    {
        try {
            $metodos = ventasModel::obtenerMetodosPago();
            echo json_encode([
                'status' => 200,
                'data' => $metodos,
                'message' => 'Métodos de pago obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            error_log("ventasController::obtenerMetodosPago -> " . $e->getMessage());
            echo json_encode([
                'status' => 500,
                'data' => [],
                'message' => 'Error al obtener métodos de pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear venta (función principal)
     */
    public function crearVenta()
    {
        if ($this->method != 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        // Validar sesión
        if (!SessionHelper::isLoggedIn()) {
            echo json_encode(responseHTTP::status401());
            return;
        }

        error_log("=== CREAR VENTA - DATOS RECIBIDOS ===");
        error_log("Data completa: " . json_encode($this->data));

        // Validar datos requeridos
        $campos_requeridos = ['ID_METODO_PAGO', 'TOTAL', 'ITEMS'];
        foreach ($campos_requeridos as $campo) {
            if (empty($this->data[$campo])) {
                error_log("Validación fallida: Campo $campo está vacío");
                echo json_encode(responseHTTP::status400("Campo $campo es requerido"));
                return;
            }
        }

        // ID_CLIENTE puede ser null (venta sin cliente), pero debe estar presente en los datos
        if (!array_key_exists('ID_CLIENTE', $this->data)) {
            error_log("Validación fallida: ID_CLIENTE no está presente");
            echo json_encode(responseHTTP::status400("Campo ID_CLIENTE es requerido"));
            return;
        }

        // Validar que ITEMS no esté vacío
        if (!is_array($this->data['ITEMS']) || empty($this->data['ITEMS'])) {
            error_log("Validación fallida: ITEMS vacío o no es array");
            echo json_encode(responseHTTP::status400("Debe agregar al menos un producto"));
            return;
        }

        $id_usuario = SessionHelper::getUserId();

        $venta = [
            'ID_USUARIO' => $id_usuario,
            'ID_CLIENTE' => $this->data['ID_CLIENTE'],
            'ID_METODO_PAGO' => $this->data['ID_METODO_PAGO'],
            'TOTAL' => floatval($this->data['TOTAL']),
            'ITEMS' => $this->data['ITEMS'],
            'CREADO_POR' => SessionHelper::getUserFullName() ?? SessionHelper::getUsername() ?? $id_usuario
        ];

        // Normalizar campos y aplicar valores por defecto
        // Si el frontend indica 'sin cliente' (ID_CLIENTE === null o 'null'),
        // dejamos ID_CLIENTE como NULL para reflejar venta sin cliente.
        $id_cliente = $venta['ID_CLIENTE'];
        if ($id_cliente === null || $id_cliente === '' || $id_cliente === 'null' || !is_numeric($id_cliente)) {
            error_log("ventasController::crearVenta -> ID_CLIENTE ausente o inválido, registrando venta SIN cliente (NULL)");
            $venta['ID_CLIENTE'] = null;
        } else {
            $venta['ID_CLIENTE'] = (int)$id_cliente;
        }

        // Asegurar tipos para ID_METODO_PAGO e ID_USUARIO
        $venta['ID_METODO_PAGO'] = isset($venta['ID_METODO_PAGO']) && is_numeric($venta['ID_METODO_PAGO']) ? (int)$venta['ID_METODO_PAGO'] : null;
        $venta['ID_USUARIO'] = (int)$venta['ID_USUARIO'];

        error_log("Iniciando creación de venta: " . print_r($venta, true));

        $resultado = ventasModel::crearVenta($venta);

        if ($resultado['success']) {
            error_log("Venta creada exitosamente - ID Factura: " . $resultado['id_factura']);
            
            // Registrar en bitácora
            $descripcion = "Creación de factura #" . $resultado['id_factura'] . " - Monto: $" . number_format($venta['TOTAL'], 2) . " - Items: " . count($venta['ITEMS']);
            ventasModel::registrarBitacora($id_usuario, 'CREAR_VENTA', $descripcion);
            
            echo json_encode(responseHTTP::status200($resultado['message'], ['id_factura' => $resultado['id_factura']]));
        } else {
            error_log("Error creando venta: " . $resultado['message']);
            echo json_encode(responseHTTP::status400($resultado['message']));
        }
    }

    /**
     * Obtener detalles de factura
     */
    public function obtenerDetallesFactura()
    {
        if ($this->method != 'GET') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $id_factura = $this->data['id_factura'] ?? 0;

        if (empty($id_factura)) {
            echo json_encode(responseHTTP::status400('ID de factura es requerido'));
            return;
        }

        $detalles = ventasModel::obtenerDetallesFactura($id_factura);
        $factura = ventasModel::obtenerFactura($id_factura);

        if ($factura) {
            $factura['DETALLES'] = $detalles;
            echo json_encode(responseHTTP::status200('Factura obtenida', $factura));
        } else {
            echo json_encode(responseHTTP::status404('Factura no encontrada'));
        }
    }

    /**
     * Listar facturas recientes
     */
    public function listarFacturas()
    {
        if ($this->method != 'GET') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        $limite = intval($this->data['limite'] ?? 20);

        // Parámetros opcionales para filtrado
        $busqueda = trim($this->data['busqueda'] ?? ($_GET['busqueda'] ?? ''));
        $fecha = trim($this->data['fecha'] ?? ($_GET['fecha'] ?? ''));

        if ($fecha !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            echo json_encode(responseHTTP::status400('Formato de fecha inválido (usar YYYY-MM-DD)'));
            return;
        }

        $facturas = ventasModel::listarFacturasRecientes($limite, $busqueda ?: null, $fecha ?: null);

        // Registrar en bitácora (navegación/consulta)
        if (SessionHelper::isLoggedIn()) {
            $id_usuario = SessionHelper::getUserId();
            $desc = 'Consultó historial de facturas';
            if (!empty($busqueda)) $desc .= ' - búsqueda: ' . $busqueda;
            if (!empty($fecha)) $desc .= ' - fecha: ' . $fecha;
            ventasModel::registrarBitacora($id_usuario, 'CONSULTAR_VENTAS', $desc);
        }

        echo json_encode(responseHTTP::status200('Facturas obtenidas', $facturas));
    }

    /**
     * Guardar comprobante de pago (archivo) para una factura
     * Espera multipart/form-data: fields -> id_factura, id_cliente, file -> comprobante
     */
    public function guardarComprobantePago()
    {
        // Debe ser POST y venir por multipart
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(responseHTTP::status405());
            return;
        }

        // Validar sesión opcional
        if (!SessionHelper::isLoggedIn()) {
            echo json_encode(responseHTTP::status401());
            return;
        }

        // Validar parámetros
        $id_factura = $_POST['id_factura'] ?? null;
        $id_cliente = $_POST['id_cliente'] ?? null;

        if ($id_factura === null || $id_cliente === null) {
            echo json_encode(responseHTTP::status400('id_factura e id_cliente son requeridos'));
            return;
        }

        if (!isset($_FILES['comprobante'])) {
            echo json_encode(responseHTTP::status400('No se recibió archivo comprobante'));
            return;
        }

        $file = $_FILES['comprobante'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $code = $file['error'];
            $map = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize en php.ini',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido por el formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Fallo al escribir archivo en disco',
                UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión'
            ];
            $msg = $map[$code] ?? ('Error de subida código ' . $code);
            echo json_encode(responseHTTP::status400('Error en subida: ' . $msg));
            return;
        }
        $allowedExt = ['jpg','jpeg','png','pdf'];
        $originalName = $file['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            echo json_encode(responseHTTP::status400('Tipo de archivo no permitido (permitido: jpg,jpeg,png,pdf)'));
            return;
        }

        // Obtener directorio y nombre
        $dir = ventasModel::ensureFacturasDir();
        if (!$dir) {
            echo json_encode(responseHTTP::status500('No se pudo asegurar carpeta de facturas'));
            return;
        }

        $filename = ventasModel::generarNombreComprobante($id_factura, $id_cliente, $originalName);
        $destination = $dir . DIRECTORY_SEPARATOR . $filename;

        // Mover archivo subido
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            error_log('ventasController::guardarComprobantePago -> move_uploaded_file failed for ' . $destination);
            echo json_encode(responseHTTP::status500('Error guardando comprobante en el servidor'));
            return;
        }

        // Actualizar estado de factura a PAGADA si corresponde
        try {
            $con = \App\db\connectionDB::getConnection();
            // Obtener método de pago
            $sql = "SELECT mp.METODO_PAGO FROM tbl_factura f JOIN tbl_metodo_pago mp ON f.ID_METODO_PAGO = mp.ID_METODO_PAGO WHERE f.ID_FACTURA = ? LIMIT 1";
            $q = $con->prepare($sql);
            $q->execute([$id_factura]);
            $metodo = strtolower($q->fetchColumn() ?? '');
            if ($metodo === 'tarjeta' || $metodo === 'transferencia') {
                $sqlUp = "UPDATE tbl_factura SET ESTADO_FACTURA = 'PAGADA' WHERE ID_FACTURA = ?";
                $qUp = $con->prepare($sqlUp);
                $qUp->execute([$id_factura]);
            }
        } catch (\Exception $e) {
            error_log('Error actualizando estado de factura tras subir comprobante: ' . $e->getMessage());
        }

        echo json_encode(responseHTTP::status200('Comprobante guardado', ['archivo' => $filename]));
    }

    /**
     * Servir comprobante asociado a una factura (devuelve bytes del archivo)
     * URL: ?caso=servirComprobante&id_factura=123
     */
    public function servirComprobante()
    {
        // Permitimos GET
        if ($this->method != 'get' && $this->method != 'GET') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $id_factura = intval($this->data['id_factura'] ?? ($_GET['id_factura'] ?? 0));
        if (empty($id_factura)) {
            http_response_code(400);
            echo 'id_factura es requerido';
            return;
        }

        $ruta = \App\models\modulo_ventas\ventasModel::buscarComprobantePorFactura($id_factura);
        if (empty($ruta) || !file_exists($ruta)) {
            http_response_code(404);
            echo 'No encontrado';
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $ruta) ?: 'application/octet-stream';
        finfo_close($finfo);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($ruta));
        // For images allow caching
        header('Cache-Control: public, max-age=86400');
        readfile($ruta);
        exit;
    }
}
