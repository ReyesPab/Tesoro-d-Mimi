// sistema/src/Views/js/systemMonitor.js
class SystemMonitor {
    constructor() {
        this.checkInterval = null;
        this.baseUrl = '/sistema/public/index.php?route=backup';
        this.isMonitoring = false;
        this.warningShown = false;
    }

    startMonitoring() {
        if (this.isMonitoring) return;
        
        this.isMonitoring = true;
        console.log('🔍 SystemMonitor iniciado');
        
        // Verificar cada 25 segundos
        this.checkInterval = setInterval(() => {
            this.checkSystemStatus();
        }, 25000);
        
        // Verificar inmediatamente al cargar
        setTimeout(() => this.checkSystemStatus(), 1000);
    }

    stopMonitoring() {
        this.isMonitoring = false;
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
        this.removeMaintenanceWarning();
        console.log('🔍 SystemMonitor detenido');
    }

    async checkSystemStatus() {
        try {
            const url = `${this.baseUrl}&caso=verificar-estado-sistema`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const status = await response.json();
            
            if (status.bloqueado && !this.warningShown) {
                console.log('⚠️ Sistema en mantenimiento detectado');
                this.showMaintenanceWarning(status.data);
            } else if (!status.bloqueado && this.warningShown) {
                console.log('✅ Sistema disponible');
                this.removeMaintenanceWarning();
            }
        } catch (error) {
            console.log('🔍 Monitor: Error verificando estado:', error);
        }
    }

    showMaintenanceWarning(lockData) {
        this.warningShown = true;
        this.removeMaintenanceWarning();
        
        const warning = document.createElement('div');
        warning.id = 'maintenance-warning';
        warning.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-0 w-100 rounded-0 border-0';
        warning.style.zIndex = '9999';
        warning.style.borderBottom = '3px solid #ffc107';
        
        const inicio = lockData?.inicio ? new Date(lockData.inicio).toLocaleString() : 'Reciente';
        const usuario = lockData?.usuario || 'Sistema';
        
        warning.innerHTML = `
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-tools fs-5 me-3"></i>
                            <div>
                                <strong class="d-block">Mantenimiento Programado del Sistema</strong>
                                <small class="d-block">
                                    Inicio: ${inicio} | Responsable: ${usuario} | 
                                    El sistema se reiniciará para mantenimiento
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.prepend(warning);
        
        // Deshabilitar funcionalidades críticas
        this.disableCriticalFunctions();
        
        // Auto-remover después de 20 segundos (para no molestar)
        setTimeout(() => {
            this.removeMaintenanceWarning();
        }, 20000);
    }

    removeMaintenanceWarning() {
        this.warningShown = false;
        const existingWarning = document.getElementById('maintenance-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        this.enableCriticalFunctions();
    }

    disableCriticalFunctions() {
        // Deshabilitar botones críticos si existen
        const criticalButtons = [
            'btnBackupAhora', 'btnProgramarAuto', 'btnRestaurar',
            'btnEliminar', 'btnActualizar', 'btnGuardar'
        ];
        
        criticalButtons.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn && !btn.disabled) {
                btn.setAttribute('data-original-disabled', btn.disabled);
                btn.disabled = true;
                btn.title = '⏳ Sistema en mantenimiento - No disponible temporalmente';
                btn.classList.add('pe-none');
            }
        });
    }

    enableCriticalFunctions() {
        // Re-habilitar botones críticos
        const criticalButtons = [
            'btnBackupAhora', 'btnProgramarAuto', 'btnRestaurar',
            'btnEliminar', 'btnActualizar', 'btnGuardar'
        ];
        
        criticalButtons.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                const originalState = btn.getAttribute('data-original-disabled');
                btn.disabled = originalState === 'true';
                btn.title = '';
                btn.classList.remove('pe-none');
                btn.removeAttribute('data-original-disabled');
            }
        });
    }
}

// Inicialización automática si está en el contexto global
if (typeof window !== 'undefined') {
    window.SystemMonitor = SystemMonitor;
    
    // Auto-iniciar en páginas relevantes
    document.addEventListener('DOMContentLoaded', function() {
        // Iniciar monitor en páginas de backup y gestión
        if (document.querySelector('[id*="backup"], [id*="Backup"], #btnRestaurar, #btnBackupAhora')) {
            window.systemMonitor = new SystemMonitor();
            window.systemMonitor.startMonitoring();
        }
    });
}