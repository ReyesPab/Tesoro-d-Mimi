<?php
session_start();
use App\models\comprasModel;

// Obtener filtros desde la URL
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_proveedor = $_GET['id_proveedor'] ?? '';
$estado_compra = $_GET['estado_compra'] ?? '';

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    
    // Obtener compras con los filtros aplicados
    $compras = $comprasModel->obtenerComprasFiltradas([
        'fecha_inicio'  => $fecha_inicio,
        'fecha_fin'     => $fecha_fin,
        'id_proveedor'  => $id_proveedor,
        'estado_compra' => $estado_compra,
    ]);
    
} catch (Exception $e) {
    die("Error al cargar compras para reporte: " . $e->getMessage());
}

// Formatear fecha actual
$fechaActual = date('d/m/Y H:i');
$logoUrl = '/sistema/public/src/Views/assets/img/Tesorodemimi.jpg';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras - Tesoro D' MIMI</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @page {
            margin: 15mm 10mm;
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 10px;
                color: #666;
            }
        }
        
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            font-size: 9px; 
            color: #333;
            line-height: 1.3;
        }
        
        .header { 
            background: linear-gradient(90deg, #D7A86E, #E38B29);
            color: #ffffff; 
            padding: 12px 15px; 
            margin-bottom: 12px;
            border-radius: 6px;
        }
        
        .brand { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        
        .brand img { 
            width: 45px; 
            height: 45px; 
            border-radius: 5px; 
            object-fit: cover; 
            background: #fff; 
        }
        
        .brand-text { 
            display: flex; 
            flex-direction: column; 
        }
        
        .header h1 { 
            margin: 0; 
            font-size: 18px; 
            letter-spacing: .5px; 
        }
        
        .header h2 { 
            margin: 2px 0 3px; 
            font-size: 11px; 
            font-weight: normal; 
            opacity: .9; 
        }
        
        .header .fecha { 
            font-size: 9px; 
            opacity: .9; 
        }
        
        .filtros-aplicados {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 10px;
            font-size: 8px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 8px; 
            font-size: 7px; 
        }
        
        th { 
            background: linear-gradient(90deg, #D7A86E, #E38B29);
            color: #fff; 
            padding: 6px 4px; 
            text-align: left; 
            border: 1px solid #B97222; 
            font-weight: bold;
        }
        
        td { 
            border: 1px solid #dee2e6; 
            padding: 4px; 
            vertical-align: top; 
        }
        
        .footer { 
            text-align: center; 
            padding: 8px; 
            color: #6c757d; 
            font-size: 8px; 
            border-top: 1px solid #dee2e6; 
            margin-top: 12px;
        }
        
        .text-right { 
            text-align: right; 
        }
        
        .text-center { 
            text-align: center; 
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <img src="<?= $logoUrl ?>" alt="Logo">
            <div class="brand-text">
                <h1>Reporte de Compras</h1>
                <h2>Tesoro D' MIMI</h2>
                <div class="fecha">Generado el: <?= $fechaActual ?></div>
            </div>
        </div>
    </div>
    
    <div class="filtros-aplicados">
        <strong>Filtros Aplicados:</strong><br>
        Fecha: <?= $fecha_inicio ? $fecha_inicio . ' a ' . $fecha_fin : 'Todas las fechas' ?><br>
        Proveedor: <?= $id_proveedor ? 'Filtrado' : 'Todos los proveedores' ?><br>
        Estado: <?= $estado_compra ? $estado_compra : 'Todos los estados' ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="15%">Proveedor</th>
                <th width="12%">Usuario</th>
                <th width="18%">Materia Prima</th>
                <th width="8%">Cantidad</th>
                <th width="8%">Unidad</th>
                <th width="10%">Precio Unitario</th>
                <th width="10%">Subtotal</th>
                <th width="10%">Fecha</th>
                <th width="9%">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($compras as $compra): ?>
            <tr>
                <td><?= htmlspecialchars($compra['PROVEEDOR']) ?></td>
                <td><?= htmlspecialchars($compra['USUARIO']) ?></td>
                <td><?= htmlspecialchars($compra['MATERIA_PRIMA']) ?></td>
                <td class="text-right"><?= number_format($compra['CANTIDAD'], 2) ?></td>
                <td class="text-center"><?= htmlspecialchars($compra['UNIDAD']) ?></td>
                <td class="text-right">L <?= number_format($compra['PRECIO_UNITARIO'], 2) ?></td>
                <td class="text-right">L <?= number_format($compra['SUBTOTAL'], 2) ?></td>
                <td><?= date('d/m/Y', strtotime($compra['FECHA_COMPRA'])) ?></td>
                <td class="text-center"><?= $compra['ESTADO_COMPRA'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
    </div>

    <script>
        window.onload = function() {
            html2pdf()
                .set({
                    margin: [15, 10, 15, 10],
                    filename: 'reporte_compras_<?= date('Y-m-d') ?>.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                })
                .from(document.body)
                .save();
        };
    </script>
</body>
</html>
