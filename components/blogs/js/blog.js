$().ready(function() {  
  $('#author_remove').click(function() {  
		if ($('#uid').attr('value') != $('#authorslist option:selected').attr('value')){
			return !$('#authorslist option:selected').remove().appendTo('#userslist');  
		} else {
			alert('Нельзя удалить из списка авторов хозяина блога!');	
		}
  });  
  $('#author_add').click(function() {  
   		return !$('#userslist option:selected').remove().appendTo('#authorslist');  
  });  
 
  $("#cfgform").submit(function() { $('#authorslist').each(function(){
	$('#authorslist option').attr("selected","selected");
  });  
	});
  
});  

function selectOwnerType(){
	var ot = $('#ownertype').attr('value');
	if (ot == 'multi') {
		$('#multiblogcfg').show();
		if ($('#forall').attr('value')==0){
			$('#multiuserscfg').show();
		}
	} else {
		$('#multiblogcfg').hide();
		$('#multiuserscfg').hide();
	}
}
function selectAuthorsType(){
	var ot = $('#forall').attr('value');
	if (ot == '0') {
		$('#multiuserscfg').show();
	} else {
		$('#multiuserscfg').hide();
	}
}