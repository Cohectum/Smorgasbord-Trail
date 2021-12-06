<?php
    require __DIR__.'/vendor/autoload.php';
    session_start();

    require('DBConnect.php');

    $query = "SELECT * FROM users ORDER BY userId DESC";
    $statement = $db->prepare($query);

    $statement->execute();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Smorgasbord Trail - Users</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('sidebar.php') ?>
            <div id="user_box">
                <ul>
                    <?php while ($user = $statement->fetch()): ?>
                        <li id="user_box">
                            <a href="viewUser.php?id=<?=$user['UserId']?>"><h3><?= $user['Username'] ?></h3></a>
                            <p><?= $user['Email'] ?></p>
                        </li>
                    <?php endwhile ?>
                </ul>
            </div>
        </div>
    </body>
</html>