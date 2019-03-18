<!doctype html>
<?php include 'controller.php'; ?>
<html>
	<head>
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
	</head>
	<body>
		<div id='wrap'>
			<header>
				шапка
			</header>
			<nav>
			<?php echo $menu; ?>
			</nav>
			<article>		
			<?php echo "\t" . $pageText . "\n"; ?>
			</article>
			<div class='cl'></div>
			<footer>
				подвал
			</footer>
		</div>
	</body>
</html>