<?php

    require("DBConnect.php");
    session_start();
    if (!isset($_SESSION['userId'])) {
        header("Location: LogIn.php");
    }

    if (filter_input(INPUT_GET, "from", FILTER_VALIDATE_INT) && filter_input(INPUT_GET, "to", FILTER_VALIDATE_INT)) {
        $from = filter_input(INPUT_GET, "from", FILTER_VALIDATE_INT);
        $to = filter_input(INPUT_GET, "to", FILTER_VALIDATE_INT);

        $query = "UPDATE reviews SET flagged = 1 WHERE Review_User = {$from} && UserId = {$to}";
        $statement = $db->prepare($query);
        $statement->execute();
    }

    header("Location: ViewUser.php?flagged&id=$to");
    exit();
