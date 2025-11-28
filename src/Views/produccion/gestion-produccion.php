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

// src/Views/produccion/gestion-produccion.php
use App\config\SessionHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$userId = SessionHelper::getUserId();

// Verificar permiso para el botón Nueva Orden
$permisoNuevaOrden = permisosModel::verificarPermiso($userId, 'CREAR_PRODUCCION', 'CONSULTAR');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes de Producción - Sistema Rosquillas</title>
    
    <!-- Usar CDN para evitar problemas de rutas -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #cf8011ff;
            --secondary-color: #D7A86E;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eaf1 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Adaptación a sidebar (local para esta vista) */
        #page-content {
            margin-left: 240px; /* ancho sidebar */
            transition: margin-left 0.3s ease;
        }
        @media (max-width: 992px) {
            #page-content { margin-left: 0; }
        }
        /* Cuando el sidebar está colapsado en escritorio */
        .sidebar.collapsed ~ #page-content { margin-left: 70px; }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 20px auto;
            overflow: hidden;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%);
            color: white;
            padding: 30px 0;
            border-radius: 12px;
        }
        
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
            background: white;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(75, 46, 5, 0.15);
        }
        
        .stats-card.warning { border-left-color: var(--warning-color); }
        .stats-card.success { border-left-color: var(--success-color); }
        .stats-card.info { border-left-color: #17a2b8; }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 46, 5, 0.4);
            background: linear-gradient(135deg, #5a3a0a 0%, #7d5a2a 100%);
            color: white;
        }
        
        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            background: white;
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%);
            color: white;
        }
        
        .badge-estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        /* Colores para estados (coinciden con la plantilla de PDF/pills) */
        .estado-planificado { background: #ffc107; color: #000; }
    .estado-en-proceso { background: #0d6efd; color: #fff; }
        .estado-finalizado { background: #28a745; color: #fff; }
        .estado-cancelado { background: #dc3545; color: #fff; }
        
        .filtros-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        
        .filtros-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%);
            color: white;
            padding: 15px 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 12px 12px 0 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #4B2E05;
            margin-bottom: 8px;
        }
        
        .form-select, .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(75, 46, 5, 0.25);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #6b4c1a 100%) !important;
            color: white;
        }
    </style>
</head>
<body>
     <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <!-- Navbar Simple y Compatible -->
 

    <!-- Contenido principal adaptativo al sidebar -->
    <div id="page-content">
    <div class="container mt-4">
        <!-- Header Principal -->
        <div class="header-gradient rounded-3 mb-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-6 fw-bold mb-2">
                            <i class="fas fa-clipboard-list me-3"></i>Órdenes de Producción
                        </h1>
                        <p class="lead mb-0 opacity-75">Gestiona y monitorea todas las órdenes de producción del sistema</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php if ($permisoNuevaOrden): ?>
                        <a href="/sistema/public/crear-produccion" class="btn btn-warning text-dark">
                            <i class="fas fa-plus me-1"></i>Nueva Orden
                        </a>
                        <?php endif; ?>
                        <!-- Botón para exportar todas las órdenes a PDF con la plantilla del sistema -->
                        <button id="btnExportarTodos" class="btn btn-danger ms-2" title="Exportar todas las órdenes a PDF">
                            <i class="fas fa-file-pdf me-1"></i> Exportar PDF (todos)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Total Órdenes</h6>
                                <h2 class="text-primary mb-0" id="total-ordenes">0</h2>
                                <small class="text-muted">Todas las órdenes</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">En Proceso</h6>
                                <h2 class="text-warning mb-0" id="en-proceso">0</h2>
                                <small class="text-muted">Producción activa</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-spinner fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Finalizadas</h6>
                                <h2 class="text-success mb-0" id="finalizadas">0</h2>
                                <small class="text-muted">Completadas</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="card-title text-muted mb-1">Eficiencia</h6>
                                <h2 class="text-info mb-0" id="eficiencia-promedio">0%</h2>
                                <small class="text-muted">Promedio general</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros - Sin título y con aplicación automática -->
        <div class="filtros-section">
            <div class="filtros-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                        </h5>
                    </div>
                    <div class="col-auto">
                        <button type="button" id="btnLimpiar" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-eraser me-1"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Estado de Producción</label>
                    <select class="form-select" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="PLANIFICADO">🟡 Planificado</option>
                        <option value="EN_PROCESO">🟠 En Proceso</option>
                        <option value="FINALIZADO">🟢 Finalizado</option>
                        <option value="CANCELADO">🔴 Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="filtroFechaDesde">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="filtroFechaHasta">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Producto</label>
                    <select class="form-select" id="filtroProducto">
                        <option value="">Cargando productos...</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <!-- Botón eliminado - los filtros se aplican automáticamente -->
                </div>
            </div>
        </div>

        <!-- Tabla de Órdenes -->
        <div class="main-container">
            <div class="card-header card-header-custom text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-table me-2"></i>Listado de Órdenes de Producción
                    </h4>
                    <span class="badge bg-light text-dark" id="contador-ordenes">0 órdenes</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaOrdenes" class="table table-hover table-custom" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center"># Orden</th>
                                <th>Producto</th>
                                <th class="text-center">Planificado</th>
                                <th class="text-center">Real</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th>Responsable</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan por AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
    // Variable global para el ID de usuario desde PHP
    const USER_ID = <?php echo $id_usuario; ?>;
    const USER_NAME = "<?php echo $nombre_usuario; ?>";

    $(document).ready(function() {
        console.log('🚀 Página cargada - Usuario:', USER_NAME, 'ID:', USER_ID);
        console.log('📊 Inicializando sistema de órdenes...');
        
        cargarProductos();
        inicializarDataTable();
        
        // Solo mantener el botón de limpiar filtros
        $('#btnLimpiar').click(limpiarFiltros);

        // Aplicación automática de filtros con debounce
        const aplicarFiltrosDebounced = debounce(aplicarFiltros, 500);
        $('#filtroEstado, #filtroProducto').on('change', aplicarFiltrosDebounced);
        $('#filtroFechaDesde, #filtroFechaHasta').on('input change', aplicarFiltrosDebounced);
    });

    // Utilidad: debounce para limitar llamadas sucesivas
    function debounce(fn, delay = 300) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(null, args), delay);
        };
    }

    function cargarProductos() {
        console.log('📦 Cargando lista de productos...');
        
        $.ajax({
            url: '/sistema/public/produccion?caso=obtenerProductosProduccion',
            type: 'GET',
            success: function(response) {
                console.log('✅ Productos cargados:', response);
                if (response.status === 200 && response.data) {
                    var select = $('#filtroProducto');
                    select.empty().append('<option value="">Todos los productos</option>');
                    
                    response.data.forEach(function(producto) {
                        select.append('<option value="' + producto.ID_PRODUCTO + '">' + producto.NOMBRE + '</option>');
                    });
                    console.log('📦 Productos cargados en filtro:', response.data.length);
                } else {
                    console.error('❌ Error en respuesta de productos:', response);
                    $('#filtroProducto').html('<option value="">Error cargando productos</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error cargando productos:', error);
                $('#filtroProducto').html('<option value="">Error al cargar productos</option>');
            }
        });
    }

    function inicializarDataTable() {
        console.log('🎯 Inicializando DataTable...');
        
        window.tablaOrdenes = $('#tablaOrdenes').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            "order": [[0, "desc"]],
            "ajax": {
                "url": "/sistema/public/produccion?caso=obtenerOrdenesProduccion",
                "dataSrc": function(json) {
                    console.log('📊 Datos recibidos del servidor:', json);
                    
                    if (json.status === 200) {
                        console.log('✅ Órdenes cargadas:', json.data.length);
                        return json.data;
                    } else {
                        console.error('❌ Error en respuesta:', json);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: json.message || 'Error al cargar las órdenes'
                        });
                        return [];
                    }
                },
                "error": function(xhr, error, thrown) {
                    console.error('❌ Error AJAX:', error, thrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudieron cargar las órdenes: ' + error
                    });
                }
            },
            "columns": [
                { 
                    "data": "ID_PRODUCCION",
                    "className": "fw-bold text-center"
                },
                { 
                    "data": "PRODUCTO",
                    "render": function(data) {
                        return '<span class="fw-semibold">' + data + '</span>';
                    }
                },
                { 
                    "data": "CANTIDAD_PLANIFICADA",
                    "render": function(data) {
                        return '<span class="badge bg-primary badge-estado">' + 
                               (data ? parseFloat(data).toFixed(0) : '0') + ' unidades</span>';
                    },
                    "className": "text-center"
                },
                { 
                    "data": "CANTIDAD_REAL",
                    "render": function(data) {
                        if (data && data > 0) {
                            return '<span class="badge bg-success badge-estado">' + parseFloat(data).toFixed(0) + ' unidades</span>';
                        } else {
                            return '<span class="badge bg-secondary badge-estado">Pendiente</span>';
                        }
                    },
                    "className": "text-center"
                },
                { 
                    "data": "ESTADO",
                    "render": function(data) {
                                const estados = {
                                    'PLANIFICADO': ['estado-planificado', '📋'],
                                    'EN_PROCESO': ['estado-en-proceso', '⚙️'], 
                                    'FINALIZADO': ['estado-finalizado', '✅'],
                                    'CANCELADO': ['estado-cancelado', '❌']
                                };

                                const [clase, icono] = estados[data] || ['bg-secondary text-white', '❓'];
                                // Usar badge-estado para padding/forma y clase específica para color
                                return '<span class="badge badge-estado ' + clase + '">' + icono + ' ' + data + '</span>';
                    },
                    "className": "text-center"
                },
                { 
                    "data": "FECHA_INICIO",
                    "render": function(data) {
                        return data ? new Date(data).toLocaleDateString('es-HN', {
                            year: 'numeric', month: 'short', day: 'numeric'
                        }) : '<span class="text-muted">-</span>';
                    },
                    "className": "text-center"
                },
                { 
                    "data": "RESPONSABLE",
                    "render": function(data) {
                        return '<span class="text-dark">' + (data || 'Sistema') + '</span>';
                    }
                },
                {
                    "data": null,
                    "render": function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info ver-detalle" data-id="${data.ID_PRODUCCION}" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-secondary descargar-pdf" data-id="${data.ID_PRODUCCION}" title="Descargar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                ${data.ESTADO === 'PLANIFICADO' ? 
                                    `<button class="btn btn-warning iniciar-produccion" data-id="${data.ID_PRODUCCION}" title="Iniciar producción">
                                        <i class="fas fa-play"></i>
                                    </button>` : ''}
                                ${data.ESTADO === 'EN_PROCESO' ? 
                                    `<button class="btn btn-success finalizar-produccion" data-id="${data.ID_PRODUCCION}" title="Finalizar producción">
                                        <i class="fas fa-check"></i>
                                    </button>` : ''}
                            </div>
                        `;
                    },
                    "className": "text-center"
                }
            ],
            "drawCallback": function(settings) {
                const data = settings.json?.data || [];
                console.log('🔄 Tabla actualizada. Datos:', data.length);
                actualizarEstadisticas(data);
                actualizarContador(data.length);
            }
        });
    }

        // Exportar todas las órdenes visibles (filtradas) a PDF con plantilla similar
        function exportarTodasOrdenesPDF() {
                if (!window.tablaOrdenes) {
                        Swal.fire({ icon: 'warning', title: 'Tabla no inicializada', text: 'Espere que la tabla cargue y vuelva a intentar.' });
                        return;
                }

                // Obtener filas visibles (applied = respetar filtros/búsqueda)
                const rows = tablaOrdenes.rows({ search: 'applied' }).data().toArray();
                if (!rows || rows.length === 0) {
                        Swal.fire({ icon: 'info', title: 'Sin datos', text: 'No hay registros para exportar.' });
                        return;
                }

                                const fechaStr = new Date().toLocaleString('es-ES');

                                // Agrupar filas por estado
                                const grupos = {
                                        'PLANIFICADO': [],
                                        'EN_PROCESO': [],
                                        'FINALIZADO': [],
                                        'CANCELADO': []
                                };
                                rows.forEach(r => {
                                        const s = (r.ESTADO || 'SIN_ESTADO').toUpperCase();
                                        if (grupos[s]) grupos[s].push(r);
                                        else {
                                                if (!grupos['OTROS']) grupos['OTROS'] = [];
                                                grupos['OTROS'].push(r);
                                        }
                                });

                                const estadoMeta = {
                                        'PLANIFICADO': { label: 'PLANIFICADO', color: '#ffc107', textColor: '#000', icon: '📋' },
                                        'EN_PROCESO': { label: 'EN_PROCESO', color: '#0d6efd', textColor: '#fff', icon: '⚙️' },
                                        'FINALIZADO': { label: 'FINALIZADO', color: '#28a745', textColor: '#fff', icon: '✅' },
                                        'CANCELADO': { label: 'CANCELADO', color: '#dc3545', textColor: '#fff', icon: '❌' },
                                };

                                // Construir HTML por secciones (uno por estado) para un PDF más claro
                                let sectionsHtml = '';
                                Object.keys(estadoMeta).forEach(key => {
                                        const list = grupos[key] || [];
                                        if (!list.length) return; // omitir secciones vacías
                                        const meta = estadoMeta[key];

                                        sectionsHtml += `
                                            <div style="margin-bottom:14px;">
                                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                                    <div style="width:12px; height:12px; background:${meta.color}; border-radius:3px;"></div>
                                                    <h3 style="margin:0; font-size:14px;">${meta.icon} ${meta.label} (${list.length})</h3>
                                                </div>

                                                <table style="width:100%; border-collapse: collapse; font-size:12px; margin-bottom:8px;">
                                                    <thead>
                                                        <tr>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:left;"># Orden</th>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:left;">Producto</th>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:right;">Planificado</th>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:right;">Real</th>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:left;">Fecha Inicio</th>
                                                            <th style="background:#f0f0f0; padding:6px; border:1px solid #e6e6e6; text-align:left;">Responsable</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${list.map(r => `
                                                            <tr>
                                                                <td style="border:1px solid #dee2e6; padding:6px;">${r.ID_PRODUCCION ?? ''}</td>
                                                                <td style="border:1px solid #dee2e6; padding:6px;">${r.PRODUCTO ?? ''}</td>
                                                                <td style="border:1px solid #dee2e6; padding:6px; text-align:right;">${r.CANTIDAD_PLANIFICADA ? parseFloat(r.CANTIDAD_PLANIFICADA).toFixed(0) : '0'}</td>
                                                                <td style="border:1px solid #dee2e6; padding:6px; text-align:right;">${r.CANTIDAD_REAL ? parseFloat(r.CANTIDAD_REAL).toFixed(0) : '-'}</td>
                                                                <td style="border:1px solid #dee2e6; padding:6px;">${r.FECHA_INICIO ? new Date(r.FECHA_INICIO).toLocaleString('es-ES') : '-'}</td>
                                                                <td style="border:1px solid #dee2e6; padding:6px;">${r.RESPONSABLE ?? ''}</td>
                                                            </tr>
                                                        `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        `;
                                });

                                // Si hay otros no categorizados
                                if (grupos['OTROS'] && grupos['OTROS'].length) {
                                        sectionsHtml += `<h3>Otros (${grupos['OTROS'].length})</h3>`;
                                        sectionsHtml += `<pre>${JSON.stringify(grupos['OTROS'], null, 2)}</pre>`;
                                }

                                const html = `
                                    <div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px;">
                                        <div style="max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow:hidden;">

                                            <!-- Encabezado con degradado y logo -->
                                            <div style="background: linear-gradient(90deg, #D7A86E 0%, #E38B29 100%); color: #fff; padding:14px 18px; display:flex; align-items:center; gap:12px;">
                                                <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Logo" style="width:60px; height:60px; border-radius:10px; object-fit:cover; background:#fff;" crossorigin="anonymous">
                                                <div style="flex:1;">
                                                    <h1 style="margin:0; font-size:18px; font-weight:700;">Listado de Órdenes de Producción</h1>
                                                    <div style="font-size:12px; opacity:.95;">Tesoro D' MIMI — Sistema de Gestión</div>
                                                </div>
                                                <div style="text-align:right; font-size:12px; opacity:.95;">
                                                    <div>Generado: ${fechaStr}</div>
                                                    <div style="margin-top:6px;">Total: ${rows.length} registros</div>
                                                </div>
                                            </div>

                                            <div style="padding:14px 16px;">
                                                <!-- Secciones por estado -->
                                                ${sectionsHtml}
                                            </div>

                                            <div style="text-align:center; padding:12px 14px; color:#6c757d; font-size:12px; border-top:1px solid #eee; background:#fafafa;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
                                        </div>
                                    </div>
                                `;

                                const element = document.createElement('div');
                                element.innerHTML = html;

                                const opt = {
                                                margin: [8, 8, 8, 8],
                                                filename: `ordenes_produccion_${new Date().toISOString().split('T')[0]}.pdf`,
                                                image: { type: 'jpeg', quality: 0.98 },
                                                html2canvas: { scale: 2, useCORS: true },
                                                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                                };

                                html2pdf().set(opt).from(element).save().then(() => console.log('PDF de órdenes generado')).catch(err => { console.error('Error generando PDF de órdenes', err); Swal.fire({ icon: 'error', title: 'Error', text: 'Error al generar el PDF.' }); });
        }

        // Registrar el click del botón después de inicializar la página
        $(document).ready(function() {
                // handler para exportar todas
                $('#btnExportarTodos').on('click', function() {
                        exportarTodasOrdenesPDF();
                });
        });

    function aplicarFiltros() {
        const fechaDesde = $('#filtroFechaDesde').val();
        const fechaHasta = $('#filtroFechaHasta').val();

        // Validación rápida de rango de fechas
        if (fechaDesde && fechaHasta && new Date(fechaDesde) > new Date(fechaHasta)) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Rango de fechas inválido',
                text: 'La fecha "Desde" no puede ser mayor que la fecha "Hasta"',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        const params = {
            estado: $('#filtroEstado').val(),
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta,
            id_producto: $('#filtroProducto').val()
        };
        
        const url = '/sistema/public/produccion?caso=obtenerOrdenesProduccion&' + $.param(params);
        console.log('🔍 Aplicando filtros automáticamente:', params);
        
        tablaOrdenes.ajax.url(url).load();
    }

    function limpiarFiltros() {
        $('#filtroEstado').val('');
        $('#filtroFechaDesde').val('');
        $('#filtroFechaHasta').val('');
        $('#filtroProducto').val('');
        console.log('🧹 Filtros limpiados');
        tablaOrdenes.ajax.url('/sistema/public/produccion?caso=obtenerOrdenesProduccion').load();
    }

    function actualizarEstadisticas(data) {
        const total = data.length;
        const enProceso = data.filter(item => item.ESTADO === 'EN_PROCESO').length;
        const finalizadas = data.filter(item => item.ESTADO === 'FINALIZADO').length;
        
        $('#total-ordenes').text(total);
        $('#en-proceso').text(enProceso);
        $('#finalizadas').text(finalizadas);
        
        // Calcular eficiencia promedio (simplificado por ahora)
        const eficiencia = total > 0 ? Math.round((finalizadas / total) * 100) : 0;
        $('#eficiencia-promedio').text(eficiencia + '%');
        
        console.log('📈 Estadísticas actualizadas - Total:', total, 'En proceso:', enProceso, 'Finalizadas:', finalizadas);
    }

    function actualizarContador(total) {
        $('#contador-ordenes').text(total + ' órdenes');
    }

    // Eventos de la tabla
    $(document).on('click', '.ver-detalle', function() {
        const id = $(this).data('id');
        console.log('👁️ Ver detalle de orden:', id);
        window.location.href = '/sistema/public/detalle-produccion?id=' + id;
    });

    $(document).on('click', '.iniciar-produccion', function() {
        const id = $(this).data('id');
        console.log('🚀 Iniciar producción:', id);
        iniciarProduccion(id);
    });

    $(document).on('click', '.descargar-pdf', function() {
        const id = $(this).data('id');
        console.log('📄 Descargar PDF de producción:', id);
        // Ruta corregida para pasar por el front controller
        window.open('/sistema/public/index.php?route=reporte_produccion_pdf&id=' + id, '_blank');
    });

    $(document).on('click', '.finalizar-produccion', function() {
        const id = $(this).data('id');
        console.log('✅ Finalizar producción:', id);
        window.location.href = '/sistema/public/finalizar-produccion?id=' + id;
    });

    function iniciarProduccion(idProduccion) {
    Swal.fire({
        title: '¿Iniciar Producción?',
        html: `
            <div class="text-start">
                <p><strong>Esta acción realizará:</strong></p>
                <ul>
                    <li>✅ Cambiar estado a "EN PROCESO"</li>
                    <li>📝 Registrar consumo de materias primas</li>
                    <li>📦 Actualizar inventario</li>
                    <li>📋 Registrar en historial (Cardex)</li>
                    <li>🔍 Registrar en bitácora del sistema</li>
                </ul>
                <p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> Los materiales se consumirán del inventario</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-play me-1"></i> Sí, iniciar producción',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
        background: 'white',
        backdrop: 'rgba(0,0,0,0.4)',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('🔄 Iniciando producción:', idProduccion);
            
            // Mostrar loading
            Swal.fire({
                title: 'Iniciando Producción...',
                text: 'Procesando los materiales y actualizando inventarios',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '/sistema/public/produccion?caso=iniciarProduccion',
                type: 'POST',
                data: {
                    id_produccion: idProduccion,
                    id_usuario: USER_ID,
                    modificado_por: USER_NAME
                },
                success: function(response) {
                    console.log('✅ Respuesta iniciar producción:', response);
                    Swal.close();
                    
                    if (response.status === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Producción Iniciada!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false,
                            background: 'white'
                        });
                        // Recargar la tabla
                        tablaOrdenes.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            background: 'white'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error iniciando producción:', error);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo iniciar la producción: ' + error,
                        background: 'white'
                    });
                }
            });
        }
    });
}

    // Debug: Verificar que todo esté cargado
    setTimeout(() => {
        console.log('✅ Sistema completamente cargado');
        console.log('👤 Usuario:', USER_NAME);
        console.log('🆔 ID Usuario:', USER_ID);
    }, 1000);
    </script>
</body>
</html>