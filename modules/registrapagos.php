<html>
    <head>
        <title>Registro de pagos</title>
        <script language="javascript" type="text/javascript" src="../js/index.js"></script>
    </head>
    <body>
        <?php
        include 'principal.inc';
        include 'mysql.php';
        $modo = $_POST['modo'];
        $identificador = $_POST['identificador'];
        $gasto = $_POST['gasto'];
        //print "modo : $modo";
        if ($modo == "L"){
            $importe = $_POST['importe'];
            insertaRegistroPago($identificador, $gasto, $importe);
        }else if($modo == "C"){
            cierraGasto($identificador, $gasto);
        }else if($modo == "A"){
            cierraActividad($identificador);
        }else{
           print "Operaci&oacute;n no reconocida.";
        }
        ?>
    <script language="javascript" type="text/javascript">
        <!--
        setTimeout("__goto('autorizar','','identificador:<?php print $identificador?>');",521);
        -->
    </script>
    </body>
</html>
