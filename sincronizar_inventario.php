<?php
/**
 * Script de sincronización: copia cantidades del SP_OBTENER_INVENTARIO_PRODUCTOS a tbl_inventario_producto
 */
require_once __DIR__ . '/src/db/connectionDB.php';

use App\db\connectionDB;

$con = connectionDB::getConnection();

echo "\n=== SINCRONIZACIÓN DE STOCK ===\n";
echo "Este script sincroniza el inventario de productos entre tablas.\n\n";

try {
    // Paso 1: Obtener datos del procedimiento almacenado (fuente de verdad)
    echo "1. Obteniendo datos del Stored Procedure (SP_OBTENER_INVENTARIO_PRODUCTOS)...\n";
    $sql_sp = "CALL SP_OBTENER_INVENTARIO_PRODUCTOS(NULL, NULL)";
    $query_sp = $con->prepare($sql_sp);
    $query_sp->execute();
    $inventario_sp = $query_sp->fetchAll(PDO::FETCH_ASSOC);
    echo "   ✓ " . count($inventario_sp) . " productos encontrados en el SP.\n\n";

    // Paso 2: Para cada producto en el SP, sincronizar con tbl_inventario_producto
    echo "2. Sincronizando con tbl_inventario_producto...\n";
    $sincronizados = 0;
    $actualizados = 0;
    
    foreach ($inventario_sp as $item) {
        $id_producto = $item['ID_PRODUCTO'] ?? null;
        $cantidad = isset($item['CANTIDAD']) ? (float)$item['CANTIDAD'] : 0;
        
        if (!$id_producto) continue;

        // Verificar si existe en tbl_inventario_producto
        $check_sql = "SELECT COUNT(*) FROM tbl_inventario_producto WHERE ID_PRODUCTO = :id";
        $check_q = $con->prepare($check_sql);
        $check_q->execute([':id' => $id_producto]);
        $existe = intval($check_q->fetchColumn()) > 0;

        if (!$existe) {
            // Insertar nuevo registro
            $insert_sql = "INSERT INTO tbl_inventario_producto (ID_PRODUCTO, CANTIDAD, MINIMO, MAXIMO) 
                          VALUES (:id, :cantidad, 0, 0)";
            $insert_q = $con->prepare($insert_sql);
            $insert_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
            $sincronizados++;
            echo "   ✓ Producto $id_producto sincronizado (Cantidad: $cantidad)\n";
        } else {
            // Actualizar cantidad si es diferente
            $update_sql = "UPDATE tbl_inventario_producto SET CANTIDAD = :cantidad WHERE ID_PRODUCTO = :id";
            $update_q = $con->prepare($update_sql);
            $update_q->execute([':id' => $id_producto, ':cantidad' => $cantidad]);
            if ($update_q->rowCount() > 0) {
                $actualizados++;
                echo "   ↻ Producto $id_producto actualizado (Cantidad: $cantidad)\n";
            }
        }
    }

    echo "\n✅ RESULTADO:\n";
    echo "   - Productos sincronizados (nuevos): $sincronizados\n";
    echo "   - Productos actualizados: $actualizados\n";
    echo "   - Total procesado: " . (count($inventario_sp)) . "\n\n";

    // Paso 3: Verificación final
    echo "3. Verificación final de consistencia...\n";
    $check_final = "SELECT COUNT(*) FROM tbl_producto p 
                    WHERE p.ESTADO = 'ACTIVO' 
                    AND EXISTS (SELECT 1 FROM tbl_inventario_producto ip WHERE ip.ID_PRODUCTO = p.ID_PRODUCTO)";
    $q_final = $con->query($check_final);
    $total_sincronizado = intval($q_final->fetchColumn());
    echo "   ✓ Productos ACTIVOS con inventario registrado: $total_sincronizado\n\n";

    echo "✅ SINCRONIZACIÓN COMPLETADA CON ÉXITO\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
