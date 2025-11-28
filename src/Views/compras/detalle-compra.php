<?php
session_start();
use App\models\comprasModel;

$id_compra = $_GET['id_compra'] ?? null;

if (!$id_compra || !is_numeric($id_compra)) {
    header('Location: /sistema/public/index.php?route=consultar-compras');
    exit;
}

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    $resultado = $comprasModel->obtenerDetalleCompra($id_compra);
    
    if (!$resultado['success']) {
        throw new Exception($resultado['message']);
    }
    
    $compra = $resultado['data']['compra'];
    $detalles = $resultado['data']['detalles'];
    
} catch (Exception $e) {
    error_log("Error al cargar detalle de compra: " . $e->getMessage());
    header('Location: /sistema/public/index.php?route=consultar-compras');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Detalle Compra #<?php echo $compra['ID_COMPRA']; ?> - Tesoro D' MIMI</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --border-color: #dee2e6;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px 25px;
        }
        
        .badge-estado {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid var(--secondary-color);
        }
        
        .info-card h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .info-line {
            display: flex;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .info-label {
            font-weight: 600;
            min-width: 100px;
            color: #495057;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        .table-detalles {
            font-size: 0.9rem;
        }
        
        .table-detalles th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        .table-detalles td {
            vertical-align: middle;
        }
        
        .total-section {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
        }
        
        .total-grande {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--success-color);
            border-top: 2px solid var(--border-color);
            padding-top: 10px;
        }
        
        .btn-group-compact .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .table-detalles {
                font-size: 0.8rem;
            }
        }
        
        @media print {
            .btn-group {
                display: none;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid #dee2e6;
            }
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="container-fluid">
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">Detalle de Compra</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=compras">Compras</a></li>
                                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=consultar-compras">Consultar Compras</a></li>
                                <li class="breadcrumb-item active">Detalle #<?php echo $compra['ID_COMPRA']; ?></li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group btn-group-compact">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                            <i class="bi bi-arrow-left"></i> Volver
                        </button>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card Principal -->
            <div class="card">
                <!-- Header de la Compra -->
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">Compra #<?php echo $compra['ID_COMPRA']; ?></h4>
                            <p class="mb-0 opacity-75">Tesoro D' MIMI</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge-estado bg-success">
                                <?php echo $compra['ESTADO_COMPRA']; ?>
                            </span>
                            <div class="mt-2">
                                <small>Fecha: <?php echo date('d-m-Y H:i', strtotime($compra['FECHA_COMPRA'])); ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información General -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="info-grid">
                                <!-- Información del Proveedor -->
                                <div class="info-card">
                                    <h6><i class="bi bi-building me-2"></i>Proveedor</h6>
                                    <div class="info-line">
                                        <span class="info-label">Empresa:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($compra['PROVEEDOR']); ?></span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">Contacto:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($compra['CONTACTO'] ?? 'No especificado'); ?></span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">Teléfono:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($compra['TELEFONO'] ?? 'No especificado'); ?></span>
                                    </div>
                                </div>

                                <!-- Información de la Compra -->
                                <div class="info-card">
                                    <h6><i class="bi bi-info-circle me-2"></i>Información</h6>
                                    <div class="info-line">
                                        <span class="info-label">Usuario:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($compra['USUARIO']); ?></span>
                                    </div>
                                    <div class="info-line">
                                        <span class="info-label">Total:</span>
                                        <span class="info-value fw-bold text-success">L <?php echo number_format(floatval($compra['TOTAL_COMPRA']), 2); ?></span>
                                    </div>
                                    <?php if (!empty($compra['OBSERVACIONES'])): ?>
                                    <div class="info-line">
                                        <span class="info-label">Observaciones:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($compra['OBSERVACIONES']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de Productos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Productos Comprados</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-detalles">
                                    <thead>
                                        <tr>
                                            <th width="40%">Producto</th>
                                            <th width="10%" class="text-center">Unidad</th>
                                            <th width="15%" class="text-center">Cantidad</th>
                                            <th width="15%" class="text-end">Precio Unitario</th>
                                            <th width="20%" class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $subtotal = 0;
                                        foreach ($detalles as $detalle): 
                                            $totalLinea = floatval($detalle['CANTIDAD']) * floatval($detalle['PRECIO_UNITARIO']);
                                            $subtotal += $totalLinea;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($detalle['MATERIA_PRIMA']); ?></strong>
                                            </td>
                                            <td class="text-center"><?php echo htmlspecialchars($detalle['UNIDAD']); ?></td>
                                            <td class="text-center"><?php echo number_format(floatval($detalle['CANTIDAD']), 2); ?></td>
                                            <td class="text-end">L <?php echo number_format(floatval($detalle['PRECIO_UNITARIO']), 2); ?></td>
                                            <td class="text-end fw-bold">L <?php echo number_format($totalLinea, 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Totales -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="total-section">
                                <h6 class="text-center mb-3">Resumen de Totales</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span class="fw-bold">L <?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                
                                <?php
                                $descuentoPorcentaje = floatval($compra['DESCUENTO'] ?? 0);
                                $descuentoMonto = $subtotal * ($descuentoPorcentaje / 100);
                                $subtotalConDescuento = $subtotal - $descuentoMonto;
                                $tasaImpuestos = 0.00;
                                $totalImpuestos = 0.00;
                                $envio = floatval($compra['ENVIO'] ?? 0);
                                $totalFinal = $subtotalConDescuento + $totalImpuestos + $envio;
                                ?>
                                
                                <?php if ($descuentoPorcentaje > 0): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Descuento (<?php echo number_format($descuentoPorcentaje, 2); ?>%):</span>
                                    <span class="fw-bold text-danger">- L <?php echo number_format($descuentoMonto, 2); ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal con descuento:</span>
                                    <span class="fw-bold">L <?php echo number_format($subtotalConDescuento, 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($envio > 0): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Envío/Almacenaje:</span>
                                    <span class="fw-bold">L <?php echo number_format($envio, 2); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between total-grande">
                                    <span>TOTAL:</span>
                                    <span>L <?php echo number_format($totalFinal, 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function imprimirDetalle() {
            window.print();
        }
        
        // Auto-print si se desea
        // setTimeout(() => {
        //     window.print();
        // }, 1000);
    </script>
    
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>