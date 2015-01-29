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
            input.text { margin-bottom:12px; width:90%; padding: .4em; }
            select { margin-bottom:12px; width:95%; padding: .4em; }
            fieldset { padding:0; border:0; margin-top:25px; }
            h2 { font-size: 1.2em; margin: .6em 0; }          
            #menu{width: 20%;height: 100%;margin-top: 10px; border: silver 2px solid;padding: 10px;display: block;float: left}
            #workarea{width: 75%;height: 400px;margin-top: 5px; border: black 2px solid;padding: 5px;display: block;float: left}
            #iworkarea{width: 100%;height: 100%;border: 1px solid silver;margin: 5px;}
            #listado{width: 90%;margin-left: 5px;border: 1px sandybrown inset;height: 300px;}
        </style>
        <script>
            $(function (){
                var titulo = $("#titulo"),
                finicio = $("#finicio"),
                ffin = $("#ffin"),
                cmd = $("#cmd"),
                file = $("#file");
                
                $("#finicio").datepicker({
                    dateFormat:"dd-mm-yy",
                    minDate: 0,
                    maxDate: "+2M +1D",
                    onClose: function(selectedDate) {
                        $("#ffin").datepicker("option", "minDate", selectedDate);
                    }
                });
                $("#ffin").datepicker({
                    dateFormat:"dd-mm-yy",
                    maxDate: "+2M +1D"
                });
                $("#registrar").button().click(function (){  
                    var data = new FormData($("#FORM_NEW_EVENT")[0]);
                    console.info(data);
                    $.ajax({
                        url: '../cmd/eventos.php?files',
                        type: 'POST',
                        data: data,
                        cache: false,
                        processData: false, // Don't process the files
                        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                        success: function(data) {
                            alert(data);
                            var nombre = data;
                            console.log(nombre.indexOf(":"));
                            if(nombre.indexOf(":")>0){
                                nombre =  nombre.substring(nombre.indexOf(":")+1);
                                console.log(nombre);
                                file.val(nombre);
                            }                            
                            $("#FORM_NEW_EVENT").submit();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                    
                });
                $("#FORM_NEW_EVENT").submit(function (){   
                    //console.log(titulo.val()+finicio.val()+ffin.val()+file.val());
                    //data = serialize()
                    $.ajax({
                        url: '../cmd/eventos.php',
                        type: 'POST',
                        data: {cmd: "almacenar", titulo:titulo.val(), inicio: finicio.val(), fin:ffin.val(), archivo:file.val()},
                        //cache: false,
                        dataType: "text",
                        async: true,
                        success: function(data) {
                            if(data=="1"){
                                alert("Se registro correctamente el evento.");                                
                            }else{
                                alert(data);
                            }
                            console.info(data);                            
                            location.reload();
                        },
                        error: function(data) {
                            alert(data);
                        }
                    });
                });
                $("#cancelar").button().click(function (){
                    location.replace("eventos.php");
                });
            });
        </script>
    </head>
    <body>
        <?php
        require '../cmd/validasesion.php';
        ?>
        <form id="FORM_NEW_EVENT" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="cmd" value="<?php echo md5("registra-evento".session_id())?>" name="cmd" />
            <input type="hidden" id="file" value="<?php echo md5("archivo-evento".session_id())?>" name="file" />
            <label>Titulo:</label>
            <input type="text" name="titulo" id="titulo" value=""class="text ui-corner-tl" />
            <label>Fecha inicio:</label>
            <input type="text" name="finicio" id="finicio" value=""class="text ui-corner-tl" />
            <label>Fecha fin:</label>
            <input type="text" name="ffin" id="ffin" value=""class="text ui-corner-tl" />
            <label>Archivo:</label>
            <input type="file" name="archivo" id="archivo" value=""class="text ui-corner-tl" />
            <a id="cancelar">Cancelar</a>&nbsp;&nbsp;&nbsp;&nbsp;<a id="registrar">Registrar</a>
        </form>
    </body>
</html>