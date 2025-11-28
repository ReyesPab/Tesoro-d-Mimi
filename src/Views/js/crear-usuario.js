class CrearUsuario {
    constructor() {
        this.init();
    }

    async init() {
        await this.cargarRoles();
        this.cargarFechasAutomaticas();
        this.configurarEventos();
    }

    async cargarRoles() {
        try {
            const response = await fetch('index.php?route=user&caso=obtener-roles');
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            console.log("Roles cargados:", data);
            
            const selectRol = document.getElementById('id_rol');
            selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
            
            if (data.status === 200 && data.data && data.data.roles) {
                data.data.roles.forEach(rol => {
                    const option = document.createElement('option');
                    // Manejar diferentes nombres de campo (mayúsculas/minúsculas)
                    option.value = rol.ID_ROL || rol.Id_Rol || rol.id_rol;
                    option.textContent = rol.ROL || rol.Rol || rol.rol;
                    selectRol.appendChild(option);
                });
            } else {
                console.warn("Estructura de roles inesperada:", data);
                this.cargarRolesPorDefecto();
            }
        } catch (error) {
            console.error('Error cargando roles:', error);
            this.cargarRolesPorDefecto();
        }
    }

    cargarRolesPorDefecto() {
        const rolesPorDefecto = [
            { ID_ROL: 1, ROL: 'ADMINISTRADOR' },
            { ID_ROL: 2, ROL: 'USUARIO' }
        ];
        
        const selectRol = document.getElementById('id_rol');
        selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
        
        rolesPorDefecto.forEach(rol => {
            const option = document.createElement('option');
            option.value = rol.ID_ROL;
            option.textContent = rol.ROL;
            selectRol.appendChild(option);
        });
    }

    cargarFechasAutomaticas() {
        const fechaActual = new Date().toLocaleDateString('es-ES');
        document.getElementById('fecha_creacion').textContent = fechaActual;
        
        // Calcular fecha de vencimiento (30 días por defecto)
        const fechaVencimiento = new Date();
        fechaVencimiento.setDate(fechaVencimiento.getDate() + 30);
        document.getElementById('fecha_vencimiento').textContent = fechaVencimiento.toLocaleDateString('es-ES');
    }

    configurarEventos() {
        const form = document.getElementById('formCrearUsuario');
        
        form.addEventListener('submit', (e) => this.crearUsuario(e));
        
        // Validar usuario único en tiempo real
        document.getElementById('usuario').addEventListener('blur', () => this.validarUsuarioUnico());
        
        // Convertir correo a minúsculas automáticamente
        document.getElementById('correo_electronico').addEventListener('input', (e) => {
            e.target.value = e.target.value.toLowerCase();
        });
    }

    async toggleAutogenerarPassword() {
        try {
            const response = await fetch('index.php?route=user&caso=generar-password');
            const data = await response.json();
            
            if (data.status === 200) {
                const passwordInput = document.getElementById('contraseña');
                const confirmInput = document.getElementById('confirmar_contraseña');
                
                passwordInput.value = data.data.password;
                confirmInput.value = data.data.password;
                
                // Validar la contraseña generada
                this.validarPasswordEnTiempoReal(data.data.password);
                
                alert('Contraseña autogenerada exitosamente');
            }
        } catch (error) {
            console.error('Error generando password:', error);
            alert('Error al generar contraseña automática');
        }
    }

    validarPasswordEnTiempoReal(password) {
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

    async validarEmailUnico(input) {
        let email = input.value.trim().toLowerCase();
        input.value = email;
        
        if (email === '') {
            this.limpiarError('correo');
            return;
        }
        
        // Validar formato primero
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.mostrarError('correo', 'Formato de correo electrónico inválido');
            return;
        }
        
        // Validar si el correo ya existe
        try {
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
            } else {
                this.limpiarError('correo');
            }
        } catch (error) {
            console.error('Error validando correo:', error);
        }
    }

// En el método crearUsuario, reemplazar esta parte:
async crearUsuario(e) {
    e.preventDefault();
    
    // Asegurar que el correo esté en minúsculas antes de enviar
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
        
        // Agregar campos adicionales
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
        
        if (result.status === 201) {
            console.log("✅ ÉXITO - Usuario creado correctamente");
            
            // MOSTRAR MENSAJE DE ÉXITO Y REDIRIGIR
            this.mostrarMensajeExito('Usuario creado exitosamente');
            
            // Redirigir después de 1.5 segundos
            setTimeout(() => {
                window.location.href = '/sistema/public/gestion-usuarios';
            }, 1500);
            
        } else {
            console.log("❌ ERROR - No se pudo crear el usuario");
            
            const mensajeError = result.message || 'No se pudo crear el usuario';
            this.mostrarMensajeError('Error: ' + mensajeError);
        }
        
    } catch (error) {
        console.error('🔴 Error en la conexión:', error);
        this.mostrarMensajeError('Error de conexión con el servidor: ' + error.message);
    } finally {
        btnSubmit.disabled = false;
        spinner.classList.add('d-none');
    }
}

// Agregar estos nuevos métodos para mostrar mensajes:
mostrarMensajeExito(mensaje) {
    // Crear alerta de Bootstrap
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insertar al inicio del card-body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    // Auto-ocultar después de 3 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

mostrarMensajeError(mensaje) {
    // Crear alerta de Bootstrap
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insertar al inicio del card-body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

    validarFormulario() {
        let isValid = true;
        
        // Validar usuario
        const usuario = document.getElementById('usuario').value.trim();
        if (usuario.length === 0) {
            this.mostrarError('usuario', 'El usuario es requerido');
            isValid = false;
        } else if (usuario.length < 3) {
            this.mostrarError('usuario', 'El usuario debe tener al menos 3 caracteres');
            isValid = false;
        }
        
        // Validar nombre
        const nombre = document.getElementById('nombre_usuario').value.trim();
        if (nombre.length === 0) {
            this.mostrarError('nombre', 'El nombre completo es requerido');
            isValid = false;
        }
        
        // Validar rol - CORREGIDO: este campo era el que faltaba
        const rol = document.getElementById('id_rol').value;
        if (rol === '') {
            this.mostrarError('rol', 'El rol es requerido');
            isValid = false;
        }
        
        // Validar contraseñas
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
        
        if (/\s/.test(password)) {
            this.mostrarError('password', 'La contraseña no puede contener espacios');
            isValid = false;
        }
        
        // Validar email si se proporciona
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

    limpiarFormulario() {
        document.getElementById('formCrearUsuario').reset();
        this.cargarFechasAutomaticas();
        
        // Limpiar errores
        const inputs = document.querySelectorAll('.is-invalid');
        inputs.forEach(input => input.classList.remove('is-invalid'));
        
        const feedbacks = document.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => feedback.textContent = '');
        
        // Ocultar requisitos de contraseña
        document.getElementById('passwordRequirements').style.display = 'none';
    }
}

// Instancia global
const crearUsuario = new CrearUsuario();

// Funciones globales para llamadas desde HTML
window.togglePassword = function(inputId) {
    crearUsuario.togglePassword(inputId);
};

window.autogenerarPassword = function() {
    crearUsuario.toggleAutogenerarPassword();
};

window.validarUsuarioUnico = function() {
    crearUsuario.validarUsuarioUnico();
};

window.validarEmailUnico = function(input) {
    crearUsuario.validarEmailUnico(input);
};

window.validarNombreUsuario = function(input) {
    let valor = input.value;
    // Permitir solo letras, números y un solo espacio entre palabras
    valor = valor.replace(/[^A-ZÁÉÍÓÚÑÜ\s]/gi, '');
    valor = valor.replace(/\s+/g, ' ');
    input.value = valor.toUpperCase();
    
    if (valor.trim().length === 0) {
        crearUsuario.mostrarError('nombre', 'El nombre es requerido');
    } else {
        crearUsuario.limpiarError('nombre');
    }
};

window.validarPasswordEnTiempoReal = function(password) {
    crearUsuario.validarPasswordEnTiempoReal(password);
};

window.validarConfirmacionPassword = function(input) {
    const password = document.getElementById('contraseña').value;
    const confirmPassword = input.value;
    
    if (confirmPassword !== password) {
        crearUsuario.mostrarError('confirmar', 'Las contraseñas no coinciden');
    } else {
        crearUsuario.limpiarError('confirmar');
    }
};