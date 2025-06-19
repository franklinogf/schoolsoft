<?php
require '../../../../app.php';

use App\Enums\Status;
use App\Models\Admin;
use Classes\Route;
use Classes\Session;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = Admin::find($_POST['id']);
    $admin->update(['activo' => $admin->activo === Status::ACTIVE->value ? Status::INACTIVE->value : Status::ACTIVE->value]);
    Session::set('success', __("El usuario ha sido actualizado"));
    Route::redirect('/users/administration/index.php');
}
