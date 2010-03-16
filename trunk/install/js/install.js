$().ready(function(){
	$("form.wizard").wizard({
			show: function(element) {
				if($(element).is("#install")){
					$('input[@name=install]').remove();
					$('.wizardcontrols').append('<input class="wizardnext" type="submit" name="install" value="Установить">');	
				} else {
					$('input[@name=install]').remove();
				}
					
			}
		});
});
