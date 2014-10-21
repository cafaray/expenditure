<?php
$identificador = valorArgumento("identificador");
if (!($identificador==null || $identificador=="")){
    $actividad_nombre = obtieneActividad($identificador);
    print "<h2>Registrando gastos para la actividad: ".$actividad_nombre."</h2>";
    ?>
<table border="0">
    <tr>
        <th width="275px" align="left">Actividad</th>
        <th width="90px" align="center">Fecha</th>
        <th width="200px" align="left">Gasto</th>
        <th width="90px" align="left">Importe</th>
	<th width="90px" align="left">IVA</th>
        <th width="16px">&nbsp;</th>
        <th width="16px">&nbsp;</th>
    </tr>

<?php
    // $gastos = obtieneGastos("*",'P');
    $gastos = obtieneGastos("$identificador", 'P');
    print "<!-- inicia extracci&oacute;n de gastos [$gastos] -->";
    if ($gastos){
        $total_gastos = 0;
        foreach ($gastos as $gasto){
            $actividad_id  = $gasto[1];
            $gasto_id = $gasto[2];
            $fecha  = fecha2display($gasto[3]);
            $cdtipo = $gasto[4];
            $tipo = $gasto[5];
            $importe = $gasto[6];
	    $hay_iva = $gasto[8]=="1"?"S&iacute;":"No";
            $total_gastos+= $importe;
        ?>
    <tr>
        <td align="left"><?php print $actividad_nombre?></td>
        <td align="center"><?php print $fecha?></td>
        <td align="left"><?php print $tipo?></td>
        <td align="right"><?php print number_format($importe,'2',',','.');?></td>
	<td align="center"><?php print $hay_iva;?></td>
        <td align="center"><a href="javascript:registrar('<?php print $actividad_id?>','<?php print $gasto_id?>','<?php print $importe?>')"><img title="Registrar pago" alt="Registrar pago" src="resources/pagar.jpg" border="0" /></a></td>
        <td align="center"><a href="javascript:cerrar('<?php print $actividad_id?>','<?php print $gasto_id?>')"><img title = "Cerrar registro" alt="Cerrar registro" src="resources/cerrar.jpg" border="0" /></a></td>
    </tr>
    <?php
        }
    }
    ?>
    <tr>
        <td colspan="4" align="right"><b>Total adeudado</b>&nbsp;&nbsp;</td>
        <td colspan="3" align="right"><b><?php print number_format($total_gastos,"2",",","."); ?></b></td>
    </tr>
    <tr><td colspan="7">&nbsp;</td></tr>
    <tr><td colspan="7">&nbsp;</td></tr>
    <tr><td colspan="7" align="left"><p class="link"><a href="javascript:_goto('listaautorizar','','');" style="color:#099;">REGRESAR</a></p></td></tr>
</table>
<?php
}else{
    print "No se localizo la actividad para el registro de pagos.";
}
?>
<form id="REGISTRO_PAGOS">
    <input type="hidden" name="identificador" id="identificador" maxlength="36" value="" />
    <input type="hidden" name="gasto" id="gasto" maxlength="6" value="" />
    <input type="hidden" name="modo" id="modo" maxlength="1" value="." />
    <input type="hidden" name="importe" id="importe" maxlength="13" value="" />
</form>
<script type="text/javascript" language="javascript">
    <!--
    function registrar(identificador, gasto, importe){
        document.getElementById('importe').value = importe;
        enviaModo(identificador, gasto, 'L');
    }
    function cerrar(identificador, gasto){
        enviaModo(identificador, gasto, 'C');
    }
    function enviaModo(identificador, gasto, modo){
        document.getElementById('identificador').value = identificador;
        document.getElementById('gasto').value = gasto;
        REGISTRO_PAGOS.modo.value = modo;
        frm = document.getElementById('REGISTRO_PAGOS');
        frm.method = "post";
        frm.action = "modules/registrapagos.php";
        frm.submit();
    }
    -->
</script>

