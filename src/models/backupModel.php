<?php
namespace App\models;

use App\db\connectionDB;
use PDO;

class backupModel extends connectionDB {

    public static function crearRegistroBackup($datos) {
        try {
            $con = self::getConnection();
            
            // Intentar con procedimiento almacenado primero
            try {
                $sql = "CALL SP_CREAR_BACKUP_MANUAL(?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->execute([
                    $datos['NOMBRE_ARCHIVO'],
                    $datos['RUTA_ARCHIVO'], 
                    $datos['CREADO_POR']
                ]);
                
                // Obtener el resultado del procedimiento
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                
                if ($result && isset($result['ID_BACKUP'])) {
                    return $result['ID_BACKUP'];
                }
                
            } catch (\PDOException $e) {
                error_log("Procedimiento SP_CREAR_BACKUP_MANUAL falló, usando INSERT directo: " . $e->getMessage());
            }
            
            // Fallback: INSERT directo
            $sql = "INSERT INTO TBL_MS_BACKUPS (
                NOMBRE_ARCHIVO, TIPO_RESPALDO, RUTA_ARCHIVO, 
                ESTADO, DETALLE, CREADO_POR, FECHA_BACKUP
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $con->prepare($sql);
            $success = $stmt->execute([
                $datos['NOMBRE_ARCHIVO'],
                'MANUAL',
                $datos['RUTA_ARCHIVO'],
                'EJECUTADO',
                $datos['DETALLE'] ?? 'Backup manual ejecutado',
                $datos['CREADO_POR']
            ]);
            
            if ($success) {
                return $con->lastInsertId();
            }
            
            return false;
            
        } catch (\PDOException $e) {
            error_log("Error en crearRegistroBackup: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerBackups() {
        try {
            $con = self::getConnection();
            
            // Intentar con procedimiento almacenado primero
            try {
                $sql = "CALL SP_OBTENER_BACKUPS(100, 0)";
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $result;
            } catch (\PDOException $e) {
                error_log("Procedimiento SP_OBTENER_BACKUPS falló, usando SELECT directo: " . $e->getMessage());
            }
            
            // Fallback: SELECT directo
            $sql = "SELECT 
                ID_BACKUP,
                NOMBRE_ARCHIVO,
                TIPO_RESPALDO,
                RUTA_ARCHIVO,
                FECHA_BACKUP,
                ESTADO,
                DETALLE,
                CREADO_POR
            FROM TBL_MS_BACKUPS 
            ORDER BY FECHA_BACKUP DESC 
            LIMIT 100";
            
            $stmt = $con->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Error en obtenerBackups: " . $e->getMessage());
            return [];
        }
    }
public static function obtenerUltimosBackups($limite = 10) {
    try {
        $con = self::getConnection();
        
        // CONSULTA MEJORADA con manejo de NULL
        $sql = "SELECT 
                    ID_BACKUP,
                    NOMBRE_ARCHIVO,
                    TIPO_RESPALDO,
                    RUTA_ARCHIVO,
                    FECHA_BACKUP,
                    ESTADO,
                    DETALLE,
                    CREADO_POR
                FROM TBL_MS_BACKUPS 
                WHERE ESTADO = 'EJECUTADO' 
                ORDER BY FECHA_BACKUP DESC 
                LIMIT :limite";
        
        $query = $con->prepare($sql);
        $query->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $query->execute();
        
        $resultados = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        // Si no hay resultados, devolver array vacío
        return $resultados ?: [];
        
    } catch (\PDOException $e) {
        error_log("❌ Error en obtenerUltimosBackups: " . $e->getMessage());
        return [];
    }
}


public static function programarBackupAutomatico($datos) {
    try {
        $con = self::getConnection();
        
        // Usar INSERT directo para la tabla de backups automáticos
        $sql = "INSERT INTO TBL_MS_BACKUPS_AUTO (
            NOMBRE_ARCHIVO, RUTA_ARCHIVO, FRECUENCIA, 
            HORA_EJECUCION, DIAS_SEMANA, PROXIMA_EJECUCION, ACTIVO, CREADO_POR
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $con->prepare($sql);
        $stmt->execute([
            $datos['NOMBRE_ARCHIVO'],
            $datos['RUTA_ARCHIVO'],
            $datos['FRECUENCIA'],
            $datos['HORA_EJECUCION'],
            $datos['DIAS_SEMANA'],
            $datos['PROXIMA_EJECUCION'] ?? null,
            $datos['ACTIVO'],
            $datos['CREADO_POR']
        ]);
        
        return $con->lastInsertId();
        
    } catch (\PDOException $e) {
        error_log("Error en programarBackupAutomatico: " . $e->getMessage());
        return false;
    }
}
    public static function eliminarBackupsAntiguos($dias) {
        try {
            $con = self::getConnection();
            
            // Intentar con procedimiento almacenado
            try {
                $sql = "CALL SP_ELIMINAR_BACKUPS_ANTIGUOS(?, 'SISTEMA')";
                $stmt = $con->prepare($sql);
                $stmt->execute([$dias]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $result['ELIMINADOS'] ?? 0;
            } catch (\PDOException $e) {
                error_log("Procedimiento SP_ELIMINAR_BACKUPS_ANTIGUOS falló: " . $e->getMessage());
            }
            
            // Fallback: DELETE directo
            $sql = "DELETE FROM TBL_MS_BACKUPS 
                   WHERE FECHA_BACKUP < DATE_SUB(NOW(), INTERVAL ? DAY)";
            
            $stmt = $con->prepare($sql);
            $stmt->execute([$dias]);
            
            return $stmt->rowCount();
            
        } catch (\PDOException $e) {
            error_log("Error en eliminarBackupsAntiguos: " . $e->getMessage());
            return false;
        }
    }

    public static function obtenerBackupPorId($idBackup) {
        try {
            $con = self::getConnection();
            
            // Intentar con procedimiento almacenado
            try {
                $sql = "CALL SP_OBTENER_BACKUP_POR_ID(?)";
                $stmt = $con->prepare($sql);
                $stmt->execute([$idBackup]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                return $result;
            } catch (\PDOException $e) {
                error_log("Procedimiento SP_OBTENER_BACKUP_POR_ID falló: " . $e->getMessage());
            }
            
            // Fallback: SELECT directo
            $sql = "SELECT * FROM TBL_MS_BACKUPS WHERE ID_BACKUP = ?";
            $stmt = $con->prepare($sql);
            $stmt->execute([$idBackup]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Error en obtenerBackupPorId: " . $e->getMessage());
            return false;
        }
    }

    // ... (mantener los otros métodos igual)
    
    public static function obtenerEstadisticasBackups() {
        try {
            $con = self::getConnection();
            
            // Totales
            $sqlTotales = "SELECT 
                COUNT(*) as total_backups,
                SUM(CASE WHEN ESTADO = 'EJECUTADO' THEN 1 ELSE 0 END) as backups_exitosos,
                SUM(CASE WHEN ESTADO = 'ERROR' THEN 1 ELSE 0 END) as backups_error
                FROM TBL_MS_BACKUPS";
            
            $stmt = $con->prepare($sqlTotales);
            $stmt->execute();
            $totales = $stmt->fetch(PDO::FETCH_ASSOC);

            // Por tipo
            $sqlTipo = "SELECT 
                TIPO_RESPALDO,
                COUNT(*) as total,
                SUM(CASE WHEN ESTADO = 'EJECUTADO' THEN 1 ELSE 0 END) as exitosos
                FROM TBL_MS_BACKUPS 
                GROUP BY TIPO_RESPALDO";
            
            $stmt = $con->prepare($sqlTipo);
            $stmt->execute();
            $porTipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'totales' => $totales,
                'por_tipo' => $porTipo
            ];

        } catch (\PDOException $e) {
            error_log("Error en obtenerEstadisticasBackups: " . $e->getMessage());
            return ['totales' => [], 'por_tipo' => []];
        }
    }

    public static function obtenerBackupsProgramados() {
        try {
            $con = self::getConnection();
            $sql = "SELECT * FROM TBL_MS_BACKUPS_AUTO WHERE ACTIVO = 1 ORDER BY CREADO_EN DESC";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error en obtenerBackupsProgramados: " . $e->getMessage());
            return [];
        }
    }


    public static function obtenerBackupsPendientes() {
    try {
        $con = self::getConnection();
        
        $sql = "SELECT 
                    ID_BACKUP,
                    NOMBRE_ARCHIVO,
                    RUTA_ARCHIVO,
                    TIPO_RESPALDO,
                    FECHA_BACKUP,
                    ESTADO,
                    DETALLE,
                    CREADO_POR
                FROM TBL_MS_BACKUPS 
                WHERE ESTADO = 'PENDIENTE' 
                AND TIPO_RESPALDO = 'AUTOMATICO'
                ORDER BY FECHA_BACKUP ASC";
        
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("Error en obtenerBackupsPendientes: " . $e->getMessage());
        return [];
    }
}

public static function marcarBackupComoEjecutado($idBackup) {
    try {
        $con = self::getConnection();
        
        $sql = "UPDATE TBL_MS_BACKUPS 
                SET ESTADO = 'EJECUTADO', 
                    FECHA_BACKUP = NOW(),
                    DETALLE = 'Backup ejecutado automáticamente'
                WHERE ID_BACKUP = ?";
        
        $stmt = $con->prepare($sql);
        return $stmt->execute([$idBackup]);
        
    } catch (\PDOException $e) {
        error_log("Error en marcarBackupComoEjecutado: " . $e->getMessage());
        return false;
    }
}

public static function obtenerUltimoBackupEjecutado() {
    try {
        $con = self::getConnection();
        
        $sql = "SELECT 
                    ID_BACKUP,
                    NOMBRE_ARCHIVO,
                    RUTA_ARCHIVO,
                    TIPO_RESPALDO,
                    FECHA_BACKUP
                FROM TBL_MS_BACKUPS 
                WHERE ESTADO = 'EJECUTADO' 
                ORDER BY FECHA_BACKUP DESC 
                LIMIT 1";
        
        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("Error en obtenerUltimoBackupEjecutado: " . $e->getMessage());
        return false;
    }
}

public static function desactivarBackupProgramado($idBackupAuto) {
    try {
        $con = self::getConnection();
        
        $sql = "UPDATE TBL_MS_BACKUPS_AUTO 
                SET ACTIVO = 0 
                WHERE ID_BACKUP_AUTO = ?";
        
        $stmt = $con->prepare($sql);
        return $stmt->execute([$idBackupAuto]);
        
    } catch (\PDOException $e) {
        error_log("Error en desactivarBackupProgramado: " . $e->getMessage());
        return false;
    }
}

public static function verificarTablaBackups() {
    try {
        $con = self::getConnection();
        
        // Verificar si la tabla existe
        $sql = "SHOW TABLES LIKE 'TBL_MS_BACKUPS'";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $tablaExiste = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tablaExiste) {
            error_log("❌ La tabla TBL_MS_BACKUPS no existe");
            return false;
        }
        
        // Verificar registros
        $sql = "SELECT COUNT(*) as total FROM TBL_MS_BACKUPS WHERE ESTADO = 'EJECUTADO'";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("📊 Verificación - Backups ejecutados en BD: " . $result['total']);
        
        // Verificar estructura de la tabla
        $sql = "DESCRIBE TBL_MS_BACKUPS";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $estructura = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("📋 Estructura de TBL_MS_BACKUPS:");
        foreach ($estructura as $columna) {
            error_log("   - " . $columna['Field'] . " : " . $columna['Type']);
        }
        
        return true;
        
    } catch (\PDOException $e) {
        error_log("❌ Error verificando tabla: " . $e->getMessage());
        return false;
    }
}

}
?>