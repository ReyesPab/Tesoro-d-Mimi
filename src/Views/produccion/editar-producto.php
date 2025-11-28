<?php
// src/Views/editar-producto.php

// Obtener ID del producto desde la URL
$id_producto = $_GET['id'] ?? null;

if (!$id_producto || !is_numeric($id_producto)) {
    header('Location: /sistema/public/gestion-productos');
    exit;
}

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión
$sesion_activa = false;
$variables_sesion = [
    'logged_in', 'iniciada', 'USUARIO', 'usuario', 
    'ID_USUARIO', 'id_usuario', 'user_name', 'usuario_nombre'
];

foreach ($variables_sesion as $variable) {
    if (isset($_SESSION[$variable])) {
        $sesion_activa = true;
        break;
    }
}

if (!$sesion_activa) {
    header('Location: /sistema/public/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Editar Producto - Sistema de Gestión</title>
    
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
        
        .numeric-input {
            text-align: right;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .price-input {
            text-align: right;
            font-weight: 600;
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
                    <h1 class="h2 mb-0">Editar Producto</h1>
                    <a href="/sistema/public/gestion-productos" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Productos
                    </a>
                </div>
            </div>

             

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-box-seam me-2"></i>Información del Producto
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formEditarProducto" novalidate>
                                <input type="hidden" id="id_producto" name="id_producto" value="<?= $id_producto ?>">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label required-label">Nombre del Producto</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               placeholder="Ingrese el nombre del producto"
                                               minlength="3" maxlength="100"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}">
                                        <div class="valid-feedback">Nombre válido</div>
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 3 y 100 caracteres (solo letras, números y espacios)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="2" placeholder="Descripción detallada del producto"
                                                  maxlength="255"></textarea>
                                        <div class="form-text">Máximo 255 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="precio" class="form-label required-label">Precio (L)</label>
                                        <input type="number" class="form-control price-input" id="precio" name="precio" required 
                                               placeholder="0.00" min="0.01" step="0.01">
                                        <div class="valid-feedback">Precio válido</div>
                                        <div class="invalid-feedback">El precio debe ser mayor a 0</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="id_unidad_medida" class="form-label required-label">Unidad de Medida</label>
                                        <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                            <option value="">Seleccione una unidad</option>
                                            <!-- Las unidades se cargarán dinámicamente -->
                                        </select>
                                        <div class="valid-feedback">Unidad válida</div>
                                        <div class="invalid-feedback">Por favor seleccione una unidad de medida</div>
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
                                    
                                     
                                
                                <!-- Información de auditoría -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="bg-light p-3 rounded">
                                            <h6 class="mb-3">Información de Auditoría</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small><strong>Creado por:</strong> <span id="display_creado_por">-</span></small><br>
                                                    <small><strong>Fecha creación:</strong> <span id="display_fecha_creacion">-</span></small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small><strong>Modificado por:</strong> <span id="display_modificado_por">-</span></small><br>
                                                    <small><strong>Última modificación:</strong> <span id="display_fecha_modificacion">-</span></small>
                                                </div>
                                            </div>
                                        </div>
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
            console.log('🚀 Documento cargado, iniciando procesos...');
            
            // Cargar los datos del producto
            cargarDatosProducto();
            
            const form = document.getElementById('formEditarProducto');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarCambios();
            });
            
            // Validación en tiempo real para máximo > mínimo
            document.getElementById('minimo').addEventListener('input', validarRangos);
            document.getElementById('maximo').addEventListener('input', validarRangos);
        });

        function cargarDatosProducto() {
            const idProducto = document.getElementById('id_producto').value;
            
            fetch(`/sistema/public/index.php?route=produccion&caso=obtenerProductoPorId&id_producto=${idProducto}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data) {
                    llenarFormulario(data.data);
                } else {
                    alert('Error al cargar los datos del producto: ' + (data.message || 'Producto no encontrado'));
                    window.location.href = '/sistema/public/gestion-productos';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cargar los datos del producto');
                window.location.href = '/sistema/public/gestion-productos';
            });
        }

        function llenarFormulario(producto) {
    console.log('📦 Datos de producto recibidos:', producto);
    
    // Llenar campos del formulario (sin minimo y maximo)
    document.getElementById('nombre').value = producto.NOMBRE || '';
    document.getElementById('descripcion').value = producto.DESCRIPCION || '';
    document.getElementById('precio').value = parseFloat(producto.PRECIO || 0).toFixed(2);
    document.getElementById('estado').value = producto.ESTADO || '';
    
    // Llenar el select de unidad de medida con la opción actual
    const selectUnidad = document.getElementById('id_unidad_medida');
    
    // Limpiar opciones existentes
    while (selectUnidad.children.length > 1) {
        selectUnidad.removeChild(selectUnidad.lastChild);
    }
    
    // Crear opción con la unidad actual
    if (producto.ID_UNIDAD_MEDIDA && producto.UNIDAD) {
        const option = document.createElement('option');
        option.value = producto.ID_UNIDAD_MEDIDA;
        option.textContent = `${producto.UNIDAD} - ${producto.DESC_UNIDAD || ''}`;
        option.selected = true;
        selectUnidad.appendChild(option);
        console.log('✅ Unidad cargada:', producto.UNIDAD);
    } else {
        console.warn('⚠️ No se encontró información de unidad de medida');
    }
    
    // Llenar información de display (sin cantidad)
    document.getElementById('display_creado_por').textContent = producto.CREADO_POR || 'SISTEMA';
    document.getElementById('display_fecha_creacion').textContent = producto.FECHA_CREACION_FORMATEADA || '-';
    document.getElementById('display_modificado_por').textContent = producto.MODIFICADO_POR || 'No modificado';
    document.getElementById('display_fecha_modificacion').textContent = producto.FECHA_MODIFICACION_FORMATEADA || 'No modificado';
}

function guardarCambios() {
    const btnGuardar = document.getElementById('btnGuardar');
    const originalText = btnGuardar.innerHTML;
    
    // Mostrar loading
    btnGuardar.innerHTML = '<span class="loading-spinner"></span> Guardando...';
    btnGuardar.disabled = true;
    
    // Obtener datos del formulario
    const formData = new FormData(document.getElementById('formEditarProducto'));
    const datos = Object.fromEntries(formData);
    
    // Convertir a números (sin minimo y maximo)
    datos.precio = parseFloat(datos.precio);
    datos.id_unidad_medida = parseInt(datos.id_unidad_medida);
    
    // Limpiar espacios en blanco
    Object.keys(datos).forEach(key => {
        if (typeof datos[key] === 'string') {
            datos[key] = datos[key].trim();
        }
    });
    
    fetch('/sistema/public/index.php?route=produccion&caso=actualizarProducto', {
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
            window.location.href = '/sistema/public/gestion-productos';
        } else {
            alert(data.message || 'Error al actualizar el producto');
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
                window.location.href = '/sistema/public/gestion-productos';
            }
        }
    </script>
</body>
</html>