<?php
if(isset($_SESSION['cuenta'])){
    echo "<!-- sesion es activa [".  session_id()." ".$_SESSION['cuenta']."]] -->";
} else {
    ?>
    <script>
        //alert(self.parent);
        self.parent.location.replace("../index.html");
        </script>
        <?php
}
