<?php
    /*
     * Author: Cameron Foy
     * Purpose: Site Login Form
     * Last Updated: 11/22/2021
     */
    session_start();


    require("DBconnect.php");
    $error_flag = false;
    $error_message = "";
    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //If a button is clicked
    if (isset($post['submit'])) {
        
        //Send user to registration
        if ($post['submit'] == "Register") {
            header("Location: Register.php");
        }
    
        //Attempt user Login
        if ($post['submit'] == "Log In") {

            $username = $post['username'];
            $password = $post['password'];

            if (empty($username) || 
                empty($password) ||
                ctype_space($username) ||
                ctype_space($password)) {
                
                $error_flag = true;
                $error_message = "Please Enter a Username and Password";
            }

            $query = "SELECT * FROM users WHERE Username = :username";
            $values = [":username" => $username];
    
            if (!$error_flag) {
                try{
                    $statement = $db->prepare($query);
                    $statement->execute($values);
                } 
                catch (PDOException $e){
                    $error_flag = true;
                    $error_message = "Error Fetching account.";
                    echo "Error Fetching account.";
                    die();
                }

                $user = $statement->fetch();
    
                if ($statement->rowcount() == 1) {
                    //7.3 Password Is Hashed and Salted
                    if (password_verify($password, $user['Password'])) {
                        $_SESSION['userId'] = $user['UserId'];
                        $_SESSION['title'] = $user['Title'];
                        header("Location: index.php?LoginSuccess");
                    }
                    else{
                        $error_flag = true;
                        $error_message = "Your Username or Password is incorrect";
                    }
                }
                else{
                    $error_flag = true;
                    $error_message = "Your Username or Password is incorrect";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Smorgasbord Trail - Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="./main.css">
    </head>
    <body>
        <div id="wrapper">
            <?php include("sidebar.php") ?>
            <div id="login_box">
                <?php if($error_flag): ?>
                        <h3 id="error_message"><?= $error_message ?></h3>
                <?php endif ?>
                <h1>Log In</h1>
                <form action="login.php" method="POST">
                    <ul id="login_form">
                        <li>
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username">
                        </li>
                        <li>
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password">
                        </li>
                        <li id="login_button_box">
                            <input type="submit" id="login" name="submit" value="Log In">
                            <input type="submit" id="register" name="submit" value="Register">
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>