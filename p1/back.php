<?php
	$dblocation = "localhost";
	$dbname = "task_db";
	$dbuser = "root";
	$dbpasswd = "12345";
	$link = mysqli_connect($dblocation, $dbuser, $dbpasswd, $dbname);
	$sql_1 = "SELECT page_id, page_name, page_weight FROM pages ORDER BY page_weight DESC";
	$result_1 = mysqli_query($link, $sql_1);

	function getMenu() {
		$menuArray = array();
		if (mysqli_num_rows($GLOBALS['result_1']) > 0) {
			while($row = mysqli_fetch_assoc($GLOBALS['result_1'])){
				$temp = array(
					"pid" => $row["page_id"],
					"pname" => $row["page_name"],
					"pweight" => $row["page_weight"]
				);
				array_push($menuArray,$temp);
			}
		}
		else echo "Данных нет";
		return $menuArray;
	}
	function buildMenu($menuArray) {
		if(empty($menuArray))
			echo "Данных нет";
		else {
			$menu = "<ul>\n";
			foreach($menuArray as $elem) {
				if(isset($_GET["page_id"]) && $_GET["page_id"] == $elem["pid"]) 
					$menu .= "\t\t\t<li>". $elem["pname"] . " " . $elem["pweight"]."</li>\n";
				else $menu .= "\t\t\t<li><a href='?page_id=" . $elem["pid"]. "'>". $elem["pname"] . "</a> ". $elem["pweight"]."</li>\n";
			}
			$menu .= "\t\t</ul>";
		}
		echo $menu;
	}
	function showPageContent() {
		if(isset($_GET["page_id"])) {
			$req_page_id = $_GET["page_id"];
			$sql_2 = "SELECT * FROM pages WHERE page_id = $req_page_id";
			$wrap = mysqli_query($GLOBALS['link'], $sql_2);
			$row = mysqli_fetch_assoc($wrap);
			echo $row["page_text"];
		}
		else {
			$sql_3 = "SELECT * FROM pages WHERE page_weight = 0";
			$wrap = mysqli_query($GLOBALS['link'], $sql_3);
			while($row = mysqli_fetch_assoc($wrap)){
				echo $row["page_text"];
				break;
			}
		}
	}
	

	echo "
		<style>
			header, nav, article, footer {
				padding: 5px;
				border: 1px solid black;
			}
			#wrap {
				width: 500px;
				margin: auto;
			}
			.cl {
				clear: both;
			}
			nav {
				float: left;
			}
			article {
				float: left;
				width: 300px;
				word-break: break-all;
			}
			
		</style>
		<div id='wrap'>
			<header>
			шапка
			</header>
			<nav>
			"; 
			buildMenu(getMenu());
			echo "
			</nav>
			<article>";
			showPageContent();
			echo "</article>
			<div class='cl'></div>
			<footer>
			подвал
			</footer>
		</div>
	";
?>