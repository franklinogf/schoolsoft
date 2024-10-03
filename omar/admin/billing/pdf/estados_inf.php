<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Mail;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['NOMBRE', 'NAME'],
    ['CUENTA', 'ACCOUNT'],
    ['PAGOS', 'PAYS'],
    ['FECHA P.', 'PAY DAY'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['GRADO', 'GRADE'],
    ['Matri/Junio', 'Regis/June'],
    ['ESTUDIANTES', 'STUDENTS'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['DEUDA', 'DEBT'],
    ['PAGO', 'PAY'],
    ['BALANCE', 'BALANCE SHEET'],
    ['Estado de cuenta', 'Statement'],
    ['BALANCE DEL ESTADO DE CUENTA:', 'TOTAL BALANCE SHEET:'],
    ['PAGO REQUERIDO:', 'PAYMENT REQUIRED:'],
    ['Mensaje', 'Message'],
    ['No has seleccionado el mes del estado. Por favor vuelve e inténtalo de nuevo.', 'You have not selected the state month. Please come back and try again.'],
]);

$school = new School(Session::id());
$year = $school->info('year2');
$id = '';
$usua = '';
$est1 = [];
$est2 = [];
$est3 = [];
if ($_POST['envia'] == 'Si') {
    DB::table('estado')->where('fecha', date('m-d-Y'))->delete();
}

date_default_timezone_set('America/Puerto_Rico');
if ($_POST['mes'] == 0) {
    echo "<br><br><center>" . $lang->translation('No has seleccionado el mes del estado. Por favor vuelve e inténtalo de nuevo.') . "</center>";
    exit;
}

class nPDF extends PDF
{
    //Cabecera de pgina
    function Header()
    {
        global $lang;
        //Logo
        $this->Ln(15);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 4, $lang->translation('ESTADO DE CUENTAS'), 0, 0, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->Ln(5);
        $this->Cell(80);
        $this->Cell(30, 5, date('m-d-Y'), 0, 0, 'C');
        $this->Ln(10);
        $this->Cell(80);
        if ($_POST['mes'] == '01') {
            $mes = $lang->translation('Enero');
        }
        if ($_POST['mes'] == '02') {
            $mes = $lang->translation('Febrero');
        }
        if ($_POST['mes'] == '03') {
            $mes = $lang->translation('Marzo');
        }
        if ($_POST['mes'] == '04') {
            $mes = $lang->translation('Abril');
        }
        if ($_POST['mes'] == '05') {
            $mes = $lang->translation('Mayo');
        }
        if ($_POST['mes'] == '06') {
            $mes = $lang->translation('Junio');
        }
        if ($_POST['mes'] == '07') {
            $mes = $lang->translation('Julio');
        }
        if ($_POST['mes'] == '08') {
            $mes = $lang->translation('Agosto');
        }
        if ($_POST['mes'] == '09') {
            $mes = $lang->translation('Septiembre');
        }
        if ($_POST['mes'] == '10') {
            $mes = $lang->translation('Octubre');
        }
        if ($_POST['mes'] == '11') {
            $mes = $lang->translation('Noviembre');
        }
        if ($_POST['mes'] == '12') {
            $mes = $lang->translation('Diciembre');
        }
        $this->Cell(30, 5, $mes, 0, 0, 'C');

        $this->Ln(15);
        $this->Cell(80, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(90, 5, $lang->translation('ESTUDIANTES'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('GRADO'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 12);
    }

    function WriteHTML($html)
    {
        //Interprete de HTML
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                //            if($this->HREF)
                //                $this->PutLink($this->HREF,$e);
                //            else
                $this->Write(5, $e);
            } else {
                //Etiqueta
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extraer atributos
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        //Etiqueta de apertura
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, true);
        if ($tag == 'A')
            $this->HREF = $attr['HREF'];
        if ($tag == 'BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        //Etiqueta de cierre
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        //Modificar estilo y escoger la fuente correspondiente
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0)
                $style .= $s;
        }
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Escribir un hiper-enlace
        //        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        //        $this->SetTextColor(0);
    }

    //Pie de pgina
    function Footer()
    {
        if ($_POST['idi'] == 'Ingles') {
            $this->SetY(-50);
            $this->Cell(50, 22, '', 0, 0, 'C');
            $this->Cell(140, 22, '', 0, 0, 'C');
        } else {
            $this->SetY(-50);
            $this->Cell(50, 22, '', 0, 0, 'C');
            $this->Cell(140, 22, '', 0, 0, 'C');
        }
    }
    function generateTable($no, $at, $id, $usua)
    {
        global $year;
        global $lang;
        global $est1;
        global $est2;
        global $est3;

        $gr1 = '';
        $gr2 = '';
        $gr3 = '';
        $gr4 = '';
        $no1 = '';
        $no2 = '';
        $no3 = '';
        $no4 = '';
        $row11 = DB::table('codigos')->whereRaw("idc='2' and codigo='" . $_POST['num2'] . "'")->first();
        for ($x = 0; $x <= 20; $x++) {
            $est1[$x] = '';
            $est2[$x] = '';
            $est3[$x] = '';
            $est4[$x] = '';
        }

        if ($_POST['idi'] == 'Ingles') {
            $text1 = $row11->tema2 ?? '';
        } else {
            $text1 = $row11->tema ?? '';
        }
        $result = DB::table('year')->select("DISTINCT id")
            ->whereRaw("year='$year' and id='$no' and activo=''")->orderBy('apellidos, nombre')->get();
        foreach ($result as $row1) {
            $reg22 = DB::table('madre')->whereRaw("id='$no'")->first();
            $result2 = DB::table('pagos')->select("DISTINCT codigo, desc1")
                ->whereRaw("year='$year'")->orderBy('codigo')->get();

            $this->SetFont('Times', '', 12);
            if ($reg22->qpaga == "" || $reg22->qpaga == "M" || empty($reg22->qpaga)) {
                $this->Cell(60, 5, $reg22->encargado, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->dir_e1, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->dir_e2, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->pue_e . ', ' . $reg22->esta_e . ' ' . $reg22->zip_e, 0, 1, 'L');
            } else {
                $this->Cell(60, 5, $reg22->madre, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->dir1, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->dir3, 0, 1, 'L');
                $this->Cell(60, 5, $reg22->pueblo1 . ', ' . $reg22->est1 . ' ' . $reg22->zip1, 0, 1, 'L');
            }
            $this->Cell(80, 5, $lang->translation('CUENTA') . ' # ' . $row1->id, 0, 1, 'L');

            $result22 = DB::table('year')
                ->whereRaw("year='$year' and id='$no' and activo=''")->orderBy('apellidos, nombre')->get();

            $totdeu = 0;
            $atra = 0;
            $est = 0;
            foreach ($result22 as $row23) {
                $est = $est + 1;
                if ($est == 1) {
                    $no1 = $row23->nombre . ' ' . $row23->apellidos;
                    $gr1 = $row23->grado;
                }
                if ($est == 2) {
                    $no2 = $row23->nombre . ' ' . $row23->apellidos;
                    $gr2 = $row23->grado;
                }
                if ($est == 3) {
                    $no3 = $row23->nombre . ' ' . $row23->apellidos;
                    $gr3 = $row23->grado;
                }
                if ($est == 4) {
                    $no4 = $row23->nombre . ' ' . $row23->apellidos;
                    $gr4 = $row23->grado;
                }
                if ($_POST['conest'] == 1) {
                    $this->Cell(80, 5, '', 0, 0, 'L');
                    $this->Cell(90, 5, $row23->nombre . ' ' . $row23->apellidos, 0, 0, 'L');
                    $this->Cell(20, 5, $row23->grado, 0, 1, 'c');
                }
            }
            $this->Cell(100, 5, $lang->translation('DESCRIPCION'), 1, 0, 'C', true);
            $this->Cell(26, 5, $lang->translation('DEUDA'), 1, 0, 'C', true);
            $this->Cell(26, 5, $lang->translation('PAGO'), 1, 0, 'C', true);
            $this->Cell(38, 5, $lang->translation('BALANCE'), 1, 1, 'C', true);
            $i = 0;
            list($yy2, $mm1, $dd1) = explode("-", date('Y-m-d'));
            list($ya, $yb) = explode("-", $year);
            if ($_POST['mes'] < '06') {
                $yy1 = '20' . $yb;
            } else {
                $yy1 = '20' . $ya;
            }

            $fec = $yy2 . '-' . $_POST['mes'] . '-' . $dd1;
            foreach ($result2 as $row) {
                $result3 = DB::table('pagos')
                    ->whereRaw("id='" . $no . "' AND year='" . $year . "' and codigo='" . $row->codigo . "' and desc1='" . $row->desc1 . "' and fecha_d <= '" . $fec . "'")->orderBy('codigo')->get();
                $deu = 0;
                $pag = 0;
                $gg = 1;
                foreach ($result3 as $row7) {
                    $deu = $deu + $row7->deuda;
                    $pag = $pag + $row7->pago;
                    if ($row7->fecha_d <= $fec) {
                        $atra = $atra + ($row7->deuda - $row7->pago);
                    }
                }
                if ($deu <> 0 or $pag <> 0) {
                    $est1[$i] = $row->desc1;
                    $est2[$i] = $deu;
                    $est3[$i] = $pag;
                    $est4[$i] = $deu - $pag;
                    $this->Cell(100, 5, $row->desc1, 0, 0, 'L');
                    $this->Cell(26, 5, number_format($deu, 2), 0, 0, 'R');
                    $this->Cell(26, 5, number_format($pag, 2), 0, 0, 'R');
                    $this->Cell(38, 5, number_format($deu - $pag, 2), 0, 1, 'R');
                    $totdeu = $totdeu + ($deu - $pag);
                    $i = $i + 1;
                }
            }

            $this->Cell(152, 5, $lang->translation('BALANCE DEL ESTADO DE CUENTA:') . ' ', 1, 0, 'R', true);
            $this->Cell(38, 5, number_format($totdeu, 2), 1, 1, 'R', true);
            $this->Cell(160, 5, '', 0, 1, 'R');
            $this->Cell(152, 5, $lang->translation('PAGO REQUERIDO:') . ' ', 0, 0, 'R');
            $this->Cell(38, 5, number_format($atra, 2), 0, 1, 'R');
            $this->Cell(160, 10, '', 0, 1, 'R');

            if ($_POST['num2'] > 0) {
                $this->Cell(189, 5, $lang->translation('Mensaje'), 1, 1, 'C');
                $this->SetLeftMargin(11);
                $this->WriteHTML($text1);
                $this->SetLeftMargin(10);
            }

            if ($_POST['envia'] == 'Si') {
                DB::table('estado')->insert([
                    'cta' => $no,
                    'year' => $year,
                    'gra1' => $gr1,
                    'gra2' => $gr2,
                    'gra3' => $gr3,
                    'gra4' => $gr4,
                    'nom1' => $no1,
                    'nom2' => $no2,
                    'nom3' => $no3,
                    'nom4' => $no4,
                    'mes' => $_POST['mes'],
                    'fecha' => date('m-d-Y'),
                ]);

                $thisCourse2 = DB::table("estado")->where([
                    ['cta', $no],
                    ['year', $year],
                    ['mes', $_POST['mes']],
                ])->update([
                    'men1' => $text1,
                    'bala' => $totdeu,
                    'req' => $atra,
                ]);

                $n = 0;
                for ($x = 0; $x <= $i; $x++) {
                    $n = $n + 1;
                    $n1 = 'des' . $n;
                    $n2 = 'deu' . $n;
                    $n3 = 'pag' . $n;
                    $thisCourse2 = DB::table("estado")->where([
                        ['cta', $no],
                        ['year', $year],
                        ['mes', $_POST['mes']],
                    ])->update([
                        $n1 => $est1[$x],
                        $n2 => $est2[$x],
                        $n3 => $est3[$x],
                    ]);
                }
            }
        }
    }
}

if ($_POST['enviae'] == 'No') {
    $pdf = new nPDF("P");
    $pdf->SetTitle($lang->translation('ESTADO DE CUENTAS') . ' ' . $year);
    $pdf->Fill();
    $pdf->AliasNbPages();
    $pdf->SetFont('Times', '', 11);
}


$n1 = $_POST['nombre'];
$usua = '';
$id = '';
if ($n1 == 1) {
    $resulta = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('apellidos')->get();
} else {
    $resulta = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and id='$n1' and activo !='B'")->orderBy('apellidos')->get();
}
$ctas = $_POST['ctas'];
if (!empty($ctas)) {
    $resulta = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and id='$ctas' and activo !='B'")->orderBy('apellidos')->get();
}

$atra = 0;
list($yy1, $mm1, $dd1) = explode("-", date('Y-m-d'));
$fec = $yy1 . '-' . $_POST['mes'] . '-' . $dd1;
foreach ($resulta as $rowa1) {
    $id7 = $rowa1->id;
    $resultb = DB::table('pagos')
        ->whereRaw("year='$year' and id='$rowa1->id' and baja=''")->orderBy('codigo')->get();
    $deu = 0;
    $pag = 0;
    $atra = 0;
    $id = '';
    $usua = '';
    foreach ($resultb as $rowa2) {
        $deu = $deu + $rowa2->deuda;
        $pag = $pag + $rowa2->pago;
        if ($rowa2->fecha_d <= $fec) {
            $atra = $atra + ($rowa2->deuda - $rowa2->pago);
        }
    }
    if ($deu > 0 or $atra > 0) {
        if ($_POST['enviae'] == 'Si') {
            $pdf = new nPDF("P");
            $pdf->AliasNbPages();
            $pdf->Fill();
            $pdf->AddPage();
            $pdf->SetFont('Times', '', 11);
        }
    }
    if ($deu - $pag > 0 and $_POST['deuda'] == 2) {
        if ($_POST['enviae'] == 'No') {
            $pdf->AddPage();
        }
        $pdf->generateTable($id7, $atra, $id, $usua);
    }
    if ($deu > 0 and $_POST['deuda'] == 1) {
        if ($_POST['enviae'] == 'No') {
            $pdf->AddPage();
        }
        $pdf->generateTable($id7, $atra, $id, $usua);
    }
    if ($atra > 0 and $_POST['deuda'] == 3) {
        if ($_POST['enviae'] == 'No') {
            $pdf->AddPage();
        }
        $pdf->generateTable($id7, $atra, $id, $usua);
    }
    $atra = 0;

    //******************************************

    $row4 = DB::table('madre')->whereRaw("id='$rowa1->id'")->orderBy('id')->first();

    if ($deu > 0 and $_POST['enviae'] == 'Si' or $atra > 0 and $_POST['enviae'] == 'Si') {
        $file_name = "Statement.pdf";
        $dir = '../../';
        $file = $pdf->Output("", "S");

        //*********************************************
        $mail = new Mail();
        $title = $lang->translation('Estado de cuenta');
        $subject = $lang->translation('Estado de cuenta');
        $message = '';
        $mail->Subject = $subject;
        $emailsSent = 0;
        $emailsError = 0;
        $error = null;

        $mail->addStringAttachment($file, $file_name);

        $parents = DB::table('madre')->where('id', $rowa1->id)->first();
        $emails = [
            ['correo' => $parents->email_p, 'nombre' => $parents->padre],
            ['correo' => $parents->email_m, 'nombre' => $parents->madre]
        ];
        foreach ($emails as $email) {
            if ($email['correo'] !== '') {
                $mail->addAddress($email['correo'], $email['nombre']);
            }
        }
        //        $mail->addAddress("alf_med@hotmail.com", 'Alfredo Medina');

        $mail->isHTML(true);
        $mail->Body = "
            <!DOCTYPE html>
            <html lang='" . __LANG . "'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>{$title}</title>
            </head>
            <body>
            <center><h2>{$title}</h2></center>
            <br>
            <p>{$message}</p>  
            </body>
            </html>
            ";

        $mail->send();
        $mail->ClearAddresses();
        //        exit;
    }
}

$pdf->Output();
