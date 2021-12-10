<?php
    require __DIR__.'/vendor/autoload.php';
    require("DBConnect.php");
    session_start();
    if (!isset($_SESSION['userId']) && $_SESSION['title'] != "Admin") {
        header("Location: LogIn.php");
        exit();
    }

    $message_flag = false;
    $message = "";

    $query = "SELECT * FROM users ORDER BY userId DESC";
    $statement = $db->prepare($query);
    $statement->execute();

    if (isset($_GET['Delete'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $deleteQuery = "DELETE FROM users WHERE UserId = {$id}";
        $deleteStatement = $db->prepare($deleteQuery);
        $deleteStatement->execute();

        header("Location: AdminUserView.php?Success&id=$id");
    }

    if (isset($_GET['Success'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $message_flag = true;
        $message = "User $id has been Successfully Deleted";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Moderation - Users</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('sidebar.php') ?>
            <div id="user_box" class="container">
                <?php if ($message_flag): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif ?>
                <div class="row unstyled">
                    <p class="display-6">All Registered Users (Newest to Oldest)</p>
                    <a class="btn btn-dark" href="./Register.php?AdminRD">Create New User</a>
                </div>
                <div class="row">
                    <table class="table table-striped">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">UserName</th>
                            <th scope="col">Title</th>
                            <th scope="col">Email</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                        <?php while ($user = $statement->fetch()): ?>
                            <tr>
                                <td><?= $user['UserId']?></td>
                                <td><a href="viewUser.php?id=<?=$user['UserId']?>"><p class="h5"><?= $user['Username'] ?></p></a></td>
                                <td><?= $user['Title']?></td>
                                <td><?= $user['Email'] ?></td>
                                <td><a class="btn btn-primary" href="./EditProfile.php?id=<?= $user['UserId'] ?>">Edit</a></td>
                                <td><a class="btn btn-dark" href="./AdminUserView.php?Delete&id=<?= $user['UserId'] ?>" onclick="return confirm('Are you sure you wish to delete this user?')">Delete</a></td>
                            </tr>      
                        <?php endwhile ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>