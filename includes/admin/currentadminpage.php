<?php
    session_start();
    $_SESSION["currentAdminPage"] = "dashboard";

    if(isset($_POST["currentadminpage"])){
        $_SESSION["currentAdminPage"] = $_POST["currentadminpage"];
        echo $_SESSION["currentAdminPage"];
    }



?>