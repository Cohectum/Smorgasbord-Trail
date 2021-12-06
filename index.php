<?php
    require __DIR__.'/vendor/autoload.php';
    session_start();

    $message_flag = false;
    $message = "";

    if (isset($_GET['LoginSuccess'])) {
        $message_flag = true;
        $message = "You have successfully logged in!";
    } elseif (isset($_GET['LogOutSuccess'])) {
        $message_flag = true;
        $message = "You have successfully logged out";
    } elseif (isset($_GET['Registered'])) {
        $message_flag = true;
        $message = "You have successfully Registered, You may now log in!";
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Smorgasbord Trail - Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include("sidebar.php") ?>
            <div class="contianer">
                <div class="split-text">
                    <?php if ($message_flag): ?>
                        <div id="index_message"> <p class="display-6"><?= $message ?></p> </div>
                    <?php endif ?>
                    <p class="display-4">Welcome to Smorgasbord Trail!</p>
                    <p class="lead">Smorgasbord Trail is a website to buy and sell retro technology. A place for collectors and enthusiasts to come together to save these gems from landfills!</p>
                    <a href="./About.php"><button class="btn btn-primary" id="about">Learn More</button></a>
                </div> <!-- Close split-text Div 1 -->
                <?php include("footer.php") ?>
            </div>
        </div> <!-- Close Wrapper Div -->
    </body>
</html>