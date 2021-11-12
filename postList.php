<?php
    require('DBConnect.php');

    $fill_flag = false;
    $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (isset($_GET['Category'])) {
        $query = "SELECT * FROM items WHERE ItemId IN (SELECT ItemId FROM itemcategories WHERE CategoryID = {$get['Category']})";
        $statement = $db->prepare($query);
        $statement->execute();
    }
    else if (isset($_GET['all'])){
        $query = "SELECT * FROM items ORDER BY Created_on DESC";
        $statement = $db->prepare($query);
        $statement->execute();
    }
    

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
            <ul id="post_list">
                <?php while($post = $statement->fetch()): ?>
                    <a href="/Smorgasbord-Trail/SinglePost.php?id=<?= $post['ItemId'] ?>">
                        <div class="post_overview">
                            <img src="<?= str_replace("Base", "Thumbnail", ".".substr($post['Image'], strpos($post['Image'], "images") - 1)) ?>">
                            <div class="post_information">
                                <h3><?= $post['Title'] ?></h3>
                                <p>
                                    <?php 
                                        if(isset($post['Location'])){
                                            echo $post['Location'];
                                        }
                                    ?> 
                                </p>
                                <h2><?= "$".$post['Price'] ?></h2>
                            </div>
                        </div>
                    </a>
                <?php endwhile ?>
            </ul>
        </div>
    </body>
</html> 