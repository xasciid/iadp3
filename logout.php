<?php
    session_start();
    foreach(array_keys($_SESSION) as $key) {
        unset($_SESSION[$key]);
    }
    $_SESSION['msg'] = "You have logged out.";
    header('Location: index.php');
    // (session_destroy() is in index.php->home.php to 
    //  communicate final flash message)
?>