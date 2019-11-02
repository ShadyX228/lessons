<?php
include 'model.php';
session_start(); 	

$pid = $_SESSION["pid"];
$user_check = dbQueryArray("SELECT player_ready FROM player WHERE player_id = $pid",$link);
echo $user_check["player_ready"];


?>