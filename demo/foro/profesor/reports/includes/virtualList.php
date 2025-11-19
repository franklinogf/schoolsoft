<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;

Server::is_post();
$teacher = new Teacher(Session::id());

if (isset($_POST['virtualByClass'])) {
   $data = DB::table('virtual')->where([
      ['id_profesor',$teacher->id],
      ['curso',$_POST['virtualByClass']],
      ['year',$teacher->info('year')],
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
