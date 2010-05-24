function auth(){
	$.modal($('#authModal'), {overlay:75});
	$('#authinput').focus();
}

function hideAuth(){
	$('#authModal').close();
}