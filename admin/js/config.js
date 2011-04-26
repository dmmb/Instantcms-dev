function makeDump(){
	alert('������� ����� ������ ��������� �����.\n�� ���������� ������� � �� ���������� �� ������ �������� �� ��� ����������.\n\n������� "Ok" ����� ����������.');
	$("#godump").attr('disabled', '1');
	$("div#dumpinfo").html('<span style="background:url(/images/ajax-loader.gif) no-repeat;padding-left:56px;">���� ������� ����, ���������...</span>');
	$("form#dump").ajaxSubmit({ 
		  success: function(msg) {
			$("div#dumpinfo").html(msg);
			$("#godump").attr('disabled', '');
		  }
	  });	
}

function importDump(){
	if(confirm('��������!\n\n�� ������������� ������ ������������ ���� ������ �� ��������� �����?\n��������� ��������� �� ����� ����� ���� �������.')){
		alert('������ ����� ������ ��������� �����.\n�� ���������� ������� � �� ���������� �� ������ �������� �� ��� ����������!\n\n������� "Ok" ����� ����������.');
		$("#goimport").attr('disabled', '1');
		$("div#importinfo").html('<span style="background:url(/images/ajax-loader.gif) no-repeat;padding-left:56px;">���� ������ ����, ���������...</span>');
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