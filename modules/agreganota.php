<html>
    <head>
        <title>A&ntilde;adir nota</title>
        <script type="text/javascript" language="javascript" src="../js/index.js"></script>
    </head>
    <body>
<?php
include 'principal.inc';
?>

<?php
include 'mysql.php';
$identificador = $_POST['nota_identificador'];
$consecutivo = $_POST['nota_detalle'];
$nota = $_POST['agrega_nota'];
$nota = str_replace("á", "&aacute;", $nota);
$nota = str_replace("é", "&eacute;", $nota);
$nota = str_replace("í", "&iacute;", $nota);
$nota = str_replace("ó", "&oacute;", $nota);
$nota = str_replace("ú", "&uacute;", $nota);
$nota = str_replace("Á", "&Aacute;", $nota);
$nota = str_replace("É", "&Eacute;", $nota);
$nota = str_replace("Í", "&Iacute;", $nota);
$nota = str_replace("Ó", "&Oacute;", $nota);
$nota = str_replace("Ú", "&Uacute;", $nota);
$nota = str_replace("Ñ", "&Ntilde;", $nota);
$nota = str_replace("ñ", "&ntilde;", $nota);
agregaNota($identificador, $consecutivo, $nota);
        ?>
<script language="javascript" type="text/javascript">
    <!--
    __goto('detalle_gasto','','identificador:<?php print $identificador?>');    
    -->
</script>
</body>
</html>