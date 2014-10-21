<script src="js/jquery.1.4.js" type="text/javascript"></script>
<script src="js/jquery.ui.core.js" type="text/javascript"></script>
<script src="js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="js/datepicker.js" type="text/javascript"></script>
<script src="js/valida_componente.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
    $(function() {
        $( "#fecha" ).datepicker();
    });
</script>
<link rel="style/stylesheet" href="demo.css" />
<link rel="stylesheet" href="style/css/ui-lightness/jquery-ui-1.8.9.custom.css" />
<style>
    #div_agrega_nota{
        visibility: hidden;
        position: absolute;
        background: #FFFFCC;
        color: gray;
        height: 150px;
        width: 500px;
        left: 370px;
        margin: 10px 10px 10px 10px;        
    }
</style>
<?php
    $identificador = valorArgumento('identificador');
    $actividad = obtieneActividad($identificador);
print "<h2>Detalle de gasto para actividad.</h2>";
if ($actividad!="") {
    print "<h3>$actividad</h3>";
}else {
    print "<h3>No se localizo la actividad $identificador para registrar gastos.</h3>";
}
?>
<hr />
<form id="REGISTRO_DETALLE">
    <input type="hidden" name="identificador" id="identificador" value="<?php print $identificador?>" />
    <input type="hidden" name="consecutivo" id="consecutivo" value="" />
    <input type="hidden" name="actividad" id="actividad" value="<?php print $actividad?>" />
    <input type="hidden" name="modo" id="modo" value="d" />
    <table width="100%">
        <tr>
            <td width="90px">Fecha</td>
            <td width="190px">Tipo de gasto</td>
            <td width="120px">Importe</td>
            <td width="30px">IVA</td>
            <td width="120px">Impuesto</td>
            <td width="90px" >&nbsp;&nbsp;</td>
            <td width="90px">&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td>
                <input id="fecha" type="text" name="fecha"  style="width:90px" />
            </td>
            <td>
                <select name="tipo_gasto" id="tipo_gasto" title="Selecciona el tipo de gasto a registrar">
                    <option value="-1">[seleccione]</option>
                    <?php print listaOpcionesTipoGasto();?>
                    <!-- termina carga de tipos de gasto -->
                </select>
            </td>
            <td>
                <input type ="text" name="importe" id="importe" value="0" onblur="formatoImporte()" style="text-align: right;width: 90px" maxlength="13" />
            </td>
            <td>
                <input type="checkbox" value="1" name="iva" id="iva" onchange="calculaImpuesto(this.checked);" />
            </td>
            <td>
                <input type ="text" name="impuesto" id="impuesto" value="0.00" style="text-align: right;width: 90px" maxlength="13" readonly />
            </td>
            <td>
                <input type="button" value="Agregar" onclick="agregar();" title="Agrega el gasto a la relaci&oacute;n." style="width: 80px" />
            </td>
            <td>
                <input type="button" value="Cerrar" onclick="cerrar();" title="Regresa a la ventana inicial de registro." style="width: 80px" />
            </td>
        </tr>
    </table>
</form>
<table style="border-style: solid;border-width: thin" width="95%" align="center">
    <tr>
        <th width="90px" align="left" style="border-right-style: solid;border-width: thin">Fecha</th>
        <th width="200px" align="left" style="border-right-style: solid;border-width: thin">Gasto</th>
        <th width="120px" align="left" style="border-right-style: solid;border-width: thin">Importe</th>
        <th width="90px" align="center" style="border-right-style: solid;border-width: thin">Estatus</th>
        <th width="300px" align="left" style="border-right-style: solid;border-width: thin">Notas</th>
        <th width="25px" align="center" title="Editar" style="border-right-style: solid;border-width: thin">&nbsp;</th>
        <th width="25px" align="center" title="Eliminar" style="border-right-style: solid;border-width: thin">&nbsp;</th>
        <th width="25px" align="center" title="A&ntilde;adir nota">&nbsp;</th>
    </tr>
    <!-- Extracción de gastos registrados -->
    <?php
    $gastos = obtieneDetalles($identificador);
    if ($gastos) {
        print "\n<!-- Iniciando impresion de gastos [$gastos] -->\n";
        $total = 0;
        foreach($gastos as $gasto) {
            $codigo = $gasto['consecutivo'];
            $fecha = fecha2display($gasto['fecha']);
            $cdtipo = $gasto['cdtipo'];
            $tipo = $gasto['tipo'];
            $importe = $gasto['valor'];
            $cdestatus = $gasto['cdestatus'];
            $estatus = $gasto['estatus'];
            $es_iva = $gasto['iva'];
            $notas = str_replace("\n", "<br />", $gasto['notas']);
            $total+= $importe;            
?>
    <tr>
        <td style="border-right-style: solid;border-width: thin" valign="top"><?php print $fecha;?></td>
        <td style="border-right-style: solid;border-width: thin" valign="top"><?php print $tipo;?></td>
        <td style="border-right-style: solid;border-width: thin" valign="top" align="right"><?php print number_format($importe, 2, '.' , ',' );?></td>
        <td style="border-right-style: solid;border-width: thin" valign="top"><?php print $estatus;?></td>
        <td style="border-right-style: solid;border-width: thin" valign="top"><?php print $notas?>&nbsp;</td>

        <?php
        if($cdestatus == "P"){
        ?>
        <td style="border-right-style: solid;border-width: thin" valign="top"><img onclick="anadirNota('<?php print $codigo;?>',event);" alt="Anexar nota" src="resources/doc.gif" border="0" title="Anexa una nota al gasto." style="cursor: pointer" /></td>
        <td style="border-right-style: solid;border-width: thin" valign="top"><a href="javascript:editar('<?php print $codigo;?>','<?php print $fecha;?>','<?php print $cdtipo;?>','<?php print $importe;?>','<?php print $es_iva;?>')"><img alt="Editar" src="resources/edit.png" border="0" title="Edita la informaci&oacute;n del gasto actual." /></a></td>
        <td valign="top"><a href="javascript:eliminar('<?php print $codigo;?>')"><img alt="Eliminar" src="resources/cancel.png" border="0" title="Elimina el gasto actual." /></a></td>
        <?php
        }else{
        ?>
        <td style="border-right-style: solid;border-width: thin" valign="top"><img onclick="operacionNoValida();" alt="Anexar nota" src="resources/nopermitido.jpg" border="0" title="Anexa una nota al gasto." style="cursor: pointer" /></td>
        <td style="border-right-style: solid;border-width: thin" valign="top"><a href="javascript:operacionNoValida();"><img alt="Editar" src="resources/nopermitido.jpg" border="0" title="Edita la informaci&oacute;n del gasto actual." /></a></td>
        <td valign="top"><a href="javascript:operacionNoValida();"><img alt="Eliminar" src="resources/nopermitido.jpg" border="0" title="Elimina el gasto actual." /></a></td>
        <?php
        }
        ?>
    </tr>
    <?php
        }
        print "\n<!-- Termina impresion de gastos -->\n";
    }
    ?>
    <tr>
        <td colspan="4" align="right"><b>TOTAL</b></td>
        <td colspan="3" align="right"><b><?php print number_format($total, 2, '.' , ',' )?></b></td>
    </tr>
</table>
<script type="text/javascript" language="javascript">
    <!--
    function calculaImpuesto(valor){
        if(valor){
            importe = document.getElementById("importe").value;
            importe = importe.replace(",","");            
            impuesto = importe - (importe / 1.16);
            //alert(impuesto);
            nStr = String(impuesto);
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1].substring(0,2) : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            impuesto = x1 + x2;                
            document.getElementById("impuesto").value = impuesto;
        }else{
            document.getElementById("impuesto").value = "0.00";
        }
    }
    function formatoImporte(){
        valor = document.getElementById("importe").value;
        valor = valor.replace(",","");
        if (isNaN(valor)){
            alert("Se debe especificar un valor valido en este campo.");
            document.getElementById("importe").focus();
            return;
        }        
        if (document.getElementById('iva').checked){
            calculaImpuesto(true);
        }
        nStr = String(valor);
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1].substring(0,2) : '.00';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        valor = x1 + x2;
        document.getElementById("importe").value = valor;
    }
    function anadirNota(consecutivo,e){
        alto = e.clientY - 65;
        alto = alto+"px";
        document.getElementById("div_agrega_nota").style.top = alto;
        document.getElementById("nota_detalle").value = consecutivo;
        document.getElementById("agrega_nota").value = "[NOTA]";
        document.getElementById("div_agrega_nota").style.visibility = "visible";
    }
    function agregar(){
        if (validaComponentes()){            
            frm = document.getElementById("REGISTRO_DETALLE");
            frm.method = "post";
            frm.action = "modules/admingastos.php";
            frm.submit();
        }
    }
    function eliminar(consecutivo){        
        AGREGAR_NOTA.nota_detalle.value = consecutivo;
        AGREGAR_NOTA.modo.value = "r";
        frm = document.getElementById('AGREGAR_NOTA');
        frm.method = "post";
        frm.action = "modules/admingastos.php";
        frm.submit();
    }
    function editar(consecutivo, fecha, cdtipo, importe, es_iva){
        REGISTRO_DETALLE.modo.value = 'a';
        REGISTRO_DETALLE.fecha.value = fecha;
        REGISTRO_DETALLE.tipo_gasto.value = cdtipo;
        REGISTRO_DETALLE.consecutivo.value = consecutivo;
        REGISTRO_DETALLE.importe.value = importe;
        if (es_iva!=0){
            REGISTRO_DETALLE.iva.checked = true;
            calculaImpuesto(true);
        }else{
            REGISTRO_DETALLE.iva.checked = false;
            REGISTRO_DETALLE.impuesto.value = "0.00";
        }
        //REGISTRO_DETALLE.agregar.value = "Actualizar";
    }
    function validaComponentes(){
        if (!checkComponent('s', 'fecha', 10)){
            alert("Debes de introducir una fecha valida.");
            return false;
        }
        if (!checkComponent('l', 'tipo_gasto', 1)){
            alert("Debes de introducir un tipo de gasto.");
            return false;
        }
        if (!checkComponent('d', 'importe', 100000)){
            alert("Debes de introducir un importe mayor a cero.");
            return false;
        }
        return true;
    }
    function cerrar(){
        _goto('gasto','','');
    }
    function operacionNoValida(){
        alert("Esta operación no esta permitida. Verifique estatus.");
    }
    -->
</script>
<div id="div_agrega_nota" >
    &nbsp;&nbsp;&nbsp;&nbsp;
    <form id="AGREGAR_NOTA">
        <input type="hidden" name="modo" id="modo" value="" />
        <input type="hidden" name="nota_identificador" id="nota_identificador" value="<?php print $identificador?>" />
        <input type="hidden" name="nota_detalle" id="nota_detalle" value="" />
        <table align="center" width="95%" border="0">
            <tr>
                <td><textarea id="agrega_nota" name="agrega_nota" cols="50" rows="6">[NOTA]</textarea></td>
                <td align="left" valign="top">
                    &nbsp;&nbsp;<a href="javascript:guardar_nota()">A&ntilde;adir</a>
                    &nbsp;&nbsp;<a href="javascript:cerrar_nota()">Cancelar</a>
                </td>
            </tr>
        </table>
    </form>
    <script type="text/javascript" language="javascript">
        <!--
        function guardar_nota(){            
            frm = document.getElementById("AGREGAR_NOTA");
            frm.method = "post";
            frm.action = "modules/agreganota.php";
            frm.submit();
        }
        function cerrar_nota(){
            document.getElementById("agrega_nota").value = "[NOTA]";
            document.getElementById("div_agrega_nota").style.visibility = "hidden";
        }
        -->
    </script>
</div>
