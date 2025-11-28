<?php

namespace App\config;

class TwoFactorAuth {
    
    // Generar código de 6 dígitos
    public static function generarCodigo() {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    // Enviar código por correo
    public static function enviarCodigoCorreo($correo, $nombreUsuario, $codigo) {
        try {
            error_log("🚀 ENVIANDO CÓDIGO 2FA A: $correo");
            
            // Usar PHPMailer como ya lo tienes configurado
            $phpmailerPath = __DIR__ . '/../../vendor/phpmailer/PHPMailer.php';
            
            if (!file_exists($phpmailerPath)) {
                error_log("❌ PHPMailer no encontrado para 2FA");
                return false;
            }
            
            require_once $phpmailerPath;
            require_once __DIR__ . '/../../vendor/phpmailer/SMTP.php';
            require_once __DIR__ . '/../../vendor/phpmailer/Exception.php';
            
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración SMTP (usa la misma que ya tienes)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'denislopez1206@gmail.com';
            $mail->Password = 'tqnm mcgv fkcm ampp';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
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
            $mail->Subject = '🔐 Código de Verificación - Sistema Rosquilleria';
            $mail->Body = self::crearCuerpoCorreo2FA($nombreUsuario, $codigo);
            $mail->AltBody = self::crearCuerpoCorreo2FATexto($nombreUsuario, $codigo);
            
            $mail->send();
            error_log("✅ CÓDIGO 2FA ENVIADO EXITOSAMENTE A: $correo");
            return true;
            
        } catch (\Exception $e) {
            error_log("❌ ERROR ENVIANDO CÓDIGO 2FA: " . $e->getMessage());
            return false;
        }
    }
    
    private static function crearCuerpoCorreo2FA($nombreUsuario, $codigo) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; border: 1px solid #ddd; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; margin: -20px -20px 20px -20px; }
                .content { padding: 20px; }
                .code-box { background: #f8f9fa; padding: 20px; border: 3px dashed #28a745; text-align: center; margin: 20px 0; font-size: 32px; font-weight: bold; letter-spacing: 5px; border-radius: 10px; color: #dc3545; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404; }
                .footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🔐 Verificación en Dos Pasos</h2>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombreUsuario</strong>,</p>
                    <p>Se ha solicitado el inicio de sesión en tu cuenta del <strong>Sistema Rosquilleria</strong>.</p>
                    
                    <div class='code-box'>
                        $codigo
                    </div>
                    
                    <div class='warning'>
                        <p><strong>⚠️ Información Importante:</strong></p>
                        <ul>
                            <li>Este código es válido por <strong>10 minutos</strong></li>
                            <li>No compartas este código con nadie</li>
                            <li>Si no solicitaste este acceso, contacta inmediatamente al administrador</li>
                        </ul>
                    </div>
                    
                    <p>Ingresa este código en la ventana de verificación para completar tu inicio de sesión.</p>
                    
                    <p>Saludos cordiales,<br><strong>Equipo de Seguridad - Sistema Rosquilleria</strong></p>
                </div>
                <div class='footer'>
                    <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private static function crearCuerpoCorreo2FATexto($nombreUsuario, $codigo) {
        return "
        Verificación en Dos Pasos - Sistema Rosquilleria
        
        Hola $nombreUsuario,
        
        Se ha solicitado el inicio de sesión en tu cuenta.
        
        Código de Verificación: $codigo
        Válido por: 10 minutos
        
        IMPORTANTE:
        - No compartas este código con nadie
        - Si no solicitaste este acceso, contacta al administrador
        
        Ingresa este código en la ventana de verificación para completar tu inicio de sesión.
        
        Saludos cordiales,
        Equipo de Seguridad - Sistema Rosquilleria
        
        Este es un correo automático, por favor no respondas a este mensaje.
        ";
    }
    
    // Validar que el código no haya expirado (10 minutos)
    public static function validarExpiracion($timestamp) {
        $expiracion = 10 * 60; // 10 minutos en segundos
        return (time() - $timestamp) <= $expiracion;
    }
}