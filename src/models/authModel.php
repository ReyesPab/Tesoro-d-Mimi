<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use App\config\validations;
use PDO;

class authModel {
    
    // Verificar credenciales de usuario usando procedimiento almacenado y Verificar usuario con validación de expiración
public static function verificarUsuario($usuario, $password) {
    try {
        $con = connectionDB::getConnection();
        
        $usuario = strtoupper(trim($usuario));
        
        $sql = "CALL SP_LOGIN_USUARIO(:usuario, :password)";
        $query = $con->prepare($sql);
        $query->execute([
            'usuario' => $usuario,
            'password' => $password
        ]);
        
        // Obtener el primer resultado
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        // Limpiar cualquier resultado adicional
        while ($query->nextRowset()) {
            // Continuar hasta que no haya más resultados
        }
        
        if ($result && $result['STATUS'] === 'success') {
            // Obtener datos completos del usuario
            $userData = self::obtenerDatosUsuarioCompletos($usuario);
            
            // VALIDAR SI LA CONTRASEÑA TEMPORAL ESTÁ EXPIRADA
            if ($userData && $userData['RESETEO_CONTRASENA'] == 1 && $userData['FECHA_VENCIMIENTO']) {
                $fechaExpiracion = new \DateTime($userData['FECHA_VENCIMIENTO']);
                $fechaActual = new \DateTime();
                
                if ($fechaExpiracion < $fechaActual) {
                    return [
                        'success' => false, 
                        'message' => 'La contraseña temporal ha expirado. Solicite una nueva recuperación.'
                    ];
                }
            }
            
            // NUEVA LÓGICA: Determinar si requiere autenticación de 2 pasos
            $requiere_2fa = false;
            
            // Si el usuario tiene 2FA habilitado explícitamente
            if ($userData && isset($userData['HABILITAR_2FA']) && $userData['HABILITAR_2FA'] == 1) {
                $requiere_2fa = true;
            }
            // Si el usuario es NUEVO y PRIMER_INGRESO es 0, requiere 2FA
            else if ($userData && $userData['ESTADO_USUARIO'] == 'Nuevo' && $userData['PRIMER_INGRESO'] == 0) {
                $requiere_2fa = true;
            }
            // Si el usuario es ACTIVO y PRIMER_INGRESO es 1, NO requiere 2FA
            else if ($userData && $userData['ESTADO_USUARIO'] == 'ACTIVO' && $userData['PRIMER_INGRESO'] == 1) {
                $requiere_2fa = false;
            }
            
            return [
                'success' => true,
                'user' => $userData,
                'requiere_cambio' => $userData && $userData['RESETEO_CONTRASENA'] == 1,
                'requiere_2fa' => $requiere_2fa
            ];
        } else {
            $message = $result['MESSAGE'] ?? 'Error desconocido';
            return ['success' => false, 'message' => $message];
        }
        
    } catch (\PDOException $e) {
        error_log("authModel::verificarUsuario -> " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en el servidor'];
    }
}

    // Agregar este método a authModel
    private static function obtenerDatosUsuarioCompletos($usuario) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT U.*, R.ROL 
                    FROM TBL_MS_USUARIOS U 
                    INNER JOIN TBL_MS_ROLES R ON U.ID_ROL = R.ID_ROL 
                    WHERE U.USUARIO = :usuario";
            
            $query = $con->prepare($sql);
            $query->execute(['usuario' => $usuario]);
            
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("authModel::obtenerDatosUsuarioCompletos -> " . $e->getMessage());
            return null;
        }
    }
    
    // Obtener datos completos del usuario
    public static function obtenerDatosUsuario($usuario) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT U.*, R.ROL 
                    FROM TBL_MS_USUARIOS U 
                    INNER JOIN TBL_MS_ROLES R ON U.ID_ROL = R.ID_ROL 
                    WHERE U.USUARIO = :usuario";
            
            $query = $con->prepare($sql);
            $query->execute(['usuario' => $usuario]);
            
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("authModel::obtenerDatosUsuario -> " . $e->getMessage());
            return [];
        }
    }
    
    // Verificar si usuario existe usando procedimiento almacenado
    public static function verificarUsuarioExiste($usuario) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_VERIFICAR_USUARIO(:usuario)";
            $query = $con->prepare($sql);
            $query->execute(['usuario' => $usuario]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result['STATUS'] === 'exists';
            
        } catch (\PDOException $e) {
            error_log("authModel::verificarUsuarioExiste -> " . $e->getMessage());
            return false;
        }
    }

    
    
    // Verificar respuestas de seguridad usando procedimiento almacenado
    public static function verificarRespuestas($idUsuario, $respuestas) {
        try {
            $con = connectionDB::getConnection();
            
            // Convertir array de respuestas a JSON para el procedimiento
            $respuestasJson = json_encode($respuestas);
            
            $sql = "CALL SP_VALIDAR_RESPUESTAS(:id_usuario, :respuestas)";
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $idUsuario,
                'respuestas' => $respuestasJson
            ]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result['STATUS'] === 'success';
            
        } catch (\PDOException $e) {
            error_log("authModel::verificarRespuestas -> " . $e->getMessage());
            return false;
        }
    }
    
    // Cambiar contraseña usando procedimiento almacenado
    public static function cambiarPassword($idUsuario, $nuevaPassword, $contraseñaActual = null, $modificadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            
            error_log(" Cambiando contraseña para usuario: " . $idUsuario);
            
            if ($contraseñaActual) {
                // Cambio normal de contraseña
                $sql = "CALL SP_CAMBIAR_CONTRASENA(:p_id_usuario, :p_contrasena_actual, :p_nueva_contrasena, :p_modificado_por)";
                $query = $con->prepare($sql);
                $query->execute([
                    'p_id_usuario' => $idUsuario,
                    'p_contrasena_actual' => $contraseñaActual,
                    'p_nueva_contrasena' => $nuevaPassword,
                    'p_modificado_por' => $modificadoPor
                ]);
            } else {
                // Reset de contraseña
                $sql = "CALL SP_RESETEAR_CONTRASENA(:p_id_usuario, :p_nueva_contrasena, :p_modificado_por)";
                $query = $con->prepare($sql);
                $query->execute([
                    'p_id_usuario' => $idUsuario,
                    'p_nueva_contrasena' => $nuevaPassword,
                    'p_modificado_por' => $modificadoPor
                ]);
            }
            
            // Obtener resultado
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            // Limpiar resultsets
            while ($query->nextRowset()) {
                // Continuar
            }
            
            if ($result && $result['STATUS'] === 'success') {
                error_log(" Contraseña cambiada exitosamente para usuario: " . $idUsuario);
                return ['success' => true, 'message' => $result['MESSAGE']];
            } else {
                $errorMsg = $result['MESSAGE'] ?? 'Error en stored procedure';
                error_log(" Error al cambiar contraseña: " . $errorMsg);
                return ['success' => false, 'message' => $errorMsg];
            }
            
        } catch (\PDOException $e) {
            error_log(" Error en cambiarPassword: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()];
        }
    }
    
    // MÉTODO ESPECÍFICO PARA CAMBIAR CONTRASEÑA DESDE DASHBOARD
    public static function cambiarPasswordDashboard($idUsuario, $nuevaPassword, $passwordActual) {
        try {
            $con = connectionDB::getConnection();
            
            error_log(" [DASHBOARD] Cambiando contraseña para usuario: " . $idUsuario);
            
            // Usar SP para cambio desde dashboard
            $sql = "CALL SP_CAMBIAR_PASSWORD_SIN_ENIE(:p_id_usuario, :p_password_actual, :p_nueva_password, :p_modificado_por)";
            
            error_log(" [DASHBOARD] Usando SP: SP_CAMBIAR_PASSWORD_SIN_ENIE");
            
            $query = $con->prepare($sql);
            $query->execute([
                'p_id_usuario' => (int)$idUsuario,
                'p_password_actual' => $passwordActual,
                'p_nueva_password' => $nuevaPassword,
                'p_modificado_por' => 'SISTEMA'
            ]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            // Limpiar resultsets
            while ($query->nextRowset()) {
                // Continuar
            }
            
            if ($result && $result['STATUS'] === 'success') {
                error_log(" [DASHBOARD] Contraseña cambiada exitosamente");
                return ['success' => true, 'message' => $result['MESSAGE']];
            } else {
                $errorMsg = $result['MESSAGE'] ?? 'Error en stored procedure';
                error_log(" [DASHBOARD] Error: " . $errorMsg);
                return ['success' => false, 'message' => $errorMsg];
            }
            
        } catch (\PDOException $e) {
            error_log(" [DASHBOARD] Error PDO: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()];
        }
    }

    
    // Registrar en bitácora
public static function registrarBitacora($idUsuario, $accion, $descripcion) {
    try {
        $con = connectionDB::getConnection();
        
        // INCLUIR LA COLUMNA FECHA QUE ES NOT NULL
        $sql = "INSERT INTO TBL_MS_BITACORA (FECHA, ID_USUARIO, ACCION, DESCRIPCION, CREADO_POR) 
                VALUES (NOW(), :id_usuario, :accion, :descripcion, :creado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_usuario' => $idUsuario,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'creado_por' => 'SISTEMA'
        ]);
        
        error_log(" REGISTRADO EN BITÁCORA - Usuario: $idUsuario, Acción: $accion");
        
    } catch (\PDOException $e) {
        error_log(" ERROR en registrarBitacora: " . $e->getMessage());
    }
}
    
    // Obtener parámetro del sistema
    public static function obtenerParametro($parametro) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT VALOR FROM TBL_MS_PARAMETROS WHERE PARAMETRO = :parametro";
            $query = $con->prepare($sql);
            $query->execute(['parametro' => $parametro]);
            
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['VALOR'];
            }
            
            return null;
        } catch (\PDOException $e) {
            error_log("authModel::obtenerParametro -> " . $e->getMessage());
            return null;
        }
    }

    // Verificar si usuario existe y obtener información para recuperación  directa sin stored procedure
public static function verificarUsuarioRecuperacion($usuario) {
    try {
        $con = connectionDB::getConnection();
        
        error_log(" BUSCANDO USUARIO PARA RECUPERACIÓN: " . $usuario);
        
        // CONSULTA CORREGIDA: usar ESTADO_USUARIO en lugar de ESTADO
        $sql = "SELECT 
                    ID_USUARIO,
                    USUARIO, 
                    NOMBRE_USUARIO,
                    CORREO_ELECTRONICO,
                    ESTADO_USUARIO,  -- CAMBIADO: de ESTADO a ESTADO_USUARIO
                    RESETEO_CONTRASENA
                FROM TBL_MS_USUARIOS 
                WHERE USUARIO = :usuario";
        
        $query = $con->prepare($sql);
        $query->execute(['usuario' => $usuario]);
        
        $userData = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            error_log(" USUARIO ENCONTRADO: " . $userData['USUARIO']);
            error_log(" CORREO: " . $userData['CORREO_ELECTRONICO']);
            error_log(" ESTADO_USUARIO: " . $userData['ESTADO_USUARIO']);
            
            // Verificar si el usuario está activo (comparar con 'ACTIVO' en mayúsculas)
            if (strtoupper($userData['ESTADO_USUARIO']) !== 'ACTIVO') {
                return [
                    'success' => false, 
                    'message' => 'Usuario no activo. Estado actual: ' . $userData['ESTADO_USUARIO']
                ];
            }
            
            // Verificar que tenga correo electrónico
            if (empty($userData['CORREO_ELECTRONICO'])) {
                return [
                    'success' => false, 
                    'message' => 'El usuario no tiene correo electrónico registrado'
                ];
            }
            
            return ['success' => true, 'user' => $userData];
        } else {
            error_log(" USUARIO NO ENCONTRADO: " . $usuario);
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
    } catch (\PDOException $e) {
        error_log(" ERROR en verificarUsuarioRecuperacion: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()];
    }
}

    

    // Solicitar recuperación por correo
public static function solicitarRecuperacionCorreo($usuario, $contraseñaTemporal) {
    try {
        $con = connectionDB::getConnection();
        
        // AGREGAR ESTA CONFIGURACIÓN PARA MÚLTIPLES RESULTSETS
        $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        
        error_log(" SOLICITANDO RECUPERACIÓN PARA: $usuario");
        error_log(" CONTRASEÑA TEMPORAL GENERADA: $contraseñaTemporal");
        
        // Usar el procedimiento con expiración
        $sql = "CALL SP_RECUPERACION_CORREO_CON_EXPIRACION(:usuario, :contrasena_temporal, :modificado_por)";
        $query = $con->prepare($sql);
        $query->execute([
            'usuario' => $usuario,
            'contrasena_temporal' => $contraseñaTemporal,
            'modificado_por' => 'SISTEMA_RECUPERACION'
        ]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        // Limpiar resultsets
        while ($query->nextRowset()) {
            // Continuar
        }
        
        error_log(" RESULTADO DEL STORED PROCEDURE: " . print_r($result, true));
        
        if ($result && $result['STATUS'] === 'success') {
            // SIEMPRE DEVOLVER LA CONTRASEÑA EN LA RESPUESTA
            return [
                'success' => true, 
                'message' => 'Contraseña temporal generada exitosamente: ' . $contraseñaTemporal,
                'correo' => $result['CORREO'],
                'nombre_usuario' => $result['NOMBRE_USUARIO'],
                'fecha_expiracion' => $result['FECHA_EXPIRACION'],
                'password_temporal' => $contraseñaTemporal 
            ];
        } else {
            $errorMsg = $result['MESSAGE'] ?? 'Error desconocido en el procedimiento';
            error_log(" ERROR EN STORED PROCEDURE: " . $errorMsg);
            return ['success' => false, 'message' => $errorMsg];
        }
        
    } catch (\PDOException $e) {
        error_log(" ERROR PDO en solicitarRecuperacionCorreo: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()];
    }
}

    //  Envío de correo con PHPMailer 
public static function enviarCorreoRecuperacion($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
    try {
        error_log(" INTENTANDO ENVIAR CORREO A: $correo");
        
        // Intentar con PHPMailer primero si está disponible
        if (self::enviarConPHPMailer($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion)) {
            return true;
        }
        
        // Si PHPMailer no está disponible o falla, usar método básico
        error_log(" Intentando método mail() básico...");
        return self::enviarConMailBasico($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion);
        
    } catch (\Exception $e) {
        error_log(" ERROR en enviarCorreoRecuperacion: " . $e->getMessage());
        return false;
    }
}

// Envío con PHPMailer
private static function enviarConPHPMailer($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
    try {
        // Verificar si PHPMailer existe
        $phpmailerPath = __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
        if (!file_exists($phpmailerPath)) {
            error_log(" PHPMailer no encontrado en: $phpmailerPath");
            return false;
        }
        
        require_once $phpmailerPath;
        require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración SMTP de Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tu-email@gmail.com'; // REEMPLAZA CON TU EMAIL
        $mail->Password = 'tu-password-de-aplicacion'; // REEMPLAZA CON CONTRASEÑA DE APLICACIÓN
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Opciones para desarrollo
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30;
        
        // Destinatarios
        $mail->setFrom('sistema@rosquilleria.com', 'Sistema Rosquilleria');
        $mail->addAddress($correo, $nombreUsuario);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = '🔐 Recuperación de Contraseña - Sistema Rosquilleria';
        $mail->Body = self::crearCuerpoCorreoHTML($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion);
        $mail->AltBody = self::crearCuerpoCorreoTexto($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion);
        
        $mail->send();
        error_log(" CORREO ENVIADO EXITOSAMENTE VÍA PHPMailer A: $correo");
        return true;
        
    } catch (\Exception $e) {
        error_log(" ERROR PHPMailer: " . $e->getMessage());
        return false;
    }
}

//  MÉTODO CON mail() básico (Fallback)
private static function enviarConMailBasico($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
    try {
        $asunto = "Recuperación de Contraseña - Sistema Rosquilleria";
        $mensaje = self::crearCuerpoCorreoHTML($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion);
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Sistema Rosquilleria <sistema@rosquilleria.com>" . "\r\n";
        $headers .= "Reply-To: no-reply@rosquilleria.com" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        $enviado = mail($correo, $asunto, $mensaje, $headers);
        
        if ($enviado) {
            error_log(" Correo enviado via mail() a: $correo");
            return true;
        } else {
            error_log(" Falló mail() para: $correo");
            return false;
        }
        
    } catch (\Exception $e) {
        error_log(" Error en mail básico: " . $e->getMessage());
        return false;
    }
}

// CREAR CUERPO HTML MEJORADO
private static function crearCuerpoCorreoHTML($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; border: 1px solid #ddd; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
            .content { padding: 20px; }
            .password-box { background: #f8f9fa; padding: 15px; border: 2px dashed #007bff; text-align: center; margin: 20px 0; font-size: 18px; font-weight: bold; border-radius: 5px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404; }
            .footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🔐 Recuperación de Contraseña</h2>
            </div>
            <div class='content'>
                <p>Hola <strong>$nombreUsuario</strong>,</p>
                <p>Se ha generado una contraseña temporal para tu cuenta en el <strong>Sistema Rosquilleria</strong>.</p>
                
                <div class='password-box'>
                    <strong>Usuario:</strong> $usuario<br>
                    <strong>Contraseña Temporal:</strong><br>
                    <span style='color: #dc3545; font-size: 24px;'>$passwordTemporal</span>
                </div>
                
                <div class='warning'>
                    <p><strong>⚠️ Información Importante:</strong></p>
                    <ul>
                        <li><strong>Válida hasta:</strong> $fechaExpiracion</li>
                        <li>Debes cambiar la contraseña inmediatamente al ingresar al sistema</li>
                        <li>Esta contraseña caducará automáticamente después de la fecha indicada</li>
                        <li>Si no solicitaste este cambio, contacta inmediatamente al administrador</li>
                    </ul>
                </div>
                
                <p>Para acceder al sistema, visita: <a href='http://localhost/sistema/public/index.php?route=login'>Sistema Rosquilleria</a></p>
                
                <p>Saludos cordiales,<br>
                <strong>Equipo de Soporte - Sistema Rosquilleria</strong></p>
            </div>
            <div class='footer'>
                <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                <p>&copy; " . date('Y') . " Sistema Rosquilleria. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

//CREAR CUERPO TEXTO PLANO
private static function crearCuerpoCorreoTexto($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
    return "
    Recuperación de Contraseña - Sistema Rosquilleria
    
    Hola $nombreUsuario,
    
    Se ha generado una contraseña temporal para tu cuenta en el Sistema Rosquilleria.
    
    Usuario: $usuario
    Contraseña Temporal: $passwordTemporal
    Válida hasta: $fechaExpiracion
    
    IMPORTANTE:
    - Debes cambiar la contraseña inmediatamente al ingresar al sistema
    - Esta contraseña caducará automáticamente después de la fecha indicada
    - Si no solicitaste este cambio, contacta inmediatamente al administrador
    
    Para acceder al sistema visita: http://localhost/sistema/public/index.php?route=login
    
    Saludos cordiales,
    Equipo de Soporte - Sistema Rosquilleria
    
    Este es un correo automático, por favor no respondas a este mensaje.
    ";
}

    // Generar contraseña temporal robusta
public static function generarContraseñaTemporal() {
    $minLongitud = 8; 
    $maxLongitud = 10;
    
    $longitud = rand($minLongitud, $maxLongitud);
    
    // Conjuntos de caracteres
    $minusculas = 'abcdefghijklmnopqrstuvwxyz';
    $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numeros = '0123456789';
    $especiales = '!@#$%^&*';
    
    // GARANTIZAR al menos uno de cada tipo
    $partes = [
        $minusculas[rand(0, strlen($minusculas) - 1)], // Minúscula
        $mayusculas[rand(0, strlen($mayusculas) - 1)], // Mayúscula
        $numeros[rand(0, strlen($numeros) - 1)],       // Número
        $especiales[rand(0, strlen($especiales) - 1)]  // Especial
    ];
    
    // Completar con caracteres aleatorios
    $todosCaracteres = $minusculas . $mayusculas . $numeros . $especiales;
    while (count($partes) < $longitud) {
        $partes[] = $todosCaracteres[rand(0, strlen($todosCaracteres) - 1)];
    }
    
    //  Mezclar bien y convertir a string
    shuffle($partes);
    $contraseña = implode('', $partes);
    
    //  VERIFICACIÓN EXTRA: Asegurar que cumple todos los requisitos
    $tieneMinuscula = preg_match('/[a-z]/', $contraseña);
    $tieneMayuscula = preg_match('/[A-Z]/', $contraseña);
    $tieneNumero = preg_match('/[0-9]/', $contraseña);
    $tieneEspecial = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $contraseña);
    
    // Si no cumple, regenerar (máximo 10 intentos)
    $intentos = 0;
    while ((!$tieneMinuscula || !$tieneMayuscula || !$tieneNumero || !$tieneEspecial) && $intentos < 10) {
        // Regenerar
        shuffle($partes);
        $contraseña = implode('', $partes);
        
        $tieneMinuscula = preg_match('/[a-z]/', $contraseña);
        $tieneMayuscula = preg_match('/[A-Z]/', $contraseña);
        $tieneNumero = preg_match('/[0-9]/', $contraseña);
        $tieneEspecial = preg_match('/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/', $contraseña);
        $intentos++;
    }
    
    error_log("🔐 Contraseña generada: $contraseña - Min: $tieneMinuscula, May: $tieneMayuscula, Num: $tieneNumero, Esp: $tieneEspecial");
    
    return $contraseña;
}

// En authModel.php - AGREGAR ESTE MÉTODO TEMPORAL PARA DEBUG
public static function debugRegistrarBitacora($idUsuario, $accion, $descripcion) {
    try {
        $con = connectionDB::getConnection();
        
        error_log(" DEBUG BITÁCORA - Intentando registrar: Usuario=$idUsuario, Acción=$accion");
        
        $sql = "INSERT INTO TBL_MS_BITACORA (FECHA, ID_USUARIO, ACCION, DESCRIPCION, CREADO_POR) 
                VALUES (NOW(), :id_usuario, :accion, :descripcion, :creado_por)";
        
        $query = $con->prepare($sql);
        $result = $query->execute([
            'id_usuario' => $idUsuario,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'creado_por' => 'SISTEMA'
        ]);
        
        if ($result) {
            $lastId = $con->lastInsertId();
            error_log(" DEBUG BITÁCORA - REGISTRO EXITOSO. ID: $lastId");
            return true;
        } else {
            error_log(" DEBUG BITÁCORA - ERROR EN EJECUCIÓN");
            return false;
        }
        
    } catch (\PDOException $e) {
        error_log(" DEBUG BITÁCORA - EXCEPCIÓN: " . $e->getMessage());
        return false;
    }
}

// Obtener datos de usuario por ID
public static function obtenerDatosUsuarioCompletosPorId($idUsuario) {
    try {
        $con = connectionDB::getConnection();
        $sql = "SELECT U.*, R.ROL 
                FROM TBL_MS_USUARIOS U 
                INNER JOIN TBL_MS_ROLES R ON U.ID_ROL = R.ID_ROL 
                WHERE U.ID_USUARIO = :id_usuario";
        
        $query = $con->prepare($sql);
        $query->execute(['id_usuario' => $idUsuario]);
        
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("authModel::obtenerDatosUsuarioCompletosPorId -> " . $e->getMessage());
        return null;
    }
}

// Método para actualizar estado después de cambiar contraseña
// Método para actualizar estado después de cambiar contraseña
public static function actualizarEstadoUsuario($idUsuario) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "UPDATE TBL_MS_USUARIOS 
                SET ESTADO_USUARIO = 'ACTIVO', 
                    PRIMER_INGRESO = 1,
                    FECHA_MODIFICACION = NOW(),
                    MODIFICADO_POR = 'SISTEMA'
                WHERE ID_USUARIO = :id_usuario";
        
        $query = $con->prepare($sql);
        $result = $query->execute(['id_usuario' => $idUsuario]);
        
        error_log(" Estado actualizado para usuario $idUsuario: ACTIVO, PRIMER_INGRESO=1");
        
        return $result;
        
    } catch (\PDOException $e) {
        error_log("Error en actualizarEstadoUsuario: " . $e->getMessage());
        return false;
    }
}

public static function obtenerFotoPerfil($idUsuario) {
    try {
        $con = connectionDB::getConnection();
        $sql = "SELECT FOTO_PERFIL FROM TBL_MS_USUARIOS WHERE ID_USUARIO = :id_usuario";
        $query = $con->prepare($sql);
        $query->execute(['id_usuario' => $idUsuario]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['FOTO_PERFIL'] ?? 'perfil.jpg';
        
    } catch (\PDOException $e) {
        error_log("authModel::obtenerFotoPerfil -> " . $e->getMessage());
        return 'perfil.jpg';
    }
}


}