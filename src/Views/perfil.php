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

// Obtener el rol directamente de la sesión (ya está disponible según tu authController)
$rolSesion = $_SESSION['rol'] ?? $_SESSION['usuario_rol'] ?? null;

// Si no se pudieron obtener los datos, usar valores por defecto
if (!$perfilData) {
    $perfilData = [
        'ID_USUARIO' => $idUsuario,
        'USUARIO' => $_SESSION['user_usuario'] ?? 'N/A',
        'NOMBRE_USUARIO' => $_SESSION['user_name'] ?? 'Invitado',
        'CORREO_ELECTRONICO' => 'No especificado',
        'ROL' => $rolSesion ?? 'Sin rol', // ← USAR EL ROL DE LA SESIÓN
        'ESTADO_USUARIO' => 'Desconocido',
        'FECHA_CREACION' => date('Y-m-d H:i:s'),
        'FECHA_ULTIMA_CONEXION' => date('Y-m-d H:i:s'),
        'FOTO_PERFIL' => 'perfil.jpg'
    ];
}

// SI EL ROL VIENE VACÍO EN LOS DATOS DEL PERFIL, USAR EL DE LA SESIÓN
if (empty($perfilData['ROL']) || $perfilData['ROL'] === 'Sin rol') {
    $perfilData['ROL'] = $rolSesion ?? 'Sin rol';
}

// DEBUG: Verificar qué datos tenemos
error_log("🎯 PERFIL DATA - Rol: " . ($perfilData['ROL'] ?? 'No disponible'));
error_log("🎯 SESIÓN - rol: " . ($_SESSION['rol'] ?? 'No en sesión'));
error_log("🎯 SESIÓN - usuario_rol: " . ($_SESSION['usuario_rol'] ?? 'No en sesión'));

// Preparar datos para mostrar
$nombre = htmlspecialchars($perfilData['NOMBRE_USUARIO'], ENT_QUOTES, 'UTF-8');
$rol = htmlspecialchars($perfilData['ROL'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($perfilData['CORREO_ELECTRONICO'], ENT_QUOTES, 'UTF-8');
$usuario_login = htmlspecialchars($perfilData['USUARIO'], ENT_QUOTES, 'UTF-8');
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
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Mi Perfil - Tesoro D' MIMI</title>
  
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    .profile-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 15px;
      color: white;
    }
    .profile-img {
      width: 150px;
      height: 150px;
      border: 5px solid white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .info-card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-item {
      padding: 12px 0;
      border-bottom: 1px solid #eee;
    }
    .info-item:last-child {
      border-bottom: none;
    }
    .info-label {
      font-weight: 600;
      color: #495057;
    }
    .info-value {
      color: #6c757d;
    }
    .stats-badge {
      background: linear-gradient(45deg, #FFD700, #FFA500);
      color: #4B2E05;
      font-weight: 600;
    }
    .badge-estado {
      font-size: 0.8em;
      padding: 5px 10px;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <?php include 'partials/header.php'; ?>

  <!-- Sidebar -->
  <?php include 'partials/sidebar.php'; ?>

  <main id="main" class="main" style="margin-left: 240px; padding: 20px; margin-top: 70px;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="p-3">
            <h2 class="fw-bold text-dark mb-4">
              <i class="bi bi-person-circle me-2"></i>Mi Perfil
            </h2>
            
            <div class="row">
              <!-- Columna izquierda - Información de perfil -->
              <div class="col-md-4 mb-4">
                <div class="card profile-card p-4 text-center">
                  <div class="card-body">
                    <img src="<?= $rutaFinal ?>" 
                         class="profile-img rounded-circle mb-3" 
                         alt="Foto de perfil"
                         onerror="this.src='/sistema/src/Views/assets/img/perfil.jpg'">
                    
                    <h4 class="mb-1"><?= $nombre ?></h4>
                    <p class="mb-2 opacity-75">
                      <i class="bi bi-person-badge me-1"></i><?= $rol ?>
                    </p>
                    
                    <button class="btn btn-light btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalFotoPerfil">
                      <i class="bi bi-camera me-1"></i>Cambiar Foto
                    </button>
                  </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="card info-card mt-4">
                  <div class="card-body">
                    <h6 class="card-title text-primary mb-3">
                      <i class="bi bi-graph-up me-2"></i>Resumen
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                      <span>Estado:</span>
                      <?php 
                      $badgeClass = 'bg-success';
                      if ($estado == 'Inactivo') $badgeClass = 'bg-secondary';
                      if ($estado == 'Bloqueado') $badgeClass = 'bg-danger';
                      if ($estado == 'Nuevo') $badgeClass = 'bg-warning';
                      ?>
                      <span class="badge <?= $badgeClass ?> badge-estado"><?= $estado ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span>Miembro desde:</span>
                      <span class="text-muted"><?= date('Y', strtotime($perfilData['FECHA_CREACION'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                      <span>Último acceso:</span>
                      <span class="text-muted"><?= $fechaUltimaConexion ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Columna derecha - Información de solo lectura -->
              <div class="col-md-8">
                <div class="card info-card">
                  <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                      <i class="bi bi-info-circle me-2 text-primary"></i>
                      Información Personal
                    </h5>
                  </div>
                  <div class="card-body">
                    <!-- Información Básica -->
                    <div class="info-item">
                      <div class="row">
                        <div class="col-sm-4">
                          <span class="info-label">
                            <i class="bi bi-person me-2 text-primary"></i>Nombre Completo
                          </span>
                        </div>
                        <div class="col-sm-8">
                          <span class="info-value"><?= $nombre ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="info-item">
                      <div class="row">
                        <div class="col-sm-4">
                          <span class="info-label">
                            <i class="bi bi-envelope me-2 text-primary"></i>Correo Electrónico
                          </span>
                        </div>
                        <div class="col-sm-8">
                          <span class="info-value"><?= $email ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="info-item">
                      <div class="row">
                        <div class="col-sm-4">
                          <span class="info-label">
                            <i class="bi bi-person-badge me-2 text-primary"></i>Rol
                          </span>
                        </div>
                        <div class="col-sm-8">
                          <span class="info-value"><?= $rol ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="info-item">
                      <div class="row">
                        <div class="col-sm-4">
                          <span class="info-label">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Usuario
                          </span>
                        </div>
                        <div class="col-sm-8">
                          <span class="info-value"><?= $usuario_login ?></span>
                        </div>
                      </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="mt-4 pt-3 border-top">
                      <h6 class="text-primary mb-3">
                        <i class="bi bi-shield-check me-2"></i>Información del Sistema
                      </h6>
                      
                      <div class="info-item">
                        <div class="row">
                          <div class="col-sm-4">
                            <span class="info-label">
                              <i class="bi bi-calendar me-2 text-success"></i>Fecha de Creación
                            </span>
                          </div>
                          <div class="col-sm-8">
                            <span class="info-value"><?= $fechaCreacion ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="info-item">
                        <div class="row">
                          <div class="col-sm-4">
                            <span class="info-label">
                              <i class="bi bi-clock me-2 text-success"></i>Última Conexión
                            </span>
                          </div>
                          <div class="col-sm-8">
                            <span class="info-value"><?= $fechaUltimaConexion ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="info-item">
                        <div class="row">
                          <div class="col-sm-4">
                            <span class="info-label">
                              <i class="bi bi-lock me-2 text-success"></i>Estado de Cuenta
                            </span>
                          </div>
                          <div class="col-sm-8">
                            <span class="badge <?= $badgeClass ?> badge-estado"><?= $estado ?></span>
                          </div>
                        </div>
                      </div>

                      <?php if ($perfilData['HABILITAR_2FA']): ?>
                      <div class="info-item">
                        <div class="row">
                          <div class="col-sm-4">
                            <span class="info-label">
                              <i class="bi bi-shield-check me-2 text-success"></i>Autenticación 2FA
                            </span>
                          </div>
                          <div class="col-sm-8">
                            <span class="badge bg-info badge-estado">Habilitada</span>
                          </div>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>

                    <div class="mt-4">
                      <a href="/sistema/public/inicio" class="btn btn-primary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Dashboard
                      </a>
                      <a href="/sistema/public/cambiar-password" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-key me-2"></i>Cambiar Contraseña
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal para cambiar foto -->
  <div class="modal fade" id="modalFotoPerfil" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cambiar Foto de Perfil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formFotoPerfil" enctype="multipart/form-data">
            <input type="hidden" id="id_usuario_foto" value="<?= $idUsuario ?>">
            <div class="text-center mb-3">
              <img id="previewFotoPerfil" src="<?= $rutaFinal ?>" 
                   class="rounded-circle" 
                   style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;"
                   onerror="this.src='/sistema/src/Views/assets/img/perfil.jpg'">
            </div>
            <div class="mb-3">
              <input type="file" class="form-control" id="fotoPerfilInput" name="foto_perfil" 
                     accept="image/jpeg,image/png,image/gif" 
                     onchange="previewFotoPerfil(this)">
              <div class="form-text">Formatos: JPG, PNG, GIF. Máx: 2MB</div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="subirFotoPerfil()">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Preview de imagen para el modal del perfil
    function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewFoto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// FUNCIÓN PRINCIPAL SIMPLIFICADA Y CORREGIDA
function subirFotoPerfil() {
    const formData = new FormData();
    const fileInput = document.getElementById('fotoPerfilInput');
    const idUsuario = '<?= SessionHelper::getUserId() ?? '' ?>';
    
    console.log('🔄 Iniciando subida de foto...');

    if (!fileInput.files[0]) {
        Swal.fire('Error', 'Por favor selecciona una imagen', 'error');
        return;
    }

    // Validaciones
    if (fileInput.files[0].size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'La imagen no debe superar los 2MB', 'error');
        return;
    }

    const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!tiposPermitidos.includes(fileInput.files[0].type)) {
        Swal.fire('Error', 'Solo se permiten imágenes JPEG, PNG o GIF', 'error');
        return;
    }

    formData.append('foto_perfil', fileInput.files[0]);
    formData.append('id_usuario', idUsuario);

    // Mostrar loading
    Swal.fire({
        title: 'Subiendo imagen...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const url = '/sistema/public/index.php?route=user&caso=subir-foto-perfil';

    // 🔥 USAR response.json() DIRECTAMENTE
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // 🔥 PRIMERO VERIFICAR SI LA RESPUESTA ES JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, obtener el texto para debug
            return response.text().then(text => {
                throw new Error(`El servidor devolvió: ${text.substring(0, 100)}`);
            });
        }
    })
    .then(data => {
        Swal.close();
        console.log('✅ Respuesta del servidor:', data);
        
        // 🔥 VERIFICAR ÉXITO CON DIFERENTES FORMATOS POSIBLES
        if (data.success === true || data.status === 200 || data.status === '200') {
            // Cerrar el modal de Bootstrap
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalFotoPerfil'));
            if (modal) {
                modal.hide();
            }
            
            // Limpiar el input de archivo
            fileInput.value = '';
            
            // Mostrar éxito
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message || 'Foto actualizada correctamente',
                confirmButtonText: 'OK',
                didClose: () => {
                    // Recargar la página para ver los cambios
                    location.reload();
                }
            });
            
        } else {
            // Manejar error del servidor
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al procesar la foto',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('❌ Error:', error);
        
        // Mostrar error específico
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Error de conexión',
            confirmButtonText: 'OK'
        });
    });
}

    // Ajustar margen cuando el sidebar está colapsado
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      
      if (sidebar && main) {
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
              if (sidebar.classList.contains('collapsed')) {
                main.style.marginLeft = '70px';
              } else {
                main.style.marginLeft = '240px';
              }
            }
          });
        });
        
        observer.observe(sidebar, { attributes: true });
      }
    });
  </script>
</body>
</html>