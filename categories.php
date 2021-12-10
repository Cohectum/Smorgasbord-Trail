<?php
    require __DIR__.'/vendor/autoload.php';
    session_start();
    require('DBConnect.php');

    $query = "SELECT * FROM Categories ORDER BY Category_Name";
    $statement = $db->prepare($query);
    $statement->execute();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>
            Smorgasbord Trail - Categories
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id='wrapper'>
            <?php include('sidebar.php') ?>
            <div class="container">
                <div class="row">
                    <ul class="list-unstyled" id="category_list">
                        <?php while ($category = $statement->fetch()): ?>
                            <li class="link-hoverable display-6"><a class="link-dark link-hoverable" href="/Smorgasbord-Trail/postList.php?Category=<?= $category[0] ?>"><?= $category[1]?></a></li>
                            <li><hr></li>
                        <?php endwhile ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>