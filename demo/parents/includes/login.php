<?php

use App\Models\Family;
use Classes\Route;


require '../../app.php';


if ($_SERVER["REQUEST_METHOD"] == 'POST') {

   $user =  Family::where([
      ['usuario', $_POST['username']],
      ['clave', $_POST['password']]
   ])->first();

   if ($user) {

      $_SESSION['logged'] = [
         'acronym' => __SCHOOL_ACRONYM,
         'location' => "parents",
         "user" => ['id' => $user->id],
      ];
      $_SESSION['start'] = time();
      $_SESSION['id1'] = $user->id;
      $_SESSION['usua1'] = $user->usuario;

      Route::redirect('parents/', false);
   } else {
      $_SESSION['errorLogin'] = __("No coincide con los datos, intentelo otra vez");

      Route::redirect();
   }
} else {
   Route::error();
}
