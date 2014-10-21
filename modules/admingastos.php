<html>
    <head>
        <title>Administrador de gastos</title>
        <script language="javascript" type="text/javascript" src="../js/index.js"></script>
    </head>
    <body>
        <?php
        include 'principal.inc';
        include 'mysql.php';        
        $modo = $_POST['modo'];
        if($modo == "d") {
            $identificador = $_POST['identificador'];
            $fecha = $_POST['fecha'];
            $tipo_gasto = $_POST['tipo_gasto'];
            $importe = $_POST['importe'];
            $es_iva = isset($_POST['iva'])?$_POST['iva']:'0';
            $impuesto = $_POST['impuesto'];
            $nota = $_POST['nota'];
            insertarDetalle($identificador, $fecha, $tipo_gasto, $importe, $es_iva, $nota);
        }else if($modo == "a"){
            $identificador = $_POST['identificador'];
            $consecutivo = $_POST['consecutivo'];
            $fecha = $_POST['fecha'];
            $tipo_gasto = $_POST['tipo_gasto'];
            $importe = $_POST['importe'];
            $es_iva = isset($_POST['iva'])?$_POST['iva']:'0';
            $impuesto = $_POST['impuesto'];
            actualizarDetalle($identificador, $consecutivo, $fecha, $tipo_gasto, $importe, $es_iva, $nota);
        }else if ($modo == "r") {
            $identificador = $_POST['nota_identificador'];
            $consecutivo = $_POST['nota_detalle'];
            eliminarGasto($identificador, $consecutivo);
        }
        ?>
        <script language="javascript" type="text/javascript">
            <!--
            __goto('detalle_gasto','','identificador:<?php print $identificador?>');
            -->
        </script>
    </body>
</html>