<?php
    session_start();
    include '../model/conexion.php';
    $usuario = $_REQUEST['usuario'];
    $contrasenia = $_REQUEST['contrasenia'];
    $validacion = validaUsuario($usuario, $contrasenia);
    if($validacion>=1){
        ?>
<script>
    setTimeout("location.replace('../default.html')", 0);
</script>
        <?php
    } else { 
        ?>
<script>
    alert("No se logro establecer el enlace con la base de datos.");
    setTimeout("location.replace('../index.html')", 0);
</script>
<?php
    }
