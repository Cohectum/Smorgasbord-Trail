<?php

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Smorgasbord Trail - Messages</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('Sidebar.php') ?>
            <div class="container">
                <div class="row">
                    <p class="display-5">We're sorry, this page has not been implemented yet. come back soon!</p>
                </div>
            </div>
        </div>
    </body>
</html>