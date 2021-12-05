<?php
    require('DBConnect.php');

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }

    $error_flag = false;
    $error_message = "";

    $get = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    $selfProfile = false; 

    if($get){
        $userSearch = $get;
    }
    else{
        $userSearch = $_SESSION['userId'];
    }

    $commentValidationQuery = "SELECT * FROM reviews WHERE UserId = {$userSearch} && Review_User = {$_SESSION['userId']}";
    $commentValidationStatement = $db->prepare($commentValidationQuery);
    $commentValidationStatement->execute();
    $reviewDoesntExist = $commentValidationStatement->rowcount() == 0;

    $selfProfile = $userSearch == $_SESSION['userId'];

    if ($_POST && !$selfProfile && $reviewDoesntExist) {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        

        if (filter_var($post['rating'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 5]])) {
            if(!strlen($post['content'] < 256)){

                if(ctype_space($post['content'] || empty($post['content']))){
                    $content = null;
                }
                else{
                    $content = $post['content'];
                }

                $reviewQuery = "INSERT INTO reviews (UserId, Review_User, Rating, Content) VALUES (:userId, :reviewUser, :rating, :content)";
                $reviewStatement = $db->prepare($reviewQuery);

                $reviewStatement->execute([ 
                    ":userId" => $userSearch, 
                    "reviewUser" => $_SESSION['userId'], 
                    ":rating" => $post['rating'], 
                    ":content" => $content 
                ]);

                $avgQuery = "SELECT AVG(Rating) FROM reviews WHERE UserId = {$userSearch}";
                $avgStatement = $db->prepare($avgQuery);
                $avgStatement->execute();

                $avg = $avgStatement->fetch();

                $userUpdate = "UPDATE users SET Review_Score = :score WHERE UserId = {$userSearch}";
                $userStatement = $db->prepare($userUpdate);
                $userStatement->bindValue(':score', $avg[0]);
                $userStatement->execute();
            }
            else{
                $error_flag = true;
                $error_message = "Review is longer than 255 characters";
            }
        }
        else{
            $error_flag = true;
            $error_message = "Incorrect Rating Entered";
        }
    }

    $query = "SELECT * FROM users WHERE UserId = {$userSearch}";
    $statement =  $db->prepare($query);
    $statement->execute();
    $user = $statement->fetch();

    $itemQuery = "SELECT * FROM items WHERE UserId = {$userSearch}";
    $itemStatement = $db->prepare($itemQuery);
    $itemStatement->execute();

    $commentQuery = "SELECT * FROM reviews WHERE UserId = {$userSearch} ORDER BY Date DESC";
    $commentStatement = $db->prepare($commentQuery);
    $commentStatement->execute();

    
?>
<!DOCTYPE html>
<html>
    <head>
    <title>
            Smorgasbord Trail - View User
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php require('sidebar.php'); ?>
            <div id="user_box" class="container">
                <?php if(isset($user['Profile_Picture'])): ?>
                   <img src='./Profile_Pictures/<?=$user['Profile_Picture']?>' alt="Profile Picture">
                <?php endif ?>
                <?php if($error_flag): ?>
                    <p class="error_message"><?= $error_message?></p>
                <?php endif ?>
                <p class="h1"><?= $user['Username']?></p>
                <hr>
                <div id="seller_items">
                    <h2>User Listings</h2>
                    <div class="container">

                    </div>
                    <?php if($itemStatement->rowCount() === 0): ?>
                        <p><?= $user['Username']?> has no items for sale.</p>
                    <?php endif ?>
                    <?php while($item = $itemStatement->fetch()): ?>
                        <a href="./SinglePost.php?id=<?= $item['ItemId'] ?>">
                            <div class="row">
                                <?php if(isset($item['Image'])): ?>
                                    <div class="col"><img src="<?= str_replace("Base", "Thumbnail", ".".substr($item['Image'], strpos($item['Image'], "images") - 1)) ?>"></div>
                                <?php else: ?>
                                    <div class="col"></div>
                                <?php endif ?>
                                <div class="col-6"><p><?= $item['Title']?></p></div>
                                <div class="col"><p>$<?= $item['Price']?></p></div>
                            </div>
                        </a>
                    <?php endwhile ?>
                </div>
                <hr>
                <h2>User Reviews</h2>
                <h3>Average User Rating: <?= round($user['Review_Score'], 2)?>/5</h3>
                <hr>
                <div id="review_box">
                <?php if(!$selfProfile && $commentValidationStatement->rowcount() == 0): ?>
                    <div>
                        <form action="viewUser.php?id=<?= $userSearch ?>" method="post">
                            <ul>
                                <li>
                                    <p class="h2">Rate this user</p>
                                    <div class="container">
                                        <div class="row" id="rating-guide">
                                            <div class="col">1</div>
                                            <div class="col">2</div>
                                            <div class="col">3</div>
                                            <div class="col">4</div>
                                            <div class="col">5</div>
                                        </div>
                                    </div>
                                    <input class="form-range" type="range" id="rating" name="rating" min="1" max="5" step="1">
                                </li>
                                <li>
                                    <div class="input-group">
                                        <span class="input-group-text">Content (Optional)</span>
                                        <textarea class="form-control" name="content" id="content" cols="30" rows="3" style="resize: none"></textarea>
                                    </div>
                                </li>
                                <li id="submit-box">
                                    <input class="btn btn-dark" type="submit" value="Post Review" onclick="return confirm('You can only do this once!')">
                                </li>
                            </ul>
                        </form>
                    </div>
                    <?php endif ?>
                    <?php if($commentStatement->rowCount() === 0): ?>
                        <p>User has not been rated yet</p>
                        <hr>
                    <?php else: ?>
                        <div class="container">
                            <?php while($comment = $commentStatement->fetch()): ?>
                                <div class="row">
                                    <div class="col"><p><?= $comment['Rating'] ?>/5</p></div>
                                    <div class="col">
                                         <?php if(isset($comment['Content'])): ?>
                                            <p><?= $comment['Content'] ?></p>
                                        <?php endif ?>
                                    </div>
                                </div>
                            <?php endwhile ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </body>
</html>