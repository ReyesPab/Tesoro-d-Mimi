<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\comprasModel;
use PDO;

class comprasController {
     
       
    private $method;
    private $data;
   /**
 * Obtener productos de un proveedor específico usando nueva relación
 */
public function obtenerProductosProveedorRelacion() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor'])) {
        echo json_encode(responseHTTP::status400("El ID del proveedor es obligatorio"));
        return;
    }
    
    try {
        $productos = comprasModel::obtenerProductosPorProveedorRelacion($this->data['id_proveedor']);
        
        echo json_encode([
            'status' => 200,
            'data' => $productos,
            'message' => 'Productos del proveedor obtenidos correctamente',
            'success' => true
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProductosProveedorRelacion -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener productos del proveedor'));
    }
} 
public function __construct($method, $data) {
    $this->method = $method;
    
    // Manejar diferentes tipos de contenido
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        $this->data = Security::sanitizeInput($data);
    } elseif (strpos($contentType, 'multipart/form-data') !== false || 
              strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
        // Para FormData, usar $_POST combinado con $data
        $this->data = Security::sanitizeInput(array_merge($_POST, $data));
    } else {
        $this->data = Security::sanitizeInput($data);
    }
    
    // Establecer headers JSON para todas las respuestas
    header('Content-Type: application/json');
}
    /**
     * Registrar una nueva orden de compra (recepción)
     */
    public function registrarOrdenCompra() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar datos requeridos
        $camposRequeridos = ['id_proveedor', 'id_usuario', 'detalles'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        // Validar que detalles sea un array válido
        $detalles = $this->data['detalles'];
        if (is_string($detalles)) {
            $detalles = json_decode($detalles, true);
        }
        
        if (!is_array($detalles) || empty($detalles)) {
            echo json_encode(responseHTTP::status400("Debe agregar al menos un producto a la compra"));
            return;
        }
        
        // Validar cada detalle
        foreach ($detalles as $index => $detalle) {
            if (empty($detalle['id_proveedor_producto']) || empty($detalle['cantidad']) || empty($detalle['precio_unitario'])) {
                echo json_encode(responseHTTP::status400("El producto en la posición " . ($index + 1) . " tiene campos incompletos"));
                return;
            }
            
            if ($detalle['cantidad'] <= 0 || $detalle['precio_unitario'] <= 0) {
                echo json_encode(responseHTTP::status400("La cantidad y precio deben ser mayores a cero"));
                return;
            }
        }
        
        try {
            $result = comprasModel::registrarOrdenCompra($this->data);
            
            if ($result['success']) {
                echo json_encode([
                    'status' => 201,
                    'message' => $result['message'],
                    'data' => ['id_recepcion' => $result['id_recepcion']]
                ]);
            } else {
                echo json_encode([
                    'status' => 400,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            error_log("comprasController::registrarOrdenCompra -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al registrar la orden de compra'));
        }
    }
    
    /**
     * Finalizar una compra (actualizar inventario)
     */
    public function finalizarCompra() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar datos requeridos
        $camposRequeridos = ['id_recepcion', 'id_usuario'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        try {
            $result = comprasModel::finalizarCompra($this->data);
            
            if ($result['success']) {
                echo json_encode([
                    'status' => 200,
                    'message' => $result['message'],
                    'data' => ['id_compra' => $result['id_compra']]
                ]);
            } else {
                echo json_encode([
                    'status' => 400,
                    'message' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            error_log("comprasController::finalizarCompra -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al finalizar la compra'));
        }
    }
    
    
    /**
     * Obtener detalles de una recepción
     */
    public function obtenerDetalleRecepcion() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_recepcion'])) {
            echo json_encode(responseHTTP::status400("El ID de recepción es obligatorio"));
            return;
        }
        
        try {
            $detalles = comprasModel::obtenerDetalleRecepcion($this->data['id_recepcion']);
            
            echo json_encode([
                'status' => 200,
                'data' => $detalles,
                'message' => 'Detalles obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("comprasController::obtenerDetalleRecepcion -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener detalles de la recepción'));
        }
    }
    
    /**
     * Obtener todas las recepciones pendientes
     */
    public function obtenerRecepcionesPendientes() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $recepciones = comprasModel::obtenerRecepcionesPendientes();
            
            echo json_encode([
                'status' => 200,
                'data' => ['recepciones' => $recepciones],
                'message' => 'Recepciones obtenidas correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("comprasController::obtenerRecepcionesPendientes -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener recepciones pendientes'));
        }
    }
    
    /**
     * Obtener todas las recepciones finalizadas
     */
    public function obtenerRecepcionesFinalizadas() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $recepciones = comprasModel::obtenerRecepcionesFinalizadas();
            
            echo json_encode([
                'status' => 200,
                'data' => ['recepciones' => $recepciones],
                'message' => 'Recepciones finalizadas obtenidas correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("comprasController::obtenerRecepcionesFinalizadas -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener recepciones finalizadas'));
        }
    }
    
    /**
     * Obtener lista de proveedores activos
     */
    public function obtenerProveedores() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $proveedores = comprasModel::obtenerProveedores();
            
            echo json_encode([
                'status' => 200,
                'data' => $proveedores,
                'message' => 'Proveedores obtenidos correctamente',
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            error_log("comprasController::obtenerProveedores -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener proveedores'));
        }
    }
    
    /**
     * Endpoint de debug para testing
     */
    public function debug() {
        echo json_encode([
            'status' => 200,
            'message' => 'Debug endpoint funcionando',
            'data' => [
                'method' => $this->method,
                'params_received' => $this->data,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
 * Obtener recepciones con filtros
 */
public function obtenerRecepcionesFiltradas() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $recepciones = comprasModel::obtenerRecepcionesFiltradas($this->data);
        
        echo json_encode([
            'status' => 200,
            'data' => ['recepciones' => $recepciones],
            'message' => 'Recepciones obtenidas correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerRecepcionesFiltradas -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener recepciones'));
    }
}

/**
 * Obtener todas las compras registradas
 */
/**
 * Obtener todas las compras registradas
 */
public function listarCompras() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        // Obtener filtros
        $filtros = [
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null,
            'id_proveedor' => $this->data['id_proveedor'] ?? null,
            'estado_compra' => $this->data['estado_compra'] ?? null
        ];
        
        $compras = comprasModel::obtenerComprasFiltradas($filtros);
        
        echo json_encode([
            'status' => 200,
            'data' => $compras,
            'message' => 'Compras obtenidas correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::listarCompras -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener compras'));
    }
}



/**
 * Cancelar una orden de compra
 */
public function cancelarOrdenCompra() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['id_recepcion', 'id_usuario'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    try {
        $result = comprasModel::cancelarOrdenCompra($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::cancelarOrdenCompra -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al cancelar la orden de compra'));
    }
}


/**
 * Obtener todas las recepciones canceladas
 */
public function obtenerRecepcionesCanceladas() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null,
            'id_proveedor' => $this->data['id_proveedor'] ?? null
        ];
        
        $recepciones = comprasModel::obtenerRecepcionesCanceladas($filtros);
        
        echo json_encode([
            'status' => 200,
            'data' => ['recepciones' => $recepciones],
            'message' => 'Órdenes canceladas obtenidas correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerRecepcionesCanceladas -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener órdenes canceladas'));
    }
}


/**
 * Obtener recepciones finalizadas con filtros
 */
public function obtenerRecepcionesFinalizadasFiltradas() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null,
            'id_proveedor' => $this->data['id_proveedor'] ?? null
        ];
        
        $recepciones = comprasModel::obtenerRecepcionesFinalizadasFiltradas($filtros);
        
        echo json_encode([
            'status' => 200,
            'data' => ['recepciones' => $recepciones],
            'message' => 'Recepciones finalizadas obtenidas correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerRecepcionesFinalizadasFiltradas -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener recepciones finalizadas'));
    }
}


/**
 * Obtener toda la materia prima
 */
public function listarMateriaPrima() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $materiaPrima = comprasModel::obtenerMateriaPrima();
        
        echo json_encode([
            'status' => 200,
            'data' => $materiaPrima,
            'message' => 'Materia prima obtenida correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::listarMateriaPrima -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener la materia prima'));
    }
} 

/**
 * Ingresar materia prima al inventario
 */
public function ingresarInventario() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos (sin descripción)
    $camposRequeridos = ['id_materia_prima', 'cantidad', 'id_usuario'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    if ($this->data['cantidad'] <= 0) {
        echo json_encode(responseHTTP::status400("La cantidad debe ser mayor a cero"));
        return;
    }
    
    try {
        $result = comprasModel::ingresarInventario($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::ingresarInventario -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al ingresar al inventario'));
    } 
}

/**
 * Obtener proveedores con filtros
 */
public function listarProveedores() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'filtro_nombre' => $this->data['filtro_nombre'] ?? null,
            'filtro_estado' => $this->data['filtro_estado'] ?? null
        ];
        
        $proveedores = comprasModel::obtenerProveedoresFiltrados($filtros);
        
        echo json_encode([
            'status' => 200,
            'data' => $proveedores,
            'message' => 'Proveedores obtenidos correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::listarProveedores -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener proveedores'));
    }
}

/**
 * Cambiar estado de un proveedor
 */
public function cambiarEstadoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['id_proveedor', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar estado válido
    if (!in_array($this->data['estado'], ['ACTIVO', 'INACTIVO'])) {
        echo json_encode(responseHTTP::status400("Estado no válido"));
        return;
    }
    
    try {
        $result = comprasModel::cambiarEstadoProveedor($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::cambiarEstadoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al cambiar el estado del proveedor'));
    }
}

/**
 * Exportar proveedores a PDF
 */
public function exportarProveedoresPDF() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'filtro_nombre' => $this->data['filtro_nombre'] ?? null,
            'filtro_estado' => $this->data['filtro_estado'] ?? null
        ];
        
        $proveedores = comprasModel::obtenerProveedoresParaPDF($filtros);
        
        echo json_encode([
            'status' => 200,
            'data' => $proveedores,
            'message' => 'Datos para PDF obtenidos correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::exportarProveedoresPDF -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener datos para PDF'));
    }
}

/**
 * Obtener proveedor por ID
 */
public function obtenerProveedorPorId() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor'])) {
        echo json_encode(responseHTTP::status400("El ID del proveedor es obligatorio"));
        return;
    }
    
    try {
        $proveedor = comprasModel::obtenerProveedorPorId($this->data['id_proveedor']);
        
        if ($proveedor) {
            echo json_encode([
                'status' => 200,
                'data' => $proveedor,
                'message' => 'Proveedor obtenido correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => 'Proveedor no encontrado'
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProveedorPorId -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener el proveedor'));
    }
}

public function editarProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // ... (validaciones existentes)
    
    try {
        $result = comprasModel::editarProveedor($this->data);
        
        if ($result['success']) {
            // Actualizar relación de productos si se enviaron
            if (isset($this->data['productos']) && is_array($this->data['productos'])) {
                $usuario = $_SESSION['usuario']['username'] ?? 'SISTEMA';
                $resultProductos = comprasModel::actualizarProductosProveedor(
                    $this->data['id_proveedor'],
                    $this->data['productos'],
                    $usuario
                );
                
                if (!$resultProductos['success']) {
                    echo json_encode([
                        'status' => 400,
                        'message' => $resultProductos['message']
                    ]);
                    return;
                }
            }
            
            echo json_encode([
                'status' => 200,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::editarProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al editar el proveedor'));
    }
}

public function registrarProductoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['nombre_producto', 'id_proveedor', 'id_unidad_medida', 'precio_unitario'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar longitud del nombre
    if (strlen($this->data['nombre_producto']) < 3 || strlen($this->data['nombre_producto']) > 100) {
        echo json_encode(responseHTTP::status400("El nombre del producto debe tener entre 3 y 100 caracteres"));
        return;
    }
    
    // Validar formato del nombre (solo letras, números y espacios)
    if (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}$/', $this->data['nombre_producto'])) {
        echo json_encode(responseHTTP::status400("El nombre del producto solo puede contener letras, números y espacios"));
        return;
    }
    
    // Validar descripción si se proporciona
    if (!empty($this->data['descripcion']) && strlen($this->data['descripcion']) > 255) {
        echo json_encode(responseHTTP::status400("La descripción no puede exceder los 255 caracteres"));
        return;
    }
    
    // Validar precio unitario
    if (!is_numeric($this->data['precio_unitario']) || $this->data['precio_unitario'] <= 0) {
        echo json_encode(responseHTTP::status400("El precio unitario debe ser un número mayor a 0"));
        return;
    }
    
    // Validar IDs numéricos
    if (!is_numeric($this->data['id_proveedor']) || $this->data['id_proveedor'] <= 0) {
        echo json_encode(responseHTTP::status400("ID de proveedor inválido"));
        return;
    }
    
    if (!is_numeric($this->data['id_unidad_medida']) || $this->data['id_unidad_medida'] <= 0) {
        echo json_encode(responseHTTP::status400("ID de unidad de medida inválido"));
        return;
    }
    
    try {
        $result = comprasModel::registrarProductoProveedor($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::registrarProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al registrar el producto'));
    }
}

public function registrarProductoProveedorCompleto() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }

    // Log para debugging
    error_log("Datos recibidos en registrarProductoProveedorCompleto:");
    error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'No definido'));
    error_log("POST data: " . print_r($_POST, true));
    error_log("Raw input: " . file_get_contents('php://input'));

    // Manejar diferentes formatos de datos
    $inputData = $this->data;
    
    // Si viene como form-data, usar $_POST
    if (empty($inputData) || !isset($inputData['nombre_producto'])) {
        $inputData = $_POST;
    }

    // Validar datos requeridos
    $camposRequeridos = ['nombre_producto', 'id_unidad_medida', 'precio_unitario', 'minimo', 'maximo'];
    foreach ($camposRequeridos as $campo) {
        if (!isset($inputData[$campo]) || $inputData[$campo] === '') {
            error_log("Campo faltante: $campo");
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }

    // Validaciones específicas
    $nombre_producto = trim($inputData['nombre_producto']);
    if (strlen($nombre_producto) < 3 || strlen($nombre_producto) > 100) {
        echo json_encode(responseHTTP::status400("El nombre del producto debe tener entre 3 y 100 caracteres"));
        return;
    }

    // Validar formato del nombre
    if (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}$/', $nombre_producto)) {
        echo json_encode(responseHTTP::status400("El nombre del producto solo puede contener letras, números y espacios"));
        return;
    }

    // Validar precio
    if (!is_numeric($inputData['precio_unitario']) || $inputData['precio_unitario'] <= 0) {
        echo json_encode(responseHTTP::status400("El precio unitario debe ser un número mayor a 0"));
        return;
    }

    // Validar stock mínimo y máximo
    if (!is_numeric($inputData['minimo']) || $inputData['minimo'] < 0) {
        echo json_encode(responseHTTP::status400("El stock mínimo debe ser mayor o igual a 0"));
        return;
    }

    if (!is_numeric($inputData['maximo']) || $inputData['maximo'] <= 0) {
        echo json_encode(responseHTTP::status400("El stock máximo debe ser mayor a 0"));
        return;
    }

    if ($inputData['maximo'] <= $inputData['minimo']) {
        echo json_encode(responseHTTP::status400("El stock máximo debe ser mayor al stock mínimo"));
        return;
    }

    // Preparar datos para el modelo
    $datosParaModelo = [
        'nombre_producto' => $nombre_producto,
        'descripcion' => $inputData['descripcion'] ?? null,
        'id_proveedor' => $inputData['id_proveedor'] ?? 0, // Siempre 0 según el formulario
        'id_unidad_medida' => $inputData['id_unidad_medida'],
        'precio_unitario' => $inputData['precio_unitario'],
        'minimo' => $inputData['minimo'],
        'maximo' => $inputData['maximo'],
        'creado_por' => $_SESSION['usuario']['username'] ?? 'SISTEMA',
        'id_usuario' => $_SESSION['usuario']['id_usuario'] ?? 1
    ];

    try {
        $result = comprasModel::registrarProductoProveedorCompleto($datosParaModelo);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::registrarProductoProveedorCompleto -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al registrar el producto: ' . $e->getMessage()));
    }
}



public function obtenerProductoProveedorCompleto() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $id_producto_proveedor = $this->data['id'] ?? null;
    
    if (!$id_producto_proveedor || !is_numeric($id_producto_proveedor)) {
        echo json_encode(['status' => 400, 'message' => 'ID de producto inválido']);
        return;
    }
    
    try {
        $producto = comprasModel::obtenerProductoProveedorCompleto($id_producto_proveedor);
        
        if ($producto) {
            echo json_encode(['status' => 200, 'data' => $producto]);
        } else {
            echo json_encode(['status' => 404, 'message' => 'Producto no encontrado']);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProductoProveedorCompleto -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener el producto'));
    }
}

// Método para obtener unidades de medida
public function obtenerUnidadesMedida() {
    try {
        $result = comprasModel::obtenerUnidadesMedida();
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerUnidadesMedida -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener unidades de medida'));
    }
}

// Método para obtener proveedores activos
public function obtenerProveedoresActivos() {
    try {
        $result = comprasModel::obtenerProveedoresActivos();
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProveedoresActivos -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener proveedores'));
    }
}


// Agregar al controlador comprasController
public function obtenerProveedoresActivosRegistroProductos() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = comprasModel::obtenerProveedoresActivosRegistroProductos();
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProveedoresActivosRegistroProductos -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener proveedores activos'));
    }
}


// Agregar al controlador comprasController
public function listarProductosProveedores() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = comprasModel::listarProductosProveedores($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::listarProductosProveedores -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener productos de proveedores'));
    }
}

// En tu controlador (ComprasController.php)

public function obtenerProductoProveedorPorId() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $id_producto_proveedor = $this->data['id'] ?? null;
    
    if (!$id_producto_proveedor || !is_numeric($id_producto_proveedor)) {
        echo json_encode(['status' => 400, 'message' => 'ID de producto inválido']);
        return;
    }
    
    try {
        // Usar el método estático del modelo
        $producto = comprasModel::obtenerProductoProveedorPorId($id_producto_proveedor);
        
        if ($producto) {
            echo json_encode(['status' => 200, 'data' => $producto]);
        } else {
            echo json_encode(['status' => 404, 'message' => 'Producto no encontrado']);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProductoProveedorPorId -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener el producto'));
    }
}

public function obtenerUnidadesMedidaProductosProveedores() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $unidades = comprasModel::obtenerUnidadesMedidaProductosProveedores();
        
        // Asegurar que la respuesta tenga la estructura correcta
        if (is_array($unidades)) {
            echo json_encode([
                'status' => 200, 
                'data' => $unidades,
                'message' => 'Unidades obtenidas correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Error al obtener unidades de medida',
                'data' => []
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerUnidadesMedidaProductosProveedores -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener unidades de medida'));
    }
}

public function editarProductoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Sanitizar manualmente evitando el problema del null
    $input = $this->data;
    foreach ($input as $key => &$value) {
        if ($value === null) {
            $value = '';
        } else {
            $value = Security::sanitizeInput($value);
        }
    }
    
    // Validar datos requeridos
    $required_fields = ['id_proveedor_producto', 'nombre_producto', 'id_unidad_medida', 'precio_unitario', 'estado', 'minimo', 'maximo'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            echo json_encode(['status' => 400, 'message' => "El campo $field es requerido"]);
            return;
        }
    }
    
    // Validaciones específicas
    if (!is_numeric($input['id_proveedor_producto']) || $input['id_proveedor_producto'] <= 0) {
        echo json_encode(['status' => 400, 'message' => 'ID de producto inválido']);
        return;
    }
    
    if (!is_numeric($input['id_unidad_medida']) || $input['id_unidad_medida'] <= 0) {
        echo json_encode(['status' => 400, 'message' => 'ID de unidad de medida inválido']);
        return;
    }
    
    if (!is_numeric($input['precio_unitario']) || $input['precio_unitario'] <= 0) {
        echo json_encode(['status' => 400, 'message' => 'El precio unitario debe ser mayor a 0']);
        return;
    }
    
    if (!is_numeric($input['minimo']) || $input['minimo'] < 0) {
        echo json_encode(['status' => 400, 'message' => 'El stock mínimo debe ser mayor o igual a 0']);
        return;
    }
    
    if (!is_numeric($input['maximo']) || $input['maximo'] <= 0) {
        echo json_encode(['status' => 400, 'message' => 'El stock máximo debe ser mayor a 0']);
        return;
    }
    
    if ($input['maximo'] <= $input['minimo']) {
        echo json_encode(['status' => 400, 'message' => 'El stock máximo debe ser mayor al stock mínimo']);
        return;
    }
    
    if (!in_array($input['estado'], ['ACTIVO', 'INACTIVO'])) {
        echo json_encode(['status' => 400, 'message' => 'Estado inválido']);
        return;
    }
    
    // Asignar valores por defecto y usuario que modifica
    $input['descripcion'] = $input['descripcion'] ?? '';
    $input['modificado_por'] = $_SESSION['usuario']['username'] ?? 'SISTEMA';
    $input['id_usuario'] = $_SESSION['usuario']['id_usuario'] ?? 1;
    
    // Log para debugging
    error_log("Datos enviados a editarProductoProveedor: " . print_r($input, true));
    
    try {
        $result = comprasModel::editarProductoProveedor($input);
        echo json_encode($result);
    } catch (\Exception $e) {
        error_log("comprasController::editarProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al editar el producto: ' . $e->getMessage()));
    }
}


public function cambiarEstadoProductoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['id_proveedor_producto', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar estado válido
    if (!in_array($this->data['estado'], ['ACTIVO', 'INACTIVO'])) {
        echo json_encode(responseHTTP::status400("Estado no válido"));
        return;
    }
    
    try {
        $result = comprasModel::cambiarEstadoProductoProveedor($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::cambiarEstadoProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al cambiar el estado del producto'));
    }
}

// Agregar al controlador existente
public function obtenerMateriaPrimaPorId() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_materia_prima'])) {
        echo json_encode(responseHTTP::status400("El ID de materia prima es requerido"));
        return;
    }
    
    try {
        $result = comprasModel::obtenerMateriaPrimaPorId($this->data['id_materia_prima']);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::obtenerMateriaPrimaPorId -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener la materia prima'));
    }
}

public function editarMateriaPrima() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos (eliminados minimo y maximo)
    $camposRequeridos = ['id_materia_prima', 'nombre', 'id_unidad_medida', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar longitud del nombre
    if (strlen($this->data['nombre']) < 3 || strlen($this->data['nombre']) > 100) {
        echo json_encode(responseHTTP::status400("El nombre debe tener entre 3 y 100 caracteres"));
        return;
    }
    
    // Validar formato del nombre
    if (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}$/', $this->data['nombre'])) {
        echo json_encode(responseHTTP::status400("El nombre solo puede contener letras, números y espacios"));
        return;
    }
    
    // Validar descripción si se proporciona
    if (!empty($this->data['descripcion']) && strlen($this->data['descripcion']) > 255) {
        echo json_encode(responseHTTP::status400("La descripción no puede exceder los 255 caracteres"));
        return;
    }
    
    // Validar estado
    if (!in_array($this->data['estado'], ['ACTIVO', 'INACTIVO'])) {
        echo json_encode(responseHTTP::status400("Estado no válido"));
        return;
    }
    
    try {
        $result = comprasModel::editarMateriaPrima($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::editarMateriaPrima -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al editar la materia prima'));
    }
}

public function guardarRelacionProductoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor'])) {
        echo json_encode(responseHTTP::status400("El ID del proveedor es obligatorio"));
        return;
    }
    
    try {
        $result = comprasModel::guardarRelacionProductoProveedor($this->data);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::guardarRelacionProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al guardar la relación'));
    }
}

/**
 * Obtener relaciones existentes
 */
public function obtenerRelacionesProductoProveedor() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $relaciones = comprasModel::obtenerRelacionesProductoProveedor();
        
        echo json_encode([
            'status' => 200,
            'data' => $relaciones,
            'message' => 'Relaciones obtenidas correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerRelacionesProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener relaciones'));
    }
}

/**
 * Eliminar relación producto-proveedor
 */
public function eliminarRelacionProductoProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor'])) {
        echo json_encode(responseHTTP::status400("El ID del proveedor es obligatorio"));
        return;
    }
    
    try {
        $result = comprasModel::eliminarRelacionProductoProveedor($this->data['id_proveedor']);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::eliminarRelacionProductoProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al eliminar la relación'));
    }
}

public function registrarProveedor() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['nombre'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar longitud del nombre
    if (strlen($this->data['nombre']) < 3 || strlen($this->data['nombre']) > 100) {
        echo json_encode(responseHTTP::status400("El nombre del proveedor debe tener entre 3 y 100 caracteres"));
        return;
    }
    
    // Validar formato del nombre (caracteres empresariales)
    if (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s\.\,\/\#\-\&]{3,100}$/', $this->data['nombre'])) {
        echo json_encode(responseHTTP::status400("El nombre del proveedor contiene caracteres no permitidos"));
        return;
    }
    
    // Validar espacios múltiples en nombre
    if (preg_match('/\s{2,}/', $this->data['nombre'])) {
        echo json_encode(responseHTTP::status400("No se permiten espacios consecutivos en el nombre"));
        return;
    }
    
    // Validar contacto si se proporciona
    if (!empty($this->data['contacto'])) {
        if (strlen($this->data['contacto']) > 100) {
            echo json_encode(responseHTTP::status400("El contacto no puede exceder los 100 caracteres"));
            return;
        }
        
        if (!preg_match('/^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{0,100}$/', $this->data['contacto'])) {
            echo json_encode(responseHTTP::status400("El contacto solo puede contener letras y espacios"));
            return;
        }
        
        if (preg_match('/\s{2,}/', $this->data['contacto'])) {
            echo json_encode(responseHTTP::status400("No se permiten espacios consecutivos en el contacto"));
            return;
        }
    }
    
    // Validar teléfono si se proporciona
    if (!empty($this->data['telefono'])) {
        if (strlen($this->data['telefono']) > 20) {
            echo json_encode(responseHTTP::status400("El teléfono no puede exceder los 20 caracteres"));
            return;
        }
        
        if (!preg_match('/^[0-9\s\+\-\(\)]{8,20}$/', $this->data['telefono'])) {
            echo json_encode(responseHTTP::status400("El teléfono contiene caracteres no permitidos"));
            return;
        }
    }
    
    // Validar correo si se proporciona
    if (!empty($this->data['correo'])) {
        if (strlen($this->data['correo']) > 50) {
            echo json_encode(responseHTTP::status400("El correo no puede exceder los 50 caracteres"));
            return;
        }
        
        if (!preg_match('/^[a-z0-9._%+-]+@(gmail|hotmail)\.com$/', $this->data['correo'])) {
            echo json_encode(responseHTTP::status400("Solo se permiten correos @gmail.com o @hotmail.com en minúsculas"));
            return;
        }
    }
    
    // Validar dirección si se proporciona
    if (!empty($this->data['direccion']) && strlen($this->data['direccion']) > 255) {
        echo json_encode(responseHTTP::status400("La dirección no puede exceder los 255 caracteres"));
        return;
    }
    
    // Asignar usuario que crea
    $this->data['creado_por'] = $_SESSION['usuario']['username'] ?? 'SISTEMA';
    
    try {
        $result = comprasModel::registrarProveedor($this->data);
        
        if ($result['success']) {
            // Registrar relación de productos si se enviaron
            if (isset($this->data['productos']) && is_array($this->data['productos']) && !empty($this->data['productos'])) {
                $usuario = $_SESSION['usuario']['username'] ?? 'SISTEMA';
                $resultProductos = comprasModel::registrarProductosProveedor(
                    $result['data']['ID_PROVEEDOR'], // ID del proveedor recién creado
                    $this->data['productos'],
                    $usuario
                );
                
                if (!$resultProductos['success']) {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'Proveedor registrado pero error al asignar productos: ' . $resultProductos['message']
                    ]);
                    return;
                }
            }
            
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::registrarProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al registrar el proveedor'));
    }
}

public function validarProveedor() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['campo']) || empty($this->data['valor'])) {
        echo json_encode(responseHTTP::status400("Campo y valor son requeridos"));
        return;
    }
    
    // Campos permitidos para validación
    $camposPermitidos = ['nombre', 'contacto', 'correo', 'telefono'];
    if (!in_array($this->data['campo'], $camposPermitidos)) {
        echo json_encode(responseHTTP::status400("Campo no válido para validación"));
        return;
    }
    
    try {
        $result = comprasModel::validarProveedorUnico($this->data['campo'], $this->data['valor']);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'disponible' => $result['disponible']
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'disponible' => false
            ]);
        }
    } catch (\Exception $e) {
        error_log("comprasController::validarProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al validar proveedor'));
    }
}
public function obtenerProductosActivos() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $productos = comprasModel::obtenerProductosActivos();
        
        echo json_encode([
            'status' => 200,
            'data' => $productos,
            'message' => 'Productos obtenidos correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProductosActivos -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener productos'));
    }
}

/**
 * Obtener productos de un proveedor específico
 */
public function obtenerProductosProveedor() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor'])) {
        echo json_encode(responseHTTP::status400("El ID del proveedor es obligatorio"));
        return;
    }
    
    try {
        $productos = comprasModel::obtenerProductosPorProveedor($this->data['id_proveedor']);
        
        echo json_encode([
            'status' => 200,
            'data' => $productos,
            'message' => 'Productos del proveedor obtenidos correctamente'
        ]);
        
    } catch (\Exception $e) {
        error_log("comprasController::obtenerProductosProveedor -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener productos del proveedor'));
    }
}
/**
 * Anular una compra y revertir inventario
 */
public function anularCompra() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Log para debugging
    error_log("Datos recibidos en anularCompra: " . print_r($this->data, true));
    
    // Validar datos requeridos
    $camposRequeridos = ['id_compra', 'motivo_anulacion', 'id_usuario'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            error_log("Campo faltante: $campo");
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar longitud del motivo
    if (strlen($this->data['motivo_anulacion']) < 5) {
        echo json_encode(responseHTTP::status400("El motivo de anulación debe tener al menos 5 caracteres"));
        return;
    }
    
    try {
        error_log("Llamando a comprasModel::anularCompra con datos: " . print_r($this->data, true));
        
        $result = comprasModel::anularCompra($this->data);
        
        error_log("Resultado de anularCompra: " . print_r($result, true));
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("Exception en comprasController::anularCompra: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode(responseHTTP::status500('Error al anular la compra: ' . $e->getMessage()));
    }
}
}