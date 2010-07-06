$().ready(function(){
	$("form.wizard").wizard({
			show: function(element) {

				if($(element).is("#install")){
					$('input[@name=install]').remove();
					$('.wizardcontrols').append('<input class="wizardnext" type="submit" name="install" style="width:150px" value="Установить">');
				} else {
					$('input[@name=install]').remove();
				}

                if($(element).is("#start")){
                    setTimeout("checkAgree()", 100);
                }

			}
		});
});

function checkAgree(){

    var agree = $('#license_agree').attr('checked');

    if (agree) { $('.wizardnext').attr('disabled', ''); } else {
        $('.wizardnext').attr('disabled', 'disabled');
    }

}