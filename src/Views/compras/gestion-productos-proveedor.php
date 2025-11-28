<?php
// src/Views/compras/gestion-productos-proveedor.php
?>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión de Productos por Proveedor - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        .bi {
            font-size: 0.8rem;
        }

        .btn-group {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            gap: 1px;
        }

        .btn-outline-primary:hover { background-color: #0d6efd; color: white; }
        .btn-outline-info:hover { background-color: #17a2b8; color: white; }
        .btn-outline-warning:hover { background-color: #ffc107; color: black; }
        .btn-outline-danger:hover { background-color: #dc3545; color: white; }
        .btn-outline-success:hover { background-color: #198754; color: white; }

        /* Tabla general */
        .table {
            font-size: 0.85rem !important;
            width: 100% !important;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.9rem;
            padding: 12px 8px;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
            word-wrap: break-word;
            word-break: break-word;
            padding: 10px 8px;
            line-height: 1.3;
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.7em;
            padding: 4px 8px;
        }

        /* Columnas específicas */
        .table td:nth-child(1) { /* PROVEEDOR */ 
            min-width: 150px;
            max-width: 200px;
        }

        .table td:nth-child(2) { /* PRODUCTO */
            min-width: 150px;
            max-width: 200px;
        }

        .table td:nth-child(3) { /* DESCRIPCION */
            min-width: 180px;
            max-width: 250px;
        }

        .table td:nth-child(4) { /* UNIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(5) { /* PRECIO */
            min-width: 100px;
            max-width: 120px;
            text-align: right;
        }

        .table td:nth-child(6) { /* FECHA CREACION */
            min-width: 120px;
            max-width: 130px;
            white-space: nowrap;
        }

        .table td:nth-child(7) { /* ESTADO */
            min-width: 80px;
            max-width: 90px;
            text-align: center;
        }

        /* Columna de acciones */
        .table th:last-child,
        .table td:last-child {
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
            padding: 8px 4px !important;
            text-align: center;
        }

        /* Loading styles */
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

        .bg-estado-activo {
            background-color: #198754 !important;
        }

        .bg-estado-inactivo {
            background-color: #6c757d !important;
        }

        /* Price styling */
        .price {
            font-weight: 600;
            color: #198754;
        }

        /* Header styles */
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table {
                font-size: 0.8rem !important;
            }
            
            .table th,
            .table td {
                padding: 8px 6px;
            }
        }

        /* Hover effects */
        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }

        /* Alertas flotantes */
        .alert-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .proveedor-header {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
    </style>
</head>

<body>    
    <main id="main" class="main">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Gestión de Productos por Proveedor</h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="recargarPagina()" title="Actualizar/recargar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <!-- Botón Nuevo Producto -->
                        <a href="/sistema/public/registrar-materia-prima" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Nuevo Producto
                        </a>
                        
                        <!-- Botón Exportar PDF -->
                        <div class="btn-group">
                            <button class="btn btn-warning" onclick="exportarPDF()">
                                <i class="bi bi-file-pdf"></i> Exportar PDF
                            </button>

                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Productos por Proveedor:</strong> Visualice todos los productos disponibles organizados por proveedor.
                        </div>
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
                                    <div class="col-md-3">
                                        <label for="filtro_nombre" class="form-label">Nombre del Producto</label>
                                        <input type="text" class="form-control" id="filtro_nombre" 
                                               placeholder="Buscar por producto...">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="filtro_estado" class="form-label">Estado</label>
                                        <select class="form-select" id="filtro_estado">
                                            <option value="">Todos los estados</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mt-2">Cargando productos...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
<table class="table table-hover" id="tablaProductosProveedores">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Precio Unitario</th>
            <th>Fecha Creación</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="tbodyProductosProveedores">
        <!-- Los productos se cargarán aquí dinámicamente -->
    </tbody>
</table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron productos con los filtros aplicados.
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
        let productosCache = [];

        document.addEventListener('DOMContentLoaded', function() {
            cargarProductosProveedores();
            
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') cargarProductosProveedores();
            });
            
            document.getElementById('filtro_proveedor').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') cargarProductosProveedores();
            });
            
            document.getElementById('filtro_estado').addEventListener('change', cargarProductosProveedores);
        });

        function cargarProductosProveedores() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyProductosProveedores');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            tbody.innerHTML = '';
            sinResultados.style.display = 'none';
            
            const filtros = {
    filtro_nombre: document.getElementById('filtro_nombre').value,
    filtro_estado: document.getElementById('filtro_estado').value
            };
            
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) queryParams.append(key, filtros[key]);
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=listarProductosProveedores&${queryParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data && data.data.length > 0) {
                    productosCache = data.data;
                    mostrarProductosProveedores(data.data);
                } else {
                    productosCache = [];
                    tbody.innerHTML = '';
                    sinResultados.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                productosCache = [];
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar los productos</td></tr>';
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

function mostrarProductosProveedores(productos) {
    const tbody = document.getElementById('tbodyProductosProveedores');
    const sinResultados = document.getElementById('sinResultados');
    
    if (productos.length === 0) {
        tbody.innerHTML = '';
        sinResultados.style.display = 'block';
        return;
    }
    
    let html = '';
    
    productos.forEach(producto => {
        html += `
            <tr>
                <td>
                    <strong>${producto.NOMBRE_PRODUCTO}</strong>
                </td>
                <td>${producto.DESCRIPCION || '-'}</td>
                <td class="text-center">
                    <span class="badge bg-secondary">${producto.UNIDAD}</span>
                </td>
                <td class="text-end price">
                    L ${parseFloat(producto.PRECIO_UNITARIO).toFixed(2)}
                </td>
                <td>
                    ${producto.FECHA_CREACION_FORMATEADA || '-'}
                </td>
                <td class="text-center">
                    <span class="badge ${getBadgeClass(producto.ESTADO)}">
                        ${producto.ESTADO}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info" onclick="editarProductoProveedor(${producto.ID_PROVEEDOR_PRODUCTO})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="cambiarEstadoProducto(${producto.ID_PROVEEDOR_PRODUCTO}, '${producto.ESTADO}', '${producto.NOMBRE_PRODUCTO.replace(/'/g, "\\'")}')" title="${producto.ESTADO === 'ACTIVO' ? 'Desactivar' : 'Activar'}">
                            <i class="bi ${producto.ESTADO === 'ACTIVO' ? 'bi-pause-circle' : 'bi-play-circle'}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    sinResultados.style.display = 'none';
}
        function getBadgeClass(estado) {
            return estado === 'ACTIVO' ? 'bg-estado-activo' : 'bg-estado-inactivo';
        }

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_proveedor').value = '';
            document.getElementById('filtro_estado').value = '';
            cargarProductosProveedores();
        }

        function recargarPagina() {
            location.reload();
        }

        function editarProductoProveedor(idProductoProveedor) {
    window.location.href = `/sistema/public/editar-productos-proveedores?id=${idProductoProveedor}`;
}

function cambiarEstadoProducto(idProductoProveedor, estadoActual, nombreProducto) {
    const nuevoEstado = estadoActual === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
    const accion = nuevoEstado === 'ACTIVO' ? 'activar' : 'desactivar';
    
    if (confirm(`¿Está seguro que desea ${accion} el producto: ${nombreProducto}?`)) {
        // Mostrar loading
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        btn.disabled = true;
        
        // Llamar al backend para cambiar estado
        fetch('/sistema/public/index.php?route=compras&caso=cambiarEstadoProductoProveedor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_proveedor_producto: idProductoProveedor,
                estado: nuevoEstado,
                modificado_por: 'SISTEMA'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                mostrarAlerta(`Producto ${accion}do exitosamente`, 'success');
                cargarProductosProveedores(); // Recargar la tabla
            } else {
                mostrarAlerta('Error al cambiar estado: ' + data.message, 'error');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión', 'error');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
    }
}

function exportarPDF() {
    if (!productosCache.length) {
        mostrarAlerta('No hay datos para exportar', 'warning');
        return;
    }

    const loadingAlert = mostrarAlerta('Generando PDF...', 'info', true);
    
    // Configuración de paginación
    const productosPorPagina = 15;
    const paginas = [];
    
    // Dividir productos en páginas
    for (let i = 0; i < productosCache.length; i += productosPorPagina) {
        paginas.push(productosCache.slice(i, i + productosPorPagina));
    }

    const elementosPDF = [];
    const totalPaginas = paginas.length;
    
    // Generar HTML para cada página
    paginas.forEach((productosPagina, index) => {
        const paginaActual = index + 1;
        elementosPDF.push(
            construirHtmlPDF(productosPagina, 'Reporte de Productos', paginaActual, totalPaginas)
        );
    });

    // Combinar todas las páginas
    const htmlCompleto = elementosPDF.join('<div style="page-break-after: always;"></div>');
    const element = document.createElement('div');
    element.innerHTML = htmlCompleto;

    const opt = {
        margin: [10, 10, 10, 10],
        filename: `productos_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            logging: false
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };

    html2pdf().set(opt).from(element).save()
        .then(() => {
            if (loadingAlert && loadingAlert.remove) loadingAlert.remove();
            mostrarAlerta(`PDF generado exitosamente - ${totalPaginas} página${totalPaginas > 1 ? 's' : ''}`, 'success');
        })
        .catch(error => {
            console.error('Error generando PDF:', error);
            if (loadingAlert && loadingAlert.remove) loadingAlert.remove();
            mostrarAlerta('Error al generar el PDF', 'error');
        });
}

function construirHtmlPDF(productos, titulo, paginaActual, totalPaginas) {
    const fecha = new Date().toLocaleString('es-ES');
    const esUltimaPagina = paginaActual === totalPaginas;
    
    let html = `
        <div style="font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 10px; height: 100%;">
            <!-- Encabezado -->
            <div style="text-align: center; margin-bottom: 15px; border-bottom: 2px solid #E38B29; padding-bottom: 10px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 20%; text-align: left;">
                            <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" 
                                 style="width: 80px; height: 80px; border-radius: 8px;" 
                                 alt="Logo Tesoro D' MIMI">
                        </td>
                        <td style="width: 60%; text-align: center;">
                            <h2 style="margin: 0; color: #2c3e50; font-size: 18px;">${titulo}</h2>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
                                Tesoro D' MIMI - Sistema de Gestión
                            </p>
                        </td>
                        <td style="width: 20%; text-align: right; font-size: 10px; color: #666;">
                            Generado: ${fecha}<br>
                            Página: ${paginaActual} de ${totalPaginas}<br>
                            Productos: ${productos.length}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tabla de productos -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: linear-gradient(90deg, #D7A86E, #E38B29); color: white;">
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: left; font-size: 11px;">Producto</th>
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: left; font-size: 11px;">Descripción</th>
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: center; font-size: 11px;">Unidad</th>
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: right; font-size: 11px;">Precio Unitario</th>
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: center; font-size: 11px;">Estado</th>
                        <th style="border: 1px solid #B97222; padding: 8px; text-align: center; font-size: 11px;">Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Mostrar productos de esta página
    productos.forEach((producto, index) => {
        const bgColor = index % 2 === 0 ? '#ffffff' : '#f8f9fa';
        const estadoColor = producto.ESTADO === 'ACTIVO' ? '#28a745' : '#6c757d';
        const estadoTexto = producto.ESTADO === 'ACTIVO' ? 'Activo' : 'Inactivo';
        
        html += `
            <tr style="background-color: ${bgColor};">
                <td style="border: 1px solid #dee2e6; padding: 8px; font-size: 11px;">
                    <strong>${producto.NOMBRE_PRODUCTO || 'N/A'}</strong>
                </td>
                <td style="border: 1px solid #dee2e6; padding: 8px; font-size: 11px;">
                    ${producto.DESCRIPCION || '-'}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center; font-size: 11px;">
                    <span style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px;">
                        ${producto.UNIDAD || 'N/A'}
                    </span>
                </td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: right; font-size: 11px; font-weight: bold; color: #28a745;">
                    L ${parseFloat(producto.PRECIO_UNITARIO || 0).toFixed(2)}
                </td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center; font-size: 11px;">
                    <span style="background-color: ${estadoColor}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold;">
                        ${estadoTexto}
                    </span>
                </td>
                <td style="border: 1px solid #dee2e6; padding: 8px; text-align: center; font-size: 11px;">
                    ${producto.FECHA_CREACION_FORMATEADA || producto.FECHA_CREACION || '-'}
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
    `;

    // Resumen solo en la última página
    if (esUltimaPagina) {
        const totalProductos = productosCache.length;
        const productosActivos = productosCache.filter(p => p.ESTADO === 'ACTIVO').length;
        const productosInactivos = totalProductos - productosActivos;
        
        html += `
            <!-- Resumen (solo en última página) -->
            <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px;">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #2c3e50;">Resumen General</h3>
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td style="padding: 5px;"><strong>Total de productos:</strong></td>
                        <td style="padding: 5px; text-align: right;">${totalProductos}</td>
                        <td style="padding: 5px;"><strong>Productos activos:</strong></td>
                        <td style="padding: 5px; text-align: right;">${productosActivos}</td>
                        <td style="padding: 5px;"><strong>Productos inactivos:</strong></td>
                        <td style="padding: 5px; text-align: right;">${productosInactivos}</td>
                    </tr>
                </table>
            </div>
        `;
    }

    // Pie de página COMPLETO en TODAS las páginas
    html += `
            <!-- Pie de página COMPLETO en todas las páginas -->
            <div style="margin-top: ${esUltimaPagina ? '30px' : '20px'}; padding: 12px; background: linear-gradient(90deg, #2c3e50, #34495e); color: white; border-radius: 6px;">
                <table style="width: 100%; font-size: 9px;">
                    <tr>
                        <td style="width: 40%; vertical-align: top;">
                            <strong style="color: #E38B29; font-size: 10px;">TESORO D' MIMI</strong><br>
                            Sistema de Gestión Integral<br>
                            Módulo de Compras y Proveedores
                        </td>
                        <td style="width: 30%; vertical-align: top; text-align: center; border-left: 1px solid #5d6d7e; border-right: 1px solid #5d6d7e; padding: 0 8px;">
                            <strong style="color: #E38B29; font-size: 10px;">PÁGINA ${paginaActual} DE ${totalPaginas}</strong><br>
                            Documento generado automáticamente
                        </td>
                        <td style="width: 30%; vertical-align: top; text-align: right;">
                            <strong style="color: #E38B29; font-size: 10px;">DATOS DEL SISTEMA</strong><br>
                            Usuario: SISTEMA<br>
                            ${new Date().getFullYear()} © Tesoro D' MIMI
                        </td>
                    </tr>
                </table>
            </div>
    `;

    // Nota legal solo en la última página
    if (esUltimaPagina) {
        html += `
            <!-- Nota legal (solo en última página) -->
            <div style="margin-top: 10px; padding: 8px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; font-size: 8px; color: #856404;">
                <strong>NOTA LEGAL:</strong> Este documento es confidencial y para uso exclusivo de Tesoro D' MIMI. 
                Queda prohibida su reproducción o distribución sin autorización expresa.
            </div>
        `;
    }

    html += `</div>`;

    return html;
}



// Función mejorada para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'info', persistente = false) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[tipo] || 'alert-info';

    const iconClass = {
        'success': 'bi-check-circle',
        'error': 'bi-exclamation-circle', 
        'warning': 'bi-exclamation-triangle',
        'info': 'bi-info-circle'
    }[tipo] || 'bi-info-circle';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show alert-flotante`;
    alertDiv.innerHTML = `
        <i class="bi ${iconClass} me-2"></i>
        ${mensaje}
        ${!persistente ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : ''}
    `;
    
    document.body.appendChild(alertDiv);
    
    if (!persistente) {
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    return alertDiv;
}
    </script>
    
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>