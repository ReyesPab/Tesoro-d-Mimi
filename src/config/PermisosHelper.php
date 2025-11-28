<?php

namespace App\config;

use App\models\permisosModel;

class PermisosHelper {
    
    /**
     * Verificar si el usuario actual tiene permiso para una acción
     */
    public static function checkPermission($objeto, $accion) {
        // Usar SessionHelper en lugar de session_start() directo
        SessionHelper::startSession();
        
        // Si no hay usuario logueado, denegar
        if (!SessionHelper::isLoggedIn()) {
            return false;
        }
        
        $id_usuario = SessionHelper::getUserId();
        
        // El administrador tiene todos los permisos
        if (SessionHelper::isAdmin()) {
            return true;
        }
        
        // Verificar permiso en la base de datos
        try {
            return permisosModel::verificarPermiso($id_usuario, $objeto, $accion);
        } catch (\Exception $e) {
            error_log("Error verificando permiso: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Requerir permiso - si no lo tiene, redirige o muestra error
     */
    public static function requirePermission($objeto, $accion, $redirectUrl = '/sistema/public/inicio') {
        if (!self::checkPermission($objeto, $accion)) {
            // Log del intento de acceso sin permisos
            error_log("🔒 ACCESO DENEGADO - Usuario: " . SessionHelper::getUserId() . 
                     ", Objeto: $objeto, Acción: $accion");
            
            http_response_code(403);
            echo "<script>
                alert('No tiene permisos para realizar esta acción.');
                window.location.href = '$redirectUrl';
            </script>";
            exit;
        }
    }
    
    /**
     * Obtener todos los permisos para un objeto específico (útil para vistas)
     */
    public static function getObjectPermissions($objeto) {
        SessionHelper::startSession();
        
        if (!SessionHelper::isLoggedIn()) {
            return [
                'consultar' => false,
                'crear' => false,
                'actualizar' => false,
                'eliminar' => false
            ];
        }
        
        return [
            'consultar' => self::checkPermission($objeto, 'CONSULTAR'),
            'crear' => self::checkPermission($objeto, 'CREAR'),
            'actualizar' => self::checkPermission($objeto, 'ACTUALIZAR'),
            'eliminar' => self::checkPermission($objeto, 'ELIMINAR')
        ];
    }
    
    /**
     * Verificar permiso y mostrar mensaje (para AJAX)
     */
    public static function checkPermissionAjax($objeto, $accion) {
        if (!self::checkPermission($objeto, $accion)) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'No tiene permisos para realizar esta acción'
            ]);
            exit;
        }
    }
}