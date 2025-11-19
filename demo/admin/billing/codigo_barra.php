<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Código de barra", "Barcode"],
    ['Hasta', 'Until'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección', 'Select'],
    ['SS a Código Barra', 'SS to Barcode'],
    ['4d+SS+cuenta a Código Barra.', '4d+SS+account to Barcode.'],
    ['Núm. Genérico a Código Barra.', 'Generic Number to Barcode.'],
    ['Selección para crear Códigos de Barras', 'Selection to create Barcodes'],
    ['Solo los que no tienen Código de barra.', 'Only those without a barcode.'],
    ['Borrar todos y crear código de barra.', 'Delete all and create barcode.'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title><?= $lang->translation('Código de barra') ?></title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: x-large;
        }

        .style2 {
            background-color: #CCCCCC;
        }

        .style3 {
            background-color: #FFFFCC;
        }

        .style4 {
            text-align: center;
        }
    </style>
</head>
<?php
$title = $lang->translation('Código de barra');
Route::includeFile('/admin/includes/layouts/header.php');
?>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Código de barra') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

            <form method="post" action="pdf/pdf_codigo_barra.php" target="_blank">
                <div class="style4">
                    <table align="center" cellpadding="2" cellspacing="0" style="width: 37%">
                        <tr>
                            <td><strong><?= $lang->translation('Selección para crear Códigos de Barras') ?></strong></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="codi" style="width: 353px" required>
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <option value="SS"><?= $lang->translation("SS a Código Barra.") ?></option>
                                    <option value="4S"><?= $lang->translation("4d+SS+cuenta a Código Barra.") ?></option>
                                    <option value="GE"><?= $lang->translation("Núm. Genérico a Código Barra.") ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong></strong></td>
                        </tr>
                        <tr>
                            <td><strong></strong></td>
                        </tr>
                        <tr>
                            <td><select name="crear" required>
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <option value="1"><?= $lang->translation("Solo los que no tienen Código de barra.") ?></option>
                                    <option value="2"><?= $lang->translation("Borrar todos y crear código de barra.") ?></option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <input name="Submit1" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>" />
                </div>
            </form>

        </div>
    </div>

    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>