<style>
	.gameField {
		width: 50%;
	}
	.gameField td {
		border: 1px solid black;
		height: 50px;
		text-align: center;
	}
</style>
<?php
	include 'model.php';		
	$errors = array();
	$res = array();	
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
	
	for($i = 0;$i<5;$i++) {
		echo "(";
		for($j = 0;$j<=1;$j++) {
			echo $playersStartCell[$i][$j];
		}
		echo ")";
	}
	/*print_r($playersStartCell);
	echo $playersStartCell[2][0];*/ 
	
	
	echo "<table class=\"gameField\">\n";	
	
	for($x = 0; $x < $fieldSize; $x++) {
		echo "\t<tr>\n";
		
		for($y = 0; $y < $fieldSize; $y++) {
			// привязываем клетку к игре
			// назначаем или не назначаем ресурс
			// записываем координаты клетки
			// изначально клетка не укреплена (cell_shield = false)
			// назначаем игрока в клетку
			

			
			
			echo "\t\t<td>(";
			
			$playerNumber;
			$cellCheck = false;
			for($playerNumber = 0; $playerNumber < 5; $playerNumber++) {
				if($x == $playersStartCell[$playerNumber][0] && $y == $playersStartCell[$playerNumber][1]) {
					echo "<b style='color: red'>" . $x . " " . $y . "</b>";
					$cellCheck = true;
					
					// ЗДЕСЬ пишем запрос к project->cell
					// cell_game_id - берем из сессии игрока
					// cell_resource_id - написать функцию, которая рандомно ставит клетке случайный ресурс или не ставит
					// cell_x = $x, cell_y = $y
					// cell_shield = false
					// player_id - берем из сессии игрока
					break;
				}
			}
			if($cellCheck == false)
				echo $x . " " . $y;	
			
			
			echo ")</td>\n";
		}
		echo "\t</tr>\n";
	}
	echo "</table>";
?>