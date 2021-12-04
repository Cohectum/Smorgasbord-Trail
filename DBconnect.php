<?php

    /*
     * Author: Cameron Foy
     * Date: Sep 28, 2021
     * Purpose: Connects web page to database.
     */

    define('DB_DSN', 'mysql:host=localhost;dbname=smorgasbordtrail;charset=utf8');
    define('DB_USER', 'Cohec');
    define('DB_PASS', '39hKt@GwPH6s^$T');

    try {
        $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        print "Error: " . $e->getMessage();
        die();
    }
