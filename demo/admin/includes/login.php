<?php

use App\Models\Admin;
use Classes\Route;


require '../../app.php';


if ($_SERVER["REQUEST_METHOD"] == 'POST') {

   $admin =  Admin::where([
      ['usuario', $_POST['username']],
      ['clave', $_POST['password']]
   ])->first();

   if ($admin) {

      $_SESSION['logged'] = [
         'acronym' => __SCHOOL_ACRONYM,
         'location' => "admin",
         "user" => ['id' => $admin->usuario],
      ];
      $_SESSION['start'] = time();
      $_SESSION['id1'] = $admin->id;
      $_SESSION['usua1'] = $admin->usuario;

      Route::redirect('admin/', false);
   } else {
      $_SESSION['errorLogin'] = __("No coincide con los datos, intentelo otra vez");

      Route::redirect();
   }
} else {
   Route::error();
}
