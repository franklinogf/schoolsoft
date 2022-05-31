<?
// exit;

session_start();
$id = $_SESSION['id1'];
$usua = $_SESSION['usua1'];
// 861 aymar
if ($usua == "") {
   exit;
}
$host = $_SERVER["HTTP_HOST"];
$ur0 = $_SERVER["REQUEST_URI"];
// 861 aymar
//echo $ur0.'<br>';
list($ur1, $ur2, $ur3) = explode("/", $ur0);
$dirhost1 = $host . '/' . $ur2 . '/' . $ur3 . '/ipn_error.php';
$dirhost2 = $host . '/' . $ur2 . '/' . $ur3 . '/ipn_success.php';

include('../control.php');
$data6 = "select * from colegio where usuario = 'administrador'";
$tabla6 = mysql_query($data6, $con) or die("problema con query0");
$cole = mysql_fetch_object($tabla6);

list($y1, $y2) = explode("-", $cole->year);
$y3 = $y2 + 1;
$y4 = $y2 . '-' . $y3;

if (isset($_POST['grabar'])) {
   include('../control.php');
   for ($a = 1; $a <= $_POST[nu]; $a++) {

      $consult1 = "select * from colegio where usuario = 'administrador'";
      $resultad1 = mysql_query($consult1);
      $row2 = mysql_fetch_array($resultad1);

      $ss2 = 'ss(' . $a . ')';
      $rema2 = 'rema(' . $a . ')';
      $ss = $_POST[$ss2];
      $rema = $_POST[$rema2];
      if ($rema == 'No') {
         $q = "UPDATE year set her='',ent='', rema='$rema' where ss='$ss' AND year='$row2[43]'";
      } else {
         $q = "UPDATE year set rema='$rema' where ss='$ss' AND year='$row2[43]'";
      }
      mysql_query($q, $db) or die("problema con query 1");
   }
}
?>



<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Re-Matríula</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
   <link rel="stylesheet" href="re_matricula.css">
   <script language="Javascript" type="text/javascript">
      function formsum() {

         //var form = document.getElementById("algunNombre2");

         form.submit();
      }
   </script>

   <style type="text/css">
      .style1 {
         text-align: center;
         background-color: #CCCCCC;
      }

      .style2 {
         background-color: #FFFFCC;
      }

      .style3 {
         text-align: center;
         font-size: large;
         background-color: #CCCCCC;
      }

      .style4 {
         text-align: center;
         background-color: #FFFFCC;
      }

      .style5 {
         font-size: large;
         text-align: center;
      }

      .style6 {
         text-align: center;
      }
   </style>
   <style type="text/css">
      .style11 {
         text-align: center;
      }
   </style>

   <link href="../../jv/botones.css" rel="stylesheet" type="text/css">
</head>

<body>
   <form id="algunNombre2" name="algunNombre2" method="post">
      <p class="style5">Formulario<strong> de Re-Matricula de estudiantes</strong></p>
      <table align="center" style="width: 76%">
         <tr>
            <td class="style3" style="width: 495px"><strong>Nombre del estudiante</strong></td>
            <td class="style1" style="width: 49px"><strong>Grado</strong></td>
            <td class="style1" style="width: 146px"><strong>Re-Matricula</strong></td>
         </tr>
         <?
         include('../control.php');

         if (!$con) {
            die('Could not connect: ' . mysql_error());
         }

         $consult1 = "SELECT * from colegio where usuario = 'administrador'";
         $resultad1 = mysql_query($consult1);
         $row2 = mysql_fetch_array($resultad1);

         $consulta = "SELECT * from year where id = '$id' AND year='$row2[43]' AND grado NOT LIKE '12%'";
         $resultado = mysql_query($consulta);
         $resultado2 = mysql_query($consulta);
         $resultado3 = mysql_query($consulta);
         $resultado4 = mysql_query($consulta);
         $resultado5 = mysql_query($consulta);

         $a = 0;
         $a2 = 0;
         $a3 = 0;
         while ($row = mysql_fetch_array($resultado)) {
            $a = $a + 1;
         ?>
            <tr>
               <td class="style2" style="width: 495px">
                  <? echo $row["nombre"] . ' ' . $row["apellidos"]; ?>
               </td>
               <td class="style4" style="width: 49px">
                  <? echo $row["grado"]; ?>
               </td>
               <td class="style4" style="width: 146px">
                  <? echo "<select name=rema($a) style='width: 47px'>"; ?>
                  <?
                  $ma = '';
                  if ($row["ent"] == "1") {
                     $a3 = 1;
                  }
                  if ($row["matri"] == "S") {
                     $ma = 'disabled="disabled"';
                  }
                  if ($row["rema"] == "Si") {
                     $a2 = 1; ?>
                     <option value="Si">Si</option>
                     <option <? echo $ma; ?>>No</option>
                  <? } else { ?>
                     <option value="No" <? echo $ma; ?> selected="selected">No</option>
                     <option>Si</option>
                  <? } ?>
                  </select>
               </td>
            </tr>
            <? echo "<input name=ss($a) type=hidden value=$row[0]>"; ?>
            <? echo "<input name=nu type='hidden' value='$a'>"; ?>

         <? } ?>
         <tr>
            <td class="style2" style="width: 495px">&nbsp;</td>
            <td class="style2" style="width: 49px">&nbsp;</td>
            <td class="style2" style="width: 146px">&nbsp;</td>
         </tr>
      </table>
      <p class="style6">
         <strong>
            <input name="grabar" type="submit" value="Enviar Solicitud" class="myButton" style="width: 200px; height: 25px"></strong>
   </form>
   </p>

   <?
   if ($a2 == 1) {

      $q = 0;
      while ($row = mysql_fetch_array($resultado2)) {
         if ($row['rema'] == 'Si' and $row['ent'] == '') {
            $q = $q + 1;
            echo '<div class="style1">';
            echo '<strong>LLene el formulario<br /></strong></div>';
            echo '<form name="padres" method="POST" action="formulario.php">';
            echo "<input name=ss type='hidden' value='" . $row[0] . "'>";
            echo '<p class="style11"><input name="grabar2" type="submit" value="Form: ' . $row[4] . ' ' . $row[3] . '" class="myButton" style="width: 330px; height: 25px" onclick="return formsum(); return true"></p></form>';
         }
      }
   }

   $a2 = 0;
   //if ($a2==1 and $a3==1)
   if ($a2 == 0) {
   ?>
      <div class="style1">
         <strong>Descargar o imprimir documentos<br />
         </strong>
      </div>

      <?
      //$q = "update year set ent='1',fecha_matri='".date('m/d/Y')."', fecha='".$_POST['e3'].'-'.$_POST['e1'].'-'.$_POST['e2']."', edad='".$_POST['e4']."',genero='".$_POST['sexo']."', vivecon='".$_POST['Radio1']."' where ss = '".$_POST['ss']."' AND year='$_POST[year]'";
      //mysql_query($q, $db) or die ("problema con query 12");
      $q = 0;
      $costo = 0.00;
      $est = '';
      while ($row = mysql_fetch_array($resultado3)) {
         if ($row['rema'] == 'Si' and $row['ent'] == 'S') {
            $q = $q + 1;
            $costo = $costo + $row['tmat'];
            $est = $row['id'];
            echo '<form name="padres" method="POST" action="matricula.php" target="_blank">';
            echo "<input name=ss type='hidden' value='" . $row[0] . "'>";
            echo '<p class="style11"><input name="grabar" type="submit" value="' . $row[4] . ' ' . $row[3] . '" class="myButton" style="width: 330px; height: 25px"></p></form>';
         }
      }

      $q = 0;
      $costo = 0.00;
      $est = '';
      while ($row = mysql_fetch_array($resultado4)) {
         if ($row['rema'] == 'Si' and $row['ent'] == 'S' and $row['matri'] == 'S') {
            if ($q == 0) {
               echo '<p class="style5"><strong>Re-Matr&#65533;cula pagada(s)</strong></p>';
            }

            $q = $q + 1;
            $costo = $costo + $row['tmat'];
            $est = $row['id'];
            echo '<p class="style11"><input name="grabar" type="submit" value="' . $row[4] . ' ' . $row[3] . '" class="myButton" style="width: 330px; height: 25px"></p>';
         }
      }



      $q = 0;
      $costo = 0.00;
      $est = '';
      while ($row = mysql_fetch_array($resultado5)) {
         if ($row['rema'] == 'Si' and $row['ent'] == 'S' and $row['matri'] == '') {

            $q = $q + 1;
            $costo = $costo + $row['tmat'];
            $est = $row['id'];
         }
      }


      if ($q == 0) {
         exit;
      }


      $mesp = 'Matr&#65533;cula ' . $y4;

      $products2 = $id . ' = Matr&#65533;cula ' . $y4;
      $products2 = $id . ' = ' . $q . ' Matr&#65533;cula(s) ' . $y4;

      $res = mysql_query($consulta);


      ?>

      <!-- <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
      <input type="hidden" name="cmd" value="_cart">
      <input type="hidden" name="upload" value="1">
      <input type="hidden" name="cbt" value="Presione aqu&#65533; para procesar el pago al Colegio" /> -->
      <!-- <input type="hidden" name="image_url" value="http://www.schoolsoftpr.com/logo.gif" style="width: 11%" /> -->
      <!-- <input type="hidden" name="currency_code" value="USD">
      <input type="hidden" name="return" value="<?= "http://" . $dirhost2 ?> " />
      <input type="hidden" name="cancel_return" value="<?= "http://" . $dirhost1 ?> " />

      <input type="hidden" name="business" value="<?= $cole->email_paypal ?>">

      <input type="hidden" name="item_name_1" value="Matr&#65533;cula 2021-2022">
      <input type="hidden" name="amount_1" value="0"> -->

      <?php //$count = 2; 
      ?>
      <?php //while ($estu = mysql_fetch_object($res)) : 
      ?>
      <?php //if ($estu->rema === 'Si' && $estu->ent === 'S') : 
      ?>
      <?php
      // $gradeNumber = (int) substr($estu->grado, 0, 2);
      // $gradeNumber++;
      // $grado = ($gradeNumber < 10) ? "0{$gradeNumber}" : $gradeNumber;
      ?>
      <!-- <input type="hidden" name="item_name_<?= $count; ?>" value="<?= "($id) $estu->nombre $estu->apellidos - Grado: $grado"  ?>">
            <input type="hidden" name="amount_<?= $count ?>" value=" <?= $estu->tmat ?>"> -->
      <?php //$count++; 
      ?>
      <?php //endif 
      ?>


      <?php //endwhile 
      ?>
      <!-- <center>
         <img alt="paypal logo" height="22" src="../../images/paypal_logo.jpg" width="69" />
         <br>
         <button type="submit" class="myButton" style="width: 330px; height: 25px"><?= $q . ' Matr&#65533;cula(s) / pagar $' . $costo ?></button>
      </center> -->




      </form>



      </form>
      <div class="container mb-4">
         <p>Favor de llenar la información en el botón <a href="padres.php">padres</a>.</p>
         <div class="row">
            <div class="col-12">
               <div class="table-responsive">
                  <table class="table table-striped">
                     <thead>
                        <tr>
                           <th scope="col"> </th>
                           <th class="text-center" scope="col">ID</th>
                           <th scope="col">Estudiante</th>
                           <th class="text-center" scope="col">Grado</th>
                           <th class="text-end" scope="col">Precio</th>
                           <th scope="col"></th>
                        </tr>
                     </thead>
                     <tbody id="estudiantes">
                        <?php $total = 0;
                        $count = 1; ?>
                        <?php while ($estu = mysql_fetch_object($res)) : ?>
                           <?php if ($estu->rema === 'Si' && $estu->ent === 'S') : ?>
                              <?php $total += $estu->tmat; ?>
                              <?php $gradeNumber = (int) substr($estu->grado, 0, 2)  + 1 ?>
                              <?php $grade = ($gradeNumber < 10) ? '0' . $gradeNumber :  $gradeNumber ?>

                              <tr>
                                 <td>
                                    <?php if (file_exists("../picture/$estu->tipo.jpg")) : ?>
                                       <img style="width: 5rem;" class="img-fluid img-thumbnail" src="../picture/<?= $estu->tipo ?>.jpg" alt="Foto de perfil" />
                                    <?php endif ?>
                                    <input type="hidden" id="grado<?= $count ?>" value="<?= $grade  ?>">
                                 </td>
                                 <td class="text-center"><?= $estu->mt  ?></td>
                                 <td><?= utf8_decode("$estu->nombre $estu->apellidos")  ?></td>
                                 <td class="text-center"><?= $grade ?></td>
                                 <td class="text-end total">$<span><?= $estu->tmat  ?></span></td>
                                 <td class="text-center"><input type="checkbox" class="form-check-input check"></td>
                              </tr>

                              <?php $count++; ?>
                           <?php endif ?>
                        <?php endwhile ?>
                        <tr>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class='text-end'><strong>Total</strong></td>
                           <th class="text-end">$<span id="total">0.00</span></th>
                           <td></td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="row col-12 mb-2">
               <div class="col-md-6">
                  <label for="nombre" class="form-label">Nombre Completo</label>
                  <input type="email" class="form-control" id="nombre" required>
                  <div class="invalid-feedback">
                     No puede dejarlo vacio.
                  </div>
               </div>
               <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" required>
                  <div class="invalid-feedback">
                     No puede dejarlo vacio.
                  </div>
               </div>
               <div class="col-12">
                  <label for="dir1" class="form-label">Dirección 1</label>
                  <input type="text" class="form-control" id="dir1" placeholder="1234 Main St">
               </div>
               <div class="col-12">
                  <label for="dir2" class="form-label">Dirección 2</label>
                  <input type="text" class="form-control" id="dir2" placeholder="Apartment, studio, or floor">
               </div>
               <div class="col-md-6">
                  <label for="ciudad" class="form-label">Ciudad</label>
                  <input type="text" class="form-control" id="ciudad">
               </div>
               <div class="col-md-4">
                  <label for="estado" class="form-label">Estado</label>
                  <input type="text" class="form-control" id="estado">
               </div>
               <div class="col-md-2">
                  <label for="zip" class="form-label">Zip</label>
                  <input type="text" class="form-control" id="zip" required>
                  <div class="invalid-feedback">
                     No puede dejarlo vacio.
                  </div>
               </div>
            </div>


            <div class="col-12 d-grid gap-2 d-flex  justify-content-end mt-3">
               <button type="button" class="btn btn-lg btn-block btn-success text-uppercase disabled" id="pagar">Ir a pagar</button>
            </div>

         </div>
      </div>


      <?php
      $resul = mysql_query("SELECT * FROM madre WHERE id = '$id'");
      $madre = mysql_fetch_object($resul);
      ?>

      <!-- needed for javascript  -->
      <input type="hidden" id='cuenta' value="<?= $id ?>">


   <?  } ?>

   <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
   <script type="text/javascript">
      $(function() {
         const _amount = $("#estudiantes").children('tr').length - 1
         let total = 0
         let gradeToPay = []
         $(".check").change(function(e) {
            total = 0
            gradeToPay = []
            $.each($(".check"), function(index, checkBox) {
               if ($(checkBox).prop('checked')) {
                  const costo = parseFloat($(checkBox).parents('tr').children('.total').children('span').text())
                  total += costo
                  gradeToPay[index] = $(checkBox).parents('tr').children('.total').prev('td').text()
               }
            });
            $("#total").text(total.toFixed(2))
            if (total > 0) {
               total = total.toFixed(2)
               $("#pagar").removeClass('disabled')
            } else {
               $("#pagar").addClass("disabled")
            }

            gradeToPay = gradeToPay.filter(student => student !== null)

         })


         $("#pagar").click(function(e) {
            if ($("#nombre").val().length > 0 && $("#zip").val().length > 0 && $("#email").val().length > 0) {
               $("#pagar").addClass('disabled')
               console.log(total)
               $("#nombre,#zip,#email").removeClass('is-invalid')
               let descripcion = ''
               gradeToPay.forEach((grade, index) => {
                  if (index === 0) {
                     descripcion = grade
                  } else if (gradeToPay.length - 1 === index) {
                     descripcion += " y " + grade
                  } else if (index > 0) {
                     descripcion += ", " + grade

                  }
               })
               let _data = {
                  "username": "CERT4549444000009",
                  "password": "433NQ2nE",
                  "accountID": $("#cuenta").val(),
                  "customerName": $("#nombre").val(),
                  "customerEmail": $("#email").val(),
                  "address1": $("#dir1").val(),
                  "address2": $("#dir2").val(),
                  "city": $("#ciudad").val(),
                  "state": $("#estado").val(),
                  "zipcode": $("#zip").val(),
                  "phone": "",
                  "fax": "",
                  "trxID": "1",
                  "trxDescription": `Matricula para ${(gradeToPay.length > 1) ? "los grados " : 'el grado '} ${descripcion}`,
                  "trxAmount": total,
                  "taxAmount1": "",
                  "taxAmount2": "",
                  "taxAmount3": "",
                  "taxAmount4": "",
                  "taxAmount5": "",
                  "filler1": "",
                  "filler2": "",
                  "filler3": "",
                  "language": "es",
                  "ignoreValues": ""

               }
               const dataJson = JSON.stringify(_data);

               $.ajax({
                  type: "POST",
                  url: "https://uat.mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessCheckoutPayment/",
                  data: dataJson,
                  crossDomain: true,
                  contentType: "application/json ",
                  complete: function(response) {
                     $("#pagar").removeClass('disabled')
                     window.open(response.responseJSON.rMsg, "ModalPopUp",
                        "toolbar=no," +
                        "scrollbars=no," +
                        "location=no," +
                        "statusbar=no," +
                        "menubar=no," +
                        "resizable=0," +
                        "width=800," +
                        "height=800," +
                        "left = 490," +
                        "top=300")
                  }
               });
            } else {
               if ($("#nombre").val().length === 0) {
                  $("#nombre").addClass('is-invalid')
               } else {
                  $("#nombre").removeClass('is-invalid')
               }
               if ($("#zip").val().length === 0) {
                  $("#zip").addClass('is-invalid')
               } else {
                  $("#zip").removeClass('is-invalid')
               }
               if ($("#email").val().length === 0) {
                  $("#email").addClass('is-invalid')
               } else {
                  $("#email").removeClass('is-invalid')
               }
            }
         })


      });
   </script>
</body>

</html>