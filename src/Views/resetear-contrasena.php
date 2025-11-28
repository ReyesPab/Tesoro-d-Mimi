<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Resetear Contraseña</h1>
            <a href='/sistema/public/gestion-usuarios' class="btn btn-secondary">
                ← Volver a Gestión de Usuarios
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="loadingMessage" class="alert alert-info text-center" style="display: none;">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Procesando...
                </div>
                
                <div id="alertMessage" class="alert" style="display: none;"></div>
                
                <?php
                // Obtener ID del usuario desde la URL
                $idUsuario = $_GET['id'] ?? '';
                if (empty($idUsuario)) {
                    echo '<div class="alert alert-danger">Error: No se especificó el usuario</div>';
                    echo '</div></div></div></main>';
                    require_once 'partials/footer.php';
                    exit;
                }
                ?>
                
                <form id="formResetContrasena">
                    <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo htmlspecialchars($idUsuario); ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usuario_info" class="form-label">Usuario Seleccionado</label>
                            <input type="text" class="form-control" id="usuario_info" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="nombre_usuario_info" class="form-label">Nombre del Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario_info" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nueva_contrasena" class="form-label">Nueva Contraseña *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="nueva_contrasena" 
                                   name="nueva_contrasena" required minlength="5" maxlength="10">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('nueva_contrasena')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            La contraseña debe tener entre 5 y 10 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial (!@#$%^&*_).
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmar_contrasena" 
                                   required minlength="5" maxlength="10">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirmar_contrasena')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="autogenerar_contrasena">
                            <label class="form-check-label" for="autogenerar_contrasena">
                                Generar contraseña automáticamente
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="generarContrasena()">
                            <i class="bi bi-key"></i> Generar Contraseña Robusta
                        </button>
                    </div>
                    
                    <div class="password-requirements alert alert-info">
                        <h6>Requisitos de la contraseña:</h6>
                        <ul class="mb-0">
                            <li id="reqLength">Entre 5 y 10 caracteres</li>
                            <li id="reqNoSpaces">Sin espacios</li>
                            <li id="reqNotUsername">No puede ser igual al nombre de usuario</li>
                            <li id="reqStrength">Debe incluir: <strong>MAYÚSCULA</strong>, <strong>minúscula</strong>, <strong>número</strong>, <strong>carácter especial (!@#$%^&*_)</strong></li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarReset()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Resetear Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
// Función de depuración global
function debugCompleto() {
    console.clear();
    console.log("🛠️ DEBUG COMPLETO DEL FORMULARIO");
    console.log("=================================");
    
    const idUsuario = document.getElementById('id_usuario')?.value;
    console.log("1. ID Usuario:", idUsuario);
    
    console.log("2. Elementos del DOM:");
    console.log("   - usuario_info:", document.getElementById('usuario_info'));
    console.log("   - nombre_usuario_info:", document.getElementById('nombre_usuario_info'));
    console.log("   - id_usuario:", document.getElementById('id_usuario'));
    
    console.log("3. Probando API...");
    
    fetch(`index.php?route=user&caso=obtener-usuario-edicion&id_usuario=${idUsuario}`)
        .then(response => {
            console.log("4. Response status:", response.status, response.statusText);
            return response.text();
        })
        .then(text => {
            console.log("5. Raw response:", text);
            try {
                const data = JSON.parse(text);
                console.log("6. Parsed JSON:", data);
                
                // Buscar usuario recursivamente
                function findUsuario(obj, path = '') {
                    for (let key in obj) {
                        const newPath = path ? `${path}.${key}` : key;
                        if (key === 'USUARIO' && typeof obj[key] === 'string') {
                            console.log(`🎯 USUARIO ENCONTRADO en: ${newPath} =`, obj[key]);
                        }
                        if (key === 'NOMBRE_USUARIO' && typeof obj[key] === 'string') {
                            console.log(`🎯 NOMBRE ENCONTRADO en: ${newPath} =`, obj[key]);
                        }
                        if (typeof obj[key] === 'object' && obj[key] !== null) {
                            findUsuario(obj[key], newPath);
                        }
                    }
                }
                
                findUsuario(data);
                
            } catch (e) {
                console.error("❌ Error parsing JSON:", e);
            }
        })
        .catch(error => {
            console.error("💥 Fetch error:", error);
        });
}

// Hacer la función global
window.debugCompleto = debugCompleto;

class ResetContrasena {
    constructor() {
        this.idUsuario = document.getElementById('id_usuario').value;
        
        if (!this.idUsuario) {
            this.mostrarError('Error: No se especificó el ID del usuario');
            return;
        }
        
        this.init();
    }

    async init() {
        try {
            await this.cargarInfoUsuario();
            this.configurarEventos();
        } catch (error) {
            console.error('Error en inicialización:', error);
            this.mostrarError('Error al inicializar el formulario');
        }
    }

    async cargarInfoUsuario() {
        try {
            console.log("🔍 Iniciando carga de usuario ID:", this.idUsuario);
            
            const response = await fetch(`index.php?route=user&caso=obtener-usuario-edicion&id_usuario=${this.idUsuario}`);
            
            console.log("📡 Estado de la respuesta:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            console.log("📄 Respuesta como texto:", text);
            
            let result;
            try {
                result = JSON.parse(text);
                console.log("🔍 JSON parseado:", result);
            } catch (e) {
                console.error("❌ Error parseando JSON:", e);
                throw new Error("La respuesta no es JSON válido");
            }
            
            // DEPURACIÓN DETALLADA - Ver toda la estructura
            console.log("🎯 ESTRUCTURA COMPLETA DE LA RESPUESTA:");
            console.log("   - result:", result);
            console.log("   - result.status:", result.status);
            console.log("   - result.message:", result.message);
            console.log("   - result.data:", result.data);
            console.log("   - result.data?.usuario:", result.data?.usuario);
            console.log("   - Keys de result:", Object.keys(result));
            if (result.data) {
                console.log("   - Keys de result.data:", Object.keys(result.data));
            }
            
            // Buscar los datos en cualquier estructura posible
            let usuarioData = null;
            
            if (result.data && result.data.usuario) {
                usuarioData = result.data.usuario;
                console.log("✅ Datos encontrados en: result.data.usuario");
            } else if (result.usuario) {
                usuarioData = result.usuario;
                console.log("✅ Datos encontrados en: result.usuario");
            } else if (result.data && result.data.ID_USUARIO) {
                usuarioData = result.data;
                console.log("✅ Datos encontrados en: result.data");
            } else if (result.ID_USUARIO) {
                usuarioData = result;
                console.log("✅ Datos encontrados en: result");
            } else {
                console.error("❌ No se pudo encontrar la estructura de datos");
                console.log("🔍 Búsqueda exhaustiva:");
                this.buscarEnEstructura(result, '');
                this.mostrarError('Estructura de datos inesperada. Ver consola para detalles.');
                return;
            }
            
            if (usuarioData && usuarioData.USUARIO) {
                console.log("✅ USUARIO ENCONTRADO:", usuarioData.USUARIO);
                console.log("✅ NOMBRE ENCONTRADO:", usuarioData.NOMBRE_USUARIO);
                
                // Llenar los campos
                this.establecerValor('usuario_info', usuarioData.USUARIO);
                this.establecerValor('nombre_usuario_info', usuarioData.NOMBRE_USUARIO);
                
                this.ocultarMensaje();
                console.log("🎉 Formulario cargado exitosamente!");
                
            } else {
                console.error("❌ Datos de usuario incompletos:", usuarioData);
                this.mostrarError('Datos de usuario incompletos. Ver consola.');
            }
            
        } catch (error) {
            console.error('💥 Error en cargarInfoUsuario:', error);
            this.mostrarError('Error al cargar información: ' + error.message);
        }
    }

    // Método auxiliar para establecer valores
    establecerValor(elementId, valor) {
        const element = document.getElementById(elementId);
        if (element) {
            element.value = valor || '';
            console.log(`✅ ${elementId} establecido a:`, valor);
        } else {
            console.error(`❌ Elemento no encontrado: ${elementId}`);
        }
    }

    // Método para buscar recursivamente en la estructura
    buscarEnEstructura(obj, path) {
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
                const currentPath = path ? `${path}.${key}` : key;
                console.log(`   🔍 ${currentPath}:`, obj[key]);
                
                if (key === 'USUARIO' || key === 'NOMBRE_USUARIO') {
                    console.log(`   🎯 ENCONTRADO: ${currentPath} =`, obj[key]);
                }
                
                if (typeof obj[key] === 'object' && obj[key] !== null) {
                    this.buscarEnEstructura(obj[key], currentPath);
                }
            }
        }
    }

    ocultarMensaje() {
        const alert = document.getElementById('alertMessage');
        alert.style.display = 'none';
        alert.className = 'alert';
    }

    configurarEventos() {
        document.getElementById('formResetContrasena').addEventListener('submit', (e) => this.enviarFormulario(e));
        document.getElementById('autogenerar_contrasena').addEventListener('change', (e) => this.toggleAutogenerar(e));
        
        // Validación en tiempo real
        document.getElementById('nueva_contrasena').addEventListener('input', (e) => this.validarContrasenaEnTiempoReal(e.target.value));
    }

    toggleAutogenerar(e) {
        const passwordInput = document.getElementById('nueva_contrasena');
        const confirmInput = document.getElementById('confirmar_contrasena');
        
        if (e.target.checked) {
            this.generarContrasenaRobusta();
        } else {
            passwordInput.value = '';
            confirmInput.value = '';
        }
    }

    generarContrasenaRobusta() {
        // Generar contraseña que cumpla con los nuevos requisitos (5-10 caracteres)
        const mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const minusculas = 'abcdefghijklmnopqrstuvwxyz';
        const numeros = '0123456789';
        const especiales = '!@#$%^&*_'; // INCLUIR guión bajo
        
        let password = '';
        
        // Asegurar al menos un carácter de cada tipo
        password += mayusculas[Math.floor(Math.random() * mayusculas.length)];
        password += minusculas[Math.floor(Math.random() * minusculas.length)];
        password += numeros[Math.floor(Math.random() * numeros.length)];
        password += especiales[Math.floor(Math.random() * especiales.length)];
        
        // Completar hasta 10 caracteres (máximo permitido)
        const todosCaracteres = mayusculas + minusculas + numeros + especiales;
        
        // Generar entre 6 y 10 caracteres para evitar problemas de longitud
        const longitudFinal = Math.floor(Math.random() * 5) + 6; // Entre 6 y 10 caracteres
        
        for (let i = password.length; i < longitudFinal; i++) {
            password += todosCaracteres[Math.floor(Math.random() * todosCaracteres.length)];
        }
        
        // Mezclar los caracteres
        password = password.split('').sort(() => 0.5 - Math.random()).join('');
        
        document.getElementById('nueva_contrasena').value = password;
        document.getElementById('confirmar_contrasena').value = password;
        
        this.validarContrasenaEnTiempoReal(password);
    }

    validarContrasenaEnTiempoReal(password) {
        const usuario = document.getElementById('usuario_info').value;
        
        // Validar longitud (5-10 caracteres)
        const longitudValida = password.length >= 5 && password.length <= 10;
        document.getElementById('reqLength').className = 
            longitudValida ? 'text-success' : 'text-danger';
        document.getElementById('reqLength').textContent = 
            longitudValida ? 
            `Entre 5 y 10 caracteres ✓ (${password.length}/10)` : 
            `Debe tener entre 5 y 10 caracteres (${password.length}/10)`;
        
        // Validar espacios
        document.getElementById('reqNoSpaces').className = 
            !/\s/.test(password) ? 'text-success' : 'text-danger';
        
        // Validar que no sea igual al usuario
        document.getElementById('reqNotUsername').className = 
            password.toLowerCase() !== usuario.toLowerCase() ? 'text-success' : 'text-danger';
        
        // Validar fortaleza con NUEVOS requisitos (INCLUYENDO guión bajo)
        const tieneMayus = /[A-Z]/.test(password);
        const tieneMinus = /[a-z]/.test(password);
        const tieneNumero = /[0-9]/.test(password);
        const tieneEspecial = /[!@#$%^&*_]/.test(password); // INCLUIR guión bajo
        
        const fortalezaValida = tieneMayus && tieneMinus && tieneNumero && tieneEspecial;
        document.getElementById('reqStrength').className = 
            fortalezaValida ? 'text-success' : 'text-danger';
        document.getElementById('reqStrength').innerHTML = 
            fortalezaValida ? 
            'Contraseña robusta (mayúscula, minúscula, número, especial) ✓' :
            'Debe incluir: <strong>MAYÚSCULA</strong>, <strong>minúscula</strong>, <strong>número</strong>, <strong>carácter especial (!@#$%^&*_)</strong>';
    }

    async enviarFormulario(e) {
    e.preventDefault();
    
    const nuevaContrasena = document.getElementById('nueva_contrasena').value;
    const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
    const usuario = document.getElementById('usuario_info').value;
    
    // Validaciones con NUEVOS requisitos (5-10 caracteres)
    const errores = [];
    
    if (nuevaContrasena.length < 5 || nuevaContrasena.length > 10) {
        errores.push(`La contraseña debe tener entre 5 y 10 caracteres (actual: ${nuevaContrasena.length})`);
    }
    
    if (/\s/.test(nuevaContrasena)) {
        errores.push('La contraseña no puede contener espacios');
    }
    
    if (nuevaContrasena.toLowerCase() === usuario.toLowerCase()) {
        errores.push('La contraseña no puede ser igual al nombre de usuario');
    }
    
    if (nuevaContrasena !== confirmarContrasena) {
        errores.push('Las contraseñas no coinciden');
    }
    
    // Validar fortaleza de la contraseña (INCLUYENDO guión bajo)
    const tieneMayus = /[A-Z]/.test(nuevaContrasena);
    const tieneMinus = /[a-z]/.test(nuevaContrasena);
    const tieneNumero = /[0-9]/.test(nuevaContrasena);
    const tieneEspecial = /[!@#$%^&*_]/.test(nuevaContrasena); // INCLUIR guión bajo
    
    if (!tieneMayus) {
        errores.push('La contraseña debe contener al menos una letra mayúscula');
    }
    
    if (!tieneMinus) {
        errores.push('La contraseña debe contener al menos una letra minúscula');
    }
    
    if (!tieneNumero) {
        errores.push('La contraseña debe contener al menos un número');
    }
    
    if (!tieneEspecial) {
        errores.push('La contraseña debe contener al menos un carácter especial (!@#$%^&*_)');
    }
    
    if (errores.length > 0) {
        this.mostrarError(errores[0]);
        return;
    }
    
    try {
        this.mostrarLoading(true);
        
        console.log("🔄 Enviando solicitud de reset...");
        const response = await fetch('index.php?route=user&caso=resetear-contrasena-admin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_usuario: this.idUsuario,
                nueva_contrasena: nuevaContrasena,
                modificado_por: 'ADMIN'
            })
        });
        
        console.log("📡 Estado HTTP de la respuesta:", response.status, response.statusText);
        
        const text = await response.text();
        console.log("📄 Respuesta COMPLETA como texto:", text);
        
        let result;
        try {
            result = JSON.parse(text);
            console.log("🔍 JSON parseado:", result);
        } catch (e) {
            console.error("❌ Error parseando JSON:", e);
            throw new Error("La respuesta no es JSON válido");
        }
        
        // DEPURACIÓN DETALLADA - Ver TODA la estructura
        console.log("🎯 ESTRUCTURA COMPLETA DE LA RESPUESTA:");
        console.log("   - result:", result);
        console.log("   - Tipo de result:", typeof result);
        console.log("   - Keys de result:", Object.keys(result));
        
        // Mostrar CADA propiedad individualmente
        if (result) {
            console.log("🔍 PROPIEDADES INDIVIDUALES:");
            for (let key in result) {
                console.log(`   - ${key}:`, result[key], `(tipo: ${typeof result[key]})`);
            }
        }
        
        // VERIFICAR TODAS LAS POSIBLES ESTRUCTURAS DE ÉXITO
        let esExitoso = false;
        let mensaje = '';
        
        // Posibilidad 1: responseHTTP con status string
        if (result.status === '200') {
            esExitoso = true;
            mensaje = result.message || 'Contraseña reseteada exitosamente';
            console.log("✅ ÉXITO detectado: result.status === '200'");
        }
        // Posibilidad 2: responseHTTP con status número
        else if (result.status === 200) {
            esExitoso = true;
            mensaje = result.message || 'Contraseña reseteada exitosamente';
            console.log("✅ ÉXITO detectado: result.status === 200");
        }
        // Posibilidad 3: Estructura del procedimiento almacenado
        else if (result.STATUS === 'success') {
            esExitoso = true;
            mensaje = result.MESSAGE || 'Contraseña reseteada exitosamente';
            console.log("✅ ÉXITO detectado: result.STATUS === 'success'");
        }
        // Posibilidad 4: success boolean
        else if (result.success === true) {
            esExitoso = true;
            mensaje = result.message || 'Contraseña reseteada exitosamente';
            console.log("✅ ÉXITO detectado: result.success === true");
        }
        // Posibilidad 5: Mensaje contiene "exitosamente"
        else if (result.message && result.message.includes('exitosamente')) {
            esExitoso = true;
            mensaje = result.message;
            console.log("✅ ÉXITO detectado: mensaje contiene 'exitosamente'");
        }
        
        console.log("📊 RESUMEN DEL ANÁLISIS:");
        console.log("   - ¿Es exitoso?:", esExitoso);
        console.log("   - Mensaje:", mensaje);
        
        if (esExitoso) {
            // ✅ ÉXITO
            alert('✅ ' + mensaje);
            window.location.href = '/sistema/public/gestion-usuarios';
        } else {
            // ❌ ERROR - Mostrar el mensaje real del servidor
            const errorMsg = result.message || result.MESSAGE || 'Error desconocido al resetear contraseña';
            console.error("❌ ERROR del servidor:", errorMsg);
            this.mostrarError(errorMsg);
        }
        
    } catch (error) {
        console.error('💥 Error en la solicitud:', error);
        this.mostrarError('Error de conexión con el servidor: ' + error.message);
    } finally {
        this.mostrarLoading(false);
    }
}

    mostrarLoading(mostrar) {
        const loading = document.getElementById('loadingMessage');
        const submitBtn = document.querySelector('#formResetContrasena button[type="submit"]');
        
        if (mostrar) {
            loading.style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
        } else {
            loading.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Resetear Contraseña';
        }
    }

    mostrarError(mensaje) {
        const alert = document.getElementById('alertMessage');
        alert.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>${mensaje}</span>
            </div>
        `;
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.style.display = 'block';
        
        // Agregar botón de cerrar si no existe
        if (!alert.querySelector('.btn-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close';
            closeBtn.setAttribute('data-bs-dismiss', 'alert');
            alert.appendChild(closeBtn);
        }
        
        // Desplazar hacia el mensaje
        alert.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    mostrarExito(mensaje) {
        const alert = document.getElementById('alertMessage');
        alert.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>${mensaje}</span>
            </div>
            <div class="mt-2 small">
                <i class="bi bi-info-circle me-1"></i>
                Redirigiendo a Gestión de Usuarios en 2 segundos...
            </div>
        `;
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.style.display = 'block';
        
        // Agregar botón de cerrar
        if (!alert.querySelector('.btn-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close';
            closeBtn.setAttribute('data-bs-dismiss', 'alert');
            alert.appendChild(closeBtn);
        }
        
        // Desplazar hacia el mensaje
        alert.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Limpiar formulario
        document.getElementById('nueva_contrasena').value = '';
        document.getElementById('confirmar_contrasena').value = '';
    }

    mostrarMensaje(mensaje, tipo) {
        const alert = document.getElementById('alertMessage');
        alert.textContent = mensaje;
        alert.className = `alert alert-${tipo}`;
        alert.style.display = 'block';
        
        // Desplazar hacia el mensaje
        alert.scrollIntoView({ behavior: 'smooth' });
    }
}

// Funciones globales
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function generarContrasena() {
    resetContrasena.generarContrasenaRobusta();
}

function cancelarReset() {
    if (confirm('¿Está seguro de que desea cancelar? Los cambios no guardados se perderán.')) {
        window.location.href = '/sistema/public/gestion-usuarios';
    }
}

// Depuración automática al cargar
document.addEventListener('DOMContentLoaded', function() {
    console.log("🏁 DOM cargado completamente");
    console.log("🔍 Elementos encontrados:");
    console.log("   - usuario_info:", document.getElementById('usuario_info'));
    console.log("   - nombre_usuario_info:", document.getElementById('nombre_usuario_info'));
    console.log("   - id_usuario:", document.getElementById('id_usuario')?.value);
    
    // Ejecutar debug automáticamente después de 1 segundo
    setTimeout(() => {
        console.log("🔄 Ejecutando debug automático...");
        debugCompleto();
    }, 1000);
});

// Inicializar
const resetContrasena = new ResetContrasena();
</script>

<style>
.password-requirements ul {
    list-style: none;
    padding-left: 0;
}

.password-requirements li {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.form-text {
    font-size: 0.875rem;
}

.alert-success {
    background-color: #d1e7dd;
    border-color: #badbcc;
    color: #0f5132;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c2c7;
    color: #842029;
}

.alert-dismissible .btn-close {
    padding: 0.75rem 0.75rem;
}
</style>

<?php require_once 'partials/footer.php'; ?>