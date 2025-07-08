<?php
require_once '../../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Mail;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

use App\Models\Admin;
use App\Models\Family;

use Classes\Email;
use Illuminate\Support\Carbon;


Session::is_logged();

$today = Carbon::now();
$files = [];

$col = db::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $col->colegio;

$school = Admin::user(Session::id())->first();

$year = $school->year2;
$chk = $school->chk;
$reply_to = $school->correo;
$user = $school->usuario;



$lang = new Lang([
    ['Usuario y contraseña', 'Username and password'],
    ['Información de acceso al área de padres.', "Parents' Area Access Information."],
    ['Esta es la información de acceso para los padres del estudiante', 'This is the access information for the parents of the student'],
    ['Usuario', 'Username'],
    ['Contraseña', 'Password'],
    ['Acceso', 'Access'],
    ["Se ha enviado el correo electrónico", "Email has been sent"],
]);
$students = $_POST['students'];
$mail = new Mail();
$co_re = __RESEND_KEY_OTHER__;
$from = "{$colegio} <".$co_re.">";

foreach ($students as $ss) {
    $student = new Student($ss);
   
        $parents = DB::table('madre')->where(['id', $student->id])->first();
        $emails = [
            ['correo' => $parents->email_p, 'nombre' => $parents->padre],
            ['correo' => $parents->email_m, 'nombre' => $parents->madre]
        ];
    
    $link = Route::url('/foro/login.php', true);
    $link = Route::url('', true);
    $schoolName = $student->info('colegio');
    $studentName = "{$student->id} {$student->nombre} {$student->apellidos}";
    $username = $parents->usuario;
    $password = $parents->clave;
    $messageTitle = $lang->translation('Usuario y contraseña');
    $_lang = __LANG;
   $Body = "
   <!DOCTYPE html>
   <html lang='{$_lang}'>
   <head>
     <meta charset='UTF-8'>
     <meta name='viewport' content='width=device-width, initial-scale=1.0'>
     <title>{$messageTitle}</title>
   </head>
   <body>
   <center><h1>{$schoolName}</h1></center>
   <center><h2>{$messageTitle}</h2></center>
   <br>
   <br>
   <p>{$lang->translation('Esta es la información de acceso para los padres del estudiante')}: <b>{$studentName}</b></p>
   <ul style='list-style: none;'>
      <li>{$lang->translation('Usuario')}: <b>{$username}</b></li>
      <li style='margin-top: 10px;'>{$lang->translation('Contraseña')}: <b>{$password}</b></li><br>   
      <li style='margin-top: 10px;'><b>Link: </b><a href='{$link}' style='color: #FFFFFF; background-color: #FF3A00'>{$lang->translation('Acceso')}</a></li>
      <br><br>
   </ul>
   <hr>
   </body>
   </html>
   ";
        $to = [];
        foreach ($emails as $email) {
            if ($email['correo'] !== '') {
                $to[] = $email['correo'];
            }
        }


//        Email::to($to)
//            ->subject($messageTitle)
//            ->body($Body)
//            ->queue($to2);
//            ->queue($student->id);


        DB::table('email_queue')->insert([
        'from' => utf8_encode($from),
        'reply_to' => $reply_to,
        'to' => json_encode($to),
        'message' => utf8_encode($Body),
        'text' => '',
        'subject' => utf8_encode($messageTitle),
        'user' => $user,
        'year' => $year,
        'id2' => $student->id,
        'social_securities' => $student->ss,
        ]);


    
}
Route::redirect("/users/email/sendUsers.php");
