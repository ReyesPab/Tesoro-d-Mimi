<?php
// src/config/errorlogs.php
namespace App\config;

class errorlogs {
    
    final public static function activa_error_logs() {
        // Activar todos los errores
        error_reporting(E_ALL);
        
        // Mostrar errores en pantalla (solo en desarrollo)
        ini_set('display_errors', '1');
        
        // Establecer el archivo de log de errores
        ini_set('log_errors', '1');
        ini_set('error_log', dirname(__DIR__, 2) . '/src/logs/php-error.log');
        
        // Establecer zona horaria
        date_default_timezone_set('America/Tegucigalpa');
    }
}