function reloadCaptcha(img_id){
    $("img#"+img_id).attr("src", "/includes/codegen/cms_codegen.php?"+Math.random());
}
function centerLink(href){

	$.post(href, {'of_ajax': 1}, function(data){
		$('div.component').html(data);
	});

}