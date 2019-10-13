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
		/*$playersStartCell = array(
			0 => "#ff0000",
			1 => "#00ff00",
			2 => "#0000ff",
			3 => "#ffff00",
			4 => "#ff00ff",
		);*/
		$playersStartCell = array(
			array(intval($fieldSize-$fieldSize/2-1), 0),
			array(0, intval($fieldSize-$fieldSize/2)),
			array(intval($fieldSize-$fieldSize/2-1), $fieldSize-1),
			array($fieldSize-1, $fieldSize-3),
			array($fieldSize-1, 2)
		);
		$user = dbQueryArrayFull("SELECT player_id FROM player WHERE player_game_id = $gid",$link);
		//echo $user[4][0];
		//echo $playersStartCell[0][0];
		for($i = 0;$i<5;$i++) {
			$pid_current = $user[$i][0];
			$x = $playersStartCell[$i][0];
			$y = $playersStartCell[$i][1];
			$query = dbQueryArray("INSERT INTO cell (cell_game_id, cell_resource_id, cell_x, cell_y, cell_shield, player_id) VALUES ($gid, 5, $x, $y, 0, $pid_current)",$link);
			//echo $playersStartCell[$i][$j];
		}
		$check = dbQueryArray("UPDATE game SET game_field_is_painted = 1 WHERE game_id = $gid",$link);
	}
	else {
		array_push($errors, "-5");
	}
	$query = dbQueryArray("SELECT cell_x, cell_y, cell_shield, cell_resource_id FROM cell WHERE player_id = $pid",$link);
	//var_dump($query);
	$res = array(
		"cell_x" => $query["cell_x"],
		"cell_y" => $query["cell_y"],
		"cell_shield" => $query["cell_shield"],
		"cell_resource_id" => $query["cell_resource_id"],
		"check_field" => $check["game_field_is_painted"]
	);
	$_SESSION["field_is_printed"] = 1;
	//var_dump($res);
	echo json_encode(array("result" => $res, "error" => $errors));		
?>