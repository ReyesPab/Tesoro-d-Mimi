class GestionUsuarios {
    constructor() {
        this.tabla = null;
        this.init();
    }

    async init() {
        await this.cargarUsuarios();
        this.configurarEventos();
    }

    async cargarUsuarios() {
        try {
            console.log("🔍 Iniciando carga de usuarios...");
            
            const response = await fetch('index.php?route=user&caso=listar');
            console.log("📦 Respuesta HTTP:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            console.log("📊 Datos recibidos:", data);
            
            if (data.status === 200) {
                console.log("✅ Usuarios encontrados:", data.data.usuarios.length);
                console.log("📋 Primer usuario:", data.data.usuarios[0]);
                this.inicializarTabla(data.data.usuarios);
            } else {
                throw new Error(data.message || 'Error al cargar usuarios');
            }
        } catch (error) {
            console.error('❌ Error cargando usuarios:', error);
            this.mostrarError(error.message);
        }
    }

inicializarTabla(usuarios) {
    // Destruir tabla existente si hay una
    if (this.tabla) {
        this.tabla.destroy();
    }

    this.tabla = $('#tablaUsuarios').DataTable({
        data: usuarios,
        columns: [
            { 
                data: 'USUARIO',
                defaultContent: 'N/A',
                title: 'Usuario'
            },
            { 
                data: 'NOMBRE_USUARIO',
                defaultContent: 'N/A',
                title: 'Nombre de Usuario'  // Cambiado aquí
            },
            { 
                data: 'ROL',
                defaultContent: 'N/A',
                title: 'Rol'
            },
            { 
                data: 'CORREO_ELECTRONICO',
                defaultContent: 'N/A',
                title: 'Correo Electrónico',
                render: function(data) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'ESTADO_USUARIO',
                defaultContent: 'N/A',
                title: 'Estado',
                render: function(data) {
                    const estado = data || 'N/A';
                    const badgeClass = estado === 'Activo' ? 'bg-success' : 
                                     estado === 'Bloqueado' ? 'bg-danger' : 
                                     estado === 'Nuevo' ? 'bg-warning' : 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${estado}</span>`;
                }
            },
            { 
                data: 'FECHA_CREACION',
                defaultContent: 'N/A',
                title: 'Fecha de Creación'  // Cambiado aquí
            },
            { 
                data: 'FECHA_VENCIMIENTO',
                defaultContent: 'N/A',
                title: 'Fecha de Vencimiento'  // Cambiado aquí
            },
            {
                data: 'ID_USUARIO',
                title: 'Acciones',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">-</span>';
                    
                    return `
                        <div class="btn-group btn-group-sm" role="group" style="gap: 2px;">
                            <button type="button" class="btn btn-outline-primary border-0 p-1" 
                                    onclick="gestionUsuarios.resetPassword(${data})" 
                                    title="Resetear Contraseña">
                                <i class="bi bi-key" style="font-size: 0.8rem;"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning border-0 p-1" 
                                    onclick="gestionUsuarios.editarUsuario(${data})" 
                                    title="Editar Usuario">
                                <i class="bi bi-pencil" style="font-size: 0.8rem;"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger border-0 p-1" 
                                    onclick="gestionUsuarios.eliminarUsuario(${data}, '${row.USUARIO || ''}', '${(row.NOMBRE_USUARIO || '').replace(/'/g, "\\'")}')" 
                                    title="Eliminar Usuario">
                                <i class="bi bi-trash" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true,
        order: [[0, 'asc']],
        autoWidth: false,
        scrollX: true
    });
}

    mostrarError(mensaje) {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        
        loadingMessage.style.display = 'none';
        errorMessage.textContent = `Error: ${mensaje}`;
        errorMessage.style.display = 'block';
    }

    configurarEventos() {
        document.getElementById('btnResetPassword').addEventListener('click', () => this.confirmarResetPassword());
        document.getElementById('reset_autogenerar').addEventListener('change', (e) => this.toggleAutogenerarReset(e));
        document.getElementById('btnConfirmarEliminar').addEventListener('click', () => this.confirmarEliminarUsuario());
    }

    async resetPassword(ID_USUARIO) {
        try {
            const response = await fetch('index.php?route=user&caso=generar-password');
            const data = await response.json();
            
            if (data.status === 200) {
                document.getElementById('reset_id_usuario').value = ID_USUARIO;
                document.getElementById('reset_nueva_password').value = data.data.password;
                document.getElementById('reset_confirmar_password').value = data.data.password;
                
                const modal = new bootstrap.Modal(document.getElementById('modalResetPassword'));
                modal.show();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar contraseña');
        }
    }

    editarUsuario(ID_USUARIO) {
        window.location.href = '/sistema/public/crear-usuario?editar=' + ID_USUARIO;
    }

    eliminarUsuario(ID_USUARIO, usuario, nombre) {
        document.getElementById('eliminar_id_usuario').value = ID_USUARIO;
        document.getElementById('eliminar_nombre_usuario').textContent = `${usuario} - ${nombre}`;
        
        const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
        modal.show();
    }

    async confirmarEliminarUsuario() {
        const ID_USUARIO = document.getElementById('eliminar_id_usuario').value;
        
        try {
            // Buscar y eliminar la fila
            const datos = this.tabla.rows().data();
            let filaIndex = -1;
            
            datos.each((index, row) => {
                if (row.ID_USUARIO == ID_USUARIO) {
                    filaIndex = index;
                    return false;
                }
            });
            
            if (filaIndex !== -1) {
                this.tabla.row(filaIndex).remove().draw();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                modal.hide();
                
                alert('Usuario eliminado visualmente de la tabla.');
            } else {
                alert('No se pudo encontrar el usuario en la tabla.');
            }
        } catch (error) {
            console.error('Error eliminando usuario:', error);
            alert('Error al eliminar el usuario');
        }
    }

    async toggleAutogenerarReset(e) {
        const passwordInput = document.getElementById('reset_nueva_password');
        const confirmInput = document.getElementById('reset_confirmar_password');
        
        if (e.target.checked) {
            try {
                const response = await fetch('index.php?route=user&caso=generar-password');
                const data = await response.json();
                
                if (data.status === 200) {
                    passwordInput.value = data.data.password;
                    confirmInput.value = data.data.password;
                }
            } catch (error) {
                console.error('Error generando password:', error);
            }
        } else {
            passwordInput.value = '';
            confirmInput.value = '';
        }
    }

    async confirmarResetPassword() {
        const form = document.getElementById('formResetPassword');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        
        // Validaciones
        if (data.NUEVA_PASSWORD.length < 5 || data.NUEVA_PASSWORD.length > 10) {
            alert('La contraseña debe tener entre 5 y 10 caracteres');
            return;
        }
        
        if (/\s/.test(data.NUEVA_PASSWORD)) {
            alert('La contraseña no puede contener espacios');
            return;
        }
        
        if (data.NUEVA_PASSWORD !== document.getElementById('reset_confirmar_password').value) {
            alert('Las contraseñas no coinciden');
            return;
        }
        
        try {
            data.MODIFICADO_POR = 'ADMIN';
            
            const response = await fetch('index.php?route=user&caso=resetear-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.status === 200) {
                alert('Contraseña reseteada exitosamente');
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalResetPassword'));
                modal.hide();
                this.cargarUsuarios();
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error resetando password:', error);
            alert('Error de conexión');
        }
    }
}

// Instancia global
const gestionUsuarios = new GestionUsuarios();