<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\inventarioModel;
use PDO;

class inventarioController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }
    
    // Listar inventario completo
    public function listarInventario() {
        try {
            $inventario = inventarioModel::obtenerInventarioCompleto();
            
            if (empty($inventario)) {
                echo json_encode([
                    'status' => 200,
                    'data' => ['inventario' => []],
                    'message' => 'No hay registros en el inventario'
                ]);
                return;
            }
            
            echo json_encode([
                'status' => 200,
                'data' => ['inventario' => $inventario],
                'message' => 'Inventario obtenido correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::listarInventario -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener el inventario'));
        }
    }
    
    // Obtener item específico del inventario
    public function obtenerItemInventario() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_inventario'])) {
            echo json_encode(responseHTTP::status400('ID de inventario requerido'));
            return;
        }
        
        $item = inventarioModel::obtenerItemInventario($this->data['id_inventario']);
        
        if ($item) {
            echo json_encode(responseHTTP::status200('Item de inventario obtenido', ['item' => $item]));
        } else {
            echo json_encode(responseHTTP::status404('Item de inventario no encontrado'));
        }
    }
    
    // Actualizar inventario
public function actualizarInventario() {
    error_log("🔍 actualizarInventario llamado - Method: " . $this->method);
    
    if ($this->method != 'post') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Log de datos recibidos
    error_log("📥 Datos recibidos: " . print_r($this->data, true));
    
    // Validar datos requeridos
    $camposRequeridos = ['id_materia_prima', 'cantidad', 'tipo_movimiento', 'descripcion'];
    $camposFaltantes = [];
    
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            $camposFaltantes[] = $campo;
        }
    }
    
    if (!empty($camposFaltantes)) {
        error_log("❌ Campos faltantes: " . implode(', ', $camposFaltantes));
        echo json_encode(responseHTTP::status400("Campos obligatorios faltantes: " . implode(', ', $camposFaltantes)));
        return;
    }
    
    // Validar tipo de movimiento
    $tiposPermitidos = ['ENTRADA', 'SALIDA', 'AJUSTE'];
    $tipoMovimiento = strtoupper($this->data['tipo_movimiento']);
    
    if (!in_array($tipoMovimiento, $tiposPermitidos)) {
        error_log("❌ Tipo movimiento inválido: " . $tipoMovimiento);
        echo json_encode(responseHTTP::status400('Tipo de movimiento no válido. Use: ENTRADA, SALIDA o AJUSTE'));
        return;
    }
    
    // Validar cantidad
    if (!is_numeric($this->data['cantidad']) || $this->data['cantidad'] <= 0) {
        error_log("❌ Cantidad inválida: " . $this->data['cantidad']);
        echo json_encode(responseHTTP::status400('La cantidad debe ser un número positivo mayor a 0'));
        return;
    }
    
    // Asegurar que los datos tengan el formato correcto
    $datosLimpios = [
        'id_materia_prima' => (int)$this->data['id_materia_prima'],
        'cantidad' => (float)$this->data['cantidad'],
        'tipo_movimiento' => $tipoMovimiento,
        'descripcion' => trim($this->data['descripcion']),
        'id_usuario' => $this->data['id_usuario'] ?? 1,
        'actualizado_por' => $this->data['actualizado_por'] ?? 'SISTEMA'
    ];
    
    error_log("🧹 Datos limpios para modelo: " . print_r($datosLimpios, true));
    
    // Actualizar inventario
    $result = inventarioModel::actualizarInventario($datosLimpios);
    
    error_log("📤 Resultado del modelo: " . print_r($result, true));
    
    if ($result['success']) {
        echo json_encode(responseHTTP::status200($result['message'], [
            'nuevo_stock' => $result['nuevo_stock'] ?? null
        ]));
    } else {
        echo json_encode(responseHTTP::status400($result['message']));
    }
}
    
    // Obtener historial de movimientos
    public function obtenerHistorial() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $filtros = [
            'id_materia_prima' => $this->data['id_materia_prima'] ?? null,
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null
        ];
        
        try {
            $historial = inventarioModel::obtenerHistorialInventario($filtros);
            
            echo json_encode([
                'status' => 200,
                'data' => ['historial' => $historial],
                'message' => 'Historial obtenido correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::obtenerHistorial -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener el historial'));
        }
    }
    
    // Exportar inventario a PDF
    public function exportarInventarioPDF() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $inventario = inventarioModel::exportarInventarioPDF();
            
            if (empty($inventario)) {
                echo json_encode(responseHTTP::status404('No hay datos para exportar'));
                return;
            }
            
            echo json_encode([
                'status' => 200,
                'message' => 'Datos de inventario obtenidos para exportación',
                'data' => ['inventario' => $inventario]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en exportarInventarioPDF: " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al exportar inventario'));
        }
    }
    
    // Obtener alertas de inventario
    public function obtenerAlertas() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $alertas = inventarioModel::obtenerAlertasInventario();
            
            echo json_encode([
                'status' => 200,
                'data' => ['alertas' => $alertas],
                'message' => 'Alertas obtenidas correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::obtenerAlertas -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener las alertas'));
        }
    }


    /**
 * Listar inventario de productos
 */
public function listarInventarioProductos() {
    error_log("🎯 INICIANDO listarInventarioProductos - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'filtro_nombre' => $this->data['filtro_nombre'] ?? null,
            'filtro_estado' => $this->data['filtro_estado'] ?? null
        ];
        
        error_log("🔍 Filtros para inventario productos: " . print_r($filtros, true));
        
        // CAMBIAR: inventarioProductoModel -> inventarioModel
        $result = inventarioModel::obtenerInventarioProductos($filtros);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'data' => $result['data']
            ];
            error_log("✅ Inventario de productos obtenido exitosamente - Total: " . count($result['data']));
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'message' => $result['message']
            ];
            error_log("❌ Error al obtener inventario productos: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador listarInventarioProductos: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener inventario de productos: ' . $e->getMessage()));
    }
}

/**
 * Obtener producto específico del inventario
 */
public function obtenerProductoInventario() {
    error_log("🎯 INICIANDO obtenerProductoInventario - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        if (empty($this->data['id_producto'])) {
            echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
            return;
        }
        
        // CAMBIAR: inventarioProductoModel -> inventarioModel
        $result = inventarioModel::obtenerProductoInventario($this->data['id_producto']);
        
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
        error_log("💥 Error en controlador obtenerProductoInventario: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener producto del inventario: ' . $e->getMessage()));
    }
}

/**
 * Ajustar inventario de producto
 */
public function ajustarInventarioProducto() {
    error_log("🎯 INICIANDO ajustarInventarioProducto - Method: " . $this->method);
    
    if ($this->method != 'post') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Iniciar sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Obtener datos del usuario desde la sesión
    $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? null;
    $actualizado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Actualizado por: " . $actualizado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? $this->data['cantidad'] ?? null;
    $tipo_movimiento = $_POST['tipo_movimiento'] ?? $this->data['tipo_movimiento'] ?? null;
    $descripcion = $_POST['descripcion'] ?? $this->data['descripcion'] ?? null;
    
    error_log("🔍 Datos obtenidos - id_producto: " . $id_producto . ", tipo_movimiento: " . $tipo_movimiento . ", cantidad: " . $cantidad);
    
    // Validar campos requeridos
    $required_fields = ['id_producto', 'cantidad', 'tipo_movimiento', 'descripcion'];
    foreach ($required_fields as $field) {
        if (empty($$field)) {
            error_log("❌ Campo requerido faltante: " . $field);
            echo json_encode(responseHTTP::status400("El campo " . $field . " es obligatorio"));
            return;
        }
    }
    
    if (empty($id_usuario)) {
        error_log("❌ No hay usuario en sesión");
        echo json_encode(responseHTTP::status400("Usuario no autenticado"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_producto' => (int)$id_producto,
            'cantidad' => (float)$cantidad,
            'tipo_movimiento' => $tipo_movimiento,
            'descripcion' => $descripcion,
            'id_usuario' => (int)$id_usuario,
            'actualizado_por' => $actualizado_por
        ];
        
        error_log("🔍 Datos para ajustar inventario: " . print_r($datos, true));
        
        // Llamar al modelo
        // CAMBIAR: inventarioProductoModel -> inventarioModel
        $result = inventarioModel::ajustarInventarioProducto($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Inventario de producto ajustado exitosamente");
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al ajustar inventario producto: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador ajustarInventarioProducto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al ajustar inventario de producto: ' . $e->getMessage()));
    }
}

/**
 * Obtener historial de movimientos de producto
 */
public function obtenerHistorialProducto() {
    error_log("🎯 INICIANDO obtenerHistorialProducto - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        if (empty($this->data['id_producto'])) {
            echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
            return;
        }
        
        $filtros = [
            'id_producto' => $this->data['id_producto'],
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null
        ];
        
        // CAMBIAR: inventarioProductoModel -> inventarioModel
        $result = inventarioModel::obtenerHistorialProducto($filtros);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador obtenerHistorialProducto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener historial de producto: ' . $e->getMessage()));
    }
}

/**
 * Exportar inventario de productos a PDF
 */
/**
 * Exportar inventario de productos a PDF
 */
public function exportarInventarioProductosPDF() {  // CAMBIÉ EL NOMBRE
    error_log("🎯 INICIANDO exportarInventarioProductosPDF - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'filtro_nombre' => $this->data['filtro_nombre'] ?? null,
            'filtro_estado' => $this->data['filtro_estado'] ?? null
        ];
        
        $result = inventarioModel::obtenerInventarioProductos($filtros);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'data' => [
                    'inventario' => $result['data']
                ]
            ];
            error_log("✅ Datos para PDF obtenidos exitosamente - Total: " . count($result['data']));
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'message' => $result['message']
            ];
            error_log("❌ Error al obtener datos para PDF: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador exportarInventarioProductosPDF: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al exportar inventario a PDF: ' . $e->getMessage()));
    }
}

/**
 * Editar inventario de producto
 */
public function editarInventarioProducto() {
    error_log("🎯 INICIANDO editarInventarioProducto - Method: " . $this->method);
    
    if ($this->method != 'post') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Iniciar sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Obtener datos del usuario desde la sesión
    $actualizado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - Actualizado por: " . $actualizado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_inventario_producto = $_POST['id_inventario_producto'] ?? $this->data['id_inventario_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? $this->data['cantidad'] ?? null;
    $precio = $_POST['precio'] ?? $this->data['precio'] ?? null;
    
    error_log("🔍 Datos obtenidos - id_inventario: " . $id_inventario_producto . ", cantidad: " . $cantidad . ", precio: " . $precio);
    
    // Validar campos requeridos
    $required_fields = ['id_inventario_producto', 'cantidad', 'precio'];
    foreach ($required_fields as $field) {
        if (empty($$field)) {
            error_log("❌ Campo requerido faltante: " . $field);
            echo json_encode(responseHTTP::status400("El campo " . $field . " es obligatorio"));
            return;
        }
    }
    
    // Validar que la cantidad sea positiva
    if ($cantidad < 0) {
        error_log("❌ Cantidad inválida: " . $cantidad);
        echo json_encode(responseHTTP::status400("La cantidad no puede ser negativa"));
        return;
    }
    
    // Validar que el precio sea positivo
    if ($precio <= 0) {
        error_log("❌ Precio inválido: " . $precio);
        echo json_encode(responseHTTP::status400("El precio debe ser mayor a 0"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_inventario_producto' => (int)$id_inventario_producto,
            'cantidad' => (float)$cantidad,
            'precio' => (float)$precio,
            'actualizado_por' => $actualizado_por
        ];
        
        error_log("🔍 Datos para editar inventario: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = inventarioModel::editarInventarioProducto($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Inventario de producto editado exitosamente");
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al editar inventario producto: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador editarInventarioProducto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al editar inventario de producto: ' . $e->getMessage()));
    }
}
}
?>