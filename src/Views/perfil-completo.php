<?php
// src/Views/perfil-completo.php
if (!isset($_SESSION['id_usuario'])) {
    header('Location: /sistema/public/login');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil Completo - Tesoro de MIMI</title>
    
    <!-- Favicons -->
    <link href="/sistema/src/Views/assets/img/Tesorodemimi.jpg" rel="icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    
    <!-- Vendor CSS Files -->
    <link href="/sistema/src/Views/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/sistema/src/Views/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Template Main CSS File -->
    <link href="/sistema/src/Views/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- ======= Header ======= -->
    <?php include __DIR__ . '/header.php'; ?>

    <!-- ======= Sidebar ======= -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Mi Perfil Completo</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/sistema/public/inicio">Inicio</a></li>
                    <li class="breadcrumb-item active">Mi Perfil</li>
                </ol>
            </nav>
        </div>

        <section class="section profile">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body pt-3">
                            <!-- Información del Usuario -->
                            <div class="tab-content pt-2">
                                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                    <h5 class="card-title">Detalles del Perfil</h5>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">ID Usuario</div>
                                        <div class="col-lg-9 col-md-8" id="info-id-usuario">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Número de Identidad</div>
                                        <div class="col-lg-9 col-md-8" id="info-identidad">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Usuario</div>
                                        <div class="col-lg-9 col-md-8" id="info-usuario">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Nombre Completo</div>
                                        <div class="col-lg-9 col-md-8" id="info-nombre">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Correo Electrónico</div>
                                        <div class="col-lg-9 col-md-8" id="info-correo">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Estado</div>
                                        <div class="col-lg-9 col-md-8" id="info-estado">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Rol</div>
                                        <div class="col-lg-9 col-md-8" id="info-rol">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Fecha de Creación</div>
                                        <div class="col-lg-9 col-md-8" id="info-fecha-creacion">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Última Conexión</div>
                                        <div class="col-lg-9 col-md-8" id="info-ultima-conexion">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Fecha de Vencimiento</div>
                                        <div class="col-lg-9 col-md-8" id="info-vencimiento">Cargando...</div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">2FA Habilitado</div>
                                        <div class="col-lg-9 col-md-8" id="info-2fa">Cargando...</div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                            <img src="/sistema/src/Views/assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
                            <h2 id="profile-name">Cargando...</h2>
                            <h3 id="profile-role">Usuario</h3>
                            
                            <div class="mt-3">
                                <button class="btn btn-primary" id="btnEditarPerfil">
                                    <i class="bi bi-pencil"></i> Editar Perfil
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botón para regresar -->
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="/sistema/public/inicio" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Vendor JS Files -->
    <script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarInformacionCompleta();
        
        // Botón para editar perfil
        document.getElementById('btnEditarPerfil').addEventListener('click', function() {
            alert('Funcionalidad de edición en desarrollo');
            // Aquí puedes implementar el modal de edición
        });
    });

    function cargarInformacionCompleta() {
        fetch('/sistema/src/routes/user.php?action=getFullInfo')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    
                    // Actualizar la información en la página
                    document.getElementById('info-id-usuario').textContent = user.ID_USUARIO;
                    document.getElementById('info-identidad').textContent = user.NUMERO_IDENTIDAD || 'No especificado';
                    document.getElementById('info-usuario').textContent = user.USUARIO;
                    document.getElementById('info-nombre').textContent = user.NOMBRE_USUARIO;
                    document.getElementById('info-correo').textContent = user.CORREO_ELECTRONICO;
                    document.getElementById('info-estado').textContent = user.ESTADO_USUARIO;
                    document.getElementById('info-rol').textContent = user.ID_ROL; // Puedes mapear esto a nombres de rol
                    document.getElementById('info-fecha-creacion').textContent = user.FECHA_CREACION;
                    document.getElementById('info-ultima-conexion').textContent = user.FECHA_ULTIMA_CONEXION || 'Nunca';
                    document.getElementById('info-vencimiento').textContent = user.FECHA_VENCIMIENTO || 'No especificada';
                    document.getElementById('info-2fa').textContent = user.HABILITAR_2FA ? 'Sí' : 'No';
                    
                    // Actualizar tarjeta de perfil
                    document.getElementById('profile-name').textContent = user.NOMBRE_USUARIO;
                    
                } else {
                    alert('Error al cargar la información del perfil');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar la información del perfil');
            });
    }
    </script>
</body>
</html>