function reloadCaptcha(img_id){
    $("img#"+img_id).attr("src", "/includes/codegen/cms_codegen.php?"+Math.random());
}
function centerLink(href){
	$('div.component').css({opacity:0.4, filter:'alpha(opacity=40)'});
	$.post(href, {'of_ajax': 1}, function(data){
		$('div.component').html(data);
		$('div.component').css({opacity:1.0, filter:'alpha(opacity=100)'});
	});

}