<?php
    /*
     * Author: Cameron Foy
     * Purpose: Displays single post in user view.
     * Last Updated: 11/20/2021
     */

    require __DIR__.'/vendor/autoload.php';
    require('DBConnect.php');
    session_start();

    //4.3 Sanitized GET and 4.2 Sanitized IDs
    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM items WHERE ItemId = {$get['id']}";
        $statement = $db->prepare($query);
        $statement->execute();

        $item = $statement->fetch();
    } else {
        header('Location: index.php');
    }

    $userQuery = "SELECT username FROM users WHERE UserId = {$item['UserId']}";
    $userStatement = $db->prepare($userQuery);
    $userStatement->execute();

    $poster = $userStatement->fetch();

?>
<!DOCTYPE html>
<html>
    <head>
    <title>
        Smorgasbord Trail - <?= $item['Title'] ?>
    </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('sidebar.php'); ?>
            <div id="post_box" class="container">
                <div class="row">
                    <h1>
                        <?= $item['Title'] ?>
                    </h1>
                    <?php if (isset($item['Image'])): ?>
                        <div>
                            <img id="single-image" src="<?= str_replace("Base", "Post", ".".substr($item['Image'], strpos($item['Image'], "images") - 1)) ?>">
                        </div>
                    <?php endif ?>
                    <hr>
                    <div class="container">
                        <div class="unstyled row">
                            <p>
                                <?= $item['Description'] ?>
                            </p>
                        </div>
                        <div class="unstyled row">
                            <p class="col">
                                <?php
                                    if (isset($item['Location'])) {
                                        echo "Location: ".$item['Location'];
                                    }
                                ?> 
                            </p>
                            <p class="col h3">Price: <?= $item['Price'] ?></h2>
                        </div>
                    </div>
                    <hr>
                    <p>
                        Posted: <?= $item['Created_on'] ?>
                    </p>
                    <p>By: <?= $poster['username'] ?></p>
                    <div class="d-flex justify-content-center">
                        <a href="./ViewUser.php?id=<?= $item['UserId'] ?>"><button class="btn btn-primary">Visit Seller Profile</button></a>
                        <a href="#"><button class="btn btn-secondary">Message Seller</button></a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
