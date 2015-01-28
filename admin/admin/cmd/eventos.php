<?php
session_start();
include '../model/conexion.php';
$titulo = $_POST['titulo'];
$finicio = $_POST['inicio'];
$ffin = $_POST['fin'];
$cmd = $_POST['cmd'];
if($cmd==  md5("registra-evento".  session_id())){
    $vuelta = registraEvento($evento, $fechaInicio, $fechaFin, $archivo);
} else {
    echo "No se logro identificar la sesión.";
}
