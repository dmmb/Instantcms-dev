function sendMessage(){
	
	var to_id = $('select#to_id').val();
	
	if (to_id > 0){			
		var action = '/users/0/'+to_id+'/sendmessage.html';
		$('form#newmessage').attr('action', action).trigger("submit");						
	} else {
		$('input#gosend').attr('disabled', 'disabled');
	}
	
}