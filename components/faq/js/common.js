function sendQuestion(){
	if($('#message').attr('value').length < 10){
	 	alert('��� ������ ������� ��������!');	
	} else {
		document.questform.submit();	
	}	
}