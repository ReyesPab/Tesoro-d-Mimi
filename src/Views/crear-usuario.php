<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<style>
    .password-toggle { cursor: pointer; }
    .form-control:read-only { background-color: #e9ecef; }
    .auto-generated { background-color: #e8f5e8 !important; }
    
    /* Estilos para los requisitos de contraseña - IDÉNTICOS AL LOGIN */
    .password-requirements {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        padding: 0.75rem;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #666;
    }
    
    .password-requirements ul {
        margin: 0;
        padding-left: 1rem;
    }
    
    .requirement-met {
        color: #28a745;
    }
    
    .requirement-not-met {
        color: #dc3545;
    }
</style>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">Crear Nuevo Usuario</h1>
            <a href='/sistema/public/gestion-usuarios' class="btn btn-secondary">Volver a Gestión</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="card-title mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form id="formCrearUsuario">
                        <div class="row">
                            <!-- Campo Número de Identidad -->
                            <div class="col-md-6 mb-3">
                                <label for="numero_identidad" class="form-label">Número de Identidad</label>
                                <input type="text" class="form-control" id="numero_identidad" name="numero_identidad" 
                                       maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       placeholder="Solo números">
                                <div class="form-text">Máximo 20 caracteres. Solo números.</div>
                                <div class="invalid-feedback" id="error-identidad"></div>
                            </div>
                            
                            <!-- Campo Usuario -->
                            <div class="col-md-6 mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       maxlength="15" required 
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÁÉÍÓÚÑÜ]/g, '')"
                                       onblur="validarUsuarioUnico()"
                                       placeholder="Solo letras en mayúsculas">
                                <div class="form-text">Máximo 15 caracteres. Solo letras.</div>
                                <div class="invalid-feedback" id="error-usuario"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                   maxlength="100" required 
                                   oninput="validarNombreUsuario(this)"
                                   placeholder="Nombre completo del usuario">
                            <div class="form-text">Máximo 100 caracteres. Solo un espacio entre palabras.</div>
                            <div class="invalid-feedback" id="error-nombre"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_rol" class="form-label">Rol de Usuario *</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccionar Rol</option>
                                </select>
                                <div class="invalid-feedback" id="error-rol"></div>
                            </div>
                            
                            <!-- Campo Correo Electrónico -->
<div class="col-md-6 mb-3">
    <label for="correo_electronico" class="form-label">Correo Electrónico</label>
    <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" 
           maxlength="50" onblur="validarEmailUnico(this)"
           oninput="this.value = this.value.toLowerCase()"
           placeholder="ejemplo@dominio.com">
    <div class="form-text">Formato válido: usuario@dominio.com (debe ser único en el sistema)</div>
    <div class="invalid-feedback" id="error-correo"></div>
</div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contraseña" class="form-label">Contraseña *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="contraseña" name="contraseña" 
                                           required minlength="5" maxlength="10"
                                           oninput="validarPasswordEnTiempoReal(this.value)"
                                           placeholder="Mínimo 5 caracteres">
                                    <span class="input-group-text password-toggle" onclick="togglePassword('contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="form-text">Mínimo 5 caracteres, máximo 10. No se permiten espacios.</div>
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
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_contraseña" 
                                           required oninput="validarConfirmacionPassword(this)"
                                           placeholder="Repetir contraseña">
                                    <span class="input-group-text password-toggle" onclick="togglePassword('confirmar_contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="error-confirmar"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary" onclick="autogenerarPassword()">
                                    🔐 Autogenerar Contraseña
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mostrar_password">
                                    <label class="form-check-label" for="mostrar_password">
                                        Mostrar contraseñas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Fecha Creación:</strong><br>
                                            <span id="fecha_creacion"><?php echo date('d/m/Y H:i:s'); ?></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Fecha Vencimiento:</strong><br>
                                            <span id="fecha_vencimiento">Calculando...</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Estado del Usuario: <span class="badge bg-warning">NUEVO</span></h6>
                                <small class="text-muted">
                                    • El usuario deberá configurar preguntas de seguridad en su primer ingreso<br>
                                    • El estado cambiará a ACTIVO después del primer ingreso exitoso
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="crearUsuario.limpiarFormulario()">
                                Limpiar Formulario
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnCrearUsuario">
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
<script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
<script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

function togglePassword(inputId) {
    crearUsuario.togglePassword(inputId);
}

function autogenerarPassword() {
    crearUsuario.autogenerarPassword();
}

function validarUsuarioUnico() {
    crearUsuario.validarUsuarioUnico();
}

function validarEmailUnico(input) {
    crearUsuario.validarEmailUnico(input);
}

function validarNombreUsuario(input) {
    crearUsuario.validarNombreUsuario(input);
}

function validarPasswordEnTiempoReal(password) {
    crearUsuario.validarPasswordEnTiempoReal(password);
}

function validarConfirmacionPassword(input) {
    crearUsuario.validarConfirmacionPassword(input);
}


class CrearUsuario {
    constructor() {
        this.init();
    }

    async init() {
        await this.cargarRoles();
        this.configurarEventos();
        this.calcularFechaVencimiento();
    }

    async cargarRoles() {
        try {
            const response = await fetch('index.php?route=user&caso=obtener-roles');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            const selectRol = document.getElementById('id_rol');
            selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
            
            if (data.status === 200 && data.data && data.data.roles) {
                data.data.roles.forEach(rol => {
                    const option = document.createElement('option');
                    option.value = rol.ID_ROL || rol.Id_Rol;
                    option.textContent = rol.ROL || rol.Rol;
                    selectRol.appendChild(option);
                });
            } else {
                // Roles por defecto
                const rolesPorDefecto = [
                    { ID_ROL: 1, ROL: 'ADMINISTRADOR' },
                    { ID_ROL: 2, ROL: 'USUARIO' }
                ];
                
                rolesPorDefecto.forEach(rol => {
                    const option = document.createElement('option');
                    option.value = rol.ID_ROL;
                    option.textContent = rol.ROL;
                    selectRol.appendChild(option);
                });
            }
            
        } catch (error) {
            console.error('Error cargando roles:', error);
        }
    }

    configurarEventos() {
        const form = document.getElementById('formCrearUsuario');
        const mostrarPasswordCheckbox = document.getElementById('mostrar_password');
        
        form.addEventListener('submit', (e) => this.crearUsuario(e));
        mostrarPasswordCheckbox.addEventListener('change', (e) => this.toggleMostrarPassword(e));
    }

    async calcularFechaVencimiento() {
        try {
            const response = await fetch('index.php?route=user&caso=obtener-parametros');
            const data = await response.json();
            
            let diasVigencia = 360; // Valor por defecto
            
            if (data.status === 200 && data.data && data.data.parametros) {
                const parametro = data.data.parametros.find(p => p.PARAMETRO === 'ADMIN_DIAS_VIGENCIA' || p.Parametro === 'ADMIN_DIAS_VIGENCIA');
                if (parametro) {
                    diasVigencia = parseInt(parametro.VALOR || parametro.Valor) || 360;
                }
            }
            
            const fechaActual = new Date();
            const fechaVencimiento = new Date(fechaActual);
            fechaVencimiento.setDate(fechaActual.getDate() + diasVigencia);
            
            document.getElementById('fecha_vencimiento').textContent = 
                fechaVencimiento.toLocaleDateString('es-ES') + ' (' + diasVigencia + ' días)';
                
        } catch (error) {
            console.error('Error calculando fecha vencimiento:', error);
            document.getElementById('fecha_vencimiento').textContent = 'Error al calcular';
        }
    }

    // FUNCIÓN IDÉNTICA A LA DEL LOGIN - VALIDACIÓN EN TIEMPO REAL
   validarPasswordEnTiempoReal(password) {
    const requirements = document.getElementById('passwordRequirements');
    
    if (password.length > 0) {
        requirements.style.display = 'block';
        
        // Validar longitud (5-10 caracteres)
        const longitudValida = password.length >= 5 && password.length <= 10;
        document.getElementById('reqLength').className = longitudValida ? 'requirement-met' : 'requirement-not-met';
        document.getElementById('reqLength').textContent = 
            longitudValida ? 
            `Mínimo 5 caracteres, máximo 10 ✓ (${password.length}/10)` : 
            `Mínimo 5 caracteres, máximo 10 (${password.length}/10)`;
        
        // Validar mayúsculas
        document.getElementById('reqUpper').className = 
            /[A-Z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
        
        // Validar minúsculas
        document.getElementById('reqLower').className = 
            /[a-z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
        
        // Validar números
        document.getElementById('reqNumber').className = 
            /[0-9]/.test(password) ? 'requirement-met' : 'requirement-not-met';
        
        // Validar caracteres especiales (INCLUYENDO guión bajo)
        document.getElementById('reqSpecial').className = 
            /[!@#$%^&*_]/.test(password) ? 'requirement-met' : 'requirement-not-met';
        
        // Validar espacios
        document.getElementById('reqNoSpaces').className = 
            !/\s/.test(password) ? 'requirement-met' : 'requirement-not-met';
    } else {
        requirements.style.display = 'none';
    }
}

    // FUNCIÓN PARA AUTOGENERAR CONTRASEÑA ROBUSTA
    async autogenerarPassword() {
    try {
        const response = await fetch('index.php?route=user&caso=generar-password');
        const data = await response.json();
        
        if (data.status === 200 && data.data && data.data.password) {
            const passwordInput = document.getElementById('contraseña');
            const confirmInput = document.getElementById('confirmar_contraseña');
            
            const nuevaPassword = data.data.password;
            
            // ✅ DEBUG: Verificar la longitud de la contraseña generada
            console.log("🔍 Contraseña generada:", nuevaPassword, "Longitud:", nuevaPassword.length);
            
            passwordInput.value = nuevaPassword;
            confirmInput.value = nuevaPassword;
            passwordInput.classList.add('auto-generated');
            confirmInput.classList.add('auto-generated');
            
            // Validar la contraseña generada
            this.validarPasswordEnTiempoReal(nuevaPassword);
            
            // Mostrar mensaje de éxito con información de longitud
            this.mostrarMensajeTemporal(`✅ Contraseña generada (${nuevaPassword.length} caracteres)`, 'success');
            
        } else {
            console.error("❌ Error en respuesta de API:", data);
            // Fallback: generar localmente si la API falla
            this.generarPasswordLocal();
        }
    } catch (error) {
        console.error('❌ Error generando password:', error);
        // Fallback: generar localmente
        this.generarPasswordLocal();
    }
}

generarPasswordLocal() {
    const mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const minusculas = 'abcdefghijklmnopqrstuvwxyz';
    const numeros = '0123456789';
    const especiales = '!@#$%^&*_';
    
    let password = '';
    
    // Asegurar al menos un carácter de cada tipo
    password += mayusculas[Math.floor(Math.random() * mayusculas.length)];
    password += minusculas[Math.floor(Math.random() * minusculas.length)];
    password += numeros[Math.floor(Math.random() * numeros.length)];
    password += especiales[Math.floor(Math.random() * especiales.length)];
    
    // ✅ CORREGIDO: Generar entre 5 y 10 caracteres (no 6-10)
    const todosCaracteres = mayusculas + minusculas + numeros + especiales;
    const longitudFinal = Math.floor(Math.random() * 6) + 5; // Entre 5 y 10
    
    console.log("🔍 Generando contraseña local, longitud objetivo:", longitudFinal);
    
    for (let i = password.length; i < longitudFinal; i++) {
        password += todosCaracteres[Math.floor(Math.random() * todosCaracteres.length)];
    }
    
    // Mezclar los caracteres
    password = password.split('').sort(() => 0.5 - Math.random()).join('');
    
    const passwordInput = document.getElementById('contraseña');
    const confirmInput = document.getElementById('confirmar_contraseña');
    
    passwordInput.value = password;
    confirmInput.value = password;
    passwordInput.classList.add('auto-generated');
    confirmInput.classList.add('auto-generated');
    
    console.log("🔍 Contraseña local generada:", password, "Longitud:", password.length);
    
    this.validarPasswordEnTiempoReal(password);
    this.mostrarMensajeTemporal(`✅ Contraseña generada (${password.length} caracteres)`, 'success');
}

    toggleMostrarPassword(e) {
        const passwordInput = document.getElementById('contraseña');
        const confirmInput = document.getElementById('confirmar_contraseña');
        const type = e.target.checked ? 'text' : 'password';
        
        passwordInput.setAttribute('type', type);
        confirmInput.setAttribute('type', type);
    }

    validarConfirmacionPassword(input) {
        const password = document.getElementById('contraseña').value;
        const confirmPassword = input.value;
        
        if (confirmPassword !== password) {
            this.mostrarError('confirmar', 'Las contraseñas no coinciden');
        } else {
            this.limpiarError('confirmar');
        }
    }

    async validarUsuarioUnico() {
        const usuario = document.getElementById('usuario').value.trim();
        
        if (usuario.length === 0) return;
        
        try {
            const response = await fetch('index.php?route=user&caso=verificar-usuario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ usuario: usuario })
            });
            
            const data = await response.json();
            
            if (data.status === 400) {
                this.mostrarError('usuario', 'Este usuario ya existe en el sistema');
            } else {
                this.limpiarError('usuario');
            }
        } catch (error) {
            console.error('Error validando usuario:', error);
        }
    }

    // FUNCIÓN MEJORADA PARA VALIDAR CORREO ÚNICO EN CREACIÓN
async validarEmailUnico(input) {
    let email = input.value.trim().toLowerCase();
    input.value = email;
    
    if (email === '') {
        this.limpiarError('correo');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        this.mostrarError('correo', 'Formato de correo electrónico inválido');
        return;
    }
    
    try {
        // Usar el endpoint existente de verificación de correo
        const response = await fetch('index.php?route=user&caso=verificar-correo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ correo: email })
        });
        
        const data = await response.json();
        
        if (data.status === 400) {
            this.mostrarError('correo', 'Este correo electrónico ya está registrado en el sistema');
        } else if (data.status === 200) {
            this.limpiarError('correo');
            this.mostrarMensajeTemporal('✅ Correo electrónico disponible', 'success');
        } else {
            this.mostrarError('correo', 'Error al validar el correo electrónico');
        }
    } catch (error) {
        console.error('Error validando correo:', error);
        this.mostrarError('correo', 'Error de conexión al validar correo');
    }
}

    validarNombreUsuario(input) {
        let valor = input.value;
        valor = valor.replace(/[^A-ZÁÉÍÓÚÑÜ\s]/gi, '');
        valor = valor.replace(/\s+/g, ' ');
        input.value = valor.toUpperCase();
        
        if (valor.trim().length === 0) {
            this.mostrarError('nombre', 'El nombre es requerido');
        } else {
            this.limpiarError('nombre');
        }
    }

    async crearUsuario(e) {
        e.preventDefault();
        
        const correoInput = document.getElementById('correo_electronico');
        correoInput.value = correoInput.value.toLowerCase();
        
        if (!this.validarFormulario()) {
            return;
        }
        
        const btnSubmit = document.getElementById('btnCrearUsuario');
        const spinner = btnSubmit.querySelector('.spinner-border');
        
        btnSubmit.disabled = true;
        spinner.classList.remove('d-none');
        
        try {
            const formData = new FormData(document.getElementById('formCrearUsuario'));
            const data = Object.fromEntries(formData);
            
            data.creado_por = 'ADMIN';
            
            console.log("📤 Enviando datos al servidor:", data);
            
            const response = await fetch('index.php?route=user&caso=crear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            console.log("📥 Respuesta COMPLETA del servidor:", result);
            
            if (result.status === 201 || result.success === true) {
                console.log("✅ ÉXITO - Usuario creado correctamente");
                
                const mensajeExito = result.message || 'Usuario creado exitosamente';
                alert(mensajeExito);
                
                // REDIRECCIÓN CORREGIDA
                window.location.href = '/sistema/public/gestion-usuarios';
                
            } else {
                console.log("❌ ERROR - No se pudo crear el usuario");
                
                const mensajeError = result.message || 'No se pudo crear el usuario';
                alert('Error: ' + mensajeError);
            }
            
        } catch (error) {
            console.error('🔴 Error en la conexión:', error);
            alert('Error de conexión con el servidor');
        } finally {
            btnSubmit.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    validarFormulario() {
        let isValid = true;
        
        const usuario = document.getElementById('usuario').value.trim();
        if (usuario.length === 0) {
            this.mostrarError('usuario', 'El usuario es requerido');
            isValid = false;
        } else if (usuario.length < 3) {
            this.mostrarError('usuario', 'El usuario debe tener al menos 3 caracteres');
            isValid = false;
        }
        
        const nombre = document.getElementById('nombre_usuario').value.trim();
        if (nombre.length === 0) {
            this.mostrarError('nombre', 'El nombre completo es requerido');
            isValid = false;
        }
        
        const rol = document.getElementById('id_rol').value;
        if (rol === '') {
            this.mostrarError('rol', 'El rol es requerido');
            isValid = false;
        }
        
        const password = document.getElementById('contraseña').value;
        const confirmPassword = document.getElementById('confirmar_contraseña').value;
        
        if (password !== confirmPassword) {
            this.mostrarError('confirmar', 'Las contraseñas no coinciden');
            isValid = false;
        } else {
            this.limpiarError('confirmar');
        }
        
        if (password.length < 5 || password.length > 10) {
            this.mostrarError('password', 'La contraseña debe tener entre 5 y 10 caracteres');
            isValid = false;
        } else {
            this.limpiarError('password');
        }
        
        const email = document.getElementById('correo_electronico').value;
        if (email && !this.validarEmailFormat(email)) {
            this.mostrarError('correo', 'Formato de correo electrónico inválido');
            isValid = false;
        }
        
        return isValid;
    }

    validarEmailFormat(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    limpiarFormulario() {
        document.getElementById('formCrearUsuario').reset();
        document.getElementById('fecha_creacion').textContent = new Date().toLocaleString('es-ES');
        this.calcularFechaVencimiento();
        
        const inputs = document.querySelectorAll('.is-invalid');
        inputs.forEach(input => input.classList.remove('is-invalid'));
        
        const feedbacks = document.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => feedback.textContent = '');
        
        document.getElementById('contraseña').classList.remove('auto-generated');
        document.getElementById('confirmar_contraseña').classList.remove('auto-generated');
        
        document.getElementById('passwordRequirements').style.display = 'none';
        
        console.log('Formulario limpiado exitosamente');
    }

    mostrarError(campo, mensaje) {
        const errorElement = document.getElementById(`error-${campo}`);
        const inputElement = document.getElementById(campo) || document.getElementById(`confirmar_${campo}`);
        
        if (errorElement && inputElement) {
            errorElement.textContent = mensaje;
            inputElement.classList.add('is-invalid');
        }
    }

    limpiarError(campo) {
        const errorElement = document.getElementById(`error-${campo}`);
        const inputElement = document.getElementById(campo) || document.getElementById(`confirmar_${campo}`);
        
        if (errorElement && inputElement) {
            errorElement.textContent = '';
            inputElement.classList.remove('is-invalid');
        }
    }

    togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
    }
}

const crearUsuario = new CrearUsuario();

document.addEventListener('DOMContentLoaded', () => {
    window.crearUsuario = crearUsuario;
});
</script>