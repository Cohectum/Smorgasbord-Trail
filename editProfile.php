<?php
    require __DIR__.'/vendor/autoload.php';
    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }
