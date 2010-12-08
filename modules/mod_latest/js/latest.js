function conPage(page, module_id){

    $.post('/modules/mod_latest/ajax/latest.php', {'module_id': module_id, 'page':page}, function(data){
		$('div#module_ajax_'+module_id).html(data);
	});

}