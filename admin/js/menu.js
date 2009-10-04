function selectIcon(file){
	document.addform.iconurl.value = file;
	document.getElementById('icondiv').style.display = "none";
	document.getElementById('iconlink').style.display = "block";	
}

function showIcons(){
	document.getElementById('icondiv').style.display = "block";
	document.getElementById('iconlink').style.display = "none";
}

function hideIcons(){
	document.getElementById('icondiv').style.display = "none";
	document.getElementById('iconlink').style.display = "block";
}

function highlight(tableId){
	
	document.getElementById('t_link').style.border = 'none';
	document.getElementById('t_content').style.border = 'none';
	document.getElementById('t_category').style.border = 'none';
	document.getElementById('t_pricecat').style.border = 'none';
	document.getElementById('t_uccat').style.border = 'none';
	document.getElementById('t_blog').style.border = 'none';
	document.getElementById('t_component').style.border = 'none';

	document.getElementById(tableId).style.border = 'solid 1px #0099CC';

}