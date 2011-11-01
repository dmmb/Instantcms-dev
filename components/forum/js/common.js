function deleteThread(link){
	if(confirm('Вы уверены что хотите удалить тему?')){
		window.location.href = link;
	}
}

function goForum(){
	var forum_id = $('#goforum').attr('value');
	var link = '/forum/' + forum_id;
	window.location.href = link;
}

function addQuoteText(author){

    var seltext = '';

	if (window.getSelection) {
		var seltext = window.getSelection();
	} else if (document.getSelection) {
		var seltext = document.getSelection();
	} else if (document.selection) {
		var seltext = document.selection.createRange().text;
	}

	if (seltext){
		var quote = '[quote='+author+']' + seltext + '[/quote]' + "\n";
		var msg = $('textarea#message').val() + quote;
		$('textarea#message').val(msg);
	}

}