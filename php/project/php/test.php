<?php
		include 'model.php';
		$login = $_GET['reg_login'];
		$check = 0;
		$logins = selectKeysFromQueryArray($link, "SELECT player_name FROM player", "player_name");
		foreach($logins as $val) {
			if($val == $login) {
				$check = 1;
				break;
			}
		}
		//var_dump(selectKeysFromQueryArray($link, "SELECT player_name FROM player", "player_name"));
		echo $check;
?>