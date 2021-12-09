<?php
    require('DBConnect.php');
    require __DIR__.'/vendor/autoload.php';
    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }

    if (!isset($_GET['id'])) {
        header("Location: Index.php?404");
    }

    $get = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($_SESSION['title'] != "Admin" && $get != $_SESSION['userId']) {
        header("Location: Index.php?404");
    }

    $error_flag = false;
    $error_message = "";
    $message_flag = false;
    $message = "";

    $query = 'SELECT * FROM users WHERE UserId = :id';
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $get);
    $statement->execute();

    if ($statement->rowcount() == 0) {
        header("Location: Index.php?404");
    }

    $user = $statement->fetch();

    if (isset($_GET['EditSuccess'])) {
        $message_flag = true;
        $message = "Profile Successfully Edited";
    }

    if (isset($_POST['submit'])) {
        if ($_POST['submit'] == "Update") {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            if ($post['username'] != $user['Username']) {
                $existsQuery = "SELECT * FROM users WHERE Username = '{$post['username']}'";
                $existsStatement = $db->prepare($existsQuery);
                $existsStatement->execute();
                
                if ($existsStatement->rowcount() !=  0) {
                    $error_flag = true;
                    $error_message = "That Username already exists, try another";
                }
        
                if (strlen($post['username']) > 100) {
                    $error_flag = true;
                    $error_message = "That Username is too long! max is 100 characters";
                }
            }
    
            if (strlen($post['email']) > 255) {
                $error_flag = true;
                $error_message = "That Email is too long! max is 255 characters";
            }


    
            if(!$error_flag){
                $updateQuery = "UPDATE users SET Username = :username, Email = :email WHERE UserId = {$get}";
                $updateStatement = $db->prepare($updateQuery);
                $updateStatement->bindValue(':username', $post['username']);
                $updateStatement->bindValue(':email', $post['email']);
                $updateStatement->execute();
    
                header("Location: EditProfile.php?EditSuccess&id=".$get);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Smorgasbord Trail - Edit Profile
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('Sidebar.php'); ?>
            <div class="container">
                <?php if($error_flag): ?>
                    <div class="alert alert-warning"><?= $error_message ?></div>
                <?php endif; ?>
                <?php if($message_flag): ?>
                    <div class="alert alert-success"><?= $message?></div>
                <?php endif; ?>
                <div class="unstyled row">
                    <p class="display-6">Edit Profile</p>
                </div>
                <div class="row">
                    <form action="EditProfile.php?id=<?= $get ?>" method="post">
                        <ul class="list-unstyled">
                            <li>
                                <label class="form-label" for="username">Username</label>
                                <input name="username" id="username" class="form-control" type="text" value="<?= $user['Username'] ?>">
                            </li>
                            <li>
                                <label for="email">Email</label>
                                <input name="email" id="email" class="form-control" type="text" value="<?= $user['Email'] ?>">
                            </li>
                            <li id="login_button_box">
                                <input name="submit", id="submit" class="btn btn-primary" type="submit" value="Update">
                                <input class="btn btn-secondary" type="submit" value="Update Password (WIP)" style="width: 200px;">
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>


