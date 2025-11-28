<?php
use App\config\SessionHelper;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$nombre = htmlspecialchars(
    SessionHelper::getUserFullName() ?? 'Invitado',
    ENT_QUOTES, 
    'UTF-8'
);

$rol = htmlspecialchars(
    $_SESSION['usuario_rol'] ?? $_SESSION['user_role'] ?? $_SESSION['rol'] ?? 'Sin rol',
    ENT_QUOTES, 
    'UTF-8'
);

// Obtener foto de perfil usando el helper
$fotoPerfil = SessionHelper::getUserPhoto();
$rutaFoto = "/sistema/public/uploads/profiles/" . $fotoPerfil;
$rutaDefault = "/sistema/src/Views/assets/img/perfil.jpg";

// Verificar si la foto existe, si no usar default
$rutaFinal = file_exists($_SERVER['DOCUMENT_ROOT'] . $rutaFoto) 
    ? $rutaFoto 
    : $rutaDefault;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $title ?? 'Tesoro D\' MIMI' ?></title>

  <!-- Favicon -->
  <link href="/sistema/src/Views/assets/img/Tesorodemimi.jpg" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="/sistema/src/Views/assets/css/layout.css" rel="stylesheet">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom Styles -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding-top: 70px;
    }

    /* === Modern Header Glass === */
    #header {
      backdrop-filter: blur(14px);
      background:linear-gradient(90deg, #D7A86E, #E38B29);
      color: #4B2E05;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0 2px 12px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 70px;
      z-index: 1000;
    }

    #header .logo {
      display: flex;
      align-items: center;
      text-decoration: none;
      font-weight: 600;
      color: #333;
      font-size: 1.3rem;
      letter-spacing: 0.5px;
      gap: 10px;
    }

    #header .logo span {
     color: #4B2E05;
}

    #header .logo img {
      height: 45px;
      width: 45px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    /* === Modern Nav === */
    .header-nav {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .header-nav .nav-icon {
      font-size: 1.3rem;
      color: #555;
      position: relative;
      transition: 0.3s;
    }

    .header-nav .nav-icon:hover {
      color: #007bff;
      transform: scale(1.15);
    }

    .badge-number {
      font-size: 0.65rem;
      position: absolute;
      top: -5px;
      right: -8px;
      background: #ff4757;
      border-radius: 50%;
      padding: 3px 6px;
      color: white;
      font-weight: 600;
    }

    /* === Profile === */
    .nav-profile {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .nav-profile img {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #007bff;
      transition: transform 0.2s, box-shadow 0.3s;
    }

    .nav-profile:hover img {
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
    }
    .nav-profile span {
      font-weight: 500;
      color: #333;
    }

    .nav-profile:hover span {
      color: #007bff;
    }

    /* Dropdowns */
    .dropdown-menu {
      background-color: #F8EDE3;
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 18px rgba(0,0,0,0.1);
      padding: 0.5rem;
      animation: fadeIn 0.25s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-item {
      border-radius: 8px;
      transition: 0.2s;
      color: 0.2s;
    }

    .dropdown-item:hover {
      background: #f0f4ff;
      color: #0d6efd;
    }

    /* === Sidebar Toggle === */
    #headerSidebarToggle {
      background: transparent;
      border: none;
      font-size: 1.5rem;
      color: #333;
      margin-right: 15px;
      transition: 0.3s;
    }

    #headerSidebarToggle:hover {
      color: #007bff;
      transform: rotate(90deg);
    }

    @media (max-width: 768px) {
      .nav-profile span {
        display: none;
      }
    }
  </style>
</head>

<body>

  <!-- ======= Header Moderno ======= -->
<header id="header" class="fixed-top d-flex justify-content-between align-items-center px-4">
    <div class="d-flex align-items-center">
      <button id="headerSidebarToggle" class="btn btn-light border-0 me-3"><i class="bi bi-list fs-4"></i></button>
      <a href=" " class="logo d-flex align-items-center text-decoration-none">
        <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Logo" style="height:45px;width:45px;border-radius:50%;margin-right:10px;">
        <span class="fw-semibold text-dark">Tesoro D' MIMI</span>
      </a>
    </div>

    <div class="dropdown">
      <button class="btn bg-transparent border-0 d-flex align-items-center dropdown-toggle" 
              id="userMenu" 
              data-bs-toggle="dropdown" 
              aria-expanded="false">
        <img src="<?= $rutaFinal ?>" 
             style="width:38px;height:38px;border-radius:50%;object-fit:cover;margin-right:8px;"
             alt="Foto de perfil de <?= $nombre ?>"
             onerror="this.src='/sistema/src/Views/assets/img/perfil.jpg'">
        <span><?= $nombre ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
        <li><h6 class="dropdown-header"><?= $nombre ?></h6></li>
        <li><a class="dropdown-item" href="/sistema/public/perfil">
          <i class="bi bi-person me-2 text-primary"></i>Mi Perfil</a>
        </li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalFotoPerfil">
          <i class="bi bi-camera me-2 text-success"></i>Cambiar Foto</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item d-flex align-items-center" href='/sistema/public/index.php?route=login'>
          <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
        </li>
      </ul>
    </div>
  </header>

  <!-- Modal para cambiar foto de perfil -->
  <div class="modal fade" id="modalFotoPerfil" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Foto de Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formFotoPerfil" enctype="multipart/form-data">
          <input type="hidden" id="id_usuario_foto" value="<?= SessionHelper::getUserId() ?>">
          <div class="text-center mb-3">
            <img id="previewFoto" src="<?= $rutaFinal ?>" 
                 class="rounded-circle" 
                 style="width:120px;height:120px;object-fit:cover;border:3px solid #dee2e6;"
                 onerror="this.src='/sistema/src/Views/assets/img/perfil.jpg'">
          </div>
          <div class="mb-3">
            <input type="file" class="form-control" id="fotoPerfilInput" name="foto_perfil" 
                   accept="image/jpeg,image/png,image/gif" 
                   onchange="previewFoto(this)">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Preview de imagen antes de subir
    // Preview de imagen
// Preview de imagen
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

    // Tu código JavaScript actual se mantiene
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (el) {
        new bootstrap.Dropdown(el);
      });

      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.getElementById('headerSidebarToggle');
      const toggleIcon = toggleBtn ? toggleBtn.querySelector('i') : null;

      if (sidebar && toggleBtn) {
        const isMobile = () => window.innerWidth < 992;

        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
          sidebar.classList.add('collapsed');
          if (toggleIcon) toggleIcon.classList.replace('bi-list', 'bi-grid-1x2');
        }

        toggleBtn.addEventListener('click', function () {
          if (isMobile()) {
            sidebar.classList.toggle('show');
          } else {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);

            if (toggleIcon) {
              toggleIcon.classList.toggle('bi-list', !isCollapsed);
              toggleIcon.classList.toggle('bi-grid-1x2', isCollapsed);
            }
          }
        });

        window.addEventListener('resize', () => {
          if (isMobile()) {
            sidebar.classList.remove('collapsed');
          } else {
            sidebar.classList.remove('show');
          }
        });
      }
    });



  </script>


</body>