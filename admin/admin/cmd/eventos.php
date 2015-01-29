<?php
session_start();
include '../model/conexion.php';


$titulo = $_POST['titulo'];
$inicio = $_POST['inicio'];
$fin = $_POST['fin'];
$cmd = $_POST['cmd'];
$archivo = $_POST['archivo'];

//echo "cmd: $cmd";    
if($cmd==  md5("registra-evento".session_id())){
    // SUBIR EL ARCHIVO:

    if(isset($_FILES["archivo"]) && $_FILES["archivo"]["error"]== UPLOAD_ERR_OK) {
        ############ Edit settings ##############
        $UploadDirectory    = '../uploads/'; //specify upload directory ends with / (slash)
        ##########################################

        /*
        Note : You will run into errors or blank page if "memory_limit" or "upload_max_filesize" is set to low in "php.ini".
        Open "php.ini" file, and search for "memory_limit" or "upload_max_filesize" limit
        and set them adequately, also check "post_max_size".
        */

        //check if this is an ajax request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
            die();
        }


        //Is file size is less than allowed size.
        if ($_FILES["archivo"]["size"] > 5242880) {
            die("File size is too big!");
        }

        //allowed file type Server side check
        switch(strtolower($_FILES['archivo']['type'])) {
                //allowed file types
                case 'image/png':
                case 'image/gif':
                case 'image/jpeg':
                case 'application/pdf':
                    break;
                default:
                    die('Unsupported File!'); //output error
        }

        $File_Name          = strtolower($_FILES['archivo']['name']);
        $File_Ext           = substr($File_Name, strrpos($File_Name, '.')); //get file extention
        $Random_Number      = rand(0, 9999999999); //Random number to be added to name.
        $NewFileName        = $Random_Number.$File_Ext; //new file name

        if(move_uploaded_file($_FILES['archivo']['tmp_name'], $UploadDirectory.$NewFileName )) {
            // do other stuff
            die("Archivo cargado:$NewFileName");
        } else {
            die('Error cargando el archivo');
        }

    } else {
        die('No se ha logrado iniciar la carga del archivo verifique el tipo de archivo y el tamaño. '.$_FILES["archivo"]["error"]);
    }
} else if($cmd=="almacenar") {
    //echo "registraEvento($titulo, $inicio, $fin, $archivo, ".session_id().");";
    date_default_timezone_set("America/Mexico_City");
    $dateIni = date_create($inicio);
    $dateFin = date_create($fin);
    //echo date_format($dateIni, "m-d-Y H:i:s.u");
    echo registraEvento($titulo, date_format($dateIni, "Y-m-d H:i:s.u"), date_format($dateFin, "Y-m-d H:i:s.u"), $archivo,  session_id());
} else if(strrpos($cmd, md5("elimina-evento".session_id()))>-1){
    $posicion = strlen(md5("elimina-evento".session_id()));
    $identificador = substr($cmd, $posicion);
    echo eliminaEvento($identificador);
} else {
    echo "No se logro identificar la sesión [$cmd].";
}
