<?php
require_once __DIR__ . '/src/db/connectionDB.php';

use App\db\connectionDB;

$con = connectionDB::getConnection();

echo "=== Estructura tbl_producto ===\n";
$sql = "DESCRIBE tbl_producto";
$q = $con->query($sql);
while($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}

echo "\n=== Estructura tbl_inventario_producto ===\n";
$sql = "DESCRIBE tbl_inventario_producto";
$q = $con->query($sql);
while($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}

echo "\n=== Ejemplo de producto con cantidad en ambas tablas ===\n";
$sql = "SELECT p.ID_PRODUCTO, p.NOMBRE, 
               (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='tbl_producto' AND COLUMN_NAME='CANTIDAD' LIMIT 1) as tiene_cantidad_en_producto,
               ip.CANTIDAD as cantidad_en_inventario
        FROM tbl_producto p
        LEFT JOIN tbl_inventario_producto ip ON p.ID_PRODUCTO = ip.ID_PRODUCTO
        LIMIT 5";
$q = $con->query($sql);
while($r = $q->fetch(PDO::FETCH_ASSOC)) {
    print_r($r);
}

echo "\n=== Verificar si tbl_producto tiene CANTIDAD ===\n";
$sql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='tbl_producto' AND COLUMN_NAME='CANTIDAD'";
$q = $con->query($sql);
$count = $q->fetchColumn();
echo "¿tbl_producto tiene columna CANTIDAD? " . ($count > 0 ? "SÍ" : "NO") . "\n";
?>
