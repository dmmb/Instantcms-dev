function publishPhoto(id){
	$.ajax({
	  type: "POST",
	  url: "/components/photos/ajax/pubphoto.php",
	  data: "id="+id,
	  success: function(msg){
			if (msg=='ok'){
				$('tr#moder'+id+' td').html('<div style="color:silver">���� ������������</div>');
			} else {
				alert('������! ���� �� ������������.');	
			}
			$('#add_album_wait').hide();
	  }
	});	
}