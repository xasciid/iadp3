<?php session_start(); require_once('controller/Controller.php'); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <div class="topnav">
            <a href="index.php">AstonCV</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
        <?php 
			$c_login = new Controller();
            if(isset($_POST['submitted_login'])) {
            	$token = filter_input(INPUT_POST, 'login_token', FILTER_SANITIZE_STRING);
                if ($token && $token === $_SESSION['login_token']) {
                	$email = $_SESSION['email'] = $_POST['login_email'];
                	$password = $_POST['login_pass'];

                	if(empty($email) || empty($password)) {
                    	$_SESSION['msg'] = "All fields are required.";
                	}
                	else {
                    	if($c_login->authenticateUser($email, $password)) {
                        	$_SESSION['msg'] = "You are now logged in.";
                        	//$_SESSION['jwt'] = $c_login->setJWT();
                        	header("Location: index.php");
                        	exit;
                    	}
                    	$_SESSION['msg'] = "Something went wrong. Please wait and try again.";
                	}
                	header("refresh: 0");
                	exit;
                }

            }
        ?>
        <h1>Login to your account</h1>
        <?php $_SESSION['login_token'] = md5(uniqid(mt_rand(), true)); ?>
        <form method="post" action="login.php">
            <label>Email</label>
            <input type="email" name="login_email" placeholder="john_doe@domain.tld" <?php if(isset($_SESSION['email'])) { echo 'value="' . $_SESSION['email'] . '"'; } ?> ><br>
            <label>Password</label>
            <input type="password" name="login_pass" placeholder="********"><br>
            <input type="submit" name="login" value="Login">
            <input type="hidden" name="submitted_login" value="true">
            <input type="hidden" name="login_token" value="<?php echo $_SESSION['login_token'];?>">
        </form>
        <?php
            if(isset($_SESSION['msg'])) {
                echo "<pre>{$_SESSION['msg']}</pre>";
                unset($_SESSION['msg']);
            }
			if(isset($_SESSION['email'])) {
                unset($_SESSION['email']);
            }
        ?>
    </body>
</html>