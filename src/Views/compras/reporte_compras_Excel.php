<?php
// Asegurar autoload (por si la vista se abre directa)
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
use App\models\comprasModel;

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=reporte_compras_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Filtros desde GET
$filtros = [
    'fecha_inicio'  => $_GET['fecha_inicio']  ?? '',
    'fecha_fin'     => $_GET['fecha_fin']     ?? '',
    'id_proveedor'  => $_GET['id_proveedor']  ?? '',
    'estado_compra' => $_GET['estado_compra'] ?? ''
];

$compras = comprasModel::obtenerComprasFiltradas($filtros);

// BOM para que Excel detecte UTF-8 correctamente
echo "\xEF\xBB\xBF";

echo "<meta charset='UTF-8'>";

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<thead style='background-color: #ffcc00; font-weight: bold;'>";
echo "<tr>
        <th>ID</th>
        <th>Proveedor</th>
        <th>Usuario</th>
        <th>Materia Prima</th>
        <th>Cantidad</th>
        <th>Unidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
        <th>Fecha Compra</th>
        <th>Estado</th>
        <th>Total</th>
      </tr>";
echo "</thead><tbody>";

if (!empty($compras)) {
    foreach ($compras as $compra) {
        echo "<tr>
                <td>{$compra['ID_COMPRA']}</td>
                <td>" . htmlspecialchars($compra['PROVEEDOR']) . "</td>
                <td>" . htmlspecialchars($compra['USUARIO']) . "</td>
                <td>" . htmlspecialchars($compra['MATERIA_PRIMA']) . "</td>
                <td>{$compra['CANTIDAD']}</td>
                <td>" . htmlspecialchars($compra['UNIDAD']) . "</td>
                <td>" . number_format($compra['PRECIO_UNITARIO'], 2) . "</td>
                <td>" . number_format($compra['SUBTOTAL'], 2) . "</td>
                <td>{$compra['FECHA_COMPRA']}</td>
                <td>" . htmlspecialchars($compra['ESTADO_COMPRA']) . "</td>
                <td>" . number_format($compra['TOTAL_COMPRA'], 2) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='11'>No se encontraron registros de compras.</td></tr>";
}

echo "</tbody></table>";
?>
