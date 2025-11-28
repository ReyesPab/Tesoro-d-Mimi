<?php

namespace App\models;

use App\db\connectionDB;
use PDO;

class roleModel {
    
    // Obtener todos los roles usando SP
    public static function obtenerRoles() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_ROLES()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::obtenerRoles -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener rol por ID usando SP
    public static function obtenerRol($idRol) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_ROL(:id_rol)";
            $query = $con->prepare($sql);
            $query->execute(['id_rol' => $idRol]);
            
            return $query->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::obtenerRol -> " . $e->getMessage());
            return null;
        }
    }
    
    // Crear nuevo rol usando SP
    public static function crearRol($datos) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_CREAR_ROL(:rol, :descripcion, :creado_por, :id_usuario, @id_rol, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'rol' => $datos['rol'],
                'descripcion' => $datos['descripcion'],
                'creado_por' => $datos['creado_por'],
                'id_usuario' => $datos['id_usuario']
            ]);
            
            // Obtener resultados
            $result = $con->query("SELECT @id_rol as id_rol, @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
            
            error_log("📊 Resultado SP crear rol: " . print_r($result, true));
            
            if (strpos($result['resultado'], 'Error:') === 0) {
                return [
                    'success' => false,
                    'message' => str_replace('Error: ', '', $result['resultado'])
                ];
            } else {
                return [
                    'success' => true,
                    'message' => $result['resultado'],
                    'id_rol' => $result['id_rol']
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::crearRol -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    // Actualizar rol usando SP
    public static function actualizarRol($datos) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_ACTUALIZAR_ROL(:id_rol, :rol, :descripcion, :modificado_por, :id_usuario, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_rol' => $datos['id_rol'],
                'rol' => $datos['rol'],
                'descripcion' => $datos['descripcion'],
                'modificado_por' => $datos['modificado_por'],
                'id_usuario' => $datos['id_usuario']
            ]);
            
            // Obtener resultado
            $result = $con->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
            
            error_log("📊 Resultado SP actualizar rol: " . $result['resultado']);
            
            if (strpos($result['resultado'], 'Error:') === 0) {
                return [
                    'success' => false,
                    'message' => str_replace('Error: ', '', $result['resultado'])
                ];
            } else {
                return [
                    'success' => true,
                    'message' => $result['resultado']
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::actualizarRol -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    // Eliminar rol (cambiar estado a INACTIVO) usando SP
    public static function eliminarRol($datos) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_ELIMINAR_ROL(:id_rol, :modificado_por, :id_usuario, @resultado)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_rol' => $datos['id_rol'],
                'modificado_por' => $datos['modificado_por'],
                'id_usuario' => $datos['id_usuario']
            ]);
            
            // Obtener resultado
            $result = $con->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);
            
            error_log("📊 Resultado SP eliminar rol: " . $result['resultado']);
            
            if (strpos($result['resultado'], 'Error:') === 0) {
                return [
                    'success' => false,
                    'message' => str_replace('Error: ', '', $result['resultado'])
                ];
            } else {
                return [
                    'success' => true,
                    'message' => $result['resultado']
                ];
            }
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::eliminarRol -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }
    
    // Verificar si el rol existe usando SP
    public static function rolExiste($rol, $excludeId = null) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_VERIFICAR_ROL_EXISTE(:rol, :exclude_id, @existe)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'rol' => trim($rol),
                'exclude_id' => $excludeId
            ]);
            
            $result = $con->query("SELECT @existe as existe")->fetch(PDO::FETCH_ASSOC);
            
            return $result['existe'] == 1;
            
        } catch (\PDOException $e) {
            error_log("💥 ERROR roleModel::rolExiste -> " . $e->getMessage());
            return true; // Por seguridad, asumir que existe si hay error
        }
    }
}
?>