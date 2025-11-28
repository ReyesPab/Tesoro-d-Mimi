
<?php
use App\config\SessionHelper;
use App\models\userModel;

SessionHelper::startSession();

$idUsuario = SessionHelper::getUserId();

// Obtener datos del perfil usando el nuevo método
$perfilData = null;
if ($idUsuario) {
    $result = userModel::obtenerPerfilUsuarioDirecto($idUsuario);
    if ($result['success']) {
        $perfilData = $result['data'];
    }
}

// Si no se pudieron obtener los datos, usar valores por defecto
if (!$perfilData) {
    $perfilData = [
        'ID_USUARIO' => $idUsuario,
        'USUARIO' => $_SESSION['user_usuario'] ?? 'N/A',
        'NOMBRE_USUARIO' => $_SESSION['user_name'] ?? 'Invitado',
        'CORREO_ELECTRONICO' => $_SESSION['user_email'] ?? 'No especificado',
        'ROL' => $_SESSION['user_rol'] ?? 'Sin rol',
        'ESTADO_USUARIO' => $_SESSION['user_estado'] ?? 'Desconocido',
        'FECHA_CREACION' => $_SESSION['user_fecha_creacion'] ?? date('Y-m-d H:i:s'),
        'FECHA_ULTIMA_CONEXION' => $_SESSION['user_ultima_conexion'] ?? date('Y-m-d H:i:s'),
        'FOTO_PERFIL' => $_SESSION['user_foto'] ?? 'perfil.jpg',
        'ID_ROL' => $_SESSION['user_rol_id'] ?? 0
    ];
}

// Definir variables compatibles con el código existente
$id_usuario = $perfilData['ID_USUARIO'];
$nombre_usuario = $perfilData['NOMBRE_USUARIO'];
$usuario = $perfilData['USUARIO'];
$id_rol = $perfilData['ID_ROL'];
$rol_nombre = $perfilData['ROL'];

// Preparar datos para mostrar (escapar para seguridad)
$nombre = htmlspecialchars($nombre_usuario, ENT_QUOTES, 'UTF-8');
$rol = htmlspecialchars($rol_nombre, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($perfilData['CORREO_ELECTRONICO'], ENT_QUOTES, 'UTF-8');
$usuario_login = htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8');
$estado = htmlspecialchars($perfilData['ESTADO_USUARIO'], ENT_QUOTES, 'UTF-8');
$fechaCreacion = date('d/m/Y', strtotime($perfilData['FECHA_CREACION']));
$fechaUltimaConexion = $perfilData['FECHA_ULTIMA_CONEXION'] ? 
    date('d/m/Y H:i', strtotime($perfilData['FECHA_ULTIMA_CONEXION'])) : 'Nunca';

// Foto de perfil
$fotoPerfil = $perfilData['FOTO_PERFIL'];
$rutaFoto = "/sistema/public/uploads/profiles/" . $fotoPerfil;
$rutaDefault = "/sistema/src/Views/assets/img/perfil.jpg";
$rutaFinal = file_exists($_SERVER['DOCUMENT_ROOT'] . $rutaFoto) ? $rutaFoto : $rutaDefault;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - Sistema Rosquilleria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #2980b9;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-container {
            padding: 20px;
        }
        
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: 600;
            padding: 15px 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }
        
        .btn-info {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .table th {
            background-color: #f1f5f9;
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .alert-info {
            background-color: #e1f0fa;
            border-color: #b6d7f2;
            color: #1a5276;
        }
        
        .badge-success {
            background-color: var(--success-color);
        }
        
        .badge-warning {
            background-color: var(--warning-color);
        }
        
        .badge-danger {
            background-color: var(--danger-color);
        }
        
        .form-select {
            border-radius: 6px;
        }
        
        .form-control {
            border-radius: 6px;
        }
        
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 24px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            background-color: transparent;
            border: none;
            border-bottom: 3px solid var(--secondary-color);
            color: var(--secondary-color);
            font-weight: 600;
        }
        
        .nav-tabs .nav-link:hover {
            border: none;
            border-bottom: 3px solid #dee2e6;
            color: var(--primary-color);
        }
        
        .permiso-checkbox {
            transform: scale(1.3);
            margin: 0 auto;
            display: block;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table > :not(caption) > * > * {
            padding: 12px 8px;
            vertical-align: middle;
        }
        
        /* Estilos específicos para la tabla de permisos */
        .permisos-table th {
            background-color: var(--primary-color);
            color: white;
        }
        
        .permisos-table th.text-center {
            text-align: center;
        }
        
        /* Estilos para los botones de acción */
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .nav-tabs .nav-link {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }

        /* Estilos para selección múltiple */
.seleccion-multiple {
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.checkbox-seleccionar-todo {
    transform: scale(1.2);
    margin-right: 8px;
}

.btn-group-masivo {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.contador-seleccionados {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.permiso-seleccionado {
    background-color: #e3f2fd !important;
    border-left: 4px solid #2196f3 !important;
}

.checkbox-seleccion {
    transform: scale(1.1);
}

.table-actions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.bulk-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Estilos específicos para los botones de acciones masivas */
#guardarSeleccionados:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

#guardarSeleccionados:not(:disabled) {
    opacity: 1;
    cursor: pointer;
}

#guardarTodos {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
}

#guardarTodos:hover {
    background-color: #e67e22;
    border-color: #e67e22;
}

.bulk-actions .btn {
    font-size: 0.875rem;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Estilos para los botones de exportar PDF */
.btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

/* Estilo específico para los botones de exportar PDF en los headers de las cards */
.card-header .btn-danger {
    font-size: 0.875rem;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Estilos diferenciados para los headers */
.card-header.bg-warning .btn-danger:hover {
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
}

.card-header.bg-info .btn-danger:hover {
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
}
    </style>
</head>
<body>
    <!-- Incluir header y sidebar -->
    <?php 
    require_once __DIR__ . '/../partials/header.php';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <main id="main" class="main">
        <div class="container-fluid main-container">
            
            <!-- Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Gestión de Permisos</h1>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="recargarPagina()">
                            <i class="fas fa-sync-alt"></i> Recargar
                        </button>
                         
                    </div>
                </div>
            </div>

            <!-- Información del Usuario -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Información del Usuario:</strong>  
                Usuario: <?= $usuario ?> | 
                Rol: <?= $rol_nombre ?>  
            </div>

            <!-- Navegación por pestañas -->
            <ul class="nav nav-tabs" id="permisosTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="permisos-tab" data-bs-toggle="tab" data-bs-target="#permisos" type="button" role="tab" aria-controls="permisos" aria-selected="true">
                        <i class="fas fa-key me-2"></i>Gestión de Permisos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
               
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sistema-tab" data-bs-toggle="tab" data-bs-target="#sistema" type="button" role="tab" aria-controls="sistema" aria-selected="false">
                        <i class="fas fa-sliders-h me-2"></i>Parámetros del Sistema
                    </button>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="permisosTabsContent">
                <!-- Pestaña de Gestión de Permisos -->
                <div class="tab-pane fade show active" id="permisos" role="tabpanel" aria-labelledby="permisos-tab">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-users-cog me-2"></i>Seleccionar Rol
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <select id="selectRol" class="form-select">
                                        <option value="">Seleccione un rol</option>
                                    </select>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i> Seleccione un rol para gestionar sus permisos
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Información del Rol Seleccionado -->
                            <div class="card mt-3 d-none" id="infoRolCard">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Información del Rol
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p id="infoRolNombre" class="mb-1"><strong>Rol:</strong> <span id="rolNombre"></span></p>
                                    <p id="infoRolDesc" class="mb-0"><strong>Descripción:</strong> <span id="rolDesc"></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-shield me-2"></i>Permisos del Rol
                                        <span id="contadorPermisos" class="badge bg-light text-dark ms-2">0 permisos</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="permisosContainer">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-key fa-3x mb-3"></i>
                                            <p>Seleccione un rol para ver y gestionar sus permisos</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 

                <!-- Pestaña de Parámetros del Sistema -->
                <div class="tab-pane fade" id="sistema" role="tabpanel" aria-labelledby="sistema-tab">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-database me-2"></i>Configuración del Sistema
                            </h5>
                            <button class="btn btn-danger btn-sm" id="btnExportarPDFSeguridad">
                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                </button>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Información:</strong> Estos parámetros controlan el comportamiento general del sistema.
                            </div>
                            <div id="parametrosSistemaContainer">
                                <div class="text-center">
                                    <div class="spinner-border text-info" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando parámetros del sistema...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Incluir footer -->
    <?php require_once __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // El código JavaScript permanece exactamente igual que antes
        // Variables globales
        let roles = [];
        let objetos = [];
        let rolSeleccionado = null;

        // Inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando módulo de permisos...');
            console.log('Usuario ID:', '<?= $id_usuario ?>');
            console.log('Rol ID:', '<?= $id_rol ?>');
            
            cargarRoles();
            cargarParametrosSeguridad();
            cargarParametrosSistema();

            // También configurar los botones si ya existen en el DOM
    setTimeout(configurarExportacionPDF, 1000);
        });

        // Función para debug de sesión
        function debugSession() {
            console.log('Información de sesión:');
            console.log('- Usuario ID:', '<?= $id_usuario ?>');
            console.log('- Nombre:', '<?= $nombre_usuario ?>');
            console.log('- Usuario:', '<?= $usuario ?>');
            console.log('- Rol ID:', '<?= $id_rol ?>');
            
            alert('Información de sesión:\n' +
                  'Usuario ID: <?= $id_usuario ?>\n' +
                  'Nombre: <?= $nombre_usuario ?>\n' +
                  'Usuario: <?= $usuario ?>\n' +
                  'Rol ID: <?= $id_rol ?>');
        }

        // Función para recargar la página
        function recargarPagina() {
            location.reload();
        }

        // Cargar lista de roles
        async function cargarRoles() {
            try {
                console.log('Cargando roles...');
                const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerRoles');
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Respuesta de roles:', result);
                
                if (result.status === 200 && result.data) {
                    roles = result.data;
                    const selectRol = document.getElementById('selectRol');
                    
                    selectRol.innerHTML = '<option value="">Seleccione un rol</option>';
                    roles.forEach(rol => {
                        const option = document.createElement('option');
                        option.value = rol.ID_ROL;
                        option.textContent = `${rol.ROL} - ${rol.DESCRIPCION}`;
                        option.setAttribute('data-desc', rol.DESCRIPCION);
                        selectRol.appendChild(option);
                    });
                    
                    console.log(`Se cargaron ${roles.length} roles`);
                    
                    // Event listener para cambio de rol
                    selectRol.addEventListener('change', function() {
                        rolSeleccionado = this.value;
                        const selectedOption = this.options[this.selectedIndex];
                        
                        if (rolSeleccionado) {
                            // Mostrar información del rol
                            document.getElementById('infoRolCard').classList.remove('d-none');
                            document.getElementById('rolNombre').textContent = selectedOption.text.split(' - ')[0];
                            document.getElementById('rolDesc').textContent = selectedOption.getAttribute('data-desc');
                            
                            cargarPermisosRol(rolSeleccionado);
                        } else {
                            document.getElementById('infoRolCard').classList.add('d-none');
                            mostrarMensajeSinSeleccion();
                        }
                    });
                } else {
                    throw new Error(result.message || 'Error desconocido al cargar roles');
                }
            } catch (error) {
                console.error('Error al cargar roles:', error);
                mostrarError('Error al cargar la lista de roles: ' + error.message);
            }
        }

        // Cargar permisos del rol seleccionado
        async function cargarPermisosRol(idRol) {
            try {
                mostrarCargandoPermisos();
                console.log(`Cargando permisos para rol: ${idRol}`);
                
                const response = await fetch(`/sistema/public/index.php?route=permisos&caso=obtenerPermisosRol&id_rol=${idRol}`);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Respuesta de permisos:', result);
                
                if (result.status === 200) {
                    const permisos = result.data;
                    mostrarPermisos(permisos);
                } else {
                    throw new Error(result.message || 'Error desconocido al cargar permisos');
                }
            } catch (error) {
                console.error('Error al cargar permisos:', error);
                mostrarError('Error al cargar los permisos del rol: ' + error.message);
            }
        }

        // Mostrar permisos en la tabla
        // Mostrar permisos en la tabla
async function mostrarPermisos(permisos) {
    try {
        console.log('Mostrando permisos...');
        
        // Cargar objetos si no están cargados
        if (objetos.length === 0) {
            console.log('Cargando objetos del sistema...');
            const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerObjetos');
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const result = await response.json();
            if (result.status === 200) {
                objetos = result.data;
                console.log(`Se cargaron ${objetos.length} objetos`);
            } else {
                throw new Error(result.message || 'No se pudieron cargar los objetos');
            }
        }

        const container = document.getElementById('permisosContainer');
        let html = `
            <!-- Panel de Selección Múltiple -->
            <div class="seleccion-multiple">
                <div class="table-actions-header">
                    <div class="form-check">
                        <input class="form-check-input checkbox-seleccionar-todo" type="checkbox" id="seleccionarTodo">
                        <label class="form-check-label fw-bold" for="seleccionarTodo">
                            Seleccionar todo
                        </label>
                        <span class="contador-seleccionados ms-2" id="contadorSeleccionados">
                            0 elementos seleccionados
                        </span>
                    </div>
                    <div class="bulk-actions">
                        <button class="btn btn-success btn-sm" id="guardarSeleccionados" disabled>
                            <i class="fas fa-save me-1"></i>Guardar seleccionados
                        </button>
                        <button class="btn btn-warning btn-sm" id="guardarTodos">
                            <i class="fas fa-save me-1"></i>Guardar todos los cambios
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover permisos-table">
                    <thead>
                        <tr>
                            <th width="40px">
                                <input type="checkbox" class="form-check-input" id="checkboxHeader">
                            </th>
                            <th>Objeto/Pantalla</th>
                            <th class="text-center">Consultar</th>
                            <th class="text-center">Crear</th>
                            <th class="text-center">Editar</th>
                            <th class="text-center">Eliminar</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        let totalPermisos = 0;

        objetos.forEach(objeto => {
            const permiso = permisos.find(p => p.ID_OBJETO == objeto.ID_OBJETO);
            const tienePermiso = permiso ? 
                (permiso.PERMISO_CONSULTAR || permiso.PERMISO_CREACION || permiso.PERMISO_ACTUALIZACION || permiso.PERMISO_ELIMINACION) : 
                false;
            
            if (tienePermiso) totalPermisos++;

            html += `
                <tr id="fila-${objeto.ID_OBJETO}" data-objeto="${objeto.ID_OBJETO}">
                    <td>
                        <input type="checkbox" class="form-check-input checkbox-seleccion" 
                            data-objeto="${objeto.ID_OBJETO}">
                    </td>
                    <td>
                        <strong>${objeto.OBJETO}</strong><br>
                        <small class="text-muted">${objeto.DESCRIPCION || 'Sin descripción'}</small>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input permiso-checkbox" 
                            data-objeto="${objeto.ID_OBJETO}" data-tipo="consultar"
                            ${permiso && permiso.PERMISO_CONSULTAR == 1 ? 'checked' : ''}>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input permiso-checkbox" 
                            data-objeto="${objeto.ID_OBJETO}" data-tipo="crear"
                            ${permiso && permiso.PERMISO_CREACION == 1 ? 'checked' : ''}>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input permiso-checkbox" 
                            data-objeto="${objeto.ID_OBJETO}" data-tipo="actualizar"
                            ${permiso && permiso.PERMISO_ACTUALIZACION == 1 ? 'checked' : ''}>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input permiso-checkbox" 
                            data-objeto="${objeto.ID_OBJETO}" data-tipo="eliminar"
                            ${permiso && permiso.PERMISO_ELIMINACION == 1 ? 'checked' : ''}>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary guardar-permiso" 
                            data-objeto="${objeto.ID_OBJETO}">
                            <i class="fas fa-save me-1"></i>Guardar
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        container.innerHTML = html;

        // Actualizar contador
        document.getElementById('contadorPermisos').textContent = `${totalPermisos} permisos activos`;

        // Inicializar sistema de selección múltiple
        inicializarSeleccionMultiple();

        // Agregar event listeners a los botones de guardar individuales
        document.querySelectorAll('.guardar-permiso').forEach(btn => {
            btn.addEventListener('click', function() {
                guardarPermiso(rolSeleccionado, this.dataset.objeto);
            });
        });

        console.log('Tabla de permisos cargada correctamente');

    } catch (error) {
        console.error('Error al mostrar permisos:', error);
        mostrarError('Error al mostrar la tabla de permisos: ' + error.message);
    }
}

        // Guardar permiso individual
        async function guardarPermiso(idRol, idObjeto) {
            const consultar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="consultar"]`).checked;
            const crear = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="crear"]`).checked;
            const actualizar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="actualizar"]`).checked;
            const eliminar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="eliminar"]`).checked;

            try {
                console.log(`Guardando permiso para rol ${idRol}, objeto ${idObjeto}`);
                
                const response = await fetch('/sistema/public/index.php?route=permisos&caso=gestionarPermiso', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_rol: idRol,
                        id_objeto: idObjeto,
                        permiso_creacion: crear ? 1 : 0,
                        permiso_eliminacion: eliminar ? 1 : 0,
                        permiso_actualizacion: actualizar ? 1 : 0,
                        permiso_consultar: consultar ? 1 : 0,
                        usuario_accion: '<?= $usuario ?>'
                    })
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const result = await response.json();

                if (result.status === 200) {
                    mostrarAlerta('success', 'Permisos guardados correctamente');
                    // Recargar permisos para reflejar cambios
                    cargarPermisosRol(idRol);
                } else {
                    mostrarAlerta('error', 'Error al guardar permisos: ' + result.message);
                }
            } catch (error) {
                console.error('Error al guardar permiso:', error);
                mostrarAlerta('error', 'Error al guardar permisos: ' + error.message);
            }
        }

        // Cargar parámetros de seguridad
        async function cargarParametrosSeguridad() {
            try {
                console.log('Cargando parámetros de seguridad...');
                const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerParametrosGenerales');
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Respuesta parámetros seguridad:', result);
                
                if (result.status === 200) {
                    mostrarParametrosSeguridad(result.data);
                } else {
                    throw new Error(result.message || 'Error desconocido al cargar parámetros de seguridad');
                }
            } catch (error) {
                console.error('Error al cargar parámetros de seguridad:', error);
                mostrarErrorParametros('Error al cargar parámetros de seguridad: ' + error.message);
            }
        }

        // Mostrar parámetros de seguridad
        function mostrarParametrosSeguridad(parametros) {
            const container = document.getElementById('parametrosSeguridadContainer');
            
            if (!parametros || parametros.length === 0) {
                container.innerHTML = '<div class="alert alert-warning">No hay parámetros de seguridad configurados</div>';
                return;
            }
            
            let html = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Descripción</th>
                                <th>Valor Actual</th>
                                <th>Nuevo Valor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            parametros.forEach(parametro => {
                html += `
                    <tr>
                        <td><strong>${parametro.PARAMETRO}</strong></td>
                        <td>${parametro.DESCRIPCION || 'Sin descripción'}</td>
                        <td><span class="badge bg-secondary">${parametro.VALOR}</span></td>
                        <td>
                            <input type="text" class="form-control form-control-sm" 
                                id="param_${parametro.ID_PARAMETRO}" 
                                value="${parametro.VALOR}">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning actualizar-parametro" 
                                data-id="${parametro.ID_PARAMETRO}">
                                <i class="fas fa-sync-alt me-1"></i>Actualizar
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            container.innerHTML = html;

            // Agregar event listeners a los botones de actualizar
            document.querySelectorAll('.actualizar-parametro').forEach(btn => {
                btn.addEventListener('click', function() {
                    actualizarParametroSeguridad(this.dataset.id);
                });
            });
        }

        // Actualizar parámetro de seguridad
        // Actualizar parámetro de seguridad
async function actualizarParametroSeguridad(idParametro) {
    const nuevoValor = document.getElementById(`param_${idParametro}`).value;
    const actualizarExistentes = document.getElementById(`masivo_${idParametro}`)?.checked || false;
    
    console.log('Actualizando parámetro:', {
        idParametro,
        nuevoValor,
        actualizarExistentes
    });
    
    if (!nuevoValor) {
        mostrarAlerta('warning', 'Por favor ingrese un valor');
        return;
    }

    try {
        const response = await fetch('/sistema/public/index.php?route=permisos&caso=actualizarParametroSeguridad', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_parametro: idParametro,
                valor: nuevoValor,
                modificado_por: '<?= $usuario ?>',
                actualizar_existentes: actualizarExistentes
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const result = await response.json();
        console.log('Respuesta del servidor:', result);

        if (result.status === 200) {
            let mensaje = 'Parámetro actualizado correctamente';
            if (result.data && result.data.registros_afectados > 0) {
                mensaje += `. Se actualizaron ${result.data.registros_afectados} registros en inventario.`;
            }
            mostrarAlerta('success', mensaje);
            cargarParametrosSeguridad(); // Recargar para mostrar valores actualizados
        } else {
            mostrarAlerta('error', 'Error al actualizar parámetro: ' + result.message);
        }
    } catch (error) {
        console.error('Error al actualizar parámetro:', error);
        mostrarAlerta('error', 'Error al actualizar parámetro: ' + error.message);
    }
}

        // Cargar parámetros del sistema
        // Cargar parámetros del sistema
async function cargarParametrosSistema() {
    try {
        console.log('Cargando parámetros del sistema...');
        const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerParametrosGenerales');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Respuesta parámetros sistema:', result);
        
        if (result.status === 200) {
            mostrarParametrosSistema(result.data);
            // Configurar el botón de exportación PDF después de cargar los datos
            configurarExportacionPDF();
        } else {
            throw new Error(result.message || 'Error desconocido al cargar parámetros del sistema');
        }
    } catch (error) {
        console.error('Error al cargar parámetros del sistema:', error);
        mostrarErrorParametrosSistema('Error al cargar parámetros del sistema: ' + error.message);
    }
}
// Inicializar sistema de selección múltiple
// Inicializar sistema de selección múltiple
function inicializarSeleccionMultiple() {
    const seleccionarTodo = document.getElementById('seleccionarTodo');
    const checkboxHeader = document.getElementById('checkboxHeader');
    const guardarSeleccionados = document.getElementById('guardarSeleccionados');
    const guardarTodos = document.getElementById('guardarTodos');
    
    console.log('Inicializando selección múltiple...');
    console.log('Botón guardar seleccionados:', guardarSeleccionados);
    console.log('Botón guardar todos:', guardarTodos);
    
    // Verificar que los elementos existen
    if (!guardarSeleccionados || !guardarTodos) {
        console.error('❌ ERROR: No se encontraron los botones de acciones masivas');
        return;
    }
    
    // Seleccionar/deseleccionar todo
    seleccionarTodo.addEventListener('change', function() {
        console.log('Seleccionar todo cambiado:', this.checked);
        const checkboxes = document.querySelectorAll('.checkbox-seleccion');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            actualizarEstiloFila(checkbox);
        });
        actualizarContadorSeleccionados();
        actualizarBotonGuardar();
    });
    
    // Checkbox del header
    checkboxHeader.addEventListener('change', function() {
        console.log('Checkbox header cambiado:', this.checked);
        const checkboxes = document.querySelectorAll('.checkbox-seleccion');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            actualizarEstiloFila(checkbox);
        });
        seleccionarTodo.checked = this.checked;
        actualizarContadorSeleccionados();
        actualizarBotonGuardar();
    });
    
    // Event listeners para checkboxes individuales
    document.querySelectorAll('.checkbox-seleccion').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Checkbox individual cambiado:', this.dataset.objeto, this.checked);
            actualizarEstiloFila(this);
            actualizarContadorSeleccionados();
            actualizarBotonGuardar();
            actualizarCheckboxHeader();
        });
    });
    
    // Botón guardar seleccionados
    guardarSeleccionados.addEventListener('click', function() {
        console.log('Clic en guardar seleccionados');
        guardarPermisosSeleccionados();
    });
    
    // Botón guardar todos
    guardarTodos.addEventListener('click', function() {
        console.log('Clic en guardar todos');
        guardarTodosLosPermisos();
    });
    
    // Estado inicial
    actualizarBotonGuardar();
    console.log('✅ Sistema de selección múltiple inicializado');
}

// Actualizar estilo de la fila cuando se selecciona
function actualizarEstiloFila(checkbox) {
    const fila = document.getElementById(`fila-${checkbox.dataset.objeto}`);
    if (checkbox.checked) {
        fila.classList.add('permiso-seleccionado');
    } else {
        fila.classList.remove('permiso-seleccionado');
    }
}

// Actualizar contador de elementos seleccionados
function actualizarContadorSeleccionados() {
    const seleccionados = document.querySelectorAll('.checkbox-seleccion:checked');
    const contador = document.getElementById('contadorSeleccionados');
    contador.textContent = `${seleccionados.length} elementos seleccionados`;
}

// Actualizar estado del botón guardar
function actualizarBotonGuardar() {
    const seleccionados = document.querySelectorAll('.checkbox-seleccion:checked');
    const guardarSeleccionados = document.getElementById('guardarSeleccionados');
    guardarSeleccionados.disabled = seleccionados.length === 0;
}

// Actualizar checkbox del header
function actualizarCheckboxHeader() {
    const checkboxes = document.querySelectorAll('.checkbox-seleccion');
    const checkboxHeader = document.getElementById('checkboxHeader');
    const seleccionarTodo = document.getElementById('seleccionarTodo');
    
    const todosSeleccionados = checkboxes.length > 0 && 
        Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    checkboxHeader.checked = todosSeleccionados;
    seleccionarTodo.checked = todosSeleccionados;
}

// Guardar permisos seleccionados
async function guardarPermisosSeleccionados() {
    const seleccionados = document.querySelectorAll('.checkbox-seleccion:checked');
    
    if (seleccionados.length === 0) {
        mostrarAlerta('warning', 'Por favor seleccione al menos un permiso para guardar');
        return;
    }
    
    const objetosIds = Array.from(seleccionados).map(checkbox => checkbox.dataset.objeto);
    
    try {
        mostrarAlerta('info', `Guardando ${objetosIds.length} permisos...`);
        
        // Guardar cada permiso seleccionado
        for (const idObjeto of objetosIds) {
            await guardarPermisoIndividual(rolSeleccionado, idObjeto);
        }
        
        mostrarAlerta('success', `Se guardaron ${objetosIds.length} permisos correctamente`);
        
        // Recargar permisos para reflejar cambios
        setTimeout(() => {
            cargarPermisosRol(rolSeleccionado);
        }, 1000);
        
    } catch (error) {
        console.error('Error al guardar permisos seleccionados:', error);
        mostrarAlerta('error', 'Error al guardar los permisos seleccionados: ' + error.message);
    }
}

// Guardar todos los permisos
async function guardarTodosLosPermisos() {
    const todosLosObjetos = objetos.map(objeto => objeto.ID_OBJETO);
    
    try {
        mostrarAlerta('info', `Guardando todos los permisos (${todosLosObjetos.length})...`);
        
        // Guardar cada permiso
        for (const idObjeto of todosLosObjetos) {
            await guardarPermisoIndividual(rolSeleccionado, idObjeto);
        }
        
        mostrarAlerta('success', `Se guardaron todos los permisos (${todosLosObjetos.length}) correctamente`);
        
        // Recargar permisos para reflejar cambios
        setTimeout(() => {
            cargarPermisosRol(rolSeleccionado);
        }, 1000);
        
    } catch (error) {
        console.error('Error al guardar todos los permisos:', error);
        mostrarAlerta('error', 'Error al guardar todos los permisos: ' + error.message);
    }
}

// Función auxiliar para guardar permiso individual (sin recargar)
async function guardarPermisoIndividual(idRol, idObjeto) {
    const consultar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="consultar"]`).checked;
    const crear = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="crear"]`).checked;
    const actualizar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="actualizar"]`).checked;
    const eliminar = document.querySelector(`input[data-objeto="${idObjeto}"][data-tipo="eliminar"]`).checked;

    const response = await fetch('/sistema/public/index.php?route=permisos&caso=gestionarPermiso', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_rol: idRol,
            id_objeto: idObjeto,
            permiso_creacion: crear ? 1 : 0,
            permiso_eliminacion: eliminar ? 1 : 0,
            permiso_actualizacion: actualizar ? 1 : 0,
            permiso_consultar: consultar ? 1 : 0,
            usuario_accion: '<?= $usuario ?>'
        })
    });

    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status} para objeto ${idObjeto}`);
    }

    const result = await response.json();
    if (result.status !== 200) {
        throw new Error(result.message || `Error al guardar permiso para objeto ${idObjeto}`);
    }
    
    return result;
}
        // Mostrar parámetros del sistema
        function mostrarParametrosSistema(parametros) {
            const container = document.getElementById('parametrosSistemaContainer');
            
            if (!parametros || parametros.length === 0) {
                container.innerHTML = '<div class="alert alert-warning">No hay parámetros del sistema configurados</div>';
                return;
            }
            
            let html = `
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Parámetro</th>
                                <th>Descripción</th>
                                <th>Valor Actual</th>
                                <th>Nuevo Valor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            parametros.forEach(parametro => {
                const esParametroInventario = parametro.PARAMETRO.includes('INVENTARIO');
                
                html += `
                    <tr>
                        <td><strong>${parametro.PARAMETRO}</strong></td>
                        <td>${parametro.DESCRIPCION || 'Sin descripción'}</td>
                        <td><span class="badge bg-info">${parametro.VALOR}</span></td>
                        <td>
                            <input type="text" class="form-control form-control-sm" 
                                id="param_sistema_${parametro.ID_PARAMETRO}" 
                                value="${parametro.VALOR}">
                            
                            <!-- Checkbox para actualización masiva (solo para parámetros de inventario) -->
                            ${esParametroInventario ? `
                            <div class="actualizacion-masiva-container">
                                <div class="form-check">
                                    <input class="form-check-input actualizacion-masiva" 
                                        type="checkbox" 
                                        id="masivo_sistema_${parametro.ID_PARAMETRO}">
                                    <label class="form-check-label small" for="masivo_sistema_${parametro.ID_PARAMETRO}">
                                        <i class="fas fa-database me-1"></i>Aplicar a todos los registros existentes
                                    </label>
                                </div>
                                <small class="text-muted">Si se marca, se actualizarán todos los productos/materias primas</small>
                            </div>
                            ` : ''}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info actualizar-parametro-sistema" 
                                data-id="${parametro.ID_PARAMETRO}">
                                <i class="fas fa-sync-alt me-1"></i>Actualizar
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            container.innerHTML = html;

            // Agregar event listeners a los botones de actualizar
            document.querySelectorAll('.actualizar-parametro-sistema').forEach(btn => {
                btn.addEventListener('click', function() {
                    actualizarParametroSistema(this.dataset.id);
                });
            });
        }

        // Actualizar parámetro del sistema
        async function actualizarParametroSistema(idParametro) {
    const nuevoValor = document.getElementById(`param_sistema_${idParametro}`).value;
    const actualizarExistentes = document.getElementById(`masivo_sistema_${idParametro}`)?.checked || false;
    
    if (!nuevoValor) {
        mostrarAlerta('warning', 'Por favor ingrese un valor');
        return;
    }

    try {
        const response = await fetch('/sistema/public/index.php?route=permisos&caso=actualizarParametroSeguridad', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_parametro: idParametro,
                valor: nuevoValor,
                modificado_por: '<?= $usuario ?>',
                actualizar_existentes: actualizarExistentes
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === 200) {
            let mensaje = 'Parámetro actualizado correctamente';
            if (result.data && result.data.registros_afectados > 0) {
                mensaje += `. Se actualizaron ${result.data.registros_afectados} registros en inventario.`;
            }
            mostrarAlerta('success', mensaje);
            cargarParametrosSistema(); // Recargar para mostrar valores actualizados
        } else {
            mostrarAlerta('error', 'Error al actualizar parámetro: ' + result.message);
        }
    } catch (error) {
        console.error('Error al actualizar parámetro:', error);
        mostrarAlerta('error', 'Error al actualizar parámetro: ' + error.message);
    }
}

        // Funciones auxiliares
        function mostrarCargandoPermisos() {
            const container = document.getElementById('permisosContainer');
            container.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando permisos...</p>
                </div>
            `;
        }

        function mostrarMensajeSinSeleccion() {
            const container = document.getElementById('permisosContainer');
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-key fa-3x mb-3"></i>
                    <p>Seleccione un rol para ver y gestionar sus permisos</p>
                </div>
            `;
            document.getElementById('contadorPermisos').textContent = '0 permisos';
        }

        function mostrarError(mensaje) {
            const container = document.getElementById('permisosContainer');
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> ${mensaje}
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="recargarPagina()">
                        <i class="fas fa-sync-alt"></i> Reintentar
                    </button>
                </div>
            `;
        }

        function mostrarErrorParametros(mensaje) {
            const container = document.getElementById('parametrosSeguridadContainer');
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> ${mensaje}
                </div>
            `;
        }

        function mostrarErrorParametrosSistema(mensaje) {
            const container = document.getElementById('parametrosSistemaContainer');
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> ${mensaje}
                </div>
            `;
        }

        function mostrarAlerta(tipo, mensaje) {
            const alertClass = tipo === 'success' ? 'alert-success' : 
                             tipo === 'warning' ? 'alert-warning' : 'alert-danger';
            const icon = tipo === 'success' ? 'fa-check-circle' : 
                        tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas ${icon}"></i> ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.main-container').insertBefore(alertDiv, document.querySelector('.main-container').firstChild);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Función para exportar PDF de parámetros del sistema
async function exportarPDFSistema() {
    try {
        console.log('Iniciando exportación PDF de parámetros del sistema...');
        
        // Obtener los parámetros actuales
        const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerParametrosGenerales');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Parámetros del sistema obtenidos para PDF:', result);
        
        if (result.status === 200 && result.data) {
            generarPDFSistema(result.data);
        } else {
            throw new Error(result.message || 'No se pudieron obtener los parámetros del sistema');
        }
    } catch (error) {
        console.error('Error exportando PDF del sistema:', error);
        mostrarAlerta('error', 'Error al exportar PDF: ' + error.message);
    }
}

// Función para generar el PDF de parámetros del sistema
function generarPDFSistema(parametros) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    // Obtener la URL base del sitio
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;

    const element = document.createElement('div');
    element.innerHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Parámetros del Sistema</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background-color: #f5f7fa; 
                margin: 0; 
                padding: 20px; 
                font-size: 10px; 
                color: #333; 
            }
            .container { 
                max-width: 1100px; 
                margin: 0 auto; 
                background: #fff; 
                border: 1px solid #ddd; 
                border-radius: 8px; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            }
            .header { 
                background: linear-gradient(90deg, #17a2b8, #138496);
                color: #ffffff; 
                padding: 18px 22px; 
                border-radius: 8px 8px 0 0; 
            }
            .brand { 
                display: flex; 
                align-items: center; 
                gap: 14px; 
            }
            .brand img { 
                width: 54px; 
                height: 54px; 
                border-radius: 8px; 
                object-fit: cover; 
                background: #fff; 
            }
            .brand-text { 
                display: flex; 
                flex-direction: column; 
            }
            .header h1 { 
                margin: 0; 
                font-size: 24px; 
                letter-spacing: .5px; 
            }
            .header h2 { 
                margin: 2px 0 4px; 
                font-size: 14px; 
                font-weight: normal; 
                opacity: .9; 
            }
            .header .fecha { 
                font-size: 12px; 
                opacity: .9; 
            }

            .section { 
                padding: 18px 24px; 
            }
            .resumen { 
                margin-bottom: 15px; 
                font-size: 11px; 
                padding: 8px; 
                background-color: #f8f9fa; 
                border-left: 4px solid #17a2b8; 
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px; 
                font-size: 9px; 
                page-break-inside: auto; 
            }
            th { 
                background: linear-gradient(90deg, #17a2b8, #138496);
                color: #fff; 
                padding: 10px 8px; 
                text-align: left; 
                border: 1px solid #117a8b; 
            }
            td { 
                border: 1px solid #dee2e6; 
                padding: 9px 8px; 
                vertical-align: top; 
            }
            tr:nth-child(even) { 
                background-color: #f2f9fb; 
            }

            .badge { 
                color: white; 
                padding: 3px 6px; 
                border-radius: 12px; 
                font-size: 8px; 
                font-weight: bold; 
            }
            .bg-info { background-color: #17a2b8; }
            .bg-secondary { background-color: #6c757d; }
            .bg-warning { background-color: #ffc107; color: #000; }
            .bg-success { background-color: #28a745; }

            .footer { 
                text-align: center; 
                padding: 16px 24px; 
                color: #6c757d; 
                font-size: 12px; 
                border-top: 1px solid #dee2e6; 
                position: relative;
            }
            .paginacion {
                position: absolute;
                right: 24px;
                font-size: 10px;
            }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            
            /* Estilos para la paginación */
            .page-number:after {
                content: "Página " counter(page);
            }
            
            @page {
                margin: 20mm;
                @bottom-right {
                    content: "Página " counter(page) " de " counter(pages);
                    font-size: 10px;
                    color: #6c757d;
                }
                @bottom-left {
                    content: "Sistema de Gestión Tesoro D' MIMI";
                    font-size: 10px;
                    color: #6c757d;
                }
            }
            
            /* Estilos para parámetros de inventario */
            .parametro-inventario {
                background-color: #fff3cd !important;
                border-left: 3px solid #ffc107 !important;
            }
        </style>
    </head>
    <body>
    <div class="container" id="contenido-pdf">
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Parámetros del Sistema</h1>
                    <h2>Tesoro D' MIMI - Sistema de Gestión</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="resumen">
                <strong>Total de parámetros del sistema: ${parametros.length}</strong> | 
                <strong>Usuario generador: ${'<?= $usuario ?>'}</strong> |
                <strong>Rol: ${'<?= $rol_nombre ?>'}</strong>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Parámetro</th>
                        <th width="35%">Descripción</th>
                        <th width="15%">Valor Actual</th>
                        <th width="10%">Tipo</th>
                        <th width="10%">Fecha Modificación</th>
                    </tr>
                </thead>
                <tbody>
                    ${parametros.map((parametro, index) => {
                        const fechaModificacion = parametro.FECHA_MODIFICACION ? 
                            new Date(parametro.FECHA_MODIFICACION).toLocaleDateString('es-ES') : 
                            'No modificado';
                        
                        const esParametroInventario = parametro.PARAMETRO.includes('INVENTARIO');
                        const tipoParametro = esParametroInventario ? 'Inventario' : 'Sistema';
                        const claseFila = esParametroInventario ? 'parametro-inventario' : '';
                        
                        return `
                        <tr class="${claseFila}">
                            <td class="text-center">${index + 1}</td>
                            <td><strong>${parametro.PARAMETRO || 'N/A'}</strong></td>
                            <td>${parametro.DESCRIPCION || 'Sin descripción'}</td>
                            <td>
                                <span class="badge ${esParametroInventario ? 'bg-warning' : 'bg-info'}">
                                    ${parametro.VALOR || 'N/A'}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge ${esParametroInventario ? 'bg-warning' : 'bg-secondary'}">
                                    ${tipoParametro}
                                </span>
                            </td>
                            <td>${fechaModificacion}</td>
                        </tr>
                        `;
                    }).join('')}
                    ${parametros.length === 0 ? 
                        '<tr><td colspan="6" style="text-align:center; padding:14px;">No hay parámetros del sistema configurados.</td></tr>' : 
                        ''}
                </tbody>
            </table>
            
            <!-- Resumen de tipos de parámetros -->
            ${function() {
                const totalInventario = parametros.filter(p => p.PARAMETRO.includes('INVENTARIO')).length;
                const totalSistema = parametros.length - totalInventario;
                
                return `
                <div class="resumen mt-3">
                    <strong>Resumen por tipo:</strong> 
                    <span class="badge bg-info">${totalSistema} Parámetros de Sistema</span> | 
                    <span class="badge bg-warning">${totalInventario} Parámetros de Inventario</span>
                </div>
                `;
            }()}
        </div>
        
        <div class="footer">
            <div>Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
    </div>
    </body>
    </html>
    `;

    const opt = {
        margin: [10, 10, 10, 10],
        filename: `parametros_sistema_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { 
            type: 'jpeg', 
            quality: 0.98 
        },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            logging: false
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait',
            compress: true
        },
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'] 
        }
    };

    // Mostrar mensaje de generación
    mostrarAlerta('info', 'Generando PDF del sistema... Por favor espere.');
    
    html2pdf().set(opt).from(element).save().then(() => {
        mostrarAlerta('success', 'PDF del sistema generado y descargado correctamente.');
    }).catch(error => {
        console.error('Error generando PDF del sistema:', error);
        mostrarAlerta('error', 'Error al generar el PDF: ' + error.message);
    });
}

// Configurar los eventos de los botones de exportación
function configurarExportacionPDF() {
    // Botón de parámetros de seguridad
    const btnExportarPDFSeguridad = document.getElementById('btnExportarPDFSeguridad');
    if (btnExportarPDFSeguridad) {
        btnExportarPDFSeguridad.addEventListener('click', exportarPDFSeguridad);
        console.log('✅ Botón de exportación PDF seguridad configurado');
    } else {
        console.log('⚠️ Botón de exportación PDF seguridad no encontrado');
    }
    
    // Botón de parámetros del sistema
    const btnExportarPDFSistema = document.getElementById('btnExportarPDFSistema');
    if (btnExportarPDFSistema) {
        btnExportarPDFSistema.addEventListener('click', exportarPDFSistema);
        console.log('✅ Botón de exportación PDF sistema configurado');
    } else {
        console.log('⚠️ Botón de exportación PDF sistema no encontrado');
    }
}

// Función para exportar PDF de parámetros de seguridad
async function exportarPDFSeguridad() {
    try {
        console.log('Iniciando exportación PDF de parámetros de seguridad...');
        
        // Obtener los parámetros actuales
        const response = await fetch('/sistema/public/index.php?route=permisos&caso=obtenerParametrosGenerales');
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Parámetros obtenidos para PDF:', result);
        
        if (result.status === 200 && result.data) {
            generarPDFSeguridad(result.data);
        } else {
            throw new Error(result.message || 'No se pudieron obtener los parámetros');
        }
    } catch (error) {
        console.error('Error exportando PDF:', error);
        mostrarAlerta('error', 'Error al exportar PDF: ' + error.message);
    }
}

// Función para generar el PDF con el estilo solicitado
function generarPDFSeguridad(parametros) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    // Obtener la URL base del sitio
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;

    const element = document.createElement('div');
    element.innerHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Parámetros de Seguridad</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background-color: #f5f7fa; 
                margin: 0; 
                padding: 20px; 
                font-size: 10px; 
                color: #333; 
            }
            .container { 
                max-width: 1100px; 
                margin: 0 auto; 
                background: #fff; 
                border: 1px solid #ddd; 
                border-radius: 8px; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            }
            .header { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #ffffff; 
                padding: 18px 22px; 
                border-radius: 8px 8px 0 0; 
            }
            .brand { 
                display: flex; 
                align-items: center; 
                gap: 14px; 
            }
            .brand img { 
                width: 54px; 
                height: 54px; 
                border-radius: 8px; 
                object-fit: cover; 
                background: #fff; 
            }
            .brand-text { 
                display: flex; 
                flex-direction: column; 
            }
            .header h1 { 
                margin: 0; 
                font-size: 24px; 
                letter-spacing: .5px; 
            }
            .header h2 { 
                margin: 2px 0 4px; 
                font-size: 14px; 
                font-weight: normal; 
                opacity: .9; 
            }
            .header .fecha { 
                font-size: 12px; 
                opacity: .9; 
            }

            .section { 
                padding: 18px 24px; 
            }
            .resumen { 
                margin-bottom: 15px; 
                font-size: 11px; 
                padding: 8px; 
                background-color: #f8f9fa; 
                border-left: 4px solid #E38B29; 
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px; 
                font-size: 9px; 
                page-break-inside: auto; 
            }
            th { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #fff; 
                padding: 10px 8px; 
                text-align: left; 
                border: 1px solid #B97222; 
            }
            td { 
                border: 1px solid #dee2e6; 
                padding: 9px 8px; 
                vertical-align: top; 
            }
            tr:nth-child(even) { 
                background-color: #fdf8f2; 
            }

            .badge { 
                color: white; 
                padding: 3px 6px; 
                border-radius: 12px; 
                font-size: 8px; 
                font-weight: bold; 
            }
            .bg-secondary { background-color: #6c757d; }
            .bg-success { background-color: #28a745; }
            .bg-danger { background-color: #dc3545; }

            .footer { 
                text-align: center; 
                padding: 16px 24px; 
                color: #6c757d; 
                font-size: 12px; 
                border-top: 1px solid #dee2e6; 
                position: relative;
            }
            .paginacion {
                position: absolute;
                right: 24px;
                font-size: 10px;
            }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            
            /* Estilos para la paginación */
            .page-number:after {
                content: "Página " counter(page);
            }
            
            @page {
                margin: 20mm;
                @bottom-right {
                    content: "Página " counter(page) " de " counter(pages);
                    font-size: 10px;
                    color: #6c757d;
                }
                @bottom-left {
                    content: "Sistema de Gestión Tesoro D' MIMI";
                    font-size: 10px;
                    color: #6c757d;
                }
            }
        </style>
    </head>
    <body>
    <div class="container" id="contenido-pdf">
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Parámetros de Seguridad</h1>
                    <h2>Tesoro D' MIMI - Sistema de Gestión</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="resumen">
                <strong>Total de parámetros de seguridad configurados: ${parametros.length}</strong> | 
                <strong>Usuario generador: ${'<?= $usuario ?>'}</strong> |
                <strong>Rol: ${'<?= $rol_nombre ?>'}</strong>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Parámetro</th>
                        <th width="40%">Descripción</th>
                        <th width="15%">Valor Actual</th>
                        <th width="15%">Fecha Modificación</th>
                    </tr>
                </thead>
                <tbody>
                    ${parametros.map((parametro, index) => {
                        const fechaModificacion = parametro.FECHA_MODIFICACION ? 
                            new Date(parametro.FECHA_MODIFICACION).toLocaleDateString('es-ES') : 
                            'No modificado';
                        
                        return `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td><strong>${parametro.PARAMETRO || 'N/A'}</strong></td>
                            <td>${parametro.DESCRIPCION || 'Sin descripción'}</td>
                            <td>
                                <span class="badge bg-secondary">${parametro.VALOR || 'N/A'}</span>
                            </td>
                            <td>${fechaModificacion}</td>
                        </tr>
                        `;
                    }).join('')}
                    ${parametros.length === 0 ? 
                        '<tr><td colspan="5" style="text-align:center; padding:14px;">No hay parámetros de seguridad configurados.</td></tr>' : 
                        ''}
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <div>Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
    </div>
    </body>
    </html>
    `;

    const opt = {
        margin: [10, 10, 10, 10],
        filename: `parametros_seguridad_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { 
            type: 'jpeg', 
            quality: 0.98 
        },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            logging: false
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait',
            compress: true
        },
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'] 
        }
    };

    // Mostrar mensaje de generación
    mostrarAlerta('info', 'Generando PDF... Por favor espere.');
    
    html2pdf().set(opt).from(element).save().then(() => {
        mostrarAlerta('success', 'PDF generado y descargado correctamente.');
    }).catch(error => {
        console.error('Error generando PDF:', error);
        mostrarAlerta('error', 'Error al generar el PDF: ' + error.message);
    });
}

// Configurar el evento del botón de exportación
function configurarExportacionPDF() {
    const btnExportarPDF = document.getElementById('btnExportarPDFSeguridad');
    if (btnExportarPDF) {
        btnExportarPDF.addEventListener('click', exportarPDFSeguridad);
        console.log('✅ Botón de exportación PDF configurado');
    } else {
        console.log('⚠️ Botón de exportación PDF no encontrado');
    }
}
    </script>
    <!-- Agregar después de Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>
    