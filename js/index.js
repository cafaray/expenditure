function _goto(pantalla,modo,argumentos){
    document.getElementById('pantalla').value = pantalla;
    document.getElementById('modo').value = modo;
    document.getElementById('argumentos').value = argumentos;
    frm = document.getElementById('PRINCIPAL');
    frm.action = "index.php";
    frm.method = "post";
    frm.submit();
}
function __goto(pantalla,modo,argumentos){
    document.getElementById('pantalla').value = pantalla;
    document.getElementById('modo').value = modo;
    document.getElementById('argumentos').value = argumentos;
    frm = document.getElementById('PRINCIPAL');
    frm.action = "../index.php";
    frm.method = "post";
    frm.submit();
}
function enviar(){
    frm = document.getElementById('INICIO_SESION');
    frm.method = "post";
    frm.action = "modules/validausuario.php";
    frm.submit();
}
function salir(){
    location.replace('http://www.biotecsa.com/crm_biotecsa/index.php');
}

function trim(texto){
    return texto.replace(/^\s+/g,'').replace(/\s+$/g,'');
}