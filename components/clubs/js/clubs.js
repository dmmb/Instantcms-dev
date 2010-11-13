function createAlbum(clubid, root_album_id){
	var title = $('input#album_title').val();
	var count_photo = $('#count_photo').html();

	if (title){
		
		$('#add_album_form').hide();
		$('#add_album_wait').show();
		
		$.ajax({
		  type: "POST",
		  url: "/components/clubs/ajax/createalbum.php",
		  data: "title="+title+"&clubid="+clubid,
		  success: function(msg){
				if (msg!='error'){
                    if ($('ul.usr_albums_list li').length==5){
                        $('.content p').prepend('<span><a href="/photos/'+root_album_id+'">Все альбомы (<strong id="count_photo">6</strong>)</a></span>');
                    }					
                    if ($('ul.usr_albums_list li').length==6){
                        $('ul.usr_albums_list li:last').remove();
                    }
                    if (count_photo){
						var new_count = Number(count_photo) + 1;
                        $('#count_photo').html(new_count);
                    }
					$('ul.usr_albums_list').prepend('<li id="'+msg+'"><div class="usr_album_thumb"><a href="/photos/'+msg+'" title="'+title+'"><img src="/images/photos/small/no_image.png" width="64" height="64" border="0" alt="'+title+'" /></a></div><div class="usr_album"><div class="link"><a href="/photos/'+msg+'">'+title+'</a>&nbsp;<a class="delete" title="Удалить альбом" href="javascript:void(0)" onclick="deleteAlbum('+msg+', \''+title+'\', '+clubid+')">X</a></div><div class="count">нет фотографий</div><div class="date">только что</div></div></li>');
                    $('ul.usr_albums_list li.no_albums').remove();
				} else {
					alert('Ошибка! Альбом не создан.');	
				}
				$('#add_album_form input.text').val('');
				$('#add_album_link').toggle();
				$('#add_album_wait').hide();
		  }
		});	
	}

}

function deleteAlbum(id, title, clubid){
	var count_photo = $('#count_photo').html();

	if (confirm('Удалить фотоальбом "'+title+'"?')){			
		$('#add_album_wait').show();
		$.ajax({
		  type: "POST",
		  url: "/components/clubs/ajax/deletealbum.php",
		  data: "id="+id+"&clubid="+clubid,
		  success: function(msg){
				if (msg=='ok'){
					$('ul.usr_albums_list li#'+id).remove();
                    if (count_photo){
                        $('#count_photo').html(count_photo-1);
                    }
                    if ($('ul.usr_albums_list li').length==0){
                        $('ul.usr_albums_list').prepend('<li class="no_albums">В клубе нет фотоальбомов.</li>');
                    }
				} else {
					alert('Ошибка! Альбом не удален.');	
				}
                $('#add_album_wait').hide();
		  }
		});        
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