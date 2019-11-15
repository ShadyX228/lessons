<?php
	include 'game_controller.php';
	
	$sql = "SELECT * FROM game WHERE game_status = 0 LIMIT 0,1"; // выбрали всю таблицу с играми
	$games = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($games);

// сделать случай: сессии есть, но у них не статус 0. требуется создать новую сессию со статусом 0
	
	if(empty($row)) { // посмотрели, есть ли у нас созданные игры вообще
		// в данном случае их нет
		// тогда создаем игру и помещаем юзверя, что нажал кнопку
		$create_game = "INSERT INTO game (game_id, game_status, game_time_begin, game_last_step_time, game_step, game_players) VALUES (NULL, 0, NULL, NULL, NULL, 1)";
		
		print 'Проверяю... ';
		
		if(mysqli_query($link, $create_game) === TRUE) {
			print 'Игра создана.';
			
			$select_lobby = "SELECT * FROM game";
			$game = mysqli_query($link, $select_lobby);
			$row = mysqli_fetch_assoc($game);
			$gid = $row["game_id"];
			
			$create_lobby = "UPDATE player SET player_game_id = $gid WHERE player.player_id = $pid";
			if(mysqli_query($link, $create_lobby) === TRUE)
				print printLog('Номер сессии пользователя: '.$gid);
			$_SESSION["player_gid"] = $gid;
		}
		else {
			print 'Ошибка БД.';
		}	
	}
	else { // если массив не пуст, возможны 2 варианта
		// сессия закреплена за пользователем и нам остается перевести его туда
		// либо сессии есть, но пользователю ни одна не назначена
		// и нам нужно найти первую сессии со status = 0 и соединить пользователя с ней
		$row2 = $row;
		$sql = "SELECT * FROM player WHERE player_id = $pid";
		$player = mysqli_query($link, $sql);
		$row = mysqli_fetch_assoc($player);
		if($row["player_game_id"] == NULL){ // случай, когда юзверю сессия не назначена
			//$select_lobby = "SELECT * FROM game WHERE game_status = 0 LIMIT 1";
			//$game = mysqli_query($link, $select_lobby);
			//$row = mysqli_fetch_assoc($game);
			$gid = $row2["game_id"];
			$new_num_p = $row2["game_players"] + 1;

			$login_to_lobby = "UPDATE player SET player_game_id = $gid WHERE player.player_id = $pid";
			$update_players_num = "UPDATE game SET game_players = $new_num_p WHERE game_id = $gid";
			mysqli_query($link, $update_players_num);
			if(mysqli_query($link, $login_to_lobby) === TRUE)
				print 'Сессия назначена. Номер вашей сессии: '.$gid.'';
			$_SESSION["player_gid"] = $gid;
		}
		else { // случай, когда юзверю сессия назначена
			$gid = $_SESSION["player_gid"];
			$sql = "SELECT * FROM game WHERE game_id = $gid";
			$game = mysqli_query($link, $sql);
			$row = mysqli_fetch_assoc($game);
			$num_players = $row["game_players"];
			if($num_players < 5) {
				$num_players = 5 - $num_players;
				print 'Номер вашей активной сессии: '.$_SESSION["player_gid"].'. Ждем еще '. $num_players . ' игроков.';
			}
			else if($num_players == 5) {// основной блок, где будем разворачивать игровое поле 
				$gid = $_SESSION["player_gid"];
				print 'Номер вашей активной сессии: '.$_SESSION["player_gid"].'. Игра началась.';
				$update_stat = "UPDATE game SET game_status = 1 WHERE game_id = $gid";
				mysqli_query($link, $update_stat);
			}
		}
	}
?>