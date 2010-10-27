function deleteArticle(id){
	if(confirm('Удалить статью?')){
		window.location.href = '/content/delete'+id+'.html';	
	}
}
