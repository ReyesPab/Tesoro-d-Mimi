<?php
// src/Views/gestion-roles.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: /sistema/src/Views/login.php');
    exit();
}

// Verificar permisos si es necesario
// if (!tienePermiso('gestion_roles')) { ... }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles - Sistema</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        .table-actions {
            white-space: nowrap;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        .badge-activo {
            background-color: #28a745;
            color: white;
        }
        .badge-inactivo {
            background-color: #6c757d;
            color: white;
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .main-content {
            min-height: calc(100vh - 120px);
        }
        body {
            padding-top: 70px;
            background-color: #f4f6f8;
        }
    </style>
</head>
<body>
    <?php 
    // Incluir header y sidebar
    require_once __DIR__ . '/partials/header.php';
    require_once __DIR__ . '/partials/sidebar.php';
    ?>

    <main id="main" class="main main-content">
        <div class="container-fluid py-4">
            <div class="pagetitle">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2">Gestión de Roles</h1>
                    <button type="button" class="btn btn-primary" onclick="gestionRoles.mostrarModalCrear()">
                        <i class="bi bi-plus-circle"></i> Nuevo Rol
                    </button>
                </div>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/sistema/src/Views/dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Gestión de Roles</li>
                    </ol>
                </nav>
            </div>

            <!-- Alertas -->
            <div id="alertContainer"></div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>Lista de Roles del Sistema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaRoles" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Rol</th>
                                            <th>Descripción</th>
                                            <th>Fecha Creación</th>
                                            <th>Última Modificación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se cargan via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para crear/editar rol -->
    <div class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="modalRolLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRolLabel">Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRol">
                    <div class="modal-body">
                        <input type="hidden" id="id_rol" name="id_rol">
                        
                        <div class="mb-3">
                            <label for="rol" class="form-label">Nombre del Rol *</label>
                            <input type="text" class="form-control" id="rol" name="rol" 
                                   maxlength="50" required
                                   placeholder="Ingrese el nombre del rol"
                                   onblur="gestionRoles.verificarRolUnico()">
                            <div class="form-text">Máximo 50 caracteres. El nombre debe ser único en el sistema.</div>
                            <div class="invalid-feedback" id="error-rol"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" maxlength="255"
                                      placeholder="Descripción opcional del rol"></textarea>
                            <div class="form-text">Máximo 255 caracteres.</div>
                            <div class="invalid-feedback" id="error-descripcion"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarRol">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <span class="btn-text">Guardar Rol</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once 'partials/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    class GestionRoles {
        constructor() {
            this.tabla = null;
            this.init();
        }

        async init() {
            await this.cargarRoles();
            this.configurarEventos();
        }

        async cargarRoles() {
            try {
                console.log('🔍 Iniciando carga de roles...');
                const url = '/sistema/public/index.php?route=role&caso=listar';
                console.log('📡 URL de petición:', url);
                
                const response = await fetch(url);
                console.log('📊 Status de respuesta:', response.status);
                console.log('📊 OK:', response.ok);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('📦 Respuesta JSON completa:', data);
                
                if (data.status === 200) {
                    const roles = data.data.roles || data.data || [];
                    console.log('👥 Roles obtenidos:', roles);
                    console.log('🔢 Número de roles:', roles.length);
                    
                    if (roles.length > 0) {
                        console.log('📋 Primer rol:', roles[0]);
                        console.log('🗂️ Campos del primer rol:', Object.keys(roles[0]));
                    }
                    
                    this.inicializarTabla(roles);
                    this.mostrarAlerta('success', `Se cargaron ${roles.length} roles correctamente`);
                } else {
                    throw new Error(data.message || 'Error en la respuesta del servidor');
                }
            } catch (error) {
                console.error('💥 Error cargando roles:', error);
                this.mostrarAlerta('error', 'Error al cargar los roles: ' + error.message);
                
                // Mostrar tabla vacía con mensaje
                this.inicializarTabla([]);
            }
        }

        inicializarTabla(roles) {
            if (this.tabla) {
                this.tabla.destroy();
            }

            this.tabla = $('#tablaRoles').DataTable({
                data: roles,
                columns: [
                    { 
                        data: 'ID_ROL',
                        title: 'ID',
                        width: '5%',
                        visible: false,
                        searchable: true
                    },
                    { 
                        data: 'ROL',
                        title: 'Rol',
                        width: '25%'
                    },
                    { 
                        data: 'DESCRIPCION',
                        title: 'Descripción',
                        width: '35%',
                        render: function(data) {
                            return data || '<span class="text-muted">Sin descripción</span>';
                        }
                    },
                    { 
                        data: 'FECHA_CREACION',
                        title: 'Fecha Creación',
                        width: '15%',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">N/A</span>';
                            return new Date(data).toLocaleDateString('es-ES', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    { 
                        data: 'FECHA_MODIFICACION',
                        title: 'Última Modificación',
                        width: '15%',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">No modificado</span>';
                            return new Date(data).toLocaleDateString('es-ES', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'ID_ROL',
                        title: 'Acciones',
                        className: 'table-actions',
                        width: '10%',
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-warning btn-action" 
                                            onclick="gestionRoles.editarRol(${data})" 
                                            title="Editar Rol">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            `;
                        }.bind(this)
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[1, 'asc']],
                autoWidth: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }

        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        configurarEventos() {
            const form = document.getElementById('formRol');
            if (form) {
                form.addEventListener('submit', (e) => this.guardarRol(e));
            }

            const modalRol = document.getElementById('modalRol');
            if (modalRol) {
                modalRol.addEventListener('hidden.bs.modal', () => {
                    this.limpiarErrores();
                });
            }
        }

        mostrarModalCrear() {
            const modalLabel = document.getElementById('modalRolLabel');
            const form = document.getElementById('formRol');
            
            if (modalLabel && form) {
                modalLabel.textContent = 'Nuevo Rol';
                form.reset();
                document.getElementById('id_rol').value = '';
                this.limpiarErrores();
                
                const modal = new bootstrap.Modal(document.getElementById('modalRol'));
                modal.show();
                
                setTimeout(() => {
                    const rolInput = document.getElementById('rol');
                    if (rolInput) rolInput.focus();
                }, 500);
            }
        }

        async editarRol(idRol) {
            try {
                this.mostrarAlerta('info', 'Cargando información del rol...');
                
                const response = await fetch(`/sistema/public/index.php?route=role&caso=obtener&id_rol=${idRol}`);
                const data = await response.json();
                
                if (data.status === 200) {
                    const rol = data.data.rol || data.data;
                    
                    document.getElementById('modalRolLabel').textContent = 'Editar Rol';
                    document.getElementById('id_rol').value = rol.ID_ROL;
                    document.getElementById('rol').value = rol.ROL;
                    document.getElementById('descripcion').value = rol.DESCRIPCION || '';
                    this.limpiarErrores();
                    
                    const modal = new bootstrap.Modal(document.getElementById('modalRol'));
                    modal.show();
                    
                    this.mostrarAlerta('success', 'Rol cargado correctamente');
                } else {
                    throw new Error(data.message || 'Error al cargar el rol');
                }
            } catch (error) {
                console.error('Error cargando rol:', error);
                this.mostrarAlerta('error', 'Error al cargar el rol: ' + error.message);
            }
        }

        async verificarRolUnico() {
            const rolInput = document.getElementById('rol');
            const rol = rolInput.value.trim();
            const idRol = document.getElementById('id_rol').value;
            
            if (rol.length === 0) return;
            
            try {
                const payload = { rol: rol };
                if (idRol) payload.id_rol = idRol;
                
                const response = await fetch('/sistema/public/index.php?route=role&caso=verificar-rol', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();
                
                if (data.status === 400) {
                    this.mostrarErrorCampo('rol', 'Este rol ya existe en el sistema');
                } else {
                    this.limpiarErrorCampo('rol');
                }
            } catch (error) {
                console.error('Error validando rol:', error);
            }
        }

        async guardarRol(e) {
            e.preventDefault();
            
            if (!this.validarFormulario()) {
                return;
            }
            
            const btnSubmit = document.getElementById('btnGuardarRol');
            const spinner = btnSubmit.querySelector('.spinner-border');
            const btnText = btnSubmit.querySelector('.btn-text');
            const idRol = document.getElementById('id_rol').value;
            const esEdicion = !!idRol;
            
            btnSubmit.disabled = true;
            spinner.classList.remove('d-none');
            btnText.textContent = 'Guardando...';
            
            try {
                const formData = new FormData(document.getElementById('formRol'));
                const data = Object.fromEntries(formData);
                
                // Agregar datos de sesión desde PHP
                data.id_usuario = <?= $_SESSION['user_id'] ?? 1 ?>;
                if (esEdicion) {
                    data.modificado_por = '<?= $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN' ?>';
                } else {
                    data.creado_por = '<?= $_SESSION['user_name'] ?? $_SESSION['usuario_nombre'] ?? $_SESSION['user_usuario'] ?? 'ADMIN' ?>';
                }
                
                const endpoint = esEdicion ? 'actualizar' : 'crear';
                
                console.log("Enviando datos para " + endpoint + ":", data);
                
                const response = await fetch(`/sistema/public/index.php?route=role&caso=${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                console.log("Status de respuesta:", response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log("Resultado JSON:", result);
                
                if (result.status === 200 || result.status === 201) {
                    this.mostrarAlerta('success', result.message);
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalRol'));
                    modal.hide();
                    
                    await this.cargarRoles();
                } else {
                    this.mostrarAlerta('error', result.message || 'Error al guardar el rol');
                }
                
            } catch (error) {
                console.error('Error guardando rol:', error);
                this.mostrarAlerta('error', 'Error de conexión con el servidor: ' + error.message);
            } finally {
                btnSubmit.disabled = false;
                spinner.classList.add('d-none');
                btnText.textContent = 'Guardar Rol';
            }
        }

        validarFormulario() {
            let isValid = true;
            this.limpiarErrores();
            
            const rol = document.getElementById('rol').value.trim();
            if (rol.length === 0) {
                this.mostrarErrorCampo('rol', 'El nombre del rol es requerido');
                isValid = false;
            } else if (rol.length < 2) {
                this.mostrarErrorCampo('rol', 'El rol debe tener al menos 2 caracteres');
                isValid = false;
            } else if (rol.length > 50) {
                this.mostrarErrorCampo('rol', 'El rol no puede exceder 50 caracteres');
                isValid = false;
            }
            
            const descripcion = document.getElementById('descripcion').value;
            if (descripcion.length > 255) {
                this.mostrarErrorCampo('descripcion', 'La descripción no puede exceder 255 caracteres');
                isValid = false;
            }
            
            return isValid;
        }

        mostrarErrorCampo(campo, mensaje) {
            const errorElement = document.getElementById(`error-${campo}`);
            const inputElement = document.getElementById(campo);
            
            if (errorElement && inputElement) {
                errorElement.textContent = mensaje;
                inputElement.classList.add('is-invalid');
            }
        }

        limpiarErrorCampo(campo) {
            const errorElement = document.getElementById(`error-${campo}`);
            const inputElement = document.getElementById(campo);
            
            if (errorElement && inputElement) {
                errorElement.textContent = '';
                inputElement.classList.remove('is-invalid');
            }
        }

        limpiarErrores() {
            const inputs = document.querySelectorAll('.is-invalid');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            const feedbacks = document.querySelectorAll('.invalid-feedback');
            feedbacks.forEach(feedback => feedback.textContent = '');
        }

        mostrarAlerta(tipo, mensaje) {
            const alertasExistentes = document.querySelectorAll('#alertContainer .alert');
            alertasExistentes.forEach(alerta => alerta.remove());
            
            const alertClass = tipo === 'success' ? 'alert-success' : 
                              tipo === 'error' ? 'alert-danger' : 
                              tipo === 'warning' ? 'alert-warning' : 'alert-info';
            
            const icon = tipo === 'success' ? 'bi-check-circle' : 
                        tipo === 'error' ? 'bi-exclamation-triangle' : 
                        tipo === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="bi ${icon} me-2"></i> ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.getElementById('alertContainer').appendChild(alertDiv);
            
            if (tipo !== 'error') {
                setTimeout(() => {
                    if (alertDiv.parentElement) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }
    }

    const gestionRoles = new GestionRoles();

    function recargarTabla() {
        gestionRoles.cargarRoles();
    }

    function exportarRoles() {
        alert('Función de exportación en desarrollo...');
    }
    </script>
</body>
</html>