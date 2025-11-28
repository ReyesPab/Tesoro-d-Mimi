<?php
// src/Views/compras/gestion-proveedores.php

use App\config\SessionHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$userId = SessionHelper::getUserId();

// Verificar permisos para cada botón
$permisoVerProductos = permisosModel::verificarPermiso($userId, 'GESTION_PRODUCTOS_PROVEEDOR', 'CONSULTAR');
$permisoNuevoProducto = permisosModel::verificarPermiso($userId, 'REGISTRAR_MATERIA_PRIMA', 'CONSULTAR');
$permisoNuevoProveedor = permisosModel::verificarPermiso($userId, 'REGISTRAR_PROVEEDOR', 'CONSULTAR');
$permisoExportarPDF = permisosModel::verificarPermiso($userId, 'EXPORTAR_PDF_COMPRAS', 'CONSULTAR');

// También verificar permisos para las acciones de la tabla
// También verificar permisos para las acciones de la tabla
$permisoEditarProveedor = permisosModel::verificarPermiso($userId, 'GESTION_PROVEEDORES', 'EDITAR');
$permisoCambiarEstado = permisosModel::verificarPermiso($userId, 'GESTION_PROVEEDORES', 'EDITAR');
$permisoVerProductosTabla = permisosModel::verificarPermiso($userId, 'GESTION_PRODUCTOS_PROVEEDOR', 'CONSULTAR');
// Usar un permiso que sabes que existe
$permisoGestionRelacion = permisosModel::verificarPermiso($userId, 'GESTION_PROVEEDORES', 'CONSULTAR');?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión de Proveedores - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
        }
        
        .badge-activo {
            background-color: #28a745;
            color: white;
        }
        
        .badge-inactivo {
            background-color: #6c757d;
            color: white;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }
        
        /* Estilo para botones deshabilitados por permisos */
        .btn-disabled-permission {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
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
                    <h1 class="h2 mb-0">Gestión de Proveedores</h1>
                    <div class="d-flex gap-2">
                        
<?php if ($permisoGestionRelacion): ?>
            <a href="/sistema/public/gestion-relacion-producto-proveedor" class="btn btn-warning">
                <i class="bi bi-link"></i> Gestionar Relaciones
            </a>
            <?php endif; ?>
                        <!-- BOTÓN NUEVO PROVEEDOR - Solo si tiene permiso -->
                        <?php if ($permisoNuevoProveedor): ?>
                        <a href="/sistema/public/registrar-proveedor" class="btn btn-success">
                            <i class="bi bi-building-add"></i> Nuevo Proveedor
                        </a>
                        <?php endif; ?>
                        
                        <!-- BOTÓN EXPORTAR PDF - Solo si tiene permiso -->
                        <?php if ($permisoExportarPDF): ?>
                        <button id="btnExportarPDF" class="btn btn-danger">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="bg-light p-3 rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="filtro_nombre" class="form-label">Nombre del Proveedor</label>
                                        <input type="text" class="form-control" id="filtro_nombre" 
                                               placeholder="Buscar por nombre...">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filtro_estado" class="form-label">Estado</label>
                                        <select class="form-select" id="filtro_estado">
                                            <option value="">Todos los estados</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarProveedores()">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mt-2">Cargando proveedores...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tablaProveedores">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Creado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProveedores">
                                <!-- Los proveedores se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron proveedores con los filtros aplicados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambiarEstadoTitle">Cambiar Estado del Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cambiar_estado_id_proveedor">
                    <p id="modalCambiarEstadoMensaje"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarCambioEstado">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Pasar los permisos de PHP a JavaScript
        // Pasar los permisos de PHP a JavaScript
const permisos = {
    editarProveedor: <?php echo $permisoEditarProveedor ? 'true' : 'false'; ?>,
    cambiarEstado: <?php echo $permisoCambiarEstado ? 'true' : 'false'; ?>,
    verProductosTabla: <?php echo $permisoVerProductosTabla ? 'true' : 'false'; ?>
};

        let tablaProveedores = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Solo agregar evento si el botón existe (tiene permiso)
            const btnExportarPDF = document.getElementById('btnExportarPDF');
            if (btnExportarPDF) {
                btnExportarPDF.addEventListener('click', exportarPDF);
            }
            
            // Inicializar DataTables con configuración básica
            inicializarDataTable();
            
            // Cargar proveedores automáticamente al iniciar
            cargarProveedores();
            
            // Escuchar cambios en los filtros para búsqueda automática
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    cargarProveedores();
                }
            });
            
            document.getElementById('filtro_estado').addEventListener('change', cargarProveedores);
        });

        function inicializarDataTable() {
            tablaProveedores = $('#tablaProveedores').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']], // Ordenar por nombre (columna 0)
                searching: false, // Desactivar búsqueda global de DataTables (usamos nuestros filtros)
                info: true,
                paging: true,
                autoWidth: false,
                columns: [
                    { data: 'nombre' },      // Columna 0: Nombre
                    { data: 'contacto' },    // Columna 1: Contacto
                    { data: 'telefono' },    // Columna 2: Teléfono
                    { data: 'correo' },      // Columna 3: Correo
                    { data: 'direccion' },   // Columna 4: Dirección
                    { data: 'estado' },      // Columna 5: Estado
                    { data: 'fecha_creacion' }, // Columna 6: Fecha Creación
                    { data: 'creado_por' },  // Columna 7: Creado Por
                    { 
                        data: 'acciones',    // Columna 8: Acciones
                        orderable: false,    // No ordenable
                        searchable: false    // No buscable
                    }
                ],
                // Deshabilitar la funcionalidad de búsqueda interna de DataTables
                // ya que tenemos nuestros propios filtros
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }

        function cargarProveedores() {
            const loading = document.getElementById('loading');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            sinResultados.style.display = 'none';
            
            // Limpiar la tabla
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
            
            // Obtener filtros
            const filtros = {
                filtro_nombre: document.getElementById('filtro_nombre').value,
                filtro_estado: document.getElementById('filtro_estado').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=listarProveedores&${queryParams.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 200 && data.data && data.data.length > 0) {
                    mostrarProveedores(data.data);
                } else {
                    mostrarSinResultados();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar los proveedores: ' + error.message);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarProveedores(proveedores) {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.style.display = 'none';
            
            // Preparar datos para DataTables
            const datosProveedores = proveedores.map(proveedor => {
                // Construir botones de acciones según permisos
                let botonesAcciones = '';
                
                // Botón Cambiar Estado (solo si tiene permiso)
                if (permisos.cambiarEstado) {
                    if (proveedor.ESTADO === 'ACTIVO') {
                        botonesAcciones += `<button type="button" class="btn btn-outline-warning btn-sm" onclick="cambiarEstado(${proveedor.ID_PROVEEDOR}, 'INACTIVO', '${proveedor.NOMBRE.replace(/'/g, "\\'")}')" title="Desactivar Proveedor">
                                <i class="bi bi-pause-circle"></i>
                            </button>`;
                    } else {
                        botonesAcciones += `<button type="button" class="btn btn-outline-success btn-sm" onclick="cambiarEstado(${proveedor.ID_PROVEEDOR}, 'ACTIVO', '${proveedor.NOMBRE.replace(/'/g, "\\'")}')" title="Activar Proveedor">
                                <i class="bi bi-play-circle"></i>
                            </button>`;
                    }
                }
                
                // Botón Editar (solo si tiene permiso)
                if (permisos.editarProveedor) {
                    botonesAcciones += `<button type="button" class="btn btn-outline-info btn-sm" onclick="editarProveedor(${proveedor.ID_PROVEEDOR})" title="Editar Proveedor">
                            <i class="bi bi-pencil"></i>
                        </button>`;
                }
                
                // Botón Ver Productos (solo si tiene permiso)
                if (permisos.verProductosTabla) {
                    botonesAcciones += `<button type="button" class="btn btn-outline-primary btn-sm" onclick="verProductosProveedor(${proveedor.ID_PROVEEDOR})" title="Ver Productos">
                            <i class="bi bi-box-seam"></i>
                        </button>`;
                }
                
                return {
                    nombre: `<strong>${proveedor.NOMBRE}</strong>`,
                    contacto: proveedor.CONTACTO || '-',
                    telefono: proveedor.TELEFONO || '-',
                    correo: proveedor.CORREO || '-',
                    direccion: proveedor.DIRECCION || '-',
                    estado: `<span class="badge ${proveedor.ESTADO === 'ACTIVO' ? 'badge-activo' : 'badge-inactivo'}">${proveedor.ESTADO}</span>`,
                    fecha_creacion: proveedor.FECHA_CREACION_FORMATEADA,
                    creado_por: proveedor.CREADO_POR || 'SISTEMA',
                    acciones: botonesAcciones ? `
                        <div class="btn-group btn-group-sm" role="group">
                            ${botonesAcciones}
                        </div>
                    ` : '<span class="text-muted">Sin permisos</span>'
                };
            });
            
            // Agregar datos a DataTables
            tablaProveedores.rows.add(datosProveedores).draw();
        }

        function mostrarSinResultados() {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.style.display = 'block';
            
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
        }

        function mostrarError(mensaje) {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${mensaje}
                </div>
            `;
            sinResultados.style.display = 'block';
            
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
        }

        function cambiarEstado(idProveedor, nuevoEstado, nombreProveedor) {
            const modal = new bootstrap.Modal(document.getElementById('modalCambiarEstado'));
            const titulo = document.getElementById('modalCambiarEstadoTitle');
            const mensaje = document.getElementById('modalCambiarEstadoMensaje');
            const btnConfirmar = document.getElementById('btnConfirmarCambioEstado');
            
            document.getElementById('cambiar_estado_id_proveedor').value = idProveedor;
            
            const accion = nuevoEstado === 'ACTIVO' ? 'activar' : 'desactivar';
            titulo.textContent = `${nuevoEstado === 'ACTIVO' ? 'Activar' : 'Desactivar'} Proveedor`;
            mensaje.innerHTML = `¿Está seguro que desea <strong>${accion}</strong> al proveedor: <strong>${nombreProveedor}</strong>?`;
            
            // Configurar el evento del botón confirmar
            btnConfirmar.onclick = function() {
                confirmarCambioEstado(idProveedor, nuevoEstado, modal);
            };
            
            modal.show();
        }

        function confirmarCambioEstado(idProveedor, nuevoEstado, modal) {
            fetch('/sistema/public/index.php?route=compras&caso=cambiarEstadoProveedor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_proveedor: idProveedor,
                    estado: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    alert(data.message);
                    modal.hide();
                    cargarProveedores(); // Recargar la lista
                } else {
                    alert(data.message || 'Error al cambiar el estado del proveedor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. Intente nuevamente.');
            });
        }

        function editarProveedor(idProveedor) {
            // Redirigir al formulario de edición
            window.location.href = `/sistema/public/editar-proveedor?id=${idProveedor}`;
        }

        function verProductosProveedor(idProveedor) {
            // Redirigir a la gestión de productos del proveedor
            window.location.href = `/sistema/public/gestion-productos-proveedor?proveedor=${idProveedor}`;
        }

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_estado').value = '';
            cargarProveedores();
        }

        // Función para exportar a PDF
       // Función para exportar a PDF
function exportarPDF() {
    const btn = document.getElementById('btnExportarPDF');
    const originalText = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';
    btn.disabled = true;
    
    // Obtener filtros actuales
    const filtros = {
        filtro_nombre: document.getElementById('filtro_nombre').value,
        filtro_estado: document.getElementById('filtro_estado').value
    };
    
    // Construir query string
    const queryParams = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) {
            queryParams.append(key, filtros[key]);
        }
    });
    
    fetch(`/sistema/public/index.php?route=compras&caso=exportarProveedoresPDF&${queryParams.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data && data.data.length > 0) {
            generarPDF(data.data, filtros);
        } else {
            alert('No hay datos para exportar con los filtros aplicados.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar el PDF: ' + error.message);
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = '<i class="bi bi-file-pdf"></i> Exportar PDF';
        btn.disabled = false;
    });
}

// Función para crear el contenido del PDF con el mismo estilo del ejemplo
function crearContenidoPDF(proveedores, filtros) {
    // Formatear fecha actual
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Calcular estadísticas
    const totalProveedores = proveedores.length;
    const activos = proveedores.filter(p => p.ESTADO === 'ACTIVO').length;
    const inactivos = proveedores.filter(p => p.ESTADO === 'INACTIVO').length;
    
    // Obtener la URL base del sitio para el logo
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Proveedores</title>
        <style>
            @page {
                margin: 15mm 10mm;
                @bottom-right {
                    content: "Página " counter(page) " de " counter(pages);
                    font-size: 10px;
                    color: #666;
                }
            }
            
            body { 
                font-family: Arial, sans-serif; 
                margin: 0; 
                padding: 0; 
                font-size: 10px; 
                color: #333;
                line-height: 1.4;
            }
            
            .header { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #ffffff; 
                padding: 15px 20px; 
                margin-bottom: 15px;
                border-radius: 8px;
            }
            
            .brand { 
                display: flex; 
                align-items: center; 
                gap: 12px; 
            }
            
            .brand img { 
                width: 50px; 
                height: 50px; 
                border-radius: 6px; 
                object-fit: cover; 
                background: #fff; 
            }
            
            .brand-text { 
                display: flex; 
                flex-direction: column; 
            }
            
            .header h1 { 
                margin: 0; 
                font-size: 20px; 
                letter-spacing: .5px; 
            }
            
            .header h2 { 
                margin: 2px 0 4px; 
                font-size: 12px; 
                font-weight: normal; 
                opacity: .9; 
            }
            
            .header .fecha { 
                font-size: 10px; 
                opacity: .9; 
            }
            
            .section { 
                margin-bottom: 20px;
            }
            
            .filtros-aplicados {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 5px;
                padding: 8px;
                margin-bottom: 12px;
                font-size: 9px;
            }
            
            .filtro-item {
                margin: 2px 0;
            }
            
            .estadisticas {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                font-size: 9px;
            }
            
            .estadistica-item {
                text-align: center;
                padding: 6px;
                background-color: #ecf0f1;
                border-radius: 5px;
                flex: 1;
                margin: 0 4px;
            }
            
            .estadistica-valor {
                font-size: 14px;
                font-weight: bold;
                color: #2c3e50;
            }
            
            .estadistica-label {
                font-size: 8px;
                color: #7f8c8d;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 10px; 
                font-size: 8px; 
                page-break-inside: auto;
            }
            
            th { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #fff; 
                padding: 8px 6px; 
                text-align: left; 
                border: 1px solid #B97222; 
                font-weight: bold;
            }
            
            td { 
                border: 1px solid #dee2e6; 
                padding: 6px; 
                vertical-align: top; 
            }
            
            tr:nth-child(even) { 
                background-color: #fdf8f2; 
            }
            
            .estado-activo { 
                background-color: #28a745; 
                color: white; 
                padding: 2px 5px; 
                border-radius: 10px; 
                font-size: 7px;
                font-weight: bold;
                display: inline-block;
            }
            
            .estado-inactivo { 
                background-color: #e74c3c; 
                color: white; 
                padding: 2px 5px; 
                border-radius: 10px; 
                font-size: 7px;
                font-weight: bold;
                display: inline-block;
            }
            
            .footer { 
                text-align: center; 
                padding: 10px; 
                color: #6c757d; 
                font-size: 9px; 
                border-top: 1px solid #dee2e6; 
                margin-top: 15px;
            }
            
            .total { 
                font-weight: bold; 
                color: #2c3e50;
            }
            
            .text-center { 
                text-align: center; 
            }
            
            /* Evitar que la tabla se divida en páginas de forma fea */
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
            
            /* Asegurar que el header se repita en cada página */
            .header {
                page-break-after: avoid;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Proveedores</h1>
                    <h2>Tesoro D' MIMI</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            ${obtenerFiltrosHTML(filtros)}
            
            <div class="estadisticas">
                <div class="estadistica-item">
                    <div class="estadistica-valor">${totalProveedores}</div>
                    <div class="estadistica-label">TOTAL PROVEEDORES</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">${activos}</div>
                    <div class="estadistica-label">PROVEEDORES ACTIVOS</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">${inactivos}</div>
                    <div class="estadistica-label">PROVEEDORES INACTIVOS</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="4%">#</th>
                        <th width="14%">Nombre</th>
                        <th width="11%">Contacto</th>
                        <th width="9%">Teléfono</th>
                        <th width="14%">Correo Electrónico</th>
                        <th width="17%">Dirección</th>
                        <th width="7%">Estado</th>
                        <th width="9%">Fecha Creación</th>
                        <th width="6%">Creado Por</th>
                    </tr>
                </thead>
                <tbody>
                    ${proveedores.map((proveedor, index) => {
                        const estadoClass = proveedor.ESTADO === 'ACTIVO' ? 'estado-activo' : 'estado-inactivo';
                        
                        return `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td><strong>${proveedor.NOMBRE || 'N/A'}</strong></td>
                            <td>${proveedor.CONTACTO || 'N/A'}</td>
                            <td>${proveedor.TELEFONO || 'N/A'}</td>
                            <td>${proveedor.CORREO || 'N/A'}</td>
                            <td>${proveedor.DIRECCION || 'N/A'}</td>
                            <td class="text-center"><span class="${estadoClass}">${proveedor.ESTADO || 'N/A'}</span></td>
                            <td>${formatearFechaPDF(proveedor.FECHA_CREACION)}</td>
                            <td>${proveedor.CREADO_POR || 'SISTEMA'}</td>
                        </tr>
                        `;
                    }).join('')}
                    ${proveedores.length === 0 ? `
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 12px;">
                                No hay proveedores para mostrar con los filtros aplicados.
                            </td>
                        </tr>
                    ` : ''}
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <div class="total">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
    </body>
    </html>
    `;
}

// Función para generar el PDF con el estilo del ejemplo
function generarPDF(proveedores, filtros) {
    // Crear contenido HTML para el PDF
    const contenidoHTML = crearContenidoPDF(proveedores, filtros);
    
    // Crear elemento temporal
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    // Configuración para html2pdf
    const opt = {
        margin: [15, 10, 15, 10],
        filename: `reporte_proveedores_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: true
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'landscape' 
        },
        // Configurar página para números de página
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'],
            before: '.page-break-before',
            after: '.page-break-after', 
            avoid: '.avoid-break'
        }
    };
    
    // Generar PDF, añadir números de página en la esquina inferior derecha y descargar
    const filename = opt.filename;
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        try {
            const totalPages = pdf.internal.getNumberOfPages();
            const pageSize = pdf.internal.pageSize;
            // Obtener ancho/alto de la página en las unidades del documento (mm)
            const pageWidth = (typeof pageSize.getWidth === 'function') ? pageSize.getWidth() : pageSize.width;
            const pageHeight = (typeof pageSize.getHeight === 'function') ? pageSize.getHeight() : pageSize.height;

            const fontSize = 9; // tamaño de la fuente para el número de página (mm en jsPDF)
            pdf.setFontSize(fontSize);

            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                const text = `Página ${i} de ${totalPages}`;
                // Calcular ancho del texto para posicionarlo a la derecha con un margen
                const textWidth = (typeof pdf.getTextWidth === 'function') ? pdf.getTextWidth(text) : (pdf.getStringUnitWidth(text) * pdf.internal.getFontSize());
                const marginRight = 10; // mm desde el borde derecho
                const marginBottom = 8; // mm desde el borde inferior
                const x = pageWidth - marginRight - textWidth;
                const y = pageHeight - marginBottom;
                // Añadir texto (usando align left con coordenadas calculadas)
                pdf.text(text, x, y);
            }

            // Guardar el PDF con los números de página añadidos
            pdf.save(filename);
            console.log('PDF generado y descargado exitosamente con números de página');
        } catch (err) {
            console.error('Error al añadir números de página:', err);
            // Fallback: intentar guardar el PDF tal cual
            try {
                pdf.save(filename);
            } catch (e) {
                console.error('Error guardando PDF en fallback:', e);
                generarPDFFallback(proveedores, filtros);
            }
        }
    }).catch(error => {
        console.error('Error generando PDF:', error);
        // Fallback: abrir en nueva ventana para imprimir
        generarPDFFallback(proveedores, filtros);
    });
}

// Función para formatear fechas en el PDF
function formatearFechaPDF(fecha) {
    if (!fecha || fecha === '0000-00-00 00:00:00' || fecha === '0000-00-00') return 'N/A';
    
    try {
        const fechaObj = new Date(fecha);
        if (isNaN(fechaObj.getTime())) return 'N/A';
        return fechaObj.toLocaleDateString('es-ES');
    } catch (e) {
        return 'N/A';
    }
}

// Función para generar el HTML de filtros aplicados
function obtenerFiltrosHTML(filtros) {
    const filtrosAplicados = [];
    
    if (filtros.filtro_nombre) {
        filtrosAplicados.push(`<div class="filtro-item"><strong>Nombre:</strong> ${filtros.filtro_nombre}</div>`);
    }
    
    if (filtros.filtro_estado) {
        const estadoTexto = filtros.filtro_estado === 'ACTIVO' ? 'Activos' : 
                           filtros.filtro_estado === 'INACTIVO' ? 'Inactivos' : filtros.filtro_estado;
        filtrosAplicados.push(`<div class="filtro-item"><strong>Estado:</strong> ${estadoTexto}</div>`);
    }
    
    if (filtrosAplicados.length > 0) {
        return `
            <div class="filtros-aplicados">
                <strong>Filtros Aplicados:</strong>
                ${filtrosAplicados.join('')}
            </div>
        `;
    }
    
    return '<div class="filtros-aplicados"><strong>Filtros Aplicados:</strong> Todos los proveedores</div>';
}

// Método fallback en caso de error con html2pdf
function generarPDFFallback(proveedores, filtros) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = crearContenidoPDF(proveedores, filtros);
    
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    
    setTimeout(() => {
        ventana.print();
    }, 500);
}
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>