<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <title>Foro - Mensajes</title>

   <?php
   Route::includeFile('/foro/profesor/includes/layouts/links.php');
   ?>

</head>

<body>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>

   <div class="container mt-md-5">
      <div class="row shadow inbox">
         <div class="col-12 col-md-4 p-0 border-primary overflow-auto mh-100 custom-scroll">

            <div class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">Franklin González Flores</p>
                  <p class="card-text mb-0 text-muted"><small>Thursday 5th July 2014</small></p>
                  <p class="card-text mb-0 text-truncate font-weight-light">sdfsd fds sfsdfsdfs Asunto asdasd asdjas kldjakdl jasdkljaskldjkalsd </p>
                  <p class="card-text text-right"><small class="badge badge-success rounded-0">new</small></p>
               </div>
            </div>

            <div class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">Franklin González Flores</p>
                  <p class="card-text mb-0 text-muted"><small>Thursday 5th July 2014</small></p>
                  <p class="card-text mb-0 text-truncate font-weight-light">Asunto asdasd asdjas kldjakdl jasdkljaskldjkalsd </p>
                  <p class="card-text text-right"><small class="badge badge-success rounded-0">new</small></p>
               </div>
            </div>

            <div class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">Franklin González Flores</p>
                  <p class="card-text mb-0 text-muted"><small>Thursday 5th July 2014</small></p>
                  <p class="card-text mb-0 text-truncate font-weight-light">Asunto asdasd asdjas kldjakdl jasdkljaskldjkalsd </p>
                  <p class="card-text text-right"><small class="badge badge-success rounded-0">new</small></p>
               </div>
            </div>

            <div class="card w-100 rounded-0 pointer-cursor">
               <div class="card-body p-2">
                  <p class="card-text mb-0 font-weight-bold">Franklin González Flores</p>
                  <p class="card-text mb-0 text-muted"><small>Thursday 5th July 2014</small></p>
                  <p class="card-text mb-0 text-truncate font-weight-light">Asunto asdasd asdjas kldjakdl jasdkljaskldjkalsd </p>
                  <p class="card-text text-right"><small class="badge badge-success rounded-0">new</small></p>
               </div>
            </div>

         </div>

         <div class="col-12 col-md-8 bg-gradient-light bg-light overflow-auto mh-100 custom-scroll">
            <div class="media p-2">
               <img src="<?= __NO_PROFILE_PICTURE ?>" class="align-self-start mr-2 rounded-circle" alt="Profile Picture" width="52" height="52">
               <div class="media-body">
                  <p class="m-0"><strong>Franklin González Flores</strong> <small>(teacher)</small></p>
                  <small class="text-muted font-weight-light">Thursday 5th July 2014</small>                  
               </div>
               <button title="Responder" class="btn btn-secondary btn-sm" data-toggle="tooltip" type="button"><i class="fas fa-reply text-primary"></i></button>
            </div>
            <p class="p-2 my-0 font-bree">Asunto del tema</p>
            <hr class="my-1">
            <p class="p-2 mt-1 message-text font-markazi">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Eaque dolorem iste corrupti perferendis accusamus voluptatem consequuntur porro, nihil nemo voluptatibus quos voluptatum commodi et laboriosam aliquam facere error sapiente soluta.
                  Aspernatur quia dicta corporis aperiam dolorem, aliquid quidem laboriosam amet. Perferendis, mollitia, illo explicabo minus obcaecati unde incidunt autem necessitatibus nostrum, vitae fugit iste consectetur itaque sapiente libero. Quo, expedita!
                  Aperiam pariatur aspernatur culpa corrupti nemo amet repellat, qui provident adipisci et quaerat minima, aliquam omnis nobis facilis est vero, dolores ex iusto nesciunt cum debitis reprehenderit quis? Eaque, magnam!
                  Deserunt dolorem commodi consequatur ut fugiat molestias ipsum hic consectetur debitis delectus fugit tenetur quaerat odit saepe, nulla vitae autem! Sapiente tenetur minima quidem expedita iusto? Mollitia magni libero voluptatem?
                  Dolores tenetur sit sapiente repudiandae cum deleniti aperiam doloribus optio, quaerat provident officia laboriosam quis ut quisquam asperiores officiis quam odio similique. Sint natus rem voluptas nihil, iure iste aliquid!
                  Saepe hic deserunt eligendi aperiam, ex dicta omnis autem. Obcaecati est commodi sit, eos suscipit fugiat quis, fugit, illo consequatur quod ullam mollitia at! Blanditiis iure quia est ipsam nostrum.
                  Facilis quibusdam aliquam eius, labore id facere deserunt nostrum, repudiandae inventore nulla quos totam. Incidunt, culpa dolorum necessitatibus temporibus distinctio harum eveniet quidem sed error, ut ratione quasi minima magni!
                  Eaque quod quo iusto quam a aliquam autem ullam, eos provident, doloremque dolorem? Voluptate, alias? Corporis hic aliquid quas quam omnis voluptatibus sint nemo porro adipisci magnam aut, sunt minima.
                  Nulla vitae tenetur et aspernatur deleniti aliquid, impedit necessitatibus odio iusto consequuntur aut assumenda natus aliquam eos placeat voluptas. Distinctio totam voluptates repudiandae voluptas reprehenderit incidunt culpa molestiae assumenda cupiditate.
                  Quas exercitationem aliquam quis ratione similique explicabo voluptates soluta, accusamus eligendi, excepturi praesentium aliquid eius dolorem consequatur nam nisi! Fugiat non exercitationem eaque qui dolor rerum, libero voluptatum aliquam accusantium!</p>
               </div>
         </div>
      </div>


      <?php
      Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
      ?>
</body>

</html>