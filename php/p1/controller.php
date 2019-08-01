<?php 
	include 'model.php';
	$menu = buildMenu(getMenu());
	$pageText;
	if(isset($_GET["page_id"]))
		$pageText = getPageContent($_GET["page_id"]);
	else 
		$pageText = getPageWeightZero();
?>