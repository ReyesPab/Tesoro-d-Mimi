<?php

// Asegurar autoload (por si se accede directo a la vista)
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

// Dependencias
require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
require_once dirname(__DIR__, 2) . '/Views/libs/fpdf/fpdf.php';

use App\models\comprasModel;

// Evitar que avisos/deprecations rompan la salida del PDF
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
        $this->Cell(100, 10, $this->iso("REPORTE DE COMPRAS - EL TESORO D' MIMI"), 0, 1, 'C');
        $this->Ln(5);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, $this->iso('Fecha de emisión: ') . date('d/m/Y'), 0, 1, 'R');
        $this->Ln(5);

        $this->SetFillColor(255, 204, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(10, 8, 'ID', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Proveedor', 1, 0, 'C', true);
        $this->Cell(45, 8, 'Usuario', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Materia Prima', 1, 0, 'C', true);
        $this->Cell(18, 8, 'Cant.', 1, 0, 'C', true);
        $this->Cell(18, 8, 'Unidad', 1, 0, 'C', true);
        $this->Cell(22, 8, 'P. Unitario', 1, 0, 'C', true);
        $this->Cell(22, 8, 'Subtotal', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Fecha', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $this->iso("Rosquillería El Tesoro D' Mimi © " . date('Y')), 0, 0, 'C');
    }
}

// Leer filtros desde GET
$filtros = [
    'fecha_inicio'  => $_GET['fecha_inicio']  ?? '',
    'fecha_fin'     => $_GET['fecha_fin']     ?? '',
    'id_proveedor'  => $_GET['id_proveedor']  ?? '',
    'estado_compra' => $_GET['estado_compra'] ?? ''
];

// Crear PDF
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

try {
    $compras = comprasModel::obtenerComprasFiltradas($filtros);

    if (!empty($compras)) {
        foreach ($compras as $compra) {
            $pdf->Cell(10, 7, $compra['ID_COMPRA'], 1);
            $pdf->Cell(40, 7, $pdf->iso($compra['PROVEEDOR']), 1);
            $pdf->Cell(45, 7, $pdf->iso($compra['USUARIO']), 1);
            $pdf->Cell(30, 7, $pdf->iso($compra['MATERIA_PRIMA']), 1);
            $pdf->Cell(18, 7, $compra['CANTIDAD'], 1, 0, 'C');
            $pdf->Cell(18, 7, $pdf->iso($compra['UNIDAD']), 1, 0, 'C');
            $pdf->Cell(22, 7, number_format($compra['PRECIO_UNITARIO'], 2), 1, 0, 'R');
            $pdf->Cell(22, 7, number_format($compra['SUBTOTAL'], 2), 1, 0, 'R');
            $pdf->Cell(30, 7, $compra['FECHA_COMPRA'], 1, 1, 'C');
        }
    } else {
        $pdf->Cell(0, 10, $pdf->iso('No se encontraron registros de compras.'), 1, 1, 'C');
    }

    if (ob_get_length()) { @ob_end_clean(); }
    header('Content-Type: application/pdf');
    $pdf->Output('I', 'Reporte_Consultas.pdf');
} catch (\Exception $e) {
    if (ob_get_length()) { @ob_end_clean(); }
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Error al generar el reporte: ' . $e->getMessage();
}
