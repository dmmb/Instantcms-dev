function checkLogin(){
	var userlogin = $("#logininput").attr('value');
    var reg= /^[a-zA-Z0-9_]{2,15}$/ ;
    if (reg.test(userlogin))
    {
        $("#logincheck").load("/core/ajax/registration.php", {opt: "checklogin", data:userlogin});
           
    }else
    {
		if (userlogin.length < 2){
			$("#logincheck").html('<span style="color:red">Минимальная длина = 2</span>'); 
		} else {
			if (userlogin.length > 15){
				$("#logincheck").html('<span style="color:red">Максимальная длина = 15</span>'); 
			} else {
				$("#logincheck").html('<span style="color:red">Только латинские буквы и цифры</span>');  		
			}
		}	     
    }
}

function checkPasswords(){
	var pass1 = $("#pass1input").attr('value');
	var pass2 = $("#pass2input").attr('value');	
	if (pass1 == pass2) {
		$('#passcheck').html('<span style="color:green">Пароли совпадают</span>');
	} else {
		$('#passcheck').html('<span style="color:red">Пароли не совпадают!</span>');	
	}	
}