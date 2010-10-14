function addComment(sess_md5, target, target_id, parent_id){
	$('div.reply').html('').hide();
    $("#cm_addentry"+parent_id).html("<div>Загрузка формы...</div>");
	$("#cm_addentry"+parent_id).load("/components/comments/addform.php", {cd: sess_md5, target: target, target_id: target_id, parent_id: parent_id}, cmLoaded());
	$("#cm_addentry"+parent_id).slideDown("slow");
}

function cmLoaded() {
    //$("#content").autogrow();
}

function cancelComment(){
	$("#cm_addentry").slideUp("slow");
}

function expandComment(id){
	$('a#expandlink'+id).hide();
	$('div#expandblock'+id).show();
}

function addSmile(tag, field_id){
	var txtarea = document.getElementById(field_id);
	txtarea.value += ' :' + tag + ': ';
	$('#smilespanel').hide();
}

function loadComments(target, target_id, anchor){

    $('div.cm_ajax_list').html('<p style="margin:30px; margin-left:0px; padding-left:50px; background:url(/images/ajax-loader.gif) no-repeat">загрузка комментариев...</p>');

    $.ajax({
			type: "POST",
			url: "/components/comments/comments.php",
			data: "target="+target+"&target_id="+target_id,
			success: function(data){
				$('div.cm_ajax_list').html(data);
                $('td.loading').html('');
                if (anchor){
                    window.location.hash = anchor.substr(1, 100);
                    $('a[@href='+anchor+']').css('color', 'red').attr('title', 'Вы пришли на страницу по этой ссылке');
                }
			}
    });

}

function goPage(dir, field, target, target_id){

	var p = Number($('#'+field).attr('value')) + dir;
    loadComments(target, target_id, p);

}

function voteComment(comment_id, vote){

    $('span#votes'+comment_id).html('<img src="/images/ajax-loader.gif" border="0"/>');
    $.ajax({
			type: "POST",
			url: "/components/comments/vote.php",
			data: "comment_id="+comment_id+"&vote="+vote,
			success: function(data){
				$('span#votes'+comment_id).html(data);
			}
    });

}