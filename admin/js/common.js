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