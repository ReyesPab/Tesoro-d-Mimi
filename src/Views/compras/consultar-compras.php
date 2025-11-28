<?php

// Iniciar sesión de manera compatible con tu sistema
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// DEBUG - Ver qué hay en la sesión (quitar después)
error_log("🎯 SESIÓN ACTUAL: " . print_r($_SESSION, true));

// Verificar sesión según tu sistema - probamos diferentes variables
$sesion_activa = false;
$variables_sesion = [
    'logged_in', 'iniciada', 'USUARIO', 'usuario', 
    'ID_USUARIO', 'id_usuario', 'user_name', 'usuario_nombre'
];

foreach ($variables_sesion as $variable) {
    if (isset($_SESSION[$variable])) {
        $sesion_activa = true;
        error_log("✅ Sesión activa con variable: $variable = " . $_SESSION[$variable]);
        break;
    }
}

if (!$sesion_activa) {
    error_log("❌ No hay sesión activa - Redirigiendo al login");
    echo "<script>
        alert('Sesión no encontrada. Serás redirigido al login.');
        window.location.href = '/sistema/public/login';
    </script>";
    exit;
}

// Obtener datos del usuario para mostrar
$nombre_usuario = $_SESSION['NOMBRE_USUARIO'] ?? 
                 $_SESSION['usuario_nombre'] ?? 
                 $_SESSION['user_name'] ?? 
                 $_SESSION['USUARIO'] ?? 
                 'Usuario';

$id_usuario = $_SESSION['ID_USUARIO'] ?? 
              $_SESSION['id_usuario'] ?? 
              $_SESSION['user_id'] ?? 
              0;

// src/Views/compras/consultar-compras.php
use App\models\comprasModel;
use App\config\SessionHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$userId = SessionHelper::getUserId();

// Verificar permiso para el botón Realizar Pedido
$permisoRealizarPedido = permisosModel::verificarPermiso($userId, 'REGISTRAR_COMPRA', 'CONSULTAR');

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    $proveedores = $comprasModel->obtenerProveedores();
} catch (Exception $e) {
    error_log("Error al cargar datos para consultar compras: " . $e->getMessage());
    $proveedores = []; // Asegurar que siempre sea un array
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Consultar Compras - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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

        /* Columnas específicas con mejor manejo de texto */
        .table td:nth-child(1) { /* PROVEEDOR */ 
            min-width: 120px;
            max-width: 150px;
        }

        .table td:nth-child(2) { /* USUARIO */
            min-width: 100px;
            max-width: 120px;
        }

        .table td:nth-child(3) { /* MATERIA_PRIMA */
            min-width: 140px;
            max-width: 180px;
        }

        .table td:nth-child(4) { /* CANTIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(5) { /* UNIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(6),
        .table td:nth-child(7) { /* PRECIO UNITARIO y SUBTOTAL */
            min-width: 100px;
            max-width: 120px;
            text-align: right;
        }

        .table td:nth-child(8) { /* FECHA */
            min-width: 100px;
            max-width: 110px;
            white-space: nowrap;
        }

        .table td:nth-child(9) { /* ESTADO */
            min-width: 90px;
            max-width: 100px;
            text-align: center;
        }

        /* Columna de acciones compacta */
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
.bg-estado-anulada {
    background-color: #dc3545 !important;
    color: white;
}
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Resumen card styles */
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

        .resumen-card p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        /* Filter styles */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }

        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .btn-refresh {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 12px;
        }

        .btn-refresh:hover {
            background: #5a6268;
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

        /* Badge colors */
        .bg-estado-activa {
            background-color: #198754 !important;
        }

        .bg-estado-anulada {
            background-color: #dc3545 !important;
        }

        .bg-estado-pendiente {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <main id="main" class="main">
        <div class="container-fluid">
            
                        <!-- Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Lista de Compras</h1>
                    <div class="d-flex gap-2">
                        <!-- Botón Realizar Pedido -->
                        <?php if ($permisoRealizarPedido): ?>
                        <a href="/sistema/public/registrar-compras" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Realizar Pedido
                        </a>
                        <?php endif; ?>
                        
                        <!-- Dropdown para Exportar -->
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownExportar" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> Exportar Reporte
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownExportar">
                                <li><a class="dropdown-item" href="#" onclick="exportarPDF(); return false;"><i class="bi bi-file-pdf"></i> Exportar PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportarExcel(); return false;"><i class="bi bi-file-spreadsheet"></i> Exportar Excel</a></li>
                            </ul>
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
                                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="id_proveedor" class="form-label">Proveedor</label>
                                        <select class="form-select" id="id_proveedor" name="id_proveedor">
                                            <option value="">Todos los proveedores</option>
                                            <?php if (!empty($proveedores)): ?>
                                                <?php foreach ($proveedores as $proveedor): ?>
                                                    <option value="<?php echo $proveedor['ID_PROVEEDOR']; ?>">
                                                        <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No hay proveedores disponibles</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="estado_compra" class="form-label">Estado</label>
                                        <select class="form-select" id="estado_compra" name="estado_compra">
                                            <option value="">Todos</option>
                                            <option value="ACTIVA">Activa</option>
                                            <option value="ANULADA">Anulada</option>
                                            <option value="PENDIENTE">Pendiente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-refresh w-100" onclick="limpiarFiltros()" title="Limpiar filtros">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mt-2">Cargando compras...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCompras">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Usuario</th>
                                    <th>Materia Prima</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCompras">
                                <!-- Las compras se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron compras con los filtros aplicados.
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Compras
                                    </h6>
                                    <div id="resumenCompras">
                                        <p>Total de compras: <strong id="totalCompras">0</strong></p>
                                        <p>Monto total: <strong id="montoTotal">L 0.00</strong></p>
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
            // Cargar compras automáticamente al iniciar
            cargarCompras();
            
            // Escuchar cambios en los filtros para búsqueda automática
            document.getElementById('fecha_inicio').addEventListener('change', cargarCompras);
            document.getElementById('fecha_fin').addEventListener('change', cargarCompras);
            document.getElementById('id_proveedor').addEventListener('change', cargarCompras);
            document.getElementById('estado_compra').addEventListener('change', cargarCompras);
        });

        function cargarCompras() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyCompras');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            tbody.innerHTML = '';
            sinResultados.style.display = 'none';
            
            // Obtener filtros
            const filtros = {
                fecha_inicio: document.getElementById('fecha_inicio').value,
                fecha_fin: document.getElementById('fecha_fin').value,
                id_proveedor: document.getElementById('id_proveedor').value,
                estado_compra: document.getElementById('estado_compra').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            fetch(`/sistema/public/index.php?route=compras&caso=listarCompras&${queryParams.toString()}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Respuesta recibida:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Datos parseados:', data);
                    
                    if (data.status === 200 && data.data && data.data.length > 0) {
                        mostrarCompras(data.data);
                    } else {
                        tbody.innerHTML = '';
                        sinResultados.style.display = 'block';
                        actualizarResumen([]);
                    }
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    console.error('Texto recibido:', text);
                    throw new Error('Respuesta no válida del servidor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error al cargar las compras: ' + error.message + '</td></tr>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarCompras(compras) {
            const tbody = document.getElementById('tbodyCompras');
            const sinResultados = document.getElementById('sinResultados');
            
            if (compras.length === 0) {
                tbody.innerHTML = '';
                sinResultados.style.display = 'block';
                actualizarResumen([]);
                return;
            }
            
            // Agrupar compras por ID_COMPRA para mostrar mejor
            const comprasAgrupadas = {};
            compras.forEach(compra => {
                if (!comprasAgrupadas[compra.ID_COMPRA]) {
                    comprasAgrupadas[compra.ID_COMPRA] = {
                        info: compra,
                        detalles: []
                    };
                }
                comprasAgrupadas[compra.ID_COMPRA].detalles.push(compra);
            });
            
            let html = '';
            Object.keys(comprasAgrupadas).forEach(idCompra => {
                const compra = comprasAgrupadas[idCompra];
                
                // Mostrar cada detalle de la compra
                compra.detalles.forEach((detalle, index) => {
                    html += `
                        <tr>
                            ${index === 0 ? `
                            <td>
                                <strong>${detalle.PROVEEDOR}</strong>
                            </td>
                            <td>
                                ${detalle.USUARIO}
                            </td>
                            ` : `
                            <td></td>
                            <td></td>
                            `}
                            <td>${detalle.MATERIA_PRIMA}</td>
                            <td>${parseFloat(detalle.CANTIDAD).toFixed(2)}</td>
                            <td>${detalle.UNIDAD}</td>
                            <td>L ${parseFloat(detalle.PRECIO_UNITARIO).toFixed(2)}</td>
                            <td>L ${parseFloat(detalle.SUBTOTAL).toFixed(2)}</td>
                            ${index === 0 ? `
                            <td>${new Date(detalle.FECHA_COMPRA).toLocaleDateString()}</td>
                            <td>
                                <span class="badge ${getBadgeClass(detalle.ESTADO_COMPRA)}">
                                    ${detalle.ESTADO_COMPRA}
                                </span>
                            </td>
<td>
    <div class="btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-info" onclick="verDetalle(${detalle.ID_COMPRA})" title="Ver detalle completo">
            <i class="bi bi-eye"></i>
        </button>
        <button type="button" class="btn btn-outline-success" onclick="descargarReporte(${detalle.ID_COMPRA})" title="Descargar PDF">
            <i class="bi bi-download"></i>
        </button>
        ${detalle.ESTADO_COMPRA === 'COMPLETADA' ? `
        <button type="button" class="btn btn-outline-danger" onclick="anularCompra(${detalle.ID_COMPRA}, '${detalle.PROVEEDOR}', ${detalle.TOTAL_COMPRA})" title="Anular compra">
            <i class="bi bi-x-circle"></i>
        </button>
        ` : ''}
    </div>
</td>
                            ` : `
                            <td></td>
                            <td></td>
                            <td></td>
                            `}
                        </tr>
                    `;
                });
            });
            
            tbody.innerHTML = html;
            actualizarResumen(compras);
        }

        function actualizarResumen(compras) {
            if (compras.length === 0) {
                document.getElementById('totalCompras').textContent = '0';
                document.getElementById('montoTotal').textContent = 'L 0.00';
                return;
            }
            
            // Calcular total único de compras (sin duplicados por ID_COMPRA)
            const comprasUnicas = [...new Set(compras.map(c => c.ID_COMPRA))];
            const montoTotal = compras.reduce((sum, compra) => sum + parseFloat(compra.SUBTOTAL), 0);
            
            document.getElementById('totalCompras').textContent = comprasUnicas.length;
            document.getElementById('montoTotal').textContent = 'L ' + montoTotal.toFixed(2);
        }

        function getBadgeClass(estado) {
    switch(estado) {
        case 'COMPLETADA': return 'bg-estado-activa';
        case 'ANULADA': return 'bg-estado-anulada';
        case 'PENDIENTE': return 'bg-estado-pendiente';
        default: return 'bg-secondary';
            }
        }

        function limpiarFiltros() {
            document.getElementById('fecha_inicio').value = '';
            document.getElementById('fecha_fin').value = '';
            document.getElementById('id_proveedor').value = '';
            document.getElementById('estado_compra').value = '';
            cargarCompras();
        }

        function verDetalle(idCompra) {
            window.location.href = `/sistema/public/index.php?route=detalle-compra&id_compra=${idCompra}`;
        }

        function descargarReporte(idCompra) {
     // Abrir en nueva ventana para descargar el PDF
     window.open(`/sistema/public/index.php?route=generar-pdf&id_compra=${idCompra}`, '_blank');
     }

        // Función para exportar PDF de consultas de compras
function exportarPDF() {
    const btn = document.querySelector('.dropdown-item[onclick*="exportarPDF"]');
    const originalText = btn.innerHTML;
    
    // Mostrar loading en el dropdown
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';
    
    // Obtener todos los filtros actuales
    const filtros = {
        fecha_inicio: document.getElementById('fecha_inicio').value,
        fecha_fin: document.getElementById('fecha_fin').value,
        id_proveedor: document.getElementById('id_proveedor').value,
        estado_compra: document.getElementById('estado_compra').value
    };
    
    // Construir query string
    const queryParams = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) {
            queryParams.append(key, filtros[key]);
        }
    });
    
    fetch(`/sistema/public/index.php?route=compras&caso=listarCompras&${queryParams.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data && data.data.length > 0) {
            generarPDFCompras(data.data, filtros);
        } else {
            alert('No hay datos para exportar con los filtros aplicados.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar el PDF: ' + error.message);
    })
    .finally(() => {
        // Restaurar texto del botón
        btn.innerHTML = '<i class="bi bi-file-pdf"></i> Exportar PDF';
    });
}

// Función para generar el PDF de compras con el mismo estilo
function generarPDFCompras(compras, filtros) {
    // Crear contenido HTML para el PDF
    const contenidoHTML = crearContenidoPDFCompras(compras, filtros);
    
    // Crear elemento temporal
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    // Configuración para html2pdf
    const opt = {
        margin: [15, 10, 15, 10],
        filename: `reporte_compras_${new Date().toISOString().split('T')[0]}.pdf`,
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
    
    // Generar PDF, añadir pie de página con número de página y descargar
    const filename = opt.filename;
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        try {
            const totalPages = pdf.internal.getNumberOfPages();
            const pageSize = pdf.internal.pageSize;
            const pageWidth = (typeof pageSize.getWidth === 'function') ? pageSize.getWidth() : pageSize.width;
            const pageHeight = (typeof pageSize.getHeight === 'function') ? pageSize.getHeight() : pageSize.height;

            const fontSize = 9;
            pdf.setFontSize(fontSize);

            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                const text = `Página ${i} de ${totalPages}`;
                const marginRight = 10; // mm desde borde derecho
                const marginBottom = 8; // mm desde borde inferior
                let x;
                if (typeof pdf.getTextWidth === 'function') {
                    const textWidth = pdf.getTextWidth(text);
                    x = pageWidth - marginRight - textWidth;
                } else if (typeof pdf.getStringUnitWidth === 'function') {
                    const textWidth = pdf.getStringUnitWidth(text) * pdf.internal.getFontSize();
                    x = pageWidth - marginRight - textWidth;
                } else {
                    x = pageWidth - marginRight - 30;
                }
                const y = pageHeight - marginBottom;
                pdf.text(text, x, y);
            }

            pdf.save(filename);
            console.log('PDF de compras generado y descargado exitosamente con pie de página');
        } catch (err) {
            console.error('Error al añadir pie de página:', err);
            try { pdf.save(filename); } catch (e) { generarPDFComprasFallback(compras, filtros); }
        }
    }).catch(error => {
        console.error('Error generando PDF:', error);
        generarPDFComprasFallback(compras, filtros);
    });
}

// Función para crear el contenido del PDF de compras
function crearContenidoPDFCompras(compras, filtros) {
    // Formatear fecha actual
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Calcular estadísticas
    const comprasUnicas = [...new Set(compras.map(c => c.ID_COMPRA))];
    const montoTotal = compras.reduce((sum, compra) => sum + parseFloat(compra.SUBTOTAL || 0), 0);
    const comprasActivas = compras.filter(c => c.ESTADO_COMPRA === 'ACTIVA').length;
    const comprasAnuladas = compras.filter(c => c.ESTADO_COMPRA === 'ANULADA').length;
    const comprasPendientes = compras.filter(c => c.ESTADO_COMPRA === 'PENDIENTE').length;
    
    // Obtener la URL base del sitio para el logo
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Compras</title>
        <style>
            @page {
                margin: 15mm 10mm;
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
            
            .filtro-item {
                margin: 1px 0;
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
                page-break-inside: auto;
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
            
            tr:nth-child(even) { 
                background-color: #fdf8f2; 
            }
            
            .estado-activa { 
                background-color: #28a745; 
                color: white; 
                padding: 2px 4px; 
                border-radius: 8px; 
                font-size: 6px;
                font-weight: bold;
                display: inline-block;
            }
            
            .estado-anulada { 
                background-color: #e74c3c; 
                color: white; 
                padding: 2px 4px; 
                border-radius: 8px; 
                font-size: 6px;
                font-weight: bold;
                display: inline-block;
            }
            
            .estado-pendiente { 
                background-color: #ffc107; 
                color: black; 
                padding: 2px 4px; 
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
            
            .total { 
                font-weight: bold; 
                color: #2c3e50;
            }
            
            .text-center { 
                text-align: center; 
            }
            
            .text-right { 
                text-align: right; 
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
                page-break-inside: avoid;
                display: block;
                position: relative;
                margin-bottom: 8px; /* reducir el espacio inferior para evitar salto de página */
            }

            /* Evitar que el bloque siguiente se rompa respecto al header */
            .header + .section {
                page-break-before: avoid;
                page-break-inside: avoid;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Compras</h1>
                    <h2>Tesoro D' MIMI</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            ${obtenerFiltrosComprasHTML(filtros)}
            
            <div class="estadisticas">
                <div class="estadistica-item">
                    <div class="estadistica-valor">${comprasUnicas.length}</div>
                    <div class="estadistica-label">TOTAL COMPRAS</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">${comprasActivas}</div>
                    <div class="estadistica-label">COMPRAS ACTIVAS</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">${comprasAnuladas}</div>
                    <div class="estadistica-label">COMPRAS ANULADAS</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">${comprasPendientes}</div>
                    <div class="estadistica-label">COMPRAS PENDIENTES</div>
                </div>
                <div class="estadistica-item">
                    <div class="estadistica-valor">L ${montoTotal.toFixed(2)}</div>
                    <div class="estadistica-label">MONTO TOTAL</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="15%">Proveedor</th>
                        <th width="12%">Usuario</th>
                        <th width="18%">Materia Prima</th>
                        <th width="8%">Cantidad</th>
                        <th width="8%">Unidad</th>
                        <th width="10%">Precio Unitario</th>
                        <th width="10%">Subtotal</th>
                        <th width="10%">Fecha</th>
                        <th width="9%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${generarFilasComprasPDF(compras)}
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

// Función para generar las filas de compras en el PDF
function generarFilasComprasPDF(compras) {
    if (compras.length === 0) {
        return `
            <tr>
                <td colspan="9" class="text-center" style="padding: 10px;">
                    No hay compras para mostrar con los filtros aplicados.
                </td>
            </tr>
        `;
    }
    
    // Agrupar compras por ID_COMPRA
    const comprasAgrupadas = {};
    compras.forEach(compra => {
        if (!comprasAgrupadas[compra.ID_COMPRA]) {
            comprasAgrupadas[compra.ID_COMPRA] = {
                info: compra,
                detalles: []
            };
        }
        comprasAgrupadas[compra.ID_COMPRA].detalles.push(compra);
    });
    
    let html = '';
    
    Object.keys(comprasAgrupadas).forEach(idCompra => {
        const compra = comprasAgrupadas[idCompra];
        
        // Mostrar cada detalle de la compra
        compra.detalles.forEach((detalle, index) => {
            const estadoClass = getEstadoClassCompra(detalle.ESTADO_COMPRA);
            
            html += `
                <tr>
                    ${index === 0 ? `
                    <td><strong>${detalle.PROVEEDOR || 'N/A'}</strong></td>
                    <td>${detalle.USUARIO || 'N/A'}</td>
                    ` : `
                    <td></td>
                    <td></td>
                    `}
                    <td>${detalle.MATERIA_PRIMA || 'N/A'}</td>
                    <td class="text-right">${parseFloat(detalle.CANTIDAD || 0).toFixed(2)}</td>
                    <td class="text-center">${detalle.UNIDAD || 'N/A'}</td>
                    <td class="text-right">L ${parseFloat(detalle.PRECIO_UNITARIO || 0).toFixed(2)}</td>
                    <td class="text-right">L ${parseFloat(detalle.SUBTOTAL || 0).toFixed(2)}</td>
                    ${index === 0 ? `
                    <td>${formatearFechaPDF(detalle.FECHA_COMPRA)}</td>
                    <td class="text-center"><span class="${estadoClass}">${detalle.ESTADO_COMPRA || 'N/A'}</span></td>
                    ` : `
                    <td></td>
                    <td></td>
                    `}
                </tr>
            `;
        });
    });
    
    return html;
}
function anularCompra(idCompra, proveedor, total) {
    const motivo = prompt(`Anular compra #${idCompra}\nProveedor: ${proveedor}\nTotal: L ${total}\n\nIngrese el motivo de la anulación (mínimo 5 caracteres):`);
    
    if (!motivo || motivo.length < 5) {
        if (motivo !== null) {
            alert('El motivo debe tener al menos 5 caracteres.');
        }
        return;
    }
    
    if (!confirm(`¿ESTÁ SEGURO de anular la compra #${idCompra}?\n\nProveedor: ${proveedor}\nTotal: L ${total}\nMotivo: ${motivo}\n\n⚠️ Esta acción revertirá el inventario y NO se puede deshacer.`)) {
        return;
    }
    
    const datos = {
        id_compra: idCompra,
        motivo_anulacion: motivo,
        id_usuario: <?php echo $id_usuario; ?>
    };
    
    console.log('Enviando datos para anular compra:', datos);
    
    // Mostrar loading
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    btn.disabled = true;
    
    fetch('/sistema/public/index.php?route=compras&caso=anularCompra', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.status === 200) {
            alert('✅ ' + data.message);
            cargarCompras(); // Recargar la tabla
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error);
        alert('❌ Error de conexión al anular la compra: ' + error.message);
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}
// Función para obtener la clase CSS del estado de compra
function getEstadoClassCompra(estado) {
    switch(estado) {
        case 'ACTIVA': return 'estado-activa';
        case 'ANULADA': return 'estado-anulada';
        case 'PENDIENTE': return 'estado-pendiente';
        default: return 'estado-activa';
    }
}

// Función para generar el HTML de filtros aplicados en compras
function obtenerFiltrosComprasHTML(filtros) {
    const filtrosAplicados = [];
    
    if (filtros.fecha_inicio) {
        filtrosAplicados.push(`<div class="filtro-item"><strong>Fecha Inicio:</strong> ${filtros.fecha_inicio}</div>`);
    }
    
    if (filtros.fecha_fin) {
        filtrosAplicados.push(`<div class="filtro-item"><strong>Fecha Fin:</strong> ${filtros.fecha_fin}</div>`);
    }
    
    if (filtros.id_proveedor) {
        const select = document.getElementById('id_proveedor');
        const selectedOption = select.options[select.selectedIndex];
        filtrosAplicados.push(`<div class="filtro-item"><strong>Proveedor:</strong> ${selectedOption.text}</div>`);
    }
    
    if (filtros.estado_compra) {
        const estadoTexto = filtros.estado_compra === 'ACTIVA' ? 'Activas' : 
                           filtros.estado_compra === 'ANULADA' ? 'Anuladas' : 
                           filtros.estado_compra === 'PENDIENTE' ? 'Pendientes' : filtros.estado_compra;
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
    
    return '<div class="filtros-aplicados"><strong>Filtros Aplicados:</strong> Todas las compras</div>';
}

// Método fallback en caso de error con html2pdf para compras
function generarPDFComprasFallback(compras, filtros) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = crearContenidoPDFCompras(compras, filtros);
    
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    
    setTimeout(() => {
        ventana.print();
    }, 500);
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

        function exportarExcel() {
            // Obtener todos los filtros actuales
            const filtros = {
                fecha_inicio: document.getElementById('fecha_inicio').value,
                fecha_fin: document.getElementById('fecha_fin').value,
                id_proveedor: document.getElementById('id_proveedor').value,
                estado_compra: document.getElementById('estado_compra').value
            };
            
            // Construir URL para exportar Excel con los mismos filtros
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            // Abrir en nueva ventana para generar el reporte Excel
            window.open(`/sistema/public/index.php?route=reporte_compras_Excel&${queryParams.toString()}`, '_blank');
        }
    </script> 
       <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>
