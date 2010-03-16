function createAlbum(clubid, menuid){
	var title = $('input#album_title').val();

	if (title){
		
		$('#add_album_form').hide();
		$('#add_album_wait').show();
		
		$.ajax({
		  type: "POST",
		  url: "/components/clubs/ajax/createalbum.php",
		  data: "title="+title+"&clubid="+clubid,
		  success: function(msg){
				if (msg!='error'){
					$('ul#albums_list').prepend('<li id="'+msg+'" class="club_album"><a href="/photos/'+menuid+'/'+msg+'">'+title+'</a> (0) <a class="delete" onclick="deleteAlbum('+msg+', \''+title+'\', '+clubid+')" href="javascript:void(0)" title="������� ������">X</a></li>');
                    $('ul#albums_list li.no_albums').remove();
				} else {
					alert('������! ������ �� ������.');	
				}
				$('#add_album_form input.text').val('');
				$('#add_album_link').toggle();
				$('#add_album_wait').hide();
		  }
		});	
	}

}

function deleteAlbum(id, title, clubid){

	if (confirm('������� ���������� "'+title+'"?')){			
		$('#add_album_wait').show();
		$.ajax({
		  type: "POST",
		  url: "/components/clubs/ajax/deletealbum.php",
		  data: "id="+id+"&clubid="+clubid,
		  success: function(msg){
				if (msg=='ok'){
					$('ul#albums_list li#'+id).remove();
                    if ($('ul#albums_list li.club_album').length==0){
                        $('ul#albums_list').prepend('<li class="no_albums">� ����� ��� ������������.</li>');
                    }
				} else {
					alert('������! ������ �� ������.');	
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