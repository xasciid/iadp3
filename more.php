<!DOCTYPE html>
<html>
    <head>
        <title>More</title>
    </head>
    <body>
    <a href="index.php">Back</a>
    <?php
        session_start();
        include_once('conndb.php');
        $temp = $db->quote($_GET['id']);
        $rows = $db->query("SELECT * FROM cvs WHERE id = " . $temp . ";"); 
        $user = $rows->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>ID:</strong> " . $user["id"] . "</p>";
        echo "<p><strong>Name:</strong> " . $user["name"] . "</p>";
        echo "<p><strong>Email:</strong> " . $user["email"] . "</p>";
        echo "<p><strong>Programming Language:</strong> " . (($user["keyprogramming"] != NULL) ? $user["keyprogramming"] : "NULL") . "</p>";
        echo "<p><strong>Profile:</strong> " . (($user["profile"] != NULL) ? $user["profile"] : "NULL") . "</p>";
        echo "<p><strong>Education:</strong> " . (($user["education"] != NULL) ? $user["education"] : "NULL") . "</p>";
        echo "<p><strong>URL Links:</strong> " . (($user["URLlinks"] != NULL) ? $user["URLlinks"] : "NULL") . "</p>";
        $db = NULL;
    ?>
    </body>
</html>