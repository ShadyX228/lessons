<?php
	include 'model.php';		
	$errors = array();
	$res = array();	
	session_start();
	$pid = $_SESSION["pid"];
	$row = dbQueryArrayFull("SELECT * FROM cell WHERE player_id = $pid",$link);
	//var_dump(count($row));
	//print_r($row);
	for($i = 0;$i < count($row);$i++) {
		$temp = array(
				"cell_resource_id" => $row[$i][2],
				"cell_x" => $row[$i][3],
				"cell_y" => $row[$i][4],
				"cell_shield" => $row[$i][5]
		);
		array_push($res,$temp);
	}
	//print_r($res);
	echo json_encode(array("result" => $res, "error" => $errors));
?>

