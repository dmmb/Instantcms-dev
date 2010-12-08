function goPage(dir){
	var newid = document.pageform.page.selectedIndex + dir;
	document.pageform.page.selectedIndex = 	newid;
	window.location.href="files"+(newid+1)+".html";
	
}

function goToPage(dir){
	var newid = document.pageform.page.selectedIndex + 1;
	window.location.href="files"+newid+".html";	
}

function orderPage(field){
	$("#orderby").attr('value', field);
	document.orderform.submit();
}

function checkSelFiles(){
	var sel =false;
	for(i=0; i<25; i++){
	 if($("#fileid"+i).attr('checked')){
		sel = true; 
	 }
	}
	return sel;
}

function delFiles(){
	var sel = checkSelFiles();
	if (sel == false){
	 	alert('Нет выбранных файлов!');	
	} else {
		$("#listform").attr('action', 'delfilelist.html');
		document.listform.submit();	
	}	
}

function pubFiles(flag){
	var sel = false;
	for(i=0; i<25; i++){
	 if($("#fileid"+i).attr('checked')){
		sel = true; 
	 }
	}
	if (sel == false){
	 	alert('Нет выбранных файлов!');	
	} else {
		if(flag==1){
		 $("#listform").attr('action', 'showfilelist.html');
		} else {
		 $("#listform").attr('action', 'hidefilelist.html');
		}
		document.listform.submit();	
	}	
}
