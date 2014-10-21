<h1>REGISTRO DE PAGOS.</h1> 
<p class="link" ><a href="javascript:cerrarRegistro()" title="Cerrar Registro de pagos" style="color:#099;">CERRAR</a></p>
<table>
    <tr>
        <th width="275px" align="left">Empleado</th>
	<th width="90px" align="left">Fecha</th>
        <th width="275px" align="left">Actividad</th>
        <th width="90px" align="center">Gastos</th>
        <th width="90px" align="center">Pagado</th>
        <th width="19px" align="center">&nbsp;</th>
        <th width="19px" align="center">&nbsp;</th>
    </tr>
    <?php
    // extracción de usuarios y actividades.
    $pendientes = obtienePorAutorizar();
    if ($pendientes) {
        foreach($pendientes as $pendiente) {
            $activitie_id = $pendiente[1];
            $activitie = $pendiente[2];
            $user_account = $pendiente[4];
            $user_name = $pendiente[5];
            $date_entered = $pendiente[6];
            $expenditure = $pendiente[7];
            $payed = $pendiente[8];
            ?>
    <tr>
        <td><?php print $user_name?></td>
	<td><?php print $date_entered?></td>
        <td><?php print $activitie?></td>
        <td align="right"><?php print number_format($expenditure, '2',',','.'); ?></td>
        <td align="right"><?php print number_format($payed, '2',',','.'); ?></td>
        <td><a href="javascript:registrarPagos('<?php print $activitie_id?>');" title="Registrar pagos."><img alt="Registrar pago" title="Registrar pagos de estos gastos" src="resources/pagar.jpg" border="0" /></a></td>
        <td><a href="javascript:cerrarActividad('<?php print $activitie_id?>');" title="Cierra la actividad."><img alt="Registrar pago" title="Cerrar el registro de pagos de estos gastos" src="resources/cerrar.jpg" border="0" /></a></td>
    </tr>
            <?php
        }
    }
    ?>
</table>
<p>&nbsp;</p>
<div align="left" style="width: 100%">
    <p class="link" >
        <a href="javascript:cerrarRegistro()" title="Cerrar Registro de pagos" style="color:#099;">CERRAR</a>
    </p>
</div>
<form id="OPERACION_ACTIVIDAD">
    <input type="hidden" name = "identificador" id="identificador" value="." />
    <input type="hidden" name = "modo" id="modo" value="" />
</form>
<script language="javascript" type="text/javascript">
    <!--
    function registrarPagos(actividad){
        _goto('autorizar','','identificador:'+actividad);
    }
    function cerrarActividad(actividad){
        OPERACION_ACTIVIDAD.modo.value = "A";
        OPERACION_ACTIVIDAD.identificador.value = actividad;
        frm = document.getElementById('OPERACION_ACTIVIDAD');
        frm.method = 'post';
        frm.action = 'modules/registrapagos.php';
        frm.submit();
    }
    function cerrarRegistro(){
        _goto('gasto','','');
    }
    -->
</script>
