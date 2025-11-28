<?php
/**
 * Archivo de pruebas para el Módulo de Ventas
 * Ejecutar desde terminal: php test_ventas.php
 * 
 * Este archivo contiene ejemplos de cómo usar los endpoints
 */

echo "========================================\n";
echo "PRUEBAS DEL MÓDULO DE VENTAS\n";
echo "========================================\n\n";

// URL base para las pruebas
$api_base = 'http://localhost/src/routes/modulo_ventas/ventas.php';

/**
 * Función auxiliar para hacer requests
 */
function hacerRequest($url, $metodo = 'GET', $datos = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);

    if (!empty($datos)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// ========== PRUEBA 1: Obtener Categorías ==========
echo "\n1. OBTENER CATEGORÍAS\n";
echo "---\n";
$resultado = hacerRequest("$api_base?caso=obtenerCategorias");
echo "Status: " . $resultado['status'] . "\n";
echo "Response:\n" . json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

// ========== PRUEBA 2: Obtener Productos por Categoría ==========
echo "\n2. OBTENER PRODUCTOS - CATEGORÍA MAIZ\n";
echo "---\n";
$resultado = hacerRequest("$api_base?caso=obtenerProductosPorCategoria", 'POST', [
    'categoria' => 'MAIZ'
]);
echo "Status: " . $resultado['status'] . "\n";
echo "Response: (primeros 2 productos)\n";
if ($resultado['data']['success']) {
    $productos = array_slice($resultado['data']['data'], 0, 2);
    echo json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}

// ========== PRUEBA 3: Obtener Métodos de Pago ==========
echo "\n3. OBTENER MÉTODOS DE PAGO\n";
echo "---\n";
$resultado = hacerRequest("$api_base?caso=obtenerMetodosPago");
echo "Status: " . $resultado['status'] . "\n";
echo "Response:\n" . json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

// ========== PRUEBA 4: Buscar Cliente por DNI (sin sesión) ==========
echo "\n4. BUSCAR CLIENTE POR DNI\n";
echo "---\n";
echo "NOTA: Esta prueba fallará porque no hay sesión iniciada\n";
echo "En producción, se debe hacer login primero\n";
$resultado = hacerRequest("$api_base?caso=buscarClientePorDNI", 'POST', [
    'dni' => '12345678'
]);
echo "Status: " . $resultado['status'] . "\n";
echo "Response:\n" . json_encode($resultado['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n========================================\n";
echo "FIN DE PRUEBAS\n";
echo "========================================\n";
echo "\nNOTA: Para pruebas con autenticación, ejecutar desde navegador:\n";
echo "1. Login en el sistema\n";
echo "2. Acceder a: /src/Views/modulo_ventas/registrar-venta.php\n";
echo "3. Los endpoints se llamarán automáticamente desde JavaScript\n";
