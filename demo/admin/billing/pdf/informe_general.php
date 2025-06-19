<?php
require('../fpdf16/fpdf.php');

class PDF extends FPDF
{
    //Cabecera de pgina
    function Header()
    {
        include('../control.php');
        $dat = "select * from colegio where usuario = 'administrador'";
        $tab = mysql_query($dat, $con) or die("problema con query");
        $row = mysql_fetch_row($tab);
        $data1 = "select * from padres where curso = '$_POST[curso]' AND year='$row[43]' ORDER BY apellidos, nombre";
        $data2 = "select * from profesor where usuario = '$_POST[usua]'";
        $tabla1 = mysql_query($data1, $con) or die("problema con query");
        $tabla2 = mysql_query($data2, $con) or die("problema con query");
        $row1 = mysql_fetch_row($tabla1);
        $num_res = mysql_num_rows($tabla1);

        //Logo
        $this->Image('../logo/logo.gif', 10, 10, 18);
        //Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        //Movernos a la derecha
        $this->Cell(80);
        //Ttulo
        $this->Cell(30, 5, $row[0], 0, 1, 'C');
        if ($row[52] == 'SI') {
            $this->Cell(80);
            $this->Cell(30, 8, $row[44], 0, 0, 'C');
        }
        $this->SetFont('Arial', '', 9);
        //Movernos a la derecha
        $this->Ln(1);
        $this->Cell(80);
        $this->Cell(30, 2, $row[1], 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 2, $row[2], 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 3, 'Tel. ' . $row[12] . ' Fax ' . $row[13], 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 3, $row[20], 0, 0, 'C');
        //Salto de lnea
        $this->Ln(7);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, 'HOJA DE PROMEDIO ANUAL', 0, 0, 'C');
        $this->Ln(7);
    }

    //Pie de pgina
    function Footer()
    {
    }
}

//Creacin del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times', '', 11);
include('../control.php');

$consult1 = "select * from colegio where usuario = 'administrador'";
$resultad1 = mysql_query($consult1);
$row4 = mysql_fetch_array($resultad1);
list($y1, $y2) = explode('-', $row4[43]);
$ya1 = '20' . $y1 . '-08-01';
$ya2 = '20' . $y1 . '-12-31';
$yb1 = '20' . $y2 . '-01-01';
$yb2 = '20' . $y2 . '-05-31';

$dat = "select * from cursos where year= '$row4[43]'";
$tabla1 = mysql_query($dat, $con) or die("problema con query");
while ($row1 = mysql_fetch_row($tabla1)) {
    $sql = "UPDATE padres SET credito='" . $row1[5] . "' WHERE year='$row4[43]' AND curso='" . $row1[1] . "'";
    $result = mysql_query($sql);

    $sql = "UPDATE padres4 SET credito='" . $row1[5] . "' WHERE year='$row4[43]' AND curso='" . $row1[1] . "'";
    $result = mysql_query($sql);
}

$data1 = "select * from profesor where usuario = '$_POST[usua]'";
$tabla1 = mysql_query($data1, $con) or die("problema con query 1");
$reg1 = mysql_fetch_row($tabla1);

$q = "select * from year where activo='' and grado='$reg1[15]' AND year='$row4[43]' ORDER BY apellidos, nombre";
$tabla1 = mysql_query($q, $dbh) or die("problema con query");
$reg = mysql_fetch_row($tabla1);
$result = mysql_query($q);
while ($row1 = mysql_fetch_array($result)) {
    $tn = 0;
    $tc = 0;
    //  $not = array(2,11);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(114, 5, 'NOMRE: ' . $row1[4] . ' ' . $row1[3], 1, 0, 'L');
    $pdf->Cell(20, 5, 'GRADO:', 1, 0, 'C');
    $pdf->Cell(20, 5, $row1[2], 1, 0, 'C');
    $pdf->Cell(20, 5, 'AÑO:', 1, 0, 'C');
    $pdf->Cell(20, 5, $row4[43], 1, 1, 'C');
    $data3 = "select * from materias where grado = '$reg1[15]'";
    $result3 = mysql_query($data3);
    $row2 = mysql_fetch_array($result3);

    $pdf->Cell(12, 5, '    ', 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[1], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[2], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[3], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[4], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[5], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[6], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[7], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[8], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[9], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[10], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[11], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, substr($row2[12], 0, -3), 1, 0, 'C');
    $pdf->Cell(14, 5, 'ELEC', 1, 0, 'C');
    $pdf->Cell(15, 5, 'Trab', 1, 0, 'C');
    $pdf->Cell(15, 5, 'Aus', 1, 0, 'C');
    $pdf->Cell(15, 5, 'Atr', 1, 1, 'C');
    $mm = 0;
    list($gr, $se) = explode("-", $reg1[15]);
    $p1 = '';
    $p2 = '';
    if ($gr > 8) {
        $pdf->Cell(12, 5, '    ', 1, 0, 'C');
        $pdf->Cell(14, 5, '½', 1, 0, 'C');
        $pdf->Cell(14, 5, '¹', 1, 0, 'C');
        $pdf->Cell(14, 5, '¹', 1, 0, 'C');
        $pdf->Cell(14, 5, '¹', 1, 0, 'C');
        $pdf->Cell(14, 5, '¹', 1, 0, 'C');
        $pdf->Cell(14, 5, '¹', 1, 0, 'C');
        $pdf->Cell(14, 5, '¼', 1, 0, 'C');
        if ($gr < 12) {
            //           $pdf->Cell(14,5,'¹',1,0,'C');
            $pdf->Cell(14, 5, '½', 1, 0, 'C');
        } else {
            $pdf->Cell(14, 5, '½', 1, 0, 'C');
        }
        if ($gr <> 11) {
            if ($gr == 12) {
                $pdf->Cell(14, 5, '½', 1, 0, 'C');
            } else {
                $pdf->Cell(14, 5, '½', 1, 0, 'C');
            }
            $pdf->Cell(14, 5, '', 1, 0, 'C');
            $pdf->Cell(14, 5, '', 1, 0, 'C');
            $pdf->Cell(14, 5, '', 1, 0, 'C');
        } else {
            $pdf->Cell(14, 5, '½', 1, 0, 'C');
            $pdf->Cell(14, 5, '½', 1, 0, 'C');
            $pdf->Cell(14, 5, '½', 1, 0, 'C');
            //           $pdf->Cell(14,5,'¼',1,0,'C');     
        }

        //        $pdf->Cell(14,5,'',1,0,'C');     
        //        $pdf->Cell(14,5,'',1,0,'C');     
        $pdf->Cell(14, 5, '', 1, 0, 'C');
        $pdf->Cell(14, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 0, 'C');
        $pdf->Cell(15, 5, '', 1, 1, 'C');
        $mm = 5;
        $p1 = '';
        $p2 = '';
    }

    $note = array(20, 20);
    $note = '';
    $not = array(20, 20);
    $not = '';
    $not[1][0] = 'Q1';
    $not[2][0] = 'Q2';
    $not[3][0] = 'Q3';
    $not[4][0] = 'Q4';
    $not[5][0] = 'SUMA';
    $not[6][0] = '70%';
    $not[7][0] = '30%';
    $not[8][0] = 'Sem.1';


    $ll = 0;
    $ll2 = 0;
    $mm2 = 0;
    for ($b1 = 1; $b1 <= 12; $b1++) {
        $q2 = "select * from padres where ss='$row1[0]' AND year='$row4[43]'AND curso='$row2[$b1]' ORDER BY curso";
        $tabla2 = mysql_query($q2, $dbh) or die("problema con query");
        $num_res = mysql_num_rows($tabla2);
        $reg2 = mysql_fetch_row($tabla2);
        $result2 = mysql_query($q2);
        if ($num_res == 0 and $b1 > 100) {
            $ll = $ll + 14;
            $pdf->SetXY(10, 49 + $mm);
            $pdf->SetFont('Times', '', 9);
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
            $pdf->Cell($ll - 2, 5, '', 0, 0, 'R');
            $pdf->Cell(14, 5, '', 1, 1, 'C');
        }
        while ($row = mysql_fetch_array($result2)) {
            $a = $a + 1;
            $nn1 = '';
            $nn2 = '';
            $nn3 = '';
            $nn4 = '';
            list($cc, $per) = explode("-", $row[7]);
            if (substr($row[7], 0, -3) == 'AAEC') {
                if ($row[19] >= $row4[62]) {
                    $tn = $tn + 2;
                    $tc = $tc + 0.5;
                }
                if ($row[19] >= $row4[63] and $row[19] < $row4[62]) {
                    $tn = $tn + 1.5;
                    $tc = $tc + 0.5;
                }
                if ($row[19] >= $row4[64] and $row[19] < $row4[63]) {
                    $tn = $tn + 1;
                    $tc = $tc + 0.5;
                }
                if ($row[19] >= $row4[65] and $row[19] < $row4[64]) {
                    $tn = $tn + 0.5;
                    $tc = $tc + 0.5;
                }
                if ($row[19] >= $row4[66] and $row[19] < $row4[65]) {
                    $tc = $tc + 0.5;
                }
            }
            if ($row[19] > 89) {
                $tn = $tn + (4 * $row[21]);
                $tc = $tc + $row[21];
            }
            if ($row[19] > 79 and $row[19] < 90) {
                $tn = $tn + (3 * $row[21]);
                $tc = $tc + $row[21];
            }
            if ($row[19] > 69 and $row[19] < 80) {
                $tn = $tn + (2 * $row[21]);
                $tc = $tc + $row[21];
            }
            if ($row[19] > 64 and $row[19] < 70) {
                $tn = $tn + (1 * $row[21]);
                $tc = $tc + $row[21];
            }
            if ($row[19] >  0 and $row[19] < 65) {
                $tc = $tc + $row[21];
            }
            $ppu = '';

            if ($cc == 'D') {
                if ($row[11] == '') {
                    $not[1][$b1] = 'A';
                }
                if ($row[12] == '') {
                    $not[2][$b1] = 'A';
                }
                if ($row[13] == '') {
                    $not[3][$b1] = 'A';
                }
                if ($row[14] == '') {
                    $not[4][$b1] = 'A';
                }
                if ($row[11] != '') {
                    $not[1][$b1] = $row[11];
                }
                if ($row[12] != '') {
                    $not[2][$b1] = $row[12];
                }
                if ($row[13] != '') {
                    $not[3][$b1] = $row[13];
                }
                if ($row[14] != '') {
                    $not[4][$b1] = $row[14];
                }
                if ($row[11] == '' or $row[11] == 'A') {
                    $ppu = $ppu + 4;
                }
                if ($row[11] == 'B') {
                    $ppu = $ppu + 3;
                }
                if ($row[11] == 'C') {
                    $ppu = $ppu + 2;
                }
                if ($row[11] == 'D') {
                    $ppu = $ppu + 1;
                }
                if ($row[12] == '' or $row[12] == 'A') {
                    $ppu = $ppu + 4;
                }
                if ($row[12] == 'B') {
                    $ppu = $ppu + 3;
                }
                if ($row[12] == 'C') {
                    $ppu = $ppu + 2;
                }
                if ($row[12] == 'D') {
                    $ppu = $ppu + 1;
                }
                if ($row[13] == '' or $row[13] == 'A') {
                    $ppu = $ppu + 4;
                }
                if ($row[13] == 'B') {
                    $ppu = $ppu + 3;
                }
                if ($row[13] == 'C') {
                    $ppu = $ppu + 2;
                }
                if ($row[13] == 'D') {
                    $ppu = $ppu + 1;
                }
                if ($row[14] == '' or $row[14] == 'A') {
                    $ppu = $ppu + 4;
                }
                if ($row[14] == 'B') {
                    $ppu = $ppu + 3;
                }
                if ($row[14] == 'C') {
                    $ppu = $ppu + 2;
                }
                if ($row[14] == 'D') {
                    $ppu = $ppu + 1;
                }
                $not[5][$b1] = $ppu;

                $nnt = '';
                if ($ppu >= 1) {
                    $nnt = 'F';
                }
                if ($ppu >  2) {
                    $nnt = 'D';
                }
                if ($ppu >  6) {
                    $nnt = 'C';
                }
                if ($ppu > 10) {
                    $nnt = 'B';
                }
                if ($ppu > 14) {
                    $nnt = 'A';
                }
                $not[8][$b1] = $nnt;

                $elec[1] = 'EMP12A';
                $elec[2] = 'EMP12B';
                $elec[3] = 'PM12A';
                $elec[4] = 'PM12B';
                $elec[5] = 'RIA12A';
                $elec[6] = 'RIA12B';
                $elec[7] = 'NATA2A';
                $elec[8] = 'MATA2B';
                $elec[9] = 'FIL11A';
                $elec[10] = 'FIL11B';
                $elec[11] = 'MUS11A';
                $elec[12] = 'MUS11B';

                //    $elec[3]='LIC12A';
                //    $elec[4]='LIC12B';
                //    $elec[5]='LEY12A';
                //    $elec[6]='LEY12B';
                //    $elec[7]='PER12A';
                //    $elec[8]='PER12B';
                //echo '77777777';
                for ($c = 1; $c <= 12; $c++) {
                    $data4a = "select * from padres where year = '$row4[43]' AND ss = '$row1[0]' AND curso = '" . $elec[$c] . "'";
                    $tabla4a = mysql_query($data4a, $con) or die("problema con query 22");
                    $resul_valor = mysql_num_rows($tabla4a);
                    $row44 = mysql_fetch_array($tabla4a);
                    if ($resul_valor > 0) {
                        $note[1][1] = $row44[11];
                        $note[2][1] = $row44[12];
                        $note[3][1] = $row44[13];
                        $note[4][1] = $row44[14];
                        $note[5][1] = $row44[117];
                        $note[6][1] = $row44[111];
                        $note[7][1] = $row44[123];
                        $note[8][1] = $row44[19];
                        $ntae2 = substr($elec[$c], 0, -3);
                        $ntae3 = $row44[21];

                        if ($row44[19] > 89) {
                            $tn = $tn + (4 * $row44[21]);
                            $tc = $tc + $row44[21];
                        }
                        if ($row44[19] > 79 and $row44[19] < 90) {
                            $tn = $tn + (3 * $row44[21]);
                            $tc = $tc + $row44[21];
                        }
                        if ($row44[19] > 69 and $row44[19] < 80) {
                            $tn = $tn + (2 * $row44[21]);
                            $tc = $tc + $row44[21];
                        }
                        if ($row44[19] > 64 and $row44[19] < 70) {
                            $tn = $tn + (1 * $row44[21]);
                            $tc = $tc + $row44[21];
                        }
                        if ($row44[19] >  0 and $row44[19] < 65) {
                            $tc = $tc + $row44[21];
                        }
                    }
                }
            }

            if ($cc != 'D') {

                $not[1][$b1] = $row[11];
                $not[2][$b1] = $row[12];
                $not[3][$b1] = $row[13];
                $not[4][$b1] = $row[14];
                $not[5][$b1] = $row[117];
                $not[6][$b1] = $row[111];
                $not[7][$b1] = $row[123];

                $nnt = '';
                $not[8][$b1] = $row[19] . $nnt;
            }

            if ($cc == 'D') {
                //$ya1
                $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$row4[29]' AND fecha <= '$row4[30]'";
                //      $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$ya1' AND fecha <= '$ya2'";
                $tabla22 = mysql_query($data8, $con) or die("problema con query 22");
                $au = 0;
                $ta = 0;
                while ($row8 = mysql_fetch_array($tabla22)) {
                    if ($row8[10] == '14') {
                        $ta = $ta + 1;
                    } else
               if ($row8[10] != '15') {
                        $au = $au + 1;
                    }
                }

                $ll2 = 0;

                $note[1][2] = $au;
                $note[1][3] = $ta;

                $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$row4[31]' AND fecha <= '$row4[32]'";
                //      $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$yb1' AND fecha <= '$yb2'";
                $tabla22 = mysql_query($data8, $con) or die("problema con query 22");
                $au = 0;
                $ta = 0;
                while ($row8 = mysql_fetch_array($tabla22)) {
                    if ($row8[10] == '14') {
                        $ta = $ta + 1;
                    } else
               if ($row8[10] != '15') {
                        $au = $au + 1;
                    }
                }

                $note[2][2] = $au;
                $note[2][3] = $ta;

                $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$row4[33]' AND fecha <= '$row4[34]'";
                $tabla22 = mysql_query($data8, $con) or die("problema con query 22");
                $au = 0;
                $ta = 0;
                while ($row8 = mysql_fetch_array($tabla22)) {
                    if ($row8[10] == '14') {
                        $ta = $ta + 1;
                    } else
               if ($row8[10] != '15') {
                        $au = $au + 1;
                    }
                }
                $note[3][2] = $au;
                $note[3][3] = $ta;

                $data8 = "select * from asispp where ss = '$row1[0]' AND fecha >= '$row4[35]' AND fecha <= '$row4[36]'";
                $tabla22 = mysql_query($data8, $con) or die("problema con query 22");
                $au = 0;
                $ta = 0;
                while ($row8 = mysql_fetch_array($tabla22)) {
                    if ($row8[10] == '14') {
                        $ta = $ta + 1;
                    } else
               if ($row8[10] != '15') {
                        $au = $au + 1;
                    }
                }

                $note[4][2] = $au;
                $note[4][3] = $ta;

                for ($m1 = 1; $m1 <= 8; $m1++) {
                    $pdf->SetFont('Times', '', 11);

                    $pdf->Cell(12, 5, $not[$m1][0], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][1], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][2], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][3], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][4], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][5], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][6], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][7], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][8], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][9], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][10], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][11], 1, 0, 'C');
                    $pdf->Cell(14, 5, $not[$m1][12], 1, 0, 'C');
                    $pdf->Cell(14, 5, $note[$m1][1], 1, 0, 'C');
                    if ($m1 < 5) {
                        $pdf->Cell(15, 5, '', 1, 0, 'C');
                        $pdf->Cell(15, 5, $note[$m1][2], 1, 0, 'C');
                        $pdf->Cell(15, 5, $note[$m1][3], 1, 1, 'C');
                    }
                    if ($m1 == 5) {
                        $pdf->Cell(45, 5, 'Enviar Ofic.: ___Si - ___No', 'LRT', 1, 'C');
                    }
                    if ($m1 == 6) {
                        $pdf->Cell(45, 5, '_____  Libera ', 'LR', 1, 'l');
                    }
                    if ($m1 == 7) {
                        $pdf->Cell(45, 5, '_____  No Libera', 'LR', 1, 'L');
                    }
                    if ($m1 == 8) {
                        if ($gr > 8 and $tc > 0) {
                            $pdf->Cell(45, 5, 'PROMEDIO SEM:   ' . number_format(ROUND($tn / $tc, 2), 2), 'LRB', 1, 'L');
                        } else {
                            $pdf->Cell(45, 5, '', 'LRB', 1, 'L');
                        }
                    }
                }
                //      $pdf->Cell(12,5,$not[0][2],1,0,'C');
                //      $pdf->Cell(14,5,$not[2][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[2][2],1,0,'C');
                //      $pdf->Cell(14,5,$not[2][3],1,1,'C');
                //      $pdf->Cell(12,5,$not[0][3],1,0,'C');
                //      $pdf->Cell(14,5,$not[3][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[3][2],1,1,'C');
                //      $pdf->Cell(12,5,$not[0][4],1,0,'C');
                //      $pdf->Cell(14,5,$not[4][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[4][2],1,1,'C');
                //      $pdf->Cell(12,5,$not[0][5],1,0,'C');
                //      $pdf->Cell(14,5,$not[5][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[5][2],1,1,'C');


                //      $pdf->Cell(12,5,$not[0][6],1,0,'C');
                //      $pdf->Cell(14,5,$not[6][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[6][2],1,1,'C');
                //      $pdf->Cell(12,5,$not[0][7],1,0,'C');
                //      $pdf->Cell(14,5,$not[7][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[7][2],1,1,'C');
                //      $pdf->Cell(12,5,$not[0][8],1,0,'C');
                //      $pdf->Cell(14,5,$not[8][1],1,0,'C');
                //      $pdf->Cell(14,5,$not[8][2],1,1,'C');

                $pdf->Cell(14, 35, '', 0, 1, 'C');
                //      $pdf->Cell(12+$ll+$mm2,5,'',0,0,'C');
                //      $pdf->Cell(42,20,'',1,1,'R');
                //      $pdf->SetXY(22+$ll+$mm2,69+$mm);
                //      $pdf->Cell(50,5,'Enviar Ofic.: ___Si - ___No',0,1,'L');
                //      $pdf->SetXY(22+$ll+$mm2,74+$mm);
                //      $pdf->Cell(50+$ll+$mm2,5,'_____  Libera ',0,1,'L');
                //      $pdf->SetXY(22+$ll+$mm2,79+$mm);
                //      $pdf->Cell(50+$ll+$mm2,5,'_____  No Libera ',0,1,'L');
                //      $pdf->SetXY(8+$ll+$mm2,84+$mm);
                //     IF ($gr>8){
                //        $pdf->Cell(56,5,'PROMEDIO SEM:   '.ROUND($tn / $tc,2),1,1,'R');}
                //      ELSE{
                //         $pdf->Cell(56,5,'  ',1,1,'L');}

            }
        }
    }

    $a = 1;

    if ($a > 0) {
        $pdf->AddPage('L');
    }
    $a = 0;
}

$pdf->Output();
