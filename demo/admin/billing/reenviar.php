<?php
require_once __DIR__ . '/../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Pasar balances", "Pass balances"],
    ['Pantalla para Pasar los Balances', 'Balance Transfer Screen'],
    ['Código', 'Code'],
    ['Persona que paga', 'Person who pays'],
    ['Cuenta', 'Account'],
    ['Estudiantes', 'Students'],
    ['Transferir', 'Transfer'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Por Selección', 'By Selection'],
    ['Descripci&oacute;n', 'Description'],
    ['Pasar Todo', 'Pass Everything'],
    ['Por Selección', 'By Selection'],
    ['Costos', 'Costs'],
    ['Opciones', 'Options'],
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
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Por Familia', 'Per Family'],
    ['Estu. Nuevo', 'New Student'],
    ['Todos', 'All'],
    ['Selecci&oacute;n', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Cambiar estado', 'Change Status'],
    ['Guardar cambios', 'Save Changes'],
    ['E', 'I'],
    ['Est&aacute;s seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$_month = isset($_POST['mes']) ? $_POST['mes'] : date("m");
$historys = DB::table('posteos_historial')->where([
    ['mensaje', 'Success'],
    ['mes', $_month],
])->get();

$createdPosts = [];
foreach ($historys as $history) {

    $post = DB::table('posteos')->where([
        ['id', $history->posteoId],
    ])->orderBy('id')->first();

    $name2 = $post->tipoDePago ?? '';
    $name = '';
    if ($post) {
        $name = $post->tipoDePago === 'tarjeta' ? $post->ccNombre : $post->achNombre;
        $stuId = DB::table('posteos_detalles')->select("estudianteId as mt")->whereRaw("posteoId=$post->id")
            ->orderBy('posteoId')->first();
    }

//    if ($stuId) {
//    if (!empty($name2)){
    if ($post) {
        $stu = DB::table('year')->where([
            ['mt', $stuId->mt],
        ])->orderBy('id')->first();
        $stu2 = $stu->nombre ?? '';
        if (!empty($stu)) {
            $stu2 = "$stu->nombre $stu->apellidos";
        }
        $createdPosts[] = [
            'id' => $post->id ?? '',
            'account' => $post->cuenta ?? '',
            'parentName' => $name,
            'student' => $stu2,
        ];
    }
}
$createdPosts = json_decode(json_encode($createdPosts));

$studentName = array_map(function ($element) {
    return $element->student;
}, $createdPosts);
array_multisort($studentName, SORT_ASC, $createdPosts);

$months = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reenviar recibos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="/datatable/css/jquery.dataTables.min.css" />
    <style>
        #tabla {
            border-radius: .4em;
        }

        #tabla tbody tr,
        input[type='checkbox'] {
            cursor: pointer;
        }

        #tabla_wrapper>div.dataTables_scroll>div.dataTables_scrollHead>div>table>thead>tr {
            background-color: #c3d9ff !important;
        }

        #tabla_wrapper {
            box-shadow: 1px 1px 11px #ccc;
        }

        .dataTables_empty {
            cursor: auto;
            pointer-events: none;
        }
    </style>
    <?php
$title = $lang->translation('Reenviar recibos');
Route::includeFile('/admin/includes/layouts/header.php');
?>
</head>

<body>
    <?php
Route::includeFile('/admin/includes/layouts/menu.php');
?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?=$lang->translation('Reenviar recibos')?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
        <div class="div">


    <div class="container mt-3">
        <form id="mesForm" method="POST">
            <select name="mes" id="mes">
                <?php foreach ($months as $index => $month): ?>
                    <option <?=$_month == ($index + 1) ? "selected" : ''?> value="<?=$index + 1?>"><?=$month?></option>
                <?php endforeach;?>

            </select>
        </form>
        <table id="tabla" class="display compact" width="100%">
            <thead>
                <tr style="text-align:left;">
                    <th style="width: 1px !important;"><input type="checkbox" id="imprimirTodos"></th>
                    <th><?=$lang->translation("Cuenta")?></th>
                    <th><?=$lang->translation("Estudiante")?></th>
                    <th><?=$lang->translation("Persona que paga")?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($createdPosts as $post):

?>
                    <tr data-id='<?=$post->id?>'>
                        <td style="text-align:left;">
                            <input type="checkbox" name="imprimir[]" value="<?=$post->id;?>">
                        </td>
                        <td><?=$post->account?></td>
                        <td><?=$post->student?></td>
                        <td><?=$post->parentName?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center">
                        <button id="pagar" class="btn btn-primary">Enviar recibos</button>
                        <div id="progressBar" class="progress invisible mt-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                    </td>
                </tr>

            </tfoot>
        </table>
    </div>
    </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../../../datatable/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            /* ---------------------------------- demo ---------------------------------- */

            const userName = 'CERT4549444000033'
            const password = '5B034VrA'
            const demo = true

            /* ----------------------------------- cbl ---------------------------------- */

            // const userName = 'ECOM4549555000561'
            // const password = 'h1MT6Eh24WDQ8LNJ'
            // const demo = false

            $("#pagar").click(function(e) {
                $("#pagar").prop('disabled', true)
                let postIds = [];
                $.each($("input[name='imprimir[]']:checked"), function(i, el) {
                    postIds.push(el.value)
                });
                if (postIds.length > 0) {
                    $("#progressBar").removeClass("invisible");
                    postIds.forEach((id, index) => {

                        $.ajax({
                            type: "POST",
                            url: "./pagos_aut_data.php",
                            data: {
                                searchPost: id
                            },
                            dataType: "json",
                            complete: function(response) {

                                progress = ((index + 1) / postIds.length) * 100
                                $("#progressBar .progress-bar")
                                    .prop("aria-valuenow", progress)
                                    .css("width", progress + "%");

                                const _post = response.responseJSON
                                console.log("Search: ", _post)
                                let data = {
                                    "username": userName,
                                    "password": password,
                                    "trxOper": "sale",
                                    "accountID": _post.cuenta,
                                    "customerEmail": _post.email,
                                    "address1": '',
                                    "address2": '',
                                    "city": '',
                                    "state": '',
                                    "trxID": _post.id,
                                    "refNumber": "",
                                    "trxDescription": `Pago manual de la cuenta ${_post.cuenta}`,
                                    "trxAmount": _post.total,
                                    "trxTipAmount": "",
                                    "trxTax1": "",
                                    "trxTax2": "",
                                    "filler1": "",
                                    "filler2": "",
                                    "filler3": ""
                                }
                                let method = '';
                                if (_post.tipoDePago === 'tarjeta') {
                                    data = {
                                        ...data,
                                        "customerName": _post.ccNombre,
                                        "cardNumber": _post.ccNumero,
                                        "expDate": _post.fechaExpiracion,
                                        "cvv": _post.cvv,
                                        "zipcode": _post.ccZip
                                    }
                                    method = 'ProcessCredit'
                                } else {
                                    data = {
                                        ...data,
                                        "customerName": _post.achNombre,
                                        "bankAccount": _post.achNumero,
                                        "routing": _post.numeroRuta,
                                        "accType": _post.tipoCuenta,
                                        "zipcode": _post.achZip
                                    }
                                    method = 'ProcessACH'
                                }

                                sendEmail(
                                    // `https://${demo ?'uat.' : ''}mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/${method}/`,
                                    data,
                                    _post.tipoDePago
                                )
                            }
                        });
                    });

                } else {
                    alert("Debe de seleccionar al menos uno");
                }
            })

            function sendEmail(_data, _paymentMethod) {
                _data.trxDescription = `Pago de ${_data.trxAmount}`
                let _emailData = {
                    ..._data
                }

                $("#pagar").prop('disabled', false)
                $("#progressBar").addClass("invisible");

                _emailData.sendEmail = true

                // _emailData.refNumber = response.refNumber
                // _emailData.authNumber = response.authNumber
                _emailData.paymentMethod = _paymentMethod
                // actualizar la cantidad en la tabla despues de que el pago sea completado
                console.log('Email data:', _emailData);
                $.post('pagos_aut_data.php', _emailData,
                    function(data) {
                        console.log('result: ', data)

                    }
                );

            }





            zeroRecords = `No hay datos`;
            info = `Hay _TOTAL_ en total`;
            infoEmpty = `No hay datos`;
            search = `Buscar:`;
            infoFiltered = `(filtrados de un total de _MAX_ )`;

            $('#tabla').DataTable({
                ordering: false,
                "scrollY": "400px",
                "paging": false,
                "language": {
                    "zeroRecords": zeroRecords,
                    "info": info,
                    "infoEmpty": infoEmpty,
                    "search": search,
                    "infoFiltered": infoFiltered
                }
            });
            //seleccionar todos
            $("#imprimirTodos").change(function(event) {
                if ($(this).prop("checked")) {
                    $("input[name='imprimir[]']").each(function(index, el) {
                        $(this).prop('checked', true);
                    });
                } else {
                    $("input[name='imprimir[]']").each(function(index, el) {
                        $(this).prop('checked', false);
                    });
                }
            });
            $("input[name='imprimir[]']").change(function(event) {
                $("input[name='imprimir[]']").each(function(index, el) {
                    if (!$(this).prop('checked')) {
                        $("#imprimirTodos").prop('checked', false);
                        return false;
                    } else {
                        $("#imprimirTodos").prop('checked', true);
                    }
                });
            });
            //marcar checkbox cuando se clicque una fila en la tabla
            $("#tabla tbody tr").click(function(event) {
                var index = $("#tabla tbody tr").index($(this));
                var check = $("input[name='imprimir[]']").eq(index);
                $(check).prop('checked', !$(check).prop('checked')).change();
                // check.click();
            });

            //END seleccionar todos

            $("#mes").change(function(e) {
                $("#mesForm").submit();
            })

        });
    </script>
</body>

</html>