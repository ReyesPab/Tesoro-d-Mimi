<?php

require 'vendor/autoload.php';

use App\db\connectionDB;
use App\config\errorlogs;

errorlogs::activa_error_logs();

$host = 'mysql:host=127.0.0.1;port=3306;dbname=api_gestion';
$user = 'root';
$pass = ''; // Contraseña correcta

connectionDB::inicializar($host, $user, $pass);

try {
    $con = connectionDB::getConnection();
    echo "Conexión exitosa a la base de datos.";
} catch (\PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}



<?php
// Verificar si hay sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Nombre'])) {
    header('Location: /gestion/public/login');
    exit;
}

$rol = $_SESSION['Rol'] ?? '';
?>

<!-- Botón para ocultar/mostrar sidebar -->
<button id="sidebarToggle" class="btn btn-sm btn-outline-secondary position-fixed" 
        style="z-index: 1000; left: 260px; top: 10px; transition: left 0.3s;">
    <i class="bi bi-chevron-double-left"></i>
</button>

<style>
/* Estilos para el layout principal */
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.header {
  width: 100%;
  z-index: 999;
}

.sidebar {
  width: 280px;
  position: fixed;
  top: 60px; /* Ajusta según la altura de tu header */
  left: 0;
  bottom: 0;
  z-index: 998;
  transition: all 0.3s;
  overflow-y: auto;
  background: #fff;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.sidebar.collapsed {
  left: -280px;
}

#main {
  margin-top: 60px; /* Ajusta según la altura de tu header */
  margin-left: 280px;
  padding: 20px;
  flex: 1;
  transition: all 0.3s;
}

#main.expanded {
  margin-left: 0;
}

#sidebarToggle {
  z-index: 999;
  left: 260px;
  top: 70px;
  transition: all 0.3s;
}

.sidebar.collapsed + #sidebarToggle {
  left: 10px;
}

.sidebar.collapsed + #sidebarToggle i {
  transform: rotate(180deg);
}

/* Responsive */
@media (max-width: 992px) {
  .sidebar {
    left: -280px;
  }
  
  .sidebar.show {
    left: 0;
  }
  
  #main {
    margin-left: 0;
  }
  
  #sidebarToggle {
    display: block !important;
  }
}
</style>


<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
  <li class="nav-item">
      <a class="nav-link" href="/gestion/public/menu">
        <i class="bi bi-grid"></i>
        <span>Admin</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="/gestion/public/menu_usuarios">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#usuarios-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Usuarios</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="usuarios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_usuarios">
            <i class="bi bi-circle"></i><span>Listar Usuarios</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/registro">
            <i class="bi bi-circle"></i><span>Crear Usuario</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/usuarios_asignar">
            <i class="bi bi-circle"></i><span>Asignar Roles</span>
          </a>
        </li>
      </ul>
    </li>


    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#servicios-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-collection"></i><span>Servicios</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="servicios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/servicios">
            <i class="bi bi-circle"></i><span>Listar Servicios</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/servicios_crear">
            <i class="bi bi-circle"></i><span>Crear Servicio</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/usuarios_asignar">
            <i class="bi bi-circle"></i><span>Asignar Servicios</span>
          </a>
        </li>
      </ul>
    </li>


    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#clinicas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-building"></i><span>Departamentos</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="clinicas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/laboratorio">
            <i class="bi bi-circle"></i><span>Crear Laboratorio</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/lista_laboratorios">
            <i class="bi bi-circle"></i><span>Listar Departamentos</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#citas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-calendar-check"></i><span>Citas</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="citas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_citas">
            <i class="bi bi-circle"></i><span>Listar Citas</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/crear_citas">
            <i class="bi bi-circle"></i><span>Crear Cita</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/citas_calendario">
            <i class="bi bi-circle"></i><span>Calendario</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Nuevo item para Historiales Médicos -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#historiales-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-medical"></i><span>Historiales Médicos</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="historiales-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_historial_medico">
            <i class="bi bi-circle"></i><span>Listar Historiales</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/crear_historial_medico">
            <i class="bi bi-circle"></i><span>Crear Historial</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Nuevo item para Recetas -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#recetas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>Recetas</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="recetas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_recetas">
            <i class="bi bi-circle"></i><span>Listar Recetas</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/crear_receta">
            <i class="bi bi-circle"></i><span>Crear Receta</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Nuevo Item para Informes Médicos -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#informes-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-medical"></i><span>Informes Médicos</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="informes-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_informes_medicos">
            <i class="bi bi-circle"></i><span>Listar Informes</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/crear_informe_medico">
            <i class="bi bi-circle"></i><span>Crear Informe</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Item para Resultados Médicos -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#resultados-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-clipboard2-pulse"></i><span>Resultados Médicos</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="resultados-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href="/gestion/public/lista_resultados_examenes">
            <i class="bi bi-circle"></i><span>Listar Resultados</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/crear_resultado_examen">
            <i class="bi bi-circle"></i><span>Crear Resultado</span>
          </a>
        </li>
        <li>
          <a href="/gestion/public/resultados_pendientes">
            <i class="bi bi-circle"></i><span>Resultados Pendientes</span>
          </a>
        </li>
      </ul>
    </li>

     <!-- Nuevo Item para Nosotros -->
     <li class="nav-item">
      <a class="nav-link" href="/gestion/public/nosotros">
        <i class="bi bi-info-circle"></i>
        <span>Nosotros</span>
      </a>
    </li>

    <?php if ($rol === 'Doctor' || $rol === 'Administrador' || $rol === 'Recepcionista' || $rol === 'Paciente'): ?>
    <!-- Nuevo Item para Contacto -->
    <li class="nav-item">
      <a class="nav-link" href="/gestion/public/contacto">
        <i class="bi bi-envelope"></i>
        <span>Contacto</span>
      </a>
    </li>
    <?php endif; ?>

    <?php if ($rol === 'Administrador'): ?>
    <li class="nav-item">
  <a class="nav-link" href="/gestion/public/crear_notificaciones">
    <i class="bi bi-bell"></i>
    <span>Crear Notificaciones</span>
    <span id="contadorNotificaciones" class="badge bg-danger rounded-pill ms-auto"></span>
  </a>
</li>
<?php endif; ?>

<?php if ($rol === 'Administrador'): ?>
    <li class="nav-item">
      <a class="nav-link" href="/gestion/public/listar_acciones">
        <i class="bi bi-list-check"></i>
        <span>Registro de Acciones</span>
      </a>
    </li>
  </ul>
</aside>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('main');
    
    // Verificar preferencia guardada
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Aplicar estado inicial
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        sidebarToggle.innerHTML = '<i class="bi bi-chevron-double-right"></i>';
    }
    
    // Manejar el clic en el botón
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        // Cambiar icono
        const icon = sidebarToggle.querySelector('i');
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.remove('bi-chevron-double-left');
            icon.classList.add('bi-chevron-double-right');
        } else {
            icon.classList.remove('bi-chevron-double-right');
            icon.classList.add('bi-chevron-double-left');
        }
        
        // Guardar preferencia
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Manejar cambios de tamaño de pantalla
    window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            // Restaurar según preferencia del usuario
            if (localStorage.getItem('sidebarCollapsed') !== 'true') {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        }
    });
});
</script>