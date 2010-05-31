function addSmile(tag, field_id){
	var txtarea = document.getElementById(field_id);
	var tag     = ' :' + tag + ': ';
    insertTagAtCursor(field_id, tag);
	$('#smilespanel').hide();
}

function addTag(field_id, s_open, s_close){

    var txtarea = document.getElementById(field_id);
    var selStart = txtarea.selectionStart;
    var selEnd = txtarea.selectionEnd;
    var selLength = txtarea.textLength;

    if (selLength>0){
        var s1 = (txtarea.value).substring(0,selStart);
        var s2 = (txtarea.value).substring(selStart, selEnd)
        var s3 = (txtarea.value).substring(selEnd, selLength);
        txtarea.value = s1 + s_open + s2 + s_close + s3;
    } else {
        txtarea.value += s_open + s_close;
    }

    return;
    
} 

function addTagUrl(field_id)
{

    var link_name_def = '';

    var txtarea = document.getElementById(field_id);

    if (txtarea.textLength > 0){
        link_name_def = (txtarea.value).substring(txtarea.selectionStart, txtarea.selectionEnd);
    }

    var tag         = '';
    var link_url    = prompt('Адрес ссылки (URL):');
    var link_name   = prompt('Название ссылки (не обязательно):', link_name_def);

    if (link_url=='') { return; }
   
    if (link_name.length > 0){
        tag = '[url='+link_url+']' + link_name + '[/url]';}
    else {
        tag = '[url]' + link_url + '[/url]';
    }

    insertTagAtCursor(field_id, tag);

    return;

} 

function addTagEmail(field_id)
{
    var link_url = prompt('Адрес электронной почты:');
    if (link_url.length > 0){
        var tag = '[email]' + link_url + '[/email]';
        insertTagAtCursor(field_id, tag);
    }
	return;
}

function addTagAudio(field_id)
{
    var link_url = prompt('Ссылка на mp3-файл:');
    var tag = '';
    if (link_url.length > 0){
        tag = '[audio]' + link_url + '[/audio]';        
    }
    insertTagAtCursor(field_id, tag);
	return;
}

function addTagVideo(field_id)
{
    var txtarea     = document.getElementById(field_id);
    var link_url    = prompt('Код видео (Youtube/Rutube):');
    var tag         = '';

    if (link_url.length > 0){
        tag = '[video]' + link_url + '[/video]';
        insertTagAtCursor(field_id, tag);
    }    

	return;

}

function addImage()
{
	$('#albumimginsert').hide();
	$('#imginsert').toggle();
}

function loadImage(field_id, session_id, placekind)
{
		//starting setting some animation when the ajax starts and completes
		$("#imgloading")
		.ajaxStart(function(){
			$(this).show();			
			$('#imginsert').hide();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload
		(
			{
				url:'/core/ajax/imginsert.php?place='+placekind, 
				secureuri:false,
				fileElementId:'attach_img',
				dataType: 'json',
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert('Ошибка: '+data.error);
						}else
						{
							imageLoaded(field_id, data.msg, placekind);
							alert('Изображение добавлено');
						}
					}
				},
				error: function (data, status, e)
				{
					alert('Ошибка! '+e);
				}
			}
		)
		
		return false;
}


function imageLoaded(field_id, data, placekind){
    var tag = '[IMG]/upload/'+placekind+'/'+data+'[/IMG]';
    insertTagAtCursor(field_id, tag);
    return;
}

function addTagQuote(field_id)
{

    var q_text_def = '';

    var txtarea = document.getElementById(field_id);

    if (txtarea.textLength > 0){
        q_text_def = (txtarea.value).substring(txtarea.selectionStart, txtarea.selectionEnd);
    }


    var q_text = prompt('Текст цитаты:', q_text_def);
    var q_user = prompt('Автор цитаты (не обязательно):');
    var tag = '';

    if (q_text=='') { return; }

    if (q_user.length > 0){
        tag = '[quote='+q_user+']' + q_text + '[/quote]';
    } else {
        tag = '[quote]' + q_text + '[/quote]';
    }
    
    insertTagAtCursor(field_id, tag);

    return;

} 

function insertAlbumImage(field_id){

    var path = document.msgform.photolist.options[document.msgform.photolist.selectedIndex].value;
    var tag  = '[IMG]/images/users/photos/medium/'+path+'[/IMG]';

    insertTagAtCursor(field_id, tag);

	$('#albumimginsert').hide();

}

function insertTagAtCursor(field_id, tag){

    if (tag == ''){ return; }

    var txtarea     = document.getElementById(field_id);
    var selStart    = txtarea.selectionStart;
    var selEnd      = txtarea.selectionEnd;
    var selLength   = txtarea.textLength;

    var s1 = (txtarea.value).substring(0,selStart);
    var s2 = (txtarea.value).substring(selEnd, selLength);
    txtarea.value = s1 + tag + s2;
    
}

function addAlbumImage(){
	$('#imginsert').hide();
	$('#albumimginsert').toggle();
}

function addTagCut(field_id)
{
    var txtarea = document.getElementById(field_id);
    var cut_text = prompt('Заголовок ссылки на полный текст поста:', 'Читать далее...');
    var tag = '[cut=' + cut_text + ']';
    insertTagAtCursor(field_id, tag);
    return;
}
