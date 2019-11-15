<?php
	//$path = $_SERVER['DOCUMENT_ROOT'];
	//$path .= "/dashobard/project/php/model.php";
	//include_once($path);
	include "../model.php";
	session_start();
	
	$gid = $_SESSION["game_id"];
	$pid = $_SESSION["pid"];
	$unit_id = $_POST['unit_id'];
	$unit_steps = $_POST['unit_steps'];
	$unit_pos_x = $_POST['unit_pos_x'];
	$unit_pos_y = $_POST['unit_pos_y'];
	$unit_count = $_POST['units_count'];
	//echo $unit_id;
	//echo $unit_steps;
	//echo $unit_pos_x;
	//echo $unit_pos_y;
	
	// проверяем, есть ли в бд запись о такой клетке
	// если есть, то она либо занята другим игроком, либо на ней есть ресурсы
	// отдельно нужно будет сделать переход на союзные клетки

	$checkCell = dbQueryArray("SELECT cell_id, player_id, cell_resource_id, cell_under_siege FROM cell WHERE cell_game_id = $gid AND cell_x = $unit_pos_x AND cell_y = $unit_pos_y",$link);
	// пустая клетка, игрок её занимает:
	if($checkCell == null) {
		mysqli_query($link, "INSERT INTO cell (cell_game_id, cell_resource_id, cell_x, cell_y, cell_shield, player_id) VALUES ($gid, 5, $unit_pos_x, $unit_pos_y, 0, $pid)");
		$cellId = mysqli_insert_id($link);
		mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
	} else {
		// не пустая. Возможны варианты
		// клетка принадлежит игроку
		$cellId = $checkCell['cell_id'];
		$cellPid = $checkCell['player_id'];
		if($cellPid == $pid) {
			mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
		} else {
			// чужая клетка
			// берем клетку в осаду, юзверю выводим сообщение.
			$units_player = $unit_count;
			$falls_player;
			
			$query = mysqli_query($link, "SELECT cell_id FROM cell WHERE player_id = $pid");			
			$territory_player = mysqli_num_rows($query);
			
			$query = dbQueryArray("SELECT player_wins FROM player WHERE player_id = $pid",$link);
			$wins_player = $query['player_wins'];
			
			$query = dbQueryArray("SELECT player_falls FROM player WHERE player_id = $pid",$link);
			$falls_player = $query['player_falls'];
			
			$win_player = (1+$wins_player)*$units_player*$territory_player/((1+$falls_player)*1000);
			
			// сделать то же самое для вражеского юнита
			
			// сделать сравнение
			
			// поставить юнит игрока на клетку в случае победы и убить все вражеские
			// если игрок проиграл, то убить юнит, который должен был зайти на клетку
			
			// можно будет запаковать в json сообщение для юзверя
			
			
		}
	}
?>