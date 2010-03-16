<!xml version="1.0" encoding="windows-1251">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>InstantCMS - Авторизация</title>
<style type="text/css">
<!--
html{
	height:100%;
}
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	height:100%;
	margin:0px;
	background:#FFF;
}
#inputs {
	padding:0px;
}
#inputs div{
	margin:2px;
	padding:0px;
	margin-left:15px;
	width:245px;
	height:40px;
	background:url(/admin/images/auth/input.gif) no-repeat;
	
}
#login, #pass{
	display:inline;
	width:245px;
	height:40px;
	background:none;
	padding:9px;
	border:none;
	font-size:18px;
	margin-left:10px;
}
#title{
	font-size:18px;
	font-weight:bold;
	text-align:center;
	color:#003366;
	margin:10px;
}
#title span{
	color:#FF3300;
}
#gobtn{
	width:64px;
	height:65px;
	margin-right:15px;
}
#copy{
	text-align:center;
	margin:10px;
}

-->
</style>
</head>

<body onLoad="document.loginform.login.focus();">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><table width="376" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
			<div id="title">
				InstantCMS - <span>Авторизация</span>
			</div>
		</td>
      </tr>
      <tr>
        <td height="114">
			<form action="/login" method="post" name="loginform" target="_top" id="loginform">
			<input type="hidden" name="is_admin" value="1" />
			<table width="378" height="115" border="0" cellpadding="0" cellspacing="0" style="background:url(/admin/images/auth/auth_bg.jpg) no-repeat;">
			  <tr>
				<td valign="middle" id="inputs">
					<div><input name="login" type="text" id="login" /></div>
					<div><input name="pass" type="password" id="pass" /></div>
				</td>
				<td>
					<input name="go" id="gobtn" type="image" src="/admin/images/auth/loginbtn.gif" alt="Вход" width="60" height="67"  onclick="document.loginform.submit()"/>
				</td>
			  </tr>
			</table>
			</form>
		</td>
      </tr>
	  <tr>
	  <td>
	  	<div id="copy"><a href="http://www.instantcms.ru/">InstantCMS</a> &copy; 2007 - <?php echo date('Y'); ?></div>
	  </td>
	  </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
