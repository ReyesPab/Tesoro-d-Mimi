<?php
use App\models\produccionModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/models/produccionModel.php';

$respuesta = produccionModel::obtenerTodasLasRecetas();
$recetas = !empty($respuesta['success']) ? ($respuesta['data'] ?? []) : [];
$usuario = $_SESSION['NOMBRE_USUARIO'] ?? $_SESSION['usuario'] ?? 'Sistema';
$fechaGeneracion = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Libro de Recetas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6fb;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .book-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }
        .book-header {
            background: linear-gradient(135deg, #d97706, #f97316);
            border-radius: 20px;
            padding: 28px;
            color: #fff;
            margin-bottom: 24px;
            box-shadow: 0 18px 40px rgba(217,119,6,0.3);
        }
        .book-header h1 {
            margin: 0;
            font-size: 26px;
        }
        .book-header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .recipe-card {
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 14px 30px rgba(15,23,42,0.12);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .recipe-title {
            font-size: 18px;
            font-weight: 700;
            color: #b45309;
            margin-bottom: 12px;
        }
        .recipe-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .chip {
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        thead th {
            background: linear-gradient(90deg, #ea580c, #f97316);
            color: #fff;
            text-align: left;
            padding: 8px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        tbody td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px;
        }
        tfoot td {
            border-top: 2px solid #0f172a;
            padding: 10px 8px;
            font-weight: 700;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-note {
            text-align: center;
            padding: 16px;
            font-size: 12px;
            color: #94a3b8;
        }
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #475569;
        }
    </style>
</head>
<body>
<div class="book-wrapper" id="libroRecetas">
    <div class="book-header">
        <h1>Compendio de Recetas</h1>
        <p>Generado el <?= htmlspecialchars($fechaGeneracion) ?> &middot; Usuario: <?= htmlspecialchars($usuario) ?></p>
        <p>Total de recetas: <?= count($recetas) ?></p>
    </div>

    <?php if (empty($recetas)): ?>
        <div class="empty">No hay recetas registradas.</div>
    <?php else: ?>
        <?php foreach ($recetas as $receta): ?>
            <div class="recipe-card">
                <div class="recipe-title"><?= htmlspecialchars($receta['NOMBRE_PRODUCTO']) ?></div>
                <div class="recipe-meta">
                    <span class="chip"><?= $receta['TOTAL_INGREDIENTES'] ?> ingredientes</span>
                    <span class="chip">Costo total: L <?= number_format($receta['COSTO_TOTAL'], 2) ?></span>
                    <span class="chip">Creado por <?= htmlspecialchars($receta['CREADO_POR'] ?? 'N/D') ?></span>
                    <span class="chip">Fecha <?= date('d/m/Y', strtotime($receta['FECHA_CREACION'] ?? 'now')) ?></span>
                </div>
                <?php if (!empty($receta['DESCRIPCION_PRODUCTO'])): ?>
                    <p style="color:#475569;margin-top:-4px;margin-bottom:14px;">
                        <?= htmlspecialchars($receta['DESCRIPCION_PRODUCTO']) ?>
                    </p>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-right">Costo Unit.</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receta['INGREDIENTES'] as $ingrediente): ?>
                            <tr>
                                <td><?= htmlspecialchars($ingrediente['NOMBRE_MATERIA_PRIMA']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($ingrediente['UNIDAD']) ?></td>
                                <td class="text-center"><?= number_format($ingrediente['CANTIDAD_NECESARIA'], 3) ?></td>
                                <td class="text-right">L <?= number_format($ingrediente['PRECIO_PROMEDIO'], 2) ?></td>
                                <td class="text-right">L <?= number_format($ingrediente['COSTO_INGREDIENTE'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Costo total</td>
                            <td class="text-right">L <?= number_format($receta['COSTO_TOTAL'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="footer-note">
        Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const libro = document.getElementById('libroRecetas');
    if (!libro) return;
    const fechaArchivo = new Date().toISOString().slice(0,19).replace(/[:T]/g, '-');
    const opciones = {
        margin: 0.35,
        filename: `recetas_${fechaArchivo}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    setTimeout(() => html2pdf().set(opciones).from(libro).save(), 700);
});
</script>
</body>
</html>
