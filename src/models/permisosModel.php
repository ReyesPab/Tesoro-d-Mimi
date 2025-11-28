<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use PDO;

class permisosModel {
    
    /**
     * Verificar si un usuario tiene permiso para una acción específica
     */
    public static function verificarPermiso($id_usuario, $objeto, $accion) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_VERIFICAR_PERMISO(:id_usuario, :objeto, :accion)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $id_usuario,
                'objeto' => $objeto,
                'accion' => $accion
            ]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result && $result['TIENE_PERMISO'] == 1;
            
        } catch (\PDOException $e) {
            error_log("permisosModel::verificarPermiso -> " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener menú del usuario según sus permisos
     */
    public static function obtenerMenuUsuario($id_usuario) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_MENU_USUARIO(:id_usuario)";
            $query = $con->prepare($sql);
            $query->execute(['id_usuario' => $id_usuario]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerMenuUsuario -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todos los objetos del sistema
     */
    public static function obtenerObjetos() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM tbl_ms_objetos WHERE ESTADO = 'ACTIVO' ORDER BY TIPO_OBJETO, OBJETO";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerObjetos -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener permisos por rol
     */
    public static function obtenerPermisosRol($id_rol) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT p.*, o.OBJETO, o.DESCRIPCION, o.TIPO_OBJETO 
                    FROM tbl_ms_permisos p 
                    INNER JOIN tbl_ms_objetos o ON p.ID_OBJETO = o.ID_OBJETO 
                    WHERE p.ID_ROL = :id_rol";
            
            $query = $con->prepare($sql);
            $query->execute(['id_rol' => $id_rol]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerPermisosRol -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Gestionar permisos (crear/actualizar)
     */
    public static function gestionarPermiso($datos) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_GESTIONAR_PERMISOS(:id_rol, :id_objeto, :permiso_creacion, :permiso_eliminacion, :permiso_actualizacion, :permiso_consultar, :usuario_accion)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_rol' => $datos['id_rol'],
                'id_objeto' => $datos['id_objeto'],
                'permiso_creacion' => $datos['permiso_creacion'],
                'permiso_eliminacion' => $datos['permiso_eliminacion'],
                'permiso_actualizacion' => $datos['permiso_actualizacion'],
                'permiso_consultar' => $datos['permiso_consultar'],
                'usuario_accion' => $datos['usuario_accion']
            ]);
            
            return [
                'success' => true,
                'message' => 'Permiso actualizado correctamente'
            ];
            
        } catch (\PDOException $e) {
            error_log("permisosModel::gestionarPermiso -> " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al gestionar permiso: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener todos los roles
     */
    public static function obtenerRoles() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM tbl_ms_roles ORDER BY ROL";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerRoles -> " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener parámetros de seguridad
     */
    public static function obtenerParametrosSeguridad() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM tbl_ms_parametros WHERE PARAMETRO LIKE 'ADMIN_%' ORDER BY PARAMETRO";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerParametrosSeguridad -> " . $e->getMessage());
            return [];
        }
    }
    
     
    
    /**
     * Obtener parámetros del sistema (todos los parámetros)
     */
    public static function obtenerParametrosSistema() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM tbl_ms_parametros ORDER BY PARAMETRO";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerParametrosSistema -> " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener parámetros generales del sistema (excluyendo seguridad)
     */
    public static function obtenerParametrosGenerales() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM tbl_ms_parametros WHERE PARAMETRO NOT LIKE 'ADMIN_%' ORDER BY PARAMETRO";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("permisosModel::obtenerParametrosGenerales -> " . $e->getMessage());
            return [];
        }
    }

    /**
 * Actualizar parámetro con sincronización de inventarios
 */
/**
 * Actualizar parámetro con sincronización de inventarios
 */
/**
 * Actualizar parámetro con sincronización de inventarios
 */
public static function actualizarParametro($datos) {
    try {
        $con = connectionDB::getConnection();
        
        // Por defecto NO actualizar existentes, a menos que se especifique
        $actualizarExistentes = isset($datos['actualizar_existentes']) ? 
                               (bool)$datos['actualizar_existentes'] : false;
        
        // Log para depuración
        error_log("Actualizando parámetro - ID: " . $datos['id_parametro'] . 
                 ", Valor: " . $datos['valor'] . 
                 ", Actualizar existentes: " . ($actualizarExistentes ? 'SI' : 'NO'));
        
        $sql = "CALL SP_ACTUALIZAR_PARAMETRO_INVENTARIO(:id_parametro, :valor, :modificado_por, :actualizar_existentes)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_parametro' => $datos['id_parametro'],
            'valor' => $datos['valor'],
            'modificado_por' => $datos['modificado_por'],
            'actualizar_existentes' => $actualizarExistentes
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        // Log del resultado
        error_log("Resultado del procedimiento: " . print_r($result, true));
        
        return [
            'success' => true,
            'message' => $result['mensaje'] ?? 'Parámetro actualizado correctamente',
            'data' => [
                'parametro' => $result['parametro'] ?? '',
                'valor_anterior' => $result['valor_anterior'] ?? '',
                'valor_nuevo' => $result['valor_nuevo'] ?? '',
                'registros_afectados' => $result['registros_actualizados'] ?? 0,
                'actualizacion_masiva' => $result['actualizacion_masiva'] ?? false
            ]
        ];
        
    } catch (\PDOException $e) {
        error_log("permisosModel::actualizarParametro -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al actualizar parámetro: ' . $e->getMessage()
        ];
    }
}
}
?>