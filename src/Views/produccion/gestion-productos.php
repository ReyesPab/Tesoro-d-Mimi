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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión de Productos - Sistema de Gestión</title>
    
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

        /* Columnas específicas */
        .table td:nth-child(1) { /* NOMBRE */ 
            min-width: 150px;
            max-width: 200px;
        }

        .table td:nth-child(2) { /* DESCRIPCION */
            min-width: 180px;
            max-width: 250px;
        }

        .table td:nth-child(3) { /* PRECIO */
            min-width: 100px;
            max-width: 120px;
            text-align: right;
        }

        .table td:nth-child(4) { /* UNIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(5) { /* FECHA CREACION */
            min-width: 120px;
            max-width: 130px;
            white-space: nowrap;
        }

        .table td:nth-child(6) { /* ESTADO */
            min-width: 80px;
            max-width: 90px;
            text-align: center;
        }

        /* Columna de acciones */
        .table th:last-child,
        .table td:last-child {
            width: 100px !important;
            min-width: 100px !important;
            max-width: 100px !important;
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

        /* Badge colors */
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
                    <h1 class="h2 mb-0">Gestión de Productos</h1>
                    <div class="d-flex gap-2">
                        <!-- Botón Crear Producto -->
                        <a href="/sistema/public/crear-producto" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Crear Producto
                        </a>
                        
                        <!-- Botón Exportar PDF -->
                        <button class="btn btn-warning" onclick="exportarPDF()">
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
                        <div class="col-md-8">
                            <div class="bg-light p-3 rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label for="filtro_nombre" class="form-label">Nombre del Producto</label>
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
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarProductos()">
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
                        <p class="text-muted mt-2">Cargando productos...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Unidad</th>
                                    <th>Fecha Creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProductos">
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

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Productos
                                    </h6>
                                    <div id="resumenProductos">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cargarProductos();
            
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') cargarProductos();
            });
            
            document.getElementById('filtro_estado').addEventListener('change', cargarProductos);
        });

        function cargarProductos() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyProductos');
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
            
            fetch(`/sistema/public/index.php?route=produccion&caso=obtenerProductos&${queryParams.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data && data.data.length > 0) {
                    mostrarProductos(data.data);
                } else {
                    tbody.innerHTML = '';
                    sinResultados.style.display = 'block';
                    actualizarResumen([]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar los productos</td></tr>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarProductos(productos) {
            const tbody = document.getElementById('tbodyProductos');
            const sinResultados = document.getElementById('sinResultados');
            
            if (productos.length === 0) {
                tbody.innerHTML = '';
                sinResultados.style.display = 'block';
                actualizarResumen([]);
                return;
            }
            
            let html = '';
            productos.forEach(item => {
                html += `
    <tr>
        <td>
            <strong>${item.NOMBRE}</strong>
        </td>
        <td>${item.DESCRIPCION || '-'}</td>
        <td class="text-end price">
            L ${parseFloat(item.PRECIO).toFixed(2)}
        </td>
        <td class="text-center">
            <span class="badge bg-secondary">${item.UNIDAD || '-'}</span>
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
                <button type="button" class="btn btn-outline-info" onclick="editarProducto(${item.ID_PRODUCTO})" title="Editar">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
        </td>
    </tr>
`;
            });
            
            tbody.innerHTML = html;
            actualizarResumen(productos);
        }

        function actualizarResumen(productos) {
            if (productos.length === 0) {
                document.getElementById('totalProductos').textContent = '0';
                document.getElementById('totalActivos').textContent = '0';
                document.getElementById('totalInactivos').textContent = '0';
                return;
            }
            
            const totalActivos = productos.filter(item => item.ESTADO === 'ACTIVO').length;
            const totalInactivos = productos.filter(item => item.ESTADO === 'INACTIVO').length;
            
            document.getElementById('totalProductos').textContent = productos.length;
            document.getElementById('totalActivos').textContent = totalActivos;
            document.getElementById('totalInactivos').textContent = totalInactivos;
        }

        function getBadgeClass(estado) {
            return estado === 'ACTIVO' ? 'bg-estado-activo' : 'bg-estado-inactivo';
        }

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_estado').value = '';
            cargarProductos();
        }

        function editarProducto(idProducto) {
            window.location.href = `/sistema/public/editar-producto?id=${idProducto}`;
        }

        function cambiarEstado(idProducto, estadoActual) {
            const nuevoEstado = estadoActual === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
            const confirmacion = confirm(`¿Está seguro que desea ${nuevoEstado === 'ACTIVO' ? 'activar' : 'desactivar'} este producto?`);
            
            if (confirmacion) {
                // Aquí iría la llamada a la API para cambiar el estado
                mostrarAlerta(`Estado del producto cambiado a ${nuevoEstado}`, 'success');
                // Recargar la tabla después de cambiar el estado
                setTimeout(() => cargarProductos(), 1000);
            }
        }

        function mostrarAlerta(mensaje, tipo = 'info') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            }[tipo] || 'alert-info';

            // Crear alerta temporal
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show alert-flotante`;
            alertDiv.innerHTML = `
                <i class="bi ${tipo === 'success' ? 'bi-check-circle' : tipo === 'error' ? 'bi-exclamation-circle' : tipo === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-eliminar después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
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
            
            // Aquí iría la llamada al generador de PDF
            alert('Funcionalidad de exportar PDF - En desarrollo');
            // window.open(`/sistema/public/index.php?route=reporte-productos-pdf&${queryParams.toString()}`, '_blank');
        }
    </script>
    
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>