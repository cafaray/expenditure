<?php
session_start();
if(isset($_REQUEST['cerrar'])){
    unset($_SESSION['cuenta']);
    $_SESSION['cuenta'] = NULL;
    //session_unregister("cuenta");
    session_destroy();
    session_commit();
    ?>
<script>
    setTimeout("location.replace('../index.html')",10);
</script>
<?php
}
