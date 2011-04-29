function copyItem(com_id, item_id){
	var copies = prompt('Количество копий:', 1);
	if (copies>0){
		window.location.href='/admin/index.php?view=components&do=config&id='+com_id+'&opt=copy_item&item_id='+item_id+'&copies='+copies;	
	}
}

function copyCat(com_id, item_id){
	var copies = prompt('Количество копий:', 1);
	if (copies>0){
		window.location.href='/admin/index.php?view=components&do=config&id='+com_id+'&opt=copy_cat&item_id='+item_id+'&copies='+copies;	
	}
}

function xlsEditRow(){
    var r = $('input#title_row').val();
    $('input.row').val(r);
}

function xlsEditCol(){
    var c = Number($('input#title_col').val());

    $("input.col").each(function (i) {
        $(this).val(i+c+1);
    });
}

function ignoreRow(row){
    var r_id = 'row_'+row;
    var c_id = 'ignore_'+row;
    var checked = Number($('input:checkbox[id='+c_id+']').attr('checked'));
    if(checked){
        $('tr#'+r_id+' input:text[class!=other]').attr('disabled', 'disabled');
        $('tr#'+r_id+' input:text[class=other]').attr('disabled', '');
    } else {
        $('tr#'+r_id+' input:text[class!=other]').attr('disabled', '');
        $('tr#'+r_id+' input:text[class=other]').attr('disabled', 'disabled');
    }
}

function toggleDiscountLimit(){
    var sign = Number($('select#sign').val());

    if (sign==3){ $('tr.if_limit').show(); }
    else { $('tr.if_limit').hide(); }
}

function checkGroupList(){

	if(document.addform.is_public.checked){
		$('select#showin').attr('disabled', '');
        $('input#can_edit').attr('disabled', '');
	} else {
		$('select#showin').attr('disabled', 'disabled');
        $('input#can_edit').attr('disabled', 'disabled');
        $('input#can_edit').attr('checked', '');
	}

}

function toggleAdvert(){
    if ($('select#view_type').val() == 'shop') {
        $('.advert').show();
    } else {
        $('.advert').hide();
    }
}
