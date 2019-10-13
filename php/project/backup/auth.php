	<?php
		include 'auth_controller.php';
		
		$errors = array();
		$res = array();
		
		if(!empty($_POST['auth_login']) && !empty($_POST['auth_pass'])) {
			
			$login = $_POST['auth_login'];
			$pass = $_POST['auth_pass'];
	
			$sql = "SELECT * FROM player WHERE player_name = '$login' AND player_pass = '$pass'";
			$user = mysqli_query($link, $sql);
			
			$row = mysqli_fetch_assoc($user);

			session_start();
			$_SESSION['login'] = $row["player_name"];
			$_SESSION['pid'] = $row["player_id"];
			$_SESSION['player_gid'] = $row["player_game_id"];
			
			

			if($_SESSION['pid'] == NULL) array_push($errors, "Error 2");
			
			$res = array(
				"pid" => $_SESSION["pid"],
				"p_gid" => $_SESSION["player_gid"],
				"p_name" => $row["player_name"],
				"errors" => $errors
			);
			
			

		} else {
			array_push($errors, "Parameter is empty");
		}
		
		echo json_encode(array("result" => $res, "error" => $errors));

		
	?>