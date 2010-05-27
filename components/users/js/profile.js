function setStatus(){

    var new_status = prompt('������� ���� ��������� ��������� (�������� 100 ��������):');

    if (new_status.length > 100) {
        new_status = new_status.substr(0, 100);
    }

    if (new_status) {
        $('.usr_status_text').show();
        $('.usr_status_date').show();
        $('.usr_status_bar').fadeOut();
        $('.usr_status_text span').eq(0).html(new_status);
        $('.usr_status_date').html('// ������ ���');
        $('.usr_status_bar').fadeIn();
    } else {
        if (new_status == ''){
            $('.usr_status_text').hide();
            $('.usr_status_date').hide();
        }
    }

    if (new_status || new_status == '') {
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