function searchGender(gender, menuid){	
	$('body').append('<form id="sform" style="display:none" method="post" action="/users/'+menuid+'/search.html"><input type="hidden" name="gender" value="'+gender+'"/></form>');
	$('form#sform').submit();	
}