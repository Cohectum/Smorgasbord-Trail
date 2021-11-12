<?php 
  /*
   * Author: Cameron Foy
   * Date: Sep 28, 2021
   * Purpose: Performs Simple Authentication for all create, update and delete functions.
   */

  define('ADMIN_LOGIN','cohec'); 
  define('ADMIN_PASSWORD','Password01'); 

  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) 
      || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN) 
      || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) { 
    header('HTTP/1.1 401 Unauthorized'); 
    header('WWW-Authenticate: Basic realm="Smorgasbord Trail"'); 
    exit("Access Denied: Username and password required."); 
  } 
   
?>
