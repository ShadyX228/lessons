<?php
	$errors = array();
	session_start();
	if (!empty($_SESSION["pid"])){	
		$res = array(
			"pid" => $_SESSION["pid"],
			"p_gid" => $_SESSION["player_gid"],
			"p_name" => $_SESSION["login"],
			"errors" => 1
		);
		if(!empty($_SESSION["game_players"])) {
			$res = array(
				"pid" => $_SESSION["pid"],
				"p_gid" => $_SESSION["player_gid"],
				"p_name" => $_SESSION["login"],
				"errors" => 2,
				"g_players_num" => $_SESSION["game_players"]
			);
		} 
		if(!empty($_SESSION["game_needed_players"])) {
			$res = array(
				"pid" => $_SESSION["pid"],
				"p_gid" => $_SESSION["player_gid"],
				"p_name" => $_SESSION["login"],
				"errors" => 3,
				"g_players_num" => $_SESSION["game_players"],
				"g_players_needed" => $_SESSION["game_needed_players"]
			);
		}
		echo json_encode($res);
	}
?>