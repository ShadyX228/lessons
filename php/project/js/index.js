$(document).ready(function(){
	ajax_page_status_update();
	function ajax_page_status_update() {	
		$.post("php/getUserData.php", function(data){		
			$("#errorbar").html("");
			if(data.length > 0) {
				var res = JSON.parse(data);
				$('#login').html(res.p_name);				
				if(res.p_gid != null && res.g_players_num == 5) {
					// Режим показа поля, pageStatus = 2
					console.log("pageStatus = 2");
					$('#autorize').hide();
					$('#gamewait').hide();	
					$('#field').show();
					$('#userbar').show();
					//$.post("php/gameSelect.php", function(data){
					//	ajax_page_status_update();
					//})
				} else {
					// Ожидание игры, pageStatus = 1
					console.log("pageStatus = 1");
					$('#autorize').hide();
					$('#gamewait').show();	
					$('#field').hide();
					$('#userbar').show();
					if(res.p_gid != null && res.g_players_num != null) {
						$('.game_number').html("Номер вашей игры: " + res.p_gid);
						if(res.g_players_needed != null) {
							$('.game_needed_players').html("Ждем " + res.g_players_needed + " игроков");
						} else {
							$('.game_needed_players').html("Ждем " + (5 - res.g_players_num) + " игроков");
						}
					}
					else {
						$.post("php/gameSelect.php", function(data){
							ajax_page_status_update();
						})
					}
				}
			} else {
				// Пользователь не авторизован, pageStatus = 0
				console.log("pageStatus = 0");
				$('#autorize').show();
				$('#gamewait').hide();	
				$('#field').hide();	
				$('#userbar').hide();
				
				$('#autorize').html();
				$('#gamewait').html();	
				$('#field').html();	
				$('#userbar').html();
				
				$("#auth_button").click(function(e){ 
					e.preventDefault();
					ajax_auth();
				}); 				
			}
		}) 
	}
	
    $("#user_exit_button").click(function(){ 
		$.post("php/exit.php", function(){
			ajax_page_status_update();
		})			
    }); 
    $("#show_form").click(function(){ 
		$("#reg_form").slideDown();
    }); 
    $("#reg_button").click(function(e){ 
		e.preventDefault();
		var login = $("#reg_login").val(); 
		var pass = $("#reg_pass").val();
		var confirm_pass = $("#reg_confirm_pass").val();
		$.post("php/reg.php", {'reg_login' : login, 'reg_pass' : pass, 'reg_confirm_pass' : confirm_pass}, function(data){
			if(data.length > 0) {
				data = JSON.parse(data);
				$("#errorbar").html(data.error);
			
				if(data.result.length > 0) {
					$("#auth_login").val(login);
					$("#auth_pass").val(pass);
				}
			}
		})			
    }); 

	function ajax_auth(){ 
		var login = $("#auth_login").val(); 
		var pass = $("#auth_pass").val(); 
		$.post("php/auth.php", {'auth_login' : login, 'auth_pass' : pass}, function(data){
			if(data.length > 0) {
				data = JSON.parse(data);
				if(data.error.length == 0) {
					ajax_page_status_update();
					$('#errorbar').html("");
					$('#login').html(login);
				}
				else {
					$("#errorbar").html(data.error);
				}
			}
			else {
				$("#errorbar").html("Данных не получено. Свяжитесь с администрацией.");
			}		
		}) 
	} 
});