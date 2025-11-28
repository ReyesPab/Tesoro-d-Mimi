<?php
session_start();
require_once __DIR__ . '/../config/SessionHelper.php';

// Verificar si el usuario está logueado
if (!App\config\SessionHelper::isLoggedIn()) {
    header('Location: /sistema/public/login');
    exit;
}

$usuario_nombre = $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? 'Invitado';
$id_usuario = $_SESSION['id_usuario'] ?? $_SESSION['ID_USUARIO'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Sistema</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: bold;
        }
        
        .password-container {
            position: relative;
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background: #5a6fd8;
        }
        
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #363;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
            color: #667eea;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
        }
        
        /* Estilos para los requisitos de contraseña */
        .requirement-met {
            color: green;
            list-style-type: '';
        }
        
        .requirement-not-met {
            color: red;
            list-style-type: '';
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Cambiar Contraseña</h2>
        
        <div id="alert" class="alert"></div>
        <div id="loading" class="loading">Procesando...</div>
        
        <form id="changePasswordForm">
            <div class="form-group">
                <label for="currentPassword">Contraseña Actual:</label>
                <div class="password-container">
                    <input type="password" id="currentPassword" name="current_password" 
                           placeholder="Ingrese su contraseña actual" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('currentPassword')">
                        👁️
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="newPassword">Nueva Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="newPassword" name="new_password" 
                           placeholder="Ingrese nueva contraseña" required
                           oninput="validarPasswordEnTiempoReal(this.value)">
                    <button type="button" class="toggle-password" onclick="togglePassword('newPassword')">
                        👁️
                    </button>
                </div>
                <div id="passwordRequirements" class="password-requirements" style="display: none;">
                    <strong>Requisitos de contraseña:</strong>
                    <ul>
                        <li id="reqLength">Mínimo 5 caracteres, máximo 10</li>
                        <li id="reqUpper">Al menos una mayúscula</li>
                        <li id="reqLower">Al menos una minúscula</li>
                        <li id="reqNumber">Al menos un número</li>
                        <li id="reqSpecial">Al menos un carácter especial</li>
                        <li id="reqNoSpaces">Sin espacios</li>
                    </ul>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirmar Nueva Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="confirmPassword" name="confirm_password" 
                           placeholder="Confirme su nueva contraseña" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                        👁️
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn" id="submitBtn">Cambiar Contraseña</button>
        </form>
        
        
    </div>

    <script>
        // Pasar el ID del usuario desde PHP a JavaScript
        const userId = <?php echo json_encode($id_usuario); ?>;
        
        // DEBUG: Verificar que el userId existe
        console.log("User ID desde PHP:", userId);
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            
            // Ocultar alerta después de 5 segundos
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
        
        function showLoading(show) {
            const loading = document.getElementById('loading');
            const submitBtn = document.getElementById('submitBtn');
            
            if (show) {
                loading.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Procesando...';
            } else {
                loading.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Cambiar Contraseña';
            }
        }
        
        function validarPasswordEnTiempoReal(password) {
            const requirements = document.getElementById('passwordRequirements');
            
            if (password.length > 0) {
                requirements.style.display = 'block';
                
                // Validar cada requisito
                document.getElementById('reqLength').className = 
                    (password.length >= 5 && password.length <= 10) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqUpper').className = 
                    /[A-Z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqLower').className = 
                    /[a-z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqNumber').className = 
                    /[0-9]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqSpecial').className = 
                    /[!@#$%^&*()\-_=+{};:,<.>]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqNoSpaces').className = 
                    !/\s/.test(password) ? 'requirement-met' : 'requirement-not-met';
            } else {
                requirements.style.display = 'none';
            }
        }
        
        function validarPassword(password, confirmPassword, currentPassword) {
            const errores = [];
            
            if (password.length < 5) {
                errores.push('La contraseña debe tener al menos 5 caracteres');
            }

            if (password.length > 10) {
                errores.push('La contraseña no puede tener más de 10 caracteres');
            }
            
            if (!/[A-Z]/.test(password)) {
                errores.push('Debe contener al menos una mayúscula');
            }
            
            if (!/[a-z]/.test(password)) {
                errores.push('Debe contener al menos una minúscula');
            }
            
            if (!/[0-9]/.test(password)) {
                errores.push('Debe contener al menos un número');
            }
            
            if (!/[!@#$%^&*()\-_=+{};:,<.>]/.test(password)) {
                errores.push('Debe contener al menos un carácter especial');
            }
            
            if (/\s/.test(password)) {
                errores.push('No puede contener espacios');
            }
            
            if (password !== confirmPassword) {
                errores.push('Las contraseñas no coinciden');
            }
            
            if (password === currentPassword) {
                errores.push('La nueva contraseña no puede ser igual a la actual');
            }
            
            return errores;
        }
        
        document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Verificar que userId existe
    if (!userId) {
        showAlert('Error: No se encontró la sesión del usuario. Por favor, inicie sesión nuevamente.', 'error');
        setTimeout(() => {
            window.location.href = '/sistema/public/login';
        }, 3000);
        return;
    }
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validar campos vacíos
    if (!currentPassword || !newPassword || !confirmPassword) {
        showAlert('Error: Todos los campos son obligatorios', 'error');
        return;
    }
    
    // Validar contraseña
    const errores = validarPassword(newPassword, confirmPassword, currentPassword);
    if (errores.length > 0) {
        showAlert('Error: ' + errores[0], 'error');
        return;
    }
    
    try {
        showLoading(true);
        
        console.log("🔑 Enviando cambio de contraseña para usuario:", userId);
        console.log("📤 Datos enviados:", {
            id_usuario: parseInt(userId),
            nueva_password: newPassword,
            password_actual: currentPassword
        });
        
        const response = await fetch('/sistema/public/index.php?route=auth&caso=cambiar-password-dashboard', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_usuario: parseInt(userId),
                nueva_password: newPassword,
                password_actual: currentPassword
            })
        });
        
        const result = await response.json();
        console.log("📥 Respuesta del servidor:", result);
        
        if (result.status === '200') {
            showAlert('✅ ' + result.message, 'success');
            
            // Limpiar formulario
            document.getElementById('changePasswordForm').reset();
            document.getElementById('passwordRequirements').style.display = 'none';
            
            // Verificar si el estado fue actualizado
            setTimeout(() => {
                console.log("🔄 Redirigiendo a inicio...");
                window.location.href = '/sistema/public/inicio';
            }, 2000);
            
        } else {
            showAlert('❌ Error: ' + result.message, 'error');
        }
        
    } catch (error) {
        console.error('💥 Error en la solicitud:', error);
        showAlert('❌ Error de conexión con el servidor', 'error');
    } finally {
        showLoading(false);
    }
});
        
        // Si no hay usuario ID, redirigir al login después de mostrar mensaje
        if (!userId) {
            showAlert('Sesión no encontrada. Redirigiendo al login...', 'error');
            setTimeout(() => {
                
                window.location.href = '/sistema/public/login';
            }, 2000);
        }
        
        // Agregar estilos dinámicamente para mejor visualización
        const style = document.createElement('style');
        style.textContent = `
            .requirement-met {
                color: green;
                list-style-type: '✅ ';
            }
            .requirement-not-met {
                color: red;
                list-style-type: '';
            }
            #passwordRequirements ul {
                list-style: none;
                padding-left: 0;
                margin-top: 0.5rem;
            }
            #passwordRequirements li {
                margin-bottom: 0.25rem;
                font-size: 0.75rem;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>