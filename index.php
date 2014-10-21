<?php
session_start();
include 'modules/mysql.php';
if ($_REQUEST['cerrar']!="true") {
    $cuenta = isset($_SESSION['cuenta'])?$_SESSION['cuenta']:"";
}else {
    session_destroy();
    session_unset();
    session_write_close();
    $cuenta = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Registro de gastos</title>
        <link REL="SHORTCUT ICON" HREF="http://www.biotecsa.com/crm_biotecsa/include/images/sugar_icon.ico">
        <link rel="stylesheet" href="style/principal.css" />
        <script language="javascript" type="text/javascript" src="js/index.js"></script>
    </head>
    <body>
        <div style="width:900px; height:auto;  margin:auto; margin-top:5px;">
            <div style=" width:900px; height:56px; background-image:url(img/cabeza_i.png); text-align:right; position:relative;">
                <?php
                $pantalla = $_REQUEST['pantalla'];
                if($cuenta!="") {
                    ?>

                <div class="link" style="position:absolute; left: 495px; top: 30px; width: 365px;">
                    <h3><a href="http://www.biotecsa.com">Ir a Biotecsa</a>&nbsp;&nbsp; |&nbsp;&nbsp;I<a href="http://www.biotecsa.com/crm_biotecsa">r a CRM&nbsp;</a>&nbsp;&nbsp;|&nbsp;<a href="modules/validausuario.php?cerrar=true">Cerrar sess&iacute;on</a>&nbsp;&nbsp;</h3>
                </div>

                    <?php
                }
                ?>
            </div>
            <div style="width:900px; height:20px; background-image:url(img/cuerpo_iS.png);"></div>
            <div style="width:830px; height:auto; background-image:url(img/cuerpo_iM.png); padding:10px; padding-bottom:0px; margin-bottom:-16px; text-align:left; padding-left:30px; padding-right:40px;">
                <p>&nbsp;</p>
                <?php
                $es_admin = esAdministradorGastos($cuenta);
                if ($es_admin=="1" && $pantalla =="gasto") {
                    ?>
                <p class="link" >
			<a href="javascript:_goto('listaautorizar','','');" style="color:#099;">REGISTRAR PAGOS</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="javascript:_goto('reporte','','');" style="color:#099;">REPORTE DE GASTOS</a>
		</p>
                    <?php
                }
                ?>
                <?php include 'modules/principal.inc';?>
                <br />
                <?php
                if ($cuenta == "") {
                    include 'modules/inicio.php';
                }else if($pantalla =="gasto" || $pantalla == "") {
                    include 'gasto.php';
                }else if($pantalla=="detalle_gasto") {
                    include 'gastos.php';
                }else if($pantalla=="listaautorizar") {
                    include 'listaxautorizar.php';
                }else if($pantalla=="autorizar") {
                    include 'autorizar.php';
                }else if($pantalla=="reporte"){
                    include 'descarga.php';
                }
                ?>
            </div>
         

            <div style="background-image:url(img/cuerpo_iI.png); width:900px; height:40px;font-size: 6pt">
            </div>

                <?php
                if ($cuenta!="") {
                    print "<p align=\"right\" style= \"margin-right:30px;font-size:6pt\">POWERED BY FTC <br />[$cuenta]</p>";
                }else {
                    print "<p align=\"right\" style= \"margin-right:30px;font-size:6pt\">POWERED BY FTC</p>";
                }
                ?>
        </div>
    </body>
    <script type="text/javascript" language="javascript">
        w = window.screen.availWidth;
        h = window.screen.availHeight;
        w = (w - 900) / 2;        
        document.getElementById("cabecera").style.left = w+'px';
        document.getElementById("cuerpo").style.left = w+'px';
        document.getElementById("pie").style.left = w+'px';
        document.getElementById("botones_cabecera").style.visibility = "visible";
        //alert(document.getElementById("cuerpo").style.left);
    </script>
</html>
