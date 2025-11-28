<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tesoro D' MIMI - Dashboard Moderno</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Estilos Glassmorphism -->
  <link href="/sistema/src/Views/assets/css/modern-dashboard.css" rel="stylesheet">
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="text-center mb-4">
      <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" width="80" height="80" style="border-radius:50%; border:2px solid rgba(255,255,255,0.3)">
      <h3 style="margin-top:10px; font-weight:600;">Tesoro D' MIMI</h3>
    </div>

    <nav>
      <ul style="list-style:none; padding-left:0;">
        <li class="mb-3"><a href="#" style="color:white; text-decoration:none;"><i class="bi bi-house"></i> <span style="margin-left:10px;">Inicio</span></a></li>
        <li class="mb-3"><a href="#" style="color:white; text-decoration:none;"><i class="bi bi-box-seam"></i> <span style="margin-left:10px;">Inventario</span></a></li>
        <li class="mb-3"><a href="#" style="color:white; text-decoration:none;"><i class="bi bi-people"></i> <span style="margin-left:10px;">Usuarios</span></a></li>
        <li class="mb-3"><a href="#" style="color:white; text-decoration:none;"><i class="bi bi-bar-chart"></i> <span style="margin-left:10px;">Reportes</span></a></li>
        <li class="mb-3"><a href="#" style="color:white; text-decoration:none;"><i class="bi bi-gear"></i> <span style="margin-left:10px;">Configuración</span></a></li>
      </ul>
    </nav>
  </aside>

  <!-- Header -->
  <header class="header-glass">
    <h2 style="font-weight:600;">Panel Principal</h2>

    <div class="d-flex align-items-center">
      <i class="bi bi-bell" style="font-size:1.4rem; margin-right:20px; cursor:pointer;"></i>

      <div class="dropdown">
        <a href="#" data-bs-toggle="dropdown" class="d-flex align-items-center" style="text-decoration:none; color:white;">
          <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="perfil" width="40" height="40" style="border-radius:50%; margin-right:10px;">
          <span><?= $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? 'Usuario' ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end mt-3">
          <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Mi Perfil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="/sistema/public/index.php?route=login"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <main class="main-content">
    <div class="container-fluid">
      <div class="row" style="display:flex; gap:1.5rem; flex-wrap:wrap;">
        
        <div class="glass-card" style="flex:1;">
          <h4>Inventario total</h4>
          <p>Productos en stock: <strong>125</strong></p>
        </div>

        <div class="glass-card" style="flex:1;">
          <h4>Usuarios activos</h4>
          <p><strong>8</strong> usuarios registrados</p>
        </div>

        <div class="glass-card" style="flex:1;">
          <h4>Ventas del día</h4>
          <p>Lps. <strong>2,540.00</strong></p>
        </div>

      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
