<?php

namespace App\config;

class SessionHelper {
    
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            // Verificar si los headers ya fueron enviados
            if (headers_sent()) {
                error_log("⚠️ No se puede iniciar sesión: headers ya enviados");
                return false;
            }
            session_start();
            return true;
        }
        return false; // Sesión ya estaba iniciada
    }
    
    public static function setUserSession($userData) {
        if (!self::startSession()) {
            return false;
        }
        
        // Usar los mismos nombres que tu sistema actual
        $_SESSION['usuario_nombre'] = $userData['NOMBRE_USUARIO'] ?? $userData['nombre_usuario'] ?? null;
        $_SESSION['user_name'] = $userData['USUARIO'] ?? $userData['usuario'] ?? null;
        $_SESSION['id_usuario'] = $userData['ID_USUARIO'] ?? $userData['id_usuario'] ?? null;
        $_SESSION['id_rol'] = $userData['ID_ROL'] ?? $userData['id_rol'] ?? null;
        $_SESSION['usuario_rol'] = $userData['ROL'] ?? $userData['rol'] ?? null;
        $_SESSION['foto_perfil'] = $userData['FOTO_PERFIL'] ?? $userData['foto_perfil'] ?? 'perfil.jpg';
        $_SESSION['logged_in'] = true;
        $_SESSION['iniciada'] = true;
        
        return true;
    }
    
    public static function getUserId() {
        if (session_status() == PHP_SESSION_NONE) {
            return null;
        }
        return $_SESSION['id_usuario'] ?? $_SESSION['ID_USUARIO'] ?? $_SESSION['user_id'] ?? null;
    }
    
    public static function getUserRoleId() {
        if (session_status() == PHP_SESSION_NONE) {
            return null;
        }
        return $_SESSION['id_rol'] ?? $_SESSION['ID_ROL'] ?? $_SESSION['user_role'] ?? null;
    }
    
    public static function getUsername() {
        if (session_status() == PHP_SESSION_NONE) {
            return null;
        }
        return $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? $_SESSION['USUARIO'] ?? null;
    }
    
    public static function getUserFullName() {
        if (session_status() == PHP_SESSION_NONE) {
            return 'Invitado';
        }
        return $_SESSION['usuario_nombre'] ?? $_SESSION['NOMBRE_USUARIO'] ?? 'Invitado';
    }
    
    public static function getUserPhoto() {
        if (session_status() == PHP_SESSION_NONE) {
            return 'perfil.jpg';
        }
        return $_SESSION['foto_perfil'] ?? 'perfil.jpg';
    }
    
    public static function setUserPhoto($fotoPerfil) {
        if (session_status() == PHP_SESSION_NONE) {
            return false;
        }
        $_SESSION['foto_perfil'] = $fotoPerfil;
        return true;
    }
    
    public static function isLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            return false;
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public static function isAdmin() {
        if (session_status() == PHP_SESSION_NONE) {
            return false;
        }
        $id_rol = self::getUserRoleId();
        return $id_rol == 1; // 1 = Administrador
    }
    
    public static function checkAuth() {
        if (!self::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['status' => 401, 'message' => 'Usuario no autenticado']);
            exit;
        }
    }
    
    public static function checkAdmin() {
        if (!self::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 403, 'message' => 'No tiene permisos de administrador']);
            exit;
        }
    }
    
    public static function destroySession() {
        if (session_status() != PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
        }
    }
    
    public static function debugSession() {
        if (session_status() == PHP_SESSION_NONE) {
            return ['error' => 'No hay sesión activa'];
        }
        return $_SESSION;
    }
}