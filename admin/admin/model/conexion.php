<?php
include 'casadelpastel.php';

$_CONFIG = array(
        'usuario' => "sysadmin",
        'contrasenia' => "sys.admin#15#",
        'servidor' => "localhost",
        'datos' => "casadelpastel",
);

function obtieneConexion() {
    $myhandle = mysql_connect($GLOBALS['_CONFIG']['servidor'],$GLOBALS['_CONFIG']['usuario'],$GLOBALS['_CONFIG']['contrasenia']);
    if ($myhandle) {
        mysql_select_db($GLOBALS['_CONFIG']['datos'],$myhandle);
        return $myhandle;
    }else {
        print enviaAlerta("No se logró establecer comunicación con la base de datos.\n".mysql_error());
        return null;
    }
}
function liberaConexion($myhandle) {
    try {
        if ($myhandle) {
            mysql_close($myhandle);
            print enviaAlerta(mysql_error());
        }
    }catch (Exception $e) {
        echo 'Excepci&oacute;n ,'. $e->getMessage().', \n';
    }
}
function validaUsuario($usuario,$contrasenia) {
    $mysql = "SELECT COUNT(idusuari) ";
    $mysql.= "FROM jsecust ";
    $mysql.= "WHERE idusuari = '$usuario' ";
    $mysql.= "AND dsvalenc = PASSWORD('$contrasenia');";
    $myhandle = obtieneConexion();
    if (!$myhandle) {
        print enviaAlerta(mysql_error());
        return null;
    }else {
        $myrst = mysql_query($mysql, $myhandle);
        if (!$myrst) {
            print enviaAlerta(mysql_error());
            return null;
        }else {            
            $myrow = mysql_fetch_array($myrst);
            $exito = ($myrow[0]>0)?1:0; 
            if($exito==1){
                session_start();
                $_SESSION['cuenta'] = $usuario;
                $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['sesion'] = session_id();
            }
            //print "libera: $liberaconexion:$exito:".$myrow[0];
            //$liberaconexion = liberaConexion($myhandle);
            return $exito;
        }
    }
}

function registraEvento($evento, $fechaInicio, $fechaFin, $archivo, $sesion){
    $handle = obtieneConexion();
    if(!$handle){
        print enviaAlerta(mysql_error());
        return "";
    } else {        
        $sql = "INSERT INTO jcem10t VALUES (MD5(CONCAT('$fechaInicio',CURRENT_TIMESTAMP)),'$evento','$fechaInicio','$fechaFin','$archivo','$sesion')";
        $myrst = mysql_query($sql, $handle);
        if($myrst) {
            //liberaConexion($handle);
            //echo "insertado $myrst";
            return "1";
        }else {
            //liberaConexion($handle);
            print enviaAlerta(mysql_error($handle));
            return "-1";
        }
    }
}

function actualizarEvento($identificador, $evento, $fechaInicio, $fechaFin, $archivo){
    $handle = obtieneConexion();
    if(!$handle){
        print enviaAlerta(mysql_error());
        return "";
    } else {
        $sql = "UPDATE jcem10t SET dscaleve = '$evento', dtfeceve = '$fechaInicio', dtfineve = '$fechaFin',dsfileve = '$archivo' ";
        $sql.= "WHERE idcaleve = '$identificador';";
        $myrst = mysql_query($sql,$handle);
        if($myrst) {
            //liberaConexion($handle);
            return "1";
        }else {
            //liberaConexion($handle);
            print enviaAlerta(mysql_error($handle));
            return null;
        }
    }
}

function eliminaEvento($identificador){
    $handle = obtieneConexion();
    if(!$handle){
        print enviaAlerta(mysql_error());
        return "";
    } else {
        $sql = "SELECT idcaleve, dsfileve FROM jcem10t WHERE idcaleve = '$identificador';";        
        if($myrst = mysql_query($sql,$handle)) {
            if($myrow = mysql_fetch_array($myrst)){ 
                $archivo = $myrow[1];
                $archivo = "../uploads/".$archivo;
                echo "unlink($archivo)";
                unlink($archivo);
            }
            $sql = "DELETE FROM jcem10t WHERE idcaleve = '$identificador';";
            $myrst = mysql_query($sql,$handle);
            if($myrst) {
                //liberaConexion($handle);
                return "1";
            }else {
                //liberaConexion($handle);
                print enviaAlerta(mysql_error($handle));
                return -1;
            }
        }
    }
}

function consultaEvento($mes, $anio) {
    $handle = obtieneConexion();
    if(!$handle){
        print enviaAlerta(mysql_error());
        return "";
    } else {
        $sql = "SELECT idcaleve, dscaleve, dtfeceve, dtfineve, dsfileve FROM jcem10t WHERE MONTH(dtfeceve) = $mes AND YEAR(dtfeceve) = $anio;";        
        if($myrst = mysql_query($sql,$handle)) {
            $x = 0;
            while($myrow = mysql_fetch_array($myrst)) {
                $registros[$x]['identificador'] = $myrow[0];
                $registros[$x]['evento'] = $myrow[1];
                $date = date_create($myrow[2]);
                $registros[$x]['fechaInicio'] = date_format($date,'d-m-Y');
                $date = date_create($myrow[3]);
                $registros[$x]['fechaFin'] = date_format($date,'d-m-Y');
                $registros[$x]['archivo'] = $myrow[4];                
                $x++;
            }
            return $registros;
        } else {
            //liberaConexion($handle);
            print enviaAlerta(mysql_error($handle));
        }
    }
}
/***
 *
 * Genera el c�digo script (javascript) para enviar una alerta y enviar un redireccionamiento.
 * @alerta mensaje que se desea enviar a la ventana
 * @retorno p�gina a la que se desea ir de forma autom�tica
 * @tiempo_retorno tiempo de setTimeout para el redireccionamiento, solo funciona cuando se especifica retorno
 *
 */
function enviaAlerta($alerta,$retorno = null,$tiempo_retorno = 0) {
    $myscript = "<script type=\"text/javascript\" language = \"javascript\">";
    $myscript.= "\n\talert(\"$alerta\");";
    if ($retorno !=null) {
        $lnk = "location.replace('$retorno')";
        if ($tiempo_retorno>0) {
            $lnk = "setTimeout(\"$lnk\",$tiempo_retorno)";
        }else {
            // nothing TODO
        }
        $myscript.= "\n\t$lnk;";
    }else {
        //nothing TODO
    }
    $myscript.= "\n</script>";
    return $myscript;
}
/***
 *
 * Genera el c�digo script (javascript) para realizar la funci�n enviada
 * @funcion funci�n al script
 *
 */
function generaScript($funcion) {
    $myscript = "<script type=\"text/javascript\" language = \"javascript\">";
    $myscript.= "\n\t$funcion;";
    $myscript.= "\n</script>";
    return $myscript;
}
/***
 *
 * Transforma un elemento mysql_query en un arreglo
 * @myrst resultset de mysql_query
 *
 */
function resultSet2array($myrst) {
    $resultset = null;
    $row = 0;
    while ($myrow = mysql_fetch_array($myrst)) {
        $col = 0;
        foreach ($myrow as $column) {
            $resultset[$row][$col]=$myrow[$col++];
        }
        $row++;
    }
    return $resultset;
}
/***
 * transforma una fecha del formato dd/mm/yyyy -> yyyy-mm-dd
 * @fecha fecha que se desea transformar en formato dd/mm/yyyy
 */
function fecha2mysql($fecha) {
    $fecha = split("/", $fecha);
    $dia = $fecha[0];
    $mes = $fecha[1];
    $anyo = $fecha[2];
    return "$anyo-$mes-$dia";
}
/***
 * transforma una fecha del formato yyyy-mm-dd -> dd/mm/yyyy
 * @fecha fecha que se desea transformar en formato yyyy-mm-dd
 */
function fecha2display($fecha) {
    $fecha = split("-", $fecha);
    $dia = $fecha[2];
    $mes = $fecha[1];
    $anyo = $fecha[0];
    return "$dia/$mes/$anyo";
}
/***
 * transforma una fecha del formato yyyy-mm-dd -> dd de mes de yyyy
 * @fecha fecha que se desea transformar en formato yyyy-mm-dd
 */
function fecha2largo($fecha) {
    $fecha = split("-", $fecha);
    $dia = $fecha[2];
    $mes = $fecha[1];
    $anyo = $fecha[0];
    return "$dia de ".strmes($mes)." de $anyo";
}
/***
 * intmes to strmes
 * convierte un n�mero entero (mes) a su cadena con respecto al mes.
 * @intmes n�mero del mes iniciando de 1 y llegando hasta 12
 */
function strmes($intmes) {
    switch ($intmes) {
        case 1: return "Enero";
        case 2: return "Febrero";
        case 3: return "Marzo";
        case 4: return "Abril";
        case 5: return "Mayo";
        case 6: return "Junio";
        case 7: return "Julio";
        case 8: return "Agosto";
        case 9: return "Septiembre";
        case 10: return "Octubre";
        case 11: return "Noviembre";
        case 12: return "Diciembre";
        default: return "N/A";
    }
}
?>