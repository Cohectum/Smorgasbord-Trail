<?php
    require('DBConnect.php');

    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }

    $get = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

    $selfProfile = false;

    if($get){
        $userSearch = $get;
    }
    else{
        $userSearch = $_SESSION['userId'];
        $selfProfile = true;
    }

    $query = "SELECT * FROM users WHERE UserId = {$userSearch}";
    $statement =  $db->prepare($query);
    $statement->execute();
    $user = $statement->fetch();

    $commentQuery = "SELECT * FROM reviews WHERE UserId = {$userSearch}";
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
            <div id="user_box">
                <?php if(isset($user['Profile_Picture'])): ?>
                   <img src='./Profile_Pictures/<?=$user['Profile_Picture']?>' alt="Profile Picture">
                <?php endif ?>
                <h2><?= $user['Username']?></h2>
                <hr>
                <h2>User Reviews</h2>
                <h3>User Rating: <?= $user['Review_Score']?>/5</h3>
                <hr>
                <div id="review_box">
                    <?php if($commentStatement->rowCount() === 0): ?>
                        <p>User has not been rated yet, Only users who have interacted with <?= $user['Username']?> can rate them.</p>
                    <?php else: ?>
                        <p><?= $commentStatement->rowCount() ?> Reviews Found</p>
                        <?php while($comment = $commentStatement->fetch()): 
                            $commentUser = "SELECT"    
                        ?>

                        <?php endwhile ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </body>
</html>