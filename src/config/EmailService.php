<?php

namespace App\config;

class EmailService {
    
    public static function enviarCorreoRecuperacion($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
        try {
            error_log("🚀 INICIANDO ENVÍO DE CORREO A: $correo");
            
            // Intentar con PHPMailer primero
            if (self::enviarConPHPMailer($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion)) {
                return true;
            }
            
            // Si PHPMailer falla, mostrar la contraseña como fallback
            error_log("🔐 CONTRASEÑA TEMPORAL (fallback): $passwordTemporal");
            return false;
            
        } catch (\Exception $e) {
            error_log("💥 ERROR CRÍTICO en EmailService: " . $e->getMessage());
            return false;
        }
    }
    
    private static function enviarConPHPMailer($correo, $nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
        try {
            // 🔥 RUTAS CORRECTAS - Los archivos están en vendor/phpmailer/ (no en src/)
            $phpmailerPath = __DIR__ . '/../../vendor/phpmailer/PHPMailer.php';
            $smtpPath = __DIR__ . '/../../vendor/phpmailer/SMTP.php';
            $exceptionPath = __DIR__ . '/../../vendor/phpmailer/Exception.php';
            
            error_log("🔍 Buscando PHPMailer en: $phpmailerPath");
            
            // Verificar que existan los archivos
            if (!file_exists($phpmailerPath)) {
                error_log("❌ PHPMailer no encontrado en: $phpmailerPath");
                // Listar archivos disponibles para debug
                $vendorPath = __DIR__ . '/../../vendor/phpmailer/';
                if (file_exists($vendorPath)) {
                    $archivos = scandir($vendorPath);
                    error_log("📁 Archivos en vendor/phpmailer/: " . implode(', ', $archivos));
                }
                return false;
            }
            
            error_log("✅ PHPMailer encontrado, cargando...");
            
            // Incluir PHPMailer con rutas CORRECTAS
            require_once $phpmailerPath;
            require_once $smtpPath;
            require_once $exceptionPath;
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // 🔥 CREDENCIALES ACTUALIZADAS - LAS QUE FUNCIONARON
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denislopez1206@gmail.com';  // ✅ Tu email real
            $mail->Password = 'tqnm mcgv fkcm ampp';       // ✅ Contraseña de aplicación
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Configuración para evitar problemas de SSL en desarrollo
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
            error_log("✅ CORREO ENVIADO EXITOSAMENTE VÍA PHPMailer A: $correo");
            return true;
            
        } catch (\Exception $e) {
            error_log("❌ ERROR PHPMailer: " . $e->getMessage());
            return false;
        }
    }
    
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
                .password-box { background: #f8f9fa; padding: 15px; border: 2px dashed #007bff; text-align: center; margin: 20px 0; font-size: 18px; border-radius: 5px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404; }
                .footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; color: #666; }
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
                        </ul>
                    </div>
                    
                    <p>Saludos cordiales,<br><strong>Equipo de Soporte - Sistema Rosquilleria</strong></p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private static function crearCuerpoCorreoTexto($nombreUsuario, $usuario, $passwordTemporal, $fechaExpiracion) {
        return "
        Recuperación de Contraseña - Sistema Rosquilleria
        
        Hola $nombreUsuario,
        
        Se ha generado una contraseña temporal para tu cuenta:
        
        Usuario: $usuario
        Contraseña Temporal: $passwordTemporal
        Válida hasta: $fechaExpiracion
        
        IMPORTANTE:
        - Debes cambiar la contraseña inmediatamente al ingresar
        - Esta contraseña caducará después de la fecha indicada
        
        Saludos cordiales,
        Equipo de Soporte - Sistema Rosquilleria
        ";
    }
}