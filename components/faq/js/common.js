function sendQuestion(){
	if($('#faq_message').attr('value').length < 10){
	 	alert('��� ������ ������� ��������!');	
	} else {
		document.questform.submit();	
	}	
}