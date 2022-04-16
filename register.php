<?php session_start(); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
    </head>
    <body>
        <div class="topnav">
            <a href="index.php">AstonCV</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
        <?php
            require_once('controller/Controller.php');
            $c_reg = new Controller();
            if(isset($_POST['submitted_register'])) {
                $token = filter_input(INPUT_POST, 'register_token', FILTER_SANITIZE_STRING);
                if ($token && $token === $_SESSION['register_token']) {
                    $name = $_SESSION['name'] = $_POST['register_name'];
                    $email = $_SESSION['email'] = $_POST['register_email'];
                    $hash = password_hash($_POST['register_pass'], PASSWORD_DEFAULT);

                    // server-side form validation
                    if(empty($name) || empty($email) || empty($_POST['register_pass']) || empty($_POST['confirm_pass'])) {
                        $_SESSION['msg'] = 'All fields are required.';
                    }
                	else if(strlen($name) > 100 || strlen($email) > 100) {
                    	$_SESSION['msg'] = 'Character limit exceeded in one or more fields.';
                    }
                    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $_SESSION['msg'] = 'Invalid email address.';
                    }
                    else if(strcmp($_POST['register_pass'], $_POST['confirm_pass'])) {
                        $_SESSION['msg'] = 'Both passwords must match.';
                    }
                	else if(!$c_reg->registerPassword());
                    else if($c_reg->hasExistingEmail($email)) {
                        $_SESSION['msg'] = 'This email has already been registered. Please try another email address.';
                    }
                    else {
                        if($id = $c_reg->registerUser($name, $email, $hash)) {
                            header("Location: index.php");
                            return;
                        }
                        $_SESSION['msg'] = "Your email or password is incorrect. Please try again.";
                    }
                }
                header("refresh: 0");
                return;
            }

        ?>
        <h1>Register a new account</h1>
        <?php $_SESSION['register_token'] = md5(uniqid(mt_rand(), true)); ?>
        <form method="post" action="register.php">
            <lavel>Name</label>
            <input type="text" name="register_name" placeholder="John Doe" <?php if(isset($_SESSION['email'])) { echo 'value="' . $_SESSION['name'] . '"'; } ?> ><br>
            <label>Email</label>
            <input type="email" name="register_email" placeholder="john_doe@domain.tld" <?php if(isset($_SESSION['email'])) { echo 'value="' . $_SESSION['email'] . '"'; } ?> ><br>
            <label>Password</label>
            <input type="password" name="register_pass" placeholder="********"><br>
            <label>Confirm password</label>
            <input type="password" name="confirm_pass" placeholder="********"><br>
            <input type="submit" name="register" value="Register">
            <input type="hidden" name="submitted_register" value="true">
            <input type="hidden" name="register_token" value="<?php echo $_SESSION['register_token'];?>">
        </form>
        <?php
            if(isset($_SESSION['msg'])) {
                echo "<pre>{$_SESSION['msg']}</pre>";
                unset($_SESSION['msg']);
            }
			if(isset($_SESSION['email'])) {
                unset($_SESSION['email']);
            }
			if(isset($_SESSION['name'])) {
                unset($_SESSION['name']);
            }
        ?>
    </body>
</html>