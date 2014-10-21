<script src="js/jquery.1.4.js" type="text/javascript"></script>
<script src="js/jquery.ui.core.js" type="text/javascript"></script>
<script src="js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="js/datepicker.js" type="text/javascript"></script>
<script src="js/valida_componente.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
    $(function() {
        $( "#fecini" ).datepicker();
    });
    $(function() {
        $( "#fecfin" ).datepicker();
    });
</script>
<link rel="style/stylesheet" href="demo.css" />
<link rel="stylesheet" href="style/css/ui-lightness/jquery-ui-1.8.9.custom.css" />
<?php
    $hoy=getdate();
    $dia=$hoy['mday'];
    $mes=$hoy['mon'];
    $anio=$hoy['year'];
    $fecha = "$dia/$mes/$anio";
?>
<form id="REPORTE_GASTOS">
    <h3>Reporte de ingreso de gastos.</h3>
    <table>
        <tr>
            <td>Fecha de inicio</td>
            <td><input id="fecini" type="text" name="fecini"  style="width:90px" value="<?php echo $fecha?>" /></td>
            <td>Fecha l&iacute;mite</td>
            <td><input id="fecfin" type="text" name="fecfin"  style="width:90px" value="<?php echo $fecha?>" /></td>
        </tr>
        <tr>
            <td colspan="4" align="right">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4" align="right">
                <p class="link">
                <a href="javascript:_goto('gasto','','');" style="color:#099;">REGRESAR</a>
                &nbsp;|&nbsp;<a href="javascript:emitir()" style="color:#099;">EMITIR</a>
                </p>
            </td>
        </tr>
    </table>
</form>      
<script language="javascript" type="text/javascript">
    <!--
    function emitir(){
        if (confirm("Estas seguro de emitir el reporte de gastos?")){
            frm = document.getElementById("REPORTE_GASTOS");
            frm.method = "post";
            frm.action = "descargaGastos.php";
            frm.submit();
        }
    }
    -->
</script>