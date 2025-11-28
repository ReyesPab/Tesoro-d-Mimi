<?php

namespace App\models;

use App\db\connectionDB;
use PDO;

class bitacoraModel {
    
    /**
     * Obtener bitácora con filtros
     */
    public static function obtenerBitacoraFiltrada($filtros) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT 
                        b.ID_BITACORA,
                        b.FECHA,
                        u.USUARIO,
                        u.NOMBRE_USUARIO,
                        o.OBJETO,
                        b.ACCION,
                        b.DESCRIPCION,
                        b.CREADO_POR
                    FROM TBL_MS_BITACORA b
                    LEFT JOIN TBL_MS_USUARIOS u ON b.ID_USUARIO = u.ID_USUARIO
                    LEFT JOIN TBL_MS_OBJETOS o ON b.ID_OBJETO = o.ID_OBJETO
                    WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['usuario'])) {
                $sql .= " AND u.USUARIO LIKE :usuario";
                $params['usuario'] = '%' . $filtros['usuario'] . '%';
            }
            
            if (!empty($filtros['accion'])) {
                $sql .= " AND b.ACCION LIKE :accion";
                $params['accion'] = '%' . $filtros['accion'] . '%';
            }
            
            if (!empty($filtros['fecha_inicio'])) {
                $sql .= " AND DATE(b.FECHA) >= :fecha_inicio";
                $params['fecha_inicio'] = $filtros['fecha_inicio'];
            }
            
            if (!empty($filtros['fecha_fin'])) {
                $sql .= " AND DATE(b.FECHA) <= :fecha_fin";
                $params['fecha_fin'] = $filtros['fecha_fin'];
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (b.DESCRIPCION LIKE :busqueda OR b.ACCION LIKE :busqueda OR u.USUARIO LIKE :busqueda)";
                $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
            }
            
            $sql .= " ORDER BY b.FECHA DESC";
            
            // Paginación
            $limite = (int)$filtros['limite'];
            $offset = ((int)$filtros['pagina'] - 1) * $limite;
            $sql .= " LIMIT :limite OFFSET :offset";
            
            $query = $con->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $query->bindValue(':' . $key, $value);
            }
            
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, PDO::PARAM_INT);
            $query->execute();
            
            $bitacora = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Obtener total para paginación
            $total = self::obtenerTotalRegistros($filtros);
            
            return [
                'bitacora' => $bitacora,
                'paginacion' => [
                    'pagina_actual' => (int)$filtros['pagina'],
                    'total_paginas' => ceil($total / $limite),
                    'total_registros' => $total,
                    'registros_por_pagina' => $limite
                ]
            ];
            
        } catch (\PDOException $e) {
            error_log("bitacoraModel::obtenerBitacoraFiltrada -> " . $e->getMessage());
            return ['bitacora' => [], 'paginacion' => []];
        }
    }
    
    /**
     * Registrar acción en bitácora
     */
    public static function registrarAccion($idUsuario, $idObjeto, $accion, $descripcion, $creadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "INSERT INTO TBL_MS_BITACORA 
                    (FECHA, ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR) 
                    VALUES (NOW(), :id_usuario, :id_objeto, :accion, :descripcion, :creado_por)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $idUsuario,
                'id_objeto' => $idObjeto,
                'accion' => $accion,
                'descripcion' => $descripcion,
                'creado_por' => $creadoPor
            ]);
            
            return true;
            
        } catch (\PDOException $e) {
            error_log("bitacoraModel::registrarAccion -> " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener total de registros para paginación
     */
    private static function obtenerTotalRegistros($filtros) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT COUNT(*) as total
                    FROM TBL_MS_BITACORA b
                    LEFT JOIN TBL_MS_USUARIOS u ON b.ID_USUARIO = u.ID_USUARIO
                    WHERE 1=1";
            
            $params = [];
            
            // Aplicar mismos filtros
            if (!empty($filtros['usuario'])) {
                $sql .= " AND u.USUARIO LIKE :usuario";
                $params['usuario'] = '%' . $filtros['usuario'] . '%';
            }
            
            if (!empty($filtros['accion'])) {
                $sql .= " AND b.ACCION LIKE :accion";
                $params['accion'] = '%' . $filtros['accion'] . '%';
            }
            
            if (!empty($filtros['fecha_inicio'])) {
                $sql .= " AND DATE(b.FECHA) >= :fecha_inicio";
                $params['fecha_inicio'] = $filtros['fecha_inicio'];
            }
            
            if (!empty($filtros['fecha_fin'])) {
                $sql .= " AND DATE(b.FECHA) <= :fecha_fin";
                $params['fecha_fin'] = $filtros['fecha_fin'];
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (b.DESCRIPCION LIKE :busqueda OR b.ACCION LIKE :busqueda OR u.USUARIO LIKE :busqueda)";
                $params['busqueda'] = '%' . $filtros['busqueda'] . '%';
            }
            
            $query = $con->prepare($sql);
            $query->execute($params);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
            
        } catch (\PDOException $e) {
            error_log("bitacoraModel::obtenerTotalRegistros -> " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtener estadísticas de bitácora
     */
    public static function obtenerEstadisticas() {
        try {
            $con = connectionDB::getConnection();
            
            // Acciones más comunes
            $sqlAcciones = "SELECT ACCION, COUNT(*) as total 
                           FROM TBL_MS_BITACORA 
                           GROUP BY ACCION 
                           ORDER BY total DESC 
                           LIMIT 10";
            $query = $con->prepare($sqlAcciones);
            $query->execute();
            $accionesComunes = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Actividad por día (últimos 7 días)
            $sqlActividad = "SELECT DATE(FECHA) as fecha, COUNT(*) as total 
                            FROM TBL_MS_BITACORA 
                            WHERE FECHA >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                            GROUP BY DATE(FECHA) 
                            ORDER BY fecha DESC";
            $query = $con->prepare($sqlActividad);
            $query->execute();
            $actividadReciente = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Usuarios más activos
            $sqlUsuarios = "SELECT u.USUARIO, u.NOMBRE_USUARIO, COUNT(*) as total 
                           FROM TBL_MS_BITACORA b
                           JOIN TBL_MS_USUARIOS u ON b.ID_USUARIO = u.ID_USUARIO
                           GROUP BY b.ID_USUARIO 
                           ORDER BY total DESC 
                           LIMIT 10";
            $query = $con->prepare($sqlUsuarios);
            $query->execute();
            $usuariosActivos = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'acciones_comunes' => $accionesComunes,
                'actividad_reciente' => $actividadReciente,
                'usuarios_activos' => $usuariosActivos
            ];
            
        } catch (\PDOException $e) {
            error_log("bitacoraModel::obtenerEstadisticas -> " . $e->getMessage());
            return [];
        }
    }
}