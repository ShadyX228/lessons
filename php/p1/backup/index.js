// после выполнения входа не работает кнопка выхода
// после того, как пришел результат из auth.php кнопка выхода теряет связь с index.js
// если обновить страницу, все приходит в норму.
$(document).ready(function(){
	
	$.post("getUserDataForUpdate.php", function(data){		
		if(data.length > 0) {
			var res = JSON.parse(data);
			if(res.p_gid != 0) {
				// Режим показа поля
				$('#autorize').hide();
				$('#gamewait').hide();	
				$('#field').show();
				
				
				
				//$("#play_button").slideUp();
				//ajax_play_game();
			} else {
				// Ожидание игры
				$('#autorize').hide();
				$('#gamewait').show();	
				$('#field').hide();
			}
		} else {
			// Пользователь не авторизован
			$('#autorize').show();
			$('#gamewait').hide();	
			$('#field').hide();			
		}
	}) 
	

    $("#auth_button").click(function(e){ 
        e.preventDefault(); 
        ajax_auth(); 
    }); 
	
    $("#exit_button").click(function(){ 
        ajax_exit(); 
    }); 
    $("#play_button").click(function(){ 
        ajax_play_game(); 
    }); 

	function ajax_auth(){ 
		var login = $("#login").val(); 
		var pass = $("#pass").val(); 
		$.post("auth.php", {'auth_login' : login, 'auth_pass' : pass}, function(data){
			
			data = JSON.parse(data);
			if (noErrors(data)){
				data = data.result;
				
				
				
			}
		
		}) 
	} 
	function ajax_play_game(){ 
		//$("#play_button").slideUp();
		$.post("game.php", function(data){
			$("#game_area").html(data);
		}) 
	} 
	
	function noErrors(data){
		if (data.errors.length == 0) return true;
		else {
			//foreach()...
			return false;
		}
	}
});