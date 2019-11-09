<?php
	include 'model.php';
	session_start(); 
	$errors = array();
	$res = array();	

	$game_row = dbQueryArray('SELECT * FROM game',$link);
	if(isset($_SESSION["pid"]))
		$pid = $_SESSION["pid"];
	
	if(empty($game_row) && isset($_SESSION["pid"])) {
		// посмотрели, есть ли у нас созданные игры вообще
		// в данном случае их нет
		// тогда создаем игру и помещаем туда юзверя, что нажал кнопку
		$create_game = "INSERT INTO game (game_id, game_status, game_time_begin, game_last_step_time, game_step, game_players) VALUES (NULL, 0, NULL, NULL, NULL, 1)";
		if(mysqli_query($link, $create_game) === TRUE) {
			$game_row = dbQueryArray('SELECT * FROM game WHERE game_status = 0 LIMIT 0,1',$link);	
			$gid = $game_row["game_id"];	
			
			$create_lobby = "UPDATE player SET player_game_id = $gid WHERE player.player_id = $pid";
			mysqli_query($link, $create_lobby);
			
			$_SESSION["player_gid"] = $gid;
			$_SESSION["game_id"] = $gid;
			$_SESSION["game_status"] = 0;
			$_SESSION["game_players"] = 1;
			$res = array(
				"g_id" => $_SESSION["game_id"],
				"g_stat" => $_SESSION["game_status"],
				"g_players" => $_SESSION["game_players"]
			);
			
			
			array_push($errors, "-1");
			
			
		} else {
			array_push($errors, "Ошибка БД.");
		}	
	} else if(isset($_SESSION["pid"])) {
		// если массив не пуст, возможны 2 варианта
		// сессия закреплена за пользователем и нам остается перевести его туда
		// либо сессии есть, но пользователю ни одна не назначена
		// и нам нужно найти первую сессии со status = 0 и соединить пользователя с ней
		$player_row = dbQueryArray("SELECT * FROM player WHERE player_id = $pid",$link);
		if($player_row["player_game_id"] == NULL) {
			// случай, когда юзверю сессия не назначена	
			// важный момент. При непустом массиве $game_row у нас может не быть сессий со status = 0
			// в этом случае нужно сделать проверку: если у нас есть сессии со status = 0, то переводим
			// юзверя в неё; иначе создаем сессию с таким status и нулевым количеством игроков и переводим юзверя туда
			$game_row = dbQueryArray('SELECT * FROM game WHERE game_status = 0 LIMIT 0,1',$link);			
			if(empty($game_row)) {
				$create_game = "INSERT INTO game (game_id, game_status, game_time_begin, game_last_step_time, game_step, game_players) VALUES (NULL, 0, NULL, NULL, NULL, 0)";
				mysqli_query($link, $create_game);
				$game_row = dbQueryArray('SELECT * FROM game WHERE game_status = 0 LIMIT 0,1',$link);
			}		
			
			$gid = $game_row["game_id"];
			$new_player = $game_row["game_players"] + 1;
			$login_to_lobby = "UPDATE player SET player_game_id = $gid WHERE player.player_id = $pid";
			$update_players_num = "";
			if($new_player != 5) {
				$update_players_num = "UPDATE game SET game_players = $new_player WHERE game_id = $gid";
				$_SESSION["game_status"] = 0;
			} else {
				$update_players_num = "UPDATE game SET game_players = $new_player, game_status = 1 WHERE game_id = $gid";
				$_SESSION["game_status"] = 1;
			}
			mysqli_query($link, $update_players_num);
			mysqli_query($link, $login_to_lobby);
			$_SESSION["player_gid"] = $gid;
			$_SESSION["game_id"] = $gid;
			$_SESSION["game_players"] = $new_player;
			$res = array(
				"g_id" => $_SESSION["game_id"],
				"g_stat" => $_SESSION["game_status"],
				"g_players" => $_SESSION["game_players"]
			);
			
			
			array_push($errors, "-2");
			
			
		} else {
			// случай, когда юзверю сессия назначена
			$gid = $_SESSION["player_gid"];
			$game_row = dbQueryArray("SELECT * FROM game WHERE game_id = $gid",$link);
			$num_players = $game_row["game_players"];
			$_SESSION["game_players"] = $game_row["game_players"];
			if($num_players < 5) {
				$num_players = 5 - $num_players;
				$_SESSION["game_id"] = $gid;
				$_SESSION["game_status"] = 0;
				$_SESSION["game_needed_players"] = $num_players;
				$res = array(
					"g_id" => $_SESSION["game_id"],
					"g_stat" => $_SESSION["game_status"],
					"g_players" => $_SESSION["game_players"],
					"g_needed_players" => $_SESSION["game_needed_players"]
				);
				
				
				array_push($errors, "-3");
				
				
			} else if($num_players == 5) {
				$gid = $_SESSION["player_gid"];
				$update_stat = "UPDATE game SET game_status = 1 WHERE game_id = $gid";
				mysqli_query($link, $update_stat);
				$_SESSION["game_id"] = $gid;
				$_SESSION["game_status"] = 1;
				$res = array(
					"g_id" => $_SESSION["game_id"],
					"g_stat" => $_SESSION["game_status"],
					"g_players" => $_SESSION["game_players"]
					// тут допилить вывод даты начала игры в сессию
				);
				
				
				array_push($errors, "-4");
				
				
			}
		}
	}
	echo json_encode(array("result" => $res, "error" => $errors));
?>