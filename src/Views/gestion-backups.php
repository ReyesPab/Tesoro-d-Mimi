<?php
session_start();

$title = 'Gestión de Backups - Tesoro D\' MIMI';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/partials/sidebar.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Gestión de Respaldos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/inicio">Inicio</a></li>
                <li class="breadcrumb-item active">Backups</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Respaldos del Sistema</h5>

                        <!-- Botones de acción -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button class="btn btn-primary" onclick="crearBackupAhora()" id="btnBackupAhora">
                                    <i class="bi bi-download"></i> Respaldar Ahora
                                </button>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalProgramarBackup" id="btnProgramarAuto">
                                    <i class="bi bi-clock"></i> Programar Automático
                                </button>
                                <a href="/sistema/public/restaurar-backup" class="btn btn-outline-warning">
                                    <i class="bi bi-arrow-clockwise"></i> Restaurar Backup
                                </a>

                            </div>

                        </div>

                        <!-- Estado del sistema -->
                        <div class="alert alert-info d-flex align-items-center" role="alert" id="estadoSistema">
                            <i class="bi bi-info-circle me-2"></i>
                            <div id="mensajeEstado">Cargando información de backups...</div>
                        </div>

                        <!-- Tabla de backups -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaBackups">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre Archivo</th>
                                        <th>Tipo</th>
                                        <th>Fecha</th>
                                        <th>Tamaño</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyBackups">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                            Cargando backups...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted" id="infoPaginacion">
                                Mostrando <span id="totalBackups">0</span> backups
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Modal Programar Backup -->
<div class="modal fade" id="modalProgramarBackup" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programar Backup Automático</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProgramarBackup">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Cada (días)</label>
                            <input type="number" class="form-control" name="FRECUENCIA" min="1" max="30" value="7" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora de ejecución</label>
                            <input type="time" class="form-control" name="HORA_EJECUCION" value="02:00" required>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        El backup automático se ejecutará en el horario programado.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="programarBackupAutomatico()">Programar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<!-- Incluir el archivo JavaScript externo -->
<script src="/sistema/src/Views/js/backups.js"></script>

<!-- Script de inicialización mínimo -->
<script>
// Variable global para el nombre de usuario (desde PHP)
const usuarioNombre = '<?php echo $_SESSION["usuario_nombre"] ?? "SISTEMA"; ?>';

// Función para crear backup inmediatamente
function crearBackupAhora() {
    if (window.backupManager) {
        window.backupManager.crearBackupManual();
    }
}

// Inicialización después de cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Página de gestión de backups cargada');
    
    // Verificar que las dependencias estén cargadas
    if (typeof BackupManager === 'undefined') {
        console.error('❌ BackupManager no está disponible');
        mostrarErrorGlobal('Error: No se pudo cargar el sistema de backups. Recargue la página.');
        return;
    }
    
    // Inicializar el manager
    window.backupManager = new BackupManager();
    console.log('✅ BackupManager inicializado correctamente');
});

function mostrarErrorGlobal(mensaje) {
    const estadoSistema = document.getElementById('estadoSistema');
    const mensajeEstado = document.getElementById('mensajeEstado');
    if (estadoSistema && mensajeEstado) {
        estadoSistema.className = 'alert alert-danger d-flex align-items-center';
        mensajeEstado.innerHTML = `<strong>❌ Error:</strong> ${mensaje}`;
    }
    
    // También mostrar alerta
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error del Sistema',
            text: mensaje,
            confirmButtonText: 'Recargar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
}

// Función auxiliar para recargar
function recargarBackups() {
    if (window.backupManager) {
        window.backupManager.cargarBackups();
    }
}

// Función para cargar más backups (si implementas paginación)
function cargarMasBackups() {
    // Implementar paginación si es necesario
    recargarBackups();
}
</script>