function makeDump(){
	alert('Экспорт может занять несколько минут.\nНе закрывайте браузер и не переходите на другие страницы до его завершения.\n\nНажмите "Ok" чтобы продолжить.');
	$("#godump").attr('disabled', '1');
	$("div#dumpinfo").html('<span style="background:url(/images/ajax-loader.gif) no-repeat;padding-left:56px;">Идет экспорт базы, подождите...</span>');
	$("form#dump").ajaxSubmit({ 
		  success: function(msg) {
			$("div#dumpinfo").html(msg);
			$("#godump").attr('disabled', '');
		  }
	  });	
}

function importDump(){
	if(confirm('Внимание!\n\nВы действительно хотите восстановить базу данных из резервной копии?\nНекоторые изменения на сайте могут быть утеряны.')){
		alert('Импорт может занять несколько минут.\nНе закрывайте браузер и не переходите на другие страницы до его завершения!\n\nНажмите "Ok" чтобы продолжить.');
		$("#goimport").attr('disabled', '1');
		$("div#importinfo").html('<span style="background:url(/images/ajax-loader.gif) no-repeat;padding-left:56px;">Идет импорт базы, подождите...</span>');
		$("form#importdump").ajaxSubmit({ 
			  success: function(msg) {
				$("div#importinfo").html(msg);
				$("#goimport").attr('disabled', '');
			  }
		  });
	}
}

function deleteDump(file){
	$("div#dumpinfo").load("/core/ajax/dumper.php", {opt: "delete", file: file});	
}