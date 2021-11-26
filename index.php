<?php
    session_start();
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
            <h2>Welcome to Smorgasbord Trail!</h2>
            <div class="split-text">
                <p>Smorgasbord Trail is a website to buy and sell retro technology. A place for collectors and enthusiasts to come together to save these gems from landfills!</p>
                <button id="about">Learn More</button>
            </div> <!-- Close split-text Div 1 -->
            <?php include("footer.php") ?>
        </div> <!-- Close Wrapper Div -->
    </body>
</html>