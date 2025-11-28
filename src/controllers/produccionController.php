<?php


namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\produccionModel;
use PDO;

class produccionController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
        header('Content-Type: application/json');
    }


    /**
     * Verificar stock para producción
     */
    public function verificarStock() {
        error_log("🎯 INICIANDO verificarStock - Method: " . $this->method);
        
        if ($this->method != 'post') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Usar $_POST directamente para evitar problemas
        if (empty($_POST['id_producto'])) {
            error_log("❌ id_producto no encontrado en _POST");
            echo json_encode(responseHTTP::status400("El campo id_producto es obligatorio"));
            return;
        }
        
        if (empty($_POST['cantidad_planificada'])) {
            error_log("❌ cantidad_planificada no encontrado en _POST");
            echo json_encode(responseHTTP::status400("El campo cantidad_planificada es obligatorio"));
            return;
        }
        
        try {
            $id_producto = (int)$_POST['id_producto'];
            $cantidad = (float)$_POST['cantidad_planificada'];
            
            error_log("🔍 Procesando con - ID Producto: " . $id_producto . ", Cantidad: " . $cantidad);
            
            $result = produccionModel::verificarStockProduccion($id_producto, $cantidad);
            
            error_log("📦 Respuesta del modelo: " . print_r($result, true));
            
            if ($result['success']) {
                $response = [
                    'status' => 200,
                    'stock_suficiente' => $result['stock_suficiente'],
                    'message' => $result['mensaje']
                ];
                error_log("✅ Enviando respuesta exitosa");
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'message' => $result['message']
                ];
                error_log("❌ Enviando respuesta de error: " . $result['message']);
                echo json_encode($response);
            }
        } catch (\Exception $e) {
            error_log("💥 Error en controlador: " . $e->getMessage());
            error_log("💥 Stack trace: " . $e->getTraceAsString());
            echo json_encode(responseHTTP::status500('Error al verificar stock: ' . $e->getMessage()));
        }
    }
    
    
 /**
     * Crear orden de producción (CON SESIÓN)
     */
    /**
     * Crear orden de producción (USANDO $_POST DIRECTAMENTE)
     */
    public function crearOrdenProduccion() {
        error_log("🎯 INICIANDO crearOrdenProduccion - Method: " . $this->method);
        
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
        $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
        
        error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Creado por: " . $creado_por);
        
        // DEBUG: Ver qué datos están llegando
        error_log("📦 _POST data: " . print_r($_POST, true));
        error_log("📦 this->data: " . print_r($this->data, true));
        
        // USAR $_POST DIRECTAMENTE para obtener los datos del formulario
        $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
        $cantidad_planificada = $_POST['cantidad_planificada'] ?? $this->data['cantidad_planificada'] ?? null;
        $observacion = $_POST['observacion'] ?? $this->data['observacion'] ?? '';
        
        error_log("🔍 Datos obtenidos - id_producto: " . $id_producto . ", cantidad: " . $cantidad_planificada);
        
        // Validar campos requeridos del formulario
        if (empty($id_producto)) {
            error_log("❌ Campo requerido faltante: id_producto");
            echo json_encode(responseHTTP::status400("El campo producto es obligatorio"));
            return;
        }
        
        if (empty($cantidad_planificada)) {
            error_log("❌ Campo requerido faltante: cantidad_planificada");
            echo json_encode(responseHTTP::status400("El campo cantidad es obligatorio"));
            return;
        }
        
        try {
            // Preparar datos para el modelo
            $datos = [
                'id_usuario' => (int)$id_usuario,
                'id_producto' => (int)$id_producto,
                'cantidad_planificada' => (float)$cantidad_planificada,
                'observacion' => $observacion,
                'creado_por' => $creado_por
            ];
            
            error_log("🔍 Datos para crear orden: " . print_r($datos, true));
            
            // Llamar al modelo (este SÍ es estático)
            $result = produccionModel::crearOrdenProduccion($datos);
            
            if ($result['success']) {
                $response = [
                    'status' => 201,
                    'success' => true,
                    'id_produccion' => $result['id_produccion'],
                    'message' => $result['message']
                ];
                error_log("✅ Orden creada exitosamente - ID: " . $result['id_produccion']);
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'success' => false,
                    'message' => $result['message']
                ];
                error_log("❌ Error al crear orden: " . $result['message']);
                echo json_encode($response);
            }
        } catch (\Exception $e) {
            error_log("💥 Error en controlador crearOrdenProduccion: " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al crear orden de producción: ' . $e->getMessage()));
        }
    }


    /**
 * Obtener órdenes de producción con filtros
 */
public function obtenerOrdenesProduccion() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [];
        
        // Aplicar filtros si existen
        if (!empty($this->data['estado'])) {
            $filtros['estado'] = $this->data['estado'];
        }
        
        if (!empty($this->data['fecha_desde'])) {
            $filtros['fecha_desde'] = $this->data['fecha_desde'];
        }
        
        if (!empty($this->data['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $this->data['fecha_hasta'];
        }
        
        if (!empty($this->data['id_producto'])) {
            $filtros['id_producto'] = $this->data['id_producto'];
        }
        
        $result = produccionModel::obtenerOrdenesProduccion($filtros);
        
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
        error_log("produccionController::obtenerOrdenesProduccion -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener órdenes de producción'));
    }
}
    /**
     * Obtener receta de un producto
     */
    public function obtenerReceta() {
        error_log("🎯 INICIANDO obtenerReceta - Method: " . $this->method);
        error_log("📦 Data recibida: " . print_r($this->data, true));
        
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Usar $this->data para GET
        if (empty($this->data['id_producto'])) {
            echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
            return;
        }
        
        try {
            $result = produccionModel::obtenerReceta($this->data['id_producto']);
            
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
            error_log("produccionController::obtenerReceta -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener receta'));
        }
    }

    /**
     * Obtener productos para producción
     */
    public function obtenerProductosProduccion() {
        error_log("🎯 INICIANDO obtenerProductosProduccion - Method: " . $this->method);
        
        if ($this->method != 'get') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            error_log("🔍 Llamando al modelo obtenerProductosProduccion");
            
            $result = produccionModel::obtenerProductosProduccion();
            
            error_log("📦 Respuesta del modelo: " . print_r($result, true));
            
            if ($result['success']) {
                $response = [
                    'status' => 200,
                    'data' => $result['data']
                ];
                error_log("✅ Enviando respuesta exitosa");
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'message' => $result['message']
                ];
                error_log("❌ Enviando respuesta de error: " . $result['message']);
                echo json_encode($response);
            }
        } catch (\Exception $e) {
            error_log("💥 Error en controlador: " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener productos'));
        }
    }

    /**
     * Obtener receta existente
     */
    public function obtenerRecetaExistente() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_producto'])) {
            echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
            return;
        }
        
        try {
            $result = produccionModel::obtenerRecetaExistente($this->data['id_producto']);
            
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
            error_log("produccionController::obtenerRecetaExistente -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener receta existente'));
        }
    }


    /**
 * Iniciar producción (FASE 3)
 */
public function iniciarProduccion() {
    error_log("🎯 INICIANDO iniciarProduccion - Method: " . $this->method);
    
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
    $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Modificado por: " . $modificado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_produccion = $_POST['id_produccion'] ?? $this->data['id_produccion'] ?? null;
    
    error_log("🔍 Datos obtenidos - id_produccion: " . $id_produccion);
    
    // Validar campos requeridos
    if (empty($id_produccion)) {
        error_log("❌ Campo requerido faltante: id_produccion");
        echo json_encode(responseHTTP::status400("El ID de producción es obligatorio"));
        return;
    }
    
    if (empty($id_usuario)) {
        error_log("❌ No hay usuario en sesión");
        echo json_encode(responseHTTP::status400("Usuario no autenticado"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_produccion' => (int)$id_produccion,
            'id_usuario' => (int)$id_usuario,
            'modificado_por' => $modificado_por
        ];
        
        error_log("🔍 Datos para iniciar producción: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::iniciarProduccion($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Producción iniciada exitosamente - ID: " . $id_produccion);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al iniciar producción: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador iniciarProduccion: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al iniciar producción: ' . $e->getMessage()));
    }
}

/**
 * Obtener detalle completo de una producción
 */
public function obtenerDetalleProduccion() {
    error_log("🎯 INICIANDO obtenerDetalleProduccion - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Usar $this->data para GET
    if (empty($this->data['id_produccion'])) {
        echo json_encode(responseHTTP::status400("El ID de producción es obligatorio"));
        return;
    }
    
    try {
        $id_produccion = (int)$this->data['id_produccion'];
        error_log("🔍 Obteniendo detalle para producción ID: " . $id_produccion);
        
        $result = produccionModel::obtenerDetalleProduccion($id_produccion);
        
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
        error_log("💥 Error en controlador obtenerDetalleProduccion: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener detalle de producción'));
    }
}


/**
 * Finalizar producción (FASE 4)
 */
/**
 * Finalizar producción (FASE 4)
 */
public function finalizarProduccion() {
    error_log("🎯 INICIANDO finalizarProduccion - Method: " . $this->method);
    
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
    $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Modificado por: " . $modificado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_produccion = $_POST['id_produccion'] ?? $this->data['id_produccion'] ?? null;
    $cantidad_buena = $_POST['cantidad_buena'] ?? $this->data['cantidad_buena'] ?? null;
    $observaciones = $_POST['observaciones'] ?? $this->data['observaciones'] ?? '';
    
    // Procesar pérdidas desde JSON
    $perdidas = [];
    if (!empty($_POST['perdidas'])) {
        if (is_string($_POST['perdidas'])) {
            $perdidas = json_decode($_POST['perdidas'], true);
        } else {
            $perdidas = $_POST['perdidas'];
        }
    }
    
    error_log("🔍 Datos obtenidos - id_produccion: " . $id_produccion . ", cantidad_buena: " . $cantidad_buena);
    error_log("🔍 Pérdidas recibidas: " . print_r($perdidas, true));
    
    // Validar campos requeridos
    if (empty($id_produccion)) {
        error_log("❌ Campo requerido faltante: id_produccion");
        echo json_encode(responseHTTP::status400("El ID de producción es obligatorio"));
        return;
    }
    
    if (empty($cantidad_buena)) {
        error_log("❌ Campo requerido faltante: cantidad_buena");
        echo json_encode(responseHTTP::status400("La cantidad buena es obligatoria"));
        return;
    }
    
    if (empty($id_usuario)) {
        error_log("❌ No hay usuario en sesión");
        echo json_encode(responseHTTP::status400("Usuario no autenticado"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_produccion' => (int)$id_produccion,
            'id_usuario' => (int)$id_usuario,
            'cantidad_buena' => (float)$cantidad_buena,
            'observaciones' => $observaciones,
            'modificado_por' => $modificado_por
        ];
        
        // Agregar pérdidas si existen
        if (!empty($perdidas) && is_array($perdidas)) {
            $datos['perdidas'] = $perdidas;
        }
        
        error_log("🔍 Datos para finalizar producción: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::finalizarProduccion($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Producción finalizada exitosamente - ID: " . $id_produccion);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al finalizar producción: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador finalizarProduccion: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al finalizar producción: ' . $e->getMessage()));
    }
}

/**
 * Crear receta de producto
 */
public function crearReceta() {
    error_log("🎯 INICIANDO crearReceta - Method: " . $this->method);
    
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
    $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - Creado por: " . $creado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $detalles = $_POST['detalles'] ?? $this->data['detalles'] ?? [];
    $sobreescribir = isset($_POST['sobreescribir']) ? filter_var($_POST['sobreescribir'], FILTER_VALIDATE_BOOLEAN) : false;
    
    // Procesar detalles si vienen como JSON string
    if (is_string($detalles)) {
        $detalles = json_decode($detalles, true);
    }
    
    error_log("🔍 Datos obtenidos - id_producto: " . $id_producto . ", sobreescribir: " . $sobreescribir);
    
    // Validar campos requeridos
    if (empty($id_producto)) {
        error_log("❌ Campo requerido faltante: id_producto");
        echo json_encode(responseHTTP::status400("El campo producto es obligatorio"));
        return;
    }
    
    if (empty($detalles) || !is_array($detalles) || count($detalles) === 0) {
        error_log("❌ No hay detalles de receta");
        echo json_encode(responseHTTP::status400("Debe agregar al menos un ingrediente a la receta"));
        return;
    }
    
    try {
        // VERIFICAR SI EXISTE RECETA (solo si no se está sobreescribiendo)
        if (!$sobreescribir) {
            $verificacion = produccionModel::verificarRecetaExistente($id_producto);
            
            if ($verificacion['success'] && $verificacion['existe_receta']) {
                $response = [
                    'status' => 409, // Conflict
                    'success' => false,
                    'message' => 'Ya existe una receta para el producto "' . $verificacion['nombre_producto'] . '". ¿Desea sobreescribirla?',
                    'existe_receta' => true,
                    'nombre_producto' => $verificacion['nombre_producto'],
                    'total_ingredientes' => $verificacion['total_ingredientes']
                ];
                error_log("⚠️ Receta existente encontrada para producto ID: " . $id_producto);
                echo json_encode($response);
                return;
            }
        }
        
        // Preparar datos para el modelo
        $datos = [
            'id_producto' => (int)$id_producto,
            'detalles' => $detalles,
            'creado_por' => $creado_por,
            'sobreescribir' => $sobreescribir
        ];
        
        error_log("🔍 Datos para crear receta: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::crearReceta($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 201,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Receta creada exitosamente para producto ID: " . $id_producto);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al crear receta: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador crearReceta: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al crear receta: ' . $e->getMessage()));
    }
}

/**
 * VERIFICAR RECETA EXISTENTE (para uso del frontend)
 */
public function verificarRecetaExistente() {
    error_log("🎯 INICIANDO verificarRecetaExistente - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Usar $this->data para GET
    if (empty($this->data['id_producto'])) {
        echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
        return;
    }
    
    try {
        $result = produccionModel::verificarRecetaExistente($this->data['id_producto']);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("produccionController::verificarRecetaExistente -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al verificar receta existente'));
    }
}

/**
 * OBTENER MATERIAS PRIMAS
 */
public function obtenerMateriasPrimas() {
    error_log("🎯 INICIANDO obtenerMateriasPrimas - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = produccionModel::obtenerMateriasPrimas();
        
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
        error_log("produccionController::obtenerMateriasPrimas -> " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener materias primas'));
    }
}

/**
 * Obtener todas las recetas
 */
public function obtenerTodasLasRecetas() {
    error_log("🎯 INICIANDO obtenerTodasLasRecetas - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = produccionModel::obtenerTodasLasRecetas();
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data'],
                'total_recetas' => $result['total_recetas']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador obtenerTodasLasRecetas: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener recetas: ' . $e->getMessage()));
    }
}

/**
 * Obtener receta por ID de producto
 */
public function obtenerRecetaPorProducto() {
    error_log("🎯 INICIANDO obtenerRecetaPorProducto - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Usar $this->data para GET
    if (empty($this->data['id_producto'])) {
        echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
        return;
    }
    
    try {
        $result = produccionModel::obtenerRecetaPorProducto($this->data['id_producto']);
        
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
        error_log("💥 Error en controlador obtenerRecetaPorProducto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener receta: ' . $e->getMessage()));
    }
}

/**
 * Sobreescribir receta existente
 */
public function sobreescribirReceta() {
    error_log("🎯 INICIANDO sobreescribirReceta - Method: " . $this->method);
    
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
    $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - Creado por: " . $creado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $detalles = $_POST['detalles'] ?? $this->data['detalles'] ?? [];
    
    // Procesar detalles si vienen como JSON string
    if (is_string($detalles)) {
        $detalles = json_decode($detalles, true);
    }
    
    error_log("🔍 Datos obtenidos - id_producto: " . $id_producto);
    
    // Validar campos requeridos
    if (empty($id_producto)) {
        error_log("❌ Campo requerido faltante: id_producto");
        echo json_encode(responseHTTP::status400("El campo producto es obligatorio"));
        return;
    }
    
    if (empty($detalles) || !is_array($detalles) || count($detalles) === 0) {
        error_log("❌ No hay detalles de receta");
        echo json_encode(responseHTTP::status400("Debe agregar al menos un ingrediente a la receta"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_producto' => (int)$id_producto,
            'detalles' => $detalles,
            'creado_por' => $creado_por
        ];
        
        error_log("🔍 Datos para sobreescribir receta: " . print_r($datos, true));
        
        // Llamar al modelo de sobreescritura
        $result = produccionModel::sobreescribirReceta($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Receta sobreescrita exitosamente para producto ID: " . $id_producto);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al sobreescribir receta: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador sobreescribirReceta: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al sobreescribir receta: ' . $e->getMessage()));
    }
     }
    

    /**
 * Obtener todos los productos
 */
public function obtenerProductos() {
    error_log("🎯 INICIANDO obtenerProductos - Method: " . $this->method);
    
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
        
        error_log("🔍 Filtros para obtener productos: " . print_r($filtros, true));
        
        $result = produccionModel::obtenerProductos($filtros);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'data' => $result['data']
            ];
            error_log("✅ Productos obtenidos exitosamente - Total: " . count($result['data']));
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'message' => $result['message']
            ];
            error_log("❌ Error al obtener productos: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador obtenerProductos: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener productos: ' . $e->getMessage()));
    }
}

/**
 * Ingresar producto al inventario
 */
public function ingresarProductoInventario() {
    error_log("🎯 INICIANDO ingresarProductoInventario - Method: " . $this->method);
    
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
    $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Creado por: " . $creado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? $this->data['cantidad'] ?? null;
    
    error_log("🔍 Datos obtenidos - id_producto: " . $id_producto . ", cantidad: " . $cantidad);
    
    // Validar campos requeridos
    if (empty($id_producto)) {
        error_log("❌ Campo requerido faltante: id_producto");
        echo json_encode(responseHTTP::status400("El campo producto es obligatorio"));
        return;
    }
    
    if (empty($cantidad)) {
        error_log("❌ Campo requerido faltante: cantidad");
        echo json_encode(responseHTTP::status400("El campo cantidad es obligatorio"));
        return;
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
            'id_usuario' => (int)$id_usuario,
            'creado_por' => $creado_por
        ];
        
        error_log("🔍 Datos para ingresar al inventario: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::ingresarProductoInventario($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Producto ingresado al inventario exitosamente");
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al ingresar producto al inventario: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador ingresarProductoInventario: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al ingresar producto al inventario: ' . $e->getMessage()));
    }
}

/**
 * Obtener producto por ID
 */
public function obtenerProductoPorId() {
    error_log("🎯 INICIANDO obtenerProductoPorId - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        // Usar $this->data para GET
        if (empty($this->data['id_producto'])) {
            echo json_encode(responseHTTP::status400("El ID del producto es obligatorio"));
            return;
        }
        
        $result = produccionModel::obtenerProductoPorId($this->data['id_producto']);
        
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
        error_log("💥 Error en controlador obtenerProductoPorId: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener producto: ' . $e->getMessage()));
    }
}

/**
 * Actualizar producto
 */
/**
 * Actualizar producto
 */
public function actualizarProducto() {
    error_log("🎯 INICIANDO actualizarProducto - Method: " . $this->method);
    
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
    $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - Modificado por: " . $modificado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? $this->data['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? $this->data['descripcion'] ?? null;
    $precio = $_POST['precio'] ?? $this->data['precio'] ?? null;
    $id_unidad_medida = $_POST['id_unidad_medida'] ?? $this->data['id_unidad_medida'] ?? null;
    $estado = $_POST['estado'] ?? $this->data['estado'] ?? null;
    
    error_log("🔍 Datos obtenidos - id_producto: " . $id_producto . ", nombre: " . $nombre);
    
    // Validar campos requeridos (sin minimo y maximo)
    $required_fields = [
        'id_producto', 'nombre', 'precio', 'id_unidad_medida', 
        'estado'
    ];
    
    foreach ($required_fields as $field) {
        if (empty($$field)) {
            error_log("❌ Campo requerido faltante: " . $field);
            echo json_encode(responseHTTP::status400("El campo " . $field . " es obligatorio"));
            return;
        }
    }
    
    try {
        // Preparar datos para el modelo (sin minimo y maximo)
        $datos = [
            'id_producto' => (int)$id_producto,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float)$precio,
            'id_unidad_medida' => (int)$id_unidad_medida,
            'estado' => $estado,
            'modificado_por' => $modificado_por
        ];
        
        error_log("🔍 Datos para actualizar producto: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::actualizarProducto($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Producto actualizado exitosamente - ID: " . $id_producto);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al actualizar producto: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador actualizarProducto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al actualizar producto: ' . $e->getMessage()));
    }
}


/**
 * Registrar pérdidas de producción
 */
public function registrarPerdidasProduccion() {
    error_log("🎯 INICIANDO registrarPerdidasProduccion - Method: " . $this->method);
    
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
    $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Modificado por: " . $modificado_por);
    
    // USAR $_POST DIRECTAMENTE
    $id_produccion = $_POST['id_produccion'] ?? $this->data['id_produccion'] ?? null;
    $perdidas = $_POST['perdidas'] ?? $this->data['perdidas'] ?? [];
    
    // Procesar pérdidas desde JSON
    if (is_string($perdidas)) {
        $perdidas = json_decode($perdidas, true);
    }
    
    error_log("🔍 Datos obtenidos - id_produccion: " . $id_produccion);
    error_log("🔍 Pérdidas recibidas: " . print_r($perdidas, true));
    
    // Validar campos requeridos
    if (empty($id_produccion)) {
        error_log("❌ Campo requerido faltante: id_produccion");
        echo json_encode(responseHTTP::status400("El ID de producción es obligatorio"));
        return;
    }
    
    if (empty($perdidas) || !is_array($perdidas) || count($perdidas) === 0) {
        error_log("❌ No hay pérdidas registradas");
        echo json_encode(responseHTTP::status400("Debe registrar al menos una pérdida"));
        return;
    }
    
    if (empty($id_usuario)) {
        error_log("❌ No hay usuario en sesión");
        echo json_encode(responseHTTP::status400("Usuario no autenticado"));
        return;
    }
    
    try {
        // Preparar datos para el modelo
        $datos = [
            'id_produccion' => (int)$id_produccion,
            'id_usuario' => (int)$id_usuario,
            'perdidas' => $perdidas,
            'modificado_por' => $modificado_por
        ];
        
        error_log("🔍 Datos para registrar pérdidas: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::registrarPerdidasProduccion($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Pérdidas registradas exitosamente - ID Producción: " . $id_produccion);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al registrar pérdidas: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador registrarPerdidasProduccion: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al registrar pérdidas: ' . $e->getMessage()));
    }
}

/**
 * Obtener motivos de pérdida
 */
public function obtenerMotivosPerdida() {
    error_log("🎯 INICIANDO obtenerMotivosPerdida - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = produccionModel::obtenerMotivosPerdida();
        
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
        error_log("💥 Error en controlador obtenerMotivosPerdida: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener motivos de pérdida'));
    }
}

/**
 * Obtener pérdidas por producción
 */
public function obtenerPerdidasPorProduccion() {
    error_log("🎯 INICIANDO obtenerPerdidasPorProduccion - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }

    try {
        // Si viene id_produccion => filtrar; si no, traer todas
        if (!empty($this->data['id_produccion'])) {
            $id_produccion = $this->data['id_produccion'];
            error_log("🔎 Filtrando pérdidas por ID_PRODUCCION = " . $id_produccion);
            $result = produccionModel::obtenerPerdidasPorProduccion($id_produccion);
        } else {
            error_log("📋 Obteniendo TODAS las pérdidas de producción");
            $result = produccionModel::obtenerTodasPerdidasProduccion();
        }
        
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
        error_log("💥 Error en controlador obtenerPerdidasPorProduccion: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener pérdidas'));
    }
}
/**
 * Crear producto completo con receta
 */
/**
 * Crear producto completo con receta
 */
public function crearProductoConRecetaCompleto() {
    error_log("🎯 INICIANDO crearProductoConRecetaCompleto - Method: " . $this->method);
    
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
    $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
    
    error_log("👤 Datos de sesión - Creado por: " . $creado_por);
    
    // USAR $_POST DIRECTAMENTE
    $nombre = $_POST['nombre'] ?? $this->data['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? $this->data['descripcion'] ?? null;
    $precio = $_POST['precio'] ?? $this->data['precio'] ?? null;
    $id_unidad_medida = $_POST['id_unidad_medida'] ?? $this->data['id_unidad_medida'] ?? null;
    $detalles = $_POST['detalles'] ?? $this->data['detalles'] ?? [];
    
    // Procesar detalles si vienen como JSON string
    if (is_string($detalles)) {
        $detalles = json_decode($detalles, true);
    }
    
    error_log("🔍 Datos obtenidos - nombre: " . $nombre . ", precio: " . $precio);
    
    // Validar campos requeridos
    $required_fields = ['nombre', 'precio', 'id_unidad_medida'];
    foreach ($required_fields as $field) {
        if (empty($$field)) {
            error_log("❌ Campo requerido faltante: " . $field);
            echo json_encode(responseHTTP::status400("El campo " . $field . " es obligatorio"));
            return;
        }
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
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float)$precio,
            'id_unidad_medida' => (int)$id_unidad_medida,
            'detalles' => $detalles,
            'creado_por' => $creado_por
        ];
        
        error_log("🔍 Datos para crear producto con receta: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::crearProductoConRecetaCompleto($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 201,
                'success' => true,
                'id_producto' => $result['id_producto'],
                'message' => $result['message']
            ];
            error_log("✅ Producto con receta creado exitosamente - ID: " . $result['id_producto']);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al crear producto con receta: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador crearProductoConRecetaCompleto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al crear producto con receta: ' . $e->getMessage()));
    }
}
/**
 * Obtener materias primas para receta
 */
public function obtenerMateriasPrimasParaReceta() {
    error_log("🎯 INICIANDO obtenerMateriasPrimasParaReceta - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = produccionModel::obtenerMateriasPrimasParaReceta();
        
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
        error_log("💥 Error en controlador obtenerMateriasPrimasParaReceta: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener materias primas para receta'));
    }
}
/**
 * Obtener unidades de medida
 */
public function obtenerUnidadesMedida() {
    error_log("🎯 INICIANDO obtenerUnidadesMedida - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $result = produccionModel::obtenerUnidadesMedida();
        
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
        error_log("💥 Error en controlador obtenerUnidadesMedida: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener unidades de medida'));
    }
}

/**
 * Obtener producto y receta por ID para edición
 */
public function obtenerProductoRecetaPorId() {
    error_log("🎯 INICIANDO obtenerProductoRecetaPorId - Method: " . $this->method);
    
    if ($this->method != 'get') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $id_producto = $_GET['id_producto'] ?? $this->data['id_producto'] ?? null;
    
    if (empty($id_producto)) {
        error_log("❌ ID de producto no proporcionado");
        echo json_encode(responseHTTP::status400("ID de producto requerido"));
        return;
    }
    
    try {
        $result = produccionModel::obtenerProductoRecetaPorId($id_producto);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'success' => true,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 404,
                'success' => false,
                'message' => $result['message']
            ]);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador obtenerProductoRecetaPorId: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener producto y receta: ' . $e->getMessage()));
    }
}

/**
 * Editar producto completo con receta
 */
public function editarProductoConRecetaCompleto() {
    error_log("🎯 INICIANDO editarProductoConRecetaCompleto - Method: " . $this->method);
    
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
    $id_producto = $_POST['id_producto'] ?? $this->data['id_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? $this->data['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? $this->data['descripcion'] ?? null;
    $precio = $_POST['precio'] ?? $this->data['precio'] ?? null;
    $id_unidad_medida = $_POST['id_unidad_medida'] ?? $this->data['id_unidad_medida'] ?? null;
    $detalles = $_POST['detalles'] ?? $this->data['detalles'] ?? [];
    
    // Procesar detalles si vienen como JSON string
    if (is_string($detalles)) {
        $detalles = json_decode($detalles, true);
    }
    
    error_log("🔍 Datos obtenidos para edición - ID: " . $id_producto . ", nombre: " . $nombre);
    
    // Validar campos requeridos
    $required_fields = ['id_producto', 'nombre', 'precio', 'id_unidad_medida'];
    foreach ($required_fields as $field) {
        if (empty($$field)) {
            error_log("❌ Campo requerido faltante: " . $field);
            echo json_encode(responseHTTP::status400("El campo " . $field . " es obligatorio"));
            return;
        }
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
            'id_producto' => (int)$id_producto,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float)$precio,
            'id_unidad_medida' => (int)$id_unidad_medida,
            'detalles' => $detalles,
            'actualizado_por' => $actualizado_por
        ];
        
        error_log("🔍 Datos para editar producto con receta: " . print_r($datos, true));
        
        // Llamar al modelo
        $result = produccionModel::editarProductoConRecetaCompleto($datos);
        
        if ($result['success']) {
            $response = [
                'status' => 200,
                'success' => true,
                'message' => $result['message']
            ];
            error_log("✅ Producto con receta actualizado exitosamente - ID: " . $id_producto);
            echo json_encode($response);
        } else {
            $response = [
                'status' => 400,
                'success' => false,
                'message' => $result['message']
            ];
            error_log("❌ Error al editar producto con receta: " . $result['message']);
            echo json_encode($response);
        }
    } catch (\Exception $e) {
        error_log("💥 Error en controlador editarProductoConRecetaCompleto: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al editar producto con receta: ' . $e->getMessage()));
    }
}
}