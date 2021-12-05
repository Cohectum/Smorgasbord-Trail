<?php

    require("DBConnect.php");
    session_start();
    if (!isset($_SESSION['userId']) && $_SESSION['title'] != "Admin") {
        header("Location: LogIn.php");
        exit();
    }

    $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($get) {
        if (filter_var($get['from'], FILTER_VALIDATE_INT)) {
            if (filter_var($get['to'], FILTER_VALIDATE_INT)) {
                if (isset($get['dismiss'])) {
                    $dismissQuery = "UPDATE reviews SET flagged = false WHERE UserId = {$get['to']} AND Review_User = {$get['from']}";
                    $dismissStatement = $db->prepare($dismissQuery);
                    $dismissStatement->execute();
                }

                if (isset($get['delete'])) {
                    $deleteQuery = "DELETE FROM reviews WHERE UserId = {$get['to']} AND Review_User = {$get['from']}";
                    $deleteStatement = $db->prepare($deleteQuery);
                    $deleteStatement->execute();
                }

                header("Location: ModerateComments.php");
            }
        }
    }

    $query = "SELECT * FROM reviews WHERE flagged = true LIMIT 100";
    $statement = $db->prepare($query);
    $statement->execute();

    $counter = 1

?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Moderation - Comments
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include("Sidebar.php") ?>
            <table class="table table-striped">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Reviewer</th>
                    <th scope="col">Reviewed</th>
                    <th scope="col">Score</th>
                    <th scope="col">Content</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">Dismiss</th>
                    <th scope="col">Delete</th>
                </tr>
            
                <?php while ($comment = $statement->fetch()): ?>
                    <tr>
                        <td scope="col"><?= $counter ?></td>
                        <td scope="col"><?= $comment["Review_User"] ?></td>
                        <td scope="col"><?= $comment["UserId"] ?></td>
                        <td scope="col"><?= $comment["Rating"] ?></td>
                        <td colspan="3"><?= $comment["Content"] ?></td>
                        <td scope="col"><a href="./ModerateComments.php?dismiss&from=<?= $comment["Review_User"] ?>&to=<?= $comment["UserId"] ?>">Dismiss</a></td>
                        <td scope="col"><a href="./ModerateComments.php?delete&from=<?= $comment["Review_User"] ?>&to=<?= $comment["UserId"] ?>">Delete</a></td>
                    </tr>
                <?php
                    $counter ++;
                    endwhile ?>
            </table>
        </div>
    </body>
</html>