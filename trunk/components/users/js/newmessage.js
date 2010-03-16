function sendMessage(){
	
	var to_id = $('select#to_id').val();
	var to_all = $('input[name=massmail]:checked').length;

	if (to_id > 0 || to_all == 1){
        if (to_all==1){ to_id = 1; }
		var action = '/users/0/'+to_id+'/sendmessage.html';
		$('form#newmessage').attr('action', action).trigger("submit");						
	} else {
		$('input#gosend').attr('disabled', 'disabled');
	}
	
}