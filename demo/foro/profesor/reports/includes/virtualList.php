<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use Classes\Util;
use Classes\Server;
use Classes\Session;


Server::is_post();
$teacher = Teacher::query()->findOrFail(Session::id());

if (isset($_POST['virtualByClass'])) {
   $data = $teacher->virtualClasses()->where([
      'curso' => $_POST['virtualByClass'],
   ])->get();

   if ($data) {
      $array = [
         'response' => true,
         'data' => $data
      ];
   } else {
      $array = ['response' => false];
   }
   echo Util::toJson($array);
}
