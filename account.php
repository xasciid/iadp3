<?php session_start(); require_once('controller/Controller.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Account</title>
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
        <?php 
            $c_acc = new Controller();
            if(!isset($_SESSION['auth'])) {
                $_SESSION['msg'] = 'Please login to access the account page.';
                header('Location: index.php');
                exit;
            }
            else {
                if(isset($_POST['saved'])) {
                    $token = filter_input(INPUT_POST, 'account_token', FILTER_SANITIZE_STRING);
                    if ($token && $token === $_SESSION['account_token']) {
                        $c_acc->updateAttributes();
                        header('Location: index.php');
                        exit;
                    }
                }
                else {
                    $attributes = $c_acc->loadAttributes();
                }
            }
        ?>
        <main>
            <h1>Account</h1>
            <?php $_SESSION['account_token'] = md5(uniqid(mt_rand(), true)); ?>
            <form method="post" action="account.php">
                <label>Name</label>
                <input type="text" name="new_name" value="<?php echo $attributes["name"] ?>"><br>
                <label>Email</label>
                <input type="text" name="new_email" value="<?php echo $attributes["email"] ?>"><br>
                <label>Programming Language</label>
                <input type="text" name="new_keyprog" value="<?php echo $attributes["keyprogramming"] ?>"><br>
                <label>Profile</label>
                <input type="text" name="new_profile" value="<?php echo $attributes["profile"] ?>"><br>
                <label>Education</label>
                <input type="text" name="new_education" value="<?php echo $attributes["education"] ?>"><br>
                <label>URL links</label>
                <input type="text" name="new_urls" value="<?php echo $attributes["URLlinks"] ?>"><br>
                <input type="submit" name="save_changes" value="Save changes">
                <input type="hidden" name="saved" value="true">
                <input type="hidden" name="account_token" value="<?php echo $_SESSION['account_token'];?>">
            </form>
        </main>
    </body>
</html>