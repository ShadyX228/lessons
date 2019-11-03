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
							content += "\t\t<td class=\"cell\" id=\"cell" + col + line + "\">(";
							content += col + " " + line;	
							content += ")</td>";
						}
						content += "</tr>\n";
					}
					content += "</table>";
					$('#field').html(content);
					var userlog = "<div id=\"userlog\"> Ход: <div id=\"game_step\"></div>. Доступные ходы юнитов: <div id=\"player_steps\"></div>. Юниты: <div id=\"player_units\"></div>. Число ходов выбранного юнита:  <div id=\"unit_steps\"></div></div>";
					$('#field').append(userlog);
				

					// подсвечиваем позиции игрока
					// игроки друг друга не видят, поэтому изначально
					// будет подсвечена одна позиция
					var player_pos_x;
					var player_pos_y;
					$.post("php/startGame.php", function(data){
						console.log("Field is painted.");
						data = JSON.parse(data);
						
						$('#player_units').html(data.result.unit_count);
						$('#player_steps').html(data.result.unit_count*5);
						$('#game_step').html(data.result.game_step);
						
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
					
						// механизм ходов
						
						
						$('.unit').bind('click', function() {								
							var unit = $(this);
							
							var unit_selector = unit.attr('id').split("");
							
							var unit_id = unit_selector[2];
							for(var i = 3; i < unit_selector.length; i++) {
								//alert(unit_selector[i]);
								unit_id += unit_selector[i];
							}
							var unit_x = unit_selector[0];
							var unit_y = unit_selector[1];
							//alert(unit_selector);
							 
							var cell_current = $('td#cell' + unit_x + unit_y + '.cell');
							var cell_current_id = $('td#cell' + unit_x + unit_y + '.cell').attr('id');
							
							unit.css("border-color","red");
							
							console.log("unit " + unit_id);
							
							
							$('.cell').click(function() {								
								var cell_new = $(this);
								var cell_new_id = $(this).attr('id');
								var cell_new_x = cell_new_id.split("")[4];
								var cell_new_y = cell_new_id.split("")[5];								
							
								
								var path = Math.sqrt((cell_new_x - unit_x)*(cell_new_x - unit_x)+(cell_new_y - unit_y)*(cell_new_y - unit_y));
								path = Math.round(path);	
																															
					
								if(path != 0) {
									if($("#unit_steps").text() == 0? path <= 5 : path <= $("#unit_steps").text()) {												
								
										console.log("|path| = " + path);
										var comp = $("#unit_steps").text() == 0? 5 : $("#unit_steps").text();
										$("#unit_steps").html(comp-path);
										
										var remain = $("#unit_steps").text();	
										console.log("remain of steps = " + remain);
										
										if(remain == 0) {
											unit.off();
										}							
										if(cell_new_id != cell_current_id) {
											var totalSteps = $('#player_steps').text();	
											allSteps = totalSteps-path;	
											$('#player_steps').html(allSteps);	
										
											$('#unit_steps').html(remain);
										
											console.log("|path| = " + path + ", moved to (" + cell_new_x + " " + cell_new_y + "), all steps: " + allSteps);
										
											cell_new.css("background", data.result.cell_color)
											cell_new.append(unit);
										
											unit.attr('id',cell_new_x + '' + cell_new_y + '' + unit_id);								
											$('.cell').unbind();
											unit.css("border-color","black");
											// тут ajax-запрос на обновление инфы в базе
										}								
									} else {
										cell_new.css("background", "red");
										console.log("|path| = " + path);
										setTimeout(function() {cell_new.css("background", "white")}, 5000);	
									
									}
								}
								if($('#player_steps').text() == 0) {
									ajax_page_status_update();
								}
																							
							})							
					
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

	/* done
	Функция: unit.click()
	внутри есть cell.click(), которая переместит юнита из одной клетки в другую
	в cell.click() нужно сделать проверку, чтобы юнит дальше одной клетки не мог быть перемещен
	в cell.click() как раз и будет эта проверка
	если проверка пройдена, то далаем ajax-запрос в файл changeUnitPosition.php
	в файл передаем: ид юнита, координаты новой и старой клетки
	проверяем, чья эта клетка
	если кто-то на ней есть, делаем битву
	победителя определяем по формуле, которая есть в правилах
	если нет, то клетка просто переходит во владение игрока
	в конце всего нужно исходя из бонус, которые дают ресурсы клетки, обновить инфу в базе об игроке
	*/
	/*
		Баг: если у выбранного юнита остаются ходы и при этом выбирается другой, остоток ходов старого юнита переходит новому
	*/
	
	
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