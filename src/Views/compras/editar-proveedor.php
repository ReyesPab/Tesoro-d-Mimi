<?php
// src/Views/compras/editar-proveedor.php

// Obtener ID del proveedor desde la URL
$id_proveedor = $_GET['id'] ?? null;

if (!$id_proveedor || !is_numeric($id_proveedor)) {
    header('Location: /sistema/public/gestion-proveedores');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Editar Proveedor - Sistema de Gestión</title>
    
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
        @keyframes spin {
            to { transform: rotate(360deg); }
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
                    <h1 class="h2 mb-0">Editar Proveedor</h1>
                    <a href="/sistema/public/gestion-proveedores" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Proveedores
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
                            <form id="formEditarProveedor" novalidate>
                                <input type="hidden" id="id_proveedor" name="id_proveedor" value="<?= $id_proveedor ?>">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label required-label">Nombre del Proveedor</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               placeholder="Ingrese el nombre completo del proveedor"
                                               minlength="5" maxlength="100">
                                        <div class="valid-feedback">Nombre válido</div>
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 5 y 100 caracteres y no puede tener espacios consecutivos
                                        </div>
                                        <div class="form-text">Mínimo 5 caracteres, máximo 100. Se permiten letras, números y caracteres especiales (1/.#@%_&*'"!?). No se permiten espacios consecutivos.</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contacto" class="form-label">Persona de Contacto</label>
                                        <input type="text" class="form-control" id="contacto" name="contacto" 
                                               placeholder="Nombre del contacto principal"
                                               maxlength="50"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{0,50}">
                                        <div class="valid-feedback">Contacto válido</div>
                                        <div class="invalid-feedback">
                                            Solo se permiten letras y espacios (máximo 50 caracteres)
                                        </div>
                                        <div class="form-text">Máximo 50 letras. Solo se permiten letras y espacios.</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" 
                                               placeholder="Ej: +504 2234-5678 o 2234-5678"
                                               maxlength="20">
                                        <div class="valid-feedback">Teléfono válido</div>
                                        <div class="form-text">Puede ingresar teléfonos nacionales o internacionales</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="correo" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="correo" name="correo" 
                                               placeholder="correo@gmail.com o correo@hotmail.com"
                                               pattern="[a-z0-9._%+-]+@(gmail|hotmail)\.com$"
                                               maxlength="50">
                                        <div class="valid-feedback">Correo válido</div>
                                        <div class="invalid-feedback">
                                            Solo se permiten correos de Gmail (@gmail.com) o Hotmail (@hotmail.com) en minúsculas y sin espacios
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
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label required-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="">Seleccione un estado</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                                    </div>
                                </div>
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
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarEdicion()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                                <i class="bi bi-check-circle"></i> Guardar Cambios
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
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar datos del proveedor
            cargarDatosProveedor();
            
            const form = document.getElementById('formEditarProveedor');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarCambios();
            });
            
            // Validación en tiempo real
            document.getElementById('nombre').addEventListener('input', validarNombre);
            document.getElementById('correo').addEventListener('input', validarCorreo);
            document.getElementById('correo').addEventListener('input', function() {
                this.value = this.value.toLowerCase().replace(/\s/g, '');
            });
        });

        function cargarDatosProveedor() {
            const idProveedor = document.getElementById('id_proveedor').value;
            
            fetch(`/sistema/public/index.php?route=compras&caso=obtenerProveedorPorId&id_proveedor=${idProveedor}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data) {
                    llenarFormulario(data.data);
                } else {
                    alert('Error al cargar los datos del proveedor: ' + (data.message || 'Proveedor no encontrado'));
                    window.location.href = '/sistema/public/gestion-proveedores';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cargar los datos del proveedor');
                window.location.href = '/sistema/public/gestion-proveedores';
            });
        }

        function llenarFormulario(proveedor) {
            document.getElementById('nombre').value = proveedor.NOMBRE || '';
            document.getElementById('contacto').value = proveedor.CONTACTO || '';
            document.getElementById('telefono').value = proveedor.TELEFONO || '';
            document.getElementById('correo').value = proveedor.CORREO || '';
            document.getElementById('direccion').value = proveedor.DIRECCION || '';
            document.getElementById('estado').value = proveedor.ESTADO || '';
            
            // Validar campos después de llenarlos
            validarNombre();
            validarCorreo();
        }

        function validarNombre() {
    const nombreInput = document.getElementById('nombre');
    const nombre = nombreInput.value.trim();
    
    // Validar longitud
    if (nombre.length < 5 || nombre.length > 100) {
        nombreInput.classList.remove('is-valid');
        nombreInput.classList.add('is-invalid');
        return false;
    }
    
    // Validar que no tenga espacios consecutivos
    if (/\s{2,}/.test(nombre)) {
        nombreInput.classList.remove('is-valid');
        nombreInput.classList.add('is-invalid');
        return false;
    }
    
    // Validar caracteres permitidos (opcional, para consistencia)
    const nombreRegex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\/.#@%_&*'"!?\s]{5,100}$/;
    if (!nombreRegex.test(nombre)) {
        nombreInput.classList.remove('is-valid');
        nombreInput.classList.add('is-invalid');
        return false;
    }
    
    // Si pasa todas las validaciones
    nombreInput.classList.remove('is-invalid');
    nombreInput.classList.add('is-valid');
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
        function validarCorreo() {
            const correoInput = document.getElementById('correo');
            const correo = correoInput.value.trim();
            
            // Si está vacío, es válido (opcional)
            if (correo === '') {
                correoInput.classList.remove('is-invalid', 'is-valid');
                return true;
            }
            
            // Convertir a minúsculas y eliminar espacios
            correoInput.value = correo.toLowerCase().replace(/\s/g, '');
            
            const correoRegex = /^[a-z0-9._%+-]+@(gmail|hotmail)\.com$/;
            const correoValido = correoRegex.test(correoInput.value);
            
            if (correoValido) {
                correoInput.classList.remove('is-invalid');
                correoInput.classList.add('is-valid');
            } else {
                correoInput.classList.remove('is-valid');
                correoInput.classList.add('is-invalid');
            }
            
            return correoValido;
        }
function guardarCambios() {
    const btnGuardar = document.getElementById('btnGuardar');
    const originalText = btnGuardar.innerHTML;
    
    // Validar campos requeridos
    if (!validarNombre()) {
        alert('Por favor ingrese un nombre válido para el proveedor (5-100 caracteres, sin espacios consecutivos).');
        return;
    }
    
    const estado = document.getElementById('estado').value;
    if (!estado) {
        alert('Por favor seleccione un estado para el proveedor.');
        return;
    }
    
    // Obtener productos seleccionados
    const selectProductos = document.getElementById('productos');
    const productosSeleccionados = Array.from(selectProductos.selectedOptions)
        .map(option => parseInt(option.value))
        .filter(id => id > 0);
    
    // Mostrar loading
    btnGuardar.innerHTML = '<span class="loading-spinner"></span> Guardando...';
    btnGuardar.disabled = true;
    
    // Obtener datos del formulario
    const formData = new FormData(document.getElementById('formEditarProveedor'));
    const datos = Object.fromEntries(formData);
    
    // Agregar productos al objeto de datos
    datos.productos = productosSeleccionados;
    
    // Limpiar espacios en blanco
    Object.keys(datos).forEach(key => {
        if (typeof datos[key] === 'string') {
            datos[key] = datos[key].trim();
        }
    });
    
    fetch('/sistema/public/index.php?route=compras&caso=editarProveedor', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 200) {
            alert(data.message);
            window.location.href = '/sistema/public/gestion-proveedores';
        } else {
            alert(data.message || 'Error al actualizar el proveedor');
            btnGuardar.innerHTML = originalText;
            btnGuardar.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión. Intente nuevamente.');
        btnGuardar.innerHTML = originalText;
        btnGuardar.disabled = false;
    });
}
        function cancelarEdicion() {
            if (confirm('¿Está seguro que desea cancelar la edición? Los cambios no guardados se perderán.')) {
                window.location.href = '/sistema/public/gestion-proveedores';
            }
        }
    </script>
</body>
</html>