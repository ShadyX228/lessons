<?php
	include 'model.php';
	session_start(); 
	// userbar, content: печатаем либо логин и кнопку выхода или форму авторизации, даём доступ к игре
	$userbar = '';
	$content = '';
	if (isset($_SESSION['login'])) {
		$session_user_login = $_SESSION['login'];
		$userbar = userbar($session_user_login);
		
		$content .= "\tИгра доступна.\n";
		$content .= "\t\t\t<button id=\"play_button\">Играть</button>";
		$content .= "\n\t\t\t<div id=\"game_area\">\n\t\t\t</div>";
	}
	else {
		$userbar = printForm();
		$content = "Игра не доступна. Авторизируйтесь.";
	}	
?>