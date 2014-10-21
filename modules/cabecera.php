<?php
session_start();
include 'mysql.php';
$identificador = $_REQUEST["actividad"];
$cuenta = $_SESSION["cuenta"];
if (registraCabecera($identificador)){
    print enviaAlerta("Debe indicar los gastos asociados a esta actividad.", "../index.php?pantalla=detalle_gasto&argumentos=identificador:$identificador", 521);
}else{
    print enviaAlerta("No se logró actualizar el registro.", "../index.php?pantalla=gasto", 521);
}
?>
