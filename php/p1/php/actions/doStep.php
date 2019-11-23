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
	$res = array();	
	
	// проверяем, есть ли в бд запись о такой клетке
	// если есть, то она либо занята другим игроком, либо на ней есть ресурсы
	// отдельно нужно будет сделать переход на союзные клетки

	$checkCell = dbQueryArray("SELECT cell_id, player_id, cell_resource_id, cell_under_siege FROM cell WHERE cell_game_id = $gid AND cell_x = $unit_pos_x AND cell_y = $unit_pos_y",$link);
	// пустая клетка, игрок её занимает:
	if($checkCell == null) {
		mysqli_query($link, "INSERT INTO cell (cell_game_id, cell_resource_id, cell_x, cell_y, cell_shield, player_id) VALUES ($gid, 5, $unit_pos_x, $unit_pos_y, 0, $pid)");
		$cellId = mysqli_insert_id($link);
		mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
		array_push($res,"Клетка занята вами");	
	} else {
		// не пустая. Возможны варианты
		// клетка принадлежит игроку
		$cellId = $checkCell['cell_id'];
		$cellPid = $checkCell['player_id'];
		if($cellPid == $pid) {
			mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
			array_push($res,"Переход на свою клетку");	
		} else if($cellPid == null) {
			// клетка с ресурсами, не занятая
			// проверить, что на клетке за ресурс и дать игроку бонусы
			// тут нужно вызвать функцию, которая назначит ресурс и выдаст игроку бонус от  него
			
			array_push($res,"Клетка занята вами, получен бонус от ресурса");	
		}
		else {
			// чужая клетка
			// определяем победителя
			$units_player = $unit_count;
			
			$query = mysqli_query($link, "SELECT cell_id FROM cell WHERE player_id = $pid");			
			$territory_player = mysqli_num_rows($query);
			
			$query = dbQueryArray("SELECT player_wins FROM player WHERE player_id = $pid",$link);
			$wins_player = $query['player_wins'];
			
			$query = dbQueryArray("SELECT player_falls FROM player WHERE player_id = $pid",$link);
			$falls_player = $query['player_falls'];
			
			$win_player = (1+$wins_player)*$units_player*$territory_player/((1+$falls_player)*1000);
		
			// сделать то же самое для вражеского юнита
			$query = mysqli_query($link, "SELECT unit_id FROM unit WHERE unit_player_id = $cellPid");	
			$units_enemy = mysqli_num_rows($query);
			
			$query = mysqli_query($link, "SELECT cell_id FROM cell WHERE player_id = $cellPid");			
			$territory_enemy = mysqli_num_rows($query);
			
			$query = dbQueryArray("SELECT player_wins FROM player WHERE player_id = $cellPid",$link);
			$wins_enemy = $query['player_wins'];
			
			$query = dbQueryArray("SELECT player_falls FROM player WHERE player_id = $cellPid",$link);
			$falls_enemy = $query['player_falls'];
			
			$win_enemy = (1+$wins_enemy)*$units_enemy*$territory_enemy/((1+$falls_enemy)*1000);
			
			//echo $win_player . " " . $win_enemy;
			
			// сделать сравнение
			if($win_player > $win_enemy) { // победа
				mysqli_query($link, "DELETE FROM unit WHERE unit_cell_id = $cellId AND unit_player_id = $cellPid"); // убиваем вражеские
				mysqli_query($link, "UPDATE cell SET player_id = $pid WHERE cell_id = $cellId;"); // передаем клетку победителю
				mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId WHERE unit_id = $unit_id"); // двигаем юнита на клетку
			
				mysqli_query($link, "UPDATE unit SET unit_steps = $unit_steps WHERE unit_id = $unit_id");
				
				// увеличить счетики побед и поражений победилея и проигравшего
				$wins_player++;
				mysqli_query($link, "UPDATE player SET player_wins = $wins_player WHERE player_id = $pid");			
				$falls_enemy++;
				mysqli_query($link, "UPDATE player SET player_falls = $falls_enemy WHERE player_id = $cellPid");
				
				array_push($res,"Вражеская клетка. Победа");							
															
			} else if($win_player == $win_enemy) {
				mysqli_query($link, "UPDATE unit SET unit_steps = $unit_steps WHERE unit_id = $unit_id");
				array_push($res,"Вражеская клетка. Ничья");		
			} else {
				mysqli_query($link, "DELETE FROM unit WHERE unit_id = $unit_id");
				
				// увеличить счетики побед и поражений победителя и проигравшего
				$wins_enemy++;
				mysqli_query($link, "UPDATE player SET player_wins = $wins_enemy WHERE player_id = $cellPid");			
				$falls_player++;
				mysqli_query($link, "UPDATE player SET player_falls = $falls_player WHERE player_id = $pid");
				
				array_push($res,"Вражеская клетка. Поражение. Юнит потерян.");	
			}
			
			
			
		}
	}
	echo json_encode(array("result" => $res));		
?>