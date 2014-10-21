<?php session_start(); ?>
<?php
include 'modules/mysql.php';
$cuenta = isset($_SESSION['cuenta'])?$_SESSION['cuenta']:"";
$fecini = fecha2mysql($_POST['fecini']);
$fecfin = fecha2mysql($_POST['fecfin']);
//print("parametros: $fecini - $fecfin");

$sql = " select activitie, name, expenditure, date_expenditure, type_expenditure_name, value, status, employer ";
$sql.= " from reporte_gastos ";
$sql.= " where date_expenditure >= '$fecini' and date_expenditure <= '$fecfin'";
$sql.= " order by date_expenditure;";
//print("<br />obtieneReporteGastos($sql, $cuenta);");
$rst = obtieneReporteGastos($sql, $cuenta);
//print("<br />RST: $rst");
if ($rst) {
    header('Content-Disposition: attachment; filename="gastos.csv"');
    $codigos = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&ntilde;","&Ntilde;",);
    $palabras = array("á","é;","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ",);
    echo 'identificadore,actividad,gasto,fecha,tipo,valor,estatus,empleado'.chr(13);
    while ($row = mysql_fetch_array($rst)) {
        $actividad = str_replace($codigos, $palabras, $row[1]);
        $tipo = str_replace($codigos, $palabras, $row[4]);
        $empleado = str_replace($codigos, $palabras, $row[7]);
        echo $row[0].','.$actividad.','.$row[2].','.$row[3].','.$tipo.','.$row[5].','.$row[6].','.$empleado.chr(13);
    }
    //echo " Se ha creado correctamente el archivo.";
}
?>