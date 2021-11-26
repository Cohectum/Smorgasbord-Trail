<?php
    session_start();

    require('DBConnect.php');

    if(!isset($_SESSION['userId'])){
        header("Location: login.php");
    }

    session_destroy();

    header('Location: LogIn.php');
?>
