<?php
require_once '../app.php';

use Classes\DataBase\DB;

/* -------------------------------------------------------------------------- */
/*                           DATABASE MODIFICATIONS                           */
/* -------------------------------------------------------------------------- */

/* ----------------------------- homeworks table ---------------------------- */
DB::table("tbl_documentos")->alter("
ADD `year` CHAR(5) NULL AFTER `hora`
");
DB::table('tbl_documentos')->where([
    ["fec_in",'>=','2020-08-01'],
    ["fec_in",'<=','2021-06-30'],
])
->update([
    "year"=>'20-21'
]);
DB::table('tbl_documentos')->where([
    ["fec_in",'>=','2021-08-01'],
    ["fec_in",'<=','2022-06-30'],
])
->update([
    "year"=>'21-22'
]);

DB::table("t_mensajes_archivos")->create(
    "
`id` INT NOT NULL AUTO_INCREMENT,
`nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
`mensaje_code` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)"
);

DB::table("tareas_enviadas")->alter("
DROP INDEX `id_tarea`, ADD INDEX `id_tarea` (`id_tarea`) USING BTREE
");

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

/* ---------------------- table for the virtual classes --------------------- */
DB::table("virtual")->create("
`id` INT NOT NULL AUTO_INCREMENT ,
`id_profesor` INT NOT NULL ,
`curso` VARCHAR(10) NOT NULL ,
`link` TEXT NOT NULL ,
`titulo` VARCHAR(150) NULL ,
`clave` VARCHAR(150) NULL ,
`informacion` TEXT NULL ,
`fecha` DATE NOT NULL,
`hora` TIME NOT NULL,
`year` VARCHAR(5) NOT NULL,
`activo` BOOLEAN NOT NULL DEFAULT TRUE,
PRIMARY KEY (`id`)");

DB::table("asistencia_virtual")->create("
`id` INT NOT NULL AUTO_INCREMENT ,
`id_virtual` INT NOT NULL ,
`ss_estudiante` VARCHAR(150) NOT NULL ,
`fecha` DATE NOT NULL,
`hora` TIME NOT NULL,
`year` VARCHAR(5) NOT NULL,
PRIMARY KEY (`id`)");

/* --------------------------- notes changes table -------------------------- */
DB::table('tarjeta_cambios')->alter("
 ADD `id2` INT NOT NULL AUTO_INCREMENT COMMENT 'autoincrement' FIRST, 
 ADD PRIMARY KEY (`id2`);");

