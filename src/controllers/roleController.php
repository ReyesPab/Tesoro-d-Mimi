<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\roleModel;

class roleController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }
    
    // Listar roles
    public function listarRoles() {
        error_log("🎯 INICIANDO listarRoles - Method: " . $this->method);
        
        if ($this->method != 'get') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $roles = roleModel::obtenerRoles();
            
            echo json_encode([
                'status' => 200,
                'data' => ['roles' => $roles],
                'message' => 'Roles obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::listarRoles -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener roles: ' . $e->getMessage()));
        }
    }
    
    // Obtener rol específico
    public function obtenerRol() {
        error_log("🎯 INICIANDO obtenerRol - Method: " . $this->method);
        
        if ($this->method != 'get') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_rol'])) {
            error_log("❌ ID de rol requerido");
            echo json_encode(responseHTTP::status400('ID de rol requerido'));
            return;
        }
        
        try {
            $rol = roleModel::obtenerRol($this->data['id_rol']);
            
            if ($rol) {
                echo json_encode(responseHTTP::status200('Rol obtenido', ['rol' => $rol]));
            } else {
                echo json_encode(responseHTTP::status404('Rol no encontrado'));
            }
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::obtenerRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener el rol: ' . $e->getMessage()));
        }
    }
    
    // Crear nuevo rol
    public function crearRol() {
        error_log("🎯 INICIANDO crearRol - Method: " . $this->method);
        
        if ($this->method != 'post') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Iniciar sesión para obtener datos del usuario
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Obtener datos del usuario desde la sesión
        $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $creado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
        
        error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Creado por: " . $creado_por);
        
        // Validar datos requeridos
        if (empty($this->data['rol'])) {
            error_log("❌ Campo rol es obligatorio");
            echo json_encode(responseHTTP::status400('El campo rol es obligatorio'));
            return;
        }
        
        // Validar rol
        $rol = trim($this->data['rol']);
        if (strlen($rol) < 2 || strlen($rol) > 50) {
            error_log("❌ Rol debe tener entre 2 y 50 caracteres");
            echo json_encode(responseHTTP::status400('El rol debe tener entre 2 y 50 caracteres'));
            return;
        }
        
        // Validar descripción si se proporciona
        if (!empty($this->data['descripcion']) && strlen($this->data['descripcion']) > 255) {
            error_log("❌ Descripción no puede exceder 255 caracteres");
            echo json_encode(responseHTTP::status400('La descripción no puede exceder 255 caracteres'));
            return;
        }
        
        try {
            // Preparar datos para el modelo
            $datos = [
                'rol' => $rol,
                'descripcion' => $this->data['descripcion'] ?? null,
                'creado_por' => $creado_por,
                'id_usuario' => $id_usuario
            ];
            
            error_log("📦 Datos para crear rol: " . print_r($datos, true));
            
            // Crear rol usando el procedimiento almacenado
            $result = roleModel::crearRol($datos);
            
            if ($result['success']) {
                $response = [
                    'status' => 201,
                    'success' => true,
                    'message' => $result['message'],
                    'data' => ['id_rol' => $result['id_rol']]
                ];
                error_log("✅ Rol creado exitosamente - ID: " . $result['id_rol']);
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'success' => false,
                    'message' => $result['message']
                ];
                error_log("❌ Error al crear rol: " . $result['message']);
                echo json_encode($response);
            }
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::crearRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al crear el rol: ' . $e->getMessage()));
        }
    }
    
    // Actualizar rol
    public function actualizarRol() {
        error_log("🎯 INICIANDO actualizarRol - Method: " . $this->method);
        
        if ($this->method != 'post') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_rol'])) {
            error_log("❌ ID de rol requerido");
            echo json_encode(responseHTTP::status400('ID de rol requerido'));
            return;
        }
        
        // Iniciar sesión para obtener datos del usuario
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Obtener datos del usuario desde la sesión
        $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
        
        error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Modificado por: " . $modificado_por);
        
        // Validar datos
        $errores = [];
        
        if (empty($this->data['rol'])) {
            $errores[] = 'El campo rol es obligatorio';
        } else {
            $rol = trim($this->data['rol']);
            if (strlen($rol) < 2 || strlen($rol) > 50) {
                $errores[] = 'El rol debe tener entre 2 y 50 caracteres';
            }
        }
        
        if (!empty($this->data['descripcion']) && strlen($this->data['descripcion']) > 255) {
            $errores[] = 'La descripción no puede exceder 255 caracteres';
        }
        
        if (!empty($errores)) {
            error_log("❌ Errores de validación: " . implode('; ', $errores));
            echo json_encode(responseHTTP::status400(implode('; ', $errores)));
            return;
        }
        
        try {
            // Preparar datos para el modelo
            $datos = [
                'id_rol' => $this->data['id_rol'],
                'rol' => trim($this->data['rol']),
                'descripcion' => $this->data['descripcion'] ?? null,
                'modificado_por' => $modificado_por,
                'id_usuario' => $id_usuario
            ];
            
            error_log("📦 Datos para actualizar rol: " . print_r($datos, true));
            
            // Actualizar rol usando el procedimiento almacenado
            $result = roleModel::actualizarRol($datos);
            
            if ($result['success']) {
                $response = [
                    'status' => 200,
                    'success' => true,
                    'message' => $result['message']
                ];
                error_log("✅ Rol actualizado exitosamente");
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'success' => false,
                    'message' => $result['message']
                ];
                error_log("❌ Error al actualizar rol: " . $result['message']);
                echo json_encode($response);
            }
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::actualizarRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al actualizar el rol: ' . $e->getMessage()));
        }
    }
    
    // Eliminar rol
    public function eliminarRol() {
        error_log("🎯 INICIANDO eliminarRol - Method: " . $this->method);
        
        if ($this->method != 'post') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_rol'])) {
            error_log("❌ ID de rol requerido");
            echo json_encode(responseHTTP::status400('ID de rol requerido'));
            return;
        }
        
        // Iniciar sesión para obtener datos del usuario
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Obtener datos del usuario desde la sesión
        $id_usuario = $_SESSION['user_id'] ?? $_SESSION['id_usuario'] ?? 1;
        $modificado_por = $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN';
        
        error_log("👤 Datos de sesión - ID Usuario: " . $id_usuario . ", Modificado por: " . $modificado_por);
        
        try {
            // Preparar datos para el modelo
            $datos = [
                'id_rol' => $this->data['id_rol'],
                'modificado_por' => $modificado_por,
                'id_usuario' => $id_usuario
            ];
            
            error_log("📦 Datos para eliminar rol: " . print_r($datos, true));
            
            // Eliminar rol usando el procedimiento almacenado
            $result = roleModel::eliminarRol($datos);
            
            if ($result['success']) {
                $response = [
                    'status' => 200,
                    'success' => true,
                    'message' => $result['message']
                ];
                error_log("✅ Rol eliminado exitosamente");
                echo json_encode($response);
            } else {
                $response = [
                    'status' => 400,
                    'success' => false,
                    'message' => $result['message']
                ];
                error_log("❌ Error al eliminar rol: " . $result['message']);
                echo json_encode($response);
            }
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::eliminarRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al eliminar el rol: ' . $e->getMessage()));
        }
    }
    
    // Verificar disponibilidad de rol
    public function verificarRol() {
        error_log("🎯 INICIANDO verificarRol - Method: " . $this->method);
        
        if ($this->method != 'post') {
            error_log("❌ Método no permitido: " . $this->method);
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['rol'])) {
            error_log("❌ Rol requerido");
            echo json_encode(responseHTTP::status400('Rol requerido'));
            return;
        }
        
        try {
            $excludeId = $this->data['id_rol'] ?? null;
            $existe = roleModel::rolExiste($this->data['rol'], $excludeId);
            
            if ($existe) {
                echo json_encode(responseHTTP::status400('El rol ya existe'));
            } else {
                echo json_encode(responseHTTP::status200('Rol disponible'));
            }
            
        } catch (\Exception $e) {
            error_log("💥 ERROR roleController::verificarRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al verificar el rol: ' . $e->getMessage()));
        }
    }
}
?>