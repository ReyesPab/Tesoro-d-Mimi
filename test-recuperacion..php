<?php
// test-phpmailer-final.php - Verificar PHPMailer
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Verificación Final de PHPMailer</h1>";

$phpmailerPath = __DIR__ . '/vendor/phpmailer/PHPMailer.php';
echo "<p><strong>Ruta PHPMailer:</strong> $phpmailerPath</p>";

if (file_exists($phpmailerPath)) {
    echo "<p style='color: green;'>✅ PHPMailer encontrado</p>";
    
    // Probar carga
    require_once $phpmailerPath;
    require_once __DIR__ . '/vendor/phpmailer/SMTP.php';
    require_once __DIR__ . '/vendor/phpmailer/Exception.php';
    
    echo "<p style='color: green;'>✅ Clases cargadas correctamente</p>";
    
    // Probar EmailService
    require_once __DIR__ . '/src/config/EmailService.php';
    
    echo "<p style='color: green;'>✅ EmailService cargado</p>";
    
    // Probar envío
    try {
        $resultado = App\config\EmailService::enviarCorreoRecuperacion(
            'denislopez1206@gmail.com',
            'Usuario Test',
            'TEST',
            'Test123!',
            date('Y-m-d H:i:s', strtotime('+24 hours'))
        );
        
        if ($resultado) {
            echo '<div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 15px 0;">';
            echo '<h3>✅ ¡EmailService funciona correctamente!</h3>';
            echo '<p>El correo fue enviado exitosamente.</p>';
            echo '</div>';
        } else {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 15px 0;">';
            echo '<h3>❌ EmailService falló</h3>';
            echo '<p>Revisa los logs para más información.</p>';
            echo '</div>';
        }
        
    } catch (Exception $e) {
        echo '<div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 15px 0;">';
        echo '<h3>❌ Error en EmailService</h3>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '</div>';
    }
    
} else {
    echo "<p style='color: red;'>❌ PHPMailer NO encontrado</p>";
    echo "<p>Verifica que los archivos estén en: vendor/phpmailer/</p>";
}
?>