function sendQuestion(){
	if($('#faq_message').attr('value').length < 10){
	 	alert('Ваш вопрос слишком короткий!');	
	} else {
		document.questform.submit();	
	}	
}