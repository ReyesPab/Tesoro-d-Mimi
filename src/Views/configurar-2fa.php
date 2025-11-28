<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Autenticación en Dos Pasos - Sistema</title>
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
        
        .status-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .status-active {
            color: #28a745;
        }
        
        .status-inactive {
            color: #dc3545;
        }
        
        .status-text {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .status-description {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #1e7e34;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
            color: #007bff;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .info-box h4 {
            color: #0056b3;
            margin-bottom: 0.5rem;
        }
        
        .info-box ul {
            padding-left: 1.5rem;
            color: #333;
        }
        
        .info-box li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Configurar Autenticación en Dos Pasos</h2>
        
        <div id="alert" class="alert"></div>
        <div id="loading" class="loading">Procesando...</div>
        
        <div class="status-card">
            <div class="status-icon" id="statusIcon">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="status-text" id="statusText">
                <!-- Se llenará dinámicamente -->
            </div>
            <div class="status-description" id="statusDescription">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
        
        <div class="info-box">
            <h4>¿Qué es la Autenticación en Dos Pasos?</h4>
            <ul>
                <li>Recibirás un código de 6 dígitos por correo electrónico cada vez que inicies sesión</li>
                <li>Mayor seguridad para tu cuenta</li>
                <li>Protección adicional contra accesos no autorizados</li>
                <li>El código expira después de 10 minutos</li>
            </ul>
        </div>
        
        <button type="button" class="btn" id="toggleBtn" onclick="toggle2FA()">
            <!-- Se llenará dinámicamente -->
        </button>
        
        <div class="back-link">
            <a href="/sistema/public/inicio.php">← Volver al Inicio</a>
        </div>
    </div>

    <script>
        let current2FAStatus = 0;
        let userId = null;
        
        // Obtener el ID del usuario de sessionStorage
        function getUserId() {
            const id = sessionStorage.getItem('user_id');
            if (!id) {
                showAlert('Error: No se encontró la sesión del usuario. Por favor, inicie sesión nuevamente.', 'error');
                setTimeout(() => {
                    window.location.href = '/sistema/public/login.php';
                }, 3000);
                return null;
            }
            return id;
        }
        
        // Cargar estado actual del 2FA
        // Cargar estado actual del 2FA
async function load2FAStatus() {
    userId = getUserId();
    if (!userId) return;
    
    showLoading(true);
    
    try {
        const response = await fetch('/sistema/public/index.php?route=user&caso=obtener-estado-2fa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_usuario: parseInt(userId)
            })
        });
        
        const result = await response.json();
        
        if (result.status === '200') {
            current2FAStatus = result.data.habilitar_2fa;
            connectionError = false;
            updateConnectionStatus();
            updateUI();
        } else {
            showAlert('Error al cargar la configuración: ' + result.message, 'error');
            connectionError = true;
            updateConnectionStatus();
        }
        
    } catch (error) {
        console.error('Error:', error);
        connectionError = true;
        updateConnectionStatus();
        showAlert('Error de conexión al cargar la configuración', 'error');
    } finally {
        showLoading(false);
    }
}
        
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
        
        function showLoading(show) {
            const loading = document.getElementById('loading');
            const toggleBtn = document.getElementById('toggleBtn');
            
            if (show) {
                loading.style.display = 'block';
                toggleBtn.disabled = true;
            } else {
                loading.style.display = 'none';
                toggleBtn.disabled = false;
            }
        }
        
        // Cargar el estado al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            load2FAStatus();
        });
    </script>
</body>
</html>