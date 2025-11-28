<?php

// Autoload
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

// Dependencias
require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
require_once dirname(__DIR__, 2) . '/Views//libs/fpdf/fpdf.php';

use App\models\comprasModel;

@ini_set('display_errors', '0');
@error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

class PDF extends \FPDF
{
    public function iso($text)
    {
        return mb_convert_encoding((string)$text, 'ISO-8859-1', 'UTF-8');
    }

    function Header()
    {
        $logo = __DIR__ . '/../assets/img/Tesorodemimi.jpg';
        if (file_exists($logo)) {
            $this->Image($logo, 10, 6, 20);
        }
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(80);
        $this->Cell(120, 10, $this->iso("REPORTE DE MATERIA PRIMA"), 0, 1, 'C');
        $this->Ln(2);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->iso('Fecha de emisión: ') . date('d/m/Y H:i'), 0, 1, 'R');
        $this->Ln(3);

        // Encabezado de tabla
        $this->SetFillColor(255, 204, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(45, 8, $this->iso('Nombre'), 1, 0, 'C', true);
        $this->Cell(20, 8, $this->iso('Unidad'), 1, 0, 'C', true);
        $this->Cell(22, 8, $this->iso('Cantidad'), 1, 0, 'C', true);
        $this->Cell(20, 8, $this->iso('Mínimo'), 1, 0, 'C', true);
        $this->Cell(20, 8, $this->iso('Máximo'), 1, 0, 'C', true);
        $this->Cell(28, 8, $this->iso('Nivel'), 1, 0, 'C', true);
        $this->Cell(28, 8, $this->iso('Precio Prom.'), 1, 0, 'C', true);
        $this->Cell(32, 8, $this->iso('Fecha Creación'), 1, 0, 'C', true);
        $this->Cell(20, 8, $this->iso('Estado'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->iso("Tesoro D' MIMI © " . date('Y')), 0, 0, 'C');
    }
}

// Filtros (opcionales)
$filtroNombre = $_GET['filtro_nombre'] ?? '';
$filtroNivel  = $_GET['filtro_nivel'] ?? '';

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

try {
    $data = comprasModel::obtenerMateriaPrima();

    // Filtrar en PHP si llegan filtros
    $rows = array_filter($data, function ($row) use ($filtroNombre, $filtroNivel) {
        $ok = true;
        if ($filtroNombre !== '') {
            $ok = $ok && (stripos($row['NOMBRE'] ?? '', $filtroNombre) !== false);
        }
        if ($filtroNivel !== '') {
            // Derivar nivel por comparación si es posible
            $cant = (float)($row['CANTIDAD'] ?? $row['CANTIDAD_ACTUAL'] ?? $row['EXISTENCIA'] ?? 0);
            $min  = (float)($row['MINIMO'] ?? $row['STOCK_MINIMO'] ?? 0);
            $max  = (float)($row['MAXIMO'] ?? $row['STOCK_MAXIMO'] ?? 0);
            $nivel = '';
            if ($min && $cant <= $min) { $nivel = 'CRITICO'; }
            elseif ($max && $cant > $max) { $nivel = 'EXCESO'; }
            else { $nivel = 'NORMAL'; }
            $ok = $ok && (strtoupper($filtroNivel) === $nivel);
        }
        return $ok;
    });

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $nombre = $row['NOMBRE'] ?? '';
            $unidad = $row['UNIDAD'] ?? ($row['UNIDAD_MEDIDA'] ?? '');
            $cant   = (float)($row['CANTIDAD'] ?? $row['CANTIDAD_ACTUAL'] ?? $row['EXISTENCIA'] ?? 0);
            $min    = (float)($row['MINIMO'] ?? $row['STOCK_MINIMO'] ?? 0);
            $max    = (float)($row['MAXIMO'] ?? $row['STOCK_MAXIMO'] ?? 0);
            $precio = (float)($row['PRECIO_PROMEDIO'] ?? $row['PRECIO'] ?? 0);
            $fecha  = $row['FECHA_CREACION_FORMATEADA'] ?? ($row['FECHA_CREACION'] ?? '');
            $estado = $row['ESTADO'] ?? '';

            // Nivel
            $nivel = '';
            if ($min && $cant <= $min) { $nivel = 'CRITICO'; }
            elseif ($max && $cant > $max) { $nivel = 'EXCESO'; }
            else { $nivel = 'NORMAL'; }

            $pdf->Cell(45, 7, $pdf->iso($nombre), 1);
            $pdf->Cell(20, 7, $pdf->iso($unidad), 1, 0, 'C');
            $pdf->Cell(22, 7, number_format($cant, 2), 1, 0, 'R');
            $pdf->Cell(20, 7, number_format($min, 2), 1, 0, 'R');
            $pdf->Cell(20, 7, number_format($max, 2), 1, 0, 'R');
            $pdf->Cell(28, 7, $pdf->iso($nivel), 1, 0, 'C');
            $pdf->Cell(28, 7, 'L ' . number_format($precio, 2), 1, 0, 'R');
            $pdf->Cell(32, 7, $pdf->iso($fecha), 1, 0, 'C');
            $pdf->Cell(20, 7, $pdf->iso($estado), 1, 1, 'C');
        }
    } else {
        $pdf->Cell(0, 10, $pdf->iso('No se encontraron registros de materia prima.'), 1, 1, 'C');
    }

    if (ob_get_length()) { @ob_end_clean(); }
    header('Content-Type: application/pdf');
    $pdf->Output('I', 'reporte_materia_prima.pdf');
} catch (\Exception $e) {
    if (ob_get_length()) { @ob_end_clean(); }
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Error al generar el reporte: ' . $e->getMessage();
}

