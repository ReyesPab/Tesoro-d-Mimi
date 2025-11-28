#!/usr/bin/env php
<?php
/**
 * Script de verificación del Módulo de Ventas
 * Ejecutar: php verificar-modulo.php
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║  VERIFICACIÓN DEL MÓDULO DE VENTAS                   ║\n";
echo "║  Sistema de Ventas con Control de Inventario          ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$basePath = __DIR__;
$errors = [];
$warnings = [];
$success = [];

// ============= VERIFICACIÓN DE ARCHIVOS =============
echo "📁 VERIFICANDO ARCHIVOS...\n";
echo "─────────────────────────────────────────\n";

$archivos = [
    // Modelos
    'src/models/modulo_ventas/ventasModel.php' => 'Model - Ventas',
    'src/models/modulo_ventas/clienteModel.php' => 'Model - Clientes',
    
    // Controladores
    'src/controllers/modulo_ventas/ventasController.php' => 'Controller - Ventas',
    
    // Rutas
    'src/routes/modulo_ventas/ventas.php' => 'Router - Ventas',
    
    // Vistas
    'src/Views/modulo_ventas/registrar-venta.php' => 'Vista - Registrar Venta',
    'src/Views/modulo_ventas/consultar-ventas.php' => 'Vista - Consultar Ventas',
    'src/Views/modulo_ventas/partials/modal_nuevo_cliente.php' => 'Partial - Modal Cliente',
    'src/Views/modulo_ventas/partials/carrito.php' => 'Partial - Carrito',
    'src/Views/modulo_ventas/partials/grid_productos.php' => 'Partial - Grid Productos',
    
    // Configuración
    'src/config/modulo_ventas/VentasConfig.php' => 'Config - Ventas',
    
    // Documentación
    'src/Views/modulo_ventas/README.md' => 'Doc - README',
];

foreach ($archivos as $ruta => $nombre) {
    $rutaCompleta = $basePath . DIRECTORY_SEPARATOR . $ruta;
    
    if (file_exists($rutaCompleta)) {
        $size = filesize($rutaCompleta);
        $success[] = "✅ $nombre ({$size} bytes)";
        echo "✅ $nombre\n";
    } else {
        $errors[] = "FALTA: $nombre en $ruta";
        echo "❌ $nombre - NO ENCONTRADO\n";
    }
}

// ============= VERIFICACIÓN DE PERMISOS =============
echo "\n📋 VERIFICANDO PERMISOS...\n";
echo "─────────────────────────────────────────\n";

$directorios = [
    'src/models/modulo_ventas' => 'Modelos',
    'src/controllers/modulo_ventas' => 'Controladores',
    'src/routes/modulo_ventas' => 'Rutas',
    'src/Views/modulo_ventas' => 'Vistas',
    'src/config/modulo_ventas' => 'Configuración',
];

foreach ($directorios as $dir => $nombre) {
    $rutaCompleta = $basePath . DIRECTORY_SEPARATOR . $dir;
    
    if (is_dir($rutaCompleta)) {
        if (is_writable($rutaCompleta)) {
            echo "✅ $nombre - ESCRITURA OK\n";
        } else {
            $warnings[] = "ADVERTENCIA: Sin permiso de escritura en $dir";
            echo "⚠️  $nombre - SIN ESCRITURA\n";
        }
    } else {
        echo "❌ $nombre - DIRECTORIO NO EXISTE\n";
    }
}

// ============= VERIFICACIÓN DE CONFIGURACIÓN =============
echo "\n⚙️  VERIFICANDO CONFIGURACIÓN...\n";
echo "─────────────────────────────────────────\n";

// Verificar composer.json
$composerPath = $basePath . DIRECTORY_SEPARATOR . 'composer.json';
if (file_exists($composerPath)) {
    $composer = json_decode(file_get_contents($composerPath), true);
    
    if (isset($composer['autoload']['psr-4']['modulo_ventas\\'])) {
        echo "✅ Namespace modulo_ventas registrado en composer.json\n";
    } else {
        $warnings[] = "ADVERTENCIA: Namespace modulo_ventas no registrado en composer.json";
        echo "⚠️  Namespace modulo_ventas no configurado\n";
        echo "   → Ejecutar: composer dump-autoload\n";
    }
} else {
    $errors[] = "No se encontró composer.json";
    echo "❌ composer.json NO ENCONTRADO\n";
}

// ============= VERIFICACIÓN DE SINTAXIS PHP =============
echo "\n🔍 VERIFICANDO SINTAXIS PHP...\n";
echo "─────────────────────────────────────────\n";

$archivosPhp = [
    'src/models/modulo_ventas/ventasModel.php',
    'src/models/modulo_ventas/clienteModel.php',
    'src/controllers/modulo_ventas/ventasController.php',
    'src/routes/modulo_ventas/ventas.php',
];

foreach ($archivosPhp as $archivo) {
    $rutaCompleta = $basePath . DIRECTORY_SEPARATOR . $archivo;
    
    if (file_exists($rutaCompleta)) {
        $output = [];
        $returnVar = 0;
        exec("php -l \"$rutaCompleta\" 2>&1", $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "✅ $archivo\n";
        } else {
            $errors[] = "ERROR DE SINTAXIS en $archivo";
            echo "❌ $archivo - ERROR DE SINTAXIS\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
    }
}

// ============= VERIFICACIÓN DE NAMESPACES =============
echo "\n📦 VERIFICANDO NAMESPACES...\n";
echo "─────────────────────────────────────────\n";

$namespaces = [
    'modulo_ventas\\models\\ventasModel' => 'ventasModel',
    'modulo_ventas\\models\\clienteModel' => 'clienteModel',
    'modulo_ventas\\controllers\\ventasController' => 'ventasController',
];

foreach ($namespaces as $fqn => $clase) {
    $archivo = str_replace('\\', '/', $fqn);
    $rutaArchivo = "$basePath/src/" . substr($archivo, 16) . ".php";
    
    if (file_exists($rutaArchivo)) {
        $contenido = file_get_contents($rutaArchivo);
        
        if (strpos($contenido, "namespace modulo_ventas") !== false) {
            echo "✅ Namespace correcto en $clase\n";
        } else {
            $warnings[] = "Namespace incorrecto en $clase";
            echo "⚠️  Namespace incorrecto en $clase\n";
        }
    }
}

// ============= ESTADÍSTICAS =============
echo "\n📊 ESTADÍSTICAS\n";
echo "─────────────────────────────────────────\n";

$totalArchivos = 0;
$totalLineas = 0;

foreach ($archivos as $ruta => $nombre) {
    $rutaCompleta = $basePath . DIRECTORY_SEPARATOR . $ruta;
    if (file_exists($rutaCompleta) && strpos($ruta, '.php') !== false) {
        $totalArchivos++;
        $totalLineas += count(file($rutaCompleta));
    }
}

echo "📁 Total de archivos PHP: $totalArchivos\n";
echo "📝 Total de líneas de código: $totalLineas\n";
echo "✅ Archivos exitosos: " . count($success) . "\n";

// ============= RESUMEN FINAL =============
echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";

if (empty($errors)) {
    echo "║  ✅ VERIFICACIÓN COMPLETADA EXITOSAMENTE             ║\n";
} else {
    echo "║  ❌ SE ENCONTRARON PROBLEMAS                         ║\n";
}

echo "╚════════════════════════════════════════════════════════╝\n";

if (!empty($errors)) {
    echo "\n⛔ ERRORES ENCONTRADOS:\n";
    echo "─────────────────────────────────────────\n";
    foreach ($errors as $error) {
        echo "  • $error\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  ADVERTENCIAS:\n";
    echo "─────────────────────────────────────────\n";
    foreach ($warnings as $warning) {
        echo "  • $warning\n";
    }
}

// ============= INSTRUCCIONES =============
echo "\n";
echo "🚀 PRÓXIMOS PASOS:\n";
echo "─────────────────────────────────────────\n";

if (!empty($errors)) {
    echo "1. ⚠️  RESOLVER ERRORES ENCONTRADOS\n";
}

echo "2. 📦 Ejecutar composer dump-autoload\n";
echo "3. 🔐 Hacer login en el sistema\n";
echo "4. 🌐 Acceder a: /src/Views/modulo_ventas/registrar-venta.php\n";
echo "5. 🧪 Probar creando una venta de prueba\n";

echo "\n";
echo "📚 DOCUMENTACIÓN:\n";
echo "─────────────────────────────────────────\n";
echo "  • README: src/Views/modulo_ventas/README.md\n";
echo "  • Integración: MODULO_VENTAS_INTEGRATION.md\n";
echo "  • Resumen: MODULO_VENTAS_RESUMEN.md\n";

echo "\n✅ Verificación completada\n\n";

exit(empty($errors) ? 0 : 1);
