function setStatus(user_id){

    var new_status = prompt('Введите ваше статусное сообщение (максимум 140 символов):');

    if (new_status.length > 140) {
        new_status = new_status.substr(0, 140);
    }

    if (new_status) {
        $('.usr_status_text').show();
        $('.usr_status_date').show();
        $('.usr_status_bar').fadeOut();
        $('.usr_status_text span').eq(0).html(new_status);
        $('.usr_status_date').html('// Только что');
        $('.usr_status_bar').fadeIn();
    } else {
        if (new_status == ''){
            $('.usr_status_text').hide();
            $('.usr_status_date').hide();
        }
    }

    if (user_id==undefined){ user_id = 0; }

    if (new_status || new_status == '') {
        $.post('/components/users/ajax/status.php', {'status': new_status, 'id': user_id}, function(data){});
    }

}

function wallPage(page){

    var user_id     = $('div.wall_body input[name=user_id]').val();
    var usertype    = $('div.wall_body input[name=usertype]').val();

    $('.wall_loading').show();
	$('div.wall_body').css({opacity:0.5, filter:'alpha(opacity=50)'});
    $.post('/components/users/ajax/wall.php', {'user_id': user_id, 'usertype': usertype, 'page':page}, function(data){
		$('div.wall_body').html(data);
		$('div.wall_body').css({opacity:1.0, filter:'alpha(opacity=100)'});
	});

}

function plusUkarma(to_user_id, user_id){
	$("#u_karma").load("/users/karma/plus/"+to_user_id+"/"+user_id+"", {'is_ajax': 1});
}
function minusUkarma(to_user_id, user_id){
	$("#u_karma").load("/users/karma/minus/"+to_user_id+"/"+user_id+"", {'is_ajax': 1});
}