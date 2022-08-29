<?
session_start();
$usua=$_SESSION['usua1'];
$id=$_SESSION['id1'];
if ($usua == "")
   {
   exit;
   }
if(isset($_POST['camb'])){
  include('cuentas.php');
  exit;
}
if(isset($_POST['anaest'])){
  include('add_estu2.php');
  exit;
}

function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'This is my secret key';
    $secret_iv = 'This is my secret iv';
    // hash
    $key = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

include('../control.php');

$consult1 = "select * from colegio where usuario = '$usua'";
$resultad1 = mysql_query($consult1);
$row2=mysql_fetch_array($resultad1);
list($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18,$r19,$r20) = explode("-",$row2[130]);

$idioma=$row2[21];

?>
<html>

<head>
<header Content-Type="image/jpeg">

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
<title>Pantalla de Padres</title>
<script type="text/javascript" src="../../jv/masked_input_1.js"></script>
<script type="text/javascript" src="../../jv/masked_input_ex.js"></script>
<script type="text/javascript">
function pasapago() {
   	var qpaga = document.padres.qpaga.value;
    if (qpaga=='M')
       {
       document.padres.enc.value=document.padres.madre.value;
       document.padres.par.value='Madre';
       document.padres.dir_e1.value=document.padres.dir1.value;
       document.padres.dir_e2.value=document.padres.dir3.value;
       document.padres.pue_e.value=document.padres.pueblo1.value;
       document.padres.esta_e.value=document.padres.est1.value;
       document.padres.zip_e.value=document.padres.zip1.value;
       document.padres.tel_en.value=document.padres.tel_m.value;
       document.padres.cel_e.value=document.padres.cel_m.value;
       document.padres.tel_t_e.value=document.padres.tel_t_m.value;
       document.padres.com_e.value=document.padres.cel_com_m.value;
       document.padres.email_e.value=document.padres.email_m.value;
       }
    if (qpaga=='P')
       {
       document.padres.enc.value=document.padres.padre.value;
       document.padres.par.value='Padre';
       document.padres.dir_e1.value=document.padres.dir2.value;
       document.padres.dir_e2.value=document.padres.dir4.value;
       document.padres.pue_e.value=document.padres.pueblo2.value;
       document.padres.esta_e.value=document.padres.est2.value;
       document.padres.zip_e.value=document.padres.zip2.value;
       document.padres.tel_en.value=document.padres.tel_p.value;
       document.padres.cel_e.value=document.padres.cel_p.value;
       document.padres.tel_t_e.value=document.padres.tel_t_p.value;
       document.padres.com_e.value=document.padres.cel_com_p.value;
       document.padres.email_e.value=document.padres.email_p.value;
       }
    if (qpaga=='E')
       {
       document.padres.enc.value='';
       document.padres.par.value='';
       document.padres.dir_e1.value='';
       document.padres.dir_e2.value='';
       document.padres.pue_e.value='';
       document.padres.esta_e.value='';
       document.padres.zip_e.value='';
       document.padres.tel_en.value='';
       document.padres.cel_e.value='';
       document.padres.tel_t_e.value='';
       document.padres.com_e.value='';
       document.padres.email_e.value='';
       }
}

function pasad() {
    document.padres.dir2.value=document.padres.dir1.value;
    document.padres.dir4.value=document.padres.dir3.value;
    document.padres.pueblo2.value=document.padres.pueblo1.value;
    document.padres.est2.value=document.padres.est1.value;
    document.padres.zip2.value=document.padres.zip1.value;
    document.padres.tel_p.value=document.padres.tel_m.value;
}

function borrard() {
    document.padres.dir2.value="";
    document.padres.dir4.value="";
    document.padres.pueblo2.value="";
    document.padres.est2.value="";
    document.padres.zip2.value="";
    document.padres.tel_p.value="";
}

function supports_input_placeholder()
{
    var i = document.createElement('input');
    return 'placeholder' in i;
}

if(!supports_input_placeholder()) {
    var fields = document.getElementsByTagName('INPUT');
    for(var i=0; i < fields.length; i++) {
      if(fields[i].hasAttribute('placeholder')) {
        fields[i].defaultValue = fields[i].getAttribute('placeholder');
        fields[i].onfocus = function() { if(this.value == this.defaultValue) this.value = ''; }
        fields[i].onblur = function() { if(this.value == '') this.value = this.defaultValue; }
      }
    }
  }
document.oncontextmenu = function(){return false}
</script>
<style type="text/css">
.style1 {
	background-color: #CCCCCC;
}
.style2 {
	background-color: #FFFFCC;
}
.style3 {
	text-align: center;
	font-size: medium;
	background-color: #CCCCCC;
}
.style4 {
	text-align: center;
	background-color: #CCCCCC;
}
.style5 {
	background-color: #FFFFCC;
	text-align: center;
}
.style6 {
	font-weight: bold;
}
</style>
<style type="text/css">

  input:required:invalid, input:focus:invalid {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeVJREFUeNqkU01oE1EQ/mazSTdRmqSxLVSJVKU9RYoHD8WfHr16kh5EFA8eSy6hXrwUPBSKZ6E9V1CU4tGf0DZWDEQrGkhprRDbCvlpavan3ezu+LLSUnADLZnHwHvzmJlvvpkhZkY7IqFNaTuAfPhhP/8Uo87SGSaDsP27hgYM/lUpy6lHdqsAtM+BPfvqKp3ufYKwcgmWCug6oKmrrG3PoaqngWjdd/922hOBs5C/jJA6x7AiUt8VYVUAVQXXShfIqCYRMZO8/N1N+B8H1sOUwivpSUSVCJ2MAjtVwBAIdv+AQkHQqbOgc+fBvorjyQENDcch16/BtkQdAlC4E6jrYHGgGU18Io3gmhzJuwub6/fQJYNi/YBpCifhbDaAPXFvCBVxXbvfbNGFeN8DkjogWAd8DljV3KRutcEAeHMN/HXZ4p9bhncJHCyhNx52R0Kv/XNuQvYBnM+CP7xddXL5KaJw0TMAF8qjnMvegeK/SLHubhpKDKIrJDlvXoMX3y9xcSMZyBQ+tpyk5hzsa2Ns7LGdfWdbL6fZvHn92d7dgROH/730YBLtiZmEdGPkFnhX4kxmjVe2xgPfCtrRd6GHRtEh9zsL8xVe+pwSzj+OtwvletZZ/wLeKD71L+ZeHHWZ/gowABkp7AwwnEjFAAAAAElFTkSuQmCC);
    background-position: right top;
    background-repeat: no-repeat;
    -moz-box-shadow: none;
  }
  input:required:valid {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAepJREFUeNrEk79PFEEUx9/uDDd7v/AAQQnEQokmJCRGwc7/QeM/YGVxsZJQYI/EhCChICYmUJigNBSGzobQaI5SaYRw6imne0d2D/bYmZ3dGd+YQKEHYiyc5GUyb3Y+77vfeWNpreFfhvXfAWAAJtbKi7dff1rWK9vPHx3mThP2Iaipk5EzTg8Qmru38H7izmkFHAF4WH1R52654PR0Oamzj2dKxYt/Bbg1OPZuY3d9aU82VGem/5LtnJscLxWzfzRxaWNqWJP0XUadIbSzu5DuvUJpzq7sfYBKsP1GJeLB+PWpt8cCXm4+2+zLXx4guKiLXWA2Nc5ChOuacMEPv20FkT+dIawyenVi5VcAbcigWzXLeNiDRCdwId0LFm5IUMBIBgrp8wOEsFlfeCGm23/zoBZWn9a4C314A1nCoM1OAVccuGyCkPs/P+pIdVIOkG9pIh6YlyqCrwhRKD3GygK9PUBImIQQxRi4b2O+JcCLg8+e8NZiLVEygwCrWpYF0jQJziYU/ho2TUuCPTn8hHcQNuZy1/94sAMOzQHDeqaij7Cd8Dt8CatGhX3iWxgtFW/m29pnUjR7TSQcRCIAVW1FSr6KAVYdi+5Pj8yunviYHq7f72po3Y9dbi7CxzDO1+duzCXH9cEPAQYAhJELY/AqBtwAAAAASUVORK5CYII=);
    background-position: right top;
    background-repeat: no-repeat;
  }

.style7 {
	border-right-style: solid;
	border-bottom-style: solid;
}

.style8 {
	background-color: #FFFFCC;
	text-align: right;
}

</style>
</head>

<body>
<?
include('../control.php');
$curso=$_POST[curso];

$consult1 = "ALTER TABLE `madre` ADD `ex_t_d_p` VARCHAR( 7 ) NOT NULL , ADD `ex_t_d_m` VARCHAR( 7 ) NOT NULL" ;
$resultad1 = mysql_query($consult1);

$consult1 = "select * from colegio where usuario='$usua'";
$resultad1 = mysql_query($consult1);
$row2=mysql_fetch_array($resultad1);

list($mes,$trozo) = explode("- ",$_POST['curso']);
//if(isset($_POST['bus2']) or isset($_POST['cta']))
if(isset($_POST['bus2']) and !empty($_POST['cta']) or isset($_POST['cta']) and !empty($_POST['cta']))
  {
  $trozo = $_POST['cta'];
  $consult17 = "select * from year where id='$trozo'";
  $resultad17 = mysql_query($consult17);
  $row27=mysql_fetch_array($resultad17);
  $curso=$row27[4].' '.$row27[3].'- '.$row27[5];
  $mes=$row27[4].' '.$row27[3];
  }

if(isset($_POST['grabar2'])){
$q="ALTER TABLE `year` ADD `padre` VARCHAR( 1 ) NOT NULL ,
ADD `nombre_padre` VARCHAR( 50 ) NOT NULL" ;
mysql_query($q);
$q="ALTER TABLE `year` ADD `colpro` VARCHAR( 40 ) NOT NULL ";
mysql_query($q);

$q="ALTER TABLE `year` ADD `cdb1` VARCHAR( 5 ) NOT NULL ,
ADD `cdb2` VARCHAR( 5 ) NOT NULL ,
ADD `cdb3` VARCHAR( 5 ) NOT NULL ";
mysql_query($q);

$q="ALTER TABLE `year` ADD `pop` VARCHAR( 1 ) NOT NULL ";
mysql_query($q);

$q="ALTER TABLE `year` ADD `dir1` VARCHAR( 50 ) NOT NULL ,
ADD `dir2` VARCHAR( 50 ) NOT NULL ,
ADD `pueblo` VARCHAR( 15 ) NOT NULL ,
ADD `estado` VARCHAR( 4 ) NOT NULL ,
ADD `zip` VARCHAR( 10 ) NOT NULL " ;
mysql_query($q);

$q="ALTER TABLE `year` ADD `celp` VARCHAR( 13 ) NOT NULL ,
ADD `emailp` VARCHAR( 70 ) NOT NULL ,
ADD `telp` VARCHAR( 13 ) NOT NULL " ;
mysql_query($q);

$aa="ALTER TABLE `year` ADD `raza` INT(2) NOT NULL, ADD `rel` INT(2) NOT NULL AFTER `raza`";
mysql_query($aa);

$aa="ALTER TABLE `year` ADD `transporte` INT(2) NOT NULL AFTER `avisar`, ADD `municipio` VARCHAR(25) NOT NULL AFTER `transporte`";
mysql_query($aa);

$aa="ALTER TABLE `year` ADD `acomodo` VARCHAR(2) NOT NULL AFTER `municipio`, ADD `trajo` VARCHAR(2) NOT NULL AFTER `acomodo`";
mysql_query($aa);
$aa="ALTER TABLE `year` ADD `emaile` VARCHAR(70) NOT NULL AFTER `trajo`";

mysql_query($aa);

$q="ALTER TABLE `madre` ADD `codigop` VARCHAR( 20 ) NOT NULL ,
ADD `codigom` VARCHAR( 20 ) NOT NULL" ;
mysql_query($q);

$consult1="ALTER TABLE `madre` ADD `per5` VARCHAR(60) NOT NULL AFTER `total_pagar`, ADD `per6` VARCHAR(60) NOT NULL AFTER `per5`, ADD `rel5` VARCHAR(30) NOT NULL AFTER `per6`, ADD `rel6` VARCHAR(30) NOT NULL AFTER `rel5`, ADD `tec5` VARCHAR(13) NOT NULL AFTER `rel6`, ADD `tec6` VARCHAR(13) NOT NULL AFTER `tec5`, ADD `tet5` VARCHAR(13) NOT NULL AFTER `tec6`, ADD `tet6` VARCHAR(13) NOT NULL AFTER `tet5`, ADD `cel5` VARCHAR(13) NOT NULL AFTER `tet6`, ADD `cel6` VARCHAR(13) NOT NULL AFTER `cel5`";
$resultad1 = mysql_query($consult1);

$q = "update pagos set grado='".$_POST['grado']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
mysql_query($q, $db) or die ("problema con query 11");

$q = "update year set municipio='".$_POST['municipio']."', transporte='".$_POST['transporte']."', dir1='".$_POST['dir1']."',dir2='".$_POST['dir2']."', pueblo='".$_POST['pue']."', estado='".$_POST['pais']."', zip='".$_POST['zip']."',telp='".$_POST['telp1']."',celp='".$_POST['telp2']."',emailp='".$_POST['emailp']."',raza='".$_POST['raza']."',rel='".$_POST['rel']."',
padre='".$_POST['pad1']."',nombre_padre='".$_POST['pad2']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
mysql_query($q, $db) or die ("problema con query 12");

$q = "update year set pop='".$_POST['pop']."',cdb1='".$_POST['cdb1']."', cdb2='".$_POST['cdb2']."', cdb3='".$_POST['cdb3']."', colpro='".$_POST['colpro']."',nombre='".$_POST['nombre']."',apellidos='".mysql_real_escape_string($_POST['apellidos'])."',grado='".$_POST['grado']."',desc_men='".$_POST['men']."',desc_mat='".$_POST['mat']."', comp='".$_POST['comp']."', nuref='".$_POST['nuref']."', lugar_nac='".$_POST['lunac']."', nuevo='".$_POST['nuevo1']."',
desc_otro1='".$_POST['otro1']."',desc_otro2='".$_POST['otro2']."',vivecon='".$_POST['vive']."',id='".$_POST['id']."',fecha='".$_POST['fec1']."', cel='".$_POST['cel']."', genero='".$_POST['genero']."', desc1='".$_POST['des1']."', desc2='".$_POST['des2']."', desc3='".$_POST['des3']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
mysql_query($q, $db) or die ("problema con query 2");

$q = "update year set medico='".$_POST['medico']."',tel1='".$_POST['tel1']."',tel2='".$_POST['tel2']."',imp1='".$_POST['imp1']."',imp2='".$_POST['imp2']."', imp3='".$_POST['imp3']."', imp4='".$_POST['imp4']."', enf1='".$_POST['enf1']."',
rec1='".$_POST['rec1']."',rec2='".$_POST['rec2']."',rec3='".$_POST['rec3']."',rec4='".$_POST['rec4']."',religion='".$_POST['religion']."',iglesia='".$_POST['iglesia']."',
bau='".$_POST['bau']."',com='".$_POST['com']."',con='".$_POST['con']."',fbau='".$_POST['fbau']."',fcom='".$_POST['fcom']."',fcon='".$_POST['fcon']."',fecha_matri='".$_POST['fec2']."',
enf2='".$_POST['enf2']."',enf3='".$_POST['enf3']."',enf4='".$_POST['enf4']."',med1='".$_POST['med1']."',med2='".$_POST['med2']."', med3='".$_POST['med3']."', med4='".$_POST['med4']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
mysql_query($q, $db) or die ("problema con query 3");

$q = "update year set emaile='".$_POST['emaile']."', acomodo='".$_POST['acomodo']."', trajo='".$_POST['trajo']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
mysql_query($q, $db) or die ("problema con query 22");

}

if(isset($_POST['grabar'])){
  IF (empty($_POST['clave']))
     {
     echo "<center><br>No ha introducido clave secreta para la cuenta. Por favor vuelve e inténtalo de nuevo.<br>La clave NO puede estar en blanco.<br><br>";
     echo "<a href='javascript:history.back()'>";
     echo "<img border='0' id='img1' src='../../images/button12.gif' height='26' width='200' alt='Pagina Anterior' onmouseover='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button4.gif')' onmouseout='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button12.gif')' onmousedown='FP_swapImg(1,0,/*id*/'img1',/*url*/'../../images/button11.gif')' onmouseup='FP_swapImg(0,0,/*id*/'img1',/*url*/'../../images/button4.gif')' fp-style='fp-btn: Embossed Capsule 6; fp-font-style: Bold; fp-font-size: 12; fp-transparent: 1; fp-proportional: 0' fp-title='Pagina Anterior'></a></p></center>";
     exit;
     }
$padre=$_POST['padre'];
$q = "update madre set ex_t_d_m='".$_POST['exm']."',ex_t_d_p='".$_POST['exp']."', madre='".mysql_real_escape_string($_POST['madre'])."',dir1='".$_POST['dir1']."',dir2='".$_POST['dir2']."',pueblo1='".$_POST['pueblo1']."',tel_e='".$_POST['tel_e']."',sueldom='".$_POST['sueldom']."',
est1='".$_POST['est1']."',zip1='".$_POST['zip1']."',dir3='".$_POST['dir3']."',dir4='".$_POST['dir4']."',pueblo2='".$_POST['pueblo2']."',est2='".$_POST['est2']."',tel_t_p='".$_POST['tel_t_p']."',usuario='".$_POST['usuario']."',
zip2='".$_POST['zip2']."',padre='".mysql_real_escape_string($padre)."',tel_m='".$_POST['tel_m']."',tel_p='".$_POST['tel_p']."',email_m='".$_POST['email_m']."',tel_t_m='".$_POST['tel_t_m']."',tel_e2='".$_POST['tel_e2']."',
email_p='".$_POST['email_p']."',ex_m='".$_POST['ex_m']."',cel_m='".$_POST['cel_m']."',cel_p='".$_POST['cel_p']."',clave='".$_POST['clave']."',ex_p='".$_POST['ex_p']."',sueldop='".$_POST['sueldop']."',
cel_com_m='".$_POST['cel_com_m']."',cel_com_p='".$_POST['cel_com_p']."',trabajo_m='".mysql_real_escape_string($_POST['trabajo_m'])."',trabajo_p='".mysql_real_escape_string($_POST['trabajo_p'])."',posicion_m='".$_POST['posicion_m']."',nfam='".$_POST['nf']."',
posicion_p='".$_POST['posicion_p']."',re_e_p='".$_POST['re_e_p']."',re_e_m='".$_POST['re_e_m']."',re_mc_p='".$_POST['re_mc_p']."',re_mc_m='".$_POST['re_mc_m']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 22");

$q = "update madre set qpaga='".$_POST['qpaga']."', per1='".$_POST['per1']."', per2='".$_POST['per2']."', per3='".$_POST['per3']."', per4='".$_POST['per4']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 23");

$q = "update madre set encargado='".mysql_real_escape_string($_POST['enc'])."', parentesco='".$_POST['par']."', tel_en='".$_POST['tel_en']."', cel_e='".$_POST['cel_e']."', com_e='".$_POST['com_e']."', tel_t_e='".$_POST['tel_t_e']."'  where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 24");

$q = "update madre set codigom='".$_POST['com']."', codigop='".$_POST['cop']."', dir_e1='".$_POST['dir_e1']."', dir_e2='".$_POST['dir_e2']."', pue_e='".$_POST['pue_e']."', esta_e='".$_POST['esta_e']."', zip_e='".$_POST['zip_e']."', email_e='".$_POST['email_e']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 25");

$q = "update madre set rel1='".$_POST['rel1']."', rel2='".$_POST['rel2']."', rel3='".$_POST['rel3']."', rel4='".$_POST['rel4']."', tec1='".$_POST['tec1']."', tec2='".$_POST['tec2']."', tec3='".$_POST['tec3']."', tec4='".$_POST['tec4']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 26");

$q = "update madre set tet1='".$_POST['tet1']."', tet2='".$_POST['tet2']."', tet3='".$_POST['tet3']."', tet4='".$_POST['tet4']."', cel1='".$_POST['cel1']."', cel2='".$_POST['cel2']."', cel3='".$_POST['cel3']."', cel4='".$_POST['cel4']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 27");

$q = "update madre set per5='".$_POST['per5']."', per6='".$_POST['per6']."', rel5='".$_POST['rel5']."', rel6='".$_POST['rel6']."', tec5='".$_POST['tec5']."', tec6='".$_POST['tec6']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 26");

$q = "update madre set tet5='".$_POST['tet5']."',  tet6='".$_POST['tet6']."', cel5='".$_POST['cel5']."', cel6='".$_POST['cel6']."' where id='".$_POST['id']."' and usuario='".$_POST['usuario2']."'" ;
mysql_query($q, $db) or die ("problema con query 27");

}

if(isset($_POST['crear']))
  {
  $query = "insert into madre (usuario, madre, padre, id, grupo, clave, activo, year)
  values 
  ('".$_POST['id']."','".$_POST['madre']."','".$_POST['padre']."','".$_POST['id']."','Padres','".$_POST['clave']."','Activo','$row2[116]')";
  $result = mysql_query($query);
  $trozo=$_POST['id'];

$q = "update madre set madre='".$_POST['madre']."',dir1='".$_POST['dir1']."',dir2='".$_POST['dir2']."',pueblo1='".$_POST['pueblo1']."',tel_e='".$_POST['tel_e']."',sueldom='".$_POST['sueldom']."',
est1='".$_POST['est1']."',zip1='".$_POST['zip1']."',dir3='".$_POST['dir3']."',dir4='".$_POST['dir4']."',pueblo2='".$_POST['pueblo2']."',est2='".$_POST['est2']."',tel_t_p='".$_POST['tel_t_p']."',usuario='".$_POST['usuario']."',
zip2='".$_POST['zip2']."',padre='".mysql_real_escape_string($_POST['padre'])."',tel_m='".$_POST['tel_m']."',tel_p='".$_POST['tel_p']."',email_m='".$_POST['email_m']."',tel_t_m='".$_POST['tel_t_m']."',tel_e2='".$_POST['tel_e2']."',
email_p='".$_POST['email_p']."',ex_m='".$_POST['ex_m']."',cel_m='".$_POST['cel_m']."',cel_p='".$_POST['cel_p']."',clave='".$_POST['clave']."',ex_p='".$_POST['ex_p']."',sueldop='".$_POST['sueldop']."',
cel_com_m='".$_POST['cel_com_m']."',cel_com_p='".$_POST['cel_com_p']."',trabajo_m='".$_POST['trabajo_m']."',trabajo_p='".$_POST['trabajo_p']."',posicion_m='".$_POST['posicion_m']."',
posicion_p='".$_POST['posicion_p']."',re_e_p='".$_POST['re_e_p']."',re_e_m='".$_POST['re_e_m']."',re_mc_p='".$_POST['re_mc_p']."',re_mc_m='".$_POST['re_mc_m']."' where id='".$_POST['id']."'";
$result = mysql_query($q);

$q = "update madre set qpaga='".$_POST['qpaga']."', per1='".$_POST['per1']."', per2='".$_POST['per2']."', per3='".$_POST['per3']."', per4='".$_POST['per4']."' where id='".$_POST['id']."'" ;
mysql_query($q, $db) or die ("problema con query 23");

$q = "update madre set encargado='".mysql_real_escape_string($_POST['enc'])."', parentesco='".$_POST['par']."', tel_en='".$_POST['tel_en']."', cel_e='".$_POST['cel_e']."', com_e='".$_POST['com_e']."', tel_t_e='".$_POST['tel_t_e']."'  where id='".$_POST['id']."'" ;
mysql_query($q, $db) or die ("problema con query 24");

$q = "update madre set dir_e1='".$_POST['dir_e1']."', dir_e2='".$_POST['dir_e2']."', pue_e='".$_POST['pue_e']."', esta_e='".$_POST['esta_e']."', zip_e='".$_POST['zip_e']."', email_e='".$_POST['amail_e']."' where id='".$_POST['id']."'" ;
mysql_query($q, $db) or die ("problema con query 25");

$q = "update madre set rel1='".$_POST['rel1']."', rel2='".$_POST['rel2']."', rel3='".$_POST['rel3']."', rel4='".$_POST['rel4']."', tec1='".$_POST['tec1']."', tec2='".$_POST['tec2']."', tec3='".$_POST['tec3']."', tec4='".$_POST['tec4']."' where id='".$_POST['id']."'" ;
mysql_query($q, $db) or die ("problema con query 26");

$q = "update madre set tet1='".$_POST['tet1']."', tet2='".$_POST['tet2']."', tet3='".$_POST['tet3']."', tet4='".$_POST['tet4']."', cel1='".$_POST['cel1']."', cel2='".$_POST['cel2']."', cel3='".$_POST['cel3']."', cel4='".$_POST['cel4']."' where id='".$_POST['id']."'" ;
mysql_query($q, $db) or die ("problema con query 27");

  }
 
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

if(isset($_POST['grabar3'])){
  $query = "insert into year (year, ss, nombre, id, genero, lugar_nac, nuref, desc1, desc2, desc3)
  values 
  ('$row2[116]','".$_POST['ss']."','".$_POST['nombre']."','".$_POST['id7']."','".$_POST['genero']."','".$_POST['lunac']."','".$_POST['nuref']."','".$_POST['des1']."','".$_POST['des2']."','".$_POST['des3']."')";
  $result = mysql_query($query);
  $q = "update year set nombre='".$_POST['nombre']."',apellidos='".$_POST['apellidos']."',grado='".$_POST['grado']."',desc_men='".$_POST['men']."',desc_mat='".$_POST['mat']."', comp='".$_POST['comp']."', nuevo='".$_POST['nuevo1']."',raza='".$_POST['raza']."',rel='".$_POST['rel']."',
  desc_otro1='".$_POST['otro1']."',desc_otro2='".$_POST['otro2']."',vivecon='".$_POST['vive']."',fecha='".$_POST['fec1']."', cel='".$_POST['cel']."' where ss = '".$_POST['ss']."' AND year='$row2[116]'";
  mysql_query($q, $db) or die ("problema con query 3");
  $trozo=$_POST['id7'];
  $curso = $_POST['apellidos'].' '.$_POST['nombre'].'- '.$_POST['id7'];

}
if(isset($_POST['grabar3'])){$trozo=$_POST['id7'];}
if(isset($_POST['grabar2'])){$trozo=$_POST['id'];}
if(isset($_POST['cancel'])){$trozo=$_POST['id'];}

$consulta = "select * from madre where id = '$trozo'";

if(isset($_POST['grabar']) or isset($_POST['crear'])){
  $consulta = "select * from madre where id = '".$_POST['id']."'";
  }

$sSQL2="Select * From year where year='$row2[116]' and activo='' Order By apellidos";
$result2=mysql_query($sSQL2);



$sSQL2="Select * From year where year='$row2[116]' and activo='' Order By apellidos";
$result22=mysql_query($sSQL2);
$sSQL2="Select * From year where year='$row2[116]' and activo='' and genero='1' OR  year='$row2[116]' and activo='' and genero='F' Order By apellidos";
$result23=mysql_query($sSQL2);
$sSQL2="Select * From year where year='$row2[116]' and activo='' and genero='2' OR  year='$row2[116]' and activo='' and genero='M' Order By apellidos";
$result24=mysql_query($sSQL2);
$sSQL2="Select DISTINCT id From year where year='$row2[116]' and activo='' Order By apellidos";
$result25=mysql_query($sSQL2);

$can_est = mysql_num_rows($result22);
$can_est_f = mysql_num_rows($result23);
$can_est_m = mysql_num_rows($result24);
$can_fam = mysql_num_rows($result25);

if(isset($_POST['nueva'])){
  $consulta1 = "SELECT * FROM madre ORDER BY id DESC LIMIT 1";
  $resultado1 = mysql_query($consulta1);
  $regn = mysql_fetch_array($resultado1);
  $regn2 = $regn[0] + 1;
  $consulta = "select * from madre where id = 'amc'";
  }

$resultado = mysql_query($consulta);

$reg = mysql_fetch_array($resultado);

?>
<form method="POST" action="padres.php" target="_self">

<div style="position: absolute; width: 178px; height: 97px; z-index: 1; left: 14px; top: 18px" id="layer1">
	<table cellspacing="0">
		<tr>
			<td class="style1"><strong>Total Estudiantes:</strong></td>
			<td class="style8" style="width: 50"><? echo $can_est; ?></td>
		</tr>
		<tr>
			<td class="style1"><strong>Femeninas:</strong></td>
			<td class="style8"><? echo $can_est_f; ?></td>
		</tr>
		<tr>
			<td class="style1"><strong>Masculinos:</strong></td>
			<td class="style8"><? echo $can_est_m; ?></td>
		</tr>
		<tr>
			<td class="style1"><strong>Total Familias:</strong></td>
			<td class="style8"><? echo $can_fam; ?></td>
		</tr>
		<tr>
			<td class="style1"><strong>Año Escolar:</strong></td>
			<td class="style8"><? echo $row2[116];?></td>
		</tr>
	</table>
	</div>

<table align="center" cellpadding="2" cellspacing="0" class="style7">
	<tr>
		<td class="style1"><strong>Selección del estudiante:</strong></td>
		<td class="style2">
		<select name="curso" size="1">
<?
			echo '<option value="'.$_POST['curso'].'">'.$_POST['curso'].'</option>';

             while ($row=mysql_fetch_array($result2))
             {echo '<option>'.$row["apellidos"].' '.$row["nombre"].'- '.$row["id"];}
?>
         </td>
		<td class="style5">
			<strong>
			<input name="bus" type="submit" value="Buscar" class="myButton" style="width: 120px; height: 28px;"></strong></td>
	</tr>
	<tr>
		<td class="style1">&nbsp;</td>
		<td class="style2">
		<input maxlength="10" name="cta" size="10" type="text"></td>
		<td class="style5">
			<strong>
			<input name="bus2" type="submit" value="Buscar" class="myButton" style="width: 120px; height: 28px;"></strong></td>
	</tr>
	<tr>
		<td class="style1"><strong>Añadir cuenta nueva:</strong></td>
		<td class="style2">
		&nbsp;</td>
		<td class="style5">
<? 
IF ($r7=='A'){
?>
			<strong>
			<input class="myButton" name="nueva" style="width: 119px; height: 28px;" type="submit" value="Nueva CTA."></strong></td>
<? 
  }
?>

	</tr>
	</table>
</form>
<p align="center"><b><font size="5">Información de los padres</font></b></p>
<form name="padres" method="POST" action="padres.php" target="_self">

<table border="0" cellpadding="2" cellspacing="0" align="center" class="style7">
	<tr>
		<td colspan="2" bgcolor="#CCCCCC" align="center"><b><font size="4">
		Información de la Madre</font></b></td>
		<td width="48%" colspan="2" bgcolor="#CCCCCC" align="center"><b>
		<font size="4">Información del Padre</font></b></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>Nro. Cta.</strong></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input name=id maxlength=7 size=8 tabindex='1' value='".$regn2.$reg[0]."' required>";?>		
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>Usuario y Clave.</strong></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input name=usuario maxlength=20 size=20 tabindex='1' value='$reg[22]' required>"?>		
		&nbsp;&nbsp;&nbsp;
<?	echo "<input name=clave maxlength=20 size=20 tabindex='1' value='$reg[23]' required>"?>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Nombre</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<input type=text name='madre' maxlength=35 size=35 value="<? echo $reg[1];?>"/></td>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Nombre</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<input type=text name=padre maxlength=35 size=35 tabindex='1' value="<? echo $reg[2];?>"/></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>Referencia</strong></td>
		<td width="35%" bgcolor="#FFFFCC">
<input type=text name='com' maxlength=20 size=20 value="<? echo $reg[84];?>"/></td>
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>Referencia</strong></td>
		<td width="34%" bgcolor="#FFFFCC">
<input type=text name='cop' maxlength=20 size=20 value="<? echo $reg[83];?>"/></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Teléfono</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-4' type=text name=tel_m maxlength=13 size=14 value='$reg[13]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Teléfono</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-5' type=text name=tel_p maxlength=13 size=14 tabindex='1' value='$reg[14]'></td>"?>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Celular los 10 números.</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=cel_m maxlength=10 size=10 value='$reg[28]' placeholder='9999999999' pattern='[0-9]{10}'>"?>
		&nbsp;
<?  echo "<select name='cel_com_m' size='1' tabindex='1' value='$reg[26]'>"?>
<?  echo "<option value = $reg[26]>$reg[26]</option>"?>
<?  echo "  <option>Ninguno</option>"?>
<?  echo "  <option>T-Movil</option>"?>
<?  echo "  <option>AT&T</option>"?>
<?  echo " 	<option>Sprint</option>"?>
<?  echo " 	<option>Open M.</option>"?>
<?  echo "	<option>Movilstar</option>"?>
<?  echo "	<option>Claro</option>"?>
<?  echo "	<option>Suncom</option>"?>
<?  echo "	<option>Verizon</option>"?>
<?  echo "	<option>Boost</option>"?>
      </select> Ejem. 7871234567</td>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Celular los 10 números. </b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=cel_p maxlength=10 size=10 tabindex='1' value='$reg[29]' placeholder='9999999999' pattern='[0-9]{10}'>"?>
		&nbsp;
<?  echo "<select name='cel_com_p' size='1' tabindex='1' value='$reg[27]'>"?>
<?  echo "<option value = $reg[27]>$reg[27]</option>"?>
<?  echo "  <option>Ninguno</option>"?>
<?  echo "  <option>T-Movil</option>"?>
<?  echo "  <option>AT&T</option>"?>
<?  echo " 	<option>Sprint</option>"?>
<?  echo " 	<option>Open M.</option>"?>
<?  echo "	<option>Movilstar</option>"?>
<?  echo "	<option>Claro</option>"?>
<?  echo "	<option>Suncom</option>"?>
<?  echo "	<option>Verizon</option>"?>
<?  echo "	<option>Boost</option>"?>
      </select> Ejem. 7871234567</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="height: 23px; width: 14%;"><b>Exalumno</b></td>
		<td width="35%" bgcolor="#FFFFCC" style="height: 23px">
<?	echo "<select name='ex_m' size='1' value='$reg[20]'>"?>
<?  echo "<option value = $reg[20]>$reg[20]</option>"?>
<?	echo "<option>NO</option>"?>
<?	echo "<option>SI</option>"?>

      </select><td bgcolor="#FFFFCC" style="height: 23px; width: 14%;"><b>
		Exalumno</b></td>
		<td width="34%" bgcolor="#FFFFCC" style="height: 23px">
<?	echo "<select name='ex_p' size='1' value='$reg[21]'>"?>
<?  echo "<option value = $reg[21]>$reg[21]</option>"?>
<?	echo "<option>NO</option>"?>
<?	echo "<option>SI</option>"?>
      </select></tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Dirección Postal</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=dir1 maxlength=40 size=40 value='$reg[3]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Dirección Postal</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=dir2 maxlength=40 size=40 tabindex='1' value='$reg[4]'></td>"?>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%">&nbsp;</td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=dir3 maxlength=40 size=40 value='$reg[8]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>
		<input class="style6" name="pasa" onclick="return pasad(); return true" style="width: 124px" type="button" value="Pasar dirección"></strong></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=dir4 maxlength=40 size=40 tabindex='1' value='$reg[9]'></td>"?>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%">&nbsp;</td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=pueblo1 maxlength=13 size=13 value='$reg[5]'>"?>
		&nbsp; &nbsp;&nbsp;
<?	echo "<input type=text name=est1 maxlength=4 size=4 value='$reg[6]'>"?>&nbsp;&nbsp; &nbsp;
<?	echo "<input type=text name=zip1 maxlength=7 size=7 value='$reg[7]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><strong>
		<input class="style6" name="borr" onclick="return borrard(); return true" style="width: 124px" type="button" value="Borrar"></strong></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=pueblo2 maxlength=13 size=13 tabindex='1' value='$reg[10]'>"?>
		&nbsp;&nbsp; &nbsp;
<?	echo "<input type=text name=est2 maxlength=4 size=4 tabindex='1' value='$reg[11]'>"?>
		&nbsp; &nbsp;&nbsp;
<?	echo "<input type=text name=zip2 maxlength=7 size=7 tabindex='1' value='$reg[12]'></td>"?>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>E-Mail</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type='email' name=email_m maxlength=40 size=40 value='$reg[18]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>E-Mail</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type='email' name=email_p maxlength=40 size=40 value='$reg[19]'></td>"?>
	</tr>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Tel. Emergencia</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-6' name=tel_e maxlength=14 size=13 value='$reg[17]'>"?>
		<td bgcolor="#FFFFCC" style="width: 14%">
		<b>Tel. Emergencia</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-7' name=tel_e2 maxlength=14 size=13 tabindex='1' value='$reg[43]' >"?></tr>
	<tr>
		<td width="91%" colspan="4" bgcolor="#CCCCCC">
		<p align="center"><b><font size="4">Información del Trabajo</font></b></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Trabajo</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<input type=text name=trabajo_m maxlength=25 size=25 value="<? echo $reg[30];?>"/>&nbsp;&nbsp;&nbsp;&nbsp; </td>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Trabajo</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<input type=text name=trabajo_p maxlength=25 size=25 tabindex='1' value="<? echo $reg[31];?>"/></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Posición</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=posicion_m maxlength=25 size=25 value='$reg[32]'></td>"?>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Posición</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input type=text name=posicion_p maxlength=25 size=25 tabindex='1' value='$reg[33]'></td>"?>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Teléfono</b></td>
		<td width="35%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-8' type=text name=tel_t_m maxlength=14 size=13 value='$reg[15]'>"?>
		&nbsp; &nbsp;&nbsp; 
		<strong>Ext.</strong>
		<input maxlength="7" name="exm" size="7" type="text" value="<? echo $reg[79];?>"><td bgcolor="#FFFFCC" style="width: 14%"><b>
		Teléfono</b></td>
		<td width="34%" bgcolor="#FFFFCC">
<?	echo "<input id='ex-9' type=text name=tel_t_p maxlength=14 size=13 tabindex='1' value='$reg[16]'>"?>
		&nbsp; <strong>&nbsp;Ext. </strong>
		<input maxlength="7" name="exp" size="7" type="text" value="<? echo $reg[78];?>"></tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%">&nbsp;</td>
		<td width="35%" bgcolor="#FFFFCC">
		<strong>
		Sueldo&nbsp;<?	echo "<input type=text name=sueldom maxlength=11 size=11 value='$reg[41]'>"?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
		Num. Fam.&nbsp;<?	echo "<input type=text name=nf maxlength=2 size=2 value='$reg[44]'></td>"?></strong>
		<td bgcolor="#FFFFCC" style="width: 14%">&nbsp;</td>
		<td width="34%" bgcolor="#FFFFCC">
		<strong>Sueldo</strong>&nbsp;
<?	echo "<input type=text name=sueldop maxlength=11 size=11 tabindex='1' value='$reg[40]'></td>"?>
		</tr>
	<tr>
		<td width="91%" colspan="4" bgcolor="#CCCCCC">
		<p align="center"><b><font size="4">Opciones de Mensajes </font></b></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Recibir E-Mail</b></td>
        <td width='35%' bgcolor='#FFFFCC'>
<?	echo "<select name='re_e_m' size='1' value='$reg[34]'>"?>
<?  echo "<option value = $reg[34]>$reg[34]</option>"?>
<?	echo "<option>NO</option>"?>
<?	echo "<option>SI</option>"?>
      </select></td>
		<td bgcolor="#FFFFCC" style="width: 14%"><b>Recibir E-Mail</b></td>
		<td width="36%" bgcolor="#FFFFCC">
<?	echo "<select name=re_e_p size='1' tabindex='1' value='$reg[35]'>"?>
<?  echo "<option value = $reg[35]>$reg[35]</option>"?>
<?	echo "<option value ='NO'>NO</option>"?>
<?	echo "<option value ='SI'>SI</option>"?>

      </select></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFCC" style="height: 23px; width: 14%;"><b>Recibir SMS</b></td>
        <td width='35%' bgcolor='#FFFFCC' style="height: 23px">
<?	echo "<select name='re_mc_m' size='1' value='$reg[36]'>"?>
<?  echo "<option value = $reg[36]>$reg[36]</option>"?>
<?	echo "<option>NO</option>"?>
<?	echo "<option>SI</option>"?>
      </select></td>
		<td bgcolor="#FFFFCC" style="width: 14%; height: 23px;"><b>Recibir SMS</b></td>
		<td width="36%" bgcolor="#FFFFCC" style="height: 23px">
<?	echo "<select name='re_mc_p' size='1' tabindex='1' value='$reg[37]'>"?>
<?  echo "<option value = $reg[37]>$reg[37]</option>"?>
<?	echo "<option>NO</option>"?>
<?	echo "<option>SI</option>"?>
      </select></td>
	</tr>
	<tr>
		<td width="91%" colspan="4" bgcolor="#CCCCCC">
		<p align="center"><font size="4"><b>Esta opción de SMS se aplica a su 
		contrato o tarifa de mensajes de textos recibidos a su celular</b>.</font></td>
	</tr>

	<tr>
		<td width="91%" colspan="4" class="style2">
		&nbsp;</td>
	</tr>

	<tr>
		<td width="91%" colspan="4" class="style4">
		<strong>Personas Autorizadas a Recoger</strong></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per1" size="60" style="width: 350px" type="text" value="<? echo $reg[46]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per2" size="60" style="width: 350px" type="text" value="<? echo $reg[47]; ?>" tabindex="3"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel1" size="30" type="text" value="<? echo $reg[61]; ?>" tabindex="2">&nbsp;
			<strong>Cel:</strong>
			<input name="cel1" size="13" type="text" value="<? echo $reg[73]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel2" size="30" type="text" value="<? echo $reg[62]; ?>" tabindex="3">&nbsp;
			<strong>Cel:</strong>
			<input name="cel2" size="13" type="text" value="<? echo $reg[74]; ?>" tabindex="3"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec1" size="13" type="text" value="<? echo $reg[65]; ?>" tabindex="2">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet1" size="13" type="text" value="<? echo $reg[69]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec2" size="13" type="text" value="<? echo $reg[66]; ?>" tabindex="3">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet2" size="13" type="text" value="<? echo $reg[70]; ?>" tabindex="3"></td>
	</tr>



	<tr>
		<td width="91%" colspan="4" class="style4">
		<strong>Personas Autorizadas a Recoger</strong></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per5" size="60" style="width: 350px" type="text" value="<? echo $reg[93]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per6" size="60" style="width: 350px" type="text" value="<? echo $reg[94]; ?>" tabindex="3"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel5" size="30" type="text" value="<? echo $reg[95]; ?>" tabindex="2">&nbsp;
			<strong>Cel:</strong>
			<input name="cel5" size="13" type="text" value="<? echo $reg[101]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel6" size="30" type="text" value="<? echo $reg[96]; ?>" tabindex="3">&nbsp;
			<strong>Cel:</strong>
			<input name="cel6" size="13" type="text" value="<? echo $reg[102]; ?>" tabindex="3"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec5" size="13" type="text" value="<? echo $reg[97]; ?>" tabindex="2">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet5" size="13" type="text" value="<? echo $reg[99]; ?>" tabindex="2"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec6" size="13" type="text" value="<? echo $reg[98]; ?>" tabindex="3">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet6" size="13" type="text" value="<? echo $reg[100]; ?>" tabindex="3"></td>
	</tr>




	<tr>
		<td width="91%" colspan="4" class="style4">
		<strong>Información de Emergencia</strong></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per3" size="60" style="width: 350px" type="text" value="<? echo $reg[48]; ?>" tabindex="4"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong>
			<input maxlength="60" name="per4" size="60" style="width: 350px" type="text" value="<? echo $reg[49]; ?>" tabindex="5"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel3" size="30" type="text" value="<? echo $reg[63]; ?>" tabindex="4">&nbsp;
			<strong>Cel:</strong>
			<input name="cel3" size="13" type="text" value="<? echo $reg[75]; ?>" tabindex="4"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong>
			<input name="rel4" size="30" type="text" value="<? echo $reg[64]; ?>" tabindex="5">&nbsp;
			<strong>Cel:</strong>
			<input name="cel4" size="13" type="text" value="<? echo $reg[76]; ?>" tabindex="5"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec3" size="13" type="text" value="<? echo $reg[67]; ?>" tabindex="4" style="height: 22px">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet3" size="13" type="text" value="<? echo $reg[71]; ?>" tabindex="4"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono Casa:</strong>
			<input name="tec4" size="13" type="text" value="<? echo $reg[68]; ?>" tabindex="5">&nbsp;&nbsp;&nbsp;&nbsp;
			<strong>Tel. Trabajo:</strong>
			<input name="tet4" size="13" type="text" value="<? echo $reg[72]; ?>" tabindex="5"></td>
	</tr>

	<tr>
		<td width="91%" colspan="4" class="style4">
		<strong>Persona Responsable a Pagar</strong></td>
	</tr>
<?
if ($reg[45]=='M') {$p1="selected=''";}
if ($reg[45]=='P') {$p2="selected=''";}
if ($reg[45]=='E') {$p3="selected=''";}
?>		

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Quién Paga:</strong> 
			<select name="qpaga" style="width: 102px" tabindex="6" onclick="return pasapago(); return true">
		<option></option>
		<option value="M" <? echo $p1; ?> >Madre</option>
		<option value="P" <? echo $p2; ?> >Padre</option>
		<option value="E" <? echo $p3; ?> >Encargado</option>
		</select></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Teléfono: </strong>
			<input name="tel_en" size="13" type="text" value="<? echo $reg[52]; ?>" tabindex="7">&nbsp;&nbsp;
			<strong>Tel. Trabajo: </strong>
			<input name="tel_t_e" size="13" type="text" value="<? echo $reg[60]; ?>" tabindex="7"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Nombre:</strong></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Celular:&nbsp; </strong>
			<input name="cel_e" size="10" type="text" value="<? echo $reg[53]; ?>" tabindex="7">&nbsp;&nbsp;&nbsp; 
			
<?  echo "<select name='com_e' size='1' tabindex='7' value='$reg[54]'>"?>
<?  echo "<option value = $reg[54]>$reg[54]</option>"?>
<?  echo "  <option>Ninguno</option>"?>
<?  echo "  <option>T-Movil</option>"?>
<?  echo "  <option>AT&T</option>"?>
<?  echo " 	<option>Sprint</option>"?>
<?  echo " 	<option>Open M.</option>"?>
<?  echo "	<option>Movilstar</option>"?>
<?  echo "	<option>Claro</option>"?>
<?  echo "	<option>Suncom</option>"?>
<?  echo "	<option>Verizon</option>"?>
<?  echo "	<option>Boost</option></select>"?>
        Ejem. 7871234567
			</td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
			<input name="enc" size="50" type="text" value="<? echo $reg[50]; ?>" tabindex="6"></td>
		<td width="91%" colspan="2" class="style2">
		<strong>Dirección Postal:</strong></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>Parentesco:</strong></td>
		<td width="91%" colspan="2" class="style2">
			<input name="dir_e1" size="50" type="text" value="<? echo $reg[55]; ?>" tabindex="7"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
			<input name="par" size="30" type="text" value="<? echo $reg[51]; ?>" tabindex="6"></td>
		<td width="91%" colspan="2" class="style2">
			<input name="dir_e2" size="50" style="height: 22px" type="text" value="<? echo $reg[56]; ?>" tabindex="7"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		<strong>E-Mail: </strong>
			<input maxlength="60" name="email_e" size="55" tabindex="6" type="text" value="<? echo $reg[77]; ?>"></td>
		<td width="91%" colspan="2" class="style2">
			<input name="pue_e" size="15" type="text" value="<? echo $reg[57]; ?>" tabindex="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="esta_e" size="4" type="text" value="<? echo $reg[58]; ?>" style="height: 22px" tabindex="7">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="zip_e" size="15" type="text" value="<? echo $reg[59]; ?>" tabindex="7"></td>
	</tr>

	<tr>
		<td width="91%" colspan="2" class="style2">
		&nbsp;</td>
		<td width="91%" colspan="2" class="style2">
		&nbsp;</td>
	</tr>

	</table>
<table cellpadding="2" cellspacing="0" align="center" class="style7">
		<tr>
			<td class="style3">&nbsp;</td>
			<td class="style3" colspan="9" style="height: 24px"><strong>
			Información de(los) Estudiante(s)</strong></td>
		</tr>
		<tr>
			<td class="style4"><strong>Foto</strong></td>
			<td class="style4" style="width: 300"><strong>Apellidos</strong></td>
			<td class="style4" style="width: 245"><strong>Nombre</strong></td>
			<td class="style4" style="width: 45px"><strong>Grado</strong></td>
			<td class="style4"><strong>Fecha Nacimiento</strong></td>
			<td class="style4"><strong>Matrícula Retenida</strong></td>
			<td class="style4" style="width: 20"><strong>Mem/Dem</strong></td>
			<td class="style4" style="width: 100px"><strong>Coger Verano</strong></td>
			<td class="style4"><strong>Curso</strong></td>
			<td class="style4">&nbsp;</td>
		</tr>
<? 
echo "<input type=hidden name=curso value='".$_POST['curso']."'>";
echo "<input type=hidden name=usuario2 value='$reg[22]'/>"; 


  $consulta2 = "select * from year where id = '$reg[0]' AND year='$row2[116]' and codigobaja=0";
  $resultado2 = mysql_query($consulta2);


while ($row7=mysql_fetch_array($resultado2))
      {
//      header ('Content-type: ' . $row7['tipo']);

//      $consulta4 = "select * from padre where ss='$row7[0]' AND year='$row2[116]' AND sem1<='$row2[97]' OR ss='$row7[0]' AND year='$row2[116]' AND sem2<='$row2[97]' OR ss='$row7[0]' AND year='$row2[116]' AND final<='$row2[97]'";
      $consulta4 = "select * from padres where ss='597-90-8592' AND year='14-15' AND sem1<=64 AND sem1>0 OR ss='597-90-8592' AND year='14-15' AND sem2<=64 AND sem2>0 OR ss='597-90-8592' AND year='14-15' AND final<=64 AND final>0";
      $resultado4 = mysql_query($consulta4);
//echo $consulta4;
      $consulta5 = "select * from memos where ss='$row7[0]'";
      $resultado5 = mysql_query($consulta5);
      $mem=0;
      $dem=0;
      while ($row5=mysql_fetch_array($resultado5))
            {
            $mem=$mem+1;
            $dem=$dem+$row5[7];
            }

      $cc=0;
      $vv='';
      while ($row4=mysql_fetch_array($resultado4))
            {
            $cc=0;
            list($gra,$sec) = explode("-",$row7[6]);
            IF ($gra <= 8)
               {
               IF ($row2[96]=='C')
                  {
                  IF ($row4[21] > 0)
                     {
                     IF ($row2[99]=='S1')
                        {
                        IF ($row4[19] > 0 AND $row4[19] <= $row2[97])
                           {$cc=$cc+1;}
                        }
                     IF ($row2[99]=='S2')
                        {
                        IF ($row4[20] > 0 AND $row4[20] <= $row2[97])
                           {$cc=$cc+1;}
                        }
                     IF ($row2[99]=='S')
                        {
                        IF ($row4[19] > 0 AND $row4[19] <= $row2[97] OR $row4[20] > 0 AND $row4[20] <= $row2[97])
                           {$cc=$cc+1;}
                        }
                     IF ($row2[99]=='NF')
                        {
                        IF ($row4[22] > 0 AND $row4[22] <= $row2[97])
                           {$cc=$cc+1;}
                        }
                     }
                  }
               IF ($row2[96]=='N')
                  {
                  IF ($row2[99]=='S1')
                     {
                     IF ($row4[19] > 0 AND $row4[19] <= $row2[97])
                        {$cc=$cc+1;}
                     }
                  IF ($row2[99]=='S2')
                     {
                     IF ($row4[20] > 0 AND $row4[20] <= $row2[97])
                        {$cc=$cc+1;}
                     }
                  IF ($row2[99]=='S')
                     {
                     IF ($row4[19] > 0 AND $row4[19] <= $row2[97] OR $row4[20] > 0 AND $row4[20] <= $row2[97])
                        {$cc=$cc+1;}
                     }
                  IF ($row2[99]=='NF')
                     {
                     IF ($row4[22] > 0 AND $row4[22] <= $row2[97])
                        {$cc=$cc+1;}
                     }
                  }
               }
            IF ($row2[98] >= $cc){$vv='S';}
            $q2 = "update year set verano='$vv',clase_verano='$cc' where id='$reg[0]' AND year='$row2[116]' AND ss='$row7[0]'";
            mysql_query($q2, $db) or die ("problema con query 7");
            echo $q2;
            }

      ?>
		<tr>
		   <?
//           header("Content-Type: image/jpeg");
		   $foto='src="../../images/none.gif"';
		   if (!empty($row7[80]))
		      {
              $foto = 'src="../picture/'.$row7[80].'.jpg"';
              }
		   ?>
			<td class="style2">
			<div id="wrapper">
			<? 

			$encrypted_txt = encrypt_decrypt('encrypt', $row7[0]);
			echo '<a href="1.php?id='.$encrypted_txt.'" >'; ?>
			<img alt="" height="49" <? echo $foto ?> width="41"/>
			<? echo '</a>'; ?>
            </div>

			</td>
			<td class="style2" style="width: 300"><? echo $row7[4];?> </td>
			<td class="style2" style="width: 245"><? echo $row7[3];?> </td>
			<td class="style5" style="width: 45px"><? echo $row7[2];?> </td>

<?

$dia=date(j);
$mes=date(n);
$ano=date(Y);

//fecha de nacimiento
$fec=$row7[8]; 
list($anonaz, $mesnaz, $dianaz) = explode('-', $fec);
//  list($code,$desc) = explode(", ",$_POST['desc']);
 
//si el mes es el mismo pero el d&#65533;a inferior aun no ha cumplido a&#65533;os, le quitaremos un a&#65533;o al actual
 
 if (($mesnaz == $mes) && ($dianaz > $dia)) {
$ano=($ano-1); }
 
//si el mes es superior al actual tampoco habr&#65533; cumplido a&#65533;os, por eso le quitamos un a&#65533;o al actual

if ($mesnaz > $mes) {
$ano=($ano-1);}
 
//ya no habra mas condiciones, ahora simplemente restamos los a&#65533;os y mostramos el resultado como su edad
 
$edad=$ano-$anonaz;
$ee=' ed. ';
if ($edad > 20){$edad='';$ee='';}

?>



			<td class="style5"><? echo $row7[8].$ee.$edad;?> </td>
			<td class="style5"><? echo $row7[22];?> </td>
			<td class="style5" style="width: 20"><? echo $mem.' / '.$dem ?></td>
			<td class="style5" style="width: 100px"><? echo $row7[12];?> </td>
			<td class="style5"><? echo $row7[38];?> </td>
			<td class="style5">
         <form method="post">

			<? echo "<input type=hidden name=ss value='$row7[0]'/>"; ?>
            <? echo '<input type=hidden name=curso value="'.$_POST['curso'].'">'; ?>
		
			<strong>
			<input name="camb" type="submit" value="Cambiar" style="width: 95px; height: 26px;" class="myButton" tabindex="8">
			</strong>

		</form>

			</td>
		</tr>
	 <?}?>	
		<tr>
			<td class="style1">&nbsp;</td>
			<td class="style1" style="width: 300">&nbsp;</td>
			<td class="style1" style="width: 245">&nbsp;</td>
			<td class="style1" style="width: 45px">&nbsp;</td>
			<td class="style1">&nbsp;</td>
			<td class="style1">&nbsp;</td>
			<td class="style1" style="width: 20">&nbsp;</td>
			<td class="style1" style="width: 100px">&nbsp;</td>
			<td class="style1">&nbsp;</td>
			<td class="style4"><strong>
<? 
$encrypted_txt1='777';
IF ($r6=='A'){
   if (isset($_POST['nueva']))
      {}
    else
      {
      echo '<input name="anaest" type="submit" value="Añadir" style="width: 95px; height: 26px;" class="myButton" tabindex="8">';
      }
   }
?>
			
			</strong></td>
		</tr>
	</table>
	<p align="center">
	
<p align="center">
<? 
IF ($r6=='A'){
   if (isset($_POST['nueva']))
      {
      echo '<input type=submit name="crear" class="myButton" style="width: 130px" value="Crear" tabindex="8" style="font-size: 12pt; font-weight: bold">';
      }
    else
      {
      echo '<input type=submit name="grabar" class="myButton" style="width: 130px" value="Grabar" tabindex="8" style="font-size: 12pt; font-weight: bold">';
      }
   }
?>

</form>

	&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">
</p>
<p align="center">&nbsp;</p>
	&nbsp;

</body>

</html>