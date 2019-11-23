<?php
	//$path = $_SERVER['DOCUMENT_ROOT'];
	//$path .= "/dashobard/project/php/model.php";
	//include_once($path);
	include "../model.php";
	session_start();
	
	
	$gid = $_SESSION["game_id"];
	$res = array();	
	
	// получаем идшники активных игроков
	$player = dbQueryArrayFull("SELECT player_id FROM player WHERE player_game_id = $gid AND player_ready = 1",$link);
	$playersIds = array();
	for($i = 0;$i<=4;$i++) {
		$playerId = $player[$i][0];
		array_push($playersIds, $playerId);
	}
	// смотрим, сколько ходов осталось у всех активных игроков
	$playerSteps = 0;	
	for($i = 0;$i<=4;$i++) {
		$playerId = $playersIds[$i];
		$unit = dbQueryArray("SELECT unit_steps FROM unit WHERE unit_player_id = $playerId",$link);
		$step = $unit["unit_steps"];
		$playerSteps += $step;		
	}
	// ходы еще есть, отсылаем в index.js результат -1 и заставляем юзверя ждать
	if($playerSteps != 0) {
		array_push($res, -1);
	} else {
		// у всех не осталось ходов
		
		// увеличиваем кон
		$gameInfo = dbQueryArray("SELECT game_step FROM game WHERE game_id = $gid",$link);
		$gameStep = $gameInfo["game_step"];
		$gameStep++;
		mysqli_query($link,"UPDATE game SET game_step = $gameStep WHERE game_id = $gid");
		
		// восстанавливаем юнитам ходы
		for($i = 0;$i<=4;$i++) {
			$playerId = $playersIds[$i];
			mysqli_query($link,"UPDATE unit SET unit_steps = 5 WHERE unit_player_id = $playerId");
		}
		array_push($res, 0);
	}
	
	

	echo json_encode(array("result" => $res));		
?>