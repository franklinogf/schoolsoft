<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Teacher;
use Classes\Util;
use Classes\Server;
use Classes\Session;


Server::is_post();
$teacher = Teacher::query()->findOrFail(Session::id());

if (isset($_POST['homeworksByClass'])) {
   $class = $_POST['homeworksByClass'];

   $data = $teacher->homeworks()->ofClass($class)->get();

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
