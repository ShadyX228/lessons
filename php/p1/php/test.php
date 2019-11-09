<?php
include 'model.php';
session_start(); 	

$gid = $_SESSION["game_id"];
$game_step = dbQueryArray("SELECT game_step FROM game WHERE game_id = $gid",$link);
echo $game_step["game_step"];


?>