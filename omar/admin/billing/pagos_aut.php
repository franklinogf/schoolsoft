<?php
require_once '../../control.php';
$school = mysql_fetch_object(mysql_query("SELECT * from colegio where usuario = 'administrador'"));
$posts = mysql_query("SELECT * FROM posteos WHERE formaDePago = 'manual' and year = '$school->year'");
$createdPosts = [];
while ($post = mysql_fetch_object($posts)) {
    $name = $post->tipoDePago === 'tarjeta' ? $post->ccNombre : $post->achNombre;
    $stuId = mysql_fetch_object(mysql_query("SELECT estudianteId as mt FROM posteos_detalles where posteoId = '$post->id' LIMIT 1"));
    $stu =  mysql_fetch_object(mysql_query("SELECT * FROM year WHERE mt = '$stuId->mt'"));
    if ($stuId) {
        $createdPosts[] = [
            'id' => $post->id,
            'account' => $post->cuenta,
            'parentName' => $name,
            'student' => "$stu->nombre $stu->apellidos",
        ];
    }
}
$createdPosts = json_decode(json_encode($createdPosts));

$studentName = array_map(function ($element) {
    return $element->student;
}, $createdPosts);
array_multisort($studentName, SORT_ASC, $createdPosts);
$month = date("m");

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos manuales</title>
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
</head>

<body>
    <div class="container mt-3">
        <h1 class="text-center">Pagos manuales</h1>
        <table id="tabla" class="display compact" width="100%">
            <thead>
                <tr style="text-align:left;">
                    <th style="width: 1px !important;"><input type="checkbox" id="imprimirTodos"></th>
                    <th>Cuenta</th>
                    <th>Estudiante</th>
                    <th>Persona que paga</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($createdPosts as $post) :
                    $msj = mysql_fetch_object(mysql_query("SELECT mensaje FROM posteos_historial WHERE mes = '$month' AND posteoId = '$post->id' ORDER BY id DESC limit 1"));

                ?>
                    <tr data-id='<?= $post->id ?>' <?= $msj ? ($msj->mensaje === 'Success' ? 'class="table-success"' : 'class="table-danger"') : '' ?>>
                        <td style="text-align:left;">
                            <?php if ($msj->mensaje !== 'Success') : ?>
                                <input type="checkbox" name="imprimir[]" value="<?= $post->id; ?>">
                            <?php endif ?>
                        </td>
                        <td><?= $post->account ?></td>
                        <td><?= $post->student ?></td>
                        <td><?= $post->parentName ?></td>
                        <td class="msj"><?= $msj->mensaje ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center">
                        <button id="pagar" class="btn btn-primary">Pagar</button>
                        <div id="progressBar" class="progress invisible mt-2">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                    </td>
                </tr>

            </tfoot>
        </table>


    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="/datatable/js/jquery.dataTables.min.js"></script>

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

                                makePayment(
                                    `https://${demo ?'uat.' : ''}mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/${method}/`,
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

            function makePayment(_url, _data, _paymentMethod) {
                _data.trxDescription = `Pago de ${_data.trxAmount}`
                let _emailData = {
                    ..._data
                }
                const dataJson = JSON.stringify(_data);
                $.ajax({
                    type: "POST",
                    url: _url,
                    data: dataJson,
                    crossDomain: true,
                    contentType: "application/json",
                    dataType: 'json',
                    complete: function(data) {
                        $("#pagar").prop('disabled', false)
                        $("#progressBar").addClass("invisible");
                        const response = data.responseJSON
                        console.log('response:', response)
                        _emailData.makePayment = response.rMsg
                        if (response.rCode === '00' || response.rCode === '0000') {
                            _emailData.refNumber = response.refNumber
                            _emailData.authNumber = response.authNumber
                            _emailData.paymentMethod = _paymentMethod
                            // actualizar la cantidad en la tabla despues de que el pago sea completado
                            $.post('pagos_aut_data.php', _emailData,
                                function(data) {
                                    console.log('result: ', data)

                                }
                            );
                            $("#tabla tbody").find(`tr[data-id=${_emailData.trxID}]`).removeClass("table-danger").addClass('table-success')
                            $("#tabla tbody").find(`tr[data-id=${_emailData.trxID}]`).find('input[type=checkbox]').remove()

                        } else {
                            $.post('pagos_aut_data.php', _emailData,
                                function(data) {
                                    console.log(data)
                                }
                            );

                            $("#tabla tbody").find(`tr[data-id=${_emailData.trxID}]`).addClass("table-danger").removeClass('table-success')

                        }
                        $("#tabla tbody").find(`tr[data-id=${_emailData.trxID}]`).find('.msj').text(response.rMsg)
                    }
                })

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

        });
    </script>
</body>

</html>