function setStatus(){

    var new_status = prompt('Введите ваше статусное сообщение:');

    if (new_status) {
        $('.usr_status_text').show();
        $('.usr_status_date').show();
        $('.usr_status_bar').fadeOut();
        $('.usr_status_text span').eq(0).html(new_status);
        $('.usr_status_date').html('// Только что');
        $('.usr_status_bar').fadeIn();
    	$.post('/components/users/ajax/status.php', {'status': new_status}, function(data){});
	}

}

function wallPage(page){

    var user_id     = $('div.wall_body input[@name=user_id]').val();
    var usertype    = $('div.wall_body input[@name=usertype]').val();

    $('.wall_loading').show();

    $.post('/components/users/ajax/wall.php', {'user_id': user_id, 'usertype': usertype, 'page':page}, function(data){
		$('div.wall_body').html(data);
	});

}