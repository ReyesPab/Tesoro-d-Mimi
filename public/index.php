
<?php
if (ob_get_length()) ob_clean();
use App\config\errorlogs;
use App\config\responseHTTP;
use App\config\Security;

require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();

// Verificar y cargar el middleware systemCheck
$middlewarePath = dirname(__DIR__) . '/src/middlewares/systemCheck.php';
if (file_exists($middlewarePath)) {
    require_once $middlewarePath;
    SystemCheck::verificarBloqueo();
} else {
    error_log("⚠️ Advertencia: Middleware systemCheck no encontrado en: " . $middlewarePath);
    // Continuar sin el middleware para no romper el sistema
} SystemCheck::verificarBloqueo();
if (isset($_GET['route'])) {
    $url = explode('/', $_GET['route']);
    $lista = ['auth', 'user', 'login', 'dashboard', 'perfil', 'recuperar-password'
    , 'gestion-usuarios', 'crear-usuario', 'registro', 'inicio', 'cambiar-password', 'bitacora', 
    'editar-usuario', 'resetear-contrasena', 'configurar-2fa', 'registrar-compras', 'consultar-compras', 
    'consultar-ordenes-pendientes', 'ordenes-canceladas', 'ordenes-finalizadas', 'detalle-compra', 'compras', 
    'generar_pdf', 'reporte_compras_pdf', 'reporte_consultas_pdf', 'reporte_compras_Excel', 'reporte-materia-prima-pdf', 'registrar-proveedor', 'gestion-proveedores', 'editar-proveedor',
     'gestion-productos-proveedor','editar-productos-proveedores','registrar-materia-prima',  'gestion-materia-prima',
      'gestion-inventario',  'editar-materia-prima', 'produccion', 'crear-produccion', 'gestion-produccion',
       'finalizar-produccion', 'detalle-produccion', 'reporte_produccion_pdf', 'crear-receta', 'ver-recetas', 'gestion-productos', 'editar-producto'
       ,'backup', 'gestion-backups', 'gestion-inventario-productos', 'permisos-usuarios', 'perdidas-produccion',
       'registrar-venta', 'consultar-ventas', 'ventas', 'role','gestion-roles', 'gestion-clientes', 'gestion-relacion-producto-proveedor', 'dashboar', 'editar-receta' ];
    $caso = filter_input(INPUT_GET, "caso");

    // CAMBIO IMPORTANTE: Usar $url[0] directamente en lugar de explode
    $rutaActual = $_GET['route']; // Esto ya contiene la ruta completa

// Agregar ESTA sección ANTES de la sección general de APIs
if ($rutaActual === 'bitacora' && !empty($caso)) {
    $file = dirname(__DIR__) . '/src/routes/bitacoraAPI.php';
    
    if (!file_exists($file) || !is_readable($file)) {
        echo json_encode(responseHTTP::status400('Archivo de ruta API no encontrado'));
        exit;
    }

    require $file;
    exit;
}

 // En la sección de APIs, AGREGAR 'dashboard':
if (in_array($rutaActual, ['auth', 'user', 'compras', 'dashboard', 'inventario',  'produccion','backup','permisos', 'ventas', 'clientes', 'role' ])) {
    $file = dirname(__DIR__) . '/src/routes/' . $rutaActual . '.php';
    
    if (!file_exists($file) || !is_readable($file)) {
        echo json_encode(responseHTTP::status400('Archivo de ruta no encontrado o no legible'));
        exit;
    }

    require $file;
    exit;
}

if ($rutaActual === 'permisos-usuarios' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/seguridad/permisos-usuarios.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
// Agregar estas rutas para las vistas de producción:

// Ruta para ver recetas (VISTA)
if ($rutaActual === 'perfil' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/perfil.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}


if ($rutaActual === 'perdidas-produccion' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/perdidas-produccion.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'reporte_produccion_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/reporte_produccion_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}


if ($rutaActual === 'gestion-inventario-productos' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/Inventario/gestion-inventario-productos.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 
if ($rutaActual === 'editar-receta' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/editar-receta.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
// Rutas para el Módulo de Ventas
if ($rutaActual === 'registrar-venta' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/modulo_ventas/registrar-venta.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'consultar-ventas' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/modulo_ventas/consultar-ventas.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'gestion-clientes' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/modulo_ventas/gestion-clientes.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 

if ($rutaActual === 'editar-producto' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/editar-producto.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'gestion-productos' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/gestion-productos.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para ver recetas (VISTA)
if ($rutaActual === 'ver-recetas' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/ver-recetas.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'reporte_receta_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/reporte_receta_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'reporte_recetas_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/reporte_recetas_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para crear-produccion (VISTA)
if ($rutaActual === 'crear-receta' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/crear-receta.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}


// Ruta para crear-produccion (VISTA)
if ($rutaActual === 'crear-produccion' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/crear-produccion.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para gestion-produccion (VISTA)
if ($rutaActual === 'gestion-produccion' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/gestion-produccion.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para finalizar-produccion (VISTA)
if ($rutaActual === 'finalizar-produccion' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/finalizar-produccion.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para detalle-produccion (VISTA)
if ($rutaActual === 'detalle-produccion' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/produccion/detalle-produccion.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

 

// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'gestion-inventario' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/Inventario/gestion-inventario.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}


// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'editar-materia-prima' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/editar-materia-prima.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'ordenes-finalizadas' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/ordenes-finalizadas.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

  // Módulo Compras-- Ruta para ordenes-canceladas
if ($rutaActual === 'ordenes-canceladas' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/ordenes-canceladas.php';
    
    
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
       
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado: ' . $file));
        exit;
    }            
}


  // Módulo Compras-- Ruta para registrar-compras
if ($rutaActual === 'consultar-ordenes-pendientes' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/consultar-ordenes-pendientes.php';
    
    
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
       
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado: ' . $file));
        exit;
    }            
}

   // Módulo Compras-- Ruta para registrar-compras
if ($rutaActual === 'registrar-compras' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-compras.php';
    
    
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
       
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado: ' . $file));
        exit;
    }            
}

// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'consultar-compras' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/consultar-compras.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para detalle-compra (VISTA)
if ($rutaActual === 'detalle-compra' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/detalle-compra.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
// Ruta para gestion-relacion-producto-proveedor (VISTA)
if ($rutaActual === 'gestion-relacion-producto-proveedor' && empty($caso)) { 
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-relacion-producto-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para detalle-compra (VISTA)
// Ruta para generar_pdf (VISTA)
if ($rutaActual === 'generar-pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/generar_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

if ($rutaActual === 'reporte_compras_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/reporte_compras_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
// Ruta para reporte_consultas_pdf (VISTA - FPDF)
if ($rutaActual === 'reporte_consultas_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/reporte_consultas_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
// Ruta para reporte_compras_Excel (VISTA - descarga Excel)
if ($rutaActual === 'reporte_compras_Excel' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/reporte_compras_Excel.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para reporte de materia prima (PDF)
if ($rutaActual === 'reporte-materia-prima-pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/reporte_materia_prima_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para editar-productos-proveedores (VISTA)
if ($rutaActual === 'editar-productos-proveedores' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/editar-productos-proveedores.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}



// Ruta para gestion-productos-proveedor (VISTA)
if ($rutaActual === 'gestion-productos-proveedor' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-productos-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para registrar-proveedor (VISTA)
if ($rutaActual === 'registrar-proveedor' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para gestion-proveedores (VISTA)
if ($rutaActual === 'gestion-proveedores' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-proveedores.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para ditar-proveedor (VISTA)
if ($rutaActual === 'editar-proveedor' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/editar-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para registrar-materia-prima (VISTA)
if ($rutaActual === 'registrar-materia-prima' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-materia-prima.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 

// Ruta para registrar-materia-prima (VISTA)
if ($rutaActual === 'gestion-materia-prima' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-materia-prima.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 


      // Ruta para login (VISTA)
    if ($rutaActual === 'login' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/login.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para inicio (VISTA)
    if ($rutaActual === 'inicio' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/inicio.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para cambiar-password (VISTA)
    if ($rutaActual === 'cambiar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/cambiar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para gestion-usuarios (VISTA)
    if ($rutaActual === 'gestion-usuarios' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/gestion-usuarios.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para crear-usuario  (VISTA)
    if ($rutaActual === 'crear-usuario' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/crear-usuario.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    if ($rutaActual === 'editar-usuario' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/editar-usuario.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    

    if ($rutaActual === 'resetear-contrasena' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/resetear-contrasena.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para dashboard (VISTA)
    if ($rutaActual === 'dashboar' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/dashboar.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para recuperar-password (VISTA)
    if ($rutaActual === 'recuperar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/recuperar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para registro (VISTA)
    if ($rutaActual === 'registro' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/registro.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }
    // Ruta para restaurar-backup (VISTA)
if ($rutaActual === 'restaurar-backup' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/restaurar-backup.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}// Ruta para gestion-backups (VISTA)
if ($rutaActual === 'gestion-backups' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/gestion-backups.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
    // Ruta para bitacora (VISTA)
    if ($rutaActual === 'bitacora' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/bitacora.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }
if ($rutaActual === 'gestion-roles' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/gestion-roles.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
    // Ruta para configurar-2fa (VISTA)
    if ($rutaActual === 'configurar-2fa' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/configurar-2fa.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    

    // Verificar si la ruta está permitida (para APIs)
    if (!in_array($rutaActual, $lista)) {
        echo json_encode(responseHTTP::status400('Ruta no permitida'));
        exit;
    }

    // Cargar archivo de ruta API (solo para rutas de API como 'auth', 'user')
if (in_array($rutaActual, ['auth', 'user','backup'])) {
        $file = dirname(__DIR__) . '/src/routes/' . $rutaActual . '.php';
        
        if (!file_exists($file) || !is_readable($file)) {
            echo json_encode(responseHTTP::status400('Archivo de ruta no encontrado o no legible'));
            exit;
        }

        require $file;
        exit;
    }

} else {
    // Redirección por defecto
    header('Location: /sistema/public/login');
    exit;
}
