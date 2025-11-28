<?php
session_start();

$title = 'Restaurar Backup - Tesoro D\' MIMI';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Restaurar Base de Datos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/inicio">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/gestion-backups">Backups</a></li>
                <li class="breadcrumb-item active">Restaurar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Últimos Backups Disponibles</h5>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>ADVERTENCIA:</strong> La restauración sobreescribirá toda la base de datos actual.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="tablaBackupsRestaurar">
                                <thead>
                                    <tr>
                                        <th>Seleccionar</th>
                                        <th>Nombre Archivo</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyBackupsRestaurar">
                                    <tr>
                                        <td colspan="5" class="text-center">Cargando backups...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-danger" onclick="confirmarRestauracion()" id="btnRestaurar" disabled>
                                <i class="bi bi-arrow-clockwise"></i> Restaurar Base de Datos
                            </button>
                            <a href="/sistema/public/gestion-backups" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Subir Backup</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Seleccionar archivo .sql</label>
                            <input type="file" class="form-control" id="archivoBackup" accept=".sql">
                        </div>
                        
                        <button class="btn btn-outline-primary" onclick="subirBackup()">
                            <i class="bi bi-upload"></i> Subir y Restaurar
                        </button>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Información</h5>
                        <div class="alert alert-info">
                            <strong>Backups recomendados:</strong>
                            <ul class="mt-2 mb-0">
                                <li>Selecciona el backup más reciente</li>
                                <li>Verifica la fecha del backup</li>
                                <li>Realiza un backup actual antes de restaurar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal Confirmación -->
<div class="modal fade" id="modalConfirmarRestauracion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Restauración
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>¡ACCIÓN IRREVERSIBLE!</strong><br>
                    Esta acción sobreescribirá TODA la base de datos actual.
                </div>
                <p><strong>Backup seleccionado:</strong> <span id="nombreBackupSeleccionado"></span></p>
                <p>¿Está absolutamente seguro de continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="ejecutarRestauracion()">Restaurar Ahora</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<script src="/sistema/src/Views/js/restaurar-backup.js"></script>