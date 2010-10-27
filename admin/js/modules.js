$(document).ready(function(){
    checkGroupList();
});

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
		$('#grp *').css('color', '#999');
		$('#grp input[type=checkbox]').attr('checked', '').attr('disabled', 'disabled');
		$('#grp select').attr('disabled', 'disabled');
	} else {
        $('#grp *').css('color', '');
		$('#grp input[type=checkbox]').attr('disabled', '');
		$('#grp select').attr('disabled', '');
	}

}