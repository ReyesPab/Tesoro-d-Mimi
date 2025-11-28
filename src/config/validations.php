<?php

namespace App\config;

class validations {
    
    // Validar formato de contraseña robusta
    public static function validarPassword($password, $usuario = '') {
        $errores = [];
        
        // Longitud mínima y máxima
        if (strlen($password) < 5) {
            $errores[] = "La contraseña debe tener al menos 5 caracteres";
        }
        
        if (strlen($password) > 10) {
            $errores[] = "La contraseña no puede tener más de 10 caracteres";
        }
        
        // Contiene al menos una mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula";
        }
        
        // Contiene al menos una minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra minúscula";
        }
        
        // Contiene al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un número";
        }
        
        // Contiene al menos un carácter especial
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un carácter especial";
        }
        
        // No puede ser igual al usuario
        if (!empty($usuario) && strtoupper($password) === strtoupper($usuario)) {
            $errores[] = "La contraseña no puede ser igual al usuario";
        }
        
        // No se permiten espacios
        if (preg_match('/\s/', $password)) {
            $errores[] = "La contraseña no puede contener espacios";
        }
        
        return $errores;
    }
    
    // Validar formato de email
    public static function validarEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "El formato del correo electrónico no es válido";
        }
        return null;
    }
    
    // Validar que no esté vacío y tenga longitud máxima
    public static function validarCampo($campo, $valor, $longitudMaxima, $nombreCampo) {
        $errores = [];
        
        if (empty(trim($valor))) {
            $errores[] = "El campo $nombreCampo es obligatorio";
        }
        
        if (strlen($valor) > $longitudMaxima) {
            $errores[] = "El campo $nombreCampo no puede tener más de $longitudMaxima caracteres";
        }
        
        // Eliminar espacios múltiples
        if (preg_match('/\s{2,}/', $valor)) {
            $errores[] = "El campo $nombreCampo no puede tener espacios múltiples";
        }
        
        return $errores;
    }
    
    // Generar contraseña robusta automática
    public static function generarPasswordRobusta($longitud = 8) {
        $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minusculas = 'abcdefghijklmnopqrstuvwxyz';
        $numeros = '0123456789';
        $especiales = '!@#$%^&*()-_=+';
        
        $password = '';
        
        // Asegurar al menos un carácter de cada tipo
        $password .= $mayusculas[rand(0, strlen($mayusculas) - 1)];
        $password .= $minusculas[rand(0, strlen($minusculas) - 1)];
        $password .= $numeros[rand(0, strlen($numeros) - 1)];
        $password .= $especiales[rand(0, strlen($especiales) - 1)];
        
        // Completar el resto de la longitud
        $todosCaracteres = $mayusculas . $minusculas . $numeros . $especiales;
        for ($i = strlen($password); $i < $longitud; $i++) {
            $password .= $todosCaracteres[rand(0, strlen($todosCaracteres) - 1)];
        }
        
        // Mezclar los caracteres
        return str_shuffle($password);
    }
    
    // Enmascarar contraseña para mostrar
    public static function enmascararPassword($password) {
        return str_repeat('•', strlen($password));
    }
}