<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <div class="topnav">
        <a href="index.php">AstonCV</a>
        <?php if(isset($_SESSION['auth'])) {
                $toks = explode("@", $_SESSION['email']);
                $username = $toks[0];
                echo "
                	<a href=\"account.php\">Account($username)</a>
                	<a href=\"logout.php\">Logout</a>";
            }
            else {
                echo "
                	<a href=\"login.php\">Login</a>
                	<a href=\"register.php\">Register</a>";
            }
        ?>
    </div>
    <h1>Find CVs</h1>
    <?php
        if(isset($_SESSION['msg'])) {
            echo "<p>{$_SESSION['msg']}</p>";
            unset($_SESSION['msg']);
        }
        if(!isset($_SESSION['auth'])) {
            session_destroy();
        }
    ?>
    <div id="search_bar">
        <form method="post" action="">
            <input type="text" name="query" placeholder="Search database">
            <select name="filter"> <!-- filter resets every time.. annoying. -->
                <option value="1">By name</option>
                <option value="2">By programming language</option>
                <option value="3">By name/programming language</option>
            </select>
            <input type="submit" name="button" value="Search">
            <input type="hidden" name="queried" value="true">
        </form>
    </div>
    <div class="cv_results">
        <?php
            if($records != null) {
                echo "<table><form action=\"more.php\" method=\"get\">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programming Language</th>
                        </tr>";
                foreach($records as $user) {
                    echo "<tr>";   
                    echo "<th><button type=\"submit\" name=\"id\" value=\"$user->id\">" . $user->id . "</button></th>";
                    echo "<th>" . $user->name . "</th>";
                    echo "<th>" . $user->email . "</th>";
                    echo "<th>" . $user->keyprogramming . "</th>";
                    echo "</tr>";
                }
                echo "</form></table>";
            }
            else {
                echo "<p><a href=\"index.php\">(<<)</a><em>No results found.</em></p>";
            }
        ?>
    </div>
</body>
    
</html>