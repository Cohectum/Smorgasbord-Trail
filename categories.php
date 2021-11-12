<?php
    require('DBConnect.php');

    $query = "SELECT * FROM Categories ORDER BY Category_Name";
    $statement = $db->prepare($query);
    $statement->execute();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Smorgasbord Trail - Categories
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id='wrapper'>
            <?php include('sidebar.php') ?>
            <ul id="category_list">
                <?php while($category = $statement->fetch()):  //<?= preg_replace("/ /", "-", $category[1]) ?>
                    <li> 
                        <a href="/Smorgasbord-Trail/postList.php?Category=<?= $category[0] ?>"><?= $category[1]?></a>
                    </li>
                <?php endwhile ?>
            </ul>
        </div>
    </body>
</html>