function selectIcon(file){
	document.addform.iconurl.value = file;
	hideIcons();
}

function showIcons(){
    $('#iconlink').slideUp('fast');
    $('#icondiv').slideDown('fast');
}

function hideIcons(){
    $('#iconlink').slideDown('fast');
    $('#icondiv').slideUp('fast');
}

function showMenuTarget(){

    $('.menu_target').hide();

    var target = $('select[@name=linktype]').val();

    $('div#t_'+target).fadeIn('fast');

}