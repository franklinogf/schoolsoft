<?php
require_once '../app.php';

use Classes\DataBase\DB;

/* -------------------------------------------------------------------------- */
/*                           DATABASE MODIFICATIONS                           */
/* -------------------------------------------------------------------------- */

DB::table("t_mensajes_archivos")->create("
`id` INT NOT NULL AUTO_INCREMENT,
`nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
`mensaje_code` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)"
);

DB::table("tareas_enviadas")->alter("DROP INDEX `id_tarea`, ADD INDEX `id_tarea` (`id_tarea`) USING BTREE");

DB::table("t_mensajes_links")->create("
`id` INT NOT NULL AUTO_INCREMENT ,
`link` TEXT NOT NULL ,
`nombre` VARCHAR(150) NULL ,
`mensaje_code` INT NOT NULL,
 PRIMARY KEY (`id`)");
DB::table('T_archivos')->alter("RENAME TO t_archivos");
DB::table('T_tareas_archivos')->alter("RENAME TO t_tareas_archivos");

DB::table('T_examenes')->alter("
ADD `hora_final` TIME NOT NULL AFTER `hora`,
ADD `desc1` CHAR(2) NOT NULL DEFAULT 'no' AFTER `activo`,
ADD `desc1_1` TEXT NULL DEFAULT NULL AFTER `desc1`,
ADD `desc2` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc1_1`,
ADD `desc2_1` TEXT NULL DEFAULT NULL AFTER `desc2`,
ADD `desc3` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc2_1`,
ADD `desc3_1` TEXT NULL DEFAULT NULL AFTER `desc3`,
ADD `desc4` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc3_1`,
ADD `desc4_1` TEXT NULL DEFAULT NULL AFTER `desc4`,
ADD `desc5` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc4_1`,
ADD `desc5_1` TEXT NULL DEFAULT NULL AFTER `desc5`
");

/* -------------------------------------------------------------------------- */
/*                         END DATABASE MODIFICATIONS                         */
/* -------------------------------------------------------------------------- */