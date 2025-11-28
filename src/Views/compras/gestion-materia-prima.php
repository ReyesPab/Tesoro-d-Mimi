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
    <title>Gestión de Materia Prima - Sistema de Gestión</title>
    
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

    /* Columnas específicas - ACTUALIZADO (eliminadas 4 columnas) */
    .table td:nth-child(1) { /* NOMBRE */ 
        min-width: 150px;
        max-width: 200px;
    }

    .table td:nth-child(2) { /* DESCRIPCION */
        min-width: 180px;
        max-width: 250px;
    }

    .table td:nth-child(3) { /* UNIDAD */
        min-width: 80px;
        max-width: 100px;
        text-align: center;
    }

    .table td:nth-child(4) { /* PRECIO - AHORA ES LA 4TA COLUMNA */
        min-width: 100px;
        max-width: 120px;
        text-align: right;
    }

    .table td:nth-child(5) { /* FECHA CREACION - AHORA ES LA 5TA COLUMNA */
        min-width: 120px;
        max-width: 130px;
        white-space: nowrap;
    }

    .table td:nth-child(6) { /* ESTADO - AHORA ES LA 6TA COLUMNA */
        min-width: 80px;
        max-width: 90px;
        text-align: center;
    }

    /* Columna de acciones - AHORA ES LA 7MA COLUMNA */
    .table th:last-child,
    .table td:last-child {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
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

        /* Ajustes responsive para menos columnas */
        .table td:nth-child(1) { min-width: 120px; max-width: 150px; }
        .table td:nth-child(2) { min-width: 150px; max-width: 200px; }
        .table td:nth-child(3) { min-width: 70px; max-width: 80px; }
        .table td:nth-child(4) { min-width: 90px; max-width: 100px; }
        .table td:nth-child(5) { min-width: 100px; max-width: 110px; }
        .table td:nth-child(6) { min-width: 70px; max-width: 80px; }
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
                    <h1 class="h2 mb-0">Gestión de Materia Prima</h1>
                    <div class="d-flex gap-2">
                        <!-- Botón Ingresar Productos al Inventario -->
                        <a href="/sistema/public/gestion-inventario" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-down"></i> Ingresar al Inventario
                        </a>
                        
                        <!-- Botón Exportar PDF -->
                        <button class="btn btn-warning" onclick="exportarPDF()">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alertas de Inventario -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <div>
                            <strong>Gestión de Materia Prima:</strong> Lista de todos los productos de materia prima (activos e inactivos) disponibles en el sistema.
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
                                    <div class="col-md-4">
                                        <label for="filtro_nombre" class="form-label">Nombre de Materia Prima</label>
                                        <input type="text" class="form-control" id="filtro_nombre" 
                                               placeholder="Buscar por nombre...">
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <label for="filtro_estado" class="form-label">Estado</label>
                                        <select class="form-select" id="filtro_estado">
                                            <option value="">Todos los estados</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                    </div>
                                   
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarMateriaPrima()">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
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
                        <p class="text-muted mt-2">Cargando materia prima...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaMateriaPrima">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Unidad</th>
                                    <th>Precio de Compra</th>
                                    <th>Fecha Creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMateriaPrima">
                                <!-- La materia prima se cargará aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontró materia prima con los filtros aplicados.
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Materia Prima
                                    </h6>
                                    <div id="resumenMateriaPrima">
                                        <p>Total productos: <strong id="totalProductos">0</strong></p>
                                        <p>Activos: <strong id="totalActivos">0</strong></p>
                                        <p>Inactivos: <strong id="totalInactivos">0</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Ingresar al Inventario -->
    <div class="modal fade" id="modalIngresarInventario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ingresar al Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formIngresarInventario">
                        <input type="hidden" id="ingresar_id_materia_prima" name="ID_MATERIA_PRIMA">
                        <input type="hidden" id="ingresar_nombre_materia_prima" name="NOMBRE_MATERIA_PRIMA">
                        
                        <div class="mb-3">
                            <label class="form-label">Materia Prima:</label>
                            <p class="form-control-plaintext fw-bold" id="display_nombre_materia_prima"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stock Actual en Materia Prima:</label>
                            <p class="form-control-plaintext" id="display_stock_actual"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ingresar_cantidad" class="form-label">Cantidad a Ingresar al Inventario:</label>
                            <input type="number" class="form-control" id="ingresar_cantidad" name="CANTIDAD" 
                                   step="0.01" min="0.01" required placeholder="Ingrese la cantidad">
                            <div class="form-text">Esta cantidad se restará de Materia Prima y se sumará al Inventario</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarIngreso">Ingresar al Inventario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cargarMateriaPrima();
            
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') cargarMateriaPrima();
            });
            
            document.getElementById('filtro_estado').addEventListener('change', cargarMateriaPrima);
            
            document.getElementById('btnConfirmarIngreso').addEventListener('click', confirmarIngresoInventario);
        });

        function cargarMateriaPrima() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyMateriaPrima');
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
            
            fetch(`/sistema/public/index.php?route=compras&caso=listarMateriaPrima&${queryParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data && data.data.length > 0) {
                    mostrarMateriaPrima(data.data);
                } else {
                    tbody.innerHTML = '';
                    sinResultados.style.display = 'block';
                    actualizarResumen([]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar la materia prima</td></tr>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarMateriaPrima(materiaPrima) {
    const tbody = document.getElementById('tbodyMateriaPrima');
    const sinResultados = document.getElementById('sinResultados');
    
    if (materiaPrima.length === 0) {
        tbody.innerHTML = '';
        sinResultados.style.display = 'block';
        actualizarResumen([]);
        return;
    }
    
    let html = '';
    materiaPrima.forEach(item => {
        html += `
            <tr>
                <td>
                    <strong>${item.NOMBRE}</strong>
                </td>
                <td>${item.DESCRIPCION || '-'}</td>
                <td class="text-center">
                    <span class="badge bg-secondary">${item.UNIDAD}</span>
                </td>
                <td class="text-end price">
                    L ${parseFloat(item.PRECIO_PROMEDIO).toFixed(2)}
                </td>
                <td>
                    ${item.FECHA_CREACION_FORMATEADA || '-'}
                </td>
                <td class="text-center">
                    <span class="badge ${getBadgeClass(item.ESTADO)}">
                        ${item.ESTADO}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info" onclick="ingresarInventario(${item.ID_MATERIA_PRIMA}, '${item.NOMBRE}', ${item.STOCK_ACTUAL || 0})" title="Sacar al Inventario">
                            <i class="bi bi-box-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="editarMateriaPrima(${item.ID_MATERIA_PRIMA})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    actualizarResumen(materiaPrima);
}

        function actualizarResumen(materiaPrima) {
            if (materiaPrima.length === 0) {
                document.getElementById('totalProductos').textContent = '0';
                document.getElementById('totalActivos').textContent = '0';
                document.getElementById('totalInactivos').textContent = '0';
                return;
            }
            
            const totalActivos = materiaPrima.filter(item => item.ESTADO === 'ACTIVO').length;
            const totalInactivos = materiaPrima.filter(item => item.ESTADO === 'INACTIVO').length;
            
            document.getElementById('totalProductos').textContent = materiaPrima.length;
            document.getElementById('totalActivos').textContent = totalActivos;
            document.getElementById('totalInactivos').textContent = totalInactivos;
        }

        function getBadgeClass(estado) {
            return estado === 'ACTIVO' ? 'bg-estado-activo' : 'bg-estado-inactivo';
        }

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_estado').value = '';
            cargarMateriaPrima();
        }

        function ingresarInventario(idMateriaPrima, nombre, stockActual) {
            document.getElementById('ingresar_id_materia_prima').value = idMateriaPrima;
            document.getElementById('ingresar_nombre_materia_prima').value = nombre;
            document.getElementById('display_nombre_materia_prima').textContent = nombre;
            document.getElementById('display_stock_actual').textContent = parseFloat(stockActual).toFixed(2);
            document.getElementById('ingresar_cantidad').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalIngresarInventario'));
            modal.show();
        }

        function confirmarIngresoInventario() {
            const idMateriaPrima = document.getElementById('ingresar_id_materia_prima').value;
            const nombre = document.getElementById('ingresar_nombre_materia_prima').value;
            const cantidad = parseFloat(document.getElementById('ingresar_cantidad').value);
            
            if (!cantidad || cantidad <= 0 || isNaN(cantidad)) {
                mostrarAlerta('Ingrese una cantidad válida mayor a 0', 'warning');
                return;
            }
            
            const stockActual = parseFloat(document.getElementById('display_stock_actual').textContent);
            if (cantidad > stockActual) {
                mostrarAlerta('La cantidad a ingresar no puede ser mayor al stock disponible en materia prima', 'warning');
                return;
            }
            
            const formData = {
                id_materia_prima: idMateriaPrima,
                cantidad: cantidad,
                descripcion: '',
                id_usuario: 1,
                creado_por: 'ADMIN'
            };
            
            const btnConfirmar = document.getElementById('btnConfirmarIngreso');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
            
            fetch('/sistema/public/index.php?route=compras&caso=ingresarInventario', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 200) {
                    mostrarAlerta(result.message, 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalIngresarInventario'));
                    modal.hide();
                    cargarMateriaPrima();
                } else {
                    mostrarAlerta(result.message || 'Error al ingresar al inventario', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión al procesar la solicitud', 'error');
            })
            .finally(() => {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = 'Ingresar al Inventario';
            });
        }

        function mostrarAlerta(mensaje, tipo = 'info') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            }[tipo] || 'alert-info';

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show alert-flotante`;
            alertDiv.innerHTML = `
                <i class="bi ${tipo === 'success' ? 'bi-check-circle' : tipo === 'error' ? 'bi-exclamation-circle' : tipo === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function editarMateriaPrima(idMateriaPrima) {
            window.location.href = `/sistema/public/editar-materia-prima?id=${idMateriaPrima}`;
        }

        function exportarPDF() {
            const filtros = {
                filtro_nombre: document.getElementById('filtro_nombre').value,
                filtro_estado: document.getElementById('filtro_estado').value
            };
            
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) queryParams.append(key, filtros[key]);
            });
            
            window.open(`/sistema/public/index.php?route=reporte-materia-prima-pdf&${queryParams.toString()}`, '_blank');
        }
    </script>
    
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>