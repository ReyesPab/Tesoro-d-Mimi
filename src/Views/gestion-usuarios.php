<?php 
// ========== GESTIÓN DE SESIÓN OPTIMIZADA ==========
require_once __DIR__ . '/../config/SessionHelper.php';
require_once __DIR__ . '/../config/PermisosHelper.php';
 
require_once __DIR__ . '/../Views/partials/header.php'; 
require_once __DIR__ . '/../Views/partials/sidebar.php'; 
use App\config\SessionHelper;
use App\config\PermisosHelper; 
use App\models\permisosModel;
// Iniciar sesión de forma segura
SessionHelper::startSession();

// Verificar autenticación
if (!SessionHelper::isLoggedIn()) {
    echo "<script>
        alert('Sesión no encontrada. Serás redirigido al login.');
        window.location.href = '/sistema/public/login';
    </script>";
    exit;
}

// Obtener datos del usuario
$nombre_usuario = SessionHelper::getUserFullName();
$id_usuario = SessionHelper::getUserId();

// ========== VERIFICACIÓN DE PERMISOS CORREGIDA ==========
// Obtener el ID del usuario de la sesión
$userId = SessionHelper::getUserId();

// Verificar permisos específicos para cada acción (usando el modelo directamente como en el ejemplo)
$permisoCrearUsuario = permisosModel::verificarPermiso($userId, 'CREAR_USUARIO', 'CONSULTAR');
$permisoResetearContrasena = permisosModel::verificarPermiso($userId, 'GESTION_USUARIOS', 'EDITAR');
$permisoEditarUsuario = permisosModel::verificarPermiso($userId, 'GESTION_USUARIOS', 'EDITAR');
$permisoBloquearUsuario = permisosModel::verificarPermiso($userId, 'GESTION_USUARIOS', 'ELIMINAR');

// Debug de permisos
error_log("🔐 PERMISOS USUARIO $id_usuario - Crear: " . ($permisoCrearUsuario ? 'SI' : 'NO') . 
          ", Resetear: " . ($permisoResetearContrasena ? 'SI' : 'NO') . 
          ", Editar: " . ($permisoEditarUsuario ? 'SI' : 'NO') . 
          ", Bloquear: " . ($permisoBloquearUsuario ? 'SI' : 'NO'));
?>

<main id="main" class="main">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">Gestión de Usuarios</h1>
                <div class="d-flex gap-2">
                    <!-- Botón Crear Usuario - Solo si tiene permiso -->
                    <?php if ($permisoCrearUsuario): ?>
                    <a href="/sistema/public/crear-usuario" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Crear Usuario
                    </a>
                    <?php endif; ?>
                    
                    <!-- Botón Exportar PDF -->
                    <button id="btnExportarPDF" class="btn btn-danger">
                        <i class="bi bi-file-pdf"></i> Exportar PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-body">
                <!-- Filtros Centrados -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-light p-3 rounded">
                            <div class="row g-3 align-items-end justify-content-center">
                                <div class="col-md-4">
                                    <label for="ordenarPor" class="form-label">Ordenar por:</label>
                                    <select id="ordenarPor" class="form-select">
                                        <option value="0">Usuario (A-Z)</option>
                                        <option value="1">Nombre (A-Z)</option>
                                        <option value="6">Fecha Creación (Más reciente)</option>
                                        <option value="7">Fecha Vencimiento (Próximo a vencer)</option>
                                        <option value="4">Estado</option>
                                        <option value="3">Correo Electrónico</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button id="btnRefrescar" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-arrow-clockwise"></i> Refrescar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading y Mensajes -->
                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando usuarios...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar los usuarios. Verifica la consola para más detalles.
                </div>
                
                <!-- Tabla con estilo del ejemplo -->
                <div class="table-responsive">
                    <table id="tablaUsuarios" class="table table-hover" style="display: none;">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre de Usuario</th>
                                <th>Rol</th>
                                <th>Correo Electrónico</th>
                                <th>Estado</th>
                                <th>Fecha de Creación</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- No Results -->
                <div class="text-center mt-4" id="sinResultados" style="display: none;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No se encontraron usuarios registrados en el sistema.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
<script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
<script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<script>
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
                    throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
                }
                
                const text = await response.text();
                console.log("📄 Respuesta cruda:", text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error("❌ Error parseando JSON:", e);
                    throw new Error("Respuesta no es JSON válido");
                }
                
                console.log("📊 Datos JSON recibidos:", data);
                
                // Verificar estructura de datos
                if (data && data.status === 200 && data.data && data.data.usuarios) {
                    console.log("👥 Usuarios encontrados:", data.data.usuarios.length);
                    console.log("👤 Primer usuario:", data.data.usuarios[0]);
                    // Filtrar usuarios eliminados previamente en la vista (localStorage)
                    const deleted = this.getDeletedIds();
                    const usuariosFiltrados = data.data.usuarios.filter(u => !deleted.includes(String(u.ID_USUARIO)));
                    this.mostrarUsuarios(usuariosFiltrados);
                } else {
                    console.error("❌ Estructura de datos inesperada:", data);
                    throw new Error("Estructura de respuesta inesperada");
                }
                
            } catch (error) {
                console.error('❌ Error cargando usuarios:', error);
                this.mostrarError(error.message);
            }
        }

        // --- Helpers para persistencia local de eliminaciones en la vista ---
        getDeletedIds() {
            try {
                const raw = localStorage.getItem('usuarios_eliminados');
                if (!raw) return [];
                const parsed = JSON.parse(raw);
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                console.error('Error leyendo usuarios_eliminados desde localStorage', e);
                return [];
            }
        }

        saveDeletedId(id) {
            try {
                const arr = this.getDeletedIds();
                const sid = String(id);
                if (!arr.includes(sid)) {
                    arr.push(sid);
                    localStorage.setItem('usuarios_eliminados', JSON.stringify(arr));
                }
            } catch (e) {
                console.error('Error guardando usuario eliminado en localStorage', e);
            }
        }

        mostrarError(mensaje) {
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            loadingMessage.style.display = 'none';
            errorMessage.textContent = `Error: ${mensaje}`;
            errorMessage.style.display = 'block';
        }

        mostrarUsuarios(usuarios) {
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            const tabla = document.getElementById('tablaUsuarios');

            // Ocultar mensajes
            loadingMessage.style.display = 'none';
            errorMessage.style.display = 'none';

            if (!usuarios || usuarios.length === 0) {
                console.log("📭 No hay usuarios para mostrar");
                errorMessage.textContent = "No hay usuarios registrados en el sistema";
                errorMessage.style.display = 'block';
                return;
            }

            console.log("📋 Mostrando", usuarios.length, "usuarios en la tabla");

            // Mostrar tabla
            tabla.style.display = 'table';

            // Limpiar tabla existente
            if (this.tabla) {
                this.tabla.destroy();
            }

            // Inicializar DataTable
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
                        title: 'Nombre de Usuario'
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
                        title: 'Fecha de Creación'
                    },
                    { 
                        data: 'FECHA_VENCIMIENTO',
                        defaultContent: 'N/A',
                        title: 'Fecha de Vencimiento'
                    },
                    {
    data: 'ID_USUARIO',
    title: 'Acciones',
    render: function(data, type, row) {
        if (!data) return '<span class="text-muted">Sin acciones</span>';
        
        let botones = '<div class="btn-group btn-group-sm" role="group">';
        
        // Botón Resetear Contraseña
        <?php if ($permisoResetearContrasena): ?>
        botones += `<button type="button" class="btn btn-outline-primary" onclick="gestionUsuarios.resetPassword(${data})" title="Resetear Contraseña">
                        <i class="bi bi-key"></i>
                    </button>`;
        <?php endif; ?>
        
        // Botón Editar Usuario
        <?php if ($permisoEditarUsuario): ?>
        botones += `<button type="button" class="btn btn-outline-warning" onclick="gestionUsuarios.editarUsuario(${data})" title="Editar Usuario">
                        <i class="bi bi-pencil"></i>
                    </button>`;
        <?php endif; ?>
        
        // Botón Bloquear Usuario
        <?php if ($permisoBloquearUsuario): ?>
        botones += `<button type="button" class="btn btn-outline-danger" onclick="gestionUsuarios.eliminarUsuario(${data}, '${row.USUARIO || ''}', '${(row.NOMBRE_USUARIO || '').replace(/'/g, "\\'")}')" title="Bloquear Usuario">
                        <i class="bi bi-person-x"></i>
                    </button>`;
        <?php endif; ?>
        
        botones += '</div>';
        
        // Si no tiene ningún permiso
        <?php if (!$permisoResetearContrasena && !$permisoEditarUsuario && !$permisoBloquearUsuario): ?>
        return '<span class="text-muted">Sin acciones permitidas</span>';
        <?php else: ?>
        return botones;
        <?php endif; ?>
    }
}
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']], // Orden inicial por usuario
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                initComplete: function() {
                    // Restaurar ordenamiento guardado si existe
                    const ordenGuardado = localStorage.getItem('ordenamientoUsuarios');
                    if (ordenGuardado) {
                        const [columna, direccion] = JSON.parse(ordenGuardado);
                        this.order([columna, direccion]).draw();
                        
                        // Actualizar select
                        document.getElementById('ordenarPor').value = columna;
                    }
                },
                drawCallback: function() {
                    // Guardar preferencia de ordenamiento
                    const order = this.order();
                    if (order.length > 0) {
                        localStorage.setItem('ordenamientoUsuarios', JSON.stringify(order[0]));
                    }
                }
            });

            console.log("✅ Tabla inicializada correctamente");
        }

        configurarEventos() {
            document.getElementById('btnResetPassword').addEventListener('click', () => this.confirmarResetPassword());
            document.getElementById('reset_autogenerar').addEventListener('change', (e) => this.toggleAutogenerarReset(e));
            document.getElementById('btnConfirmarEliminar').addEventListener('click', () => this.confirmarEliminarUsuario());
            
            // Nuevos eventos para ordenamiento
            document.getElementById('ordenarPor').addEventListener('change', (e) => this.ordenarTabla(e.target.value));
            document.getElementById('btnRefrescar').addEventListener('click', () => this.recargarUsuarios());
            
            // Evento para exportar PDF
            document.getElementById('btnExportarPDF').addEventListener('click', () => this.exportarPDF());
        }

        async exportarPDF() {
            try {
                const btn = document.getElementById('btnExportarPDF');
                const originalText = btn.innerHTML;
                
                // Mostrar loading
                btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';
                btn.disabled = true;
                
                const response = await fetch('index.php?route=user&caso=exportar-pdf');
                const result = await response.json();
                
                if (result.status === 200 && result.data && result.data.usuarios) {
                    this.generarPDF(result.data.usuarios);
                } else {
                    throw new Error(result.message || 'Error al obtener datos para exportar');
                }
                
            } catch (error) {
                console.error('❌ Error exportando PDF:', error);
                alert('Error al exportar PDF: ' + error.message);
            } finally {
                // Restaurar botón
                const btn = document.getElementById('btnExportarPDF');
                btn.innerHTML = '<i class="bi bi-file-pdf"></i> Exportar PDF';
                btn.disabled = false;
            }
        }

        generarPDF(usuarios) {
    // Crear contenido HTML para el PDF
    const contenidoHTML = this.crearContenidoPDF(usuarios);
    
    // Crear elemento temporal
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    // Configuración para html2pdf
    const opt = {
        margin: [10, 10, 10, 10],
        filename: `reporte_usuarios_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: true
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };
    
    // Generar y descargar PDF
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            console.log('PDF generado y descargado exitosamente');
        })
        .catch(error => {
            console.error('Error generando PDF:', error);
            // Fallback: abrir en nueva ventana para imprimir
            this.generarPDFFallback(usuarios);
        });
}

crearContenidoPDF(usuarios) {
    // Formatear fecha actual
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Usuarios</title>
        <style>
            body { 
                font-family: 'Arial', sans-serif; 
                margin: 15px; 
                font-size: 12px; 
                color: #333;
            }
            .header { 
                text-align: center; 
                margin-bottom: 20px; 
                border-bottom: 2px solid #333; 
                padding-bottom: 10px; 
            }
            .title { 
                font-size: 18px; 
                font-weight: bold; 
                color: #2c3e50; 
                margin-bottom: 5px;
            }
            .subtitle { 
                font-size: 12px; 
                color: #7f8c8d; 
                margin-bottom: 5px; 
            }
            .fecha { 
                text-align: right; 
                margin-bottom: 15px; 
                font-size: 10px; 
                color: #666; 
            }
            .resumen { 
                margin-bottom: 15px; 
                font-size: 11px;
                padding: 8px;
                background-color: #f8f9fa;
                border-left: 4px solid #3498db;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px; 
                font-size: 9px;
                page-break-inside: auto;
            }
            th { 
                background-color: #34495e; 
                color: white;
                border: 1px solid #2c3e50; 
                padding: 8px; 
                text-align: left; 
                font-weight: bold;
                font-size: 10px;
            }
            td { 
                border: 1px solid #bdc3c7; 
                padding: 6px; 
                vertical-align: top;
            }
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            .estado-activo { 
                background-color: #27ae60; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-bloqueado { 
                background-color: #e74c3c; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-nuevo { 
                background-color: #f39c12; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-inactivo { 
                background-color: #95a5a6; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .footer { 
                margin-top: 20px; 
                text-align: center; 
                font-size: 9px; 
                color: #7f8c8d; 
                border-top: 1px solid #bdc3c7; 
                padding-top: 10px; 
            }
            .total { 
                font-weight: bold; 
                margin-top: 8px; 
                color: #2c3e50;
            }
            @media print {
                body { margin: 0; }
                .header { margin-top: 20px; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">REPORTE DE USUARIOS DEL SISTEMA</div>
            <div class="subtitle">Sistema de Gestión de Usuarios</div>
        </div>
        
        <div class="fecha">
            Generado el: ${fechaActual}
        </div>
        
        <div class="resumen">
            <strong>Total de usuarios registrados: ${usuarios.length}</strong>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="12%">Usuario</th>
                    <th width="18%">Nombre Completo</th>
                    <th width="12%">Rol</th>
                    <th width="15%">Correo Electrónico</th>
                    <th width="10%">Estado</th>
                    <th width="10%">Fecha Creación</th>
                    <th width="10%">Fecha Vencimiento</th>
                    <th width="8%">Última Conexión</th>
                </tr>
            </thead>
            <tbody>
                ${usuarios.map((usuario, index) => {
                    const estadoClass = usuario.ESTADO_USUARIO === 'Activo' ? 'estado-activo' : 
                                       usuario.ESTADO_USUARIO === 'Bloqueado' ? 'estado-bloqueado' : 
                                       usuario.ESTADO_USUARIO === 'Nuevo' ? 'estado-nuevo' : 'estado-inactivo';
                    
                    return `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${usuario.USUARIO || 'N/A'}</strong></td>
                        <td>${usuario.NOMBRE_USUARIO || 'N/A'}</td>
                        <td>${usuario.ROL || 'N/A'}</td>
                        <td>${usuario.CORREO_ELECTRONICO || 'N/A'}</td>
                        <td><span class="${estadoClass}">${usuario.ESTADO_USUARIO || 'N/A'}</span></td>
                        <td>${this.formatearFecha(usuario.FECHA_CREACION)}</td>
                        <td>${this.formatearFecha(usuario.FECHA_VENCIMIENTO)}</td>
                        <td>${this.formatearFecha(usuario.FECHA_ULTIMA_CONEXION)}</td>
                    </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        
        <div class="footer">
            <div class="total">Reporte generado automáticamente por el sistema de gestión</div>
            <div>Página 1 de 1</div>
        </div>
    </body>
    </html>
    `;
}

// Método fallback en caso de error con html2pdf
generarPDFFallback(usuarios) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = this.crearContenidoPDF(usuarios);
    
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    
    setTimeout(() => {
        ventana.print();
    }, 500);
}

        // Método auxiliar para formatear fechas
        formatearFecha(fecha) {
            if (!fecha || fecha === '0000-00-00 00:00:00' || fecha === '0000-00-00') return 'N/A';
            
            try {
                const fechaObj = new Date(fecha);
                if (isNaN(fechaObj.getTime())) return 'N/A';
                return fechaObj.toLocaleDateString('es-ES');
            } catch (e) {
                return 'N/A';
            }
        }

        // Método para ordenar la tabla
        ordenarTabla(columna) {
            if (this.tabla) {
                // Determinar dirección del ordenamiento
                let orden = 'asc';
                
                // Para fechas, orden descendente por defecto (más reciente primero)
                if (columna === '6' || columna === '7') {
                    orden = 'desc';
                }
                
                this.tabla.order([parseInt(columna), orden]).draw();
            }
        }

        // Método para recargar usuarios
        recargarUsuarios() {
            const btn = document.getElementById('btnRefrescar');
            const icon = btn.querySelector('i');
            
            // Agregar animación de giro
            icon.classList.add('spin');
            
            this.cargarUsuarios().finally(() => {
                // Quitar animación después de cargar
                setTimeout(() => {
                    icon.classList.remove('spin');
                }, 500);
            });
        }

        resetPassword(ID_USUARIO) {
    // Verificar permiso antes de resetear
    fetch('/sistema/public/index.php?route=permisos&caso=verificarPermiso&objeto=RESETEAR_CONTRASENA&accion=EDITAR&id_usuario=<?= $id_usuario ?>')
        .then(response => response.json())
        .then(data => {
            if (data.data.tiene_permiso) {
                window.location.href = `/sistema/public/resetear-contrasena?id=${ID_USUARIO}`;
            } else {
                alert('No tiene permisos para resetear contraseñas');
            }
        })
        .catch(error => {
            console.error('Error verificando permiso:', error);
            alert('Error al verificar permisos');
        });
}

       editarUsuario(ID_USUARIO) {
    // Verificar permiso antes de editar
    fetch('/sistema/public/index.php?route=permisos&caso=verificarPermiso&objeto=EDITAR_USUARIO&accion=EDITAR&id_usuario=<?= $id_usuario ?>')
        .then(response => response.json())
        .then(data => {
            if (data.data.tiene_permiso) {
                window.location.href = '/sistema/public/editar-usuario?editar=' + ID_USUARIO;
            } else {
                alert('No tiene permisos para editar usuarios');
            }
        })
        .catch(error => {
            console.error('Error verificando permiso:', error);
            alert('Error al verificar permisos');
        });
}

        eliminarUsuario(ID_USUARIO, usuario, nombre) {
    // Verificar permiso antes de eliminar
    fetch('/sistema/public/index.php?route=permisos&caso=verificarPermiso&objeto=GESTION_USUARIOS&accion=ELIMINAR&id_usuario=<?= $id_usuario ?>')
        .then(response => response.json())
        .then(data => {
            if (data.data.tiene_permiso) {
                document.getElementById('eliminar_id_usuario').value = ID_USUARIO;
                document.getElementById('eliminar_nombre_usuario').textContent = `${usuario} - ${nombre}`;
                
                // Actualizar el título del modal dinámicamente
                document.querySelector('#modalEliminarUsuario .modal-title').textContent = 'Bloquear Usuario';
                document.querySelector('#modalEliminarUsuario #btnConfirmarEliminar').textContent = 'Bloquear';
                document.querySelector('#modalEliminarUsuario #btnConfirmarEliminar').className = 'btn btn-warning';
                
                const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
                modal.show();
            } else {
                alert('No tiene permisos para bloquear usuarios');
            }
        })
        .catch(error => {
            console.error('Error verificando permiso:', error);
            alert('Error al verificar permisos');
        });
}
        async confirmarEliminarUsuario() {
            const ID_USUARIO = document.getElementById('eliminar_id_usuario').value;

            try {
                // Llamar al endpoint para cambiar el estado a "Bloqueado"
                const response = await fetch('index.php?route=user&caso=cambiar-estado', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_usuario: ID_USUARIO,
                        estado: 'Bloqueado',
                        modificado_por: 'ADMIN'
                    })
                });

                const result = await response.json();
                
                if (result.status === 200) {
                    // Cerrar el modal inmediatamente
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                    if (modal) modal.hide();

                    // Recargar la página automáticamente sin mostrar alertas
                    window.location.reload();
                    
                } else {
                    // Si hay error, también recargar la página sin mostrar alertas
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                    if (modal) modal.hide();
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error bloqueando usuario:', error);
                // En caso de error también recargar sin alertas
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarUsuario'));
                if (modal) modal.hide();
                window.location.reload();
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
</script>

<style>
/* ESTILOS MEJORADOS PARA TABLA MÁS ESTÉTICA */
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
.btn-outline-warning:hover { background-color: #ffc107; color: black; }
.btn-outline-danger:hover { background-color: #dc3545; color: white; }

/* Tabla general */
#tablaUsuarios {
    font-size: 0.85rem !important;
    width: 100% !important;
}

#tablaUsuarios th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.9rem;
    padding: 10px 8px;
}

#tablaUsuarios td {
    vertical-align: middle;
    word-wrap: break-word;
    word-break: break-word;
    padding: 8px 6px;
    line-height: 1.2;
}

.badge {
    font-size: 0.7em;
    padding: 4px 6px;
}

/* Columnas específicas con mejor manejo de texto */
#tablaUsuarios td:nth-child(1) { /* USUARIO */ 
    min-width: 100px;
    max-width: 120px;
}

#tablaUsuarios td:nth-child(2) { /* NOMBRE_USUARIO */
    min-width: 160px;
    max-width: 200px;
}

#tablaUsuarios td:nth-child(3) { /* ROL */
    min-width: 120px;
    max-width: 150px;
}

#tablaUsuarios td:nth-child(4) { /* CORREO_ELECTRONICO */
    min-width: 180px;
    max-width: 220px;
}

#tablaUsuarios td:nth-child(5) { /* ESTADO_USUARIO */
    min-width: 80px;
    max-width: 100px;
}

#tablaUsuarios td:nth-child(6), /* FECHA_CREACION */
#tablaUsuarios td:nth-child(7) { /* FECHA_VENCIMIENTO */
    min-width: 95px;
    max-width: 110px;
    white-space: nowrap;
}

/* Columna de acciones compacta */
#tablaUsuarios th:last-child,
#tablaUsuarios td:last-child {
    width: 105px !important;
    min-width: 105px !important;
    max-width: 105px !important;
    padding: 4px 2px !important;
}

/* Botones ultra compactos */
.btn-group-sm .btn.border-0 {
    padding: 1px 2px;
    margin: 0;
    border: 1px solid transparent !important;
    font-size: 0.7rem;
}

.btn-group-sm .btn.border-0:hover {
    border: 1px solid currentColor !important;
}

/* Mejorar responsividad */
@media (max-width: 768px) {
    #tablaUsuarios {
        font-size: 0.8rem !important;
    }
    
    #tablaUsuarios th,
    #tablaUsuarios td {
        padding: 6px 4px;
    }
}

/* Header de tabla más claro */
.card-header {
    background-color: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}

/* En la sección de estilos CSS, actualiza: */
.btn-outline-danger:hover { 
    background-color: #dc3545; 
    color: white; 
}

/* Opcional: cambiar el color del botón de bloquear a naranja/ámbar */
.btn-outline-danger {
    border-color: #fd7e14;
    color: #fd7e14;
}

.btn-outline-danger:hover {
    background-color: #fd7e14;
    border-color: #fd7e14;
}

/* Agregar al final de los estilos existentes */
.bi-arrow-clockwise.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Estilos para los controles de ordenamiento */
.form-select {
    max-width: 200px;
}

/* Mejorar responsividad de controles */
@media (max-width: 768px) {
    .form-select {
        max-width: 100%;
    }
}

/* Estilos para el botón de exportar PDF */
#btnExportarPDF {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

#btnExportarPDF:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

#btnExportarPDF:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}

/* Estilos para los badges en el PDF */
.estado-activo, .estado-bloqueado, .estado-nuevo, .estado-inactivo {
    font-size: 9px;
    font-weight: bold;
    display: inline-block;
    text-align: center;
    min-width: 60px;
}
</style>