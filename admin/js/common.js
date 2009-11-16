function jsmsg(msg, link){
	if(confirm(msg)){
		window.location.href = link;	
	}
}

function checked(){
	var c = 0;
	for (var i=0; i<document.selform.length; i++){
		if(document.selform.elements[i].name == 'item[]'){
			if(document.selform.elements[i].checked){
				c = c + 1;	
			}
		}
	}
	return c;
}

function checkSel(link){
	var ch = 0;
	for (var i=0; i<document.selform.length; i++){
		if(document.selform.elements[i].name == 'item[]'){
			if(document.selform.elements[i].checked){
				ch++;
			}
		}
	}

	if (ch>0){
		document.selform.action = link;
		document.selform.submit();
	} else { alert('Ничего не выбрано!'); }

}

function sendForm(link){
	document.selform.action = link;
	document.selform.submit();
}

function invert(){
	for (var i=0; i<document.selform.length; i++){
		if(document.selform.elements[i].name == 'item[]'){
			document.selform.elements[i].checked = !document.selform.elements[i].checked;
		}
	}
}

function install(href){
	$('div.update_process').show();
	$('div.update_go').hide();
	window.location.href=href;
}

function activateListTable(){
	$('table.tablesorter').tablesorter({headers: {0: {sorter: false}}});
	
	if (!$.browser.msie || $.browser.version != '6.0'){	
		$('table.tablesorter').columnFilters();
	}
}

function pub(id, qs, qs2, action, action2){
	$('img#pub'+id).attr('src', 'images/actions/loader.gif');
    $('a#publink'+id).attr('href', '');
	$.ajax({
		  type: "GET",
		  url: "index.php",
		  data: qs,
		  success: function(msg){
			$('img#pub'+id).attr('src', 'images/actions/'+action+'.gif');	
			$('a#publink'+id).attr('href', 'javascript:pub('+id+', "'+qs2+'", "'+qs+'", "'+action2+'", "'+action+'");');
		  }
	});
}

function showIns(){

	document.getElementById('material').style.display = 'none';
	document.getElementById('album').style.display = 'none';
	document.getElementById('photo').style.display = 'none';
	document.getElementById('price').style.display = 'none';
	document.getElementById('blank').style.display = 'none';
	document.getElementById('frm').style.display = 'none';
	document.getElementById('filelink').style.display = 'none';
	document.getElementById('include').style.display = 'none';
	document.getElementById('banpos').style.display = 'none';

	needDiv = document.addform.ins.options[document.addform.ins.selectedIndex].value;

	document.getElementById(needDiv).style.display = "table-row";

}

function insertTag(kind){
	var oEditor = FCKeditorAPI.GetInstance('content') ;

	var text = '';

	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG ) {

		if (kind=='material'){
			text = '{МАТЕРИАЛ=' + document.addform.m.options[document.addform.m.selectedIndex].text + '}';
		}
		if (kind=='photo'){
			text = '{ФОТО=' + document.addform.f.options[document.addform.f.selectedIndex].text + '}';
		}
		if (kind=='album'){
			text = '{АЛЬБОМ=' + document.addform.a.options[document.addform.a.selectedIndex].text + '}';
		}
		if (kind=='price'){
			text = '{ПРАЙС=' + document.addform.p.options[document.addform.p.selectedIndex].text + '}';
		}
		if (kind=='frm'){
			text = '{ФОРМА=' + document.addform.fm.options[document.addform.fm.selectedIndex].text + '}';
		}
		if (kind=='blank'){
			text = '{БЛАНК=' + document.addform.b.options[document.addform.b.selectedIndex].text + '}';
		}
		if (kind=='include'){
			text = '{ФАЙЛ=' + document.addform.i.value + '}';
		}
		if (kind=='filelink'){
			text = '{СКАЧАТЬ=' + document.addform.fl.value + '}';
		}
		if (kind=='banpos'){
			text = '{БАННЕР=' + document.addform.ban.value + '}';
		}
		if (kind=='page'){
			text = '{pagebreak}';
		}

		oEditor.InsertHtml( text ) ;
	}
	else alert( 'Переключите редактор в визуальный режим!' ) ;
}

function InsertPagebreak()
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('content') ;

	// Check the active editing mode.
	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
	{
		// Insert the desired HTML.
		oEditor.InsertHtml( '{pagebreak}' ) ;
	}
	else
		alert( 'Переключите редактор в визуальный режим!' ) ;
}