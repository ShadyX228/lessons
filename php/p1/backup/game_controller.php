<?php
	include 'model.php';
	session_start(); 
	$link = dbConnect();
	$pid = $_SESSION["pid"];
?>