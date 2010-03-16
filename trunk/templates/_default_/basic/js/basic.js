function auth(){
	$.modal($('#authModal'), {overlay:75});
	document.authform.login.focus();
}

function hideAuth(){
	$('#authModal').close;
}