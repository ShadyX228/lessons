<?php
	session_start();
	include 'model.php';
	sessExit();
	header("Location: index.php");
?>