<?php
use App\models\produccionModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$filtroNombre = trim($_GET['filtro_nombre'] ?? '');
$filtroEstado = trim($_GET['filtro_estado'] ?? '');
$usuario = $_SESSION['NOMBRE_USUARIO'] ?? $_SESSION['usuario'] ?? 'Sistema';

try {
    require_once dirname(__DIR__, 2) . '/models/produccionModel.php';
    $respuesta = produccionModel::obtenerProductos([
        'filtro_nombre' => $filtroNombre !== '' ? $filtroNombre : null,
        'filtro_estado' => $filtroEstado !== '' ? $filtroEstado : null,
    ]);
    $productos = $respuesta['success'] ? ($respuesta['data'] ?? []) : [];
} catch (Exception $e) {
    error_log('Error al generar reporte de productos: ' . $e->getMessage());
    $productos = [];
}

$totalProductos = count($productos);
$totalActivos = count(array_filter($productos, fn($p) => ($p['ESTADO'] ?? '') === 'ACTIVO'));
$totalInactivos = count(array_filter($productos, fn($p) => ($p['ESTADO'] ?? '') === 'INACTIVO'));
$fechaGeneracion = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Productos</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f5f9;
            margin: 20px;
            color: #1f2937;
        }
        .report-card {
            max-width: 960px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(15, 36, 84, 0.15);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .report-header {
            background: linear-gradient(135deg, #d97706, #f97316);
            color: #fff;
            padding: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        .header-details h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        .header-details p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            padding: 24px 30px 10px;
        }
        .summary-box {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            background: #fafafa;
        }
        .summary-box span {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }
        .summary-box strong {
            font-size: 20px;
            color: #0f172a;
        }
        .filters {
            padding: 0 30px 20px;
            font-size: 13px;
            color: #475569;
        }
        .filters strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 13px;
        }
        thead th {
            background: linear-gradient(90deg, #ea580c, #f97316);
            color: #fff;
            text-align: left;
            padding: 12px 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 11px;
        }
        tbody td {
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 10px;
        }
        tbody tr:nth-child(odd) {
            background: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-activo {
            background: rgba(34,197,94,0.15);
            color: #15803d;
        }
        .badge-inactivo {
            background: rgba(248,113,113,0.18);
            color: #b91c1c;
        }
        .footer-note {
            padding: 16px 30px 26px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .empty-state {
            padding: 40px 30px;
            text-align: center;
            color: #475569;
        }
    </style>
</head>
<body>
<div class="report-card" id="reporteProductos">
    <div class="report-header">
        <div class="header-icon">📦</div>
        <div class="header-details">
            <h1>Reporte de Productos</h1>
            <p>Generado el <?= htmlspecialchars($fechaGeneracion) ?> &middot; Usuario: <?= htmlspecialchars($usuario) ?></p>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-box">
            <span>Total productos</span>
            <strong><?= $totalProductos ?></strong>
        </div>
        <div class="summary-box">
            <span>Activos</span>
            <strong><?= $totalActivos ?></strong>
        </div>
        <div class="summary-box">
            <span>Inactivos</span>
            <strong><?= $totalInactivos ?></strong>
        </div>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        <?= $filtroNombre !== '' ? "Nombre contiene \"".htmlspecialchars($filtroNombre)."\"" : "Sin filtro por nombre" ?> |
        <?= $filtroEstado !== '' ? "Estado: ".htmlspecialchars($filtroEstado) : "Todos los estados" ?>
    </div>

    <?php if (empty($productos)): ?>
        <div class="empty-state">
            No se encontraron productos con los filtros seleccionados.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripcion</th>
                    <th class="text-right">Precio</th>
                    <th class="text-center">Unidad</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Min</th>
                    <th class="text-right">Max</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($producto['NOMBRE'] ?? '-') ?></strong><br>
                        <small>ID: <?= htmlspecialchars($producto['ID_PRODUCTO'] ?? '-') ?></small>
                    </td>
                    <td><?= htmlspecialchars($producto['DESCRIPCION'] ?? '-') ?></td>
                    <td class="text-right">L <?= number_format((float)($producto['PRECIO'] ?? 0), 2) ?></td>
                    <td class="text-center"><?= htmlspecialchars($producto['UNIDAD'] ?? '-') ?></td>
                    <td class="text-right"><?= number_format((float)($producto['CANTIDAD'] ?? 0), 2) ?></td>
                    <td class="text-right"><?= number_format((float)($producto['MINIMO'] ?? 0), 2) ?></td>
                    <td class="text-right"><?= number_format((float)($producto['MAXIMO'] ?? 0), 2) ?></td>
                    <td class="text-center">
                        <?php $estado = strtoupper($producto['ESTADO'] ?? ''); ?>
                        <span class="badge <?= $estado === 'ACTIVO' ? 'badge-activo' : 'badge-inactivo' ?>">
                            <?= $estado !== '' ? $estado : 'N/D' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer-note">
        Documento generado automaticamente por el Sistema de Gestion Tesoro D' MIMI.
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reporte = document.getElementById('reporteProductos');
    if (!reporte) return;

    const fechaArchivo = new Date().toISOString().slice(0,19).replace(/[:T]/g, '-');
    const opciones = {
        margin:       0.5,
        filename:     `reporte_productos_${fechaArchivo}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    setTimeout(() => {
        html2pdf().set(opciones).from(reporte).save();
    }, 500);
});
</script>
</body>
</html>
