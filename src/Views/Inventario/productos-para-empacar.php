<?php
// ========== VERIFICACIÓN DE PERMISOS ==========
require_once __DIR__ . '/../../config/SessionHelper.php';
require_once __DIR__ . '/../../config/PermisosHelper.php';
use App\config\SessionHelper;
use App\config\PermisosHelper;

PermisosHelper::requirePermission('GESTION_INVENTARIO_PRODUCTOS', 'CONSULTAR');
$puedeEditar = PermisosHelper::checkPermission('GESTION_INVENTARIO_PRODUCTOS', 'ACTUALIZAR');
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Productos Para Empacar</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Controles de ordenamiento y filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="ordenarPor" class="form-label">Ordenar por:</label>
                        <select id="ordenarPor" class="form-select" style="max-width: 200px;">
                            <option value="nombre">Nombre (A-Z)</option>
                            <option value="cantidad">Cantidad (Mayor a menor)</option>
                            <option value="estado">Estado</option>
                            <option value="fecha">Última Actualización</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <button id="btnExportarPDF" class="btn btn-danger me-2">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                        <button id="btnRefrescar" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Refrescar
                        </button>
                    </div>
                </div>

                <!-- Filtros adicionales -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="CRITICO">Crítico</option>
                            <option value="BAJO">Bajo</option>
                            <option value="NORMAL">Normal</option>
                            <option value="EXCESO">Exceso</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroBusqueda" class="form-label">Buscar:</label>
                        <input type="text" id="filtroBusqueda" class="form-control" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltros" class="btn btn-primary me-2">Aplicar Filtros</button>
                        <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">Limpiar</button>
                    </div>
                </div>

                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando productos para empacar...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar los productos. Verifica la consola para más detalles.
