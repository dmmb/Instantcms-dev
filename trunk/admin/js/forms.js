function hideAll(){
	document.getElementById('kind_text').style.display = "none";
	document.getElementById('kind_textarea').style.display = "none";
	document.getElementById('kind_checkbox').style.display = "none";
	document.getElementById('kind_radiogroup').style.display = "none";
	document.getElementById('kind_list').style.display = "none";
	document.getElementById('kind_menu').style.display = "none";
}

function show(){
	hideAll();
	needDiv = 'kind_' + document.fieldform.kind.value;
	document.getElementById(needDiv).style.display = "block";
}

function toggleSendTo(){
	var sendto = $('#sendto').attr('value');	
	if (sendto=='mail'){
		$('#sendto_mail').show();
		$('#sendto_user').hide();
	} else {
		$('#sendto_mail').hide();
		$('#sendto_user').show();		
	}	
}