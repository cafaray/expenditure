<?php
$tipo = isset($_REQUEST['tipo_actividad'])?$_REQUEST['tipo_actividad']:"*";
$asignado = $_SESSION['cuenta'];
?>
<form id="REGISTRO_CABECERA">
    <table>
        <tr>
            <td>Tipo de actividad</td>
            <td>
                <select id="tipo_actividad" name="tipo_actividad" title="Selecciona el tipo de actividad realizada" onchange="cambioTipo();" style="width: 200px">
                    <option value="*" selected>[todas]</option>
                    <?php
                    $tipos = obtieneTiposActividad();
                    foreach($tipos as $tipoactividad) {
                        $codigo = $tipoactividad[1];
                        $descripcion = $tipoactividad[2];
                        if($tipo == $codigo) {
                            ?>
                    <option value="<?php print $codigo;?>" selected><?php print $descripcion;?></option>
                            <?php
                        }else {
                            ?>
                    <option value="<?php print $codigo;?>"><?php print $descripcion;?></option>
                            <?php
                        }
                    }
                    ?>
                    <option value="-" <?php print ($tipo=="-"?"selected":"");?>>Sin actividad</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Actividad realizada</td>
            <td>                
                <select id="actividad" name="actividad" title="Selecciona la actividad realizada y de la que se registrarán los gastos" style="width: 300px">
                    <option value="-1">[seleccione]</option>                    
                    <?php print listaOpcionesActividad($asignado,$tipo); ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <input type="button" value="Registrar" title="Registrar los gastos de esta actividad" onclick="registrar()" style="width: 100px" />                
            </td>
        </tr>
    </table>
</form>
<hr />
<h1>Listado de actividades para registro de gastos.</h1>
<table width="706" border="0">
    <tr>
        <th width="295" style="text-align:center;"><img src="img/activida.png" width="295" height="22" /></th>
        <th width="296" style="text-align:center;"><strong><img src="img/valor.png" width="295" height="22" /></strong></th>
        <th width="50" bgcolor="#efefef">&nbsp;</th>
        <th width="47" bgcolor="#efefef">&nbsp;</th>
    </tr>
    <?php
    $registros = obtieneRegistrosCabecera($asignado);
    if ($registros) {
        $x = 0;
        foreach($registros as $registro) {
            $identificador = $registro['identificador'];
            $actividad = $registro['actividad'];
            //$total = number_format($registro['total'], 2, '.' , ',');
            $total = $registro['total'];
            $estilo = $x%2==0?"style = \"background-color:#efefef\"":"";
            ?>
    <tr <?php print $estilo?>>
        <td><?php print $actividad?></td>
        <td align="right"><?php print number_format($total, 2,".",".")?></td>
        <td align="center"><a href="javascript:editar('<?php print $identificador?>');"><img alt="Editar" src="img/icons/pencil_32.png" border="0" title="Modifica los gastos asociados a la actividad." /></a></td>
        <td align="center"><a href="javascript:cerrar('<?php print $identificador?>');"><img alt="Cerrar gastos" src="img/icons/clipboard_32.png" border="0" title="Cierra el reporte de gastos de esta actividad." /></a></td>
    </tr>
            <?php
            $x++;
        }
    }else {
        print "<!-- No hay registros para $asignado -->";
    }
    ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php

    ?>
</table>
<script language="javascript" type="text/javascript">
    <!--
    function registrar(){
        tipo = document.getElementById("tipo_actividad");
        actividad = document.getElementById("actividad");
        if (actividad.selectedIndex<1 && tipo.value !='-'){
            alert("Debe seleccionar una actividad para poder registrar gastos.");
            actividad.focus();
            return;
        }
        frm = document.getElementById("REGISTRO_CABECERA");
        frm.method = "post";
        frm.action = "modules/cabecera.php";
        frm.submit();
    }
    function cambioTipo(){
        frm = document.getElementById("REGISTRO_CABECERA");
        frm.method = "post";
        frm.action = "index.php";
        frm.submit();
    }
    function editar(actividad){        
        _goto('detalle_gasto', '', 'identificador:'+actividad);
    }
    function cerrar(actividad){
        alert("Solo el supervisor de gastos puede cerrar los gastos de esta actividad.");
    }
    -->
</script>