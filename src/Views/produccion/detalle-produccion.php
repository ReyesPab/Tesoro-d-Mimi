<?php 
// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión
$sesion_activa = false;
$variables_sesion = ['logged_in', 'iniciada', 'USUARIO', 'usuario', 'ID_USUARIO', 'id_usuario'];
foreach ($variables_sesion as $variable) {
    if (isset($_SESSION[$variable])) {
        $sesion_activa = true;
        break;
    }
}

if (!$sesion_activa) {
    echo "<script>
        alert('Sesión no encontrada. Serás redirigido al login.');
        window.location.href = '/sistema/public/login';
    </script>";
    exit;
}

// Obtener datos del usuario
$nombre_usuario = $_SESSION['NOMBRE_USUARIO'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? 'Usuario';
$id_usuario = $_SESSION['ID_USUARIO'] ?? $_SESSION['id_usuario'] ?? $_SESSION['user_id'] ?? 0;

// Obtener ID de producción desde GET
$id_produccion = $_GET['id'] ?? 0;
if (!$id_produccion) {
    echo "<script>
        alert('No se especificó la producción a visualizar.');
        window.location.href = '/sistema/public/ordenes-produccion';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Producción #<?php echo $id_produccion; ?> - Sistema Rosquillas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            /* Paleta anaranjada para encabezados */
            --orange-start: #ffb35c;   /* claro */
            --orange-end: #c95a00;     /* más oscuro */
            /* Paleta café claro para cabeceras superiores */
            --coffee-start: #f0d7c3;   /* café claro */
            --coffee-end: #8b5e3c;     /* café más oscuro */
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: transparent; /* fondo del nav sin color para poder limitar el ancho */
            padding: 0; /* quitamos padding para que lo controle el contenedor interno */
        }
        .navbar-custom > .container {
            background: linear-gradient(135deg, var(--orange-start) 0%, var(--orange-end) 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            color: #111;
            border-radius: 14px;
            margin: 8px auto; /* separarlo del borde superior */
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.15);
            padding-top: .6rem;
            padding-bottom: .6rem;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-text,
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand i {
            color: #111 !important;
        }
        .navbar-custom .btn-outline-light {
            color: #111;
            border-color: #111;
        }
        .navbar-custom .btn-outline-light:hover {
            background-color: #111;
            color: #fff;
            border-color: #111;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 20px auto;
            overflow: hidden;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, var(--orange-start) 0%, var(--orange-end) 100%);
            color: #111;
            padding: 25px 0;
            border-radius: 14px;
            overflow: hidden;
        }
        
        .info-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-card.success { border-left-color: var(--success-color); }
        .info-card.warning { border-left-color: var(--warning-color); }
        .info-card.info { border-left-color: #17a2b8; }
        .info-card.danger { border-left-color: var(--danger-color); }
        
        .badge-estado {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .table-custom thead {
            /* Encabezado de tablas en degradado anaranjado con texto negro */
            background: linear-gradient(135deg, var(--orange-start) 0%, var(--orange-end) 100%);
            color: #111 !important;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
        }

        /* Encabezados de tarjetas en degradado anaranjado */
        .card-header-custom {
            background: linear-gradient(135deg, var(--orange-start) 0%, var(--orange-end) 100%);
            color: #111 !important;
            border-bottom: none;
        }

        .card-header-custom h4,
        .card-header-custom i {
            color: inherit !important;
        }

        /* Estilos para el modal de finalización */
.perdida-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.perdida-item:hover {
    background: #e9ecef;
}

.perdida-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 10px;
}

.perdida-numero {
    font-weight: bold;
    color: #6c757d;
}

.btn-eliminar-perdida {
    color: #dc3545;
    background: none;
    border: none;
    padding: 2px 8px;
}

.btn-eliminar-perdida:hover {
    color: #fff;
    background: #dc3545;
}

.balance-correcto {
    color: #28a745;
    font-weight: bold;
}

.balance-incorrecto {
    color: #dc3545;
    font-weight: bold;
}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/sistema/public/inicio">
                <i class="fas fa-industry me-2"></i>SISTEMA ROSQUILLAS
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($nombre_usuario); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="/sistema/public/logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="header-gradient rounded-3 mb-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-6 fw-bold mb-2">
                            <i class="fas fa-file-alt me-3"></i>Detalle de Producción
                        </h1>
                        <p class="lead mb-0 opacity-75" id="subtitulo">Cargando información...</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="/sistema/public/crear-produccion" class="btn btn-light me-2">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                        <button class="btn btn-warning text-dark" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimir
                        </button>
                        <!-- En el header, después del botón Imprimir -->
                        <button class="btn btn-success" id="btnFinalizarProduccion" style="display: none;">
                         <i class="fas fa-check-circle me-1"></i>Finalizar Producción
                            </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando información de producción...</p>
        </div>

        <!-- Contenido Principal -->
        <div id="contenido" style="display: none;">
            <!-- Tarjetas de Resumen -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card info-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted mb-1">Estado</h6>
                                    <h4 class="mb-0" id="estado-badge">Cargando...</h4>
                                    <small class="text-muted">Estado actual</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-tag fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card info-card success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted mb-1">Eficiencia</h6>
                                    <h2 class="text-success mb-0" id="eficiencia">0%</h2>
                                    <small class="text-muted">Rendimiento</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card info-card warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted mb-1">Costo Materiales</h6>
                                    <h4 class="text-warning mb-0" id="costo-materiales">L. 0.00</h4>
                                    <small class="text-muted">Total invertido</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card info-card info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-muted mb-1">Materiales Usados</h6>
                                    <h2 class="text-info mb-0" id="total-materiales">0</h2>
                                    <small class="text-muted">Tipos de materiales</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-boxes fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Columna Izquierda - Información General -->
                <div class="col-lg-8">
                    <!-- Información de la Producción -->
                    <div class="main-container mb-4">
                        <div class="card-header card-header-custom py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información General
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold text-muted">Producto:</td>
                                            <td id="info-producto">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Cantidad Planificada:</td>
                                            <td id="info-cantidad-planificada">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Cantidad Real:</td>
                                            <td id="info-cantidad-real">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Costo Total:</td>
                                            <td id="info-costo-total">-</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold text-muted">Fecha Inicio:</td>
                                            <td id="info-fecha-inicio">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Fecha Fin:</td>
                                            <td id="info-fecha-fin">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Responsable:</td>
                                            <td id="info-responsable">-</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Observaciones:</td>
                                            <td id="info-observaciones">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Materias Primas Utilizadas -->
                    <div class="main-container mb-4">
                        <div class="card-header card-header-custom py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>Materias Primas Utilizadas
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-custom" id="tabla-materiales">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th class="text-center">Cantidad Usada</th>
                                            <th class="text-center">Costo Unitario</th>
                                            <th class="text-center">Subtotal</th>
                                            <th class="text-center">Receta Unit.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-materiales">
                                        <!-- Los datos se cargan por JavaScript -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Total:</td>
                                            <td class="text-center" id="total-materiales-footer">L. 0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Movimientos en Cardex -->
                    <div class="main-container mb-4">
                        <div class="card-header card-header-custom py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-history me-2"></i>Movimientos de Inventario
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabla-cardex">
                                    <thead>
                                        <tr>
                                            <th>Material</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Tipo</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-cardex">
                                        <!-- Los datos se cargan por JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Información Adicional -->
                <div class="col-lg-4">
                    <!-- Historial de Bitácora -->
                    <div class="main-container mb-4">
                        <div class="card-header card-header-custom py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>Historial del Sistema
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="timeline" id="timeline-bitacora">
                                <!-- Los datos se cargan por JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Información de Auditoría -->
                    <div class="main-container">
                        <div class="card-header card-header-custom py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-user-shield me-2"></i>Información de Auditoría
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Creado por:</td>
                                    <td id="auditoria-creado-por">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Fecha creación:</td>
                                    <td id="auditoria-fecha-creacion">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Modificado por:</td>
                                    <td id="auditoria-modificado-por">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Fecha modificación:</td>
                                    <td id="auditoria-fecha-modificacion">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Error Message -->
        <div id="error-message" class="alert alert-danger text-center" style="display: none;">
            <h4><i class="fas fa-exclamation-triangle me-2"></i>Error</h4>
            <p id="error-text">No se pudo cargar la información de la producción.</p>
            <a href="/sistema/public/ordenes-produccion" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Volver a Órdenes
            </a>
        </div>
         
    </div>

   <!-- Modal para Finalizar Producción -->
<div class="modal fade" id="modalFinalizarProduccion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Finalizar Producción
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Resumen de la Producción -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Resumen de Producción</h6>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Producto:</strong> <span id="modal-producto">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Planificado:</strong> <span id="modal-planificado">-</span> unidades
                        </div>
                    </div>
                </div>

                <!-- Cantidad Buena -->
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-thumbs-up me-1 text-success"></i>Cantidad Buena Producida
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="cantidadBuena" 
                               min="0" step="1" placeholder="Ingrese cantidad de productos en buen estado">
                        <span class="input-group-text">unidades</span>
                    </div>
                    <div class="form-text">
                        Solo los productos en buen estado irán al inventario.
                    </div>
                </div>

                <!-- Sección de Pérdidas -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">
                            <i class="fas fa-exclamation-triangle me-1 text-warning"></i>Registro de Pérdidas
                        </label>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarPerdida">
                            <i class="fas fa-plus me-1"></i>Agregar Pérdida
                        </button>
                    </div>
                    
                    <div id="contenedorPerdidas">
                        <!-- Las pérdidas se agregarán dinámicamente aquí -->
                    </div>
                    
                    <div class="mt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Total Buenas:</strong> <span id="totalBuenas" class="text-success">0</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Total Pérdidas:</strong> <span id="totalPerdidas" class="text-danger">0</span>
                            </div>
                        </div>
                        <div class="mt-1">
                            <strong>Balance:</strong> 
                            <span id="balanceTotal" class="fw-bold">0</span> / 
                            <span id="totalPlanificado">0</span> unidades
                            <span id="balanceStatus" class="ms-2"></span>
                        </div>
                    </div>
                </div>

                <!-- Observaciones Finales -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-sticky-note me-1"></i>Observaciones Finales
                    </label>
                    <textarea class="form-control" id="observacionesFinales" 
                              rows="3" placeholder="Observaciones sobre el resultado de la producción..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnConfirmarFinalizacion" disabled>
                    <i class="fas fa-check me-1"></i>Confirmar Finalización
                </button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const ID_PRODUCCION = <?php echo $id_produccion; ?>;

// ==============================================
// CÓDIGO PARA FINALIZAR PRODUCCIÓN
// ==============================================

// Variables globales para finalización
// Variables globales para finalización
let datosProduccion = null;
let contadorPerdidas = 0;

$(document).ready(function() {
    console.log('🎯 Cargando detalle de producción:', ID_PRODUCCION);
    cargarDetalleProduccion();
});

function cargarDetalleProduccion() {
    $.ajax({
        url: '/sistema/public/produccion?caso=obtenerDetalleProduccion&id_produccion=' + ID_PRODUCCION,
        type: 'GET',
        success: function(response) {
            console.log('✅ Detalle cargado:', response);
            
            if (response.status === 200 && response.data) {
                mostrarDetalle(response.data);
            } else {
                mostrarError(response.message || 'Error al cargar el detalle');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error cargando detalle:', error);
            mostrarError('Error de conexión: ' + error);
        }
    });
}

function mostrarDetalle(data) {
    $('#loading').hide();
    $('#contenido').show();
    
    const prod = data.produccion;
    const stats = data.estadisticas;
    datosProduccion = data; // Guardar para usar después
    
    // Actualizar header
    $('#subtitulo').text(`Producción #${prod.ID_PRODUCCION} - ${prod.PRODUCTO}`);
    
    // Actualizar tarjetas de resumen
    actualizarBadgeEstado(prod.ESTADO_PRODUCCION);
    $('#eficiencia').text(stats.eficiencia + '%');
    $('#costo-materiales').text('L. ' + stats.costo_total_materiales.toFixed(2));
    $('#total-materiales').text(stats.total_materiales);
    
    // Actualizar información general
    $('#info-producto').html(`<strong>${prod.PRODUCTO}</strong><br><small class="text-muted">${prod.DESCRIPCION_PRODUCTO || 'Sin descripción'}</small>`);
    $('#info-cantidad-planificada').html(`<span class="badge bg-primary">${parseFloat(prod.CANTIDAD_PLANIFICADA).toFixed(0)} unidades</span>`);
    $('#info-cantidad-real').html(prod.CANTIDAD_REAL ? `<span class="badge bg-success">${parseFloat(prod.CANTIDAD_REAL).toFixed(0)} unidades</span>` : '<span class="badge bg-secondary">Pendiente</span>');
    $('#info-costo-total').html(`<strong>L. ${parseFloat(prod.COSTO_TOTAL).toFixed(2)}</strong>`);
    $('#info-fecha-inicio').text(formatearFecha(prod.FECHA_INICIO));
    $('#info-fecha-fin').text(prod.FECHA_FIN ? formatearFecha(prod.FECHA_FIN) : 'En proceso...');
    $('#info-responsable').text(prod.RESPONSABLE);
    $('#info-observaciones').text(prod.OBSERVACION || 'Sin observaciones');
    
    // Actualizar materias primas
    actualizarTablaMateriales(data.materias_primas);
    
    // Actualizar movimientos cardex
    actualizarTablaCardex(data.movimientos_cardex);
    
    // Actualizar bitácora
    actualizarTimelineBitacora(data.bitacora);
    
    // Actualizar auditoría
    $('#auditoria-creado-por').text(prod.CREADO_POR);
    $('#auditoria-fecha-creacion').text(formatearFecha(prod.FECHA_CREACION));
    $('#auditoria-modificado-por').text(prod.MODIFICADO_POR || 'No modificado');
    $('#auditoria-fecha-modificacion').text(prod.FECHA_MODIFICACION ? formatearFecha(prod.FECHA_MODIFICACION) : 'No modificado');
    
    // 🆕 MOSTRAR BOTÓN SOLO SI ESTÁ EN PROCESO
    if (prod.ESTADO_PRODUCCION === 'EN_PROCESO') {
        $('#btnFinalizarProduccion').show();
    }
}

// 🆕 Evento para abrir el modal de finalización
// 🆕 Evento para abrir el modal de finalización
$(document).on('click', '#btnFinalizarProduccion', function() {
    abrirModalFinalizacion();
});

// 🆕 Función para abrir el modal
function abrirModalFinalizacion() {
    if (!datosProduccion) return;
    
    const prod = datosProduccion.produccion;
    
    // Llenar datos del modal
    $('#modal-producto').text(prod.PRODUCTO);
    $('#modal-planificado').text(prod.CANTIDAD_PLANIFICADA);
    $('#totalPlanificado').text(prod.CANTIDAD_PLANIFICADA);
    
    // Resetear formulario
    $('#cantidadBuena').val('');
    $('#observacionesFinales').val('');
    $('#contenedorPerdidas').empty();
    contadorPerdidas = 0;
    actualizarTotales();
    
    // Mostrar modal
    $('#modalFinalizarProduccion').modal('show');
}

// 🆕 Evento para agregar pérdida
$(document).on('click', '#btnAgregarPerdida', function() {
    agregarPerdida();
});

// 🆕 Función para agregar una línea de pérdida
function agregarPerdida() {
    contadorPerdidas++;
    
    const htmlPerdida = `
        <div class="perdida-item" id="perdida-${contadorPerdidas}">
            <div class="perdida-header">
                <span class="perdida-numero">Pérdida #${contadorPerdidas}</span>
                <button type="button" class="btn-eliminar-perdida" data-id="${contadorPerdidas}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small">Motivo</label>
                    <select class="form-select form-select-sm motivo-perdida" data-id="${contadorPerdidas}">
                        <option value="">Seleccionar motivo...</option>
                        <option value="DEF_CALIDAD">Defecto de Calidad</option>
                        <option value="EQUIPO">Falla de Equipo</option>
                        <option value="MAT_PRIMA">Materia Prima Defectuosa</option>
                        <option value="PROCESO">Error en Proceso</option>
                        <option value="MANIPULACION">Mala Manipulación</option>
                        <option value="ALMACEN">Problema de Almacenamiento</option>
                        <option value="CADUCIDAD">Caducidad</option>
                        <option value="PRUEBA">Muestras de Prueba</option>
                        <option value="CLIENTE">Rechazo de Cliente</option>
                        <option value="OTRO">Otro Motivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Cantidad</label>
                    <input type="number" class="form-control form-control-sm cantidad-perdida" 
                           data-id="${contadorPerdidas}" min="1" step="1" placeholder="0">
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Descripción</label>
                    <input type="text" class="form-control form-control-sm descripcion-perdida" 
                           data-id="${contadorPerdidas}" placeholder="Descripción de la pérdida...">
                </div>
            </div>
        </div>
    `;
    
    $('#contenedorPerdidas').append(htmlPerdida);
    actualizarTotales();
}

// 🆕 Evento para eliminar pérdida
$(document).on('click', '.btn-eliminar-perdida', function() {
    const id = $(this).data('id');
    $(`#perdida-${id}`).remove();
    actualizarTotales();
});

// 🆕 Eventos para actualizar totales en tiempo real
$(document).on('input', '#cantidadBuena', actualizarTotales);
$(document).on('input', '.cantidad-perdida', actualizarTotales);

// 🆕 Función para calcular y actualizar totales
function actualizarTotales() {
    const cantidadBuena = parseFloat($('#cantidadBuena').val()) || 0;
    const totalPlanificado = parseFloat(datosProduccion?.produccion?.CANTIDAD_PLANIFICADA) || 0;
    
    // Calcular total de pérdidas
    let totalPerdidas = 0;
    $('.cantidad-perdida').each(function() {
        totalPerdidas += parseFloat($(this).val()) || 0;
    });
    
    // Actualizar displays
    $('#totalBuenas').text(cantidadBuena);
    $('#totalPerdidas').text(totalPerdidas);
    $('#balanceTotal').text(cantidadBuena + totalPerdidas);
    
    // Validar balance
    const balance = cantidadBuena + totalPerdidas;
    const balanceElement = $('#balanceTotal');
    
    if (balance === totalPlanificado) {
        balanceElement.removeClass('balance-incorrecto').addClass('balance-correcto');
        $('#btnConfirmarFinalizacion').prop('disabled', false);
    } else {
        balanceElement.removeClass('balance-correcto').addClass('balance-incorrecto');
        $('#btnConfirmarFinalizacion').prop('disabled', true);
    }
}// 🆕 Función para abrir el modal
function abrirModalFinalizacion() {
    if (!datosProduccion) {
        mostrarAlerta('Error', 'No se pudieron cargar los datos de la producción', 'error');
        return;
    }
    
    const prod = datosProduccion.produccion;
    
    // Validar que esté en estado EN_PROCESO
    if (prod.ESTADO_PRODUCCION !== 'EN_PROCESO') {
        mostrarAlerta('Error', 
            `No se puede finalizar una producción en estado: ${prod.ESTADO_PRODUCCION}. 
             Solo se pueden finalizar producciones en estado EN_PROCESO.`, 
            'error'
        );
        return;
    }
    
    // Llenar datos del modal
    $('#modal-producto').text(prod.PRODUCTO);
    $('#modal-planificado').text(prod.CANTIDAD_PLANIFICADA);
    $('#totalPlanificado').text(prod.CANTIDAD_PLANIFICADA);
    
    // Resetear formulario
    $('#cantidadBuena').val('');
    $('#observacionesFinales').val('');
    $('#contenedorPerdidas').empty();
    contadorPerdidas = 0;
    actualizarTotales();
    
    // Mostrar modal
    $('#modalFinalizarProduccion').modal('show');
}

// 🆕 Evento para agregar pérdida
$(document).on('click', '#btnAgregarPerdida', function() {
    agregarPerdida();
});

// 🆕 Función para agregar una línea de pérdida
function agregarPerdida() {
    contadorPerdidas++;
    
    const htmlPerdida = `
        <div class="perdida-item" id="perdida-${contadorPerdidas}">
            <div class="perdida-header">
                <span class="perdida-numero">Pérdida #${contadorPerdidas}</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-perdida" data-id="${contadorPerdidas}">
                    <i class="fas fa-times"></i> Eliminar
                </button>
            </div>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small">Motivo *</label>
                    <select class="form-select form-select-sm motivo-perdida" data-id="${contadorPerdidas}" required>
                        // En la función agregarPerdida(), cambia los valores a los códigos correctos:
<option value="DEF_CALIDA">Defecto de Calidad</option>
<option value="EQUIPO">Falla de Equipo</option>
<option value="MAT_PRIMA">Materia Prima Defectuosa</option>
<option value="PROCESO">Error en Proceso</option>
<option value="MANIPULACI">Mala Manipulación</option>
<option value="ALMACEN">Problema de Almacenamiento</option>
<option value="CADUCIDAD">Caducidad</option>
<option value="PRUEBA">Muestras de Prueba</option>
<option value="CLIENTE">Rechazo de Cliente</option>
<option value="OTRO">Otro Motivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Cantidad *</label>
                    <input type="number" class="form-control form-control-sm cantidad-perdida" 
                           data-id="${contadorPerdidas}" min="1" step="1" placeholder="0" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Descripción</label>
                    <input type="text" class="form-control form-control-sm descripcion-perdida" 
                           data-id="${contadorPerdidas}" placeholder="Descripción de la pérdida...">
                </div>
            </div>
        </div>
    `;
    
    $('#contenedorPerdidas').append(htmlPerdida);
    actualizarTotales();
}

// 🆕 Evento para eliminar pérdida
$(document).on('click', '.btn-eliminar-perdida', function() {
    const id = $(this).data('id');
    $(`#perdida-${id}`).remove();
    actualizarTotales();
});

// 🆕 Eventos para actualizar totales en tiempo real
$(document).on('input', '#cantidadBuena', actualizarTotales);
$(document).on('input', '.cantidad-perdida', actualizarTotales);
$(document).on('change', '.motivo-perdida', validarFormulario);

// 🆕 Función para calcular y actualizar totales
function actualizarTotales() {
    const cantidadBuena = parseFloat($('#cantidadBuena').val()) || 0;
    const totalPlanificado = parseFloat(datosProduccion?.produccion?.CANTIDAD_PLANIFICADA) || 0;
    
    // Calcular total de pérdidas
    let totalPerdidas = 0;
    $('.cantidad-perdida').each(function() {
        totalPerdidas += parseFloat($(this).val()) || 0;
    });
    
    // Actualizar displays
    $('#totalBuenas').text(cantidadBuena);
    $('#totalPerdidas').text(totalPerdidas);
    $('#balanceTotal').text(cantidadBuena + totalPerdidas);
    
    // Validar balance
    const balance = cantidadBuena + totalPerdidas;
    const balanceElement = $('#balanceTotal');
    const balanceStatus = $('#balanceStatus');
    
    if (balance === totalPlanificado) {
        balanceElement.removeClass('text-danger').addClass('text-success');
        balanceStatus.html('<i class="fas fa-check-circle text-success"></i> Balance correcto');
        $('#btnConfirmarFinalizacion').prop('disabled', false);
    } else if (balance < totalPlanificado) {
        balanceElement.removeClass('text-success').addClass('text-danger');
        const diferencia = totalPlanificado - balance;
        balanceStatus.html(`<i class="fas fa-exclamation-triangle text-warning"></i> Faltan ${diferencia} unidades`);
        $('#btnConfirmarFinalizacion').prop('disabled', true);
    } else {
        balanceElement.removeClass('text-success').addClass('text-danger');
        const diferencia = balance - totalPlanificado;
        balanceStatus.html(`<i class="fas fa-exclamation-triangle text-danger"></i> Sobran ${diferencia} unidades`);
        $('#btnConfirmarFinalizacion').prop('disabled', true);
    }
    
    // Validar formulario completo
    validarFormulario();
} 


// 🆕 Evento para confirmar finalización
$(document).on('click', '#btnConfirmarFinalizacion', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('🎯 Botón Confirmar Finalización clickeado');
    confirmarFinalizacion();
});



// 🆕 Función para validar el formulario completo
function validarFormulario() {
    const cantidadBuena = parseFloat($('#cantidadBuena').val()) || 0;
    let formularioValido = true;
    
    // Validar cantidad buena
    if (cantidadBuena <= 0) {
        formularioValido = false;
    }
    
    // Validar pérdidas
    $('.perdida-item').each(function() {
        const motivo = $(this).find('.motivo-perdida').val();
        const cantidad = parseFloat($(this).find('.cantidad-perdida').val()) || 0;
        
        if (!motivo || cantidad <= 0) {
            formularioValido = false;
        }
    });
    
    // Validar balance
    const totalPlanificado = parseFloat(datosProduccion?.produccion?.CANTIDAD_PLANIFICADA) || 0;
    let totalPerdidas = 0;
    $('.cantidad-perdida').each(function() {
        totalPerdidas += parseFloat($(this).val()) || 0;
    });
    
    if ((cantidadBuena + totalPerdidas) !== totalPlanificado) {
        formularioValido = false;
    }
    
    $('#btnConfirmarFinalizacion').prop('disabled', !formularioValido);
}

// 🆕 Evento para confirmar finalización
$(document).on('click', '#btnConfirmarFinalizacion', function() {
    confirmarFinalizacion();
});

// 🆕 Función para confirmar finalización
function confirmarFinalizacion() {
    const cantidadBuena = parseFloat($('#cantidadBuena').val()) || 0;
    const observaciones = $('#observacionesFinales').val();
    
    // Validar cantidad buena
    if (cantidadBuena <= 0) {
        mostrarAlerta('Error', 'La cantidad buena debe ser mayor a 0', 'error');
        return;
    }

    // Recolectar pérdidas
    const perdidas = [];
    let totalPerdidas = 0;
    
    $('.perdida-item').each(function() {
        const motivo = $(this).find('.motivo-perdida').val();
        const cantidad = parseFloat($(this).find('.cantidad-perdida').val()) || 0;
        const descripcion = $(this).find('.descripcion-perdida').val();
        
        if (motivo && cantidad > 0) {
            perdidas.push({
                motivo: motivo,
                cantidad: cantidad,
                descripcion: descripcion
            });
            totalPerdidas += cantidad;
        }
    });

    // Preparar datos para enviar
    const datosFinalizacion = {
        id_produccion: ID_PRODUCCION,
        id_usuario: <?php echo $id_usuario; ?>,
        cantidad_buena: cantidadBuena,
        perdidas: perdidas,
        observaciones: observaciones,
        modificado_por: "<?php echo $nombre_usuario; ?>"
    };

    // Mostrar confirmación
    mostrarConfirmacionFinalizacion(datosFinalizacion, cantidadBuena, totalPerdidas, perdidas.length);
}


function mostrarAlerta(title, text, icon) {
    if (typeof Swal !== 'undefined') {
        Swal.fire(title, text, icon);
    } else {
        alert(`${title}: ${text}`);
        console.log(`${icon.toUpperCase()}: ${title} - ${text}`);
    }
}

// 🆕 Función para mostrar confirmación
function mostrarConfirmacionFinalizacion(datos, cantidadBuena, totalPerdidas, numPerdidas) {
    const confirmacionHTML = `
        <div class="text-start">
            <p><strong>¿Está seguro de finalizar la producción?</strong></p>
            <div class="alert alert-warning">
                <strong>Resumen Final:</strong>
                <ul class="mb-1">
                    <li>Productos Buenos: <strong class="text-success">${cantidadBuena}</strong></li>
                    <li>Total Pérdidas: <strong class="text-danger">${totalPerdidas}</strong></li>
                    <li>Registros de Pérdida: <strong>${numPerdidas}</strong></li>
                    <li>Total Producción: <strong>${cantidadBuena + totalPerdidas}</strong></li>
                </ul>
            </div>
            <p class="text-danger small">
                <i class="fas fa-exclamation-triangle"></i> 
                Esta acción no se puede deshacer. Los productos buenos se agregarán al inventario.
            </p>
        </div>
    `;

    Swal.fire({
        title: 'Finalizar Producción',
        html: confirmacionHTML,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check me-1"></i> Sí, Finalizar',
        cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarFinalizacion(datos);
        }
    });
}


// 🆕 Función para enviar datos al servidor
// 🆕 Función mejorada para enviar datos al servidor
function enviarFinalizacion(datos) {
    // Mostrar loading
    Swal.fire({
        title: 'Finalizando Producción...',
        text: 'Procesando resultados y actualizando inventarios',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar FormData
    const formData = new FormData();
    formData.append('id_produccion', datos.id_produccion);
    formData.append('id_usuario', datos.id_usuario);
    formData.append('cantidad_buena', datos.cantidad_buena);
    formData.append('observaciones', datos.observaciones);
    formData.append('modificado_por', datos.modificado_por);
    
    // Agregar pérdidas como JSON
    if (datos.perdidas && datos.perdidas.length > 0) {
        formData.append('perdidas', JSON.stringify(datos.perdidas));
    }
    
    $.ajax({
        url: '/sistema/public/produccion?caso=finalizarProduccion',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();
            
            if (response.status === 200) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Producción Finalizada!',
                    html: `
                        <div class="text-start">
                            <p>${response.message}</p>
                            <div class="alert alert-success mt-3">
                                <strong>Acciones realizadas:</strong>
                                <ul class="mb-0">
                                    <li>Producción marcada como FINALIZADA</li>
                                    <li>${datos.cantidad_buena} productos buenos agregados al inventario</li>
                                    ${datos.perdidas && datos.perdidas.length > 0 ? 
                                        `<li>${datos.perdidas.length} registros de pérdidas guardados</li>` : 
                                        '<li>No se registraron pérdidas</li>'
                                    }
                                    <li>Bitácora actualizada</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Cerrar modal y recargar página
                    $('#modalFinalizarProduccion').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            let errorMsg = 'Error de conexión: ' + error;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

// 🆕 Función para debug - Agregar al final del script
function debugFinalizacion() {
    console.log('=== DEBUG FINALIZACIÓN ===');
    console.log('ID_PRODUCCION:', ID_PRODUCCION);
    console.log('Datos producción:', datosProduccion);
    console.log('Botón finalizar visible:', $('#btnFinalizarProduccion').is(':visible'));
    console.log('Modal cargado:', $('#modalFinalizarProduccion').length > 0);
    console.log('SweetAlert cargado:', typeof Swal !== 'undefined');
    console.log('======================');
}

// Ejecutar debug al cargar la página
$(document).ready(function() {
    setTimeout(debugFinalizacion, 2000);
});
// ==============================================
// FUNCIONES EXISTENTES (NO MODIFICAR)
// ==============================================

function actualizarBadgeEstado(estado) {
    const estados = {
        'PLANIFICADO': ['bg-warning text-dark', '📋 PLANIFICADO'],
        'EN_PROCESO': ['bg-info text-white', '⚙️ EN PROCESO'], 
        'FINALIZADO': ['bg-success text-white', '✅ FINALIZADO'],
        'CANCELADO': ['bg-danger text-white', '❌ CANCELADO']
    };
    
    const [clase, texto] = estados[estado] || ['bg-secondary text-white', '❓ DESCONOCIDO'];
    $('#estado-badge').html(`<span class="badge ${clase} badge-estado">${texto}</span>`);
}

function actualizarTablaMateriales(materiales) {
    const tbody = $('#tbody-materiales');
    tbody.empty();
    
    let total = 0;
    
    materiales.forEach(mp => {
        total += parseFloat(mp.SUBTOTAL);
        tbody.append(`
            <tr>
                <td>
                    <strong>${mp.MATERIA_PRIMA}</strong><br>
                    <small class="text-muted">${mp.UNIDAD}</small>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary">${parseFloat(mp.CANTIDAD_USADA).toFixed(2)}</span>
                </td>
                <td class="text-center">L. ${parseFloat(mp.COSTO_UNITARIO).toFixed(2)}</td>
                <td class="text-center fw-bold">L. ${parseFloat(mp.SUBTOTAL).toFixed(2)}</td>
                <td class="text-center">
                    <small class="text-muted">${parseFloat(mp.CANTIDAD_RECETA_UNITARIA).toFixed(2)} ${mp.UNIDAD}/unidad</small>
                </td>
            </tr>
        `);
    });
    
    $('#total-materiales-footer').text('L. ' + total.toFixed(2));
}

function actualizarTablaCardex(movimientos) {
    const tbody = $('#tbody-cardex');
    tbody.empty();
    
    if (movimientos.length === 0) {
        tbody.append('<tr><td colspan="5" class="text-center text-muted">No hay movimientos registrados</td></tr>');
        return;
    }
    
    movimientos.forEach(mov => {
        const badgeClass = mov.TIPO_MOVIMIENTO === 'SALIDA' ? 'bg-danger' : 'bg-success';
        tbody.append(`
            <tr>
                <td>${mov.MATERIA_PRIMA}</td>
                <td class="text-center">
                    <span class="badge ${badgeClass}">${parseFloat(mov.CANTIDAD).toFixed(2)}</span>
                </td>
                <td class="text-center">
                    <span class="badge ${badgeClass}">${mov.TIPO_MOVIMIENTO}</span>
                </td>
                <td><small>${mov.DESCRIPCION}</small></td>
                <td class="text-center"><small>${formatearFecha(mov.FECHA_MOVIMIENTO)}</small></td>
            </tr>
        `);
    });
}

function actualizarTimelineBitacora(bitacora) {
    const timeline = $('#timeline-bitacora');
    timeline.empty();
    
    if (bitacora.length === 0) {
        timeline.append('<p class="text-muted text-center">No hay registros en bitácora</p>');
        return;
    }
    
    bitacora.forEach(reg => {
        timeline.append(`
            <div class="timeline-item">
                <div class="card border-0 shadow-sm mb-2">
                    <div class="card-body p-3">
                        <h6 class="card-title mb-1">${reg.ACCION}</h6>
                        <p class="card-text mb-1 small">${reg.DESCRIPCION}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${reg.USUARIO_BITACORA}</small>
                            <small class="text-muted">${formatearFecha(reg.FECHA)}</small>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}

function formatearFecha(fechaString) {
    if (!fechaString) return '-';
    const fecha = new Date(fechaString);
    return fecha.toLocaleDateString('es-HN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function mostrarError(mensaje) {
    $('#loading').hide();
    $('#error-text').text(mensaje);
    $('#error-message').show();
}

    
    </script>
</body>
</html>