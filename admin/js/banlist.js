function loadUserIp(){
	$.ajax({
	  type: "POST",
	  url: "/core/ajax/getip.php",
	  data: "user_id="+$('#user_id').attr('value'),
	  success: function(msg){
		$('#ip').val(msg);
	  }
	});
}

