function searchGender(gender){	
	$('body').append('<form id="sform" style="display:none" method="post" action="/users/search.html"><input type="hidden" name="gender" value="'+gender+'"/></form>');
	$('form#sform').submit();	
}