function addSmile(tag, field_id){
	var txtarea = document.getElementById(field_id);
	txtarea.value += ' :' + tag + ': ';
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
   var txtarea = document.getElementById(field_id);
   var link_url = prompt('Адрес ссылки (URL):');
   var link_name = prompt('Название ссылки (не обязательно):');   
   if (link_url=='') { return; }   
   if (link_name.length > 0){txtarea.value += '[url='+link_url+']' + link_name + '[/url]';} 
   else {txtarea.value += '[url]' + link_url + '[/url]';}
   return;
} 

function addTagEmail(field_id)
{
   var txtarea = document.getElementById(field_id);
   var link_url = prompt('Адрес электронной почты:');  
   if (link_url.length > 0){txtarea.value += '[email]' + link_url + '[/email]';}
	return;
}

function addTagVideo(field_id)
{
   var txtarea = document.getElementById(field_id);
   var link_url = prompt('Код видео (Youtube/Rutube):');  
   if (link_url.length > 0){txtarea.value += '[video]' + link_url + '[/video]';}
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
   var txtarea = document.getElementById(field_id);
	txtarea.value += '[IMG]/upload/'+placekind+'/'+data+'[/IMG]';
	return;
}

function addTagQuote(field_id)
{
   var txtarea = document.getElementById(field_id);
   var q_text = '';
   q_text = prompt('Текст цитаты:');
   var q_user = prompt('Автор цитаты (не обязательно):');   
   if (q_text=='') { return; }   
   if (q_user.length > 0){txtarea.value += '[quote='+q_user+']' + q_text + '[/quote]';} 
   else {txtarea.value += '[quote]' + q_text + '[/quote]';}
   return;
} 

function insertAlbumImage(field_id){
	var path = document.msgform.photolist.options[document.msgform.photolist.selectedIndex].value;
	document.msgform.message.value += '[IMG]/images/users/photos/medium/'+path+'[/IMG]';
	$('#albumimginsert').hide();
}

function addAlbumImage(){
	$('#imginsert').hide();
	$('#albumimginsert').toggle();
}

function addTagCut(field_id)
{
   var txtarea = document.getElementById(field_id);
   var cut_text = prompt('Заголовок ссылки на полный текст поста:', 'Читать далее...');
   if (cut_text.length > 0){ txtarea.value += '[cut=' + cut_text + ']'; }
	return;
}
