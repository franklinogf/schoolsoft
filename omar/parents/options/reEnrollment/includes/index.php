<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;

Server::is_post();
$parents = new Parents(Session::id());
$year = $parents->info('year');

if (isset($_POST['reEnrollment'])) {
    foreach ($parents->kids() as $kid) {
        $rema = 'No';
        if (isset($_POST["student"][$kid->mt])) {
            $rema = 'Si';
        } else {
            $rema = 'No';
        }
        DB::table("year")->where('mt', $kid->mt)->update([
            "rema" => $rema
        ]);
    }
}
