<?php
// src/Views/compras/registrar-proveedor.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Proveedor - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .required-label::after {
            content: " *";
            color: #dc3545;
        }
        
        .valid-feedback, .invalid-feedback {
            display: block;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .selected-products-list {
    max-height: 200px;
    overflow-y: auto;
}

.selected-products-list .list-group-item {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}

/* Mejorar el select múltiple */
.form-select[multiple] {
    min-height: 120px;
}

.form-select[multiple] option {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
}

.form-select[multiple] option:checked {
    background-color: #667eea;
    color: white;
}
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Registrar Nuevo Proveedor</h1>
                    <a href="/sistema/public/consultar-compras" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Compras
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-building me-2"></i>Información del Proveedor
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formRegistrarProveedor" novalidate>
                                <div class="row">
                                    <div class="row">
    <div class="col-md-12 mb-3">
        <label for="nombre" class="form-label required-label">Nombre del Proveedor</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required 
               placeholder="Ej: Empresa S.A. de C.V., Compañía S/L, Negocio #123"
               minlength="3" maxlength="100"
               pattern="^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s\.\,\/\#\-\&]{3,100}$">
        <div class="valid-feedback">Nombre válido</div>
        <div class="invalid-feedback">
            El nombre debe tener entre 3 y 100 caracteres. Solo se permiten letras, números, espacios y los caracteres: . , / # - &
        </div>
        <div class="form-text">Mínimo 3 caracteres. Permite letras, números, espacios y: . , / # - &</div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="contacto" class="form-label">Persona de Contacto</label>
        <input type="text" class="form-control" id="contacto" name="contacto" 
               placeholder="Nombre completo del contacto"
               maxlength="50"
               pattern="^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{0,50}$">
        <div class="valid-feedback">Contacto válido</div>
        <div class="invalid-feedback">
            Solo se permiten letras y espacios (máximo 50 caracteres)
        </div>
        <div class="form-text">Solo letras y espacios. Máximo 50 caracteres.</div>
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" class="form-control" id="telefono" name="telefono" 
               placeholder="Ej: +504 2234-5678, 2234-5678, 98765432"
               pattern="^[0-9\s\+\-\(\)]{8,20}$"
               maxlength="20">
        <div class="valid-feedback">Teléfono válido</div>
        <div class="invalid-feedback">
            Solo se permiten números, espacios y los caracteres: + - ( )
        </div>
        <div class="form-text">Ej: +504 2234-5678, 2234-5678, 98765432. Solo números y: + - ( )</div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" 
               placeholder="correo@gmail.com o correo@hotmail.com"
               pattern="^[a-z0-9._%+-]+@(gmail|hotmail)\.com$"
               maxlength="50"
               oninput="this.value = this.value.toLowerCase()">
        <div class="valid-feedback">Correo válido</div>
        <div class="invalid-feedback">
            Solo se permiten correos de Gmail (@gmail.com) o Hotmail (@hotmail.com) en minúsculas
        </div>
        <div class="form-text">Solo se aceptan correos @gmail.com y @hotmail.com en minúsculas</div>
    </div>
    
    <div class="col-md-6 mb-3">
        <label for="direccion" class="form-label">Dirección</label>
        <textarea class="form-control" id="direccion" name="direccion" 
                  rows="1" placeholder="Dirección completa del proveedor"
                  maxlength="255"></textarea>
        <div class="form-text">Máximo 255 caracteres</div>
    </div>
</div>
  <!-- Agregar después de la sección de estado -->
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="productos" class="form-label">Productos que Provee</label>
        <select class="form-select" id="productos" name="productos[]" multiple size="5">
            <option value="">Seleccione los productos que provee</option>
            <!-- Los productos se cargarán dinámicamente -->
        </select>
        <div class="form-text">
            Mantenga presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples productos.
            Puede buscar productos escribiendo en el campo.
        </div>
        <div id="productos-seleccionados" class="mt-2"></div>
    </div>
</div>                              
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarRegistro()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnRegistrar" disabled>
                                                <i class="bi bi-check-circle"></i> Registrar Proveedor
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

     <script>
    // Variables para controlar validaciones
    let nombreValido = false;
    let telefonoValido = false;
    let nombreUnico = false;
    let contactoUnico = true; // Opcional
    let correoUnico = true; // Opcional

   document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulario cargado');
    
    const form = document.getElementById('formRegistrarProveedor');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Formulario enviado');
        registrarProveedor();
    });
    
    // Validación en tiempo real para todos los campos
    document.getElementById('nombre').addEventListener('input', function() {
        console.log('Validando nombre...');
        validarNombre();
        verificarBoton();
    });
    
    document.getElementById('contacto').addEventListener('input', function() {
        console.log('Validando contacto...');
        validarContacto();
        verificarBoton();
    });
    
    document.getElementById('telefono').addEventListener('input', function() {
        console.log('Validando teléfono...');
        validarTelefono();
        verificarBoton();
    });
    
    document.getElementById('correo').addEventListener('input', function() {
        console.log('Validando correo...');
        validarCorreo();
        verificarBoton();
    });
    
    // Auto-convertir correo a minúsculas
    document.getElementById('correo').addEventListener('input', function() {
        this.value = this.value.toLowerCase();
    });
    
    // Validar unicidad cuando el usuario sale del campo
    document.getElementById('nombre').addEventListener('blur', function() {
        console.log('Validando unicidad nombre...');
        if (nombreValido) {
            validarNombreUnico();
        }
    });
    });

    function validarNombre() {
    const nombreInput = document.getElementById('nombre');
    const nombre = nombreInput.value.trim();
    
    console.log('Nombre:', nombre);
    
    // Validar formato: letras, números, espacios y caracteres especiales empresariales
    const nombreRegex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s\.\,\/\#\-\&]{3,100}$/;
    nombreValido = nombreRegex.test(nombre);
    
    // Validar solo un espacio entre palabras
    const espaciosMultiples = /\s{2,}/;
    if (espaciosMultiples.test(nombre)) {
        nombreValido = false;
    }
    
    console.log('Nombre válido:', nombreValido);
    
    if (nombreValido) {
        nombreInput.classList.remove('is-invalid');
        nombreInput.classList.add('is-valid');
    } else {
        nombreInput.classList.remove('is-valid');
        nombreInput.classList.add('is-invalid');
    }
    
    return nombreValido;
}

function validarContacto() {
    const contactoInput = document.getElementById('contacto');
    const contacto = contactoInput.value.trim();
    
    console.log('Contacto:', contacto);
    
    // Si está vacío, es válido (campo opcional)
    if (!contacto) {
        contactoInput.classList.remove('is-invalid');
        contactoInput.classList.remove('is-valid');
        return true;
    }
    
    // Validar formato: solo letras y espacios
    const contactoRegex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{1,50}$/;
    let contactoValido = contactoRegex.test(contacto);
    
    // Validar solo un espacio entre palabras
    const espaciosMultiples = /\s{2,}/;
    if (espaciosMultiples.test(contacto)) {
        contactoValido = false;
    }
    
    console.log('Contacto válido:', contactoValido);
    
    if (contactoValido) {
        contactoInput.classList.remove('is-invalid');
        contactoInput.classList.add('is-valid');
    } else {
        contactoInput.classList.remove('is-valid');
        contactoInput.classList.add('is-invalid');
    }
    
    return contactoValido;
}

function validarTelefono() {
    const telefonoInput = document.getElementById('telefono');
    const telefono = telefonoInput.value.trim();
    
    console.log('Teléfono:', telefono);
    
    // Si está vacío, es válido (campo opcional)
    if (!telefono) {
        telefonoInput.classList.remove('is-invalid');
        telefonoInput.classList.remove('is-valid');
        telefonoValido = true;
        return true;
    }
    
    // Validar formato: números, espacios, +, -, (, )
    const telefonoRegex = /^[0-9\s\+\-\(\)]{8,20}$/;
    telefonoValido = telefonoRegex.test(telefono);
    
    console.log('Teléfono válido:', telefonoValido);
    
    if (telefonoValido) {
        telefonoInput.classList.remove('is-invalid');
        telefonoInput.classList.add('is-valid');
    } else {
        telefonoInput.classList.remove('is-valid');
        telefonoInput.classList.add('is-invalid');
    }
    
    return telefonoValido;
}

function validarCorreo() {
    const correoInput = document.getElementById('correo');
    const correo = correoInput.value.trim();
    
    console.log('Correo:', correo);
    
    // Si está vacío, es válido (campo opcional)
    if (!correo) {
        correoInput.classList.remove('is-invalid');
        correoInput.classList.remove('is-valid');
        return true;
    }
    
    // Validar formato: solo minúsculas y dominios específicos
    const correoRegex = /^[a-z0-9._%+-]+@(gmail|hotmail)\.com$/;
    const correoValido = correoRegex.test(correo);
    
    console.log('Correo válido:', correoValido);
    
    if (correoValido) {
        correoInput.classList.remove('is-invalid');
        correoInput.classList.add('is-valid');
    } else {
        correoInput.classList.remove('is-valid');
        correoInput.classList.add('is-invalid');
    }
    
    return correoValido;
}

    async function validarNombreUnico() {
        const nombreInput = document.getElementById('nombre');
        const nombre = nombreInput.value.trim();
        
        if (!nombre) return;
        
        console.log('Validando nombre único:', nombre);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=nombre&valor=${encodeURIComponent(nombre)}`);
            const data = await response.json();
            
            console.log('Respuesta validación nombre:', data);
            
            nombreUnico = data.disponible;
            
            if (nombreUnico) {
                nombreInput.classList.remove('is-invalid');
                nombreInput.classList.add('is-valid');
            } else {
                nombreInput.classList.remove('is-valid');
                nombreInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando nombre:', error);
            // En caso de error, asumimos que está disponible para no bloquear al usuario
            nombreUnico = true;
            verificarBoton();
        }
    }

    async function validarContactoUnico() {
        const contactoInput = document.getElementById('contacto');
        const contacto = contactoInput.value.trim();
        
        if (!contacto) {
            contactoUnico = true;
            verificarBoton();
            return;
        }
        
        console.log('Validando contacto único:', contacto);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=contacto&valor=${encodeURIComponent(contacto)}`);
            const data = await response.json();
            
            console.log('Respuesta validación contacto:', data);
            
            contactoUnico = data.disponible;
            
            if (contactoUnico) {
                contactoInput.classList.remove('is-invalid');
                contactoInput.classList.add('is-valid');
            } else {
                contactoInput.classList.remove('is-valid');
                contactoInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando contacto:', error);
            contactoUnico = true;
            verificarBoton();
        }
    }

    async function validarCorreoUnico() {
        const correoInput = document.getElementById('correo');
        const correo = correoInput.value.trim();
        
        if (!correo) {
            correoUnico = true;
            verificarBoton();
            return;
        }
        
        console.log('Validando correo único:', correo);
        
        try {
            const response = await fetch(`/sistema/public/index.php?route=compras&caso=validarProveedor&campo=correo&valor=${encodeURIComponent(correo)}`);
            const data = await response.json();
            
            console.log('Respuesta validación correo:', data);
            
            correoUnico = data.disponible;
            
            if (correoUnico) {
                correoInput.classList.remove('is-invalid');
                correoInput.classList.add('is-valid');
            } else {
                correoInput.classList.remove('is-valid');
                correoInput.classList.add('is-invalid');
            }
            
            verificarBoton();
            
        } catch (error) {
            console.error('Error validando correo:', error);
            correoUnico = true;
            verificarBoton();
        }
    }

    function verificarBoton() {
        const btnRegistrar = document.getElementById('btnRegistrar');
        
        console.log('Estado validaciones:', {
            nombreValido,
            telefonoValido,
            nombreUnico,
            contactoUnico,
            correoUnico
        });
        
        // Solo requerimos que nombre y teléfono sean válidos y únicos
        const formularioValido = nombreValido && 
                               telefonoValido && 
                               nombreUnico;
        
        console.log('Formulario válido:', formularioValido);
        console.log('Botón habilitado:', formularioValido);
        
        btnRegistrar.disabled = !formularioValido;
    }

    function validarFormularioCompleto() {
    console.log('Validando formulario completo...');
    
    // Solo validamos campo requerido (nombre)
    if (!nombreValido) {
        alert('Por favor ingrese un nombre válido para el proveedor (3-100 caracteres, permite: letras, números, espacios y . , / # - &).');
        return false;
    }
    
    if (!nombreUnico) {
        alert('Este nombre de proveedor ya existe en el sistema.');
        return false;
    }
    
    console.log('Formulario validado correctamente');
    return true;
}
// Función para cargar productos disponibles
function cargarProductosDisponibles() {
    fetch('/sistema/public/index.php?route=compras&caso=obtenerProductosActivos')
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data) {
            const selectProductos = document.getElementById('productos');
            selectProductos.innerHTML = '<option value="">Seleccione los productos que provee</option>';
            
            data.data.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.ID_PROVEEDOR_PRODUCTO;
                option.textContent = `${producto.NOMBRE_PRODUCTO} - L. ${producto.PRECIO_UNITARIO}`;
                option.setAttribute('data-precio', producto.PRECIO_UNITARIO);
                selectProductos.appendChild(option);
            });
            
            // Cargar productos actuales del proveedor
            cargarProductosProveedor();
        }
    })
    .catch(error => {
        console.error('Error al cargar productos:', error);
    });
}

// Función para cargar productos actuales del proveedor
function cargarProductosProveedor() {
    const idProveedor = document.getElementById('id_proveedor').value;
    
    fetch(`/sistema/public/index.php?route=compras&caso=obtenerProductosProveedor&id_proveedor=${idProveedor}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data) {
            const selectProductos = document.getElementById('productos');
            data.data.forEach(producto => {
                const option = Array.from(selectProductos.options).find(
                    opt => parseInt(opt.value) === producto.ID_PROVEEDOR_PRODUCTO
                );
                if (option) {
                    option.selected = true;
                }
            });
            actualizarProductosSeleccionados();
        }
    })
    .catch(error => {
        console.error('Error al cargar productos del proveedor:', error);
    });
}

// Función para actualizar la visualización de productos seleccionados
function actualizarProductosSeleccionados() {
    const selectProductos = document.getElementById('productos');
    const divSeleccionados = document.getElementById('productos-seleccionados');
    const seleccionados = Array.from(selectProductos.selectedOptions);
    
    if (seleccionados.length === 0) {
        divSeleccionados.innerHTML = '<span class="text-muted">No hay productos seleccionados</span>';
        return;
    }
    
    let html = '<div class="selected-products-list">';
    html += '<strong>Productos seleccionados:</strong>';
    html += '<ul class="list-group mt-2">';
    
    seleccionados.forEach(option => {
        if (option.value) {
            html += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${option.textContent}
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="deseleccionarProducto(${option.value})">
                        <i class="bi bi-x"></i>
                    </button>
                </li>
            `;
        }
    });
    
    html += '</ul></div>';
    divSeleccionados.innerHTML = html;
}

// Función para deseleccionar un producto
function deseleccionarProducto(idProducto) {
    const selectProductos = document.getElementById('productos');
    const option = Array.from(selectProductos.options).find(opt => parseInt(opt.value) === idProducto);
    if (option) {
        option.selected = false;
        actualizarProductosSeleccionados();
    }
}

// Event listener para el select múltiple
document.addEventListener('DOMContentLoaded', function() {
    const selectProductos = document.getElementById('productos');
    if (selectProductos) {
        selectProductos.addEventListener('change', actualizarProductosSeleccionados);
        cargarProductosDisponibles();
    }
});


function registrarProveedor() {
    console.log('Iniciando registro de proveedor...');
    
    const btnRegistrar = document.getElementById('btnRegistrar');
    const originalText = btnRegistrar.innerHTML;
    
    // Mostrar loading
    btnRegistrar.innerHTML = '<span class="loading-spinner"></span> Registrando...';
    btnRegistrar.disabled = true;
    
    // Obtener productos seleccionados
    const selectProductos = document.getElementById('productos');
    const productosSeleccionados = Array.from(selectProductos.selectedOptions)
        .map(option => parseInt(option.value))
        .filter(id => id > 0);
    
    // Obtener datos del formulario
    const formData = new FormData(document.getElementById('formRegistrarProveedor'));
    const datos = Object.fromEntries(formData);
    
    // Agregar productos al objeto de datos
    datos.productos = productosSeleccionados;
    
    // Limpiar espacios en blanco
    Object.keys(datos).forEach(key => {
        if (typeof datos[key] === 'string') {
            datos[key] = datos[key].trim();
        }
    });
    
    console.log('Datos a enviar:', datos);
    
    fetch('/sistema/public/index.php?route=compras&caso=registrarProveedor', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => {
        console.log('Respuesta HTTP:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        
        if (data.status === 201) {
            // Registro exitoso
            alert(data.message);
            window.location.href = '/sistema/public/gestion-proveedores';
        } else {
            // Error
            alert(data.message || 'Error al registrar el proveedor');
            btnRegistrar.innerHTML = originalText;
            btnRegistrar.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        alert('Error de conexión. Intente nuevamente.');
        btnRegistrar.innerHTML = originalText;
        btnRegistrar.disabled = false;
    });
}

    function cancelarRegistro() {
        if (confirm('¿Está seguro que desea cancelar el registro? Los datos no guardados se perderán.')) {
            window.location.href = '/sistema/public/gestion-proveedores';
        }
    }
</script>
</body>
</html>