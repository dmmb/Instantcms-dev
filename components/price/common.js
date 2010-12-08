// JavaScript Document
function checkSumm(total){
	var i=1;
	var summ = 0;

	for(i=1; i<=total; i++){
		if (document.listform["item"+i].checked) {
		 	summ += Number(document.listform["price"+i].value) * Number(document.listform["kolvo["+document.listform["item"+i].value+"]"].value);
			document.getElementById("kdiv"+i).style.display = "block";
		} else {
			document.getElementById("kdiv"+i).style.display = "none";	
		}
	}
	
	document.listform.summField.value = String(summ);
	
}