<?php
	function dbConnect() {
		$dblocation = "localhost";
		$dbname = "project";
		$dbuser = "root";
		$dbpasswd = "12345";
		$link = mysqli_connect($dblocation, $dbuser, $dbpasswd, $dbname) or die('Error!');
		return $link;
	}
	function dbQueryArray($sql,$link) {
		$query = mysqli_query($link, $sql);
		return mysqli_fetch_assoc($query);
	}
	function dbQueryArrayFull($sql,$link) {
		$query = mysqli_query($link, $sql);
		return mysqli_fetch_all($query);
	}
	function selectKeysFromQueryArray($link, $sql, $key) { // подаем запрос и спрашиваем, массив с какими атрибутами хотим получить
		$result = array();
		if($query = mysqli_query($link,$sql)) {
			while($row = mysqli_fetch_assoc($query)) {
				array_push($result, $row["$key"]);
			}
		}
		return $result;
	}
	
	function random_color_part() {
		return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	}
	function random_color() {
		return '#'.random_color_part() . random_color_part() . random_color_part();
	}
	/* полезная штука, выводит нужные ключи заданной выборки
	$sql = "SELECT ... "
			if($res = mysqli_query($link,$sql)) {
			while($logins = mysqli_fetch_assoc($res)) {
				echo $logins["key"]. " ";
			}
		}
		*/
	
	
	$link = dbConnect();
?>