<?php

use Illuminate\Database\Capsule\Manager;

require_once '../../../app.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $buttons = Manager::table('T_cafeteria')->orderBy('orden')->get();
  $foods = [];
  foreach ($buttons as $button) {
    $foods[] = [
      "id" => $button->id,
      "imageUrl" => isset($button->foto) ? "../../../cafeteria_im/$button->foto" : "../../../cafeteria_im/no-image.png",
      "label" => $button->articulo,
      "price" => floatval($button->precio),
      "discountedPrice" => floatval($button->precio_descuento),
      "priceForHighGrades" => floatval($button->precio2),
    ];
  }
  echo json_encode($foods, JSON_UNESCAPED_UNICODE);
}
