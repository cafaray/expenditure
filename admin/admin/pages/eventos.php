<?php session_start();?>
<!DOCTYPE html>
<html>
    <head>
        <title>.::Manejador de eventos::.</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/sunny/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <style>
            body {
                font-family:  "Verdana", "Helvetica", "Trebuchet MS","Arial", "sans-serif";
                font-size: 85.5%;
            }
            label, input[text], select{ display:block; }
            input.text { margin-bottom:12px; width:95%; padding: .4em; }
            select { margin-bottom:12px; width:95%; padding: .4em; }
            fieldset { padding:0; border:0; margin-top:25px; }
            h2 { font-size: 1.2em; margin: .6em 0; }          
            #menu{width: 20%;height: 100%;margin-top: 10px; border: silver 2px solid;padding: 10px;display: block;float: left}
            #workarea{width: 75%;height: 400px;margin-top: 5px; border: black 2px solid;padding: 5px;display: block;float: left}
            #iworkarea{width: 100%;height: 100%;border: 1px solid silver;margin: 5px;}
            #listado{width: 90%;margin-left: 5px;border: 1px sandybrown inset;height: 300px;}
            .par{background: #fcefa1; color: #915608; height: 30px;}
            .non{background: #fcefa1; color: #0a0a0a; height: 30px;}
        </style>
        <script>
            $(function (){
               $("#nuevo").button({
                   icons:{
                       primary:"ui-icon-document"                       
                   },
                   text:true
                }).click(function (){
                    location.replace("nuevoEvento.php");
                }); 
                $("#mes").change(function (event){
                    event.preventDefault();
                    cambiaFecha();
                });
                $("#anio").change(function (event){
                    event.preventDefault();
                    cambiaFecha();
                });
                $(".editar").button({
                    icons:{
                       primary:"ui-icon-pencil"                       
                   },
                   text:false
                }).click(function (event){
                    event.preventDefault();
                    alert("cmd: "+$(this).attr("cmd"));
                    var cmd = $(this).attr("cmd");
                    location.replace("nuevoEvento.php?evento="+cmd);
                });
                $(".eliminar").button({
                    icons:{
                       primary:"ui-icon-trash"                       
                   },
                   text:false
                }).click(function (event){
                    event.preventDefault();
                    //alert("cmd: "+$(this).attr("cmd"));
                    if(confirm("Estas seguro de eliminar este evento?")){
                        var cmd = $(this).attr("cmd");
                        $.ajax({
                            url: '../cmd/eventos.php',
                            type: 'POST',
                            data: {cmd: cmd},
                            dataType: "text",
                            async: true,
                            success: function(data) {
                                //alert(data);
                                console.info(data);
                                if(data=="1"){
                                    alert("Se ha eliminado con exito el evento.");
                                } else {
                                    alert(data);
                                }
                                location.reload();
                            },
                            error: function(data) {
                                alert(data);
                            }
                        });
                    }
                });
                
            });
            function cambiaFecha(){                    
                    $("#FORM_SHOW_EVENTS").submit();
                }
        </script>
    </head>
    <body>
        <div>
<?php
require '../cmd/validasesion.php';
    include '../model/conexion.php';
    
    echo "<!-- Listado de eventos -->";
    date_default_timezone_set("America/Mexico_City");
    if($_POST['mes']){        
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];
        $cmd = $_POST["cmd"];
    } else {
        $mes = date('n');
        $anio = date('Y');
    }
    $registros = consultaEvento($mes,$anio);
    
?>
            <form id="FORM_SHOW_EVENTS" method="POST">
                <input name="cmd" id="cmd" value="" type="hidden" />
            <div>Filtrar por a&ntilde;o-mes: 
                <select name="anio" id = "anio" style="width: 20%;display: inline">
                    <option value="2015" <?php echo $anio==2015?" selected = \"true\"":"";?>>2015</option>
                    <option value="2016" <?php echo $anio==2016?" selected = \"true\"":"";?>>2016</option>
                    <option value="2017" <?php echo $anio==2017?" selected = \"true\"":"";?>>2017</option>
                    <option value="2018" <?php echo $anio==2018?" selected = \"true\"":"";?>>2018</option>
                    <option value="2019" <?php echo $anio==2019?" selected = \"true\"":"";?>>2019</option>
                    <option value="2019" <?php echo $anio==2020?" selected = \"true\"":"";?>>2020</option>
                </select>
                <select name="mes" id="mes"  style="width: 50%;display: inline">
                    <option value="1"<?php echo $mes==1?" selected = \"true\"":"";?>>Enero</option>
                    <option value="2"<?php echo $mes==2?" selected = \"true\"":"";?>>Febrero</option>
                    <option value="3"<?php echo $mes==3?" selected = \"true\"":"";?>>Marzo</option>
                    <option value="4"<?php echo $mes==4?" selected = \"true\"":"";?>>Abril</option>
                    <option value="5"<?php echo $mes==5?" selected = \"true\"":"";?>>Mayo</option>
                    <option value="6"<?php echo $mes==6?" selected = \"true\"":"";?>>Junio</option>
                    <option value="7"<?php echo $mes==7?" selected = \"true\"":"";?>>Julio</option>
                    <option value="8"<?php echo $mes==8?" selected = \"true\"":"";?>>Agosto</option>
                    <option value="9"<?php echo $mes==9?" selected = \"true\"":"";?>>Septiembre</option>
                    <option value="10"<?php echo $mes==10?" selected = \"true\"":"";?>>Octubre</option>
                    <option value="11"<?php echo $mes==11?" selected = \"true\"":"";?>>Noviembre</option>
                    <option value="12"<?php echo $mes==12?" selected = \"true\"":"";?>>Diciembre</option>
                </select>              
                <input type="hidden" id="cmd" value="cmd:" />
            </div>
            <table id="listado">
                <tr style="height: 30px;border: 1px solid #fcefa1;">
                    <th>Id</th>
                    <th>Evento</th>
                    <th>Fecha inicio</th>
                    <th>Fecha termino</th>
                    <th>&nbsp;</th>
                    <!-- <th>&nbsp;</th> -->
                    <th>&nbsp;</th>
                </tr>
                <?php
                    if($registros) {
                        $x = 0;
                        foreach($registros as $registro){
                            $parnon = ($x%2)==1?"par":"non";
                ?>
                <tr class="<?php echo $parnon?>"> 
                    <td><?php echo ++$x;?></td>
                    <td><?php echo $registro['evento'];?></td>
                    <td><?php echo $registro['fechaInicio'];?></td>
                    <td><?php echo $registro['fechaFin'];?></td>
                    <td><a href="../uploads/<?php echo $registro['archivo'];?>" target="_blank"><img src="../uploads/<?php echo $registro['archivo'];?>" alt="No se encontro la imaen" width="40" /></a></td>
                    <!-- <td><a class="editar" cmd="<?php echo md5("edita-evento".session_id()).$registro['identificador'];?>">Editar</a></td> -->
                    <td><a class="eliminar" cmd = "<?php echo md5("elimina-evento".session_id()).$registro['identificador'];?>">Eliminar</a></td>
                </tr>
                <?php
                        }
                    }
                ?>
                <tr>
                    <td colspan="7"></td>
                </tr>
            </table>
            <table style="width: 90%">
                <tr>
                    <td colspan="7" style="text-align: right">
                        <a id="nuevo">Nuevo</a>
                    </td>
                </tr>
            </table>
                </form>
            </div>
        
    </body>
</html>
