<?php
    require('DBConnect.php');
    require __DIR__.'/vendor/autoload.php';
    session_start();

    if (isset($_GET) && !empty($_GET['term']) && !ctype_space($_GET['term'])) {
        $term = filter_input(INPUT_GET, "term", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $searchQuery = "SELECT * FROM items WHERE Title LIKE '%{$term}%'";

        if ($_GET['category'] != "" && filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT)) {
            $category = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
            $searchQuery = $searchQuery." AND ItemId IN (SELECT ItemId FROM itemcategories WHERE CategoryID = {$category})";
        }
        
        $searchStatement = $db->prepare($searchQuery);
        $searchStatement->execute();
    }

    $query = "SELECT * FROM categories ORDER BY Category_Name";
    $statement = $db->prepare($query);
    $statement->execute();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Smorgasbord Trail - Search</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('Sidebar.php'); ?>
            <div class="container">
                <div class="row">
                    <p class="display-6">Search</p>
                    <form action="Search.php" method="get" class="container">
                        <input class="form-control" type="text" name="term" id="term">
                        <div id="filter">
                            <div>
                            <p class="h6">Filter Category <b>(Optional)</b>:</p>
                            </div>
                            <select class="form-select" name="category" id="category" style="width: 300px;">
                                <option value=''> -- None --</option>
                                <?php while ($row = $statement->fetch()): ?>
                                    <option value="<?= $row[0] ?>"><?= $row[1] ?></option>
                                <?php endwhile ?>
                            </select>
                            <input class="btn btn-primary" id="search-button" type="submit" value="Search" style="width: 150px;">
                        </div>
                    </form>
                </div>
                <?php if(isset($term)): ?>
                    <?php if($searchStatement->rowcount() == 0): ?>
                        <div class="row">
                            <p class="display-6">No Results Found.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <p class="display-6">Showing <?= $searchStatement->rowcount() ?> Items For Term "<?= $term ?>"</p>
                        </div>
                        <?php while ($post = $searchStatement->fetch()): ?>
                        <a href="/Smorgasbord-Trail/SinglePost.php?id=<?= $post['ItemId'] ?>">
                            <div class="row">
                                <?php if (isset($post['Image'])): ?>
                                    <div class="col"><div class="list-image-box"><img src="<?= str_replace("Base", "Thumbnail", ".".substr($post['Image'], strpos($post['Image'], "images") - 1)) ?>" alt="Post Thumbnail"></div></div>
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
                    <?php endif ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>