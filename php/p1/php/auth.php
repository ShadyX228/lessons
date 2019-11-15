<?php
	include 'model.php';		
	$errors = array();
	$res = array();	
	
	if(!empty($_POST['auth_login']) && !empty($_POST['auth_pass'])) {	
		$login = $_POST['auth_login'];
		$pass = $_POST['auth_pass'];	
		$row = dbQueryArray("SELECT * FROM player WHERE player_name = '$login' AND player_pass = '$pass'",$link);
		
		session_start();
		$_SESSION['login'] = $row["player_name"];
		$_SESSION['pid'] = $row["player_id"];
		$_SESSION['player_gid'] = $row["player_game_id"];		
		if($_SESSION['pid'] == NULL) {
			array_push($errors, "Пользователь не найден. Проверьте введенные данные.");		
		}
		$res = array(
			"pid" => $_SESSION["pid"],
			"p_gid" => $_SESSION["player_gid"],
			"p_name" => $row["player_name"],
			"errors" => $errors
		);
	} else {
		array_push($errors, "Введите данные полностью.");
	}		
	echo json_encode(array("result" => $res, "error" => $errors));		
?>