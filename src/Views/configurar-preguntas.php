<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Primer Ingreso - Sistema</title>
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
        
        .config-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 600px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ddd;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
        }

        .step.active .step-number {
            background: #667eea;
            color: white;
        }

        .step-line {
            position: absolute;
            top: 15px;
            left: 15%;
            right: 15%;
            height: 2px;
            background: #ddd;
            z-index: 1;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
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

        .password-container {
            position: relative;
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
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin: 0 0.5rem;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
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

        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="config-container">
        <h2>Configuración de Primer Ingreso</h2>
        
        <div class="step-indicator">
            <div class="step-line"></div>
            <div class="step active" id="step1">
                <div class="step-number">1</div>
                <div>Preguntas Seguridad</div>
            </div>
            <div class="step" id="step2">
                <div class="step-number">2</div>
                <div>Nueva Contraseña</div>
            </div>
        </div>
        
        <div id="alert" class="alert"></div>
        <div id="loading" class="loading">Cargando...</div>
        
        <!-- Paso 1: Preguntas de Seguridad -->
        <div class="form-step active" id="stepForm1">
            <form id="preguntasForm">
                <div class="form-group">
                    <label for="pregunta1">Pregunta de Seguridad 1:</label>
                    <select id="pregunta1" name="preguntas[0][id_pregunta]" required>
                        <option value="">Seleccione una pregunta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="respuesta1">Respuesta:</label>
                    <input type="text" id="respuesta1" name="preguntas[0][respuesta]" 
                           maxlength="255" placeholder="Ingrese su respuesta" required
                           oninput="this.value = this.value.toUpperCase()">
                </div>
                
                <div class="form-group">
                    <label for="pregunta2">Pregunta de Seguridad 2:</label>
                    <select id="pregunta2" name="preguntas[1][id_pregunta]" required>
                        <option value="">Seleccione una pregunta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="respuesta2">Respuesta:</label>
                    <input type="text" id="respuesta2" name="preguntas[1][respuesta]" 
                           maxlength="255" placeholder="Ingrese su respuesta" required
                           oninput="this.value = this.value.toUpperCase()">
                </div>
                
                <div class="form-group">
                    <label for="pregunta3">Pregunta de Seguridad 3:</label>
                    <select id="pregunta3" name="preguntas[2][id_pregunta]" required>
                        <option value="">Seleccione una pregunta</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="respuesta3">Respuesta:</label>
                    <input type="text" id="respuesta3" name="preguntas[2][respuesta]" 
                           maxlength="255" placeholder="Ingrese su respuesta" required
                           oninput="this.value = this.value.toUpperCase()">
                </div>
            </form>
            
            <div class="button-group">
                <div></div> <!-- Espacio vacío para alineación -->
                <button type="button" class="btn" onclick="siguientePaso()">Siguiente</button>
            </div>
        </div>
        
        <!-- Paso 2: Nueva Contraseña -->
        <div class="form-step" id="stepForm2">
            <form id="passwordForm">
                <div class="form-group">
                    <label for="nuevaPassword">Nueva Contraseña:</label>
                    <div class="password-container">
                        <input type="password" id="nuevaPassword" name="nueva_password" 
                               maxlength="10" placeholder="Ingrese nueva contraseña" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('nuevaPassword')">
                            👁️
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirmarPassword">Confirmar Contraseña:</label>
                    <div class="password-container">
                        <input type="password" id="confirmarPassword" name="confirmar_password" 
                               maxlength="10" placeholder="Confirme su contraseña" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirmarPassword')">
                            👁️
                        </button>
                    </div>
                </div>
                
                <div class="password-requirements">
                    <strong>La contraseña debe contener:</strong>
                    <ul>
                        <li>Mínimo 5 caracteres, máximo 10</li>
                        <li>Al menos una letra mayúscula</li>
                        <li>Al menos una letra minúscula</li>
                        <li>Al menos un número</li>
                        <li>Al menos un carácter especial (!@#$%^&* etc.)</li>
                        <li>Sin espacios</li>
                        <li>No puede ser igual al usuario</li>
                    </ul>
                </div>
            </form>
            
            <div class="button-group">
                <button type="button" class="btn btn-secondary" onclick="anteriorPaso()">Anterior</button>
                <button type="button" class="btn" onclick="finalizarConfiguracion()">Finalizar</button>
            </div>
        </div>
    </div>

    <script>
        let preguntasDisponibles = [];
        const userId = new URLSearchParams(window.location.search).get('id');

        // Cargar preguntas al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            if (!userId) {
                showAlert('Error: ID de usuario no especificado', 'error');
                return;
            }
            cargarPreguntas();
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
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
            loading.style.display = show ? 'block' : 'none';
        }

        async function cargarPreguntas() {
            try {
                showLoading(true);
                // 🔥 ACTUALIZADO: Ruta del sistema
                const response = await fetch('/sistema/public/index.php?route=auth&caso=obtener-preguntas');
                const result = await response.json();
                
                if (result.status === '200') {
                    preguntasDisponibles = result.data.preguntas || [];
                    llenarSelectPreguntas();
                } else {
                    showAlert('Error al cargar preguntas', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión', 'error');
            } finally {
                showLoading(false);
            }
        }

        function llenarSelectPreguntas() {
            const selects = ['pregunta1', 'pregunta2', 'pregunta3'];
            
            selects.forEach((selectId, index) => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Seleccione una pregunta</option>';
                
                preguntasDisponibles.forEach(pregunta => {
                    const option = document.createElement('option');
                    // 🔥 ACTUALIZADO: Campo en mayúsculas
                    option.value = pregunta.ID_PREGUNTA || pregunta.Id_Pregunta;
                    option.textContent = pregunta.PREGUNTA || pregunta.Pregunta;
                    select.appendChild(option);
                });
            });
        }

        function siguientePaso() {
            // Validar preguntas
            const formData = new FormData(document.getElementById('preguntasForm'));
            const preguntasSeleccionadas = new Set();
            let todasValidas = true;

            for (let i = 0; i < 3; i++) {
                const preguntaId = document.getElementById(`pregunta${i+1}`).value;
                const respuesta = document.getElementById(`respuesta${i+1}`).value.trim();
                
                if (!preguntaId || !respuesta) {
                    showAlert('Complete todas las preguntas y respuestas', 'error');
                    todasValidas = false;
                    break;
                }
                
                if (preguntasSeleccionadas.has(preguntaId)) {
                    showAlert('No puede seleccionar la misma pregunta más de una vez', 'error');
                    todasValidas = false;
                    break;
                }
                
                preguntasSeleccionadas.add(preguntaId);
                
                // Validar respuesta (solo un espacio entre palabras)
                if (respuesta.includes('  ')) {
                    showAlert('Las respuestas no pueden tener espacios múltiples', 'error');
                    todasValidas = false;
                    break;
                }
            }

            if (todasValidas) {
                // Cambiar al paso 2
                document.getElementById('stepForm1').classList.remove('active');
                document.getElementById('stepForm2').classList.add('active');
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
            }
        }

        function anteriorPaso() {
            document.getElementById('stepForm2').classList.remove('active');
            document.getElementById('stepForm1').classList.add('active');
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
        }

        function validarPassword(password, confirmarPassword) {
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
            
            if (password !== confirmarPassword) {
                errores.push('Las contraseñas no coinciden');
            }
            
            return errores;
        }

        async function finalizarConfiguracion() {
            const nuevaPassword = document.getElementById('nuevaPassword').value;
            const confirmarPassword = document.getElementById('confirmarPassword').value;
            
            console.log("🔍 FINALIZANDO CONFIGURACIÓN - DEBUG ACTIVADO");
            console.log("🔑 User ID:", userId);
            
            // Validar contraseña
            const errores = validarPassword(nuevaPassword, confirmarPassword);
            if (errores.length > 0) {
                showAlert('Error en contraseña: ' + errores[0], 'error');
                return;
            }
            
            try {
                showLoading(true);
                
                // 🔥 PASO 1: Obtener datos de las preguntas
                console.log("📝 PASO 1: Recolectando preguntas...");
                const preguntasRespuestas = [];
                for (let i = 0; i < 3; i++) {
                    const preguntaId = document.getElementById(`pregunta${i+1}`).value;
                    const respuesta = document.getElementById(`respuesta${i+1}`).value.trim().toUpperCase();
                    
                    console.log(`   Pregunta ${i+1}: ID=${preguntaId}, Respuesta=${respuesta}`);
                    
                    preguntasRespuestas.push({
                        id_pregunta: parseInt(preguntaId),
                        respuesta: respuesta
                    });
                }
                
                console.log("📦 Datos a enviar para preguntas:", {
                    id_usuario: parseInt(userId),
                    preguntas_respuestas: preguntasRespuestas
                });
                
                // 🔥 PASO 2: Guardar las preguntas de seguridad
                console.log("🚀 PASO 2: Enviando preguntas al servidor...");
                // 🔥 ACTUALIZADO: Ruta del sistema
                const responsePreguntas = await fetch('/sistema/public/index.php?route=auth&caso=configurar-preguntas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_usuario: parseInt(userId),
                        preguntas_respuestas: preguntasRespuestas
                    })
                });
                
                // Verificar respuesta de preguntas
                const rawResponsePreguntas = await responsePreguntas.text();
                console.log("📨 RESPUESTA CRUDA DEL SERVIDOR (PREGUNTAS):", rawResponsePreguntas);
                
                let resultPreguntas;
                try {
                    resultPreguntas = JSON.parse(rawResponsePreguntas);
                    console.log("✅ RESPUESTA JSON PARSEADA (PREGUNTAS):", resultPreguntas);
                } catch (parseError) {
                    console.error("❌ ERROR PARSEANDO JSON (PREGUNTAS):", parseError);
                    console.error("📄 RESPUESTA QUE FALLÓ:", rawResponsePreguntas);
                    showAlert('Error técnico al guardar preguntas', 'error');
                    showLoading(false);
                    return;
                }
                
                // Verificar si las preguntas se guardaron correctamente
                if (resultPreguntas.status === '200') {
                    console.log("✅ PREGUNTAS GUARDADAS EXITOSAMENTE");
                    
                    // 🔥 VERIFICAR EN BASE DE DATOS (DEBUG)
                    console.log("🔍 Verificando en BD...");
                    // 🔥 ACTUALIZADO: Ruta del sistema
                    const responseVerificar = await fetch('/sistema/public/index.php?route=auth&caso=debug-preguntas', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_usuario: parseInt(userId)
                        })
                    });
                    
                    const debugResult = await responseVerificar.json();
                    console.log("🔍 DEBUG BD RESULT:", debugResult);
                    
                } else {
                    console.error("❌ ERROR GUARDANDO PREGUNTAS:", resultPreguntas);
                    showAlert('Error al guardar preguntas: ' + (resultPreguntas.message || 'Error desconocido'), 'error');
                    showLoading(false);
                    return;
                }
                
                // 🔥 PASO 3: Cambiar la contraseña
                console.log("🔑 PASO 3: Cambiando contraseña...");
                // 🔥 ACTUALIZADO: Ruta del sistema
                const responsePassword = await fetch('/sistema/public/index.php?route=auth&caso=cambiar-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_usuario: parseInt(userId),
                        nueva_password: nuevaPassword,
                        password_actual: null
                    })
                });
                
                // Verificar respuesta de contraseña
                const rawResponsePassword = await responsePassword.text();
                console.log("📨 RESPUESTA CRUDA (CONTRASEÑA):", rawResponsePassword);
                
                let resultPassword;
                try {
                    resultPassword = JSON.parse(rawResponsePassword);
                    console.log("✅ RESPUESTA JSON (CONTRASEÑA):", resultPassword);
                } catch (parseError) {
                    console.error("❌ ERROR PARSEANDO JSON (CONTRASEÑA):", parseError);
                    console.error("📄 RESPUESTA QUE FALLÓ:", rawResponsePassword);
                    showAlert('Configuración completada. Redirigiendo...', 'success');
                    setTimeout(() => {
                        // 🔥 ACTUALIZADO: Ruta del sistema
                        window.location.href = '/sistema/public/index.php?route=inicio';
                    }, 2000);
                    return;
                }
                
                if (resultPassword.status === '200') {
                    console.log("✅ CONTRASEÑA CAMBIADA EXITOSAMENTE");
                    showAlert('✅ Configuración completada exitosamente. Redirigiendo...', 'success');
                    setTimeout(() => {
                        // 🔥 ACTUALIZADO: Ruta del sistema
                        window.location.href = '/sistema/public/index.php?route=inicio';
                    }, 2000);
                } else {
                    console.error(" ERROR CAMBIANDO CONTRASEÑA:", resultPassword);
                    showAlert('Error en contraseña: ' + (resultPassword.message || 'Error desconocido'), 'error');
                }
                
            } catch (error) {
                console.error(' ERROR DE CONEXIÓN:', error);
                showAlert('Error de conexión. Intente nuevamente.', 'error');
            } finally {
                showLoading(false);
            }
        }
    </script>
</body>
</html>