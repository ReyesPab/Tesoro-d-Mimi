<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Preguntas de Seguridad</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
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

        select, input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #667eea;
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

        .password-fields {
            display: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
        }

        .step-indicator {
            text-align: center;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña - Preguntas de Seguridad</h2>
        
        <div id="alert" class="alert"></div>
        <div id="loading" class="loading">Validando...</div>
        
        <!-- Paso 1: Preguntas de Seguridad -->
        <div id="preguntas-step">
            <div class="step-indicator" id="step-indicator">Pregunta 1 de 3</div>
            
            <form id="preguntasForm">
                <input type="hidden" id="ID_USUARIO" name="ID_USUARIO" value="<?= $_GET['id'] ?? '' ?>">
                
                <div id="preguntas-container">
                    <!-- Las preguntas se cargarán dinámicamente aquí -->
                </div>
                
                <button type="submit" class="btn" id="siguienteBtn">Siguiente</button>
            </form>
        </div>
        
        <!-- Paso 2: Nueva Contraseña -->
        <div id="password-step" class="password-fields">
            <form id="passwordForm">
                <input type="hidden" id="ID_USUARIO_PASSWORD" name="ID_USUARIO">
                
                <div class="form-group">
                    <label for="NUEVA_PASSWORD">Nueva Contraseña:</label>
                    <input type="password" id="NUEVA_PASSWORD" name="NUEVA_PASSWORD" required 
                           placeholder="Ingrese nueva contraseña">
                </div>
                
                <div class="form-group">
                    <label for="CONFIRMAR_PASSWORD">Confirmar Contraseña:</label>
                    <input type="password" id="CONFIRMAR_PASSWORD" name="CONFIRMAR_PASSWORD" required 
                           placeholder="Confirme la contraseña">
                </div>
                
                <button type="submit" class="btn" id="cambiarBtn">Cambiar Contraseña</button>
            </form>
        </div>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="/sistema/public/index.php?route=login">Volver al Login</a>
        </div>
    </div>

    <script>
        let preguntas = [];
        let preguntaActual = 0;
        let respuestasUsuario = [];

        // Cargar preguntas al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            const ID_USUARIO = document.getElementById('ID_USUARIO').value;
            if (ID_USUARIO) {
                cargarPreguntasUsuario(ID_USUARIO);
            }
        });

        async function cargarPreguntasUsuario(ID_USUARIO) {
            showLoading(true);
            
            try {
                const response = await fetch(`/sistema/public/index.php?route=auth&caso=obtener-preguntas-usuario&ID_USUARIO=${ID_USUARIO}`);
                const result = await response.json();
                
                if (result.status === '200') {
                    preguntas = result.data.preguntas || [];
                    if (preguntas.length > 0) {
                        mostrarPreguntaActual();
                    } else {
                        showAlert('El usuario no tiene preguntas de seguridad configuradas', 'error');
                    }
                } else {
                    showAlert(result.message || 'Error al cargar preguntas', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión', 'error');
            } finally {
                showLoading(false);
            }
        }

        function mostrarPreguntaActual() {
            const container = document.getElementById('preguntas-container');
            const stepIndicator = document.getElementById('step-indicator');
            
            if (preguntaActual < preguntas.length) {
                const pregunta = preguntas[preguntaActual];
                
                stepIndicator.textContent = `Pregunta ${preguntaActual + 1} de ${preguntas.length}`;
                
                container.innerHTML = `
                    <div class="form-group">
                        <label>${pregunta.PREGUNTA}</label>
                        <input type="hidden" name="ID_PREGUNTA" value="${pregunta.ID_PREGUNTA}">
                        <input type="text" name="RESPUESTA" required 
                               placeholder="Ingrese su respuesta" 
                               oninput="this.value = this.value.toUpperCase().replace(/\s{2,}/g, ' ')">
                    </div>
                `;
                
                document.getElementById('siguienteBtn').textContent = 
                    preguntaActual === preguntas.length - 1 ? 'Validar Respuestas' : 'Siguiente';
                    
            } else {
                // Todas las preguntas respondidas, validar
                validarRespuestas();
            }
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
        }

        function showLoading(show) {
            const loading = document.getElementById('loading');
            const siguienteBtn = document.getElementById('siguienteBtn');
            
            if (show) {
                loading.style.display = 'block';
                siguienteBtn.disabled = true;
            } else {
                loading.style.display = 'none';
                siguienteBtn.disabled = false;
            }
        }

        // Manejar envío de formulario de preguntas
        document.getElementById('preguntasForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const ID_PREGUNTA = document.querySelector('input[name="ID_PREGUNTA"]').value;
            const RESPUESTA = document.querySelector('input[name="RESPUESTA"]').value.trim();
            
            if (!RESPUESTA) {
                showAlert('Por favor ingrese su respuesta', 'error');
                return;
            }
            
            // Guardar respuesta
            respuestasUsuario.push({
                ID_PREGUNTA: parseInt(ID_PREGUNTA),
                RESPUESTA: RESPUESTA.toUpperCase()
            });
            
            preguntaActual++;
            mostrarPreguntaActual();
        });

        async function validarRespuestas() {
    showLoading(true);
    
    const ID_USUARIO = document.getElementById('ID_USUARIO').value;
    
    try {
        const response = await fetch('/sistema/public/index.php?route=auth&caso=validar-respuestas-recuperacion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ID_USUARIO: parseInt(ID_USUARIO),
                RESPUESTAS: respuestasUsuario
            })
        });
        
        const result = await response.json();
        
        if (result.status === '200') {
            showAlert('Respuestas correctas. Ahora puede establecer su nueva contraseña.', 'success');
            
            // Mostrar formulario de nueva contraseña
            document.getElementById('preguntas-step').style.display = 'none';
            document.getElementById('password-step').style.display = 'block';
            document.getElementById('ID_USUARIO_PASSWORD').value = ID_USUARIO;
            
        } else {
            showAlert(result.message || 'Error al validar respuestas', 'error');
            // Reiniciar
            preguntaActual = 0;
            respuestasUsuario = [];
            mostrarPreguntaActual();
        }
        
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    } finally {
        showLoading(false);
    }
}


        // Manejar cambio de contraseña
        async function cambiarContraseña() {
    const ID_USUARIO = document.getElementById('ID_USUARIO_PASSWORD').value;
    const NUEVA_PASSWORD = document.getElementById('NUEVA_PASSWORD').value;
    const CONFIRMAR_PASSWORD = document.getElementById('CONFIRMAR_PASSWORD').value;
    
    if (NUEVA_PASSWORD !== CONFIRMAR_PASSWORD) {
        showAlert('Las contraseñas no coinciden', 'error');
        return;
    }
    
    showLoading(true);
    
    try {
        const response = await fetch('/sistema/public/index.php?route=auth&caso=cambiar-password-recuperacion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                ID_USUARIO: parseInt(ID_USUARIO),
                NUEVA_PASSWORD: NUEVA_PASSWORD,
                CONFIRMAR_PASSWORD: CONFIRMAR_PASSWORD
            })
        });
        
        const result = await response.json();
        
        if (result.status === '200') {
            showAlert('Contraseña cambiada exitosamente. Redirigiendo al login...', 'success');
            
            setTimeout(() => {
                window.location.href = '/sistema/public/index.php?route=login';
            }, 2000);
            
        } else {
            showAlert(result.message || 'Error al cambiar contraseña', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'error');
    } finally {
        showLoading(false);
    }
}
        ;
    </script>
</body>
</html>