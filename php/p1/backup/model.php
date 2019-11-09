<?php
	function dbConnect() {
		$dblocation = "localhost";
		$dbname = "project";
		$dbuser = "root";
		$dbpasswd = "12345";
		$link = mysqli_connect($dblocation, $dbuser, $dbpasswd, $dbname) or die('Error!');
		return $link;
	}
	function sessExit() {
		session_unset();
		
	}
	function printForm() {
		return '	<form id="auth_form" method="POST">
				<input type="text" name="auth_login" id="login" placeholder="Логин">
				<input type="password" name="auth_pass" id="pass" placeholder="Пароль">
				<input type="submit" id="submit_button" value="тык">
			</form>	';
	}
	function userbar($session_user_login) {
		if (isset($session_user_login)) {
			return "Йоу, " . $session_user_login . ". <a href='exit.php'>Выйти</a>";
		}
		else {
			return printForm();
		}	
	}
?>