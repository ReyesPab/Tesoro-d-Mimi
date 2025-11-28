<?php
use App\models\produccionModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productoId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

require_once dirname(__DIR__, 2) . '/models/produccionModel.php';

$receta = [];
$mensaje = '';
if ($productoId) {
    $respuesta = produccionModel::obtenerRecetaPorProducto($productoId);
    if (!empty($respuesta['success']) && !empty($respuesta['data'])) {
        $receta = $respuesta['data'];
    } else {
        $mensaje = $respuesta['message'] ?? 'No se encontró la receta solicitada.';
    }
} else {
    $mensaje = 'Identificador de producto inválido.';
}

$generadoPor = $_SESSION['NOMBRE_USUARIO'] ?? $_SESSION['usuario'] ?? 'Sistema';
$fechaGeneracion = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Receta de Producción</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f5f9;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .report-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .report-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .report-header {
            background: linear-gradient(135deg, #d97706, #f97316);
            padding: 32px;
            color: #fff;
        }
        .report-header h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 0.5px;
        }
        .report-header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .section {
            padding: 24px 32px;
        }
        .section + .section {
            border-top: 1px solid #e5e7eb;
        }
        .section h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #d97706;
            margin-bottom: 16px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .info-box {
            background: #f9fafb;
            border-radius: 12px;
            padding: 14px;
            border: 1px solid #eee;
        }
        .info-box span {
            display: block;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }
        .info-box strong {
            font-size: 16px;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 10px;
        }
        thead th {
            background: linear-gradient(90deg, #ea580c, #f97316);
            color: #fff;
            text-align: left;
            padding: 10px;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        tbody td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px;
        }
        tfoot td {
            border-top: 2px solid #1f2937;
            font-weight: 700;
            padding: 12px 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            padding: 16px;
        }
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #475569;
            font-size: 15px;
        }
    </style>
</head>
<body>
<div class="report-container" id="recetaReporte">
    <div class="report-card">
        <div class="report-header">
            <h1>Receta de Producción <?php echo $receta ? '#'.htmlspecialchars($receta['ID_PRODUCTO']) : ''; ?></h1>
            <p>Generado el <?php echo htmlspecialchars($fechaGeneracion); ?> &middot; Por: <?php echo htmlspecialchars($generadoPor); ?></p>
        </div>

        <?php if (!$receta): ?>
            <div class="empty">
                <?php echo htmlspecialchars($mensaje ?: 'No hay información para mostrar.'); ?>
            </div>
        <?php else: ?>
            <div class="section">
                <h3>Información General</h3>
                <div class="info-grid">
                    <div class="info-box">
                        <span>Producto</span>
                        <strong><?php echo htmlspecialchars($receta['NOMBRE_PRODUCTO']); ?></strong>
                    </div>
                    <div class="info-box">
                        <span>Total ingredientes</span>
                        <strong><?php echo htmlspecialchars($receta['TOTAL_INGREDIENTES']); ?></strong>
                    </div>
                    <div class="info-box">
                        <span>Costo total</span>
                        <strong>L <?php echo number_format($receta['COSTO_TOTAL'] ?? 0, 2); ?></strong>
                    </div>
                    <div class="info-box">
                        <span>Fecha creación</span>
                        <strong><?php echo isset($receta['FECHA_CREACION']) ? date('d/m/Y', strtotime($receta['FECHA_CREACION'])) : '-'; ?></strong>
                    </div>
                </div>
                <?php if (!empty($receta['DESCRIPCION_PRODUCTO'])): ?>
                    <p style="margin-top:16px;color:#475569;"><?php echo htmlspecialchars($receta['DESCRIPCION_PRODUCTO']); ?></p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h3>Ingredientes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Costo unit.</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receta['INGREDIENTES'] as $ingrediente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ingrediente['NOMBRE_MATERIA_PRIMA']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($ingrediente['UNIDAD']); ?></td>
                                <td class="text-center"><?php echo number_format($ingrediente['CANTIDAD_NECESARIA'], 3); ?></td>
                                <td class="text-right">L <?php echo number_format($ingrediente['PRECIO_PROMEDIO'], 2); ?></td>
                                <td class="text-right">L <?php echo number_format($ingrediente['COSTO_INGREDIENTE'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Costo total de materiales</td>
                            <td class="text-right">L <?php echo number_format($receta['COSTO_TOTAL'] ?? 0, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>

        <div class="footer-note">
            Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reporte = document.getElementById('recetaReporte');
    if (!reporte) return;

    const fechaArchivo = new Date().toISOString().slice(0,19).replace(/[:T]/g, '-');
    const opciones = {
        margin: 0.5,
        filename: `receta_producto_<?php echo $productoId ?: 'sin_id'; ?>_${fechaArchivo}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    setTimeout(() => html2pdf().set(opciones).from(reporte).save(), 500);
});
</script>
</body>
</html>
