<?php
$_CONFIG = array(
        'usuario' => "crm_biotecsa",
        'contrasenia' => "bio11crm",
        'servidor' => "localhost",
        'datos' => "crm_biotecsa",
);

function obtieneConexion() {
    $myhandle = mysql_connect($GLOBALS['_CONFIG']['servidor'],$GLOBALS['_CONFIG']['usuario'],$GLOBALS['_CONFIG']['contrasenia']);
    if ($myhandle) {
        //print "mysql_select_db(".$GLOBALS['_CONFIG']['datos'].",$myhandle)";
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
function validaUsuario($usuario,$contrasenia,$grupo = 0) {
    //print "validaUsuario:::";
    $mysql = "select count(user_name) ";
    $mysql.= "from users ";
    $mysql.= "where user_name = '$usuario' ";
    $mysql.= "and user_hash = MD5('$contrasenia');";
    //print enviaAlerta($mysql);
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
            $exito = ($myrow[0]>0)?true:false;            
            //print "libera: $liberaconexion:$exito:".$myrow[0];
            //$liberaconexion = liberaConexion($myhandle);
            return $exito;
        }
    }
}

function obtieneRegistrosCabecera($usuario) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return null;
    }else {
        $sql = "select A.id, name from kexm00t A inner join actividades B on A.id = B.id ";
        $sql.= " where B.assigned_user_id = (select id from users where user_name = '$usuario') ";
        $sql.= " and A.status = 'O';";
        $myrst = mysql_query($sql, $handle);
        if (!$myrst) {
            print enviaAlerta(mysql_error($handle));
        }else {
            $i=0;
            while ($myrow = mysql_fetch_array($myrst)) {
                $vuelta[$i]['identificador'] = $myrow[0];
                $vuelta[$i]['actividad'] = $myrow[1];
                $i++;
            }
            mysql_free_result($myrst);
            $sql = "select id, activitie_name from kexm00t ";
            $sql.= " where user_id = '$usuario' ";
            $sql.= " and status = 'O';";
            $myrst = mysql_query($sql, $handle);
            while ($myrow = mysql_fetch_array($myrst)) {
                $vuelta[$i]['identificador'] = $myrow[0];
                $vuelta[$i]['actividad'] = $myrow[1];
                $i++;
            }
            for($x=0;$x<$i;$x++){
                mysql_free_result($myrst);
                $identificador = $vuelta[$x]['identificador'];
                //extrae datos de registro de gastos (detalle):
                $sql = "select importe from totales where id = '$identificador';";                
                $myrst = mysql_query($sql, $handle);
                if ($myrst) {
                    $mytotal = mysql_fetch_array($myrst);
                    $total = $mytotal[0];
                }else {
                    $total = 0;
                }
                $vuelta[$x]['total'] = $total;
            }
            return $vuelta;
        }
        //liberaConexion($handle);
    }
    return null;
}
function obtieneActividad($identificador) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
    }else {
        $sql = "select convert(name using utf8) from actividades where id = '$identificador';";
        $myrst = mysql_query($sql, $handle);
        //print "<!-- myrst:$myrst [$sql] -->";
        if (!$myrst) {
            print enviaAlerta(mysql_error($handle));
        }else {
            $myrow = mysql_fetch_array($myrst);
            if($myrow) {
                $vuelta = $myrow[0];
                return $vuelta;
            }else {                
                if ($identificador!="-1") {
                    $sql = "select activitie_name from kexm00t where id = '$identificador';";
                }else {
                    $sql = "select activitie_name from kexm00t order by tmstmp desc;";
                }
                $myrst = mysql_query($sql, $handle);
                if (!$myrst) {
                    print enviaAlerta(mysql_error($handle));
                }else {
                    $myrow = mysql_fetch_array($myrst);
                    if($myrow) {
                        $vuelta = $myrow[0];
                        return $vuelta;
                    }
                }
            }
        }
    }
    return "";
}

function registraCabecera($actividad) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    if($actividad!="-1") {
        $sql = "insert into kexm00t ";
        $sql.= "(id, date_entered, status, usuario, programa, tmstmp)";
        $sql.= " values ";
        $sql.= "('$actividad',current_date,'O',getUser(),'registraCabecera',current_timestamp);";
    }else {
        $cuenta = $_SESSION['cuenta'];
        $sql = "insert into kexm00t ";
        $sql.= "(id, date_entered, status, usuario, programa, tmstmp,user_id,activitie_name)";
        $sql.= " values ";
        $sql.= "(getCode(current_timestamp),current_date,'O',getUser(),'registraCabecera.NoActividad',current_timestamp,";
        $sql.= "'$cuenta',concat('Gastos del ',current_date));";
    }
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

function listaOpcionesActividad($asignado,$tipo="*") {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "select A.id, A.assigned_user_id, A.modified_user_id, A.created_by, A.name, A.productos ";
    $sql.= " from ";
    if($tipo=="*") {
        $sql.=" actividades ";
    }else if($tipo == "T") {
        $sql.=" tareas ";
    }else if($tipo == "P") {
        $sql.=" tareas_proyecto ";
    }else if($tipo == "M") {
        $sql.=" reuniones ";
    }else {
        return "\n<!-- No se localizaron registros con $tipo, $asignado -->";
    }
    $sql.= " A where assigned_user_id = '".identificadorUsuario($asignado)."' and A.id not in(select id from kexm00t) ";
    $sql.= " order by name;";
    //print "\n<!-- mysql: $sql -->\n";
    $myrst = mysql_query($sql,$handle);
    print "<!-- handle: $handle &nbsp;&nbsp;&nbsp;&nbsp; myrst: $myrst -->";
    if (!$myrst) {
        print enviaAlerta(mysql_error($handle));
    } else {
        $vuelta = "\n\t<!-- inicia carga de actividades desde crm -->";
        while ($elemento=mysql_fetch_array($myrst)) {
            $codigo = $elemento[0];
            $descripcion = $elemento[4];
            $vuelta.="\n<option value=\"$codigo\">$descripcion</option>\n\t";
        }
        //liberaConexion($handle);
        $vuelta.= "\n\t<!-- termino la carga de actividades desde crm -->\n";
        return $vuelta;
    }
}

function identificadorUsuario($usuario) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $identificador = "";
    $sql = "select id from users where user_name = '$usuario';";
    $myrst = mysql_query($sql, $handle);
    if (!$myrst) {
        //liberaConexion($handle);
        print enviaAlerta(mysql_error($handle));
    }else {
        while ($elemento=mysql_fetch_array($myrst)) {
            //print "<!-- elemento: ".$elemento[0]." -->";
            $identificador = $elemento[0];
        }
        //liberaConexion($handle);
    }
    return $identificador;
}

function listaOpcionesTipoActividad() {
    $elementos = obtieneCatalogo("tipact");
    if ($elementos) {
        $vuelta = "\n\t";
        while ($elemento=mysql_fetch_array($elementos)) {
            $codigo = $elemento[1];
            $descripcion = $elemento[2];
            $vuelta.="<option value=\"$codigo\">$descripcion</option>\n\t";
        }
        return $vuelta;
    }else {
        return "";
    }
}
function obtieneTiposActividad() {
    $elementos = obtieneCatalogo("tipact");
    return resultSet2array($elementos);
}

function listaOpcionesTipoGasto() {
    $elementos = obtieneCatalogo("tipgto");
    if ($elementos) {
        print "\n<!-- inicia carga de tipos de gasto[$elementos] -->";
        $vuelta = "\n\t";
        while ($elemento=mysql_fetch_array($elementos)) {
            $codigo = $elemento[0];
            $descripcion = $elemento[1];
            $vuelta.="<option value=\"$codigo\">$descripcion</option>\n\t";
        }
        return $vuelta;
    }
    return "\n<!-- No se localizo la tabla de tipos de gasto -->";
}

function insertaRegistroPago($identificador, $gasto, $importe, $comentario="Pagado.", $es_cerrar = false) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "insert into kexm10t ";
    $sql.= "(id, consecutive_expenditure, payed, date_pay, comment, usuario, programa, tmstmp)";
    $sql.= " values ";
    $sql.= "('$identificador', $gasto, $importe, current_date, '$comentario',getUser(),'insertaRegistroPago',current_timestamp);";
    if($rst = mysql_query($sql,$handle)) {
        $sql = "update kexm01t set ";
        if (!$es_cerrar) {
            $sql.= " status = 'L' ";
        }else {
            $sql.= " status = 'C' ";
        }
        $sql.= "where id = '$identificador' and consecutive = '$gasto';";
        $rst = mysql_query($sql,$handle);
        print enviaAlerta("Se inserto correctamente el registro [$rst]");
    }else {
        print enviaAlerta(mysql_error($handle));
    }
}
function cierraGasto($identificador, $gasto, $comentario="Cerrar sin pago.") {
    insertaRegistroPago($identificador, $gasto, 0.00, $comentario, true);
}
function cierraActividad($identificador) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "update kexm00t set ";
    $sql.= " status = 'C' ";
    $sql.= " where id = '$identificador';";
    $rst = mysql_query($sql,$handle);
    if($rst) {
        print enviaAlerta("Se cerro la actividad correctamente [$rst].");
    }else {
        print enviaAlerta(mysql_error($handle));
    }
}

function insertarDetalle($identificador, $fecha, $tipo, $importe, $es_iva, $nota="") {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $importe = str_replace(",", "", $importe);
    $sql = "insert into kexm01t ";
    $sql.= "(id, date_expenditure, type_expenditure, value, is_iva, status, usuario, programa, tmstmp)";
    $sql.= " values ";
    $sql.= "('$identificador','".fecha2mysql($fecha)."','$tipo',$importe,'$es_iva','P',getUser(),'insertaDetalle',current_timestamp);";
    if(mysql_query($sql,$handle)) {
        print enviaAlerta("Se inserto correctamente el registro");
    }else {
        print enviaAlerta(mysql_error($handle));
    }
    //liberaConexion($handle);
}
function actualizarDetalle($identificador, $consecutivo, $fecha, $tipo, $importe, $es_iva) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $importe = str_replace(",", "", $importe);
    $sql = "update kexm01t set ";
    $sql.= " date_expenditure = '".fecha2mysql($fecha)."', ";
    $sql.= " type_expenditure = '$tipo', ";
    $sql.= " value = $importe, ";
    $sql.= " is_iva = $es_iva, ";
    $sql.= " usuario = getUser(), ";
    $sql.= " programa = 'actualizaDetalle', ";
    $sql.= " tmstmp = current_timestamp ";
    $sql.= " where ";
    $sql.= " id = '$identificador' ";
    $sql.= " and consecutive = $consecutivo;";
    if(mysql_query($sql,$handle)) {

        print enviaAlerta("Se actualizo correctamente el registro");
    }else {
        print enviaAlerta(mysql_error($handle));
    }
    //liberaConexion($handle);
}
function eliminarGasto($identificador, $consecutivo) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "delete from kexm01t where id = '$identificador' and consecutive = $consecutivo;";
    $myrst = mysql_query($sql, $handle);
    $sql = "delete from kexm02t where id = '$identificador' and consecutive_expenditure = $consecutivo;";
    $myrst = mysql_query($sql, $handle);
    if ($myrst) {
        print enviaAlerta("Se elimino correctamente el registro.");
        //liberaConexion($handle);
        return "1";
    }else {
        //liberaConexion($handle);
        print enviaAlerta(mysql_error($handle));
    }
}

function obtieneGastos($identificador = "*", $estatus = "*") {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "select convert(activitie using utf8), expenditure, date_expenditure, type_expenditure, type_expenditure_name, value, status, is_iva ";
    $sql.= " from gastos ";
    if ($identificador!="*" || $estatus!="*") {
        $sql.= " where ";
        if($identificador!="*" && $estatus=="*") {
            $sql.=" activitie = '$identificador' ";
        }else if($identificador=="*" && $estatus!="*") {
            $sql.=" status = '$estatus' ";
        }else {
            $sql.=" activitie = '$identificador' and status = '$estatus' ";
        }
    }
    $sql.= " order by date_expenditure;";
    $myrst = mysql_query($sql, $handle);
    if ($myrst) {
        return resultSet2array($myrst);
    }else {
        print enviaAlerta(mysql_error($handle));
    }
}

function obtieneDetalles($actividad) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "select actividad, consecutivo, fecha, tipo, getDescription('cattipgto',tipo), ";
    $sql.= " valor, iva, estatus, getDescription('catestgto',estatus) ";
    $sql.= " from detalles ";
    $sql.= " where actividad = '$actividad';";
    if($myrst = mysql_query($sql,$handle)) {
        $x = 0;
        while($myrow = mysql_fetch_array($myrst)) {
            $registros[$x]['actividad'] = $myrow[0];
            $registros[$x]['consecutivo'] = $myrow[1];
            $registros[$x]['fecha'] = $myrow[2];
            $registros[$x]['cdtipo'] = $myrow[3];
            $registros[$x]['tipo'] = $myrow[4];
            $registros[$x]['valor'] = $myrow[5];
            $registros[$x]['iva'] = $myrow[6];
            $registros[$x]['cdestatus'] = $myrow[7];
            $registros[$x]['estatus'] = $myrow[8];
            $registros[$x]['notas'] = obtieneNotas($myrow[0],$myrow[1]);
            $x++;
        }
        //liberaConexion($handle);
        return $registros;
//        return resultSet2array($myrst);
    }else {
        //liberaConexion($handle);
        print enviaAlerta(mysql_error($handle));
    }
}

function obtienePorAutorizar($user_name = "*") {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "select activitie_id, activitie_name, assigned_user_id, user_account, user_name, date_entered, expenditure, payed ";
    $sql.= " from actividades_pendientes ";
    $sql.= " where status = 'O' ";
    if ($user_name!='*') {
        $sql.= " and user_name = '$user_name' ";
    }
    $sql.= " order by user_name, date_entered;";
    $myrst = mysql_query($sql,$handle);
    if ($myrst) {
        return resultSet2array($myrst);
    }else {
        print enviaAlerta(mysql_error($handle));
    }
}

function obtieneReporteGastos($sql,$user_name) {
    if (!esAdministradorGastos($user_name)){
        print enviaAlerta("El usuario $user_name no tiene permisos para emitir este reporte.");
        return null;
    }
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $rst = mysql_query($sql,$handle);
    if ($rst){
        return $rst;
    }else{
        print enviaAlerta(mysql_error($handle));
    }
}

function esAdministradorGastos($user_name) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    $sql = "select isAdminExpenditure('$user_name'); ";
    $myrst = mysql_query($sql,$handle);
    if ($myrst) {
        $myrow = mysql_fetch_array($myrst);
        return $myrow[0];
    }else {
        print enviaAlerta(mysql_error($handle));
        return null;
    }
}

function obtieneNotas($identificador, $consecutivo,$handle = null) {
    if (!$handle) {
        $handle = obtieneConexion();
        if (!$handle) {
            print enviaAlerta(mysql_error());
            return "";
        }
    }
    $sql = "select concat(consecutive_comment,':',comment) ";
    $sql.= " from kexm02t ";
    $sql.= " where id='$identificador' and consecutive_expenditure=$consecutivo;";
    $myrst = mysql_query($sql,$handle);
    $vuelta = "No hay notas.";
    if ($myrst) {
        $vuelta = "";
        while ($myrow = mysql_fetch_array($myrst)) {
            $vuelta.= $myrow[0]."\n";
        }
    }else {
        print enviaAlerta(mysql_error($handle));
    }
    //liberaConexion($handle);
    return $vuelta;
}

function agregaNota($identificador, $detalle, $nota) {
    $handle = obtieneConexion();
    if (!$handle) {
        print enviaAlerta(mysql_error());
        return "";
    }
    if(!($nota=="")) {
        $sql = "insert into kexm02t ";
        $sql.= "(id, consecutive_expenditure, comment, usuario, programa, tmstmp) ";
        $sql.= " values ";
        $sql.= "('$identificador',$detalle,'$nota',getUser(),'agregaNota',current_timestamp);";
        if($myrst = mysql_query($sql,$handle)) {
            print enviaAlerta("Se agrego la nota correctamente.");
        }else {
            print enviaAlerta(mysql_error($handle));
        }
    }
    //liberaConexion($handle);
}

function valorArgumento($argumento) {
    $argumentos = $_REQUEST['argumentos'];
    //print enviaAlerta($argumentos);
    if ($argumentos) {
        if (strstr("|", $argumentos)>0) {
            $elementos = split("|",$argumentos);
            foreach($elementos as $elemento) {
                $tmp = split(":",$elemento);
                $llave = $tmp[0];
                $valor = $tmp[1];
                if ($llave==$argumento) {
                    return $valor;
                }
            }
        }else {
            //SOLO ES UNO
            $tmp = split(":",$argumentos);
            $llave = $tmp[0];
            $valor = $tmp[1];
            if ($llave==$argumento) {
                return $valor;
            }
        }
        return "";
    }else {
        return null;
    }
}

function obtieneCatalogo($catalogo) {
    $mysql = "select cdelem codigo, dselem descripcion ";
    $mysql.= "from ktcm00t ";
    $mysql.= "where cdtabla = 'cat$catalogo';";
    $myhandle = obtieneConexion();
    if ($myhandle==null) print enviaAlerta(mysql_error());
    $myrst = mysql_query($mysql,$myhandle);
    if ($myrst) {
        return $myrst;
    }else {
        print enviaAlerta(mysql_error($myhandle));
        return null;
    }
}
/***
 * obtiene descripci�n del cat�logo:
 * @catalogo nombre del catalogo en la tabla.
 * @cdelem c�digo del elemento a describir
 * regresa la descripci�n del codigo del elemento
 */
function descripcion($catalogo,$cdelem) {
    $mysql = "select dselem descripcion ";
    $mysql.= "from ktcm00t ";
    $mysql.= "where cdtabla = '$catalogo' ";
    $mysql.= "and cdelem = '$cdelem';";
    $myhandle = obtieneConexion();
    if ($myhandle==null) print enviaAlerta(mysql_error());
    ;
    $myrst = mysql_query($mysql,$myhandle);
    if ($myrst) {
        $myrow = mysql_fetch_array($myrst);
        if ($myrow) {
            $descripcion = $myrow[0];
        }else {
            $descripcion = "";
        }
        //liberaConexion($handle);
        return $descripcion;
    }else {
        //liberaConexion($handle);
        print enviaAlerta(mysql_error($myhandle));
        return null;
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
