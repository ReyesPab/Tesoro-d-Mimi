<?php

// Iniciar sesión de manera compatible con tu sistema
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// DEBUG - Ver qué hay en la sesión (quitar después)
error_log("🎯 SESIÓN ACTUAL: " . print_r($_SESSION, true));

// Verificar sesión según tu sistema - probamos diferentes variables
$sesion_activa = false;
$variables_sesion = [
    'logged_in', 'iniciada', 'USUARIO', 'usuario', 
    'ID_USUARIO', 'id_usuario', 'user_name', 'usuario_nombre'
];

foreach ($variables_sesion as $variable) {
    if (isset($_SESSION[$variable])) {
        $sesion_activa = true;
        error_log("✅ Sesión activa con variable: $variable = " . $_SESSION[$variable]);
        break;
    }
}

if (!$sesion_activa) {
    error_log("❌ No hay sesión activa - Redirigiendo al login");
    echo "<script>
        alert('Sesión no encontrada. Serás redirigido al login.');
        window.location.href = '/sistema/public/login';
    </script>";
    exit;
}

// Obtener datos del usuario para mostrar
$nombre_usuario = $_SESSION['NOMBRE_USUARIO'] ?? 
                 $_SESSION['usuario_nombre'] ?? 
                 $_SESSION['user_name'] ?? 
                 $_SESSION['USUARIO'] ?? 
                 'Usuario';

$id_usuario = $_SESSION['ID_USUARIO'] ?? 
              $_SESSION['id_usuario'] ?? 
              $_SESSION['user_id'] ?? 
              0;

// src/Views/produccion/ver-recetas.php
use App\config\SessionHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$userId = SessionHelper::getUserId();

// Verificar permiso para el botón Nueva Receta
$permisoNuevaReceta = permisosModel::verificarPermiso($userId, 'CREAR_RECETAS', 'CONSULTAR');

// Ver Recetas - Vista
use App\models\produccionModel;

// Obtener todas las recetas
try {
    require_once __DIR__ . '/../../models/produccionModel.php';
    $recetasData = produccionModel::obtenerTodasLasRecetas();
    $recetas = $recetasData['success'] ? $recetasData['data'] : [];
} catch (Exception $e) {
    error_log("Error al cargar recetas: " . $e->getMessage());
    $recetas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Ver Recetas - Rosquilleria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D2691E;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .main {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .pagetitle {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .pagetitle h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 25px;
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-body {
            padding: 25px;
        }
        
        .card-title {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .badge-cost {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-size: 0.9rem;
        }
        
        .badge-ingredients {
            background: linear-gradient(135deg, var(--secondary-color), #CD853F);
            color: white;
            font-size: 0.9rem;
        }
        
        .table-ingredients {
            font-size: 0.9rem;
        }
        
        .table-ingredients th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        .cost-total {
            font-size: 1.1rem;
            font-weight: 700;
            color: #28a745;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #CD853F);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #A0522D, var(--secondary-color));
            transform: translateY(-1px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .recipe-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            gap: 12px;
        }

        .recipe-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-export {
            border: 1px solid rgba(0,0,0,0.08);
            color: var(--secondary-color);
            background: rgba(210,105,30,0.08);
            padding: 4px 10px;
            font-size: 0.85rem;
            border-radius: 6px;
        }

        .btn-export:hover {
            background: rgba(210,105,30,0.15);
            color: var(--primary-color);
        }
        
        .recipe-info {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .recipe-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Recetas de Productos</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=produccion">Producción</a></li>
                <li class="breadcrumb-item active">Ver Recetas</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Listado de Recetas</h5>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button class="btn btn-outline-danger" onclick="exportarTodasRecetas()">
            <i class="bi bi-collection"></i> Exportar todas
        </button>
         
        <a href="/sistema/public/crear-receta" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Crear Producto
        </a>
                                 <?php if ($permisoNuevaReceta): ?>
                                <a href="/sistema/public/crear-receta" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Nueva Receta
                                </a>
                                <?php endif; ?>
                                <span class="badge bg-primary">
                                    <i class="bi bi-list-check"></i> 
                                    <?php echo count($recetas); ?> Recetas
                                </span>
                            </div>
                        </div>

                        <?php if (empty($recetas)): ?>
                            <div class="empty-state">
                                <i class="bi bi-journal-x"></i>
                                <h4>No hay recetas registradas</h4>
                                <p class="text-muted">No se han encontrado recetas en el sistema.</p>
                                <a href="/sistema/public/crear-receta" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Crear Producto
            </a>
                                <a href="/sistema/public/index.php?route=crear-receta" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle"></i> Crear Primera Receta
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($recetas as $receta): ?>
                                    <div class="col-lg-6 col-xl-4 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="recipe-header">
                                                    <div>
                                                        <h6 class="card-title mb-2"><?php echo htmlspecialchars($receta['NOMBRE_PRODUCTO']); ?></h6>
                                                        <div class="recipe-info">
                                                            <span class="badge badge-ingredients">
                                                                <i class="bi bi-box-seam"></i> 
                                                                <?php echo $receta['TOTAL_INGREDIENTES']; ?> ingredientes
                                                            </span>
                                                            <span class="badge badge-cost">
                                                                <i class="bi bi-currency-dollar"></i> 
                                                                L <?php echo number_format($receta['COSTO_TOTAL'], 2); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="recipe-actions">
    <button class="btn btn-export btn-sm" onclick="exportarReceta(<?php echo $receta['ID_PRODUCTO']; ?>)">
        <i class="bi bi-file-earmark-pdf"></i> Exportar
    </button>
    <!-- 🆕 NUEVO: Botón de editar -->
    <a href="/sistema/public/editar-receta?id=<?php echo $receta['ID_PRODUCTO']; ?>" 
       class="btn btn-warning btn-sm" 
       title="Editar producto y receta">
        <i class="bi bi-pencil"></i> Editar
    </a>
</div>
                                                </div>
                                                
                                                <?php if (!empty($receta['DESCRIPCION_PRODUCTO'])): ?>
                                                    <p class="text-muted small mb-3"><?php echo htmlspecialchars($receta['DESCRIPCION_PRODUCTO']); ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-ingredients">
                                                        <thead>
                                                            <tr>
                                                                <th>Ingrediente</th>
                                                                <th class="text-center">Cantidad</th>
                                                                <th class="text-end">Costo</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($receta['INGREDIENTES'] as $ingrediente): ?>
                                                                <tr>
                                                                    <td>
                                                                        <small><?php echo htmlspecialchars($ingrediente['NOMBRE_MATERIA_PRIMA']); ?></small>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <small>
                                                                            <?php echo number_format($ingrediente['CANTIDAD_NECESARIA'], 3); ?> 
                                                                            <?php echo htmlspecialchars($ingrediente['UNIDAD']); ?>
                                                                        </small>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <small>L <?php echo number_format($ingrediente['COSTO_INGREDIENTE'], 2); ?></small>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="2" class="text-end fw-bold">Total:</td>
                                                                <td class="text-end cost-total">L <?php echo number_format($receta['COSTO_TOTAL'], 2); ?></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                
                                                <div class="mt-3 pt-3 border-top">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            <i class="bi bi-person"></i> 
                                                            <?php echo htmlspecialchars($receta['CREADO_POR']); ?>
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar"></i> 
                                                            <?php echo date('d/m/Y', strtotime($receta['FECHA_CREACION'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Funcionalidad adicional para las recetas
document.addEventListener('DOMContentLoaded', function() {
    // Agregar funcionalidad de búsqueda futura aquí
    console.log('Recetas cargadas: <?php echo count($recetas); ?>');
});

function exportarReceta(idProducto) {
    if (!idProducto) return;
    window.open(`/sistema/public/index.php?route=reporte_receta_pdf&id=${idProducto}`, '_blank');
}

function exportarTodasRecetas() {
    window.open('/sistema/public/index.php?route=reporte_recetas_pdf', '_blank');
}
</script>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>
