<?php
    session_start();
    if(!isset($_SESSION["admin_username"])) {
        header("Location: adminlogin.php");
        exit();
    }
?>
