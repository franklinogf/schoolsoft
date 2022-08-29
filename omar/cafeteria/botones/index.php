<?php 
include('../../control.php'); 
$foods = array();
 $result = mysql_query("SELECT * FROM T_cafeteria ORDER BY orden");

while ($row = mysql_fetch_object($result)) {
   $foods[] = $row;
 }
 //cantidad de botones que hay
 $cant_buttons = sizeof($foods);
/* for ($i=0; $i < 4; $i++) { 
   $foods[] = $i;
 }*/

$foods = json_decode(json_encode($foods));
$foods = array_chunk($foods, 5);
$directory = "../../../cafeteria_im";
$images = glob($directory . "/*");


//maximo de botones permitidos para crear
$max_buttons = 15;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   

    <title>Cafeteria </title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
   
     <link rel="stylesheet" type="text/css" href="../css/all.css">

    <style type="text/css">
      .img-list{
        margin: 10px;
      }
      .card-body{
            padding: 8px !important;
             text-align: center;
      }
      .card-img-top{
        max-width: 123px;
        height: 130px;
      }


      .card:hover, #images img:hover {
        opacity: 0.6;
      box-shadow: 0px 0 5px 1px black;
      }
      .card{
        margin-left: 5px !important;
        margin-bottom: 5px !important;        
        max-width: 132px;
        max-height: 208px;
        cursor: pointer;
        float: left;

      }
      #sortable{
        min-height: 213px;
        padding: 45px;
      }

.card:active,#images img:active {
    /*box-shadow: 0 0 5px -1px rgba(0,0,0,0.6);*/
  box-shadow:  0px 0px 14px 8px #666;
  transform: translateY(-4px);
}


.price b:before{
   font-family: "Font Awesome 5 Free"; 
  content:"\f155";
  font-size:15px; 
  }


#images img{
width: 73px;
height: 73px;
cursor: pointer;
margin-top: 5px;
}

.selected-img{
    box-shadow: 0px 0px 10px 2px #007bff;
}

.selected-img:hover {
    opacity: 0.6;
    box-shadow: 0px 0 5px 1px #007bff !important;
  }

.selected-img:active {
  box-shadow:  0px 0px 14px 8px #007bff !important;
}


.sort-placehoder { 
   /*height: 208px !important;*/
  width: 125.6px !important;  
  /*line-height: 125.6px;*/
  background: #007bff30;
}
.text-center{
    padding-bottom: 1rem!important;


    padding-top: 2rem!important;
}
    </style>
  </head>

  <body class="bg-light">

    <div class="container">
      <div class="py-5 text-center">
        <h2>Botones <i class="fas fa-clipboard-list"></i></h2>        
      </div>       
          
      <div class="row">
  <!-- LIST START -->
        <div id="sortable"  class="col-md-8 order-md-1 bg-white shadow"> 
       <?php foreach ($foods as $food): ?>           
              <?php foreach ($food as $foo): ?>
                 <div id="<?echo $foo->id ?>" class="card" data-action='edit' data-target="#Modal" data-toggle="modal">
                   <a href="#"><img class="card-img-top" src="<?php echo (isset($foo->foto))?"../../../cafeteria_im/$foo->foto" :'../../../cafeteria_im/no-image.png'?>" alt="<?php echo $foo->foto ?>"></a>
                   <div class="card-body">
                     <p class="card-title"><?php echo $foo->articulo ?></p>
                     <span class="price"><b><?php echo $foo->precio ?></b></span>  
                   </div>
                 </div>    
               <?php endforeach ?>
        
       <?php endforeach ?>
         



        </div>  
<!-- LIST END -->
<!-- agregar boton -->

        <div class="col-md-4 order-md-2 mb-4 ">
          <a href="#" id="add" class="btn btn-primary btn-lg btn-block <?php echo ($cant_buttons == $max_buttons)?'disabled':'' ?>" data-action='add' data-toggle="modal" data-target="#Modal">Agregar Boton <i class="fas fa-plus"></i></a>
           <a href="../menu.php" class="btn btn-secondary btn-lg btn-block" >Salir <i class="fas fa-angle-left"></i></a>
          
        </div>

        <!-- Modal -->
        <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="Modal">Agregar Boton</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
                <form id="form" method="post" action="guardar.php">
              <div class="modal-body">
                  <div class="form-group">
                    <label for="title">Titulo</label>
                    <input type="text" class="form-control" required="" id="title" name="titulo" placeholder="Titulo del articulo">                    
                  </div>
                  <div class="form-group">
                    <label for="price">Precio:</label>
                    <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" required="" class="form-control" id="price" name="precio" placeholder="Precio del articulo">
                    </div>
                  </div>
                  <div id="images">
                    <?php foreach ($images as $image): ?>
                      <img src="<?php echo $image ?>" alt="<?php echo substr(strrchr($image,'/'), 1) ?>" class="img-thumbnail">
                    <?php endforeach ?>                    
                  </div>
                  <input id="id" type="hidden" name="id">
                  <input id="image" type="hidden" name="image">
                  <input id="orden" type="hidden" name="orden">
              </div>
              <div class="modal-footer">
                <button type="button" id="cancelar" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="eliminar" class="btn btn-danger">Eliminar</button>
                <button type="submit" id="guardar"  class="btn btn-primary">Guardar</button>
              </div>
                </form>
            </div>
          </div>
        </div>



    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script src="../js/jquery-3.3.1.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

    <script>
      $(document).ready(function(){

//mover los botones
    $( "#sortable" ).sortable({
      // placeholder: "sort-placehoder",
      grid: [3,5],
      appendTo: "#sortable",
      cursor: "pointer",
      cursorAt: { left: 5 },
      delay: 750,
      // forcePlaceholderSize: true,
      revert: 115,
      tolerance: "pointer",     
      update: function( event, ui ) {
       // var $sorted = $( ".card-deck" ).sortable( "serialize", { key: "sort" } );
       //guardar el cambio de orden
        Ordenar();
      }  

    });
    $( ".card-deck" ).disableSelection();

function Ordenar(){
   var $sorted_array = $( "#sortable" ).sortable( "toArray");
         $.post('orden.php', {"ids":$sorted_array}, function(data, textStatus, xhr) {
         });
}



//modal

$("#eliminar").click(function(event) {
  if (confirm('Esta seguro que desea eliminarlo?')) {
    $("#form").prop('action', 'eliminar.php');
    console.log("#"+$("#id").val());
    $("#"+$("#id").val()).remove();
    Ordenar();
    $('#form').submit();
  }
});



$('#Modal').on('shown.bs.modal', function (event) {
  var modal = $(this);
  var button = $(event.relatedTarget);
  var recipient = button.data('action');
  $("#images img").removeClass('selected-img');


   if (recipient == 'add') {

        modal.find('.modal-title').text('Agregar Boton');
        modal.find('#title').val('');
        modal.find('#price').val('');
        modal.find('#guardar').text('Guardar');
        modal.find('#eliminar').hide('fast');
        modal.find('#form').prop('action','guardar.php');
        modal.find('#image').val('');

        $('#title').trigger('focus');    

   }else{

         var $title = $(button).find('.card-title').text(); 
         var $price = $(button).find('.price').text(); 
         var $id = $(button).prop('id'); 
         var $img = $(button).find('.card-img-top').prop('alt');      

          modal.find('.modal-title').text('Editar Boton');
          modal.find('#title').val($title);
          modal.find('#price').val($price);
          modal.find('#id').val($id);
          modal.find('#guardar').text('Actualizar');
          modal.find('#eliminar').show('fast');
          modal.find('#form').prop('action','editar.php');
          modal.find('#image').val($img);

          $("#images img").each(function(index, val) {
             if ($(this).prop('alt') == $img) {
              $(this).addClass('selected-img');
              return;
             }
          });
   }
  
})

$('#Modal').on('hidden.bs.modal', function (event) {  
    $("#images img").removeClass('selected-img');     
})


//form

$("#form").submit(function(event) {
  var $foto = false;

  $("#images img").each(function(index, val) {
     if ($(this).hasClass('selected-img')) {
      $foto = true;
      return;
     }
  });

  $("#orden").val($(".card").length+1);


  if ($(this).prop('action') != 'eliminar.php'){
    if(!$foto){
      event.preventDefault();
      alert("Debe de seleccionar una imagen para su articulo\nSi no desea utilizar una imagen por favor seleccione 'No imagen'");
    }
}


});


//total del carrito
      function Total() {
        var $price = 0;
        var $cant = 0;
       /* $( "#cart li.pric" ).each(function() {        
          $cant++;
        });      
        $("#cant").text($cant);*/
      }


     /* $('#cart').on('click','li.pric',function(){
        console.log("hola");
        $(this).hide( "drop", { direction: "left" }, "fast", function(){
          $(this).remove();
          Total();
        });
      });*/


      //add
      $("#images img").click(function(){

          $("#images img").removeClass('selected-img');
          $(this).addClass('selected-img');

          $("#image").val($(this).attr('alt'));


      });

        
      
      });

    </script>
  </body>
</html>
