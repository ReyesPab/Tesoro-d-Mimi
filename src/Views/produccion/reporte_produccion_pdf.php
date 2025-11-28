<?php
// Vista para generar PDF de una orden de producción específica usando html2pdf (lado cliente)
session_start();

use App\models\produccionModel;

// Validar parámetro
$id_produccion = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_produccion <= 0) {
    die('Parámetro id inválido o no proporcionado.');
}

// Cargar datos del detalle
try {
    // Asegurar disponibilidad del modelo cuando se carga esta vista directamente
    require_once dirname(__DIR__, 2) . '/models/produccionModel.php';

    $resultado = produccionModel::obtenerDetalleProduccion($id_produccion);
    if (!$resultado['success']) {
        throw new Exception($resultado['message'] ?? 'No fue posible obtener el detalle de producción');
    }

    $data = $resultado['data'];
    $p = $data['produccion'] ?? [];
    $materias = $data['materias_primas'] ?? [];
    $cardex = $data['movimientos_cardex'] ?? [];
    $bitacora = $data['bitacora'] ?? [];
    $estad = $data['estadisticas'] ?? [];

    // Helpers
    $fmt = function ($n, $dec = 2) {
        if ($n === null || $n === '' || !is_numeric($n)) return number_format(0, $dec);
        return number_format((float)$n, $dec);
    };

    // Helper para obtener un valor numérico a partir de múltiples posibles llaves
    $pickNum = function(array $row, array $keys, $default = 0) {
        foreach ($keys as $k) {
            if (isset($row[$k]) && is_numeric($row[$k])) {
                return (float)$row[$k];
            }
        }
        return (float)$default;
    };
    // Helper para obtener cadena de forma segura
    $pickStr = function(array $row, array $keys, $default = '-') {
        foreach ($keys as $k) {
            if (!empty($row[$k])) return (string)$row[$k];
        }
        return $default;
    };

} catch (Exception $e) {
    die('Error al cargar la producción: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Producción #<?= htmlspecialchars($p['ID_PRODUCCION'] ?? $id_produccion) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        /* Encabezado en tonos naranja del logo */
        .header { 
            background: linear-gradient(90deg, #D7A86E, #E38B29);
            color: #ffffff; 
            padding: 18px 22px; 
            border-radius: 8px 8px 0 0; 
        }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand img { width: 54px; height: 54px; border-radius: 8px; object-fit: cover; background: #fff; }
        .brand-text { display: flex; flex-direction: column; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: .5px; }
        .header h2 { margin: 2px 0 4px; font-size: 14px; font-weight: normal; opacity: .9; }
        .header .fecha { font-size: 12px; opacity: .9; }
        .section { padding: 18px 24px; }
    .section-title { font-size: 16px; font-weight: bold; color: #2c3e50; margin: 10px 0 12px; border-left: 4px solid #E38B29; padding-left: 10px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px 18px; font-size: 13px; }
        .grid .item { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 10px 12px; }
        .label { color: #6c757d; font-size: 12px; }
        .value { color: #212529; font-weight: 600; }
        .table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; }
        .table th { 
            background: linear-gradient(90deg, #D7A86E, #E38B29);
            color: #fff; 
            padding: 10px 8px; 
            text-align: left; 
            border: 1px solid #B97222; 
        }
        .table td { border: 1px solid #dee2e6; padding: 9px 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totales { background: #f8f9fa; border-top: 2px solid #dee2e6; padding: 16px 24px; }
        .row-flex { display: flex; justify-content: space-between; margin: 6px 0; font-size: 14px; }
        .total-strong { font-size: 16px; font-weight: bold; margin-top: 8px; padding-top: 8px; border-top: 2px solid #2c3e50; }
        .footer { text-align: center; padding: 16px 24px; color: #6c757d; font-size: 12px; border-top: 1px solid #dee2e6; }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .pill-plan { background: #ffc107; color: #000; }
        .pill-proc { background: #0dcaf0; color: #fff; }
        .pill-fin { background: #28a745; color: #fff; }
        .pill-canc { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
<div class="container" id="contenido-pdf">
    <div class="header">
        <div class="brand">
            <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Logo" crossorigin="anonymous">
            <div class="brand-text">
                <h1>Orden de Producción #<?= htmlspecialchars($p['ID_PRODUCCION'] ?? $id_produccion) ?></h1>
                <h2>Tesoro D' MIMI</h2>
                <div class="fecha">Generado el: <?= date('d/m/Y H:i') ?></div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Información General</div>
        <div class="grid">
            <div class="item"><div class="label">Estado</div><div class="value">
                <?php 
                    $estado = $p['ESTADO'] ?? $p['ESTADO_PRODUCCION'] ?? 'N/D';
                    $cls = 'pill';
                    if ($estado === 'PLANIFICADO') $cls .= ' pill-plan';
                    elseif ($estado === 'EN_PROCESO') $cls .= ' pill-proc';
                    elseif ($estado === 'FINALIZADO') $cls .= ' pill-fin';
                    elseif ($estado === 'CANCELADO') $cls .= ' pill-canc';
                ?>
                <span class="<?= $cls ?>"><?= htmlspecialchars($estado) ?></span>
            </div></div>
            <div class="item"><div class="label">Producto</div><div class="value"><?= htmlspecialchars($p['PRODUCTO'] ?? $p['NOMBRE_PRODUCTO'] ?? 'N/D') ?></div></div>
            <div class="item"><div class="label">Cantidad Planificada</div><div class="value"><?= $fmt($p['CANTIDAD_PLANIFICADA'] ?? 0, 0) ?> unidades</div></div>
            <div class="item"><div class="label">Cantidad Real</div><div class="value"><?= $fmt($p['CANTIDAD_REAL'] ?? 0, 0) ?> unidades</div></div>
            <div class="item"><div class="label">Fecha Inicio</div><div class="value"><?= !empty($p['FECHA_INICIO']) ? date('d/m/Y H:i', strtotime($p['FECHA_INICIO'])) : '-' ?></div></div>
            <div class="item"><div class="label">Fecha Fin</div><div class="value"><?= !empty($p['FECHA_FIN']) ? date('d/m/Y H:i', strtotime($p['FECHA_FIN'])) : '-' ?></div></div>
            <div class="item"><div class="label">Responsable</div><div class="value"><?= htmlspecialchars($p['RESPONSABLE'] ?? $p['USUARIO'] ?? 'Sistema') ?></div></div>
            <div class="item"><div class="label">Observación</div><div class="value"><?= htmlspecialchars($p['OBSERVACION'] ?? '-') ?></div></div>
            <div class="item"><div class="label">Eficiencia</div><div class="value"><?= $fmt($estad['eficiencia'] ?? 0, 2) ?>%</div></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Materias Primas</div>
        <table class="table">
            <thead>
                <tr>
                    <th width="34%">Materia Prima</th>
                    <th width="14%">Unidad</th>
                    <th width="14%" class="text-right">Cant. Usada</th>
                    <th width="14%" class="text-right">Costo Unit.</th>
                    <th width="24%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($materias)): $total_mat = 0; foreach ($materias as $mp):
                // Nombres y unidad
                $nombre = $pickStr($mp, ['MATERIA_PRIMA','NOMBRE','NOMBRE_MATERIA_PRIMA']);
                $unidad = $pickStr($mp, ['UNIDAD','DESC_UNIDAD']);

                // Costos y cantidades posibles según SP
                $costo_u = $pickNum($mp, ['COSTO_UNITARIO','PRECIO_PROMEDIO']);
                $sub = $pickNum($mp, ['SUBTOTAL']);
                $cant = $pickNum($mp, [
                    'CANTIDAD_USADA','CANTIDAD_CONSUMIDA','CANTIDAD_UTILIZADA','CANTIDAD_REQUERIDA','CANTIDAD_NECESARIA','CANTIDAD_MP','CANTIDAD'
                ]);

                // Fallback: si no hay cantidad pero sí subtotal y costo unitario, derivar cantidad
                if (($cant === 0.0 || $cant === 0) && $sub > 0 && $costo_u > 0) {
                    $cant = $sub / $costo_u;
                }
                // Fallback: si no hay subtotal pero sí cantidad y costo unitario, calcularlo
                if (($sub === 0.0 || $sub === 0) && $costo_u > 0 && $cant > 0) {
                    $sub = $costo_u * $cant;
                }

                $total_mat += (float)$sub;
            ?>
                <tr>
                    <td><?= htmlspecialchars($nombre) ?></td>
                    <td><?= htmlspecialchars($unidad) ?></td>
                    <td class="text-right"><?= $fmt($cant, 2) ?></td>
                    <td class="text-right">L <?= $fmt($costo_u, 2) ?></td>
                    <td class="text-right">L <?= $fmt($sub, 2) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5" class="text-center" style="padding:14px">No hay materias primas registradas</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="totales">
        <div class="row-flex">
            <div>Total de materiales</div>
            <div><strong><?= isset($estad['total_materiales']) ? intval($estad['total_materiales']) : (is_countable($materias) ? count($materias) : 0) ?></strong></div>
        </div>
        <div class="row-flex total-strong">
            <div>COSTO TOTAL DE MATERIALES</div>
            <div>L <?= $fmt($estad['costo_total_materiales'] ?? ($total_mat ?? 0), 2) ?></div>
        </div>
    </div>

    <!-- Se omiten las secciones de Cardex y Bitácora por solicitud: el reporte concluye en los totales. -->

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
    </div>
</div>

<script>
    function generarPDF() {
        const element = document.getElementById('contenido-pdf');
        const opt = {
            margin: [8, 8, 8, 8],
            filename: 'orden_produccion_<?= intval($p['ID_PRODUCCION'] ?? $id_produccion) ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(() => {
            console.log('PDF generado correctamente');
        }).catch(err => {
            console.error('Error generando PDF', err);
            alert('Error al generar el PDF. Intente nuevamente.');
        });
    }
    window.onload = function() { setTimeout(generarPDF, 600); };
</script>
</body>
</html>
