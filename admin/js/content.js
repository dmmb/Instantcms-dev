function checkGroupList(){

	if(document.addform.is_public.checked){
		$('select#showin').attr('disabled', 'disabled');
	} else {
		$('select#showin').attr('disabled', '');
	}

}