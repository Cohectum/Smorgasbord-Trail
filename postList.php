<?php
    session_start();
    require('DBConnect.php');

    $isLoggedIn = isset($_SESSION['userId']);
    $fill_flag = false;
    $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $orderStatement = "ORDER BY Created_on DESC";
    $sort = "Newest";
    $category = '';

    if ($isLoggedIn) {
        if (isset($get['Sort'])) {
            if ($get['Sort'] == "Cheapest") {
                $orderStatement = "ORDER BY Price ASC";
                $sort = "Cheapest";
            } elseif ($get['Sort'] == "Oldest") {
                $orderStatement = "ORDER BY Created_on ASC";
                $sort = "Oldest";
            } elseif ($get['Sort'] == "Alphabetical") {
                $orderStatement = "ORDER BY Title ASC";
                $sort = "Alphabetical";
            }
        }
    }

    if (isset($_GET['Category'])) {
        $query = "SELECT * FROM items WHERE ItemId IN (SELECT ItemId FROM itemcategories WHERE CategoryID = {$get['Category']}) ".$orderStatement;
        $statement = $db->prepare($query);
        $statement->execute();

        $category = $get['Category'];
    } elseif (isset($_GET['all'])) {
        $query = "SELECT * FROM items ".$orderStatement;
        $statement = $db->prepare($query);
        $statement->execute();
    } else {
        header('Location: PostList.php?all');
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
                <?php if ($isLoggedIn): ?>
                    <div id="list_nav">
                        <?php if ($statement->rowcount() == 0): ?>
                            <h1>Search Returned No Results.</h1>
                        <?php elseif (isset($_GET['Category'])): ?>
                            <h2>Seach Returned <?= $statement->rowcount() ?> Rows</h2>
                            <a href="./PostList.php?Category=<?= $category ?>"><button>Newest</button></a>
                            <a href="./PostList.php?Category=<?= $category ?>&Sort=Oldest"><button>Oldest</button></a>
                            <a href="./PostList.php?Category=<?= $category ?>&Sort=Cheapest"><button>Cheapest</button></a>
                            <a href="./PostList.php?Category=<?= $category ?>&Sort=Alphabetial"><button>Alphabetical</button></a>
                            <h2>Sorted By: <?= $sort ?></h2>
                        <?php elseif (isset($_GET['all'])): ?>
                            <h2>Seach Returned <?= $statement->rowcount() ?> Rows</h2>
                            <a href="./PostList.php?all"><button>Newest</button></a>
                            <a href="./PostList.php?all&Sort=Oldest"><button>Oldest</button></a>
                            <a href="./PostList.php?all&Sort=Cheapest"><button>Cheapest</button></a>
                            <a href="./PostList.php?all&Sort=Alphabetical"><button>Alphabetical</button></a>
                            <h2>Sorted By: <?= $sort ?></h2>
                        <?php endif ?>    
                    </div>
                <?php endif; ?>
                <div class="container">
                    <?php while ($post = $statement->fetch()): ?>
                        <a href="/Smorgasbord-Trail/SinglePost.php?id=<?= $post['ItemId'] ?>">
                            <div class="row">
                                <?php if (isset($post['Image'])): ?>
                                    <div class="col"><div class="list-image-box"><img src="<?= str_replace("Base", "Thumbnail", ".".substr($post['Image'], strpos($post['Image'], "images") - 1)) ?>"></div></div>
                                <?php else: ?>
                                    <div class="col"></div>
                                <?php endif ?>
                                <div class="col-7">
                                    <p class="display-6"><?= $post['Title'] ?></p>
                                    <p class="lead">
                                        <?php
                                            if (isset($post['Location'])) {
                                                echo $post['Location'];
                                            }
                                        ?> 
                                    </p>
                                </div>
                                <div class="col"><p class="display-5"><?= "$".$post['Price'] ?></p></div>
                            </div>
                        </a>
                    <?php endwhile ?>
                </div>
            </ul>
        </div>
    </body>
</html> 