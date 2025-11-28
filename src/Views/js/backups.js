class BackupManager {
    constructor() {
        this.baseUrl = '/sistema/public/index.php?route=backup';
        this.init();
    }

    async init() {
        if (typeof bootstrap === 'undefined') {
            this.mostrarErrorGlobal('Bootstrap no está cargado');
            return;
        }
        
        if (typeof Swal === 'undefined') {
            this.mostrarErrorGlobal('SweetAlert2 no está cargado');
            return;
        }

        this.cargarBackups();
        this.cargarProximoBackup();
        this.configurarEventos();
    }

    async hacerPeticion(url, options = {}) {
        const config = {
            method: options.method || 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            credentials: 'include',
            ...options
        };

        if (config.body && typeof config.body !== 'string') {
            config.body = JSON.stringify(config.body);
        }

        try {
            const response = await fetch(url, config);
            const responseText = await response.text();

            if (responseText.trim().startsWith('<!DOCTYPE') || 
                responseText.trim().startsWith('<html') || 
                responseText.includes('<b>') || 
                responseText.includes('<br />') ||
                responseText.includes('Parse error') ||
                responseText.includes('Fatal error')) {
                
                const errorMatch = responseText.match(/<b>(.*?)<\/b>(.*?)<br\s*\/?>/i);
                const errorInfo = errorMatch ? 
                    `Error PHP: ${errorMatch[1]}${errorMatch[2]}` : 
                    'Error PHP no especificado';
                
                throw new Error(errorInfo);
            }

            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error(`Respuesta no es JSON válido: ${responseText.substring(0, 100)}`);
            }

            if (result && typeof result.status !== 'undefined') {
                if (result.status === '200' || result.status === 200) {
                    return result;
                } else {
                    if (result.message) {
                        throw new Error(result.message);
                    } else {
                        throw new Error(`Error ${result.status}: Operación falló`);
                    }
                }
            } else {
                throw new Error('Estructura de respuesta no reconocida');
            }

        } catch (error) {
            if (error.message.includes('Error PHP')) {
                this.mostrarError(`
                    <strong>Error del Servidor PHP</strong><br><br>
                    ${error.message}<br><br>
                    <strong>¿Qué hacer?</strong>
                    <ul>
                        <li>Verifique los logs de error de Apache/PHP</li>
                        <li>Revise la sintaxis de los archivos PHP</li>
                        <li>Confirme que todos los archivos incluidos existan</li>
                        <li>Verifique los permisos de archivos</li>
                    </ul>
                `);
            } else {
                this.mostrarError(`<strong>Error:</strong> ${error.message}`);
            }
            
            return null;
        }
    }

    configurarEventos() {
        setInterval(() => {
            this.cargarBackups();
        }, 30000);
    }

    async cargarBackups() {
        try {
            const url = `${this.baseUrl}&caso=obtener-backups`;
            const result = await this.hacerPeticion(url);
            
            if (!result) return;

            if (result.status === '200') {
                const backups = result.data?.backups || result.data || [];
                this.mostrarBackups(backups);
                this.actualizarEstado('success', `<strong>✅ Sistema cargado:</strong> ${backups.length} backups encontrados`);
            } else {
                this.mostrarError('Error al cargar backups: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            this.actualizarEstado('error', `<strong>❌ Error:</strong> ${error.message}`);
        }
    }

    async cargarProximoBackup() {
        try {
            const url = `${this.baseUrl}&caso=obtener-proximo-backup`;
            const result = await this.hacerPeticion(url);
            
            if (result && result.status === '200') {
                this.mostrarInfoProximoBackup(result.data);
            }
        } catch (error) {
            console.log('No se pudo cargar información del próximo backup:', error.message);
        }
    }

    mostrarInfoProximoBackup(data) {
        const estadoSistema = document.getElementById('estadoSistema');
        const mensajeEstado = document.getElementById('mensajeEstado');
        
        if (!estadoSistema || !mensajeEstado) return;

        if (data.proximo_backup) {
            const proximo = data.proximo_backup;
            const tiempoRestante = data.tiempo_restante;
            
            let html = `
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <strong>✅ Sistema Activo</strong> - Próximo backup automático: 
                        <span class="text-primary">${tiempoRestante}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-info" onclick="verDetallesProximoBackup()">
                        <i class="bi bi-info-circle"></i>
                    </button>
                </div>
            `;
            
            mensajeEstado.innerHTML = html;
            estadoSistema.className = 'alert alert-success d-flex align-items-center';
            
            window.proximoBackupData = data;
        } else {
            mensajeEstado.innerHTML = '<strong>✅ Sistema Activo</strong> - No hay backups automáticos programados';
            estadoSistema.className = 'alert alert-success d-flex align-items-center';
        }
    }

    mostrarBackups(backups) {
        const tbody = document.getElementById('tbodyBackups');
        if (!tbody) {
            console.error('No se encontró tbodyBackups');
            return;
        }
        
        tbody.innerHTML = '';

        if (!backups || backups.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i><br>
                        No hay backups registrados
                    </td>
                </tr>
            `;
            return;
        }

        backups.forEach(backup => {
            const fila = document.createElement('tr');
            const tipo = backup.TIPO_RESPALDO || backup.tipo;
            const estado = backup.ESTADO || backup.estado;
            const fecha = backup.FECHA_BACKUP || backup.fecha;
            const tamaño = backup.TAMAÑO_ARCHIVO || backup.tamaño;
            
            fila.innerHTML = `
                <td>${backup.ID_BACKUP || backup.id}</td>
                <td>${backup.NOMBRE_ARCHIVO || backup.nombre_archivo}</td>
                <td>
                    <span class="badge ${tipo === 'MANUAL' ? 'bg-primary' : 'bg-success'}">
                        ${tipo}
                    </span>
                </td>
                <td>${this.formatearFecha(fecha)}</td>
                <td>${this.formatearTamaño(tamaño)}</td>
                <td>
                    <span class="badge ${estado === 'EJECUTADO' ? 'bg-success' : 
                                      estado === 'PENDIENTE' ? 'bg-warning' : 'bg-danger'}">
                        ${estado}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="verDetallesBackup(${backup.ID_BACKUP || backup.id})" title="Ver detalles">
                            <i class="bi bi-eye"></i>
                        </button>
                        ${estado === 'EJECUTADO' ? `
                        <button class="btn btn-outline-success" onclick="descargarBackup(${backup.ID_BACKUP || backup.id})" title="Descargar">
                            <i class="bi bi-download"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            `;
            tbody.appendChild(fila);
        });
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

    formatearTamaño(bytes) {
        if (!bytes || bytes === 0) return 'N/A';
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
    }

    async crearBackupManual() {
        try {
            const url = `${this.baseUrl}&caso=crear-backup-manual`;
            const result = await this.hacerPeticion(url, {
                method: 'POST',
                body: {}
            });

            if (!result) return;

            if (result.status === '200') {
                this.mostrarExito('✅ Backup creado exitosamente');
                this.cargarBackups();
            } else {
                this.mostrarError('❌ Error al crear backup: ' + (result.message || 'Error desconocido'));
            }
        } catch (error) {
            this.mostrarError('❌ Error de conexión: ' + error.message);
        }
    }

async programarBackupAutomatico() {
    const form = document.getElementById('formProgramarBackup');
    if (!form) {
        this.mostrarError('No se encontró el formulario de programación');
        return;
    }

    // Validar formulario
    const formData = new FormData(form);
    const dias = formData.get('FRECUENCIA');
    const hora = formData.get('HORA_EJECUCION');
    
    if (!dias || dias < 1) {
        this.mostrarError('La frecuencia debe ser al menos 1 día');
        return;
    }
    
    if (!hora) {
        this.mostrarError('La hora de ejecución es requerida');
        return;
    }

    const datos = {
        FRECUENCIA: dias.toString(),
        HORA_EJECUCION: hora,
        DIAS_SEMANA: null // Puedes agregar selección de días si necesitas
    };

    try {
        const url = `${this.baseUrl}&caso=programar-backup-automatico`;
        const result = await this.hacerPeticion(url, {
            method: 'POST',
            body: datos
        });

        if (!result) return;

        if (result.status === '200') {
            this.mostrarExito('✅ Backup automático programado exitosamente');
            this.cerrarModal('modalProgramarBackup');
            this.cargarProximoBackup(); // Actualizar información del próximo backup
        } else {
            this.mostrarError('❌ Error al programar backup: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        this.mostrarError('❌ Error de conexión: ' + error.message);
    }
}

    cerrarModal(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            } else {
                new bootstrap.Modal(modalElement).hide();
            }
        }
    }

    actualizarEstado(tipo, mensaje) {
        const estadoSistema = document.getElementById('estadoSistema');
        const mensajeEstado = document.getElementById('mensajeEstado');
        if (estadoSistema && mensajeEstado) {
            mensajeEstado.innerHTML = mensaje;
            estadoSistema.className = `alert alert-${tipo} d-flex align-items-center`;
        }
    }

    mostrarExito(mensaje) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: mensaje,
            timer: 3000,
            showConfirmButton: false
        });
    }

    mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: mensaje,
            confirmButtonText: 'Entendido'
        });
    }

    mostrarErrorGlobal(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error del Sistema',
            html: mensaje,
            confirmButtonText: 'Recargar Página'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
}

function crearBackupAhora() {
    if (window.backupManager) {
        window.backupManager.crearBackupManual();
    } else {
        Swal.fire('Error', 'Sistema no inicializado correctamente', 'error');
    }
}

function programarBackupAutomatico() {
    if (window.backupManager) {
        window.backupManager.programarBackupAutomatico();
    } else {
        Swal.fire('Error', 'Sistema no inicializado correctamente', 'error');
    }
}

function verDetallesBackup(idBackup) {
    if (window.backupManager) {
        window.backupManager.cargarBackups().then(() => {
            Swal.fire({
                title: 'Detalles del Backup',
                html: `
                    <div class="text-start">
                        <p><strong>ID:</strong> ${idBackup}</p>
                        <p>Los detalles completos se mostrarán próximamente.</p>
                    </div>
                `,
                icon: 'info'
            });
        });
    }
}

function verDetallesProximoBackup() {
    const data = window.proximoBackupData;
    if (!data || !data.proximo_backup) {
        Swal.fire('Información', 'No hay información del próximo backup disponible', 'info');
        return;
    }

    const backup = data.proximo_backup;
    
    Swal.fire({
        title: '📅 Próximo Backup Automático',
        html: `
            <div class="text-start">
                <p><strong>Programación:</strong></p>
                <ul>
                    <li><strong>Frecuencia:</strong> ${backup.FRECUENCIA}</li>
                    <li><strong>Hora de ejecución:</strong> ${backup.HORA_EJECUCION}</li>
                    ${backup.DIAS_SEMANA ? `<li><strong>Días de la semana:</strong> ${backup.DIAS_SEMANA}</li>` : ''}
                </ul>
                <p><strong>Próxima ejecución:</strong><br>
                <span class="text-primary">${new Date(backup.PROXIMA_EJECUCION).toLocaleString('es-ES')}</span></p>
                <p><strong>Tiempo restante:</strong><br>
                <span class="text-success">${data.tiempo_restante}</span></p>
                <div class="alert alert-info mt-3">
                    <small>
                        <i class="bi bi-info-circle"></i>
                        El sistema creará automáticamente un backup en el horario programado.
                    </small>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Entendido'
    });
}

async function descargarBackup(idBackup) {
    try {
        const url = `/sistema/public/index.php?route=backup&caso=descargar-backup&id=${idBackup}`;
        const response = await fetch(url, {
            method: 'GET',
            credentials: 'include'
        });

        if (response.ok) {
            const blob = await response.blob();
            const urlDescarga = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = urlDescarga;
            a.download = `backup_${idBackup}_${new Date().toISOString().split('T')[0]}.sql`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(urlDescarga);
            Swal.fire('Éxito', 'Descarga iniciada', 'success');
        } else {
            Swal.fire('Error', 'Error al descargar el backup', 'error');
        }
    } catch (error) {
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    }
}

async function eliminarBackupsAntiguos() {
    const { value: dias } = await Swal.fire({
        title: 'Eliminar Backups Antiguos',
        input: 'number',
        inputLabel: 'Días de antigüedad',
        inputValue: 30,
        inputAttributes: {
            min: 1,
            max: 365,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value < 1) {
                return 'Debe ingresar un número válido de días';
            }
        }
    });

    if (dias && window.backupManager) {
        try {
            const url = `/sistema/public/index.php?route=backup&caso=eliminar-backups-antiguos`;
            const result = await window.backupManager.hacerPeticion(url, {
                method: 'POST',
                body: { dias: parseInt(dias) }
            });

            if (result && result.status === '200') {
                Swal.fire('Éxito', result.message, 'success');
                window.backupManager.cargarBackups();
            } else {
                Swal.fire('Error', 'Error al eliminar backups: ' + (result?.message || 'Error desconocido'), 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        }
    }
}

async function verEstadisticas() {
    if (window.backupManager) {
        try {
            const url = `/sistema/public/index.php?route=backup&caso=estadisticas`;
            const result = await window.backupManager.hacerPeticion(url);

            if (result && result.status === '200') {
                const stats = result.data;
                
                let html = `
                    <div class="row text-start">
                        <div class="col-md-6">
                            <h6>Resumen General</h6>
                            <ul>
                `;
                
                if (stats.totales) {
                    html += `
                        <li><strong>Total Backups:</strong> ${stats.totales.total_backups || 0}</li>
                        <li><strong>Exitosos:</strong> ${stats.totales.backups_exitosos || 0}</li>
                        <li><strong>Con Error:</strong> ${stats.totales.backups_error || 0}</li>
                    `;
                } else {
                    html += '<li>No hay datos disponibles</li>';
                }
                
                html += `
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Por Tipo</h6>
                            <ul>
                `;
                
                if (stats.por_tipo && stats.por_tipo.length > 0) {
                    stats.por_tipo.forEach(tipo => {
                        html += `<li><strong>${tipo.TIPO_RESPALDO}:</strong> ${tipo.total} (${tipo.exitosos} exitosos)</li>`;
                    });
                } else {
                    html += '<li>No hay datos por tipo</li>';
                }
                
                html += `
                            </ul>
                        </div>
                    </div>
                `;
                
                Swal.fire({
                    title: 'Estadísticas de Backups',
                    html: html,
                    width: 600,
                    icon: 'info'
                });
            } else {
                Swal.fire('Error', 'Error al obtener estadísticas', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        }
    }
}

function recargarBackups() {
    if (window.backupManager) {
        window.backupManager.cargarBackups();
        Swal.fire({
            icon: 'success',
            title: 'Recargado',
            text: 'Lista de backups actualizada',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

function cargarMasBackups() {
    recargarBackups();
}

document.addEventListener('DOMContentLoaded', function() {
    window.backupManager = new BackupManager();
});