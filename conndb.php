<?php
    try {
        $db = new PDO("mysql:dbname=u_200219998_db;host=localhost;", "u-200219998", "eQkPde3XGHj3Rxr");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo $e->getMessage();
        exit;
    }
?>