<?php
	function dbConnect() {
		$dblocation = "localhost";
		$dbname = "task_db";
		$dbuser = "root";
		$dbpasswd = "12345";
		$link = mysqli_connect($dblocation, $dbuser, $dbpasswd, $dbname);
		return $link;
	}		
	function getMenu() {
		$link = dbConnect();
		$sql_1 = "SELECT page_id, page_name, page_weight FROM pages ORDER BY page_weight DESC";
		$result_1 = mysqli_query($link, $sql_1);
		$menuArray = array();
		while($row = mysqli_fetch_assoc($result_1)){
			$menuArray[] = array(
				"pid" => $row["page_id"],
				"pname" => $row["page_name"],
				"pweight" => $row["page_weight"]
			);
		}
		return $menuArray;
	}	
	function buildMenu($menuArray) {
		if(empty($menuArray))
			echo "Данных нет";
		else {
			$menu = "\t<ul>\n";
			foreach($menuArray as $elem) {
				if(isset($_GET["page_id"]) && $_GET["page_id"] == $elem["pid"]) 
					$menu .= "\t\t\t\t\t<li>". $elem["pname"] . " " . $elem["pweight"]."</li>\n";
				else $menu .= "\t\t\t\t\t<li><a href='?page_id=" . $elem["pid"]. "'>". $elem["pname"] . "</a> (". $elem["pweight"].")</li>\n";
			}
			$menu .= "\t\t\t\t</ul>\n";
		}
		return $menu;
	}	
	function getPageContent($pid) {
		$req_page_id = $_GET["page_id"];
		$link = dbConnect();
		$sql_2 = "SELECT * FROM pages WHERE page_id = $req_page_id";
		$wrap = mysqli_query($link, $sql_2);
		$row = mysqli_fetch_assoc($wrap);
		return $row["page_text"];
	}
	function getPageWeightZero() {
		$link = dbConnect();
		$sql_3 = "SELECT * FROM pages WHERE page_weight = 0 LIMIT 0,1";
		$wrap = mysqli_query($link, $sql_3);
		$row = mysqli_fetch_assoc($wrap);
		return $row["page_text"];
	}
?>