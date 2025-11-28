<?php
// src/Views/compras/ordenes-canceladas.php
use App\models\comprasModel;

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $proveedores = comprasModel::obtenerProveedores();
} catch (Exception $e) {
    error_log("Error al cargar datos para órdenes canceladas: " . $e->getMessage());
    $proveedores = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Órdenes de Compra Canceladas - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .table {
            font-size: 0.9rem;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .badge {
            font-size: 0.75em;
            padding: 4px 8px;
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

        .resumen-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
        }

        .resumen-card .card-body {
            padding: 20px;
        }

        .resumen-card h6 {
            color: white;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .table-detalles {
            font-size: 0.85rem;
        }

        .table-detalles th {
            background-color: #e9ecef;
        }

        .bg-estado-cancelada {
            background-color: var(--danger-color) !important;
        }

        .bg-estado-pendiente {
            background-color: var(--warning-color) !important;
            color: #000 !important;
        }

        .bg-estado-finalizada {
            background-color: #27ae60 !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        .total-orden {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--danger-color);
        }

        .motivo-cancelacion {
            background-color: #fff3f3;
            border-left: 4px solid var(--danger-color);
            padding: 10px 15px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .text-cancelada {
            color: var(--danger-color);
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
                    <div>
                        <h1 class="h2 mb-1 text-cancelada">Órdenes de Compra Canceladas</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=compras">Compras</a></li>
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=consultar-ordenes-pendientes">Órdenes Pendientes</a></li>
                                <li class="breadcrumb-item active text-cancelada">Órdenes Canceladas</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/sistema/public/index.php?route=consultar-ordenes-pendientes" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Ver Órdenes Pendientes
                        </a>
                        <a href="/sistema/public/index.php?route=consultar-compras" class="btn btn-outline-success">
                            <i class="bi bi-check-circle"></i> Ver Compras Finalizadas
                        </a>
                        <button id="btnExportarPDFGeneral" class="btn btn-danger">
        <i class="bi bi-file-pdf"></i> Exportar PDF
    </button>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-10">
                            <div class="bg-light p-3 rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="fecha_inicio" class="form-label">Fecha Cancelación Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fecha_fin" class="form-label">Fecha Cancelación Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="id_proveedor" class="form-label">Proveedor</label>
                                        <select class="form-select" id="id_proveedor" name="id_proveedor">
                                            <option value="">Todos los proveedores</option>
                                            <?php if (!empty($proveedores)): ?>
                                                <?php foreach ($proveedores as $proveedor): ?>
                                                    <option value="<?php echo $proveedor['ID_PROVEEDOR']; ?>">
                                                        <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()" title="Limpiar filtros">
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
                        <p class="text-muted mt-2">Cargando órdenes canceladas...</p>
                    </div>

                    <!-- Órdenes Canceladas -->
                    <div id="ordenes-container">
                        <!-- Las órdenes canceladas se cargarán aquí dinámicamente -->
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron órdenes canceladas con los filtros aplicados.
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Cancelaciones
                                    </h6>
                                    <div id="resumenOrdenes">
                                        <p>Total de órdenes canceladas: <strong id="totalOrdenes">0</strong></p>
                                        <p>Monto total cancelado: <strong id="montoTotal">L 0.00</strong></p>
                                        <p>Productos no recibidos: <strong id="totalProductos">0</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cargarOrdenesCanceladas();
            
            // Escuchar cambios en los filtros
            document.getElementById('fecha_inicio').addEventListener('change', cargarOrdenesCanceladas);
            document.getElementById('fecha_fin').addEventListener('change', cargarOrdenesCanceladas);
            document.getElementById('id_proveedor').addEventListener('change', cargarOrdenesCanceladas);
        });

        function cargarOrdenesCanceladas() {
            const loading = document.getElementById('loading');
            const container = document.getElementById('ordenes-container');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            container.innerHTML = '';
            sinResultados.style.display = 'none';
            
            // Obtener filtros
            const filtros = {
                fecha_inicio: document.getElementById('fecha_inicio').value,
                fecha_fin: document.getElementById('fecha_fin').value,
                id_proveedor: document.getElementById('id_proveedor').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=obtenerRecepcionesCanceladas&${queryParams.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos completos recibidos:', data);
                
                if (data.status === 200 && data.data && data.data.recepciones && data.data.recepciones.length > 0) {
                    mostrarOrdenes(data.data.recepciones);
                } else {
                    container.innerHTML = '';
                    sinResultados.style.display = 'block';
                    actualizarResumen([]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="alert alert-danger">Error al cargar las órdenes canceladas: ' + error.message + '</div>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarOrdenes(ordenes) {
            const container = document.getElementById('ordenes-container');
            const sinResultados = document.getElementById('sinResultados');
            
            if (ordenes.length === 0) {
                container.innerHTML = '';
                sinResultados.style.display = 'block';
                actualizarResumen([]);
                return;
            }
            
            let html = '<div class="accordion" id="accordionOrdenes">';
            
            ordenes.forEach((orden, index) => {
                const accordionId = `accordion-${orden.ID_RECEPCION}`;
                const collapseId = `collapse-${orden.ID_RECEPCION}`;
                const fechaCancelacion = orden.FECHA_MODIFICACION || orden.FECHA_RECEPCION;
                const canceladoPor = orden.MODIFICADO_POR || orden.NOMBRE_USUARIO;
                
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-${orden.ID_RECEPCION}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#${collapseId}" aria-expanded="false" 
                                    aria-controls="${collapseId}">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div>
                                        <strong>Orden #${orden.ID_RECEPCION}</strong> - ${orden.PROVEEDOR}
                                        <span class="badge bg-estado-cancelada ms-2">
                                            <i class="bi bi-x-circle"></i> CANCELADA
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <div class="total-orden">L ${parseFloat(orden.TOTAL_ORDEN).toFixed(2)}</div>
                                        <small class="text-muted">Cancelada: ${new Date(fechaCancelacion).toLocaleDateString()}</small>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="${collapseId}" class="accordion-collapse collapse" 
                             aria-labelledby="heading-${orden.ID_RECEPCION}" 
                             data-bs-parent="#accordionOrdenes">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Información de la Orden:</strong><br>
                                        <strong>Proveedor:</strong> ${orden.PROVEEDOR}<br>
                                        <strong>Usuario:</strong> ${orden.NOMBRE_USUARIO}<br>
                                        <strong>Fecha creación:</strong> ${new Date(orden.FECHA_RECEPCION).toLocaleString()}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Información de Cancelación:</strong><br>
                                        <strong>Cancelado por:</strong> ${canceladoPor}<br>
                                        <strong>Fecha cancelación:</strong> ${new Date(fechaCancelacion).toLocaleString()}<br>
                                        <strong>Total orden:</strong> L ${parseFloat(orden.TOTAL_ORDEN).toFixed(2)}
                                    </div>
                                </div>
                                
                                ${orden.MOTIVO_CANCELACION ? `
                                <div class="motivo-cancelacion">
                                    <strong><i class="bi bi-exclamation-triangle"></i> Motivo de cancelación:</strong><br>
                                    ${orden.MOTIVO_CANCELACION}
                                </div>
                                ` : ''}
                                
                                ${orden.OBSERVACIONES ? `
                                <div class="alert alert-info mt-2">
                                    <strong>Observaciones originales:</strong> ${orden.OBSERVACIONES}
                                </div>
                                ` : ''}
                                
                                <div class="mt-3">
                                    <strong>Productos No Recibidos:</strong>
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm table-bordered table-detalles">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th width="100">Cantidad</th>
                                                    <th width="120">Precio Unitario</th>
                                                    <th width="120">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalles-${orden.ID_RECEPCION}">
                                                <!-- Los detalles se cargarán aquí -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-3">
                                     
                                    <button type="button" class="btn btn-danger btn-sm" onclick="exportarPDFOrden(${orden.ID_RECEPCION})">
            <i class="bi bi-file-pdf"></i> Exportar PDF
        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
            
            // Cargar detalles para cada orden
            ordenes.forEach(orden => {
                cargarDetallesOrden(orden.ID_RECEPCION);
            });
            
            actualizarResumen(ordenes);
        }

        function cargarDetallesOrden(idRecepcion) {
            fetch(`/sistema/public/index.php?route=compras&caso=obtenerDetalleRecepcion&id_recepcion=${idRecepcion}`)
            .then(response => response.json())
            .then(data => {
                console.log('Detalles recibidos para orden cancelada', idRecepcion, ':', data);
                if (data.status === 200 && data.data) {
                    mostrarDetallesOrden(idRecepcion, data.data);
                    actualizarResumenProductos();
                } else {
                    document.getElementById(`detalles-${idRecepcion}`).innerHTML = 
                        '<tr><td colspan="4" class="text-center text-warning">No se pudieron cargar los detalles</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error cargando detalles:', error);
                document.getElementById(`detalles-${idRecepcion}`).innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Error al cargar detalles: ' + error.message + '</td></tr>';
            });
        }

        function mostrarDetallesOrden(idRecepcion, detalles) {
            const tbody = document.getElementById(`detalles-${idRecepcion}`);
            let html = '';
            
            if (detalles.length === 0) {
                html = '<tr><td colspan="4" class="text-center">No hay productos en esta orden</td></tr>';
            } else {
                detalles.forEach(detalle => {
                    const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD) * parseFloat(detalle.PRECIO_UNITARIO));
                    
                    html += `
                        <tr>
                            <td>
                                <strong>${detalle.NOMBRE_PRODUCTO}</strong>
                                ${detalle.DESCRIPCION ? `<br><small class="text-muted">${detalle.DESCRIPCION}</small>` : ''}
                                <br><small class="text-muted">Unidad: ${detalle.UNIDAD}</small>
                            </td>
                            <td class="text-center">${parseFloat(detalle.CANTIDAD).toFixed(2)}</td>
                            <td class="text-end">L ${parseFloat(detalle.PRECIO_UNITARIO).toFixed(2)}</td>
                            <td class="text-end">L ${parseFloat(subtotal).toFixed(2)}</td>
                        </tr>
                    `;
                });
                
                const totalOrden = detalles.reduce((sum, detalle) => {
                    const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD) * parseFloat(detalle.PRECIO_UNITARIO));
                    return sum + parseFloat(subtotal);
                }, 0);
                
                html += `
                    <tr class="table-active">
                        <td colspan="3" class="text-end"><strong>Total de la Orden Cancelada:</strong></td>
                        <td class="text-end"><strong>L ${totalOrden.toFixed(2)}</strong></td>
                    </tr>
                `;
            }
            
            tbody.innerHTML = html;
        }

        function actualizarResumen(ordenes) {
            if (ordenes.length === 0) {
                document.getElementById('totalOrdenes').textContent = '0';
                document.getElementById('montoTotal').textContent = 'L 0.00';
                document.getElementById('totalProductos').textContent = '0';
                return;
            }
            
            const totalOrdenes = ordenes.length;
            const montoTotal = ordenes.reduce((sum, orden) => sum + parseFloat(orden.TOTAL_ORDEN), 0);
            
            document.getElementById('totalOrdenes').textContent = totalOrdenes;
            document.getElementById('montoTotal').textContent = 'L ' + montoTotal.toFixed(2);
            document.getElementById('totalProductos').textContent = '0'; // Se actualizará con los detalles
        }

        function actualizarResumenProductos() {
            let totalProductos = 0;
            const todasLasTablas = document.querySelectorAll('table.table-detalles tbody');
            
            todasLasTablas.forEach(tbody => {
                const filasProductos = tbody.querySelectorAll('tr:not(.table-active)');
                totalProductos += filasProductos.length;
            });
            
            document.getElementById('totalProductos').textContent = totalProductos;
        }

        function limpiarFiltros() {
            document.getElementById('fecha_inicio').value = '';
            document.getElementById('fecha_fin').value = '';
            document.getElementById('id_proveedor').value = '';
            cargarOrdenesCanceladas();
        }

        function verDetalleOrden(idRecepcion) {
            window.open(`/sistema/public/index.php?route=detalle-orden-cancelada&id_recepcion=${idRecepcion}`, '_blank');
        }

        function descargarReporteCancelacion(idRecepcion) {
            window.open(`/sistema/public/index.php?route=reporte-cancelacion-pdf&id_recepcion=${idRecepcion}`, '_blank');
        }

        // Función para exportar PDF general de todas las órdenes canceladas
function exportarPDFGeneral() {
    const btn = document.getElementById('btnExportarPDFGeneral');
    const originalText = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';
    btn.disabled = true;
    
    // Obtener filtros actuales
    const filtros = {
        fecha_inicio: document.getElementById('fecha_inicio').value,
        fecha_fin: document.getElementById('fecha_fin').value,
        id_proveedor: document.getElementById('id_proveedor').value
    };
    
    // Construir query string
    const queryParams = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) {
            queryParams.append(key, filtros[key]);
        }
    });
    
    fetch(`/sistema/public/index.php?route=compras&caso=obtenerRecepcionesCanceladas&${queryParams.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data && data.data.recepciones && data.data.recepciones.length > 0) {
            generarPDFGeneral(data.data.recepciones, filtros);
        } else {
            alert('No hay órdenes canceladas para exportar con los filtros aplicados.');
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

// Función para exportar PDF de una orden cancelada específica
// Función MEJORADA para exportar PDF
function exportarPDFOrden(idRecepcion) {
    console.log('Exportando PDF para orden:', idRecepcion);
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando...';
    btn.disabled = true;
    
    Promise.all([
        fetch(`/sistema/public/index.php?route=compras&caso=obtenerRecepcionesCanceladas`).then(r => r.json()),
        fetch(`/sistema/public/index.php?route=compras&caso=obtenerDetalleRecepcion&id_recepcion=${idRecepcion}`).then(r => r.json())
    ])
    .then(([ordenData, detalleData]) => {
        if (ordenData.status === 200 && ordenData.data && ordenData.data.recepciones) {
            const orden = ordenData.data.recepciones.find(o => o.ID_RECEPCION == idRecepcion);
            
            if (orden && detalleData.status === 200 && detalleData.data) {
                // Usar el método ROBUSTO
                generarPDFIndividual(orden, detalleData.data);
            } else {
                throw new Error('Datos incompletos');
            }
        } else {
            throw new Error('Error en servidor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar PDF. Se intentará método alternativo.');
        generarPDFDesdeVista(idRecepcion);
    })
    .finally(() => {
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 3000);
    });
}
// Función para generar PDF general de todas las órdenes canceladas
function generarPDFGeneral(ordenes, filtros) {
    const contenidoHTML = crearContenidoPDFGeneral(ordenes, filtros);
    
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    const opt = {
        margin: [15, 10, 15, 10],
        filename: `ordenes_canceladas_${new Date().toISOString().split('T')[0]}.pdf`,
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
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy']
        }
    };
    
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            console.log('PDF general de cancelaciones generado exitosamente');
        })
        .catch(error => {
            console.error('Error generando PDF:', error);
            generarPDFGeneralFallback(ordenes, filtros);
        });
}

// Función para generar PDF individual de una orden cancelada
function generarPDFIndividual(orden, detalles) {
    console.log('Generando PDF robusto para orden:', orden.ID_RECEPCION);
    
    const contenidoHTML = crearContenidoPDFIndividualRobusto(orden, detalles);
    
    // Crear elemento temporal
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    // Configuración SIMPLIFICADA de html2pdf
    const opt = {
        margin: 10,
        filename: `orden_cancelada_${orden.ID_RECEPCION}.pdf`,
        image: { 
            type: 'jpeg', 
            quality: 0.8 
        },
        html2canvas: { 
            scale: 2,
            useCORS: false, // Deshabilitar CORS para mayor compatibilidad
            logging: false,
            scrollX: 0,
            scrollY: 0
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        }
    };
    
    console.log('Iniciando generación de PDF...');
    
    // Método MÁS SIMPLE y confiable
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            console.log('PDF generado exitosamente');
        })
        .catch(error => {
            console.error('Error generando PDF:', error);
            // Fallback: método alternativo sin html2canvas
            generarPDFAlternativo(orden, detalles);
        });
}

// Función MEJORADA para descargar PDF usando Blob - MANTIENE EXTENSIÓN PDF
function descargarPDFConBlob(contenidoHTML, filename) {
    console.log('Usando método Blob para descarga...');
    
    try {
        // Crear un Blob con el contenido HTML pero mantener extensión PDF
        const blob = new Blob([contenidoHTML], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        
        // Crear enlace de descarga
        const link = document.createElement('a');
        link.href = url;
        link.download = filename; // Mantener extensión .pdf
        link.style.display = 'none';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Liberar el objeto URL
        setTimeout(() => URL.revokeObjectURL(url), 100);
        
        console.log('Descarga iniciada via Blob');
    } catch (error) {
        console.error('Error con método Blob:', error);
        // Último recurso: abrir en nueva ventana
        const ventana = window.open('', '_blank');
        ventana.document.write(contenidoHTML);
        ventana.document.close();
        setTimeout(() => {
            ventana.print();
        }, 500);
    }
}
function generarPDFAlternativo(orden, detalles) {
    console.log('Usando método alternativo para PDF...');
    
    const contenidoHTML = crearContenidoPDFIndividualSimple(orden, detalles);
    
    // Crear ventana de impresión
    const ventana = window.open('', '_blank');
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    
    // Esperar a que cargue y luego imprimir/guardar como PDF
    setTimeout(() => {
        ventana.print();
        // Cerrar ventana después de un tiempo
        setTimeout(() => {
            ventana.close();
        }, 1000);
    }, 500);
}

function generarPDFDesdeVista(idRecepcion) {
    console.log('Generando PDF desde contenido visible...');
    
    // Encontrar el accordion abierto
    const collapseElement = document.getElementById(`collapse-${idRecepcion}`);
    
    if (collapseElement && !collapseElement.classList.contains('show')) {
        // Si el accordion está cerrado, abrirlo primero
        const button = document.querySelector(`[data-bs-target="#collapse-${idRecepcion}"]`);
        if (button) {
            button.click();
            
            // Esperar a que se abra y cargue el contenido
            setTimeout(() => {
                generarPDFDesdeElemento(collapseElement, idRecepcion);
            }, 1000);
        }
    } else {
        generarPDFDesdeElemento(collapseElement, idRecepcion);
    }
}

function generarPDFDesdeElemento(element, idRecepcion) {
    if (!element) {
        console.error('No se pudo encontrar el elemento para generar PDF');
        alert('No se pudo generar el PDF. Por favor, asegúrese de que la orden esté expandida.');
        return;
    }
    
    // Clonar el elemento para no afectar la vista original
    const elementToPrint = element.cloneNode(true);
    
    // Remover botones y elementos interactivos
    const buttons = elementToPrint.querySelectorAll('button');
    buttons.forEach(button => button.remove());
    
    // Agregar estilos adicionales para impresión
    const style = document.createElement('style');
    style.textContent = `
        .accordion-body { display: block !important; }
        .table { width: 100% !important; }
        body { font-size: 12px; }
    `;
    elementToPrint.appendChild(style);
    
    // Configuración de html2pdf
    const opt = {
        margin: [10, 10, 10, 10],
        filename: `orden_cancelada_${idRecepcion}.pdf`,
        image: { 
            type: 'jpeg', 
            quality: 0.98 
        },
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
    
    // Generar PDF
    html2pdf()
        .set(opt)
        .from(elementToPrint)
        .save()
        .then(() => {
            console.log('PDF descargado exitosamente desde vista');
        })
        .catch(error => {
            console.error('Error descargando PDF desde vista:', error);
            alert('Error al descargar el PDF. Intente nuevamente.');
        });
}


// Función para crear contenido PDF general de órdenes canceladas
function crearContenidoPDFGeneral(ordenes, filtros) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
    
    // Calcular totales
    const totalOrdenes = ordenes.length;
    const montoTotal = ordenes.reduce((sum, orden) => sum + parseFloat(orden.TOTAL_ORDEN || 0), 0);
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Órdenes Canceladas</title>
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
                font-size: 9px; 
                color: #333;
                line-height: 1.3;
            }
            
            .header { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #ffffff; 
                padding: 12px 15px; 
                margin-bottom: 12px;
                border-radius: 6px;
            }
            
            .brand { 
                display: flex; 
                align-items: center; 
                gap: 10px; 
            }
            
            .brand img { 
                width: 45px; 
                height: 45px; 
                border-radius: 5px; 
                object-fit: cover; 
                background: #fff; 
            }
            
            .brand-text { 
                display: flex; 
                flex-direction: column; 
            }
            
            .header h1 { 
                margin: 0; 
                font-size: 18px; 
                letter-spacing: .5px; 
            }
            
            .header h2 { 
                margin: 2px 0 3px; 
                font-size: 11px; 
                font-weight: normal; 
                opacity: .9; 
            }
            
            .header .fecha { 
                font-size: 9px; 
                opacity: .9; 
            }
            
            .section { 
                margin-bottom: 15px;
            }
            
            .filtros-aplicados {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 4px;
                padding: 6px;
                margin-bottom: 10px;
                font-size: 8px;
            }
            
            .estadisticas {
                display: flex;
                justify-content: space-between;
                margin-bottom: 12px;
                font-size: 8px;
            }
            
            .estadistica-item {
                text-align: center;
                padding: 5px;
                background-color: #ecf0f1;
                border-radius: 4px;
                flex: 1;
                margin: 0 3px;
            }
            
            .estadistica-valor {
                font-size: 12px;
                font-weight: bold;
                color: #2c3e50;
            }
            
            .estadistica-label {
                font-size: 7px;
                color: #7f8c8d;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 8px; 
                font-size: 7px; 
            }
            
            th { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #fff; 
                padding: 6px 4px; 
                text-align: left; 
                border: 1px solid #B97222; 
                font-weight: bold;
            }
            
            td { 
                border: 1px solid #dee2e6; 
                padding: 4px; 
                vertical-align: top; 
            }
            
            .estado-cancelada {
                background-color: #e74c3c;
                color: white;
                padding: 2px 5px;
                border-radius: 8px;
                font-size: 6px;
                font-weight: bold;
                display: inline-block;
            }
            
            .footer { 
                text-align: center; 
                padding: 8px; 
                color: #6c757d; 
                font-size: 8px; 
                border-top: 1px solid #dee2e6; 
                margin-top: 12px;
            }
            
            .text-right { 
                text-align: right; 
            }
            
            .text-center { 
                text-align: center; 
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Órdenes Canceladas</h1>
                    <h2>Tesoro D' MIMI</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            ${obtenerFiltrosHTML(filtros)}
            
            <div class="estadisticas">
                <div class="estadistica-item">
                    <div class="estadistica-valor">${totalOrdenes}</div>
                    <div class="estadistica-label">TOTAL CANCELACIONES</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">L ${montoTotal.toFixed(2)}</div>
                    <div class="estadistica-label">MONTO CANCELADO</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="8%"># Orden</th>
                        <th width="18%">Proveedor</th>
                        <th width="12%">Usuario</th>
                        <th width="12%">Fecha Creación</th>
                        <th width="12%">Fecha Cancelación</th>
                        <th width="12%">Total Orden</th>
                        <th width="16%">Cancelado Por</th>
                        <th width="10%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${ordenes.map((orden) => {
                        const fechaCancelacion = orden.FECHA_MODIFICACION || orden.FECHA_RECEPCION;
                        const canceladoPor = orden.MODIFICADO_POR || orden.NOMBRE_USUARIO;
                        
                        return `
                            <tr>
                                <td class="text-center">${orden.ID_RECEPCION}</td>
                                <td>${orden.PROVEEDOR || 'N/A'}</td>
                                <td>${orden.NOMBRE_USUARIO || 'N/A'}</td>
                                <td>${new Date(orden.FECHA_RECEPCION).toLocaleDateString()}</td>
                                <td>${new Date(fechaCancelacion).toLocaleDateString()}</td>
                                <td class="text-right">L ${parseFloat(orden.TOTAL_ORDEN || 0).toFixed(2)}</td>
                                <td>${canceladoPor || 'N/A'}</td>
                                <td class="text-center"><span class="estado-cancelada">CANCELADA</span></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
        </div>
    </body>
    </html>
    `;
}

// Función para crear contenido PDF individual de orden cancelada
// Función para crear contenido PDF individual de orden cancelada - MEJORADA
// Función para crear contenido PDF individual de orden cancelada - EXACTA como la imagen
// Función para crear contenido PDF individual de orden cancelada - ESTILO EXACTO
function crearContenidoPDFIndividualRobusto(orden, detalles) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Calcular total
    const totalOrden = detalles.reduce((sum, detalle) => {
        const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD || 0) * parseFloat(detalle.PRECIO_UNITARIO || 0));
        return sum + parseFloat(subtotal);
    }, 0);
    
    const fechaCancelacion = orden.FECHA_MODIFICACION || orden.FECHA_RECEPCION;
    const canceladoPor = orden.MODIFICADO_POR || orden.NOMBRE_USUARIO;
    
    // Formatear fechas
    const fechaCreacion = new Date(orden.FECHA_RECEPCION).toLocaleDateString('es-ES');
    const fechaCancel = new Date(fechaCancelacion).toLocaleDateString('es-ES');

    // Crear tabla de productos
    let tablaProductos = '';
    if (detalles && detalles.length > 0) {
        detalles.forEach((detalle, index) => {
            const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD || 0) * parseFloat(detalle.PRECIO_UNITARIO || 0));
            tablaProductos += `
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">${index + 1}</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">
                        <strong>${detalle.NOMBRE_PRODUCTO || 'N/A'}</strong>
                        ${detalle.DESCRIPCION ? `<br><small style="color: #666;">${detalle.DESCRIPCION}</small>` : ''}
                        <br><small style="color: #888; font-style: italic;">Unidad: ${detalle.UNIDAD || 'N/A'}</small>
                    </td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">${parseFloat(detalle.CANTIDAD || 0).toFixed(2)}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">L ${parseFloat(detalle.PRECIO_UNITARIO || 0).toFixed(2)}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">L ${parseFloat(subtotal).toFixed(2)}</td>
                </tr>
            `;
        });
        
        // Fila del total
        tablaProductos += `
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="padding: 10px; border: 1px solid #ddd; text-align: right;">TOTAL DE LA ORDEN CANCELADA:</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">L ${totalOrden.toFixed(2)}</td>
            </tr>
        `;
    }

    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Orden Cancelada #${orden.ID_RECEPCION}</title>
        <style>
            @page {
                margin: 15mm 10mm;
            }
            
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                color: #333;
                line-height: 1.4;
                font-size: 12px;
            }
            
            .header {
                border-bottom: 3px solid #D7A86E;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            
            .titulo-principal {
                font-size: 24px;
                color: #2c3e50;
                margin: 0 0 5px 0;
                font-weight: bold;
            }
            
            .subtitulo {
                font-size: 18px;
                color: #D7A86E;
                margin: 0 0 10px 0;
                font-weight: bold;
            }
            
            .fecha-generacion {
                color: #666;
                font-size: 12px;
                margin-bottom: 10px;
            }
            
            .seccion {
                margin-bottom: 25px;
            }
            
            .titulo-seccion {
                font-size: 16px;
                color: #2c3e50;
                border-bottom: 2px solid #3498db;
                padding-bottom: 5px;
                margin: 25px 0 15px 0;
                font-weight: bold;
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .info-box {
                background: #f8f9fa;
                padding: 15px;
                border: 1px solid #dee2e6;
                border-radius: 5px;
            }
            
            .linea-info {
                margin-bottom: 8px;
            }
            
            .linea-info strong {
                color: #2c3e50;
                display: inline-block;
                width: 150px;
            }
            
            .estado-cancelada {
                background-color: #e74c3c;
                color: white;
                padding: 4px 10px;
                border-radius: 10px;
                font-size: 10px;
                font-weight: bold;
            }
            
            .motivo-cancelacion {
                background-color: #ffe6e6;
                border: 1px solid #e74c3c;
                padding: 15px;
                border-radius: 5px;
                margin: 15px 0;
            }
            
            .observaciones {
                background-color: #e3f2fd;
                border: 1px solid #2196f3;
                padding: 15px;
                border-radius: 5px;
                margin: 15px 0;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 11px;
            }
            
            th {
                background: #D7A86E;
                color: white;
                padding: 12px 8px;
                text-align: left;
                border: 1px solid #B97222;
                font-weight: bold;
            }
            
            td {
                padding: 8px;
                border: 1px solid #ddd;
                vertical-align: top;
            }
            
            .footer {
                text-align: center;
                padding: 20px;
                color: #6c757d;
                font-size: 10px;
                border-top: 2px solid #dee2e6;
                margin-top: 30px;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
            
            .divider {
                height: 2px;
                background: linear-gradient(90deg, transparent, #D7A86E, transparent);
                margin: 25px 0;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1 class="titulo-principal">Orden Cancelada #${orden.ID_RECEPCION}</h1>
            <h2 class="subtitulo">Tesoro D' MIMI</h2>
            <div class="fecha-generacion">Generado el: ${fechaActual}</div>
        </div>

        <div class="seccion">
            <h3 class="titulo-seccion">Información de la Orden</h3>
            
            <div class="info-grid">
                <div class="info-box">
                    <div class="linea-info">
                        <strong>Proveedor:</strong> ${orden.PROVEEDOR || 'N/A'}
                    </div>
                    <div class="linea-info">
                        <strong>Usuario:</strong> ${orden.NOMBRE_USUARIO || 'N/A'}
                    </div>
                    <div class="linea-info">
                        <strong>Fecha creación:</strong> ${fechaCreacion}
                    </div>
                    <div class="linea-info">
                        <strong>Estado:</strong> <span class="estado-cancelada">CANCELADA</span>
                    </div>
                </div>
                
                <div class="info-box">
                    <div class="linea-info">
                        <strong>Cancelado por:</strong> ${canceladoPor || 'N/A'}
                    </div>
                    <div class="linea-info">
                        <strong>Fecha cancelación:</strong> ${fechaCancel}
                    </div>
                    <div class="linea-info">
                        <strong>Total orden:</strong> L ${parseFloat(orden.TOTAL_ORDEN || totalOrden).toFixed(2)}
                    </div>
                </div>
            </div>

            ${orden.MOTIVO_CANCELACION ? `
            <div class="motivo-cancelacion">
                <strong>Motivo de cancelación:</strong><br>
                ${orden.MOTIVO_CANCELACION}
            </div>
            ` : ''}

            ${orden.OBSERVACIONES ? `
            <div class="observaciones">
                <strong>Observaciones originales:</strong><br>
                ${orden.OBSERVACIONES}
            </div>
            ` : ''}
        </div>

        <div class="divider"></div>

        <div class="seccion">
            <h3 class="titulo-seccion">Productos No Recibidos</h3>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="50%">Producto</th>
                        <th width="10%">Cantidad</th>
                        <th width="15%">Precio Unitario</th>
                        <th width="15%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${tablaProductos || `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            No hay productos en esta orden
                        </td>
                    </tr>
                    `}
                </tbody>
            </table>
        </div>

        <div class="footer">
            Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI<br>
            ${fechaActual}
        </div>
    </body>
    </html>
    `;
}
function crearContenidoPDFIndividualSimple(orden, detalles) {
    const fechaActual = new Date().toLocaleDateString('es-ES');
    
    const totalOrden = detalles.reduce((sum, detalle) => {
        const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD || 0) * parseFloat(detalle.PRECIO_UNITARIO || 0));
        return sum + parseFloat(subtotal);
    }, 0);
    
    const fechaCancelacion = orden.FECHA_MODIFICACION || orden.FECHA_RECEPCION;
    const canceladoPor = orden.MODIFICADO_POR || orden.NOMBRE_USUARIO;
    
    const fechaCreacion = new Date(orden.FECHA_RECEPCION).toLocaleDateString('es-ES');
    const fechaCancel = new Date(fechaCancelacion).toLocaleDateString('es-ES');

    let tablaProductos = '';
    if (detalles && detalles.length > 0) {
        detalles.forEach((detalle, index) => {
            const subtotal = detalle.SUBTOTAL || (parseFloat(detalle.CANTIDAD || 0) * parseFloat(detalle.PRECIO_UNITARIO || 0));
            tablaProductos += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${detalle.NOMBRE_PRODUCTO || 'N/A'}</td>
                    <td>${parseFloat(detalle.CANTIDAD || 0).toFixed(2)}</td>
                    <td>L ${parseFloat(detalle.PRECIO_UNITARIO || 0).toFixed(2)}</td>
                    <td>L ${parseFloat(subtotal).toFixed(2)}</td>
                </tr>
            `;
        });
    }

    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Orden Cancelada #${orden.ID_RECEPCION}</title>
        <style>
            body { font-family: Arial; margin: 20px; font-size: 14px; }
            h1, h2 { text-align: center; margin: 5px 0; }
            h1 { color: #2c3e50; }
            h2 { color: #D7A86E; }
            .info { margin: 10px 0; padding: 10px; background: #f5f5f5; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background: #D7A86E; color: white; }
            .total { font-weight: bold; background: #f0f0f0; }
            .footer { text-align: center; margin-top: 20px; color: #666; }
        </style>
    </head>
    <body>
        <h1>Orden Cancelada #${orden.ID_RECEPCION}</h1>
        <h2>Tesoro D' MIMI</h2>
        <div style="text-align: center; color: #666; margin-bottom: 20px;">
            Generado el: ${fechaActual}
        </div>

        <div class="info">
            <strong>Proveedor:</strong> ${orden.PROVEEDOR || 'N/A'}<br>
            <strong>Usuario:</strong> ${orden.NOMBRE_USUARIO || 'N/A'}<br>
            <strong>Fecha creación:</strong> ${fechaCreacion}<br>
            <strong>Cancelado por:</strong> ${canceladoPor || 'N/A'}<br>
            <strong>Fecha cancelación:</strong> ${fechaCancel}<br>
            <strong>Total:</strong> L ${totalOrden.toFixed(2)}
        </div>

        ${orden.MOTIVO_CANCELACION ? `
        <div style="background: #ffe6e6; padding: 10px; margin: 10px 0;">
            <strong>Motivo de cancelación:</strong><br>
            ${orden.MOTIVO_CANCELACION}
        </div>
        ` : ''}

        <strong>Productos No Recibidos:</strong>
        <table>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
            ${tablaProductos || '<tr><td colspan="5" style="text-align: center;">No hay productos</td></tr>'}
            <tr class="total">
                <td colspan="4"><strong>TOTAL:</strong></td>
                <td><strong>L ${totalOrden.toFixed(2)}</strong></td>
            </tr>
        </table>

        <div class="footer">
            Sistema de Gestión Tesoro D' MIMI - ${fechaActual}
        </div>
    </body>
    </html>
    `;
}
// Función auxiliar para mostrar filtros aplicados
function obtenerFiltrosHTML(filtros) {
    const filtrosAplicados = [];
    
    if (filtros.fecha_inicio) {
        filtrosAplicados.push(`<strong>Fecha Inicio:</strong> ${filtros.fecha_inicio}`);
    }
    
    if (filtros.fecha_fin) {
        filtrosAplicados.push(`<strong>Fecha Fin:</strong> ${filtros.fecha_fin}`);
    }
    
    if (filtros.id_proveedor) {
        const select = document.getElementById('id_proveedor');
        const selectedOption = select.options[select.selectedIndex];
        filtrosAplicados.push(`<strong>Proveedor:</strong> ${selectedOption.text}`);
    }
    
    if (filtrosAplicados.length > 0) {
        return `
            <div class="filtros-aplicados">
                <strong>Filtros Aplicados:</strong><br>
                ${filtrosAplicados.join(' | ')}
            </div>
        `;
    }
    
    return '<div class="filtros-aplicados"><strong>Filtros Aplicados:</strong> Todas las órdenes canceladas</div>';
}

// Funciones fallback
function generarPDFGeneralFallback(ordenes, filtros) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = crearContenidoPDFGeneral(ordenes, filtros);
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    setTimeout(() => ventana.print(), 500);
}

function generarPDFIndividualFallback(orden, detalles) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = crearContenidoPDFIndividual(orden, detalles);
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    setTimeout(() => ventana.print(), 500);
}

// Agregar event listener para el botón general
document.addEventListener('DOMContentLoaded', function() {
    const btnExportarPDFGeneral = document.getElementById('btnExportarPDFGeneral');
    if (btnExportarPDFGeneral) {
        btnExportarPDFGeneral.addEventListener('click', exportarPDFGeneral);
    }
});
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>