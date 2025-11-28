class RestaurarManager {
    constructor() {
        this.baseUrl = '/sistema/public/index.php?route=backup';
        this.backupSeleccionado = null;
        this.init();
    }

    async init() {
        await this.cargarUltimosBackups();
    }

    async cargarUltimosBackups() {
        try {
            console.log('Cargando últimos backups para restauración...');
            
            const url = `${this.baseUrl}&caso=obtener-ultimos-backups`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Respuesta completa:', result);

            let backups = [];
            if (result.data && result.data.backups) {
                backups = result.data.backups;
            } else if (result.backups) {
                backups = result.backups;
            } else if (Array.isArray(result.data)) {
                backups = result.data;
            } else if (Array.isArray(result)) {
                backups = result;
            }

            console.log(`${backups.length} backups cargados para restauración`);
            this.mostrarBackups(backups);
            
        } catch (error) {
            console.error('Error cargando backups:', error);
            this.mostrarError('Error: ' + error.message);
            this.mostrarBackups([]);
        }
    }

    mostrarBackups(backups) {
        const tbody = document.getElementById('tbodyBackupsRestaurar');
        if (!tbody) {
            console.error('No se encontró tbodyBackupsRestaurar');
            return;
        }

        tbody.innerHTML = '';

        if (!backups || backups.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i><br>
                        No hay backups ejecutados disponibles para restaurar
                        <br><small class="text-warning">Los backups deben estar en estado "EJECUTADO"</small>
                    </td>
                </tr>
            `;
            return;
        }

        backups.forEach(backup => {
            const tipo = backup.TIPO_RESPALDO || backup.tipo;
            const estado = backup.ESTADO || backup.estado;
            
            if (estado !== 'EJECUTADO') {
                return;
            }
            
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>
                    <input type="radio" name="backupSeleccionado" value="${backup.ID_BACKUP || backup.id}" 
                           onchange="restaurarManager.seleccionarBackup(${backup.ID_BACKUP || backup.id}, '${backup.NOMBRE_ARCHIVO || backup.nombre_archivo}')">
                </td>
                <td>${backup.NOMBRE_ARCHIVO || backup.nombre_archivo}</td>
                <td>${this.formatearFecha(backup.FECHA_BACKUP || backup.fecha)}</td>
                <td>
                    <span class="badge ${tipo === 'MANUAL' ? 'bg-primary' : 'bg-success'}">
                        ${tipo}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info" onclick="descargarBackup(${backup.ID_BACKUP || backup.id})" title="Descargar">
                        <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="verDetallesBackupRestaurar(${backup.ID_BACKUP || backup.id})" title="Ver detalles">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    }

    seleccionarBackup(idBackup, nombreArchivo) {
        this.backupSeleccionado = { 
            id: idBackup, 
            nombre: nombreArchivo 
        };
        
        const btnRestaurar = document.getElementById('btnRestaurar');
        if (btnRestaurar) {
            btnRestaurar.disabled = false;
            console.log(`Backup seleccionado: ${nombreArchivo} (ID: ${idBackup})`);
        }
    }

    async confirmarRestauracion() {
        if (!this.backupSeleccionado) {
            this.mostrarError('Selecciona un backup primero');
            return;
        }

        try {
            const url = `${this.baseUrl}&caso=obtener-backup-por-id&id=${this.backupSeleccionado.id}`;
            const response = await fetch(url);
            const responseText = await response.text();
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Respuesta del servidor no es JSON válido');
            }
            
            if (result.status === '200') {
                const backup = result.data;
                if (backup.ESTADO !== 'EJECUTADO') {
                    this.mostrarError('Solo se pueden restaurar backups ejecutados');
                    return;
                }
            }
        } catch (error) {
            console.error('Error verificando backup:', error);
        }

        document.getElementById('nombreBackupSeleccionado').textContent = this.backupSeleccionado.nombre;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmarRestauracion'));
        modal.show();
    }

    async ejecutarRestauracion() {
        if (!this.backupSeleccionado) {
            this.mostrarError('No hay backup seleccionado');
            return;
        }

        try {
            await this.verificarYBloquearSistema();
            
            const confirmacion = await Swal.fire({
                title: '⚠️ Cierre Total del Sistema',
                html: `
                    <div class="text-start">
                        <p><strong>El sistema se cerrará completamente durante la restauración.</strong></p>
                        <div class="alert alert-warning">
                            <i class="bi bi-clock"></i>
                            <strong>Tiempo estimado:</strong> 20-30 minutos
                        </div>
                        <ul>
                            <li>Todas las sesiones serán terminadas</li>
                            <li>Los usuarios activos serán desconectados</li>
                            <li>No se podrá acceder al sistema hasta que termine</li>
                            <li>Serás redirigido al login cuando esté disponible</li>
                        </ul>
                        <p class="text-danger mt-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            ¿Continuar con la restauración?
                        </p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, Restaurar y Cerrar Sistema',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            });

            if (!confirmacion.isConfirmed) {
                return;
            }

            const url = `${this.baseUrl}&caso=restaurar-backup`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_backup: this.backupSeleccionado.id
                })
            });

            const responseText = await response.text();
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Respuesta del servidor no es JSON válido: ' + responseText);
            }

            console.log(`Iniciando restauración del backup: ${this.backupSeleccionado.id}`);

            if (result.status === '200') {
                this.mostrarExito(`
                    <h5>✅ Restauración Exitosa</h5>
                    <p>Base de datos restaurada desde:</p>
                    <p><strong>${result.data.backup_restaurado}</strong></p>
                    <p>Fecha del backup: ${result.data.fecha_backup}</p>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>El sistema se reiniciará automáticamente.</strong><br>
                        Serás redirigido a la página de inicio de sesión.
                    </div>
                `);
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarRestauracion'));
                if (modal) modal.hide();
                
                setTimeout(() => {
                    window.location.href = '/sistema/public/login';
                }, 5000);
                
            } else {
                this.mostrarError(result.message || 'Error al restaurar la base de datos');
            }
        } catch (error) {
            this.mostrarError(error.message);
        }
    }

    formatearFecha(fechaString) {
        if (!fechaString) return 'N/A';
        try {
            const fecha = new Date(fechaString);
            return fecha.toLocaleString('es-ES');
        } catch (e) {
            return fechaString;
        }
    }

    mostrarExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            html: mensaje,
            confirmButtonText: 'Aceptar'
        });
    }

    mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje,
            confirmButtonText: 'Entendido'
        });
    }

    async verificarYBloquearSistema() {
        try {
            const url = `${this.baseUrl}&caso=verificar-bloqueo-completo`;
            const response = await fetch(url);
            const responseText = await response.text();
            
            let status;
            try {
                status = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Respuesta de verificación no es JSON válido');
            }
            
            if (status.bloqueado) {
                throw new Error('El sistema ya está en proceso de restauración. Espere a que termine.');
            }
            
            return true;
        } catch (error) {
            console.error('Error verificando estado del sistema:', error);
            throw error;
        }
    }
}

let restaurarManager;

function confirmarRestauracion() {
    if (restaurarManager) {
        restaurarManager.confirmarRestauracion();
    }
}

function ejecutarRestauracion() {
    if (restaurarManager) {
        restaurarManager.ejecutarRestauracion();
    }
}

async function descargarBackup(idBackup) {
    try {
        const url = `/sistema/public/index.php?route=backup&caso=descargar-backup&id=${idBackup}`;
        window.open(url, '_blank');
    } catch (error) {
        console.error('Error al descargar:', error);
        Swal.fire('Error', 'Error al descargar el backup', 'error');
    }
}

function verDetallesBackupRestaurar(idBackup) {
    if (restaurarManager) {
        Swal.fire({
            title: 'Detalles del Backup',
            html: `
                <div class="text-start">
                    <p><strong>ID:</strong> ${idBackup}</p>
                    <p>Obteniendo información del backup...</p>
                </div>
            `,
            icon: 'info',
            showConfirmButton: true
        });
    }
}

async function subirBackup() {
    const archivoInput = document.getElementById('archivoBackup');
    
    if (!archivoInput || !archivoInput.files || !archivoInput.files[0]) {
        Swal.fire('Error', 'Selecciona un archivo .sql primero', 'error');
        return;
    }

    const archivo = archivoInput.files[0];
    
    if (!archivo.name.toLowerCase().endsWith('.sql')) {
        Swal.fire('Error', 'Solo se permiten archivos .sql', 'error');
        return;
    }

    if (archivo.size > 100 * 1024 * 1024) {
        Swal.fire('Error', 'El archivo es demasiado grande. Máximo 100MB', 'error');
        return;
    }

    const confirmacion = await Swal.fire({
        title: '¿Subir y Restaurar Backup?',
        html: `
            <div class="text-start">
                <p><strong>Archivo:</strong> ${archivo.name}</p>
                <p><strong>Tamaño:</strong> ${(archivo.size / 1024 / 1024).toFixed(2)} MB</p>
                <div class="alert alert-warning mt-2">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Advertencia:</strong> Esto restaurará la base de datos completa
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Subir y Restaurar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    });

    if (!confirmacion.isConfirmed) {
        return;
    }

    const formData = new FormData();
    formData.append('archivo_backup', archivo);

    try {
        Swal.fire({
            title: 'Subiendo Backup...',
            text: 'Por favor espera, esto puede tomar varios minutos',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const url = `/sistema/public/index.php?route=backup&caso=subir-backup`;
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        const responseText = await response.text();
        let result;
        
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            throw new Error('Respuesta del servidor no es JSON válido');
        }

        if (result.status === '200') {
            Swal.fire({
                icon: 'success',
                title: '✅ Backup Subido Exitosamente',
                html: `
                    <div class="text-start">
                        <p>El backup se ha subido y registrado correctamente.</p>
                        <p><strong>ID:</strong> ${result.data.id_backup}</p>
                        <p><strong>Archivo:</strong> ${result.data.archivo_original}</p>
                        <div class="alert alert-info mt-2">
                            ¿Deseas restaurar este backup ahora?
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Sí, Restaurar Ahora',
                cancelButtonText: 'No, Solo Subir'
            }).then((restoreResult) => {
                if (restoreResult.isConfirmed) {
                    restaurarManager.seleccionarBackup(
                        result.data.id_backup, 
                        result.data.archivo_original
                    );
                    setTimeout(() => {
                        restaurarManager.confirmarRestauracion();
                    }, 500);
                } else {
                    restaurarManager.cargarUltimosBackups();
                }
            });

            archivoInput.value = '';

        } else {
            throw new Error(result.message || 'Error al subir el backup');
        }

    } catch (error) {
        console.error('Error subiendo backup:', error);
        Swal.fire('Error', error.message, 'error');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando RestaurarManager...');
    restaurarManager = new RestaurarManager();
});

async function verificarYBloquearSistema() {
    try {
        const url = `${this.baseUrl}&caso=verificar-bloqueo-completo`;
        const response = await fetch(url);
        const status = await response.json();
        
        if (status.bloqueado) {
            throw new Error('El sistema ya está en proceso de restauración. Espere a que termine.');
        }
        
        return true;
    } catch (error) {
        console.error('Error verificando estado del sistema:', error);
        throw error;
    }
}