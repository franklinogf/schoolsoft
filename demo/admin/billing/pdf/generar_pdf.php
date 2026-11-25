<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF as BasePDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$school = new School(Session::id());
$year = $school->info('year2');

$colegioRow = DB::table('colegio')->where('usuario', 'administrador')->first();
$row = $colegioRow ? array_values((array) $colegioRow) : [];

if (empty($_POST['grupos'])) {
    die('Debes seleccionar al menos un grupo.');
}

$grupos = $_POST['grupos'];
$codigo_fijo = 2473;

foreach ($grupos as $g) {
    if (!preg_match('/^([0-9]{2}|K-EO|PK|M )$/', $g)) {
    }
}

if (empty($_POST['mes'])) {
    die('Debes seleccionar un mes.');
}
$mes = $_POST['mes'];
if (!preg_match('/^(0[1-9]|1[0-2])$/', $mes)) {
    die("Mes no valido: $mes");
}
$mes_int = intval($mes);

// NUEVO: Tipo de listado
if (empty($_POST['tipo_listado'])) {
    die('Debes seleccionar un tipo de listado.');
}
$tipo_listado = $_POST['tipo_listado'];
if (!in_array($tipo_listado, ['1', '2', '3'])) {
    die("Tipo de listado no valido: $tipo_listado");
}

// Definir orden segun tipo
if ($tipo_listado == '1') {
    $order_by = 'nombre';
} elseif ($tipo_listado == '2') {
    $order_by = 'id';
} else {
    $order_by = 'grado';
}

class PDF extends BasePDF
{
    function Header()
    {
        global $row;
        global $year;
        global $mes;

        $meses_palabra = [
            '01' => 'ENERO',
            '02' => 'FEBRERO',
            '03' => 'MARZO',
            '04' => 'ABRIL',
            '05' => 'MAYO',
            '06' => 'JUNIO',
            '07' => 'JULIO',
            '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE',
            '10' => 'OCTUBRE',
            '11' => 'NOVIEMBRE',
            '12' => 'DICIEMBRE'
        ];
        $mes_nombre = isset($meses_palabra[$mes]) ? $meses_palabra[$mes] : $mes;

        $this->Image('../../../logo/logo.gif', 10, 10, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 5, $row[0] ?? '', 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Ln(2);
        $this->Cell(80);
        $this->Cell(30, 2, $row[1] ?? '', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 2, $row[2] ?? '', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 2, ($row[3] ?? '') . ', ' . ($row[4] ?? '') . ' ' . ($row[5] ?? ''), 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 3, 'Tel. ' . ($row[12] ?? '') . ' Fax ' . ($row[13] ?? ''), 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 3, $row[20] ?? '', 0, 0, 'C');
        // Salto de linea
        $this->Ln(7);

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 8, mb_convert_encoding('LISTADO OFICIAL - HORARIO EXTENDIDO - ' . $year, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(37, 99, 235);
        $this->Cell(0, 7, mb_convert_encoding('MES: ' . $mes_nombre, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);

        $this->SetFont('Arial', '', 10);
        $this->Ln(3);
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$like_parts = [];
foreach ($grupos as $g) {
    $g_esc = str_replace("'", "''", $g);
    $like_parts[] = "grado LIKE '%" . $g_esc . "%'";
}
$where_like = 'AND (' . implode(' OR ', $like_parts) . ') AND MONTH(fecha_d) = ' . $mes_int;

$registros = DB::table('pagos')
    ->select('DISTINCT nombre, grado, codigo, id')
    ->where('year', $year)
    ->where('codigo', $codigo_fijo)
    ->whereRaw($where_like)
    ->orderBy($order_by)
    ->get();

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(30, 30, 30);
$pdf->Cell(0, 8, mb_convert_encoding('Grupos: ' . implode(', ', $grupos) . ' | Mes: ' . $mes, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
$pdf->Ln(2);

// Cabecera tabla
$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, '#', 1, 0, 'C', true);
$pdf->Cell(30, 6, 'CUENTA', 1, 0, 'C', true);
$pdf->Cell(100, 6, 'APELLIDOS Y NOMBRE', 1, 0, 'L', true);
$pdf->Cell(25, 6, 'GRADO', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(50, 50, 50);
$contador = 0;
$fill = false;

foreach ($registros as $reg) {
    $db_nombre = $reg->nombre;
    $db_grado = $reg->grado;
    $db_codigo = $reg->codigo;
    $db_id = $reg->id;
    $contador++;
    if ($fill) {
        $pdf->SetFillColor(248, 250, 252);
    } else {
        $pdf->SetFillColor(255, 255, 255);
    }

    $pdf->Cell(10, 5, $contador, 1, 0, 'C', true);
    $pdf->Cell(30, 5, $db_id, 1, 0, 'L', true);
    $pdf->Cell(100, 5, mb_convert_encoding($db_nombre, 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
    $pdf->Cell(25, 5, $db_grado, 1, 1, 'C', true);
    $fill = !$fill;
}

if ($contador == 0) {
    $pdf->Cell(0, 10, mb_convert_encoding('No se encontraron registros con codigo 2473 en esos grupos para el mes ' . $mes, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, mb_convert_encoding('Total registros: ' . $contador, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');

$nombre_archivo = 'Listado_2473_' . implode('_', $grupos) . '_mes_' . $mes . '.pdf';
$nombre_archivo = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $nombre_archivo);
$pdf->Output($nombre_archivo, 'I');
