<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Carta de cobro", "Collection letter"],
    ['Carta', 'Letter'],
    ['Papel orientación', 'Paper orientation'],
    ['Mes', 'Month'],
    ['Todos', 'All'],
    ['Con Cantidad', 'With Quantity'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Informe pdf', 'PDF report'],
    ['Enviar por E-mail', 'Send by E-mail'],
    ['Deudores', 'Debtors'],
    ['Descripción', 'Description'],
    ['Hoja Legal', 'Legal Sheet'],
    ['Hoja carta', 'Letter Sheet'],
    ['Por Cuenta', 'By Account'],
    ['Por Grado', 'By Grade'],
    ['Selección de código', 'Code selection'],
    ['Opciones', 'Options'],
    ['En Orden', 'In order'],
    ['Fecha', 'Date'],
    ['Cuentas', 'Accounts'],
    ['Estudiantes', 'Students'],
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
    ['Primer aviso de cobro', 'First collection notice'],
    ['Segundo aviso de cobro', 'Second collection notice'],
    ['Carta de suspensión', 'Suspension Letter'],
    ['Carta general', 'General Letter'],
    ['Carta de cobro general A', 'General Collection Letter A'],
    ['', ''],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Selección de Meses', 'Month Selection'],
    ['Borrar todos los Cargos', 'Eliminate all costs'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');

//session_start();
//$id=$_SESSION['id1'];
//$_SESSION['usua1']= 'administrador';
///usua = $_SESSION['usua1'];
//require_once '../../control.php';
//$result = mysql_query("SELECT * FROM colegio WHERE usuario = '$usua'",$con);
//$reg=mysql_fetch_object($result);
//$year = $reg->year2;
//$result = mysql_query("SELECT * FROM presupuesto WHERE year='$year'",$con);
//$cartas = mysql_query("SELECT * FROM T_historial_cartas WHERE year='$year'",$con);
?>
<!DOCTYPE html>
<html>

<head>
    <?php
    $title = $lang->translation('Carta de cobro');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Cartas</title>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Carta de cobro') ?></h1>
    <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
        <div class="div">
            <form method="POST">
                <table align="center" cellspacing="2" cellpadding="0" border="0">
                    <thead>
                        <tr>
                            <th colspan="2"><?= $lang->translation('Opciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="tipo">
                                    <option value="pdf"><?= $lang->translation('Informe pdf') ?></option>
                                    <option value="email"><?= $lang->translation('Enviar por E-mail') ?></option>
                                </select>
                            </td>
                            <td>
                                <select name="opcion">
                                    <option value="todos"><?= $lang->translation('Todos') ?></option>
                                    <option value="deudores"><?= $lang->translation('Deudores') ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><?= $lang->translation('Mes') ?></th>
                            <th><?= $lang->translation('Carta') ?></th>
                        </tr>
                        <tr>
                            <td>
                                <select name="mes">
                                    <option value="1"><?= $lang->translation('Enero') ?></option>
                                    <option value="2"><?= $lang->translation('Febrero') ?></option>
                                    <option value="3"><?= $lang->translation('Marzo') ?></option>
                                    <option value="4"><?= $lang->translation('Abril') ?></option>
                                    <option value="5"><?= $lang->translation('Mayo') ?></option>
                                    <option value="6"><?= $lang->translation('Junio') ?></option>
                                    <option value="7"><?= $lang->translation('Julio') ?></option>
                                    <option value="8"><?= $lang->translation('Agosto') ?></option>
                                    <option value="9"><?= $lang->translation('Septiembre') ?></option>
                                    <option value="10"><?= $lang->translation('Octubre') ?></option>
                                    <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                    <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                </select>
                            </td>
                            <td>
                                <select id="carta" name="carta">
                                    <option value="1"><?= $lang->translation('Primer aviso de cobro') ?></option>
                                    <option value="2"><?= $lang->translation('Segundo aviso de cobro') ?></option>
                                    <option value="3"><?= $lang->translation('Carta de suspensión') ?></option>
                                    <option value="4"><?= $lang->translation('Carta general') ?></option>
                                    <option value="5"><?= $lang->translation('Carta de cobro general A') ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <strong>
                                    <center><br><br>
                                        <input class="btn btn-primary form-control" id='Aceptar' name="buscar" type="submit" value="<?= $lang->translation('Procesar') ?>" style="width: 129px;" />
                                    </center>
                                </strong><br />
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </form>

        </div>
    </div>
    <script type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript">
        $(function() {
            function CARTAS(data) {
                var $data = $.parseJSON(data);
                var $tbody = '';
                if ($data.length > 0) {
                    $.each($data, function(index, val) {
                        $tbody += '<tr id="' + val.id + '" class="color"><td align="left" class="row">' + val.titulo + '</td><td><button class="myButton delete">Borrar</button></td></tr>';
                    });
                    $("#cartas").show();
                } else {
                    $("#cartas").hide();

                }
                $("#cartas > tbody").html($tbody);
            }
            $.post('acciones.php', {
                'year': '<?php echo $year; ?>'
            }, function(data) {
                CARTAS(data);
            });
            $("#Aceptar").click(function() {
                setTimeout(function() {
                    $('.mensaje').val('');
                }, 500);
            });
            $("form").submit(function(event) {
                $(this).prop({
                    'action': 'carta' + $("#carta").val() + '.php',
                    'target': 'carta' + $("#carta").val()
                });
                if ($('#guardarM').prop('checked')) {
                    if ($('#guardarM').prop('name') == 'guardar') {
                        $.post('acciones.php', {
                            'guardar': 'si',
                            'titulo': $('#mensajeTitulo').val(),
                            'mensaje': $('#mensaje').val(),
                            'saludo': $('#mensajeSaludo').val(),
                            'despedida': $('#mensajeDespedida').val(),
                            'pt': $('#pt').val(),
                            'pm': $('#pm').val(),
                            'ps': $('#ps').val(),
                            'pd': $('#pd').val(),
                            'year': '<?php echo $year; ?>'
                        }, function(data) {
                            CARTAS(data);
                        });
                    } else if ($('#guardarM').prop('name') == 'actualizar') {
                        $.post('acciones.php', {
                            'actualizar': $('#id').val(),
                            'titulo': $('#mensajeTitulo').val(),
                            'mensaje': $('#mensaje').val(),
                            'saludo': $('#mensajeSaludo').val(),
                            'despedida': $('#mensajeDespedida').val(),
                            'pt': $('#pt').val(),
                            'pm': $('#pm').val(),
                            'ps': $('#ps').val(),
                            'pd': $('#pd').val(),
                            'year': '<?php echo $year; ?>'
                        }, function(data) {
                            CARTAS(data);
                        });
                    }
                }
                $('#guardarM').prop('name', 'guardar');
                $('#g').text('Guardar mensaje');
                $('#id').remove();
                $("#guardarM").prop('checked', false);
                $('.delete').removeClass('disabledBtn');

            });
            $(document).on('click', '.row', function(event) {
                event.preventDefault();
                var $id = $(this).parent('tr').prop('id');
                var $btn = $(this).next('td').find('.delete');
                $('.delete').removeClass('disabledBtn');
                $.post('acciones.php', {
                    'buscar': $id
                }, function(data) {
                    var $carta = jQuery.parseJSON(data);
                    $('#mensajeTitulo').val($carta.titulo);
                    $('#mensaje').val($carta.mensaje);
                    $('#mensajeSaludo').val($carta.saludo);
                    $('#mensajeDespedida').val($carta.despedida);
                    $('#pt').val($carta.p_t);
                    $('#pm').val($carta.p_m);
                    $('#ps').val($carta.p_s);
                    $('#pd').val($carta.p_d);
                    $('#guardarM').prop('name', 'actualizar');
                    $('#g').text('Actualizar mensaje');
                    $("#carta").val('4');
                    $(".mensaje").prop('disabled', false);
                    $('#presupuesto').prop('disabled', true);
                    $("#id").remove();
                    $('<input id="id" type="hidden" value="' + $carta.id + '">').insertAfter('#guardarM');
                    $btn.addClass('disabledBtn');
                });
            });
            $(document).on('click', '.delete', function(event) {
                event.preventDefault();
                var $id = $(this).parents('tr').prop('id');
                if (confirm('ESTA SEGURO QUE DESEA BORRARLO?')) {
                    $.post('acciones.php', {
                        'borrar': $id
                    }, function(data) {
                        CARTAS(data);
                    });
                }
            });
            $('#carta').change(function(event) {
                if ($(this).val() == 3) {
                    $('#presupuesto').prop('disabled', false);
                    $('.mensaje').prop('disabled', true);
                } else if ($(this).val() == 4) {
                    $('.mensaje').prop('disabled', false);
                    $('#presupuesto').prop('disabled', true);

                } else {
                    $('#presupuesto,.mensaje').prop('disabled', true);
                }
            });
        });
    </script>
</body>

</html>