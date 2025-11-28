<?php
// src/Views/compras/gestion-relacion-producto-proveedor.php

use App\config\SessionHelper;
use App\models\permisosModel;

SessionHelper::startSession();
$userId = SessionHelper::getUserId();

$permisoGestionar = permisosModel::verificarPermiso($userId, 'GESTION_RELACION_PRODUCTO_PROVEEDOR', 'CONSULTAR');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión Relación Producto-Proveedor - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .selected-products {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .product-item {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
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
        
        .required-label::after {
            content: " *";
            color: #dc3545;
        }
        
        .productos-lista .badge {
            font-size: 0.75rem;
            margin: 2px;
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
                    <h1 class="h2 mb-0">Gestión Relación Producto-Proveedor</h1>
                    <a href="/sistema/public/gestion-proveedores" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Proveedores
                    </a>
                </div>
            </div>

            <?php if ($permisoGestionar): ?>
            
            <!-- Formulario de Gestión -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-link me-2"></i>Asociar Productos a Proveedor
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formRelacionProductoProveedor">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="proveedor" class="form-label required-label">Seleccionar Proveedor</label>
                                        <select class="form-select select2" id="proveedor" name="proveedor" required>
                                            <option value="">Buscar proveedor...</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor seleccione un proveedor</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="productos" class="form-label">Productos Disponibles</label>
                                        <select class="form-select select2" id="productos" name="productos[]" multiple>
                                            <option value="">Buscar productos...</option>
                                        </select>
                                        <div class="form-text">Seleccione los productos que provee este proveedor</div>
                                    </div>
                                </div>
                                
                                <!-- Productos Seleccionados -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-list-check me-2"></i>
                                                    Productos Seleccionados
                                                    <span class="badge bg-primary ms-2" id="contador-productos">0</span>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="productos-seleccionados" class="selected-products">
                                                    <div class="text-center text-muted py-4">
                                                        <i class="bi bi-inbox display-4"></i>
                                                        <p class="mt-2">No hay productos seleccionados</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormulario()">
                                                <i class="bi bi-arrow-clockwise"></i> Limpiar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                                <i class="bi bi-check-circle"></i> Guardar Relación
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Relaciones Existentes -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-diagram-3 me-2"></i>Relaciones Existentes
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaRelaciones">
                                    <thead>
                                        <tr>
                                            <th>Proveedor</th>
                                            <th>Productos Asociados</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyRelaciones">
                                        <!-- Las relaciones se cargarán aquí -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No tiene permisos para gestionar la relación producto-proveedor.
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        let proveedorSeleccionado = null;
        let productosSeleccionados = [];

        $(document).ready(function() {
            // Inicializar Select2 con eventos CORREGIDOS
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Seleccione...';
                },
                allowClear: true
            });

            // Eventos CORREGIDOS - Usar jQuery para Select2
            $('#proveedor').on('change', function() {
                proveedorSeleccionado = $(this).val();
                console.log('🔄 Proveedor seleccionado:', proveedorSeleccionado);
                if (proveedorSeleccionado) {
                    cargarProductosProveedor(proveedorSeleccionado);
                } else {
                    limpiarProductosSeleccionados();
                }
            });

            // EVENTO CORREGIDO - Usar el evento change de Select2
            $('#productos').on('change', function() {
                console.log('🔄 Cambio en productos detectado:', $(this).val());
                actualizarProductosSeleccionados();
            });

            // Evento del formulario
            $('#formRelacionProductoProveedor').on('submit', function(e) {
                e.preventDefault();
                guardarRelacion();
            });

            // Cargar datos iniciales
            cargarProveedores();
            cargarProductos();
            cargarRelacionesExistentes();
        });

        // Función principal para cargar proveedores
        function cargarProveedores() {
            console.log('🔄 Cargando proveedores...');
            
            fetch('/sistema/public/index.php?route=compras&caso=listarProveedores')
            .then(response => {
                console.log('📊 Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Datos de proveedores recibidos:', data);
                
                if (data.status === 200 && data.data && Array.isArray(data.data)) {
                    const selectProveedor = $('#proveedor');
                    selectProveedor.empty().append('<option value="">Buscar proveedor...</option>');
                    
                    // Filtrar solo proveedores activos
                    const proveedoresActivos = data.data.filter(proveedor => 
                        proveedor.ESTADO === 'ACTIVO'
                    );
                    
                    proveedoresActivos.forEach(proveedor => {
                        selectProveedor.append(
                            $('<option>', {
                                value: proveedor.ID_PROVEEDOR,
                                text: proveedor.NOMBRE
                            })
                        );
                    });
                    
                    // Actualizar Select2 después de agregar opciones
                    selectProveedor.trigger('change');
                    
                    console.log('✅ Proveedores cargados correctamente: ' + proveedoresActivos.length + ' encontrados');
                } else {
                    console.error('❌ Error en la respuesta o datos vacíos:', data);
                    mostrarErrorProveedores();
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar proveedores:', error);
                mostrarErrorProveedores();
            });
        }

        // Función para cargar productos
        function cargarProductos() {
            console.log('🔄 Cargando productos...');
            
            fetch('/sistema/public/index.php?route=compras&caso=obtenerProductosActivos')
            .then(response => response.json())
            .then(data => {
                console.log('✅ Datos de productos recibidos:', data);
                
                if (data.status === 200 && data.data) {
                    const selectProductos = $('#productos');
                    selectProductos.empty().append('<option value="">Buscar productos...</option>');
                    
                    data.data.forEach(producto => {
                        selectProductos.append(
                            $('<option>', {
                                value: producto.ID_PROVEEDOR_PRODUCTO,
                                text: `${producto.NOMBRE_PRODUCTO} - L. ${producto.PRECIO_UNITARIO}`,
                                'data-precio': producto.PRECIO_UNITARIO,
                                'data-unidad': producto.UNIDAD
                            })
                        );
                    });
                    
                    selectProductos.trigger('change');
                    console.log('✅ Productos cargados correctamente');
                } else {
                    console.error('❌ Error al cargar productos:', data);
                    mostrarErrorProductos();
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar productos:', error);
                mostrarErrorProductos();
            });
        }

        function mostrarErrorProductos() {
            const selectProductos = $('#productos');
            selectProductos.empty().append('<option value="">Error al cargar productos</option>');
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los productos. Verifica la conexión.',
                confirmButtonText: 'Entendido'
            });
        }

        // Función para cargar productos de un proveedor específico
        function cargarProductosProveedor(idProveedor) {
            console.log(`🔄 Cargando productos del proveedor: ${idProveedor}`);
            
            fetch(`/sistema/public/index.php?route=compras&caso=obtenerProductosProveedor&id_proveedor=${idProveedor}`)
            .then(response => response.json())
            .then(data => {
                console.log('✅ Productos del proveedor recibidos:', data);
                
                if (data.status === 200 && data.data) {
                    // Seleccionar los productos del proveedor
                    const selectProductos = $('#productos');
                    selectProductos.val(null).trigger('change');
                    
                    const productosIds = data.data.map(p => p.ID_PROVEEDOR_PRODUCTO.toString());
                    selectProductos.val(productosIds).trigger('change');
                    
                    // Actualizar la visualización
                    actualizarProductosSeleccionados();
                    
                    console.log(`✅ ${productosIds.length} productos cargados para el proveedor`);
                } else {
                    console.log('ℹ️ El proveedor no tiene productos asignados');
                    limpiarProductosSeleccionados();
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar productos del proveedor:', error);
                limpiarProductosSeleccionados();
            });
        }

        // Función para actualizar la visualización de productos seleccionados
        function actualizarProductosSeleccionados() {
            const selectProductos = $('#productos');
            const productosSeleccionadosDiv = document.getElementById('productos-seleccionados');
            const contadorProductos = document.getElementById('contador-productos');
            
            // Obtener los productos seleccionados de Select2
            const seleccionados = selectProductos.select2('data');
            console.log('📦 Productos seleccionados:', seleccionados);
            
            productosSeleccionados = seleccionados.filter(item => item.id && item.id !== '');
            
            // Actualizar contador
            contadorProductos.textContent = productosSeleccionados.length;
            
            if (productosSeleccionados.length === 0) {
                productosSeleccionadosDiv.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-2">No hay productos seleccionados</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="row">';
            productosSeleccionados.forEach(producto => {
                const precio = producto.element?.dataset?.precio || '0.00';
                const unidad = producto.element?.dataset?.unidad || 'N/A';
                
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="product-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${producto.text}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-currency-dollar"></i> L. ${precio} 
                                        | ${unidad}
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                        onclick="removerProducto('${producto.id}')">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            productosSeleccionadosDiv.innerHTML = html;
        }

        // Función para remover un producto de la selección
        function removerProducto(idProducto) {
            const selectProductos = $('#productos');
            const currentValues = selectProductos.val() || [];
            const newValues = currentValues.filter(val => val !== idProducto);
            
            selectProductos.val(newValues).trigger('change');
            actualizarProductosSeleccionados();
        }

        // Función para guardar la relación - VERSIÓN CORREGIDA
        function guardarRelacion() {
            const btnGuardar = document.getElementById('btnGuardar');
            const originalText = btnGuardar.innerHTML;
            
            if (!proveedorSeleccionado) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Proveedor requerido',
                    text: 'Por favor seleccione un proveedor',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            // Mostrar loading
            btnGuardar.innerHTML = '<span class="loading-spinner"></span> Guardando...';
            btnGuardar.disabled = true;
            
            const datos = {
                id_proveedor: proveedorSeleccionado,
                productos: productosSeleccionados.map(p => p.id)
            };
            
            console.log('🔄 Guardando relación:', datos);
            
            fetch('/sistema/public/index.php?route=compras&caso=guardarRelacionProductoProveedor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            })
            .then(response => {
                console.log('📊 Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('✅ Respuesta del servidor:', data);
                
                // CORRECCIÓN: Manejar diferentes formatos de respuesta
                if (data.success || data.status === 200 || data.message?.includes('correctamente')) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message || 'Productos actualizados correctamente',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        limpiarFormulario();
                        cargarRelacionesExistentes();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al guardar la relación',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Error de conexión. Intente nuevamente.',
                    confirmButtonText: 'Entendido'
                });
            })
            .finally(() => {
                btnGuardar.innerHTML = originalText;
                btnGuardar.disabled = false;
            });
        }

        // Función para cargar relaciones existentes
        function cargarRelacionesExistentes() {
            console.log('🔄 Cargando relaciones existentes...');
            
            fetch('/sistema/public/index.php?route=compras&caso=obtenerRelacionesProductoProveedor')
            .then(response => response.json())
            .then(data => {
                console.log('✅ Relaciones existentes recibidas:', data);
                
                if (data.status === 200 && data.data) {
                    const tbody = document.getElementById('tbodyRelaciones');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(relacion => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>
                                <strong>${relacion.PROVEEDOR}</strong>
                                <br><small class="text-muted">${relacion.CORREO || 'Sin correo'}</small>
                            </td>
                            <td>
                                <div class="productos-lista">
                                    ${relacion.PRODUCTOS && relacion.PRODUCTOS.length > 0 ? 
                                        relacion.PRODUCTOS.map(producto => `
                                            <span class="badge bg-light text-dark me-1 mb-1">
                                                ${producto.NOMBRE} - L. ${producto.PRECIO}
                                            </span>
                                        `).join('') : 
                                        '<span class="text-muted">Sin productos</span>'
                                    }
                                </div>
                                <small class="text-muted">${relacion.CANTIDAD_PRODUCTOS || 0} productos</small>
                            </td>
                            <td>
                                <span class="badge ${relacion.ESTADO === 'ACTIVO' ? 'bg-success' : 'bg-secondary'}">
                                    ${relacion.ESTADO || 'N/A'}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="editarRelacion(${relacion.ID_PROVEEDOR})"
                                            title="Editar relación">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="eliminarRelacion(${relacion.ID_PROVEEDOR}, '${relacion.PROVEEDOR}')"
                                            title="Eliminar relación">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                    
                    console.log(`✅ ${data.data.length} relaciones cargadas`);
                } else {
                    console.log('ℹ️ No hay relaciones existentes');
                    document.getElementById('tbodyRelaciones').innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4"></i>
                                <p class="mt-2">No hay relaciones existentes</p>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('❌ Error al cargar relaciones:', error);
                document.getElementById('tbodyRelaciones').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle display-4"></i>
                            <p class="mt-2">Error al cargar las relaciones</p>
                        </td>
                    </tr>
                `;
            });
        }

        // Función para editar relación
        function editarRelacion(idProveedor) {
            console.log(`✏️ Editando relación del proveedor: ${idProveedor}`);
            
            // Seleccionar el proveedor en el formulario
            $('#proveedor').val(idProveedor).trigger('change');
            
            // Scroll al formulario
            document.getElementById('formRelacionProductoProveedor').scrollIntoView({
                behavior: 'smooth'
            });
            
            Swal.fire({
                icon: 'info',
                title: 'Proveedor seleccionado',
                text: 'Puede editar los productos del proveedor seleccionado',
                confirmButtonText: 'Entendido'
            });
        }

        // Función para eliminar relación
        function eliminarRelacion(idProveedor, nombreProveedor) {
            console.log(`🗑️ Solicitando eliminar relación del proveedor: ${idProveedor}`);
            
            Swal.fire({
                icon: 'warning',
                title: '¿Está seguro?',
                html: `¿Está seguro que desea eliminar todas las relaciones de productos para el proveedor: <strong>${nombreProveedor}</strong>?`,
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmarEliminacionRelacion(idProveedor, nombreProveedor);
                }
            });
        }

        function confirmarEliminacionRelacion(idProveedor, nombreProveedor) {
            console.log(`🗑️ Confirmando eliminación del proveedor: ${idProveedor}`);
            
            fetch('/sistema/public/index.php?route=compras&caso=eliminarRelacionProductoProveedor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_proveedor: idProveedor })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Respuesta de eliminación:', data);
                
                if (data.success || data.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: data.message || 'Relación eliminada correctamente',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        cargarRelacionesExistentes();
                        limpiarFormulario();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al eliminar la relación',
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error al eliminar:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Error de conexión. Intente nuevamente.',
                    confirmButtonText: 'Entendido'
                });
            });
        }

        // Función para limpiar formulario
        function limpiarFormulario() {
            console.log('🧹 Limpiando formulario...');
            
            $('#proveedor').val(null).trigger('change');
            $('#productos').val(null).trigger('change');
            proveedorSeleccionado = null;
            productosSeleccionados = [];
            actualizarProductosSeleccionados();
        }

        // Función auxiliar para limpiar productos seleccionados
        function limpiarProductosSeleccionados() {
            $('#productos').val(null).trigger('change');
            productosSeleccionados = [];
            actualizarProductosSeleccionados();
        }
    </script>
</body>
</html>