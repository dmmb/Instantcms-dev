function repairTrees(){
	if(confirm('Внимание:\nИсправление деревьев восстановит их функциональность, но будет потеряна вложенность элементов (т.е. все разделы будут перенесены в корневой).\n\nПродолжить?')){
		$('#repairform').submit();
	}
}