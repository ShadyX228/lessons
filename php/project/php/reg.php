<?php
	include 'model.php';		
	$errors = array();
	$res = array();	
	
	$login = $_POST['reg_login'];
	$pass = $_POST['reg_pass'];
	$confirm_pass = $_POST['reg_confirm_pass'];
	
	if((!empty($login) && !empty($pass) && !empty($confirm_pass)) && ($pass == $confirm_pass)) {
		$check = 0;
		$logins = selectKeysFromQueryArray($link, "SELECT player_name FROM player", "player_name");
		foreach($logins as $val) {
			if($val == $login) {
				$check = 1;
				break;
			}
		}
		if($check == 0) {
			$insert_user = "INSERT INTO player (player_id, player_name, player_game_id, player_color, player_steps, player_pass) VALUES (NULL, '$login', NULL, NULL, NULL, $pass)";
			if(mysqli_query($link, $insert_user) === TRUE) {
				array_push($res, $login, $pass);
				array_push($errors, "Успешно.");
			}
		} else {
			array_push($errors, "Ошибка. Выберите другой ник.");
		}
	} else {
		array_push($errors, "Ошибка. Проверьте введенные данные.");
	}		
	echo json_encode(array("result" => $res, "error" => $errors));		
?>