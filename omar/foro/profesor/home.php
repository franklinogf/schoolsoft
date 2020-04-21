<?php
use Classes\Route;
use Classes\Controllers\Teacher;

include '../../app.php';
if(!isset($_SESSION['logged'])){
  Route::redirect('/foro');
}
$teacher = new Teacher($_SESSION['logged']['user']['id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro - Salon Hogar</title>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  Route::includeFile('/includes/datatable-css.php',true); 
  ?>
</head>

<body>

  <?php    
    Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 class="text-center">Mi Salon Hogar</h1>
    <table id="studentsTable" class="table table-striped table-hover cell-border w-100 shadow">
      <thead class="bg-gradient-primary border-0">
        <tr>
          <th>Estudiante</th>
          <th>Usuario</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($teacher->homeStudents() as $student): ?>
        <tr>
          <td><?= "$student->apellidos $student->nombre" ?></td>
          <td><?= $student->usuario ?></td>
        </tr>
        <?php endforeach ?>
        
        
      </tbody>
      <tfoot>
        <tr class="bg-gradient-secondary">
          <th>Estudiante</th>
          <th>Usuario</th>
        </tr>
      </tfoot>
    </table>
    <a href="#" class="btn btn-primary mt-2">Enviar usuarios a los padres</a>

    <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="username">Usuario</label>
                  <input type="text" class="form-control" name='username' id="username">
                </div>
                <div class="form-group  col-md-6">
                  <label for="pass1">Nueva Clave</label>
                  <input type="password" class="form-control" name='pass1' id="pass1">
                  <label for="pass2">Confirmar Clave</label>
                  <input type="password" class="form-control" name='pass2' id="pass2">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php'); 
  Route::includeFile('/includes/datatable-js.php',true); 
  ?>
</body>

</html>