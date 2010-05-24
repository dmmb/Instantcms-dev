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

    var target = $('select[@name=mode]').val();

    $('div#t_'+target).fadeIn('fast');

}

function submitItem(){

    var linktype    = $('#addform #linktype').val();
    var link        = $('#addform #link').val();

    if (linktype=='link' && link==''){
        alert('”кажите ссылку пункта меню!'); return;
    }

    $('#addform').submit();

}