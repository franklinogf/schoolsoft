<?php
require_once '../app.php';

use Classes\DataBase\DB;

/* -------------------------------------------------------------------------- */
/*                           DATABASE MODIFICATIONS                           */
/* -------------------------------------------------------------------------- */

/* ------------------------------ table Padres ------------------------------ */
DB::table("padres")->alter("
ADD COLUMN IF NOT EXISTS `average1` INT NULL,
ADD COLUMN IF NOT EXISTS `average2` INT NULL,
ADD COLUMN IF NOT EXISTS `average3` INT NULL,
ADD COLUMN IF NOT EXISTS `average4` INT NULL;");
/* ------------------------------ Teachers table ----------------------------- */
DB::table('profesor')->alter("
CHANGE `nivel` `nivel` CHAR(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `grado` `grado` CHAR(5) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lic1` `lic1` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lic2` `lic2` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lic3` `lic3` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lic4` `lic4` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lp1` `lp1` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lp2` `lp2` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lp3` `lp3` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `lp4` `lp4` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `fex1` `fex1` DATE NULL,
CHANGE `fex2` `fex2` DATE NULL,
CHANGE `fex3` `fex3` DATE NULL,
CHANGE `fex4` `fex4` DATE NULL,
CHANGE `club1` `club1` CHAR(35) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `club2` `club2` CHAR(35) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `club3` `club3` CHAR(35) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `club4` `club4` CHAR(35) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `club5` `club5` CHAR(35) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `pre1` `pre1` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `pre2` `pre2` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `pre3` `pre3` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `pre4` `pre4` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `pre5` `pre5` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `vi1` `vi1` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `vi2` `vi2` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `vi3` `vi3` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `vi4` `vi4` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `vi5` `vi5` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `se1` `se1` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `se2` `se2` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `se3` `se3` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `se4` `se4` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
CHANGE `se5` `se5` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL;");


/* ---------------------------- Cafeteria orders ---------------------------- */

DB::table("cafeteria_orders")->create("
id INT PRIMARY KEY AUTO_INCREMENT,
ss varchar(50) NOT NULL,
id_compra INT,
year varchar(50) NOT NULL,
despachado boolean NOT NULL default false
");

/* ----------------------------- homeworks table ---------------------------- */
DB::table("tbl_documentos")->alter("
ADD COLUMN IF NOT EXISTS `year` CHAR(5) NULL AFTER `hora`
");
DB::table('tbl_documentos')->where([
    ["fec_in", '>=', '2020-08-01'],
    ["fec_in", '<=', '2021-06-30'],
])
    ->update([
        "year" => '20-21'
    ]);
DB::table('tbl_documentos')->where([
    ["fec_in", '>=', '2021-08-01'],
    ["fec_in", '<=', '2022-06-30'],
])
    ->update([
        "year" => '21-22'
    ]);

DB::table("t_mensajes_archivos")->create(
    "
`id` INT NOT NULL AUTO_INCREMENT,
`nombre` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
`mensaje_code` int(11) DEFAULT NULL,
PRIMARY KEY (`id`)"
);

// DB::table("tareas_enviadas")->alter("
// DROP INDEX `id_tarea`, ADD COLUMN IF NOT EXISTS INDEX `id_tarea` (`id_tarea`)
// ");

DB::table("t_mensajes_links")->create("
`id` INT NOT NULL AUTO_INCREMENT ,
`link` TEXT NOT NULL ,
`nombre` VARCHAR(150) NULL ,
`mensaje_code` INT NOT NULL,
PRIMARY KEY (`id`)");
DB::table('T_archivos')->alter("RENAME TO t_archivos");
DB::table('T_tareas_archivos')->alter("RENAME TO t_tareas_archivos");

DB::table('T_examenes')->alter("
ADD COLUMN IF NOT EXISTS `hora_final` TIME NOT NULL AFTER `hora`,
ADD COLUMN IF NOT EXISTS `desc1` CHAR(2) NOT NULL DEFAULT 'no' AFTER `activo`,
ADD COLUMN IF NOT EXISTS `desc1_1` TEXT NULL DEFAULT NULL AFTER `desc1`,
ADD COLUMN IF NOT EXISTS `desc2` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc1_1`,
ADD COLUMN IF NOT EXISTS `desc2_1` TEXT NULL DEFAULT NULL AFTER `desc2`,
ADD COLUMN IF NOT EXISTS `desc3` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc2_1`,
ADD COLUMN IF NOT EXISTS `desc3_1` TEXT NULL DEFAULT NULL AFTER `desc3`,
ADD COLUMN IF NOT EXISTS `desc4` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc3_1`,
ADD COLUMN IF NOT EXISTS `desc4_1` TEXT NULL DEFAULT NULL AFTER `desc4`,
ADD COLUMN IF NOT EXISTS `desc5` CHAR(2) NOT NULL DEFAULT 'no' AFTER `desc4_1`,
ADD COLUMN IF NOT EXISTS `desc5_1` TEXT NULL DEFAULT NULL AFTER `desc5`
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
// DB::table('tarjeta_cambios')->alter("
//  ADD COLUMN IF NOT EXISTS `id2` INT NOT NULL AUTO_INCREMENT COMMENT 'autoincrement' FIRST, 
//  ADD PRIMARY KEY (`id2`);");
