<?php
include 'mysql.php';
$es_cerrar = $_REQUEST['cerrar'];
if ($es_cerrar=="true") {
    ?>
<script language="javascript" type="text/javascript">
    <!--
    setTimeout("location.replace('../index.php?cerrar=true')",521);
    -->
</script>
<?php
}else {
    $usuario = $_POST['usuario'];
    $contrasenia = $_POST['contrasenia'];
    if (validaUsuario($usuario, $contrasenia)) {
        session_start();
        $_SESSION['cuenta'] = $usuario;
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['sesion'] = session_id();
//        $fecha = new DateTime();
        $fecha = getdate();
?>
<script language="javascript" type="text/javascript">alert("chingon");</script>
        <?php
        //$fecha->format('Y-m-d H:i:sP');
        $_SESSION['inicio'] = $fecha["year"]."-".$fecha["mon"]."-".$fecha["mday"]." ".$fecha["hours"].":".$fecha["minutes"].":".$fecha["seconds"];
        $_SESSION['es_cerrar'] = "false";
        ?>
<script language="javascript" type="text/javascript">
    <!--
    alert("chingon");
    setTimeout("location.replace('../index.php?pantalla=gasto')",521);
    -->
</script>
        <?php
    }else {
        $_SESSION['cuenta'] = "";
        ?>
<script language="javascript" type="text/javascript">
    <!--
    alert("No se logro conectar con la base de datos.\nEl usuario o contrasena son incorrectos.");
    location.replace('../index.php');
    -->
</script>
        <?php
    }    
}
?>
