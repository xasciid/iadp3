<?php
    include_once("model/User.php");
    class Controller {
        
        public function invoke() {
            if(isset($_POST["queried"])) {
                // fetches records based on search query
                $records = $this->getCVRecords($_POST['query']);
            }
            else { 
                // empty search returns all records
                $records = $this->getCVRecords('');
            }
            include_once('view/home.php');
        }

        /*
        @return array of records matching search 
            query, no duplicates.
        */
        public function getCVRecords($search_query) {
            include('conndb.php');
            $prefix = "SELECT id, name, email, keyprogramming, profile, education, URLLinks FROM cvs ";
            if(empty(trim($search_query))) {
                $sql_query = $prefix . "ORDER BY id DESC;";
                $rows = $db->query($sql_query);
                return $this->selectUsers($rows);
            }
            else {
                $array = [];
                $keywords = explode(' ', $search_query);
                $sql_query = $prefix . "WHERE ";
                switch($_POST['filter']) {
                    case "1":
                        $sql_query .= "name LIKE ?;";
                        break;
                    case "2":
                        $sql_query .= "keyprogramming LIKE ?;";
                        break;
                    default:
                        $sql_query .= "name LIKE ? OR keyprogramming LIKE ?;";
                }
                try {
                    foreach($keywords as $keyword) {
                    	$mod_keyword = "%" . $keyword . "%";
                        $sth = $db->prepare($sql_query);
                        $sth->bindParam(1, $mod_keyword, PDO::PARAM_STR, !strcmp($_POST['filter'], "1") ? 100 : 255);
                        if(!strcmp($_POST['filter'], "3")) {
                            $sth->bindParam(2, $mod_keyword, PDO::PARAM_STR, 255);
                        }
                        $sth->execute();
                        $users = $this->selectUsers($sth);
                        // temporary solution for 'doubled' array
                        if(!empty($users)) {
                            for($i = 0; $i < count($users); $i++) {
                                $array[] = $users[$i];
                            }
                        }
                    }
                    return array_unique($array, SORT_REGULAR);
                } catch(PDOException $e) {
                    echo "Internal issue; failed to retrieve table data.";
                }
            }
        }

        /*
        @param PDOStatement object
        @param int
        @return array of User objects
        */
        public function selectUsers($rows) {
            if(!($row_count = $rows->rowCount())) {
                return null;
            }
            for($i = 0; $i < $row_count; $i++) {
                $row = $rows->fetch(PDO::FETCH_ASSOC);
                $users[] = new User($row["id"], $row["name"], $row["email"], $row["keyprogramming"], $row["profile"], $row["education"], $row["URLLinks"]);
            }
            return $users;
        }

        /*
        @param String email
        @return int > 0 if email is already registered, 0 otherwise.
        */
        public function hasExistingEmail($email) {
            include("conndb.php");
            $temp = $db->quote($email);
            $rows = $db->query("SELECT email FROM cvs WHERE email = " . $temp . ";");
            $count = $rows->rowCount();
            $db = NULL;
            return $count;
        }

        public function registerUser($name, $email, $hash) {
            include('conndb.php');
            $success = 0;
            try {
                $sth = $db->prepare("INSERT INTO cvs (name, email, password)
                    VALUES (?, ?, ?);");
                $sth->bindParam(1, $name, PDO::PARAM_STR, 100);
                $sth->bindParam(2, $email, PDO::PARAM_STR, 100);
                $sth->bindParam(3, $hash, PDO::PARAM_STR, 255);
                $sth->execute();
                $_SESSION['msg'] = "You are now registered. ID = {$db->lastInsertId()}";
                $success = 1;
            } catch(PDOException $e) {
                $_SESSION['msg'] = "Something went wrong. Please wait and try again.";
            }
            $db = NULL;
            return $success;
        }
    
    	public function registerPassword() {
            define("GREEN", "MediumSeaGreen");
            define("RED", "Tomato");
            $ret = 1;
            $pass = $_POST['register_pass'];
            $req_arr = array(
                'plen' => GREEN,
                'pupp' => GREEN,
                'plow' => GREEN,
                'pnum' => GREEN,
                'psym' => GREEN
            );
            if(strlen($pass) < 8) {
                $req_arr['plen'] = RED;
                $ret = 0;
            }
            if(!preg_match('/[A-Z]/', $pass)){
                $req_arr['pupp'] = RED;
                $ret = 0;
            }
            if(!preg_match('/[a-z]/', $pass)){
                $req_arr['plow'] = RED;
                $ret = 0;
            }
            if(!preg_match('/[0-9]/', $pass)){
                $req_arr['pnum'] = RED;
                $ret = 0;
            }
            if (!preg_match('/[^a-zA-Z0-9]+/', $pass)) {
                $req_arr['psym'] = RED;
                $ret = 0;
            }

            $_SESSION['msg'] = '<pre>Password requirements: 
                <ul>
                    <li style="color:' . $req_arr['plen'] . ';">8 or more characters</li>
                    <li style="color:' . $req_arr['pupp'] . ';">at least one uppercase character</li>
                    <li style="color:' . $req_arr['plow'] . ';">at least one lowercase character</li>
                    <li style="color:' . $req_arr['pnum'] . ';">at least one number</li>
                    <li style="color:' . $req_arr['psym'] . ';">at least one symbol</li>
                </ul></pre>';

            return $ret;
        }

        public function authenticateUser($email, $password) {
            include('conndb.php');
            $success = 0;
            try {
                $temp = $db->quote($email);
                $rows = $db->query("SELECT password FROM cvs WHERE email = " . $temp . ";");
                $row = $rows->fetch(PDO::FETCH_ASSOC);
                if($rows->rowCount() > 0) {
                    if(password_verify($password, $row['password'])) {
                        $token = filter_input(INPUT_POST, 'login_token', FILTER_SANITIZE_STRING);
                        if ($token && $token === $_SESSION['login_token']) {
                            $_SESSION['email'] = $email;
                            $success = $_SESSION['auth'] = 1;
                        }
                    }
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
            $db = NULL;
            return $success;
            
        }

        public function loadAttributes() {
            require_once('conndb.php');
            $temp = $db->quote($_SESSION['email']);
            $rows = $db->query("SELECT * FROM cvs WHERE email = " . $temp . ";");
            $attributes = $rows->fetch(PDO::FETCH_ASSOC);
        	return $attributes;
        }

        public function updateAttributes() {
            // check email, name etc. are valid (use regular expression)
            require_once('conndb.php');
            $success = 1;
            if(!filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL)) {
                $success = 0;
            }
        	else {
	            try {
                	$sth = $db->prepare("UPDATE cvs
                    	SET name = ?, email = ?, keyprogramming = ?, profile = ?, education = ?, URLlinks = ?
                    	WHERE email = ?;");
                	$sth->bindParam(1, $_POST['new_name'], PDO::PARAM_STR, 100);
                	$sth->bindParam(2, $_POST['new_email'], PDO::PARAM_STR, 100);
                	$sth->bindParam(3, $_POST['new_keyprog'], PDO::PARAM_STR, 255);
                	$sth->bindParam(4, $_POST['new_profile'], PDO::PARAM_STR, 500);
                	$sth->bindParam(5, $_POST['new_education'], PDO::PARAM_STR, 500);
                	$sth->bindParam(6, $_POST['new_urls'], PDO::PARAM_STR, 500);
                	$sth->bindParam(7, $_SESSION['email'], PDO::PARAM_STR, 100);
                	$sth->execute();
                	$_SESSION['msg'] = "Your account has been updated.";
            	} catch(PDOException $e) {
                	$success = 0;
            	}
            }
            if(!$success) {
            	$_SESSION['msg'] = "Something went wrong. Please wait and try again.";
            }
            $db = NULL;
            return $success;
        }
    }

?>