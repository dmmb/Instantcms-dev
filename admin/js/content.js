function checkGroupList(){

	if ($('#is_public').attr('checked')){
		$('select#showin').attr('disabled', 'disabled');
	} else {
		$('select#showin').attr('disabled', '');
	}

}

function sendContentForm(opt, object_id, subject_id){

    var link = 'index.php?view=content&do='+opt;

    if (object_id && object_id.length>0) { link = link + '&obj_id='+ object_id; }

    if (subject_id>0) { link = link + '&subj_id='+ subject_id; }

    var sel  = checked();

    if (sel){
        if (opt!='delete' || confirm('Удалить отмеченные статьи ('+sel+' шт.)?')){

            document.selform.action = link;
            document.selform.submit();

        }
    } else {
        alert('Нет отмеченных статей');
    }

}

function moveItem(item_id, dir){

    var cat_id = $('#filter_form input[name=cat_id]').val();

	$.ajax({
		  type: "POST",
		  url: "index.php",
		  data: "view=content&do=move&id="+item_id+"&cat_id="+cat_id+"&dir="+dir,
		  success: function(msg){
            var trh = $('#listTable tr#'+item_id).html();
            if (dir == -1){
                $('#listTable tr#'+item_id).prev('tr').before('<tr id="'+item_id+'">'+trh+'</tr>').next('tr').remove();
            }
            if (dir == 1){
                $('#listTable tr#'+item_id).next('tr').after('<tr id="'+item_id+'">'+trh+'</tr>').prev('tr').remove();
            }
            $('#listTable tr').find('.move_item_up').show();
            $('#listTable tr').find('.move_item_down').show();
            $('#listTable tr').eq(1).find('.move_item_up').hide();
            $('#listTable tr').eq($('#listTable tr').length-1).find('.move_item_down').hide();
            $('#listTable tr#'+item_id).animate( { opacity:0.01 }, 200 ).animate( { opacity:1 }, 200 );
		  }
	});
}

function deleteCat(cat_name, cat_id){

    var sure = confirm('Удалить раздел "'+cat_name+'" и подразделы?');

    if (!sure){ return; }

    var is_with_content = confirm('Удалить все вложенные статьи?');

    var link = '?view=cats&do=delete&id='+cat_id;

    if (!is_with_content){
        window.location.href = link;
    } else {
        window.location.href = link + '&content=1';
    }

}