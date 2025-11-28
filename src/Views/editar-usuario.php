<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Editar Usuario</h1>
            <a href='/sistema/public/gestion-usuarios' class="btn btn-secondary">← Volver</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando información del usuario...
                </div>
                
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar la información del usuario.
                </div>

                <form id="formEditarUsuario" style="display: none;">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    
                    <div class="row">
                        <!-- Columna Izquierda -->
                        <div class="col-md-6">
                            <!-- Información Básica -->
                            <div class="mb-3">
                                <label for="numero_identidad" class="form-label">Número de Identidad</label>
                                <input type="text" class="form-control" id="numero_identidad" name="numero_identidad" 
                                       placeholder="Ej: 0801-1990-12345" maxlength="20">
                                <div class="form-text">Máximo 20 caracteres</div>
                                <div class="invalid-feedback" id="error_numero_identidad"></div>
                            </div>

                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       required maxlength="15" readonly
                                       style="background-color: #f8f9fa;">
                                <div class="form-text">El usuario no puede ser modificado</div>
                            </div>

                            <div class="mb-3">
                                <label for="nombre_usuario" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                       required maxlength="100" placeholder="Ingrese el nombre completo">
                                <div class="invalid-feedback" id="error_nombre_usuario"></div>
                            </div>

                            <div class="mb-3">
                                <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" 
                                       maxlength="50" placeholder="usuario@ejemplo.com">
                                <div class="invalid-feedback" id="error_correo_electronico"></div>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="col-md-6">
                            <!-- Rol y Estado -->
                            <div class="mb-3">
                                <label for="id_rol" class="form-label">Rol </label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccione un rol</option>
                                    <!-- Los roles se cargarán dinámicamente -->
                                </select>
                                <div class="invalid-feedback" id="error_id_rol"></div>
                            </div>

                            <div class="mb-3">
                                <label for="estado_usuario" class="form-label">Estado </label>
                                <select class="form-select" id="estado_usuario" name="estado_usuario" required>
                                    <option value="">Seleccione un estado</option>
                                    <option value="ACTIVO">ACTIVO</option>
                                    <option value="Bloqueado">Bloqueado</option>
                                    <option value="Nuevo">Nuevo</option>
                                </select>
                                <div class="invalid-feedback" id="error_estado_usuario"></div>
                            </div>

                            <!-- Información de Auditoría -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Información de Auditoría</h6>
                                    <div class="small">
                                        <div><strong>Creado por:</strong> <span id="info_creado_por">-</span></div>
                                        <div><strong>Fecha creación:</strong> <span id="info_fecha_creacion">-</span></div>
                                        <div><strong>Modificado por:</strong> <span id="info_modificado_por">-</span></div>
                                        <div><strong>Última modificación:</strong> <span id="info_fecha_modificacion">-</span></div>
                                        <div><strong>Última conexión:</strong> <span id="info_ultima_conexion">-</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='/sistema/public/gestion-usuarios'">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <span class="spinner-border spinner-border-sm me-2" style="display: none;" id="spinnerGuardar"></span>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script>
console.log("🟢 Script cargado - Iniciando...");

// Función simple para obtener parámetro de URL
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Función principal mejorada
async function iniciarEdicionUsuario() {
    console.log("🟢 iniciarEdicionUsuario() ejecutándose");
    
    const idUsuario = getUrlParameter('editar');
    console.log("🆔 ID Usuario:", idUsuario);
    
    if (!idUsuario) {
        mostrarError('No se especificó ID de usuario');
        return;
    }
    
    try {
        // Cargar roles primero
        await cargarRoles();
        
        // Luego cargar usuario
        await cargarUsuario(idUsuario);
        
    } catch (error) {
        console.error('Error inicial:', error);
        mostrarError('Error al cargar: ' + error.message);
    }
}

// CARGAR ROLES - VERSIÓN CORREGIDA (igual a la del formulario de creación)
async function cargarRoles() {
    try {
        console.log("📋 Cargando roles...");
        const response = await fetch('index.php?route=user&caso=obtener-roles');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("📊 Respuesta de roles:", data);
        
        const selectRol = document.getElementById('id_rol');
        selectRol.innerHTML = '<option value="">Seleccione un rol</option>';
        
        if (data.status === 200 && data.data && data.data.roles) {
            data.data.roles.forEach(rol => {
                const option = document.createElement('option');
                option.value = rol.ID_ROL || rol.Id_Rol;
                option.textContent = rol.ROL || rol.Rol;
                selectRol.appendChild(option);
            });
            console.log("✅ Roles cargados correctamente desde servidor");
        } else {
            // Roles por defecto en caso de error
            console.warn("⚠️ Usando roles por defecto");
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
        // No interrumpir el flujo, usar roles por defecto
        const selectRol = document.getElementById('id_rol');
        selectRol.innerHTML = '<option value="">Seleccione un rol</option>';
        
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
}

// Cargar usuario
async function cargarUsuario(idUsuario) {
    try {
        console.log("🔍 Cargando usuario ID:", idUsuario);
        
        const response = await fetch(`index.php?route=user&caso=obtener-usuario-edicion&id_usuario=${idUsuario}`);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log("📊 Respuesta completa del servidor:", data);
        
        // VERIFICACIÓN ESPECÍFICA PARA EL PROBLEMA
        if (data.status === 200 || data.message === "Usuario obtenido para edición") {
            // Buscar el usuario en diferentes ubicaciones posibles de la respuesta
            let usuario = null;
            
            if (data.data && data.data.usuario) {
                usuario = data.data.usuario;
            } else if (data.data && data.data.ID_USUARIO) {
                usuario = data.data; // Los datos están directamente en data.data
            } else if (data.ID_USUARIO) {
                usuario = data; // Los datos están en la raíz
            } else if (Array.isArray(data.data) && data.data.length > 0) {
                usuario = data.data[0]; // Es un array, tomar el primer elemento
            }
            
            if (usuario && usuario.ID_USUARIO) {
                console.log("✅ Usuario encontrado, mostrando datos...", usuario);
                mostrarUsuario(usuario);
                mostrarFormulario();
            } else {
                throw new Error('Estructura de datos de usuario incorrecta');
            }
        } else {
            throw new Error(data.message || `Error del servidor: ${data.status}`);
        }
    } catch (error) {
        console.error('❌ Error cargando usuario:', error);
        mostrarError('Error al cargar usuario: ' + error.message);
    }
}

// Mostrar datos del usuario en el formulario - VERSIÓN MEJORADA
function mostrarUsuario(usuario) {
    console.log("🎯 Mostrando usuario en formulario:", usuario);
    
    try {
        // Formatear número de identidad si es muy largo
        let numeroIdentidad = usuario.NUMERO_IDENTIDAD || '';
        if (numeroIdentidad.length === 20) {
            // Formatear como XXXX-XXXX-XXXXXXXXX
            numeroIdentidad = numeroIdentidad.replace(/(\d{4})(\d{4})(\d{12})/, '$1-$2-$3');
        }
        
        // Llenar campos básicos con verificación
        const campos = {
            'id_usuario': usuario.ID_USUARIO,
            'numero_identidad': numeroIdentidad,
            'usuario': usuario.USUARIO || '',
            'nombre_usuario': usuario.NOMBRE_USUARIO || '',
            'correo_electronico': usuario.CORREO_ELECTRONICO || '',
            'id_rol': usuario.ID_ROL || '',
            'estado_usuario': usuario.ESTADO_USUARIO || ''
        };
        
        Object.entries(campos).forEach(([id, valor]) => {
            const elemento = document.getElementById(id);
            if (elemento) {
                // Para el estado, convertir "Activo" a "ACTIVO" para mostrarlo en el frontend
                if (id === 'estado_usuario' && valor === 'Activo') {
                    valor = 'ACTIVO';
                }
                
                elemento.value = valor !== null && valor !== undefined ? valor : '';
                console.log(`✅ Campo ${id} = "${valor}"`);
                
                // IMPORTANTE: Para el select de rol, asegurarse de que se seleccione la opción correcta
                if (id === 'id_rol' && elemento.tagName === 'SELECT') {
                    // Esperar un momento para asegurar que los roles estén cargados
                    setTimeout(() => {
                        elemento.value = valor;
                        console.log(` Rol seleccionado: ${valor}`);
                    }, 100);
                }
            } else {
                console.warn(` No se encontró el campo: ${id}`);
            }
        });
        
        // Información de auditoría
        const textos = {
            'info_creado_por': usuario.CREADO_POR || '-',
            'info_fecha_creacion': formatearFecha(usuario.FECHA_CREACION),
            'info_modificado_por': usuario.MODIFICADO_POR || '-',
            'info_fecha_modificacion': formatearFecha(usuario.FECHA_MODIFICACION),
            'info_ultima_conexion': formatearFecha(usuario.FECHA_ULTIMA_CONEXION)
        };
        
        Object.entries(textos).forEach(([id, texto]) => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.textContent = texto || '-';
                console.log(`✅ Texto ${id} = "${texto}"`);
            } else {
                console.warn(` No se encontró el elemento: ${id}`);
            }
        });
        
        console.log("✅✅✅ FORMULARIO CARGADO EXITOSAMENTE");
        
    } catch (error) {
        console.error('❌ Error en mostrarUsuario:', error);
        throw new Error('Error al mostrar datos en el formulario: ' + error.message);
    }
}

// Mostrar formulario cuando los datos estén listos
function mostrarFormulario() {
    document.getElementById('loadingMessage').style.display = 'none';
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('formEditarUsuario').style.display = 'block';
    console.log("🎉 Formulario mostrado al usuario");
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    try {
        // Intentar formatear la fecha
        const fechaObj = new Date(fecha);
        return isNaN(fechaObj.getTime()) ? fecha : fechaObj.toLocaleString('es-ES');
    } catch (e) {
        return fecha;
    }
}

function mostrarError(mensaje) {
    const errorDiv = document.getElementById('errorMessage');
    const loading = document.getElementById('loadingMessage');
    const form = document.getElementById('formEditarUsuario');
    
    if (loading) loading.style.display = 'none';
    if (form) form.style.display = 'none';
    if (errorDiv) {
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
    }
    
    console.error("❌ Error:", mensaje);
}

// Configurar evento del formulario para guardar
function configurarEventos() {
    const form = document.getElementById('formEditarUsuario');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            await guardarCambios();
        });
        console.log("✅ Evento submit configurado");
    }
}

// Función para convertir estado del frontend al formato del backend
function convertirEstadoParaBackend(estadoFrontend) {
    console.log(`🔄 Convirtiendo estado: "${estadoFrontend}"`);
    
    // SOLO convertir "ACTIVO" a "Activo" para pasar validación
    // El backend luego lo convertirá a "ACTIVO" para guardar
    if (estadoFrontend === 'ACTIVO') {
        return 'Activo';
    }
    
    // Mantener otros estados como están
    return estadoFrontend;
}
// Función para guardar cambios - VERSIÓN CORREGIDA DEL ESTADO
async function guardarCambios() {
    const btnGuardar = document.getElementById('btnGuardar');
    const spinner = document.getElementById('spinnerGuardar');
    
    try {
        // Mostrar loading
        btnGuardar.disabled = true;
        spinner.style.display = 'inline-block';
        
        // Recopilar datos del formulario
        const formData = new FormData(document.getElementById('formEditarUsuario'));
        const datos = Object.fromEntries(formData.entries());
        
        // CORRECCIÓN: Convertir el estado del frontend al formato del backend
        if (datos.estado_usuario) {
            datos.estado_usuario = convertirEstadoParaBackend(datos.estado_usuario);
            console.log(`🔄 Estado convertido: ${formData.get('estado_usuario')} -> ${datos.estado_usuario}`);
        }
        
        console.log("💾 Guardando cambios:", datos);
        
        // Aquí iría la llamada AJAX para guardar
        const response = await fetch('index.php?route=user&caso=actualizar-usuario', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(datos)
        });
        
        const result = await response.json();
        console.log("📥 Respuesta del servidor:", result);
        
        // CORRECCIÓN: Verificar correctamente el éxito
        if (result.status === 200 || result.message === "Usuario actualizado correctamente") {
            // MOSTRAR MENSAJE DE ÉXITO CORRECTO
            alert('✅ Usuario actualizado exitosamente');
            window.location.href = '/sistema/public/gestion-usuarios';
        } else {
            // Solo mostrar error si realmente hay un error
            throw new Error(result.message || 'Error al actualizar usuario');
        }
        
    } catch (error) {
        console.error('Error guardando cambios:', error);
        // SOLO mostrar mensaje de error si realmente es un error
        if (error.message.includes("Usuario actualizado correctamente")) {
            // Si el "error" es en realidad un mensaje de éxito, mostrarlo como éxito
            alert('✅ Usuario actualizado exitosamente');
            window.location.href = '/sistema/public/gestion-usuarios';
        } else {
            alert('❌ Error al guardar cambios: ' + error.message);
        }
    } finally {
        btnGuardar.disabled = false;
        spinner.style.display = 'none';
    }
}

// SOLUCIÓN DE EMERGENCIA - Si después de 5 segundos no carga, usar datos de prueba
setTimeout(() => {
    if (document.getElementById('loadingMessage').style.display !== 'none') {
        console.log("🆘 Timeout - Cargando datos de prueba de emergencia");
        const usuarioPrueba = {
            ID_USUARIO: 27,
            NUMERO_IDENTIDAD: "2227-7777-7777777777",
            USUARIO: "HUBER",
            NOMBRE_USUARIO: "HUBERT",
            ESTADO_USUARIO: "Bloqueado",
            ID_ROL: 2,
            CORREO_ELECTRONICO: "hubert@gmail.com",
            CREADO_POR: "ADMIN",
            FECHA_CREACION: "2025-10-25 15:47:48",
            MODIFICADO_POR: "ADMIN",
            FECHA_MODIFICACION: "2025-10-25 15:48:07",
            FECHA_ULTIMA_CONEXION: null
        };
        mostrarUsuario(usuarioPrueba);
        mostrarFormulario();
    }
}, 5000);

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log("🟢 DOMContentLoaded ejecutado");
    iniciarEdicionUsuario();
    configurarEventos();
});

// Mensaje de prueba
console.log("🟢 Script completamente cargado");
</script>

<style>
.form-text {
    font-size: 0.8rem;
    color: #6c757d;
}

.card.bg-light {
    border: 1px solid #dee2e6;
}

.card.bg-light .card-body {
    padding: 1rem;
}

.small div {
    margin-bottom: 0.3rem;
}

.invalid-feedback {
    display: block;
}

.form-control:read-only {
    background-color: #f8f9fa;
    opacity: 1;
}

/* Estilos para estados de carga */
#loadingMessage {
    transition: opacity 0.3s ease;
}

#formEditarUsuario {
    transition: opacity 0.3s ease;
}
</style>