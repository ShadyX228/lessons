// допилить вывод цвета в клетках
$(document).ready(function(){
	ajax_page_status_update();
	function ajax_page_status_update() {	
		$.post("php/getUserData.php", function(data){		
			$("#errorbar").html("");
			if(data.length > 0) {
				var res = JSON.parse(data);
				$('#login').html(res.p_name);				
				if(res.p_gid != null && res.g_players_num == 5) {
					// Игра началась, pageStatus = 2
					console.log("pageStatus = 2");
					$('#autorize').hide();
					$('#gamewait').hide();	
					$('#field').show();
					$('#userbar').show();
					
					// рисуем поле, делаем расстановку игроков на позиции	
					var fieldSize = 9;
					var playersStartCell = [
						[parseInt(fieldSize-fieldSize/2-1), 0],
						[0, parseInt(fieldSize-fieldSize/2)],
						[parseInt(fieldSize-fieldSize/2-1), fieldSize-1],
						[fieldSize-1, fieldSize-3],
						[fieldSize-1, 2]
					];
					
					var content = "<table class=\"gameField\">";
					for(var col = 0; col < fieldSize; col++) {
						content += "\t<tr>\n";
						for(var line = 0;line < fieldSize; line++) {
							//content += "\t\t<td>(";
							content += "\t\t<td id=\"cell" + col + line + "\">(";
							content += col + " " + line;	
							content += ")</td>";
						}
						content += "</tr>\n";
					}
					content += "</table>";
					$('#field').html(content);
				

					// подсвечиваем позиции игрока
					// игроки друг друга не видят, поэтому изначально
					// будет подсвечена одна позиция
					var player_pos_x;
					var player_pos_y;
					$.post("php/startGame.php", function(data){
						console.log("Field is painted.");
						data = JSON.parse(data);
						
						//if(data.result.check_field != 1) {
						
						var cells = data.result.cells;
						
						cells.forEach(function(item, position, cells) {
							player_pos_x = item[1];
							player_pos_y = item[2];
							
							$('td#cell' + player_pos_x + player_pos_y).html("*<br>");
							$('td#cell' + player_pos_x + player_pos_y).css("background-color", data.result.cell_color);
							
							var units = data.result.units;
							var cell_id = cells[position][0];
							var cell_selector = $('td#cell' + player_pos_x + player_pos_y);
							
							units.forEach(function(item, position, arr) {
								var unit_cell_id = units[position][1];																
								var unit_id = units[position][0];
								
								if(unit_cell_id == cell_id) {
								
									cell_selector.append("<span class =\"unit\" id=" + player_pos_x + player_pos_y + unit_id + ">" + unit_id + "</span>");
								} else {
									cell_selector.html("No units.");
								}
												
								var unit_selector = $('span#' + player_pos_x + player_pos_y + unit_id +'.unit');
								unit_selector.css("border", "1px solid black");
								unit_selector.css("margin", "2px");
								unit_selector.css("padding", "2px");													
								unit_selector.css("background-color", data.result.cell_color);													
																	
							});
							
						})
					})
					
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
				// управление обновлением страницы
				//setTimeout(ajax_page_status_update, 5000);
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