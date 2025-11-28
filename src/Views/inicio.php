
<?php
require_once dirname(__DIR__) . '/config/session.php';

// Obtener datos iniciales - con manejo de errores y namespaces
try {
    require_once dirname(__DIR__) . '/models/dashboardModel.php';
    
    // Crear instancia usando namespace completo
    $dashboardModel = new App\models\DashboardModel();
    $estadisticas = $dashboardModel->obtenerEstadisticasGenerales();
    $detalleUsuarios = $dashboardModel->obtenerDetalleUsuarios();
    $datosFinancieros = $dashboardModel->obtenerDatosFinancieros();
    $tendenciaMensual = $dashboardModel->obtenerTendenciaMensual();
    
    // OBTENER EL TOTAL DE ALERTAS DEL SISTEMA - NUEVO
    $totalAlertasSistema = $dashboardModel->obtenerTotalAlertasSistema();
     $totalSesionesActivas = $dashboardModel->obtenerTotalSesionesActivas24h();
    
} catch (Exception $e) {
    // Si hay error, usar valores por defecto
    error_log("Error al cargar datos del dashboard: " . $e->getMessage());
    $estadisticas = [];
    $detalleUsuarios = [];
    $datosFinancieros = [
        'total_usuarios' => 0,
        'total_compras_monto' => 0,
        'total_ventas_monto' => 0,
        'total_produccion_cantidad' => 0,
        'total_bitacora' => 0,
        'utilidad' => 0,
        'porcentaje_utilidad' => 0
    ];
    $tendenciaMensual = [];
    $totalAlertasSistema = 0;
    
     // Valor por defecto
}

// Valores por defecto para evitar errores
$estadisticas = array_merge([
    'total_usuarios' => 0,
    'sesiones_activas' => 0,
    'usuarios_bloqueados' => 0,
    'usuarios_nuevos' => 0,
    'actividades_hoy' => 0,
    'total_bitacora' => 0,
    'alertas_stock_bajo' => 0,
    'alertas_stock_excesivo' => 0,
    'total_compras' => 0,
    'total_productos' => 0,
    'ordenes_produccion_activas' => 0,
    'ventas_mes_actual' => 0,
    'actividad_reciente' => [],
    'uso_modulos' => []
], $estadisticas);
?>

  <style>
    .info-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        cursor: pointer;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        color: white;
        border-radius: 10px;
    }

    .bg-primary { background: linear-gradient(135deg, #4361ee, #3a56d4); }
    .bg-success { background: linear-gradient(135deg, #38b000, #32a100); }
    .bg-info { background: linear-gradient(135deg, #00b4d8, #00a0c4); }
    .bg-warning { background: linear-gradient(135deg, #ff9e00, #e68a00); }
    .bg-secondary { background: linear-gradient(135deg, #6c757d, #5a6268); }
    .bg-danger { background: linear-gradient(135deg, #dc3545, #c82333); }
    .bg-purple { background: linear-gradient(135deg, #6f42c1, #5e36b1); }
    .bg-pink { background: linear-gradient(135deg, #d63384, #c2185b); }
    .bg-teal { background: linear-gradient(135deg, #20c997, #1ba87e); }

    .pagetitle {
        margin-bottom: 2rem;
    }

    .card-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .ps-3 h6 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .text-muted.small {
        font-size: 0.875rem;
        color: #6c757d !important;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .modal-detail {
        max-width: 500px;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-active { background-color: #d4edda; color: #155724; }
    .badge-blocked { background-color: #f8d7da; color: #721c24; }
    .badge-new { background-color: #d1ecf1; color: #0c5460; }
    .badge-critico { background-color: #dc3545; color: white; }
    .badge-bajo { background-color: #fd7e14; color: white; }
    .badge-excesivo { background-color: #ffc107; color: black; }
  </style>

<body>
  <?php require_once 'partials/header.php'; ?>
  <?php require_once 'partials/sidebar.php'; ?>
  
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Panel de Control</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <!-- Tarjeta de Usuarios -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarDetalleUsuarios()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-usuarios"><?php echo $estadisticas['total_usuarios']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Registrados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Sesiones Activas -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarSesionesActivas()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Sesiones Activas</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-success">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-sesiones"><?php echo $estadisticas['sesiones_activas']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Conectados (24h)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Actividades Hoy -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Actividades</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-info">
                  <i class="bi bi-activity"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-actividades"><?php echo $estadisticas['actividades_hoy']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Hoy</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Tarjeta de Alertas - ACTUALIZADA -->
<div class="col-xxl-3 col-md-6" onclick="mostrarAlertasSistema()">
    <div class="card info-card">
        <div class="card-body">
            <h5 class="card-title">Alertas</h5>
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="ps-3">
                    <h6 id="total-alertas"><?php echo $estadisticas['alertas_stock_bajo'] + $estadisticas['alertas_stock_excesivo']; ?></h6>
                    <span class="text-muted small pt-2 ps-1">Del Sistema</span>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Tarjeta de Compras -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarReporteFinanciero()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Compras</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-secondary">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-compras"><?php echo $estadisticas['total_compras']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Último Mes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Inventarios -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Inventarios</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-danger">
                  <i class="bi bi-box-seam"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-inventarios"><?php echo $estadisticas['total_productos']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Productos Activos</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Producción -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Producción</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-purple">
                  <i class="bi bi-gear"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-produccion"><?php echo $estadisticas['ordenes_produccion_activas']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Órdenes Activas</span>
                </div>
              </div>
            </div>
          </div>
        </div>

                <!-- Tarjeta de Ventas -->
                <div class="col-xxl-3 col-md-6" onclick="mostrarDetalleVentas()" style="cursor:pointer;">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title">Ventas</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-pink">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 id="total-ventas"><?php echo $estadisticas['ventas_mes_actual']; ?></h6>
                                    <span class="text-muted small pt-2 ps-1">Este Mes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- Tarjeta de Bitácora -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Registros</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-teal">
                  <i class="bi bi-journal-text"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-bitacora"><?php echo $estadisticas['total_bitacora']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">En Bitácora</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficas -->
      <div class="row mt-4">
        <!-- Sección de Reporte de Compras -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cart-check me-2"></i>Reporte de Compras
                        </h5>
                        <div class="btn-group" role="group" id="periodo-compras-buttons">
                            <button type="button" class="btn btn-sm btn-primary active" onclick="cambiarPeriodoCompras('hoy')">
                                <i class="bi bi-calendar-day me-1"></i>Al Día
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('semana')">
                                <i class="bi bi-calendar-week me-1"></i>Última Semana
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('quincena')">
                                <i class="bi bi-calendar3 me-1"></i>15 Días
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('mes')">
                                <i class="bi bi-calendar-month me-1"></i>Último Mes
                            </button>
                        </div>
                    </div>
                    
                    <!-- Resumen de Compras -->
                    <div class="row text-center" id="resumen-compras">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-cart me-2"></i>Total Compras
                                    </h6>
                                    <h3 class="text-primary mb-1" id="total-compras-cantidad">0</h3>
                                    <small class="text-muted" id="periodo-compras-texto">Hoy</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="bi bi-currency-dollar me-2"></i>Monto Total
                                    </h6>
                                    <h3 class="text-success mb-1" id="total-compras-monto">L 0.00</h3>
                                    <small class="text-muted" id="periodo-monto-texto">Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información Adicional -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-info-circle me-2"></i>
                                        <span id="info-compras">Seleccione un período para ver el reporte</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="verComprasCompletas()">
                                        <i class="bi bi-list-ul me-1"></i>Ver Todas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Reporte de Ventas - NUEVA -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up-arrow me-2"></i>Reporte de Ventas
                        </h5>
                        <div class="btn-group" role="group" id="periodo-ventas-buttons">
                            <button type="button" class="btn btn-sm btn-primary active" data-period="hoy" onclick="cambiarPeriodoVentas('hoy')">
                                <i class="bi bi-calendar-day me-1"></i>Al Día
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-period="semana" onclick="cambiarPeriodoVentas('semana')">
                                <i class="bi bi-calendar-week me-1"></i>Última Semana
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-period="quincena" onclick="cambiarPeriodoVentas('quincena')">
                                <i class="bi bi-calendar3 me-1"></i>15 Días
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-period="mes" onclick="cambiarPeriodoVentas('mes')">
                                <i class="bi bi-calendar-month me-1"></i>Último Mes
                            </button>
                            <!-- Botón adicional: Totales generales -->
                             
                        </div>
                    </div>
                    
                    <!-- Resumen de Ventas -->
                    <div class="row text-center" id="resumen-ventas">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="bi bi-cart me-2"></i>Total Ventas
                                    </h6>
                                    <h3 class="text-primary mb-1" id="total-ventas-cantidad">0</h3>
                                    <small class="text-muted" id="periodo-ventas-texto">Hoy</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="bi bi-currency-dollar me-2"></i>Monto Total
                                    </h6>
                                    <h3 class="text-success mb-1" id="total-ventas-monto">L 0.00</h3>
                                    <small class="text-muted" id="periodo-ventas-monto-texto">Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información Adicional -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-info-circle me-2"></i>
                                        <span id="info-ventas">Seleccione un período para ver el reporte</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="verVentasCompletas()">
                                        <i class="bi bi-list-ul me-1"></i>Ver Todas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Modal Detalle Usuarios -->
  <div class="modal fade" id="modalDetalleUsuarios" tabindex="-1">
      <div class="modal-dialog modal-sm">
          <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">
                      <i class="bi bi-people me-2"></i>Detalle de Usuarios
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-0">
                  <div id="contenido-usuarios-detalle">
                      <div class="text-center py-4">
                          <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Sesiones Activas -->
  <div class="modal fade" id="modalSesionesActivas" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
      <div class="modal-dialog modal-md">
          <div class="modal-content">
              <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">
                      <i class="bi bi-person-check me-2"></i>Sesiones Activas (24h)
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div id="contenido-sesiones-activas">
                      <div class="text-center py-4">
                          <div class="spinner-border text-success" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Ventas Recientes -->
  <div class="modal fade" id="modalVentasRecientes" tabindex="-1" data-bs-backdrop="true" data-bs-keyboard="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header bg-pink text-white">
                  <h5 class="modal-title">
                      <i class="bi bi-graph-up-arrow me-2"></i>Ventas Recientes
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div id="contenido-ventas-recientes">
                      <div class="text-center py-4">
                          <div class="spinner-border text-pink" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Alertas Materia Prima -->
  <div class="modal fade" id="modalAlertasMateriaPrima" tabindex="-1">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header bg-warning text-dark">
                  <h5 class="modal-title">
                      <i class="bi bi-exclamation-triangle me-2"></i>Alertas de Materia Prima
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div id="contenido-alertas-materia-prima">
                      <div class="text-center py-4">
                          <div class="spinner-border text-warning" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <?php require_once 'partials/footer.php'; ?>
<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
<script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
<script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <script>
    // Variables globales para los reportes
    let periodoComprasActual = 'hoy';
    let periodoVentasActual = 'hoy';

    document.addEventListener('DOMContentLoaded', function() {
        // Cargar reportes iniciales
        cargarReporteCompras();
        cargarReporteVentas();
        
        // Configurar tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Funciones para Compras (ya existentes)
    function cambiarPeriodoCompras(periodo) {
        periodoComprasActual = periodo;
        
        // Actualizar botones
        document.querySelectorAll('#periodo-compras-buttons .btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });
        
        // Activar botón seleccionado
        const botones = document.querySelectorAll('#periodo-compras-buttons .btn');
        let botonActivo = null;
        
        switch(periodo) {
            case 'hoy':
                botonActivo = botones[0];
                break;
            case 'semana':
                botonActivo = botones[1];
                break;
            case 'quincena':
                botonActivo = botones[2];
                break;
            case 'mes':
                botonActivo = botones[3];
                break;
        }
        
        if (botonActivo) {
            botonActivo.classList.remove('btn-outline-primary');
            botonActivo.classList.add('btn-primary', 'active');
        }
        
        // Cargar datos del nuevo período
        cargarReporteCompras();
    }

    function cargarReporteCompras() {
        fetch(`/sistema/public/index.php?route=dashboard&action=reporte-compras-periodo&periodo=${periodoComprasActual}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    actualizarVistaCompras(data.data);
                } else {
                    console.error('Error al cargar reporte de compras:', data.message);
                    mostrarErrorCompras();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarErrorCompras();
            });
    }

    function actualizarVistaCompras(datos) {
        if (!datos) return;
        
        let textoPeriodo, infoTexto;
        
        switch(periodoComprasActual) {
            case 'hoy':
                textoPeriodo = 'Hoy';
                infoTexto = `Se realizaron ${datos.cantidad} compras hoy`;
                break;
            case 'semana':
                textoPeriodo = 'Última Semana';
                infoTexto = `${datos.cantidad} compras en la última semana`;
                break;
            case 'quincena':
                textoPeriodo = 'Últimos 15 Días';
                infoTexto = `${datos.cantidad} compras en los últimos 15 días`;
                break;
            case 'mes':
                textoPeriodo = 'Último Mes';
                infoTexto = `${datos.cantidad} compras en el último mes`;
                break;
        }
        
        // Actualizar valores
        document.getElementById('total-compras-cantidad').textContent = datos.cantidad;
        document.getElementById('total-compras-monto').textContent = `L ${datos.monto_total.toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('periodo-compras-texto').textContent = textoPeriodo;
        document.getElementById('periodo-monto-texto').textContent = textoPeriodo;
        document.getElementById('info-compras').textContent = infoTexto;
    }

    function mostrarErrorCompras() {
        document.getElementById('total-compras-cantidad').textContent = '0';
        document.getElementById('total-compras-monto').textContent = 'L 0.00';
        document.getElementById('info-compras').textContent = 'Error al cargar los datos de compras';
    }

    function verComprasCompletas() {
        window.location.href = '/sistema/public/consultar-compras';
    }

    // Funciones para Ventas (NUEVAS)
    function cambiarPeriodoVentas(periodo) {
        periodoVentasActual = periodo;
        
        // Actualizar botones
        // Resetear clases de todos los botones
        document.querySelectorAll('#periodo-ventas-buttons .btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('btn-primary');
            // Reset a outline según tipo de botón
            if (btn.dataset.period === 'totales') {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-outline-secondary');
            } else {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-outline-primary');
            }
        });

        // Activar el botón que tenga el atributo data-period igual al seleccionado
        const botonSeleccionado = document.querySelector(`#periodo-ventas-buttons .btn[data-period="${periodo}"]`);
        if (botonSeleccionado) {
            // Ajustar estilos según si es el botón "totales" o uno normal
            if (periodo === 'totales') {
                botonSeleccionado.classList.remove('btn-outline-secondary');
                botonSeleccionado.classList.add('btn-outline-secondary', 'active');
            } else {
                botonSeleccionado.classList.remove('btn-outline-primary');
                botonSeleccionado.classList.add('btn-primary', 'active');
            }
        }

        // Cargar datos del nuevo período (guardamos el periodo solicitado para evitar sobreescrituras por respuestas atrasadas)
        cargarReporteVentas();
    }

    function cargarReporteVentas() {
        const periodoSolicitado = periodoVentasActual;
        fetch(`/sistema/public/index.php?route=dashboard&action=reporte-ventas-periodo&periodo=${periodoSolicitado}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                // Ignorar respuestas que no correspondan al periodo actualmente seleccionado
                if (periodoVentasActual !== periodoSolicitado) {
                    // Respuesta obsoleta
                    return;
                }

                if (data.status === 'success') {
                    actualizarVistaVentas(data.data);
                } else {
                    console.error('Error al cargar reporte de ventas:', data.message);
                    mostrarErrorVentas();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarErrorVentas();
            });
    }

    function actualizarVistaVentas(datos) {
        if (!datos) return;
        
        let textoPeriodo, infoTexto;
        switch(periodoVentasActual) {
            case 'hoy':
                textoPeriodo = 'Hoy';
                infoTexto = `Se realizaron ${datos.cantidad} ventas hoy`;
                break;
            case 'semana':
                textoPeriodo = 'Última Semana';
                infoTexto = `${datos.cantidad} ventas en la última semana`;
                break;
            case 'quincena':
                textoPeriodo = 'Últimos 15 Días';
                infoTexto = `${datos.cantidad} ventas en los últimos 15 días`;
                break;
            case 'mes':
                textoPeriodo = 'Último Mes';
                infoTexto = `${datos.cantidad} ventas en el último mes`;
                break;
            case 'totales':
                textoPeriodo = 'Totales';
                infoTexto = `${datos.cantidad} ventas en totales`;
                break;
            default:
                textoPeriodo = '';
                infoTexto = '';
                break;
        }
        
        // Actualizar valores
        document.getElementById('total-ventas-cantidad').textContent = datos.cantidad;
        document.getElementById('total-ventas-monto').textContent = `L ${datos.monto_total.toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('periodo-ventas-texto').textContent = textoPeriodo;
        document.getElementById('periodo-ventas-monto-texto').textContent = textoPeriodo;
        document.getElementById('info-ventas').textContent = infoTexto;
    }

    function mostrarErrorVentas() {
        document.getElementById('total-ventas-cantidad').textContent = '0';
        document.getElementById('total-ventas-monto').textContent = 'L 0.00';
        document.getElementById('info-ventas').textContent = 'Error al cargar los datos de ventas';
    }

    function verVentasCompletas() {
        window.location.href = '/sistema/public/consultar-ventas';
    }

    // Actualizar reportes cada 2 minutos
    setInterval(function() {
        cargarReporteCompras();
        cargarReporteVentas();
    }, 120000);
    // Función para mostrar detalle de usuarios
    // Función para mostrar detalle de usuarios
function mostrarDetalleUsuarios() {
    const contenido = document.getElementById('contenido-usuarios-detalle');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=detalle-usuarios-modal')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data); // Para debug
            if (data.status === 'success') {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover mb-0">';
                html += '<thead class="table-light"><tr><th>Estado</th><th>Cantidad</th><th>Porcentaje</th></tr></thead>';
                html += '<tbody>';
                
                const totalUsuarios = data.data.reduce((sum, user) => sum + parseInt(user.total), 0);
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(usuario => {
                        let badgeClass = '';
                        let icono = '';
                        let estadoDisplay = usuario.estado;
                        
                        // Normalizar el estado para mostrar
                        switch(usuario.estado.toUpperCase()) {
                            case 'ACTIVO': 
                                badgeClass = 'badge-active'; 
                                icono = '<i class="bi bi-person-check text-success me-1"></i>';
                                estadoDisplay = 'ACTIVO';
                                break;
                            case 'BLOQUEADO': 
                                badgeClass = 'badge-blocked'; 
                                icono = '<i class="bi bi-person-x text-danger me-1"></i>';
                                estadoDisplay = 'BLOQUEADO';
                                break;
                            case 'NUEVO': 
                                badgeClass = 'badge-new'; 
                                icono = '<i class="bi bi-person-plus text-info me-1"></i>';
                                estadoDisplay = 'NUEVO';
                                break;
                            case 'INACTIVO': 
                                badgeClass = 'badge-secondary'; 
                                icono = '<i class="bi bi-person-dash text-secondary me-1"></i>';
                                estadoDisplay = 'INACTIVO';
                                break;
                            default: 
                                badgeClass = 'badge-secondary';
                                icono = '<i class="bi bi-person me-1"></i>';
                        }
                        
                        const porcentaje = totalUsuarios > 0 ? ((usuario.total / totalUsuarios) * 100).toFixed(1) : 0;
                        
                        html += `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    ${icono}
                                    <span class="status-badge ${badgeClass}">${estadoDisplay}</span>
                                </div>
                            </td>
                            <td><strong>${usuario.total}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar ${badgeClass.replace('badge-', 'bg-')}" 
                                             style="width: ${porcentaje}%"></div>
                                    </div>
                                    <small class="text-muted">${porcentaje}%</small>
                                </div>
                            </td>
                        </tr>`;
                    });
                    
                    // Total
                    html += `<tr class="table-primary">
                        <td><strong><i class="bi bi-people-fill me-1"></i>TOTAL</strong></td>
                        <td><strong>${totalUsuarios}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>`;
                } else {
                    html += '<tr><td colspan="3" class="text-center text-muted py-3">No hay datos disponibles</td></tr>';
                }
                
                html += '</tbody></table></div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar los datos') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar los datos: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalDetalleUsuarios')).show();
}

// Función para mostrar detalle de ventas (modal)
function mostrarDetalleVentas() {
    const contenido = document.getElementById('contenido-ventas-recientes');
    // Abrir modal
    var myModal = new bootstrap.Modal(document.getElementById('modalVentasRecientes'));
    myModal.show();

    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-pink" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    // Solicitar todas las ventas sin límite (parámetro limit muy alto)
    fetch('/sistema/public/index.php?route=dashboard&action=detalle-ventas-modal&limit=10000')
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                const ventas = data.data;
                if (!ventas || ventas.length === 0) {
                    contenido.innerHTML = '<div class="alert alert-info text-center"><i class="bi bi-info-circle me-2"></i>No se encontraron ventas</div>';
                    return;
                }

                // Ordenar ventas de más reciente a más antigua
                ventas.sort((a, b) => {
                    const fechaA = new Date(a.FECHA_VENTA);
                    const fechaB = new Date(b.FECHA_VENTA);
                    return fechaB - fechaA; // De más reciente a más antigua
                });

                let html = '<div class="table-responsive"><table class="table table-sm table-hover mb-0">';
                html += '<thead class="table-light"><tr><th><i class="bi bi-hash me-1"></i>ID</th><th><i class="bi bi-calendar me-1"></i>Fecha</th><th><i class="bi bi-person me-1"></i>Cliente</th><th><i class="bi bi-credit-card me-1"></i>Método</th><th class="text-end"><i class="bi bi-cash-coin me-1"></i>Total</th><th><i class="bi bi-tag me-1"></i>Estado</th></tr></thead><tbody>';

                ventas.forEach(v => {
                    // Formatear fecha para mejor legibilidad
                    const fecha = new Date(v.FECHA_VENTA);
                    const fechaFormato = fecha.toLocaleString('es-HN', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
                    
                    // Determinar color de estado
                    let badgeEstado = '';
                    switch(v.ESTADO_FACTURA?.toUpperCase()) {
                        case 'ACTIVA':
                            badgeEstado = '<span class="badge bg-success">ACTIVA</span>';
                            break;
                        case 'PAGADA':
                            badgeEstado = '<span class="badge bg-primary">PAGADA</span>';
                            break;
                        case 'CANCELADA':
                            badgeEstado = '<span class="badge bg-danger">CANCELADA</span>';
                            break;
                        default:
                            badgeEstado = `<span class="badge bg-secondary">${v.ESTADO_FACTURA}</span>`;
                    }

                    html += `<tr>`;
                    html += `<td><strong>#${v.ID_FACTURA}</strong></td>`;
                    html += `<td><small>${fechaFormato}</small></td>`;
                    html += `<td>${v.cliente}</td>`;
                    html += `<td><small>${v.METODO_PAGO || 'No especificado'}</small></td>`;
                    html += `<td class="text-end"><strong>L ${Number(v.TOTAL_VENTA).toLocaleString('es-HN', {minimumFractionDigits:2, maximumFractionDigits:2})}</strong></td>`;
                    html += `<td>${badgeEstado}</td>`;
                    html += `</tr>`;
                });

                html += '</tbody></table></div>';
                html += `<div class="mt-3 alert alert-info"><small><i class="bi bi-info-circle me-2"></i>Total de ventas: <strong>${ventas.length}</strong> | Ordenadas de más reciente a más antigua</small></div>`;
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar ventas recientes</div>';
            }
        })
        .catch(err => {
            console.error('Error al cargar ventas recientes:', err);
            contenido.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar ventas recientes</div>';
        });
}

    // Función para mostrar sesiones activas
    // Función para mostrar sesiones activas - CORREGIDA
function mostrarSesionesActivas() {
    const contenido = document.getElementById('contenido-sesiones-activas');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=sesiones-activas-modal')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos sesiones:', data);
            
            if (data.status === 'success') {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover">';
                html += '<thead class="table-light"><tr><th>Usuario</th><th>Nombre</th><th>Última Conexión</th><th>Tiempo Transcurrido</th></tr></thead>';
                html += '<tbody>';
                
                const sesionesData = data.data || [];
                
                if (sesionesData.length > 0) {
                    sesionesData.forEach(sesion => {
                        const fechaConexion = new Date(sesion.FECHA_ULTIMA_CONEXION);
                        const ahora = new Date();
                        const diferenciaMs = ahora - fechaConexion;
                        const diferenciaMinutos = Math.floor(diferenciaMs / (1000 * 60));
                        const diferenciaHoras = Math.floor(diferenciaMs / (1000 * 60 * 60));
                        
                        let badgeTiempo = '';
                        let textoFecha = '';
                        
                        if (diferenciaMinutos < 1) {
                            badgeTiempo = '<span class="badge bg-success">En línea</span>';
                            textoFecha = 'Hace unos segundos';
                        } else if (diferenciaMinutos < 60) {
                            badgeTiempo = `<span class="badge bg-success">Hace ${diferenciaMinutos} min</span>`;
                            textoFecha = `Hace ${diferenciaMinutos} minutos`;
                        } else if (diferenciaHoras < 24) {
                            badgeTiempo = `<span class="badge bg-info">Hace ${diferenciaHoras} h</span>`;
                            textoFecha = `Hace ${diferenciaHoras} horas`;
                        } else {
                            const diferenciaDias = Math.floor(diferenciaHoras / 24);
                            badgeTiempo = `<span class="badge bg-warning">Hace ${diferenciaDias} d</span>`;
                            textoFecha = `Hace ${diferenciaDias} días`;
                        }
                        
                        html += `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    <div>
                                        <strong>${sesion.USUARIO}</strong>
                                        <div class="mt-1">${badgeTiempo}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${sesion.NOMBRE_USUARIO || 'No disponible'}</td>
                            <td>
                                <div class="small">${fechaConexion.toLocaleString('es-HN')}</div>
                                <div class="text-muted smaller">${textoFecha}</div>
                            </td>
                            <td>
                                <div class="text-center">
                                    ${badgeTiempo}
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    html += `<tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-clock-history display-4"></i>
                                <h6 class="mt-2">No hay sesiones activas</h6>
                                <small>No se han registrado conexiones en las últimas 24 horas</small>
                            </div>
                        </td>
                    </tr>`;
                }
                
                html += '</tbody></table>';
                
                // Información adicional
                html += `<div class="mt-3 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-success">●</span> En línea: Últimos 5 min</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-info">●</span> Reciente: Últimas 24h</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-warning">●</span> Antiguo: Más de 24h</small>
                        </div>
                    </div>
                </div>`;
                
                html += '</div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar las sesiones activas') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar las sesiones activas: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalSesionesActivas')).show();
}

    // Función para actualizar el contador de alertas
function actualizarContadorAlertas() {
    fetch('/sistema/public/index.php?route=dashboard&action=estadisticas-alertas')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const totalAlertas = data.data.reduce((total, tipo) => total + parseInt(tipo.total), 0);
                document.getElementById('total-alertas').textContent = totalAlertas;
            }
        })
        .catch(error => {
            console.error('Error al actualizar contador de alertas:', error);
        });
}

// Llamar a la función cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // ... código existente ...
    
    // Actualizar contador de alertas cada 30 segundos
    setInterval(actualizarContadorAlertas, 30000);
    // Actualizar contador de ventas (mes actual) cada 30 segundos
    actualizarContadorVentas();
    setInterval(actualizarContadorVentas, 30000);
});

// También actualizar el contador después de marcar una alerta como leída
function marcarAlertaLeida(idAlerta) {
    const formData = new FormData();
    formData.append('action', 'marcar-alerta-leida');
    formData.append('id_alerta', idAlerta);
    
    fetch('/sistema/public/index.php?route=dashboard', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Recargar las alertas y actualizar contador
            mostrarAlertasSistema();
            actualizarContadorAlertas();
        } else {
            alert('Error al marcar la alerta como leída: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al marcar la alerta como leída');
    });
}

    // Función para mostrar alertas de materia prima
    // Función para mostrar alertas del sistema - COMPLETAMENTE NUEVA
function mostrarAlertasSistema() {
    const contenido = document.getElementById('contenido-alertas-materia-prima');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=alertas-sistema')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos alertas:', data);
            
            if (data.status === 'success') {
                const alertas = data.data || [];
                let html = '<div class="alertas-completas">';
                
                if (alertas.length > 0) {
                    // Agrupar alertas por tipo
                    const alertasPorTipo = {};
                    alertas.forEach(alerta => {
                        if (!alertasPorTipo[alerta.TIPO_ALERTA]) {
                            alertasPorTipo[alerta.TIPO_ALERTA] = [];
                        }
                        alertasPorTipo[alerta.TIPO_ALERTA].push(alerta);
                    });
                    
                    // Mostrar cada tipo de alerta
                    Object.keys(alertasPorTipo).forEach(tipo => {
                        const alertasTipo = alertasPorTipo[tipo];
                        let titulo = '';
                        let icono = '';
                        let colorClase = '';
                        
                        // En la función mostrarAlertasSistema, actualiza el switch:
// En la función mostrarAlertasSistema, actualiza el switch:
switch(tipo) {
    case 'INVENTARIO_MP_BAJO':
        titulo = 'Stock Bajo Materia Prima';
        icono = 'bi-exclamation-triangle-fill';
        colorClase = 'danger';
        break;
    case 'INVENTARIO_MP_EXCESIVO':
        titulo = 'Stock Excesivo Materia Prima';
        icono = 'bi-info-circle-fill';
        colorClase = 'warning';
        break;
    case 'INVENTARIO_PROD_BAJO':
        titulo = 'Stock Bajo Productos';
        icono = 'bi-exclamation-triangle-fill';
        colorClase = 'danger';
        break;
    case 'INVENTARIO_PROD_EXCESIVO':
        titulo = 'Stock Excesivo Productos';
        icono = 'bi-info-circle-fill';
        colorClase = 'warning';
        break;
    case 'NUEVO_USUARIO':
        titulo = 'Nuevos Usuarios';
        icono = 'bi-person-plus-fill';
        colorClase = 'info';
        break;
    case 'NUEVO_CLIENTE':
        titulo = 'Nuevos Clientes';
        icono = 'bi-people-fill';
        colorClase = 'primary';
        break;
    case 'NUEVO_PRODUCTO':
        titulo = 'Nuevos Productos';
        icono = 'bi-box-seam';
        colorClase = 'success';
        break;
    case 'NUEVO_PROVEEDOR':
        titulo = 'Nuevos Proveedores';
        icono = 'bi-truck';
        colorClase = 'secondary';
        break;
    case 'NUEVA_RECETA':
        titulo = 'Nuevas Recetas';
        icono = 'bi-journal-text';
        colorClase = 'info';
        break;
    case 'INICIO_SESION':
        titulo = 'Inicios de Sesión';
        icono = 'bi-door-open';
        colorClase = 'success';
        break;
    case 'RECEPCION_FINALIZADA':
        titulo = 'Recepciones Finalizadas';
        icono = 'bi-check-circle';
        colorClase = 'success';
        break;
    case 'PRODUCCION_INICIADA':
        titulo = 'Producciones Iniciadas';
        icono = 'bi-play-circle';
        colorClase = 'primary';
        break;
    case 'PRODUCCION_FINALIZADA':
        titulo = 'Producciones Finalizadas';
        icono = 'bi-check-circle-fill';
        colorClase = 'success';
        break;
    default:
        titulo = tipo;
        icono = 'bi-bell-fill';
        colorClase = 'secondary';
}

// Actualiza el número que aparece en la tarjeta de Ventas (`#total-ventas`)
function actualizarContadorVentas() {
    // Obtener todas las ventas directamente desde el modal para contar el total
    fetch(`/sistema/public/index.php?route=dashboard&action=detalle-ventas-modal&limit=100000`)
        .then(response => {
            if (!response.ok) throw new Error('Respuesta inválida del servidor');
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.data) {
                const ventas = data.data;
                const cantidad = ventas.length; // Contar registros obtenidos
                const montoTotal = ventas.reduce((sum, v) => sum + (parseFloat(v.TOTAL_VENTA) || 0), 0);
                
                // Mostrar cantidad de ventas en la tarjeta
                const elem = document.getElementById('total-ventas');
                if (elem) {
                    elem.textContent = cantidad;
                }
                
                // También actualizar los campos del panel de ventas si existen
                const elemCant = document.getElementById('total-ventas-cantidad');
                const elemMonto = document.getElementById('total-ventas-monto');
                if (elemCant) elemCant.textContent = cantidad;
                if (elemMonto) {
                    elemMonto.textContent = `L ${Number(montoTotal).toLocaleString('es-HN', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                }
            }
        })
        .catch(err => {
            console.error('Error actualizando contador de ventas:', err);
        });
}
                        
                        html += `<div class="mb-4">
                            <h6 class="text-${colorClase} mb-3">
                                <i class="bi ${icono} me-2"></i>
                                ${titulo} (${alertasTipo.length})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Nivel</th>
                                            <th>Fecha</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        
                        alertasTipo.forEach(alerta => {
                            let badgeNivel = '';
                            switch(alerta.NIVEL_URGENCIA) {
                                case 'CRITICA':
                                    badgeNivel = '<span class="badge bg-danger">CRÍTICA</span>';
                                    break;
                                case 'ALTA':
                                    badgeNivel = '<span class="badge bg-warning">ALTA</span>';
                                    break;
                                case 'MEDIA':
                                    badgeNivel = '<span class="badge bg-info">MEDIA</span>';
                                    break;
                                case 'BAJA':
                                    badgeNivel = '<span class="badge bg-secondary">BAJA</span>';
                                    break;
                                default:
                                    badgeNivel = '<span class="badge bg-secondary">' + alerta.NIVEL_URGENCIA + '</span>';
                            }
                            
                            const fecha = new Date(alerta.FECHA_CREACION);
                            const horasTranscurridas = Math.floor((new Date() - fecha) / (1000 * 60 * 60));
                            
                            let badgeTiempo = '';
                            if (horasTranscurridas < 1) {
                                badgeTiempo = '<span class="badge bg-success">Ahora</span>';
                            } else if (horasTranscurridas < 24) {
                                badgeTiempo = `<span class="badge bg-info">Hace ${horasTranscurridas}h</span>`;
                            } else {
                                const dias = Math.floor(horasTranscurridas / 24);
                                badgeTiempo = `<span class="badge bg-warning">Hace ${dias}d</span>`;
                            }
                            
                            html += `<tr>
                                <td>
                                    <strong>${alerta.TITULO}</strong>
                                    <br><small class="text-muted">${alerta.DESCRIPCION}</small>
                                </td>
                                <td>${badgeNivel}</td>
                                <td>
                                    <div>${fecha.toLocaleString('es-HN')}</div>
                                    <small>${badgeTiempo}</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="marcarAlertaLeida(${alerta.ID_ALERTA})">
                                        <i class="bi bi-check-lg"></i> Marcar leída
                                    </button>
                                </td>
                            </tr>`;
                        });
                        
                        html += `</tbody></table></div></div>`;
                    });
                } else {
                    html += `<div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        No hay alertas activas en este momento
                    </div>`;
                }
                
                html += '</div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar las alertas') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar las alertas: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalAlertasMateriaPrima')).show();
}

// Función para marcar alerta como leída
function marcarAlertaLeida(idAlerta) {
    const formData = new FormData();
    formData.append('action', 'marcar-alerta-leida');
    formData.append('id_alerta', idAlerta);
    
    fetch('/sistema/public/index.php?route=dashboard', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Recargar las alertas
            mostrarAlertasSistema();
        } else {
            alert('Error al marcar la alerta como leída: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al marcar la alerta como leída');
    });
}

// Función para actualizar alertas en tiempo real
function actualizarAlertasTiempoReal() {
    fetch('/sistema/public/index.php?route=dashboard&action=alertas-tiempo-real')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                actualizarContadorAlertasTiempoReal(data.data);
                
                // Si el modal de alertas está abierto, actualizar contenido
                const modalAlertas = document.getElementById('modalAlertasMateriaPrima');
                if (modalAlertas && modalAlertas.classList.contains('show')) {
                    mostrarAlertasSistema();
                }
            }
        })
        .catch(error => {
            console.error('Error al actualizar alertas:', error);
        });
}

// Función para actualizar el contador
function actualizarContadorAlertasTiempoReal(alertas) {
    const totalAlertas = alertas.length;
    const alertasCriticas = alertas.filter(a => a.NIVEL_URGENCIA === 'CRITICA').length;
    
    document.getElementById('total-alertas').textContent = totalAlertas;
    
    // Agregar badge para alertas críticas si existen
    const alertaElement = document.querySelector('[onclick="mostrarAlertasSistema()"]');
    let badgeCritico = alertaElement.querySelector('.badge-critico');
    
    if (alertasCriticas > 0) {
        if (!badgeCritico) {
            badgeCritico = document.createElement('span');
            badgeCritico.className = 'badge bg-danger badge-critico position-absolute top-0 start-100 translate-middle';
            badgeCritico.style.fontSize = '0.6rem';
            alertaElement.querySelector('.card-body').appendChild(badgeCritico);
        }
        badgeCritico.textContent = alertasCriticas;
    } else if (badgeCritico) {
        badgeCritico.remove();
    }
}

// Inicializar actualización en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar cada 30 segundos
    setInterval(actualizarAlertasTiempoReal, 30000);
    
    // Primera actualización
    actualizarAlertasTiempoReal();
});
// Función para actualizar el contador de ventas en tiempo real
function actualizarContadorVentasTiempoReal() {
    // Obtener todas las ventas directamente desde el modal para contar el total
    fetch(`/sistema/public/index.php?route=dashboard&action=detalle-ventas-modal&limit=100000`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.data) {
                const ventas = data.data;
                const cantidad = ventas.length; // Contar registros obtenidos
                const montoTotal = ventas.reduce((sum, v) => sum + (parseFloat(v.TOTAL_VENTA) || 0), 0);
                
                // Mostrar cantidad de ventas en la tarjeta
                const elem = document.getElementById('total-ventas');
                if (elem) {
                    elem.textContent = cantidad;
                }
                
                // También actualizar los campos del panel de ventas si existen
                const elemCant = document.getElementById('total-ventas-cantidad');
                const elemMonto = document.getElementById('total-ventas-monto');
                if (elemCant) elemCant.textContent = cantidad;
                if (elemMonto) {
                    elemMonto.textContent = `L ${Number(montoTotal).toLocaleString('es-HN', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                }

                // Si el modal de ventas está abierto, recargar su contenido
                const modalVentas = document.getElementById('modalVentasRecientes');
                if (modalVentas && modalVentas.classList.contains('show')) {
                    // Recargar las ventas mostradas en el modal
                    mostrarDetalleVentas();
                }
            }
        })
        .catch(error => {
            console.error('Error al actualizar contador de ventas:', error);
        });
}

// Inicializar actualización de ventas en tiempo real junto con alertas
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar cada 30 segundos
    setInterval(actualizarContadorVentasTiempoReal, 30000);

    // Primera actualización
    actualizarContadorVentasTiempoReal();
});

    function initializeCharts() {
        // Gráfica de líneas - Actividad del Sistema
        const lineCtx = document.getElementById('lineChart');
        if (lineCtx) {
            new Chart(lineCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [
                        {
                            label: 'Actividades Diarias',
                            data: [45, 52, 38, 61, 55, 48, 35],
                            borderColor: '#4361ee',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }
  </script>
</body>
