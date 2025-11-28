<?php
// src/Views/compras/editar-productos-proveedores.php

// Obtener ID del producto desde la URL
$id_producto_proveedor = $_GET['id'] ?? null;

if (!$id_producto_proveedor || !is_numeric($id_producto_proveedor)) {
    header('Location: /sistema/public/gestion-productos-proveedor');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Editar Producto Proveedor - Sistema de Gestión</title>
    
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
        
        .proveedor-info {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .inventario-info {
            background-color: #d1ecf1;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
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
                    <h1 class="h2 mb-0">Editar Producto de Proveedor</h1>
                    <a href="/sistema/public/gestion-productos-proveedor" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Productos Proveedor
                    </a>
                </div>
            </div>

            <!-- Información del Producto -->
            <div class="info-box">
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID Producto:</strong> <span id="display_id"><?= $id_producto_proveedor ?></span>
                    </div>

                </div>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-box-seam me-2"></i>Información del Producto
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formEditarProductoProveedor" novalidate>
                                <input type="hidden" id="id_proveedor_producto" name="id_proveedor_producto" value="<?= $id_producto_proveedor ?>">
                                

                                <!-- Información de Inventario (solo lectura) -->
                                <div class="inventario-info">
                                    <h6 class="mb-3">Información de Inventario</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Stock Actual:</strong> <span id="info_stock_actual">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Estado Inventario:</strong> <span id="info_estado_inventario">-</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>ID Materia Prima:</strong> <span id="info_id_materia_prima">-</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre_producto" class="form-label required-label">Nombre del Producto</label>
                                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required 
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
                                        <label for="id_unidad_medida" class="form-label required-label">Unidad de Medida</label>
                                        <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                            <option value="">Seleccione una unidad</option>
                                            <!-- Las unidades se cargarán dinámicamente -->
                                        </select>
                                        <div class="valid-feedback">Unidad válida</div>
                                        <div class="invalid-feedback">Por favor seleccione una unidad de medida</div>
                                    </div>
                                    
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
                                    <div class="col-md-4 mb-3">
                                        <label for="precio_unitario" class="form-label required-label">Precio Unitario</label>
                                        <div class="input-group">
                                            <span class="input-group-text">L</span>
                                            <input type="number" class="form-control numeric-input" id="precio_unitario" name="precio_unitario" required 
                                                   placeholder="0.00" min="0.01" step="0.01">
                                        </div>
                                        <div class="valid-feedback">Precio válido</div>
                                        <div class="invalid-feedback">El precio unitario debe ser mayor a 0</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="minimo" class="form-label required-label">Stock Mínimo</label>
                                        <input type="number" class="form-control numeric-input" id="minimo" name="minimo" required 
                                               placeholder="0" min="0" step="1">
                                        <div class="valid-feedback">Stock mínimo válido</div>
                                        <div class="invalid-feedback">El stock mínimo debe ser mayor o igual a 0</div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="maximo" class="form-label required-label">Stock Máximo</label>
                                        <input type="number" class="form-control numeric-input" id="maximo" name="maximo" required 
                                               placeholder="100" min="1" step="1">
                                        <div class="valid-feedback">Stock máximo válido</div>
                                        <div class="invalid-feedback">El stock máximo debe ser mayor al mínimo</div>
                                    </div>
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
    
    // Cargar primero las unidades de medida, luego los datos del producto
    cargarUnidadesMedida()
        .then((unidades) => {
            console.log('✅ Unidades cargadas correctamente:', unidades.length);
            return cargarDatosProductoProveedor();
        })
        .then(() => {
            console.log('✅ Todos los datos cargados exitosamente');
        })
        .catch(error => {
            console.error('❌ Error en carga inicial:', error);
            alert('Error al cargar datos iniciales: ' + error);
            // Intentar cargar solo los datos del producto como fallback
            cargarDatosProductoProveedor();
        });
    
    const form = document.getElementById('formEditarProductoProveedor');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarCambios();
    });
});



    function cargarDatosProductoProveedor() {
        const idProductoProveedor = document.getElementById('id_proveedor_producto').value;
        
        console.log('🔍 Cargando datos del producto ID:', idProductoProveedor);
        
        fetch(`/sistema/public/index.php?route=compras&caso=obtenerProductoProveedorCompleto&id=${idProductoProveedor}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Datos recibidos:', data);
            if (data.status === 200 && data.data) {
                llenarFormulario(data.data);
            } else {
                alert('Error al cargar los datos del producto: ' + (data.message || 'Producto no encontrado'));
                window.location.href = '/sistema/public/gestion-productos-proveedor';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al cargar los datos del producto');
            window.location.href = '/sistema/public/gestion-productos-proveedor';
        });
    }

function cargarUnidadesMedida() {
    console.log('🔍 Cargando unidades de medida...');
    
    return new Promise((resolve, reject) => {
        fetch('/sistema/public/index.php?route=compras&caso=obtenerUnidadesMedidaProductosProveedores')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Respuesta completa de unidades:', data);
            
            // Verificar la estructura de la respuesta
            if (data.status === 200 && data.data) {
                llenarUnidadesMedida(data.data);
                resolve(data.data);
            } else if (data.data) { // Si viene directamente el array en data
                llenarUnidadesMedida(data.data);
                resolve(data.data);
            } else {
                console.error('Error en estructura de respuesta:', data);
                reject('Error en la estructura de la respuesta: ' + (data.message || 'Estructura desconocida'));
            }
        })
        .catch(error => {
            console.error('Error en fetch:', error);
            reject('Error de conexión al cargar unidades de medida: ' + error.message);
        });
    });
}

function llenarUnidadesMedida(unidades) {
    const selectUnidad = document.getElementById('id_unidad_medida');
    
    console.log('🔄 Llenando select de unidades con:', unidades);
    
    // Validar que unidades sea un array
    if (!Array.isArray(unidades)) {
        console.error('❌ Error: unidades no es un array:', unidades);
        return;
    }
    
    if (unidades.length === 0) {
        console.warn('⚠️ No se recibieron unidades de medida');
        return;
    }
    
    // Limpiar opciones excepto la primera
    while (selectUnidad.children.length > 1) {
        selectUnidad.removeChild(selectUnidad.lastChild);
    }
    
    // Agregar unidades
    unidades.forEach(unidad => {
        const option = document.createElement('option');
        option.value = unidad.ID_UNIDAD_MEDIDA;
        option.textContent = `${unidad.UNIDAD} - ${unidad.DESCRIPCION || ''}`;
        selectUnidad.appendChild(option);
    });
    
    console.log('✅ Unidades de medida cargadas:', unidades.length, 
               'Opciones en select:', selectUnidad.children.length);
}

// En llenarFormulario(), eliminar las líneas que llenan info del proveedor:
function llenarFormulario(producto) {
    console.log('📝 Llenando formulario con:', producto);
    
    // Llenar campos del formulario
    document.getElementById('nombre_producto').value = producto.NOMBRE_PRODUCTO || '';
    document.getElementById('descripcion').value = producto.DESCRIPCION || '';
    document.getElementById('precio_unitario').value = parseFloat(producto.PRECIO_UNITARIO || 0).toFixed(2);
    document.getElementById('estado').value = producto.ESTADO || '';
    
    setTimeout(() => {
        if (producto.ID_UNIDAD_MEDIDA) {
            const selectUnidad = document.getElementById('id_unidad_medida');
            selectUnidad.value = producto.ID_UNIDAD_MEDIDA;
            console.log('✅ Unidad de medida establecida:', producto.ID_UNIDAD_MEDIDA);
        }
    }, 100);
    
    document.getElementById('minimo').value = producto.MINIMO || 0;
    document.getElementById('maximo').value = producto.MAXIMO || 100;
    
    // Llenar información de display (sin proveedor)
    document.getElementById('display_creado_por').textContent = producto.CREADO_POR || 'SISTEMA';
    document.getElementById('display_fecha_creacion').textContent = producto.FECHA_CREACION_FORMATEADA || '-';
    document.getElementById('display_modificado_por').textContent = producto.MODIFICADO_POR || 'No modificado';
    document.getElementById('display_fecha_modificacion').textContent = producto.FECHA_MODIFICACION_FORMATEADA || 'No modificado';
    
    // Llenar información de inventario
    document.getElementById('info_stock_actual').textContent = producto.CANTIDAD || '0';
    document.getElementById('info_estado_inventario').textContent = getEstadoInventarioTexto(producto.ESTADO_INVENTARIO);
    document.getElementById('info_id_materia_prima').textContent = producto.ID_MATERIA_PRIMA || 'No asignado';
    
    // Aplicar color al estado de inventario
    const estadoElement = document.getElementById('info_estado_inventario');
    estadoElement.className = getEstadoInventarioClass(producto.ESTADO_INVENTARIO);
}

    function getEstadoInventarioTexto(estado) {
        switch(estado) {
            case 'CRITICO': return 'CRÍTICO';
            case 'BAJO': return 'BAJO';
            case 'EXCESO': return 'EXCESO';
            case 'NORMAL': return 'NORMAL';
            default: return 'SIN DATOS';
        }
    }

    function getEstadoInventarioClass(estado) {
        switch(estado) {
            case 'CRITICO': return 'badge bg-danger';
            case 'BAJO': return 'badge bg-warning';
            case 'EXCESO': return 'badge bg-info';
            case 'NORMAL': return 'badge bg-success';
            default: return 'badge bg-secondary';
        }
    }

    function guardarCambios() {
        const btnGuardar = document.getElementById('btnGuardar');
        const originalText = btnGuardar.innerHTML;
        
        // Validar que la unidad de medida esté seleccionada
        const idUnidadMedida = document.getElementById('id_unidad_medida').value;
        if (!idUnidadMedida) {
            alert('❌ Debe seleccionar una unidad de medida.');
            return;
        }
        
        // Validar campos requeridos
        const precio = parseFloat(document.getElementById('precio_unitario').value);
        const minimo = parseFloat(document.getElementById('minimo').value);
        const maximo = parseFloat(document.getElementById('maximo').value);
        
        if (precio <= 0) {
            alert('❌ El precio unitario debe ser mayor a 0.');
            return;
        }
        
        if (minimo < 0) {
            alert('❌ El stock mínimo debe ser mayor o igual a 0.');
            return;
        }
        
        if (maximo <= 0) {
            alert('❌ El stock máximo debe ser mayor a 0.');
            return;
        }
        
        if (maximo <= minimo) {
            alert('❌ El stock máximo debe ser mayor al stock mínimo.');
            return;
        }
        
        // Mostrar loading
        btnGuardar.innerHTML = '<span class="loading-spinner"></span> Guardando...';
        btnGuardar.disabled = true;
        
        // Obtener datos del formulario
        const formData = new FormData(document.getElementById('formEditarProductoProveedor'));
        const datos = Object.fromEntries(formData);
        
        // Convertir a números
        datos.precio_unitario = parseFloat(datos.precio_unitario);
        datos.id_unidad_medida = parseInt(datos.id_unidad_medida);
        datos.id_proveedor_producto = parseInt(datos.id_proveedor_producto);
        datos.minimo = parseFloat(datos.minimo);
        datos.maximo = parseFloat(datos.maximo);
        
        // Agregar ID de usuario para la bitácora (necesario según el procedimiento almacenado)
        datos.id_usuario = <?= $_SESSION['usuario']['id_usuario'] ?? 1 ?>;
        
        // Limpiar espacios en blanco
        Object.keys(datos).forEach(key => {
            if (typeof datos[key] === 'string') {
                datos[key] = datos[key].trim();
            }
        });
        
        console.log('📤 Datos enviados:', datos);
        
        fetch('/sistema/public/index.php?route=compras&caso=editarProductoProveedor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            console.log('📥 Respuesta HTTP:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📥 Respuesta JSON:', data);
            if (data.status === 200) {
                alert('✅ ' + data.message);
                window.location.href = '/sistema/public/gestion-productos-proveedor';
            } else {
                alert('❌ ' + (data.message || 'Error al actualizar el producto'));
                btnGuardar.innerHTML = originalText;
                btnGuardar.disabled = false;
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión:', error);
            alert('❌ Error de conexión. Intente nuevamente.');
            btnGuardar.innerHTML = originalText;
            btnGuardar.disabled = false;
        });
    }

    function cancelarEdicion() {
        if (confirm('¿Está seguro que desea cancelar la edición? Los cambios no guardados se perderán.')) {
            window.location.href = '/sistema/public/gestion-productos-proveedor';
        }
    }
</script>   
</body>
</html>