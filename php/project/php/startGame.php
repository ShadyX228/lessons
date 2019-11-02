<?php
	include 'model.php';		
	$errors = array();
	$res = array();	
	session_start(); 
	//print_r($_SESSION);
	$gid = $_SESSION["game_id"];
	$pid = $_SESSION["pid"];
	$check = dbQueryArray("SELECT game_field_is_painted FROM game WHERE game_id = $gid",$link);
	// если игра началась, то рисуем таблицу, иначе нет смысла
	if($_SESSION["game_status"] == 1 && $check["game_field_is_painted"] == 0) {
		// Игра началась. Рисуем клетки.
		$fieldSize = 9;
		$playersStartCell = array(
			array(intval($fieldSize-$fieldSize/2-1), 0),
			array(0, intval($fieldSize-$fieldSize/2)),
			array(intval($fieldSize-$fieldSize/2-1), $fieldSize-1),
			array($fieldSize-1, $fieldSize-3),
			array($fieldSize-1, 2)
		);
		// определяем цвет игрока
		$user = dbQueryArrayFull("SELECT player_id FROM player WHERE player_game_id = $gid",$link);
		$player_color = random_color();
		
		mysqli_query($link, "UPDATE player SET player_color = \"$player_color\" WHERE player.player_id = $pid;");
		// распределяем игроков по карте
		for($i = 0;$i<5;$i++) {
			$pid_current = $user[$i][0];
			$x = $playersStartCell[$i][0];
			$y = $playersStartCell[$i][1];
			mysqli_query($link,"INSERT INTO cell (cell_game_id, cell_resource_id, cell_x, cell_y, cell_shield, player_id) VALUES ($gid, 5, $x, $y, 0, $pid_current)");
		}
		// распределение ресурсов по карте
		
		
		// добавляем 3 юнита игроку (по правилам так), которые могут сделать 3 хода за кон
		$cell_id_query = dbQueryArray("SELECT cell_id FROM cell WHERE cell.player_id = $pid",$link);
		$cell_id = $cell_id_query["cell_id"];
		for($i = 1; $i <= 3; $i++) {
			mysqli_query($link,"INSERT INTO unit (unit_player_id, unit_cell_id, unit_steps) VALUES ($pid, $cell_id, 3)");
		}
		// поле отрисовано, игроки распределены. Это значение будет использовано при дальнейшем обращении к этой странице
		mysqli_query($link,"UPDATE game SET game_field_is_painted = 1 WHERE game_id = $gid");
	}
	else {
		array_push($errors, "-5");
	}
	$cell_color = dbQueryArray("SELECT player_color FROM player WHERE player_id = $pid",$link);
	$query = dbQueryArrayFull("SELECT cell_id, cell_x, cell_y, cell_shield, cell_resource_id FROM cell WHERE player_id = $pid",$link);
	$units = dbQueryArrayFull("SELECT unit_id, unit_cell_id, unit_steps FROM unit WHERE unit_player_id = $pid",$link);
	
	//var_dump($query);
	$res = array(
		"cells" => $query,
		"cell_color" => $cell_color["player_color"],
		"units" => $units,
		"check_field" => $check['game_field_is_painted']
	);
	$_SESSION["field_is_printed"] = 1;
	//var_dump($res);
	echo json_encode(array("result" => $res, "error" => $errors));		
?>