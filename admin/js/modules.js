// JavaScript Document
function checkDiv(){
	
	var visible_div = document.addform.operate.options[document.addform.operate.selectedIndex].value + '_div';
	
	if (visible_div == 'user_div'){
		document.getElementById('clone_div').style.display = 'none';
		document.getElementById('user_div').style.display = 'block';
	} else {
		document.getElementById('clone_div').style.display = 'block';
		document.getElementById('user_div').style.display = 'none';	
	}
	
}
function checkGroupList(){

	if(document.addform.show_all.checked){
		$('select#showin').attr('disabled', 'disabled');
		$('#is_strict_bind').attr('checked', '').attr('disabled', 'disabled');
		$('label[for=is_strict_bind] strong').css('color', '#999');
	} else {
		$('select#showin').attr('disabled', '');
        $('#is_strict_bind').attr('disabled', '');
        $('label[for=is_strict_bind] strong').css('color', '');
	}

}