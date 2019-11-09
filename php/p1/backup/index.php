<?php //include 'index_controller.php'; 
// v0.001a ?>
<!doctype html>
<html>
	<head>
		<style>
		
			#autorize, #gamewait, #field {display: none;}
			header, nav, article, footer {
				padding: 5px;
				border: 1px solid black;
			}
			#wrap {
				widthin: 500px;
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
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript" src="index.js"></script>
	</head>
	<body>
		<div id="errorbar"></div>
		<div id="autorize">
			<form id="auth_form" method="POST">
				<input type="text" name="auth_login" id="login" placeholder="Логин">
				<input type="password" name="auth_pass" id="pass" placeholder="Пароль">
				<input type="submit" id="auth_button" value="тык">
			</form>
		</div>
		<div id="gamewait">
		
		</div>
		<div id="field">
		
		</div>
	</body>
</html>