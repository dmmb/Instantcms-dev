function publishPhoto(id){
	$.ajax({
	  type: "POST",
	  url: "/components/photos/ajax/pubphoto.php",
	  data: "id="+id,
	  success: function(msg){
			if (msg=='ok'){
				$('tr#moder'+id+' td').html('<div style="color:silver">Фото опубликовано</div>');
			} else {
				alert('Ошибка! Фото не опубликовано.');	
			}
			$('#add_album_wait').hide();
	  }
	});	
}