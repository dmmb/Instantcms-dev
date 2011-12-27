//FILES ATTACH
function toggleFilesAttach(){
	s = document.getElementById("fa_entries").style.display;
	
	if (s == "block"){
		document.getElementById("fa_entries").style.display = "none";		
	} else {
		document.getElementById("fa_entries").style.display = "block";				
	}

}
function showFaEntry(id){
		var preid = id - 1;
		$("#fa_entry"+id).show();
		$("#fa_entry_btn"+preid).hide();
		document.msgform['fa_count'].value++;		
}

function hideFaEntry(id){
		var preid = id - 1;			
		document.msgform['fa_count'].value--;				
		$("#fa_entry"+id).hide();
		$("#fa_entry_btn"+preid).show();
}

//POLLS ATTACH
function togglePollsAttach(){

	s = document.getElementById("pa_entries").style.display;
	
	if (s == "block"){
		document.getElementById("pa_entries").style.display = "none";		
	} else {
		document.getElementById("pa_entries").style.display = "block";				
	}

}
function showPaEntry(id){
		preid = id - 1;
		document.getElementById("pa_entry"+id).style.display = "block";		
		document.getElementById("pa_entry_btn"+preid).style.display = "none";				
}

function hidePaEntry(id){
		var preid = id - 1;			
		document.getElementById("pa_entry"+id).style.display = "none";
		document.getElementById("pa_entry_input"+id).value = '';		
		document.getElementById("pa_entry_btn"+preid).style.display = "block";						
}

