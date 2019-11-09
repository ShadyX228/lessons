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
	$allSteps = $_POST['allSteps'];
	//echo $unit_id;
	//echo $unit_steps;
	//echo $unit_pos_x;
	//echo $unit_pos_y;
	
	// проверяем, есть ли в бд запись о такой клетке
	// если есть, то она либо занята другим игроком, либо на ней есть ресурсы
	// отдельно нужно будет сделать переход на союзные клетки

	$checkCell = dbQueryArray("SELECT cell_id, player_id FROM cell WHERE cell_game_id = $gid AND cell_x = $unit_pos_x AND cell_y = $unit_pos_y",$link);
	// пустая незанятая клетка, игрок её занимает:
	if($checkCell == null) {
		mysqli_query($link, "INSERT INTO cell (cell_game_id, cell_resource_id, cell_x, cell_y, cell_shield, player_id) VALUES ($gid, 5, $unit_pos_x, $unit_pos_y, 0, $pid)");
		$cellId = mysqli_insert_id($link);
		mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
	} 
	// занятая этим же игроком клетка
	if($checkCell["player_id"] == $pid) {
		$cellId = $checkCell["cell_id"];
		mysqli_query($link, "UPDATE unit SET unit_cell_id = $cellId, unit_steps = $unit_steps WHERE unit_id = $unit_id");
	}
	
	
	// тут еще должна быть проверка на то, есть ли в этой клетке ресурс
	// если есть, то создаем вешаем на игрока баф, например, создаем ему юнита в этой клетке, есть ресурс = вода
?>