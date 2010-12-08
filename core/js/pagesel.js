function goPage(dir, field, formname){
	
	var p = Number($('#'+field).attr('value')) + dir;
	$('#'+field).attr('value', p);
	$('#'+formname).submit();
	
}