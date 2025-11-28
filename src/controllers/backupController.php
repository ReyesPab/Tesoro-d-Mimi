<?php
namespace App\controllers;

use App\config\responseHTTP;
use App\models\backupModel;
use App\config\Security;

class backupController {

    private static function responder($status, $message, $data = []) {
        return json_encode([
            'status' => (string)$status,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function bloquearSistema() {
        try {
            $lockFile = dirname(__DIR__, 2) . '/system.lock';
            $lockContent = json_encode([
                'bloqueado' => true,
                'inicio' => date('Y-m-d H:i:s'),
                'motivo' => 'RESTAURACION_BACKUP',
                'usuario' => $_SESSION['usuario_nombre'] ?? 'SISTEMA'
            ]);
            
            file_put_contents($lockFile, $lockContent);
            return true;
        } catch (\Throwable $e) {
            error_log("Error al bloquear sistema: " . $e->getMessage());
            return false;
        }
    }

    public static function desbloquearSistema() {
        try {
            $lockFile = dirname(__DIR__, 2) . '/system.lock';
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
            return true;
        } catch (\Throwable $e) {
            error_log("Error al desbloquear sistema: " . $e->getMessage());
            return false;
        }
    }

    public static function verificarBloqueo() {
        try {
            $lockFile = dirname(__DIR__, 2) . '/system.lock';
            if (file_exists($lockFile)) {
                $lockData = json_decode(file_get_contents($lockFile), true);
                return [
                    'bloqueado' => true,
                    'data' => $lockData
                ];
            }
            return ['bloqueado' => false];
        } catch (\Throwable $e) {
            return ['bloqueado' => false];
        }
    }

    public static function bloquearSistemaCompletamente() {
        try {
            $lockFile = dirname(__DIR__, 2) . '/system.lock';
            
            $lockData = [
                'bloqueado' => true,
                'inicio' => date('Y-m-d H:i:s'),
                'motivo' => 'RESTAURACION_BACKUP',
                'usuario' => $_SESSION['usuario_nombre'] ?? 'SISTEMA',
                'timestamp' => time(),
                'ruta_backup' => $_POST['ruta_backup'] ?? '',
                'proceso_id' => getmypid()
            ];
            
            if (file_put_contents($lockFile, json_encode($lockData, JSON_PRETTY_PRINT))) {
                self::cerrarSesionesActivas();
                
                session_unset();
                session_destroy();
                
                return true;
            }
            
            return false;
        } catch (\Throwable $e) {
            error_log("Error al bloquear sistema: " . $e->getMessage());
            return false;
        }
    }

    private static function cerrarSesionesActivas() {
        try {
            $sessionPath = session_save_path();
            if (empty($sessionPath)) {
                $sessionPath = sys_get_temp_dir();
            }
            
            $files = glob($sessionPath . '/sess_*');
            $currentSession = session_id();
            
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== 'sess_' . $currentSession) {
                    unlink($file);
                }
            }
            
            return true;
        } catch (\Throwable $e) {
            error_log("No se pudieron cerrar todas las sesiones: " . $e->getMessage());
            return false;
        }
    }

    public static function verificarBloqueoCompleto() {
        try {
            $lockFile = dirname(__DIR__, 2) . '/system.lock';
            
            if (!file_exists($lockFile)) {
                return ['bloqueado' => false];
            }
            
            $lockData = json_decode(file_get_contents($lockFile), true);
            
            if (isset($lockData['timestamp']) && (time() - $lockData['timestamp']) > 1800) {
                self::desbloquearSistema();
                return ['bloqueado' => false];
            }
            
            return [
                'bloqueado' => true,
                'data' => $lockData
            ];
        } catch (\Throwable $e) {
            error_log("Error verificando bloqueo: " . $e->getMessage());
            return ['bloqueado' => false];
        }
    }

    public static function crearBackupManual() {
        try {
            error_log("INICIANDO BACKUP MANUAL...");
            
            $nombreArchivo = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $rutaBase = self::obtenerRutaBackups();
            $rutaArchivo = $rutaBase . $nombreArchivo;

            error_log("Ruta de backup: " . $rutaArchivo);

            $datosBackup = [
                'NOMBRE_ARCHIVO' => $nombreArchivo,
                'TIPO_RESPALDO' => 'MANUAL',
                'RUTA_ARCHIVO' => $rutaArchivo,
                'DETALLE' => 'Backup manual ejecutado',
                'CREADO_POR' => $_SESSION['usuario_nombre'] ?? 'SISTEMA',
                'ESTADO' => 'EJECUTADO'
            ];

            $resultadoBackup = self::ejecutarBackupFisico($datosBackup);
            
            if (!$resultadoBackup) {
                error_log("Falló la creación del backup físico");
                return self::responder(500, 'Error al crear el backup físico');
            }

            $idBackup = backupModel::crearRegistroBackup($datosBackup);
            
            if (!$idBackup) {
                if (file_exists($rutaArchivo)) {
                    unlink($rutaArchivo);
                }
                error_log("Falló el registro en BD");
                return self::responder(500, 'Error al guardar el registro del backup');
            }

            error_log("BACKUP MANUAL COMPLETADO EXITOSAMENTE - ID: " . $idBackup);
            return self::responder(200, 'Backup creado exitosamente', [
                'id_backup' => $idBackup,
                'archivo' => $nombreArchivo,
                'ruta' => $rutaArchivo
            ]);

        } catch (\Throwable $e) {
            error_log("ERROR CRÍTICO en crearBackupManual: " . $e->getMessage());
            return self::responder(500, 'Error interno: ' . $e->getMessage());
        }
    }

public static function programarBackupAutomatico() {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            return self::responder(400, 'Datos JSON inválidos');
        }

        // Validaciones mejoradas
        $errores = [];
        if (empty($input['FRECUENCIA'])) {
            $errores[] = "La frecuencia es obligatoria";
        }
        if (empty($input['HORA_EJECUCION'])) {
            $errores[] = "La hora de ejecución es obligatoria";
        }

        if (!empty($errores)) {
            return self::responder(400, implode(', ', $errores));
        }

        // Calcular próxima ejecución
        $proximaEjecucion = self::calcularProximaEjecucion(
            $input['FRECUENCIA'], 
            $input['HORA_EJECUCION'],
            $input['DIAS_SEMANA'] ?? null
        );

        $rutaBase = self::obtenerRutaBackups();
        $nombreArchivo = 'backup_auto_' . date('Ymd_His') . '.sql';
        $rutaArchivo = $rutaBase . $nombreArchivo;

        $datosBackup = [
            'NOMBRE_ARCHIVO' => $nombreArchivo,
            'RUTA_ARCHIVO' => $rutaArchivo,
            'FRECUENCIA' => $input['FRECUENCIA'],
            'HORA_EJECUCION' => $input['HORA_EJECUCION'],
            'DIAS_SEMANA' => $input['DIAS_SEMANA'] ?? null,
            'PROXIMA_EJECUCION' => $proximaEjecucion,
            'ACTIVO' => 1,
            'CREADO_POR' => $_SESSION['usuario_nombre'] ?? 'SISTEMA'
        ];

        // Usar el modelo existente
        $idBackup = backupModel::programarBackupAutomatico($datosBackup);
        
        if (!$idBackup) {
            return self::responder(500, 'Error al programar el backup automático');
        }

        return self::responder(200, 'Backup automático programado exitosamente', [
            'id_backup' => $idBackup,
            'proxima_ejecucion' => $proximaEjecucion,
            'detalles' => "Próximo backup: " . date('d/m/Y H:i', strtotime($proximaEjecucion))
        ]);

    } catch (\Throwable $e) {
        error_log("Error en programarBackupAutomatico: " . $e->getMessage());
        return self::responder(500, 'Error interno: ' . $e->getMessage());
    }
}

    private static function calcularProximaEjecucion($frecuencia, $horaEjecucion, $diasSemana = null) {
        $ahora = time();
        $horaParts = explode(':', $horaEjecucion);
        $hora = (int)$horaParts[0];
        $minutos = (int)$horaParts[1];
        
        switch($frecuencia) {
            case 'DIARIO':
                $proxima = strtotime('tomorrow ' . $hora . ':' . $minutos);
                break;
                
            case 'SEMANAL':
                if ($diasSemana) {
                    $dias = explode(',', $diasSemana);
                    $diaActual = date('w');
                    
                    foreach ($dias as $dia) {
                        $diaNum = (int)$dia;
                        if ($diaNum > $diaActual) {
                            $proxima = strtotime("+".($diaNum - $diaActual)." days " . $hora . ":" . $minutos);
                            break;
                        }
                    }
                    
                    if (!isset($proxima)) {
                        $primerDia = min($dias);
                        $diasHastaProximo = (7 - $diaActual) + $primerDia;
                        $proxima = strtotime("+".$diasHastaProximo." days " . $hora . ":" . $minutos);
                    }
                } else {
                    $proxima = strtotime('next monday ' . $hora . ':' . $minutos);
                }
                break;
                
            case 'MENSUAL':
                $proxima = strtotime('first day of next month ' . $hora . ':' . $minutos);
                break;
                
            default:
                $dias = (int)$frecuencia;
                $proxima = strtotime("+{$dias} days " . $hora . ":" . $minutos);
                break;
        }
        
        return date('Y-m-d H:i:s', $proxima);
    }

    public static function obtenerProximoBackup() {
        try {
            $backupsProgramados = backupModel::obtenerBackupsProgramados();
            
            $proximoBackup = null;
            $ahora = time();
            
            foreach ($backupsProgramados as $backup) {
                if ($backup['ACTIVO'] == 1 && !empty($backup['PROXIMA_EJECUCION'])) {
                    $tiempoProximo = strtotime($backup['PROXIMA_EJECUCION']);
                    if ($tiempoProximo > $ahora) {
                        if (!$proximoBackup || $tiempoProximo < strtotime($proximoBackup['PROXIMA_EJECUCION'])) {
                            $proximoBackup = $backup;
                        }
                    }
                }
            }
            
            return self::responder(200, 'Próximo backup obtenido', [
                'proximo_backup' => $proximoBackup,
                'tiempo_restante' => $proximoBackup ? self::calcularTiempoRestante($proximoBackup['PROXIMA_EJECUCION']) : null
            ]);
            
        } catch (\Throwable $e) {
            error_log("Error en obtenerProximoBackup: " . $e->getMessage());
            return self::responder(500, 'Error al obtener próximo backup: ' . $e->getMessage());
        }
    }

    private static function calcularTiempoRestante($proximaEjecucion) {
        $ahora = time();
        $proximo = strtotime($proximaEjecucion);
        $diferencia = $proximo - $ahora;
        
        if ($diferencia <= 0) {
            return 'En progreso';
        }
        
        $dias = floor($diferencia / (60 * 60 * 24));
        $horas = floor(($diferencia % (60 * 60 * 24)) / (60 * 60));
        $minutos = floor(($diferencia % (60 * 60)) / 60);
        
        if ($dias > 0) {
            return "{$dias}d {$horas}h";
        } elseif ($horas > 0) {
            return "{$horas}h {$minutos}m";
        } else {
            return "{$minutos}m";
        }
    }

    public static function obtenerBackups() {
        try {
            $backups = backupModel::obtenerBackups();
            
            return self::responder(200, 'Backups obtenidos exitosamente', [
                'backups' => $backups
            ]);

        } catch (\Throwable $e) {
            error_log("Error en obtenerBackups: " . $e->getMessage());
            return self::responder(500, 'Error al obtener backups: ' . $e->getMessage());
        }
    }

    public static function obtenerUltimosBackups() {
        try {
            $backups = backupModel::obtenerUltimosBackups(10);
            
            error_log("Controlador - Backups obtenidos: " . count($backups));
            
            if (empty($backups)) {
                error_log("No hay backups ejecutados disponibles");
                return self::responder(200, 'No hay backups disponibles', [
                    'backups' => []
                ]);
            }
            
            return self::responder(200, 'Backups obtenidos exitosamente', [
                'backups' => $backups
            ]);

        } catch (\Throwable $e) {
            error_log("Error en obtenerUltimosBackups: " . $e->getMessage());
            return self::responder(500, 'Error al obtener backups: ' . $e->getMessage());
        }
    }

    public static function eliminarBackupsAntiguos() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $dias = (int)($input['dias'] ?? 30);

            if ($dias <= 0) {
                return self::responder(400, 'El número de días debe ser mayor a 0');
            }

            $resultado = backupModel::eliminarBackupsAntiguos($dias);
            
            if ($resultado === false) {
                return self::responder(500, 'Error al eliminar backups antiguos');
            }

            return self::responder(200, "Backups anteriores a $dias días eliminados exitosamente", [
                'eliminados' => $resultado
            ]);

        } catch (\Throwable $e) {
            error_log("Error en eliminarBackupsAntiguos: " . $e->getMessage());
            return self::responder(500, 'Error interno: ' . $e->getMessage());
        }
    }

    public static function ejecutarBackupsPendientes() {
        try {
            $backupsPendientes = backupModel::obtenerBackupsPendientes();
            $ejecutados = 0;

            foreach ($backupsPendientes as $backup) {
                $resultado = self::ejecutarBackupFisico([
                    'NOMBRE_ARCHIVO' => $backup['NOMBRE_ARCHIVO'],
                    'RUTA_ARCHIVO' => $backup['RUTA_ARCHIVO']
                ]);

                if ($resultado) {
                    backupModel::marcarBackupComoEjecutado($backup['ID_BACKUP']);
                    $ejecutados++;
                }
            }

            return self::responder(200, "Backups pendientes ejecutados: $ejecutados", [
                'ejecutados' => $ejecutados,
                'total' => count($backupsPendientes)
            ]);

        } catch (\Throwable $e) {
            error_log("Error en ejecutarBackupsPendientes: " . $e->getMessage());
            return self::responder(500, 'Error interno: ' . $e->getMessage());
        }
    }

    public static function obtenerEstadisticas() {
        try {
            $estadisticas = backupModel::obtenerEstadisticasBackups();
            
            return self::responder(200, 'Estadísticas obtenidas exitosamente', [
                'data' => $estadisticas
            ]);

        } catch (\Throwable $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return self::responder(500, 'Error al obtener estadísticas: ' . $e->getMessage());
        }
    }

    public static function restaurarBackup() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                return self::responder(400, 'Datos JSON inválidos');
            }

            if (empty($input['id_backup'])) {
                return self::responder(400, 'ID del backup es requerido');
            }

            if (!self::bloquearSistema()) {
                return self::responder(500, 'No se pudo bloquear el sistema para mantenimiento');
            }

            $backup = backupModel::obtenerBackupPorId($input['id_backup']);
            
            if (!$backup) {
                self::desbloquearSistema();
                return self::responder(404, 'Backup no encontrado');
            }

            if ($backup['ESTADO'] !== 'EJECUTADO') {
                self::desbloquearSistema();
                return self::responder(400, 'Solo se pueden restaurar backups ejecutados');
            }

            $resultado = self::ejecutarRestauracion($backup);
            
            self::desbloquearSistema();
            
            if ($resultado) {
                return self::responder(200, 'Base de datos restaurada exitosamente', [
                    'backup_restaurado' => $backup['NOMBRE_ARCHIVO'],
                    'fecha_backup' => $backup['FECHA_BACKUP'],
                    'reiniciar_sistema' => true
                ]);
            } else {
                return self::responder(500, 'Error al restaurar la base de datos');
            }

        } catch (\Throwable $e) {
            self::desbloquearSistema();
            error_log("Error en restaurarBackup: " . $e->getMessage());
            return self::responder(500, 'Error interno: ' . $e->getMessage());
        }
    }

    public static function descargarBackup() {
        try {
            $idBackup = $_GET['id'] ?? null;
            
            if (!$idBackup) {
                return self::responder(400, 'ID del backup es requerido');
            }

            $backup = backupModel::obtenerBackupPorId($idBackup);
            
            if (!$backup) {
                return self::responder(404, 'Backup no encontrado');
            }

            if (!file_exists($backup['RUTA_ARCHIVO'])) {
                return self::responder(404, 'Archivo de backup no encontrado en el servidor');
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($backup['RUTA_ARCHIVO']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup['RUTA_ARCHIVO']));
            readfile($backup['RUTA_ARCHIVO']);
            exit;

        } catch (\Throwable $e) {
            error_log("Error en descargarBackup: " . $e->getMessage());
            return self::responder(500, 'Error al descargar backup: ' . $e->getMessage());
        }
    }

    public static function obtenerBackupsProgramados() {
        try {
            $backupsProgramados = backupModel::obtenerBackupsProgramados();
            
            return self::responder(200, 'Backups programados obtenidos exitosamente', [
                'backups_programados' => $backupsProgramados
            ]);

        } catch (\Throwable $e) {
            error_log("Error en obtenerBackupsProgramados: " . $e->getMessage());
            return self::responder(500, 'Error al obtener backups programados: ' . $e->getMessage());
        }
    }

    public static function subirBackupArchivo() {
        try {
            error_log("INICIANDO SUBIDA DE BACKUP...");
            
            if (!isset($_FILES['archivo_backup']) || $_FILES['archivo_backup']['error'] !== UPLOAD_ERR_OK) {
                return self::responder(400, 'No se ha subido ningún archivo o hay un error en la subida');
            }

            $archivo = $_FILES['archivo_backup'];
            
            $nombreArchivo = $archivo['name'];
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            if ($extension !== 'sql') {
                return self::responder(400, 'Solo se permiten archivos .sql');
            }

            if ($archivo['size'] > 100 * 1024 * 1024) {
                return self::responder(400, 'El archivo es demasiado grande. Máximo 100MB');
            }

            if ($archivo['size'] < 100) {
                return self::responder(400, 'El archivo está vacío o es muy pequeño');
            }

            $directorioSubidos = dirname(__DIR__, 2) . '/backups/subidos/';
            if (!is_dir($directorioSubidos)) {
                if (!mkdir($directorioSubidos, 0755, true)) {
                    throw new \Exception("No se pudo crear el directorio para backups subidos");
                }
            }

            $nombreUnico = 'backup_subido_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.sql';
            $rutaDestino = $directorioSubidos . $nombreUnico;

            if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                throw new \Exception("Error al mover el archivo subido");
            }

            if (!file_exists($rutaDestino)) {
                throw new \Exception("El archivo subido no se guardó correctamente");
            }

            $datosBackup = [
                'NOMBRE_ARCHIVO' => $nombreArchivo,
                'TIPO_RESPALDO' => 'SUBIDO',
                'RUTA_ARCHIVO' => $rutaDestino,
                'DETALLE' => 'Backup subido manualmente por el usuario',
                'CREADO_POR' => $_SESSION['usuario_nombre'] ?? 'USUARIO',
                'ESTADO' => 'EJECUTADO'
            ];

            $idBackup = backupModel::crearRegistroBackup($datosBackup);
            
            if (!$idBackup) {
                unlink($rutaDestino);
                throw new \Exception("Error al registrar el backup en la base de datos");
            }

            error_log("BACKUP SUBIDO EXITOSAMENTE - ID: " . $idBackup);
            
            return self::responder(200, 'Backup subido y registrado exitosamente', [
                'id_backup' => $idBackup,
                'archivo_original' => $nombreArchivo,
                'archivo_guardado' => $nombreUnico,
                'ruta' => $rutaDestino
            ]);

        } catch (\Throwable $e) {
            error_log("ERROR en subirBackupArchivo: " . $e->getMessage());
            return self::responder(500, 'Error al subir backup: ' . $e->getMessage());
        }
    }

    private static function obtenerRutaBackups() {
        $rutaBase = dirname(__DIR__, 2) . '/backups/';
        
        if (!is_dir($rutaBase)) {
            if (!mkdir($rutaBase, 0755, true)) {
                throw new \Exception("No se pudo crear el directorio: $rutaBase");
            }
        }
        
        return $rutaBase;
    }

    private static function verificarConexionBD() {
        try {
            error_log("Verificando conexión a BD...");
            
            $config = self::obtenerConfiguracionDesdeEnv();
            
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8";
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 10,
            ];
            
            error_log("Conectando a: " . str_replace($config['password'], '***', $dsn));
            
            $pdo = new \PDO($dsn, $config['user'], $config['password'], $options);
            
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            if (empty($tables)) {
                throw new \Exception("No se encontraron tablas en la base de datos");
            }
            
            error_log("Conexión a BD verificada. Tablas encontradas: " . count($tables));
            return true;
            
        } catch (\Throwable $e) {
            error_log("Error verificando conexión BD: " . $e->getMessage());
            throw new \Exception("No se puede conectar a la base de datos: " . $e->getMessage());
        }
    }

    private static function ejecutarBackupFisico($datosBackup) {
        try {
            error_log("INICIANDO EJECUTAR BACKUP FISICO...");
            
            $config = self::obtenerConfiguracionDesdeEnv();
            
            $dbHost = $config['host'];
            $dbUser = $config['user'];
            $dbPass = $config['password'];
            $dbName = $config['database'];
            $dbPort = $config['port'];

            error_log("Configuración DB - Host: $dbHost, DB: $dbName, User: $dbUser, Puerto: $dbPort");

            $directorio = dirname($datosBackup['RUTA_ARCHIVO']);
            if (!is_dir($directorio)) {
                error_log("Creando directorio: $directorio");
                if (!mkdir($directorio, 0755, true)) {
                    throw new \Exception("No se pudo crear el directorio: $directorio");
                }
            }

            $mysqldumpPath = self::encontrarMySQLDump();
            
            if (!empty($mysqldumpPath)) {
                error_log("mysqldump encontrado en: $mysqldumpPath");
                
                $comandos = self::generarComandosMysqldump($mysqldumpPath, $dbHost, $dbUser, $dbPass, $dbPort, $dbName, $datosBackup['RUTA_ARCHIVO']);
                
                foreach ($comandos as $index => $command) {
                    error_log("Probando comando #" . ($index + 1) . "...");
                    error_log("Comando: " . str_replace($dbPass, '***', $command['command']));
                    
                    if (self::ejecutarComandoMysqldump($command['command'], $dbPass, $datosBackup['RUTA_ARCHIVO'])) {
                        error_log("Backup exitoso con comando #" . ($index + 1));
                        return true;
                    }
                }
            }

            error_log("Todos los comandos mysqldump fallaron, intentando método PDO...");
            return self::crearBackupPDO($datosBackup);

        } catch (\Throwable $e) {
            error_log("Error crítico en ejecutarBackupFisico: " . $e->getMessage());
            return false;
        }
    }

    private static function generarComandosMysqldump($mysqldumpPath, $host, $user, $password, $port, $database, $rutaArchivo) {
        $comandos = [];
        
        $cmd1 = "\"$mysqldumpPath\" --host=$host --user=$user --port=$port $database > \"$rutaArchivo\" 2>&1";
        $comandos[] = ['command' => $cmd1, 'use_env' => true];
        
        $cmd2 = "\"$mysqldumpPath\" --host=$host --user=$user $database > \"$rutaArchivo\" 2>&1";
        $comandos[] = ['command' => $cmd2, 'use_env' => true];
        
        if (!empty($password)) {
            $cmd3 = "\"$mysqldumpPath\" --host=$host --user=$user --password=\"$password\" --port=$port $database > \"$rutaArchivo\" 2>&1";
            $comandos[] = ['command' => $cmd3, 'use_env' => false];
        }
        
        $cmd4 = "\"$mysqldumpPath\" -u $user $database > \"$rutaArchivo\" 2>&1";
        $comandos[] = ['command' => $cmd4, 'use_env' => true];
        
        $cmd5 = "\"$mysqldumpPath\" --host=$host --user=$user --port=$port --protocol=TCP $database > \"$rutaArchivo\" 2>&1";
        $comandos[] = ['command' => $cmd5, 'use_env' => true];
        
        return $comandos;
    }

    private static function ejecutarComandoMysqldump($command, $password, $rutaArchivo) {
        try {
            if (strpos($command, 'use_env') !== false || !strpos($command, '--password=')) {
                if (!empty($password)) {
                    putenv("MYSQL_PWD=$password");
                }
            }
            
            exec($command, $output, $returnVar);
            
            if (!empty($password)) {
                putenv("MYSQL_PWD");
            }
            
            if ($returnVar === 0 && file_exists($rutaArchivo)) {
                $fileSize = filesize($rutaArchivo);
                if ($fileSize > 100) {
                    error_log("Comando exitoso. Tamaño archivo: " . $fileSize . " bytes");
                    return true;
                } else {
                    if (file_exists($rutaArchivo)) {
                        unlink($rutaArchivo);
                    }
                }
            }
            
            return false;
            
        } catch (\Throwable $e) {
            if (!empty($password)) {
                putenv("MYSQL_PWD");
            }
            return false;
        }
    }

    private static function obtenerConfiguracionDesdeEnv() {
        try {
            error_log("Cargando configuración desde .env...");
            
            $dotenvPath = dirname(__DIR__, 2) . '/.env';
            error_log("Buscando .env en: $dotenvPath");
            
            if (!file_exists($dotenvPath)) {
                throw new \Exception("Archivo .env no encontrado en: $dotenvPath");
            }
            
            $envContent = file_get_contents($dotenvPath);
            $lines = explode("\n", $envContent);
            
            $envVars = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
                        $value = $matches[1];
                    }
                    
                    $envVars[$key] = $value;
                }
            }
            
            $host = $envVars['IP'] ?? $envVars['DB_HOST'] ?? 'localhost';
            $port = $envVars['PORT'] ?? $envVars['DB_PORT'] ?? '3306';
            $database = $envVars['DB'] ?? $envVars['DB_NAME'] ?? $envVars['DB_DATABASE'] ?? '';
            $user = $envVars['USER'] ?? $envVars['DB_USER'] ?? $envVars['DB_USERNAME'] ?? '';
            $password = $envVars['PASSWORD'] ?? $envVars['DB_PASS'] ?? $envVars['DB_PASSWORD'] ?? '';
            
            error_log("Configuración obtenida - Host: $host, Port: $port, DB: $database, User: $user");
            
            if (empty($database) || empty($user)) {
                throw new \Exception("Configuración de base de datos incompleta en .env");
            }
            
            return [
                'host' => $host,
                'port' => $port,
                'database' => $database,
                'user' => $user,
                'password' => $password
            ];
            
        } catch (\Throwable $e) {
            error_log("Error cargando configuración .env: " . $e->getMessage());
            throw $e;
        }
    }

    private static function encontrarMySQLDump() {
        error_log("Buscando mysqldump...");
        
        $mysqldumpPath = '';
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            error_log("Sistema: Windows");
            $possiblePaths = [
                'C:\xampp\mysql\bin\mysqldump.exe',
                'C:\xampp\mysql\bin\mysqldump',
                'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe',
                'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysqldump.exe',
                'mysqldump.exe',
                'mysqldump'
            ];
            
            foreach ($possiblePaths as $path) {
                error_log("Verificando: $path");
                if (file_exists($path)) {
                    $mysqldumpPath = $path;
                    error_log("Encontrado en: $path");
                    break;
                }
            }
            
            if (empty($mysqldumpPath)) {
                error_log("Buscando en PATH...");
                exec('where mysqldump 2>&1', $output, $returnVar);
                if ($returnVar === 0 && !empty($output[0])) {
                    $foundPath = trim($output[0]);
                    if (file_exists($foundPath)) {
                        $mysqldumpPath = $foundPath;
                        error_log("Encontrado en PATH: $mysqldumpPath");
                    }
                }
            }
        } else {
            error_log("Sistema: Linux/Mac");
            $possiblePaths = [
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                '/opt/local/bin/mysqldump',
                'mysqldump'
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $mysqldumpPath = $path;
                    error_log("Encontrado en: $path");
                    break;
                }
            }
            
            if (empty($mysqldumpPath)) {
                exec('which mysqldump 2>&1', $output, $returnVar);
                if ($returnVar === 0 && !empty($output[0]) && file_exists($output[0])) {
                    $mysqldumpPath = $output[0];
                    error_log("Encontrado con which: $mysqldumpPath");
                }
            }
        }
        
        if (empty($mysqldumpPath)) {
            error_log("No se pudo encontrar mysqldump en ninguna ubicación");
        } else {
            if (!is_executable($mysqldumpPath)) {
                error_log("mysqldump encontrado pero no es ejecutable, intentando de todos modos: $mysqldumpPath");
            }
        }
        
        return $mysqldumpPath;
    }

    private static function crearBackupPDO($datosBackup) {
        try {
            error_log("Iniciando backup PDO...");
            
            $config = self::obtenerConfiguracionDesdeEnv();
            
            $hosts = [$config['host'], 'localhost', '127.0.0.1'];
            $pdo = null;
            
            foreach ($hosts as $host) {
                try {
                    $dsn = "mysql:host={$host};port={$config['port']};dbname={$config['database']};charset=utf8";
                    $options = [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                        \PDO::ATTR_TIMEOUT => 30,
                    ];
                    
                    error_log("Intentando conexión PDO a: " . str_replace($config['password'], '***', $dsn));
                    
                    $pdo = new \PDO($dsn, $config['user'], $config['password'], $options);
                    error_log("Conexión PDO exitosa con host: $host");
                    break;
                    
                } catch (\PDOException $e) {
                    error_log("Conexión PDO falló con host $host: " . $e->getMessage());
                    continue;
                }
            }
            
            if (!$pdo) {
                throw new \Exception("No se pudo conectar a la base de datos con ningún host");
            }
            
            $backupContent = "-- Backup del Sistema Rosquilleria\n";
            $backupContent .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
            $backupContent .= "-- Base de datos: {$config['database']}\n";
            $backupContent .= "-- Método: PDO (Alternativo)\n";
            $backupContent .= "-- Host: {$config['host']}:{$config['port']}\n\n";
            
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            if (empty($tables)) {
                throw new \Exception("No se encontraron tablas en la base de datos");
            }
            
            error_log("Procesando " . count($tables) . " tablas...");
            
            foreach ($tables as $table) {
                error_log("Procesando tabla: " . $table);
                
                try {
                    $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
                    $createTable = $stmt->fetch();
                    
                    if ($createTable && isset($createTable['Create Table'])) {
                        $backupContent .= "\n-- --------------------------------------------------------\n";
                        $backupContent .= "-- Estructura para tabla: $table\n";
                        $backupContent .= "-- --------------------------------------------------------\n\n";
                        $backupContent .= "DROP TABLE IF EXISTS `$table`;\n";
                        $backupContent .= $createTable['Create Table'] . ";\n\n";
                        
                        $backupContent .= "-- \n";
                        $backupContent .= "-- Volcado de datos para la tabla: $table\n";
                        $backupContent .= "-- \n\n";
                        
                        $stmtData = $pdo->query("SELECT * FROM `$table` LIMIT 1000");
                        $rows = $stmtData->fetchAll();
                        
                        if (!empty($rows)) {
                            $rowCount = 0;
                            
                            foreach ($rows as $row) {
                                $columns = [];
                                $values = [];
                                
                                foreach ($row as $column => $value) {
                                    $columns[] = "`$column`";
                                    if ($value === null) {
                                        $values[] = 'NULL';
                                    } else {
                                        $escapedValue = str_replace("'", "''", $value);
                                        $escapedValue = str_replace("\\", "\\\\", $escapedValue);
                                        $escapedValue = str_replace("\n", "\\n", $escapedValue);
                                        $escapedValue = str_replace("\r", "\\r", $escapedValue);
                                        $values[] = "'" . $escapedValue . "'";
                                    }
                                }
                                
                                $backupContent .= "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                                $rowCount++;
                            }
                            
                            $backupContent .= "-- Total registros: $rowCount\n\n";
                            error_log("Tabla $table: $rowCount registros exportados");
                        } else {
                            $backupContent .= "-- Tabla vacía\n\n";
                            error_log("Tabla $table: vacía");
                        }
                    }
                } catch (\PDOException $e) {
                    error_log("Error procesando tabla $table: " . $e->getMessage());
                    $backupContent .= "-- Error al procesar tabla $table: " . $e->getMessage() . "\n\n";
                    continue;
                }
            }
            
            $backupContent .= "-- \n";
            $backupContent .= "-- Fin del backup\n";
            $backupContent .= "-- Tablas exportadas: " . count($tables) . "\n";
            $backupContent .= "-- \n";
            
            error_log("Guardando archivo: " . $datosBackup['RUTA_ARCHIVO']);
            $bytesWritten = file_put_contents($datosBackup['RUTA_ARCHIVO'], $backupContent);
            
            if ($bytesWritten === false) {
                throw new \Exception("No se pudo escribir el archivo de backup PDO");
            }
            
            $fileSize = filesize($datosBackup['RUTA_ARCHIVO']);
            
            if ($fileSize > 1000) {
                error_log("Backup PDO exitoso. Tamaño: " . $fileSize . " bytes");
                error_log("Tablas exportadas: " . count($tables));
                return true;
            } else {
                if (file_exists($datosBackup['RUTA_ARCHIVO'])) {
                    unlink($datosBackup['RUTA_ARCHIVO']);
                }
                throw new \Exception("El archivo de backup PDO está vacío o es muy pequeño (" . $fileSize . " bytes)");
            }
            
        } catch (\PDOException $e) {
            error_log("Error PDO en crearBackupPDO: " . $e->getMessage());
            throw new \Exception("Error de base de datos: " . $e->getMessage());
        } catch (\Throwable $e) {
            error_log("Error general en crearBackupPDO: " . $e->getMessage());
            throw $e;
        }
    }

    private static function ejecutarRestauracion($backup) {
        try {
            $config = self::obtenerConfiguracionDesdeEnv();
            
            $dbHost = $config['host'];
            $dbUser = $config['user'];
            $dbPass = $config['password'];
            $dbName = $config['database'];
            $dbPort = $config['port'];

            error_log("Restauración - Configuración DB - Host: $dbHost, DB: $dbName, User: $dbUser");

            if (!file_exists($backup['RUTA_ARCHIVO'])) {
                throw new \Exception("Archivo de backup no encontrado: " . $backup['RUTA_ARCHIVO']);
            }

            $fileSize = filesize($backup['RUTA_ARCHIVO']);
            if ($fileSize < 100) {
                throw new \Exception("El archivo de backup está vacío o es muy pequeño: " . $fileSize . " bytes");
            }

            $mysqlPath = self::encontrarMySQL();
            
            if (empty($mysqlPath)) {
                throw new \Exception("No se pudo encontrar mysql en el sistema");
            }

            $passwordPart = $dbPass ? "-p\"$dbPass\"" : "";
            $comando = "\"$mysqlPath\" -h $dbHost -P $dbPort -u $dbUser $passwordPart $dbName < \"{$backup['RUTA_ARCHIVO']}\" 2>&1";
            
            error_log("Ejecutando restauración: " . str_replace($dbPass, '***', $comando));
            
            exec($comando, $output, $returnVar);

            error_log("Salida restauración: " . implode(', ', $output));
            error_log("Código de retorno: " . $returnVar);

            if ($returnVar === 0) {
                error_log("Restauración completada exitosamente");
                return true;
            } else {
                $errorMsg = "Error en restauración (código: $returnVar): " . implode(', ', $output);
                throw new \Exception($errorMsg);
            }

        } catch (\Throwable $e) {
            error_log("Error en ejecutarRestauracion: " . $e->getMessage());
            return false;
        }
    }

    private static function encontrarMySQL() {
        $mysqlPath = '';
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $possiblePaths = [
                'C:\xampp\mysql\bin\mysql.exe',
                'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe',
                'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe',
                'mysql.exe'
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $mysqlPath = $path;
                    break;
                }
            }
            
            if (empty($mysqlPath)) {
                exec('where mysql.exe 2>&1', $output, $returnVar);
                if ($returnVar === 0 && !empty($output[0])) {
                    $mysqlPath = $output[0];
                }
            }
        } else {
            $mysqlPath = 'mysql';
            
            exec('which mysql 2>&1', $output, $returnVar);
            if ($returnVar !== 0) {
                $mysqlPath = '';
            }
        }
        
        return $mysqlPath;
    }

}
?>