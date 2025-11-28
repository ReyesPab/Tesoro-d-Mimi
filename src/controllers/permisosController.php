<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\permisosModel;
use PDO;

class permisosController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
        
        // Establecer headers JSON para todas las respuestas
        header('Content-Type: application/json');
    }
    
    /**
     * Verificar permiso de usuario
     */
    public function verificarPermiso() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $camposRequeridos = ['id_usuario', 'objeto', 'accion'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        try {
            $tienePermiso = permisosModel::verificarPermiso(
                $this->data['id_usuario'],
                $this->data['objeto'],
                $this->data['accion']
            );
            
            echo json_encode([
                'status' => 200,
                'data' => ['tiene_permiso' => $tienePermiso],
                'message' => 'Permiso verificado correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::verificarPermiso -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al verificar permiso'));
        }
    }
    
    /**
     * Obtener menú del usuario
     */
    public function obtenerMenuUsuario() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_usuario'])) {
            echo json_encode(responseHTTP::status400("El ID de usuario es obligatorio"));
            return;
        }
        
        try {
            $menu = permisosModel::obtenerMenuUsuario($this->data['id_usuario']);
            
            echo json_encode([
                'status' => 200,
                'data' => $menu,
                'message' => 'Menú obtenido correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerMenuUsuario -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener menú del usuario'));
        }
    }
    
    /**
     * Obtener objetos del sistema
     */
    public function obtenerObjetos() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $objetos = permisosModel::obtenerObjetos();
            
            echo json_encode([
                'status' => 200,
                'data' => $objetos,
                'message' => 'Objetos obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerObjetos -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener objetos del sistema'));
        }
    }
    
    /**
     * Obtener permisos por rol
     */
    public function obtenerPermisosRol() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_rol'])) {
            echo json_encode(responseHTTP::status400("El ID del rol es obligatorio"));
            return;
        }
        
        try {
            $permisos = permisosModel::obtenerPermisosRol($this->data['id_rol']);
            
            echo json_encode([
                'status' => 200,
                'data' => $permisos,
                'message' => 'Permisos del rol obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerPermisosRol -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener permisos del rol'));
        }
    }
    
    /**
     * Gestionar permisos (crear/actualizar)
     */
    public function gestionarPermiso() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $camposRequeridos = ['id_rol', 'id_objeto', 'permiso_creacion', 'permiso_eliminacion', 'permiso_actualizacion', 'permiso_consultar', 'usuario_accion'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        try {
            $result = permisosModel::gestionarPermiso($this->data);
            
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
            error_log("permisosController::gestionarPermiso -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al gestionar permiso'));
        }
    }
    
    /**
     * Obtener todos los roles
     */
    public function obtenerRoles() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $roles = permisosModel::obtenerRoles();
            
            echo json_encode([
                'status' => 200,
                'data' => $roles,
                'message' => 'Roles obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerRoles -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener roles'));
        }
    }
    
    /**
     * Obtener parámetros de seguridad
     */
    public function obtenerParametrosSeguridad() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $parametros = permisosModel::obtenerParametrosSeguridad();
            
            echo json_encode([
                'status' => 200,
                'data' => $parametros,
                'message' => 'Parámetros de seguridad obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerParametrosSeguridad -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener parámetros de seguridad'));
        }
    }
    
    /**
     * Actualizar parámetro de seguridad
     */
    public function actualizarParametroSeguridad() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $camposRequeridos = ['id_parametro', 'valor', 'modificado_por'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        try {
            $result = permisosModel::actualizarParametro($this->data);
            
            if ($result['success']) {
                $response = [
                    'status' => 200,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? []
                ];
                
                echo json_encode($response);
            } else {
                echo json_encode([
                    'status' => 400,
                    'message' => $result['message']
                ]);
            }
            
        } catch (\Exception $e) {
            error_log("permisosController::actualizarParametroSeguridad -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al actualizar parámetro'));
        }
    }
    
    /**
     * Obtener parámetros del sistema
     */
    public function obtenerParametrosSistema() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $parametros = permisosModel::obtenerParametrosSistema();
            
            echo json_encode([
                'status' => 200,
                'data' => $parametros,
                'message' => 'Parámetros del sistema obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerParametrosSistema -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener parámetros del sistema'));
        }
    }

    /**
     * Obtener parámetros generales del sistema
     */
    public function obtenerParametrosGenerales() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $parametros = permisosModel::obtenerParametrosGenerales();
            
            echo json_encode([
                'status' => 200,
                'data' => $parametros,
                'message' => 'Parámetros generales obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("permisosController::obtenerParametrosGenerales -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener parámetros generales'));
        }
    }
    
    /**
     * Endpoint de debug para testing
     */
    public function debug() {
        echo json_encode([
            'status' => 200,
            'message' => 'Debug endpoint funcionando - Módulo Permisos',
            'data' => [
                'method' => $this->method,
                'params_received' => $this->data,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
?>