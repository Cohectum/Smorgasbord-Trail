<?php
    session_start();
    require("DBconnect.php");
    require __DIR__.'/vendor/autoload.php';

    $admin = isset($_GET['AdminRD']);

    if (isset($_SESSION['userId']) && !isset($_GET['AdminRD'])) {
        header("Location: viewUser.php");
    }

    $error_flag = false;
    $error_message = "";

    //4.3
    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($post && $post['submit'] == "Register") {
        if (empty($post['username']) ||
            empty($post['email']) ||
            empty($post['password']) ||
            empty($post['password_confirmation']) ||
            ctype_space($post['username']) ||
            ctype_space($post['email']) ||
            ctype_space($post['password']) ||
            ctype_space($post['password_confirmation'])) {
            $error_flag = true;
            $error_message = "All Fields are Required";
        } else {
            $username = $post['username'];
            $email = $post['email'];
            $password = $post['password'];
            $confirmation = $post['password_confirmation'];

            //4.1
            if (strlen($username) > 100) {
                $error_flag = true;
                $error_message = "Username is too long, max size is 100 characters";
            }

            if (strlen($email) > 255) {
                $error_flag = true;
                $error_message = "Email is too long, max size is 255 characters";
            }

            if (strlen($password) < 8) {
                $error_flag = true;
                $error_message = "Password must be at least 8 characters";
            }

            if (strlen($password) > 255) {
                $error_flag = true;
                $error_message = "Password too long, max length is 255 characters";
            }

            if ($password === $confirmation && !$error_flag) {
                $query = "INSERT INTO users (Username, Email, Password) VALUES (:username, :email, :password)";
                //7.3 Passwords are hashed and salted
                $values = [":username" => $username, ":email" => $email, ":password" => password_hash($password, PASSWORD_DEFAULT)];
                $statement = $db->prepare($query);
                $statement->execute($values);

                header($admin ? "Location: AdminUserView.php" : "Location: Index.php?Registered");
            } else {
                $error_flag = true;
                $error_message = "Your Passwords do not match, Please Try Again";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Smorgasbord Trail - Register</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include('Sidebar.php') ?>
            <div id="login_box" class="container d-flex flex-column align-items-center">
                <?php if ($error_flag): ?>
                    <h3 id="error_message"><?= $error_message ?></h3>
                <?php endif ?>
                <div class="row unstyled">
                        <p class="display-5"><?= $admin ? "Add User" : "Register" ?></p>
                </div>
                <form class="row" action="register.php<?= $admin ? "?AdminRD" : "" ?>" method="post">    
                    <ul id="registration_form">
                        <li>
                            <label for="username">Username:</label>
                            <input class="form-control" type="text" id="username" name="username">
                        </li>
                        <li>
                            <label for="email">Email:</label>
                            <input class="form-control" type="email" id="email" name="email">
                        </li>
                        <li>
                            <label for="password">Password:</label>
                            <input class="form-control" type="password" id="password" name="password">
                        </li>
                        <li>
                            <label for="password_confirmation">Confirm Password:</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation">
                        </li>
                        <li id="login_button_box">
                            <input class="btn btn-primary" type="submit" id="registration_button" name="submit" value="Register">
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>