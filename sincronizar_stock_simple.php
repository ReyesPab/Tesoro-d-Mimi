<?php
/**
 * Script simple de sincronización de stock
 * Copia datos del SP_OBTENER_INVENTARIO_PRODUCTOS a tbl_inventario_producto
 */

// Conexión directa sin autoloader
$host = '127.0.0.1';
$port = '3308';
$user = 'root';
$pass = '';
$db = 'rosquilleria';

try {
    $con = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    
    echo "\n=== SINCRONIZACIÓN DE STOCK DESDE SP ===\n";
    echo "Conectado a BD: $db\n\n";
    
    // PASO 1: Obtener datos del SP (fuente de verdad)
    echo "1. Obteniendo datos del Stored Procedure...\n";
    $sql_sp = "CALL SP_OBTENER_INVENTARIO_PRODUCTOS(NULL, NULL)";
    $query_sp = $con->prepare($sql_sp);
    $query_sp->execute();
    $inventario_sp = $query_sp->fetchAll(PDO::FETCH_ASSOC);
    $query_sp->closeCursor(); // Cerrar cursor del SP antes de hacer más queries
    echo "   ✓ " . count($inventario_sp) . " productos encontrados en el SP\n\n";
    
    // PASO 2: Sincronizar con tbl_inventario_producto
    echo "2. Sincronizando con tbl_inventario_producto...\n";
    $sincronizados = 0;
    $actualizados = 0;
    $errores = [];
    
    foreach ($inventario_sp as $item) {
        $id_producto = intval($item['ID_PRODUCTO'] ?? 0);
        $cantidad = floatval($item['CANTIDAD'] ?? 0);
        $nombre = $item['NOMBRE'] ?? 'Sin nombre';
        
        if ($id_producto <= 0) continue;
        
        try {
            // Verificar si existe
            $check_sql = "SELECT COUNT(*) FROM tbl_inventario_producto WHERE ID_PRODUCTO = :id";
            $check_q = $con->prepare($check_sql);
            $check_q->execute([':id' => $id_producto]);
            $existe = intval($check_q->fetchColumn()) > 0;
            
            if (!$existe) {
                // Insertar
                $insert_sql = "INSERT INTO tbl_inventario_producto (ID_PRODUCTO, CANTIDAD, MINIMO, MAXIMO) 
                              VALUES (:id, :cantidad, 0, 0)";
                $insert_q = $con->prepare($insert_sql);
                $insert_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
                $sincronizados++;
                echo "   ✓ Insertado: [$id_producto] $nombre (Cantidad: $cantidad)\n";
            } else {
                // Actualizar cantidad
                $current_sql = "SELECT CANTIDAD FROM tbl_inventario_producto WHERE ID_PRODUCTO = :id";
                $current_q = $con->prepare($current_sql);
                $current_q->execute([':id' => $id_producto]);
                $current = floatval($current_q->fetchColumn());
                
                if ($current != $cantidad) {
                    $update_sql = "UPDATE tbl_inventario_producto SET CANTIDAD = :cantidad WHERE ID_PRODUCTO = :id";
                    $update_q = $con->prepare($update_sql);
                    $update_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
                    $actualizados++;
                    echo "   ↻ Actualizado: [$id_producto] $nombre ($current → $cantidad)\n";
                }
            }
        } catch (Exception $e) {
            $errores[] = "Producto $id_producto: " . $e->getMessage();
            echo "   ✗ Error en producto $id_producto: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ RESULTADO:\n";
    echo "   - Productos insertados: $sincronizados\n";
    echo "   - Productos actualizados: $actualizados\n";
    echo "   - Errores: " . count($errores) . "\n";
    
    if (!empty($errores)) {
        echo "\nDetalles de errores:\n";
        foreach ($errores as $err) {
            echo "   - $err\n";
        }
    }
    
    // PASO 3: Sincronizar TAMBIÉN tbl_producto (tiene columnas CANTIDAD, MINIMO, MAXIMO)
    echo "\n3. Sincronizando TAMBIÉN tbl_producto...\n";
    
    $prod_actualizados = 0;
    foreach ($inventario_sp as $item) {
        $id_producto = intval($item['ID_PRODUCTO'] ?? 0);
        $cantidad = floatval($item['CANTIDAD'] ?? 0);
        $minimo = floatval($item['MINIMO'] ?? 0);
        $maximo = floatval($item['MAXIMO'] ?? 0);
        
        if ($id_producto <= 0) continue;
        
        try {
            $update_prod_sql = "UPDATE tbl_producto 
                               SET CANTIDAD = :cantidad,
                                   MINIMO = :minimo,
                                   MAXIMO = :maximo,
                                   MODIFICADO_POR = 'SINCRONIZACIÓN AUTOMÁTICA',
                                   FECHA_MODIFICACION = NOW()
                               WHERE ID_PRODUCTO = :id AND ESTADO = 'ACTIVO'";
            $update_prod_q = $con->prepare($update_prod_sql);
            $update_prod_q->execute([
                ':id' => $id_producto,
                ':cantidad' => $cantidad,
                ':minimo' => $minimo,
                ':maximo' => $maximo
            ]);
            
            if ($update_prod_q->rowCount() > 0) {
                $prod_actualizados++;
                echo "   ✓ Actualizado tbl_producto[$id_producto]: CANTIDAD=$cantidad\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Error actualizando tbl_producto[$id_producto]: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   - Total actualizados en tbl_producto: $prod_actualizados\n";
    
    // Verificación final
    echo "\n4. Verificación final...\n";
    $final_sql = "SELECT COUNT(*) FROM tbl_producto p 
                  WHERE p.ESTADO = 'ACTIVO' 
                  AND EXISTS (SELECT 1 FROM tbl_inventario_producto ip WHERE ip.ID_PRODUCTO = p.ID_PRODUCTO)";
    $final_q = $con->query($final_sql);
    $total = intval($final_q->fetchColumn());
    echo "   ✓ Productos ACTIVOS con inventario sincronizado (ambas tablas): $total\n";
    
    // Mostrar estado actual de tbl_producto
    $check_prod = $con->query("SELECT ID_PRODUCTO, NOMBRE, CANTIDAD FROM tbl_producto WHERE ESTADO = 'ACTIVO' ORDER BY ID_PRODUCTO");
    $prod_list = $check_prod->fetchAll(PDO::FETCH_ASSOC);
    echo "\n📋 Estado actual de tbl_producto:\n";
    foreach ($prod_list as $p) {
        echo "   [{$p['ID_PRODUCTO']}] {$p['NOMBRE']}: {$p['CANTIDAD']} unidades\n";
    }
    
    echo "\n✅ SINCRONIZACIÓN COMPLETADA (ambas tablas actualizadas)\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
