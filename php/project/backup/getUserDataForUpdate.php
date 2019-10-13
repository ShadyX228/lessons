<?php

	$errors = array();

	session_start();
	if (!empty($_SESSION["pid"])){
		
		$res = array(
			"pid" => $_SESSION["pid"],
			"p_gid" => $_SESSION["player_gid"],
			"errors" => $errors
		);
		echo json_encode($res);
	}
?>