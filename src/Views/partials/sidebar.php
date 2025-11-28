<?php
use App\config\SessionHelper;
use App\config\PermisosHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$nombre = $_SESSION['user_usuario'] ?? 'Usuario';
$rol = $_SESSION['usuario_rol'] ?? '';
$userId = SessionHelper::getUserId();



// Obtener menú según permisos del usuario
$menuItems = [];
try {
    $menuItems = permisosModel::obtenerMenuUsuario($userId);
} catch (Exception $e) {
    error_log("Error al cargar menú: " . $e->getMessage());
    // Si hay error, mostrar menú básico
    $menuItems = [
        ['OBJETO' => 'INICIO', 'RUTA' => '/sistema/public/inicio', 'ICONO' => 'bi bi-house-door']
    ];
}

// Obtener foto de perfil usando el helper
$fotoPerfil = SessionHelper::getUserPhoto();
$rutaFoto = "/sistema/public/uploads/profiles/" . $fotoPerfil;
$rutaDefault = "/sistema/src/Views/assets/img/perfil.jpg";

// Verificar si la foto existe
$rutaFinal = file_exists($_SERVER['DOCUMENT_ROOT'] . $rutaFoto) 
    ? $rutaFoto 
    : $rutaDefault;

// Función para verificar si un objeto está en el menú permitido
function tienePermisoMenu($objeto, $menuItems) {
    foreach ($menuItems as $item) {
        if ($item['OBJETO'] === $objeto && $item['PERMISO_CONSULTAR'] == 1) {
            return true;
        }
    }
    return false;
}
?>

<aside id="sidebar" class="sidebar">
  <div class="sidebar-header text-center py-3">
    <img src="<?= $rutaFinal ?>" alt="Avatar" class="avatar" 
         onerror="this.src='/sistema/src/Views/assets/img/perfil.jpg'">
    <h6 class="mt-2 mb-0 fw-semibold text-white"><?= htmlspecialchars($nombre) ?></h6>
    <?php if ($rol): ?>
      <p class="text-muted small mb-0"><?= htmlspecialchars($rol) ?></p>
    <?php endif; ?>
  </div>

  <ul class="nav flex-column px-2" id="sidebar-nav">
    <!-- Inicio - siempre visible -->
    <li class="nav-item">
      <a href="/sistema/public/inicio" class="nav-link active" onclick="registrarNavegacion('inicio')">
        <i class="bi bi-house-door"></i><span>Inicio</span>
      </a>
    </li>

    <!-- Módulo Usuarios - solo si tiene permiso -->
    <?php if (tienePermisoMenu('GESTION_USUARIOS', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#usuarios-nav" onclick="ocultarOtrosMenus('usuarios-nav')">
        <i class="bi bi-people"></i><span>Usuarios</span><i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <ul id="usuarios-nav" class="collapse nav-submenu">
        <?php if (tienePermisoMenu('GESTION_USUARIOS', $menuItems)): ?>
        <li><a href="/sistema/public/gestion-usuarios" onclick="registrarNavegacion('gestion-usuarios')"><i class="bi bi-circle"></i>Registrados</a></li>
        <?php endif; ?>
        
    <?php if (tienePermisoMenu('GESTION_ROLES', $menuItems) || SessionHelper::isAdmin()): ?>
    <li><a href="/sistema/public/gestion-roles" onclick="registrarNavegacion('gestion-roles')"><i class="bi bi-circle"></i>Gestión de Roles</a></li>
    <?php endif; ?>

        <?php if (tienePermisoMenu('GESTION_PERMISOS', $menuItems)): ?>
        <li><a href="/sistema/public/permisos-usuarios" onclick="registrarNavegacion('permisos-usuarios')"><i class="bi bi-circle"></i>Permisos</a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <!-- Módulo Compras - solo si tiene permiso -->
    <?php if (tienePermisoMenu('compras', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#compras-nav" onclick="ocultarOtrosMenus('compras-nav')">
        <i class="bi bi-basket"></i><span>Compras</span><i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <ul id="compras-nav" class="collapse nav-submenu">
        <?php if (tienePermisoMenu('GESTION_PROVEEDORES', $menuItems)): ?>
        <li><a href="/sistema/public/gestion-proveedores" onclick="registrarNavegacion('gestion-proveedores')"><i class="bi bi-circle"></i>Proveedores</a></li>
        <?php endif; ?>
        
        <?php if (tienePermisoMenu('GESTION_PRODUCTOS_PROVEEDOR', $menuItems)): ?>
        <li><a href="/sistema/public/gestion-productos-proveedor" onclick="registrarNavegacion('gestion-productos-proveedor')"><i class="bi bi-circle"></i>Productos Proveedor</a></li>
        <?php endif; ?>
        
        <?php if (tienePermisoMenu('ÓRDENES_DE_COMPRAS', $menuItems)): ?>
        <li><a href="/sistema/public/consultar-ordenes-pendientes" onclick="registrarNavegacion('consultar-ordenes-pendientes')"><i class="bi bi-circle"></i>Órdenes de Compras</a></li>
        <?php endif; ?>
        
        <?php if (tienePermisoMenu('CONSULTAR_COMPRAS', $menuItems)): ?>
        <li><a href="/sistema/public/consultar-compras" onclick="registrarNavegacion('consultar-compras')"><i class="bi bi-circle"></i>Compras realizadas</a></li>
        <?php endif; ?>
      </ul>
    </li>
    <?php endif; ?>

    <!-- Módulo Producción - solo si tiene permiso -->
    <?php if (tienePermisoMenu('Producción', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#produccion-nav" onclick="ocultarOtrosMenus('produccion-nav')">
        <i class="bi bi-cash-stack"></i><span>Producción</span><i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <ul id="produccion-nav" class="collapse nav-submenu">
        <?php if (tienePermisoMenu('GESTION_PRODUCCION', $menuItems)): ?>
        <li><a href="/sistema/public/gestion-produccion" onclick="registrarNavegacion('gestion-produccion')"><i class="bi bi-circle"></i>Gestión de producción</a></li> 
        <?php endif; ?>
        
        <?php if (tienePermisoMenu('VER_RECETAS', $menuItems)): ?>
        <li><a href="/sistema/public/ver-recetas" onclick="registrarNavegacion('ver-recetas')"><i class="bi bi-circle"></i>Ver recetas</a></li>
        <?php endif; ?>
        
        <li><a href="/sistema/public/perdidas-produccion" onclick="registrarNavegacion('perdidas-produccion')"><i class="bi bi-circle"></i>Pérdidas</a></li>
      </ul>
    </li>
    <?php endif; ?>

    <!-- Módulo Inventarios - solo si tiene permiso -->
    <?php if (tienePermisoMenu('inventario', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#inventarios-nav" onclick="ocultarOtrosMenus('inventarios-nav')">
        <i class="bi bi-box-seam"></i><span>Inventarios</span><i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <ul id="inventarios-nav" class="collapse nav-submenu">
        <li><a href='/sistema/public/gestion-inventario' onclick="registrarNavegacion('gestion-inventario')"><i class="bi bi-circle"></i>Inventario Materia Prima</a></li>
        <li><a href='/sistema/public/gestion-inventario-productos' onclick="registrarNavegacion('gestion-inventario-productos')"><i class="bi bi-circle"></i>Productos</a></li>
      </ul>
    </li>
    <?php endif; ?>

    <!-- Módulo Ventas - solo si tiene permiso -->
    <?php if (tienePermisoMenu('VENTAS', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <button class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#ventas-nav" onclick="ocultarOtrosMenus('ventas-nav')">
        <i class="bi bi-cash-coin"></i><span>Ventas</span><i class="bi bi-chevron-down ms-auto"></i>
      </button>
      <ul id="ventas-nav" class="collapse nav-submenu">
        <!--<li><a href='/sistema/public/registrar-venta' onclick="registrarNavegacion('registrar-venta')"><i class="bi bi-circle"></i>Registrar Venta</a></li> -->
        <li><a href='/sistema/public/consultar-ventas' onclick="registrarNavegacion('consultar-ventas')"><i class="bi bi-circle"></i>Consultar Ventas</a></li>
        <li><a href='/sistema/public/gestion-clientes' onclick="registrarNavegacion('gestion-clientes')"> <i class="bi bi-circle"></i>Gestionar Clientes</a></li>
      </ul>
    </li>
    <?php endif; ?>

    <!-- Bitácora - solo si tiene permiso -->
    <?php if (tienePermisoMenu('BITACORA', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item mt-2">
      <a href="/sistema/public/bitacora" class="nav-link" onclick="registrarNavegacion('bitacora')">
        <i class="bi bi-list-check"></i><span>Bitácora</span>
      </a>
    </li>
    <?php endif; ?>

    <?php if (tienePermisoMenu('BITACORA', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item mt-2">
      <a href="/sistema/public/dashboar" class="nav-link" onclick="registrarNavegacion('permisos-usuarios')">
        <i class="bi bi-house-door"></i><span>Nosotros</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Respaldos - solo si tiene permiso -->
    <?php if (tienePermisoMenu('RESPALDOS_SISTEMA', $menuItems) || SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <a href="/sistema/public/gestion-backups" class="nav-link" onclick="registrarNavegacion('gestion-backups')">
        <i class="bi bi-database"></i><span>Respaldos</span>
      </a>
    </li>
    <?php endif; ?>

    <!-- Si no tiene ningún permiso, mostrar mensaje -->
    <?php if (empty($menuItems) && !SessionHelper::isAdmin()): ?>
    <li class="nav-item">
      <a href="#" class="nav-link text-muted">
        <i class="bi bi-info-circle"></i><span>Sin permisos asignados</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>
</aside>

<script>
// Variable global para controlar registros en curso
let registroEnCurso = false;

// Función para ocultar otros menús cuando se abre uno
function ocultarOtrosMenus(menuActual) {
    const todosLosMenus = [
        'usuarios-nav', 
        'compras-nav', 
        'produccion-nav', 
        'inventarios-nav', 
        'ventas-nav'
    ];
    
    // Ocultar todos los menús excepto el actual
    todosLosMenus.forEach(menu => {
        if (menu !== menuActual) {
            const elemento = document.getElementById(menu);
            if (elemento) {
                // Usar Bootstrap Collapse para cerrar
                const bsCollapse = new bootstrap.Collapse(elemento, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        }
    });
}

// Función para registrar navegación con mejor control de duplicados
function registrarNavegacion(pagina) {
    // Si ya hay un registro en curso, ignorar
    if (registroEnCurso) {
        console.log('⏱️  Registro en curso, ignorando duplicado');
        return;
    }
    
    // Prevenir registros duplicados usando sessionStorage
    const claveRegistro = `ultimoRegistro_${pagina}`;
    const ahora = Date.now();
    const ultimoRegistro = sessionStorage.getItem(claveRegistro);
    
    // Si se registró la misma página en los últimos 2 segundos, ignorar
    if (ultimoRegistro && (ahora - parseInt(ultimoRegistro)) < 2000) {
        console.log('⏱️  Evitando registro duplicado reciente');
        return;
    }
    
    // Marcar como en curso
    registroEnCurso = true;
    sessionStorage.setItem(claveRegistro, ahora.toString());
    
    console.log('📝 Registrando navegación a:', pagina);
    
    fetch('/sistema/public/index.php?route=bitacora&caso=registrar-navegacion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            pagina: pagina,
            accion: 'ACCESO'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 200) {
            console.log('✅ Acceso registrado correctamente');
        }
    })
    .catch(error => {
        console.error('❌ Error registrando acceso:', error);
    })
    .finally(() => {
        // Liberar el bloqueo después de un tiempo
        setTimeout(() => {
            registroEnCurso = false;
        }, 1000);
    });
}

// SOLO UN event listener bien controlado
document.addEventListener('DOMContentLoaded', function() {
    const enlaces = document.querySelectorAll('#sidebar-nav a[href]');
    
    enlaces.forEach(enlace => {
        enlace.addEventListener('click', function(e) {
            // Obtener el nombre de la página desde la URL
            const href = this.getAttribute('href');
            const pagina = href.split('/').pop().replace('.php', '') || 'inicio';
            
            console.log('🖱️ Clic detectado en:', pagina);
            
            // Registrar navegación (no prevenimos el comportamiento por defecto)
            registrarNavegacion(pagina);
            
            // Permitir que la navegación continúe normalmente
            // No usar preventDefault() para no romper la navegación
        });
    });
});

// Limpiar cualquier event listener duplicado que pueda existir
// y eliminar el beforeunload problemático
window.removeEventListener('beforeunload', registrarNavegacion);
</script>

<!-- Mantener los mismos estilos -->
<style>
.sidebar {
  position: fixed;
  top: 70px;
  left: 0;
  height: 100vh;
  width: 240px;
  background-color: #4B2E05; 
  backdrop-filter: blur(12px);
  color: white;
  transition: all 0.3s ease;
  overflow-y: auto;
  box-shadow: 3px 0 20px rgba(0,0,0,0.25);
  z-index: 998;
}
.sidebar.collapsed { width: 70px; }

.sidebar-header {
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

#sidebar a {
  color: #F8EDE3;
  text-decoration: none;
}

#sidebar a:hover,
#sidebar .active {
  background-color: #D7A86E; /* naranja suave */
  color: #4B2E05; /* texto oscuro */
}

.sidebar .avatar {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
  transition: 0.3s;
}
.sidebar .avatar:hover {
  box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.6);
  transform: scale(1.05);
}

.sidebar .nav-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  color: rgba(255,255,255,0.8);
  font-weight: 500;
  border-radius: 10px;
  margin-bottom: 4px;
  transition: background 0.3s, color 0.3s;
}
.sidebar .nav-link:hover {
  background: rgba(0,123,255,0.2);
  color: #fff;
}

.sidebar .nav-link i {
  font-size: 1.2rem;
  transition: transform 0.3s;
}
.sidebar .nav-link:hover i {
  transform: scale(1.15);
  color: #0d6efd;
}

.sidebar .nav-submenu {
  list-style: none;
  padding-left: 30px;
  margin: 0;
}
.sidebar .nav-submenu a {
  color: rgba(255,255,255,0.7);
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 8px;
  transition: 0.3s;
}
.sidebar .nav-submenu a:hover {
  background: rgba(0,123,255,0.15);
  color: white;
}
.sidebar .nav-submenu i {
  font-size: 0.7rem;
}

/* scrollbar fino */
.sidebar::-webkit-scrollbar {
  width: 6px;
}
.sidebar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.15);
  border-radius: 10px;
}

/* responsive */
@media (max-width: 992px) {
  .sidebar {
    transform: translateX(-100%);
  }
  .sidebar.show {
    transform: translateX(0);
  }
}
</style>