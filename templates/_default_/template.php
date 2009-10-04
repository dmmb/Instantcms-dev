<?php if(!defined('VALID_CMS')) { die('ACCESS DENIED'); } 
/*********************************************************************************************/
//																							 //
// 						     design by Vladimir E. Obukhov, 2008                             //
//                                                                                           //
/*********************************************************************************************/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- HEAD !-->
<?php cmsPrintHead(); ?>
<link href="/templates/_default_/css/styles.css" rel="stylesheet" type="text/css" />
<link type='text/css' href='/templates/_default_/basic/css/basic.css' rel='stylesheet' media='screen' />
<!--[if lt IE 7]>
<link type='text/css' href='/templates/modern/basic/css/basic_ie.css' rel='stylesheet' media='screen' />
<![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>
<script src='/templates/_default_/basic/js/jquery.simplemodal.js' type='text/javascript'></script>
<script src='/templates/_default_/basic/js/basic.js' type='text/javascript'></script>
<script type="text/javascript" src="/templates/_default_/jquery.iepnghack.1.6.js"></script>
<script type="text/javascript">$(function(){ $("#logoimg").pngfix(); })</script>
</head>

<body>
<div id="authModal" style='display:none'>
<form id="authform" target="_self" style="margin: 0px;" name="authform" method="post" action="/login">
	<table>
		<tr>
			<td colspan="2" id="authtitle"><div>Авторизация</div></td>
		</tr>
		<tr>
			<td id="authtd">Логин:</td>
			<td id=""><input name="login" type="text" id="authinput" /></td>
		</tr>
		<tr>
			<td id="authtd">Пароль:</td>
			<td id=""><input name="pass" type="password" id="authinput" /></td>			
		</tr>
		<tr>
			<td id="authtd">&nbsp;</td>
			<td id=""><div id="remember"><input id="remember" type="checkbox" value="1" name="remember"/>Запомнить пароль</div></td>			
		</tr>
		<tr>
			<td id="authtd">&nbsp;</td>
			<td id=""><input name="auth" type="submit" value="Войти" id="authbtn"/></td>			
		</tr>
	
	</table>
</form>
</div>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" id="outertable">
  <tr>
    <td height="143" valign="top"><table width="100%" height="143" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="17" colspan="4">&nbsp;</td>
        </tr>
      <tr>
        <td width="15" height="126" style="background:url(/templates/_default_/images/topbgl.jpg) repeat-y;">&nbsp;</td>
        <!-- SITENAME --->
		<td width="473" height="126" id="logotd" style="background:url(/templates/_default_/images/logobg.jpg) no-repeat;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="43" style="padding-left:15px"><a href="/"><img id="logoimg" src="/templates/_default_/images/logoicon.png" border="0"/></a></td>
            <td><div id="sitename"><?php cmsPrintSitename(); ?></div></td>
          </tr>
        </table></td>
		 <!-- HEADING TOOLS --->
        <td valign="top" style="background:url(/templates/_default_/images/topbg.jpg) repeat-x;">
		<table width="370" border="0" align="right" cellpadding="0" cellspacing="0" id="headtools">
          <tr>
            <td>
			  <!-- Если юзер не авторизован, показываем ему ссылки с регистрацией и авторизацией -->
  			  <?php if(!$inUser->id){ ?> 
				<a href="/registration" id="ht_reg">Регистрация</a>
				<a href="javascript:auth()" id="ht_auth">Авторизация</a>
				<a href="javascript:window.external.AddFavorite('http://<?php echo $_SERVER['HTTP_HOST']?>/', '<?php cmsPrintSitename(); ?>')" id="ht_fav">В избранное</a>
			  <?php } else { 
							$uid    = $inUser->id;
							$newmsg = usrNewMessages($inUser->id); 			  	
			  ?>
				<a href="/users/0/<?php echo $uid?>/profile.html" id="ht_profile">Мой профиль</a>
				<?php if (!$newmsg) { ?>	
					<a href="/users/0/<?php echo $uid?>/messages.html" id="ht_messages">Cообщения</a>
				<?php } else { ?>
					<a style="color:#F60;" href="/users/0/<?php echo $uid?>/messages.html" id="ht_messages">Cообщения <?php echo strip_tags($newmsg)?></a>
				<?php } ?>
				<a href="/logout" id="ht_logout">Выход</a>
			  <?php } ?>			</td>
          </tr>
          <tr>
            <td>
                <div id="bar_search">
                        <form name="searchform" action="/index.php" method="get">
                            <input type="hidden" name="view" value="search" />
                          <input name="query" type="text" id="searchinput" /><input name="gosearch" id="searchbtn" type="image" src="/templates/_default_/images/searchbtn.png" alt="Поиск" width="22" height="23"  onclick="document.searchform.submit()"/>
                        </form>
                </div>
            </td>
          </tr>
        </table></td>
        <td width="15" height="126" style="background:url(/templates/_default_/images/topbgr.jpg) repeat-y;">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="100%" valign="top"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="15" height="126" style="background:url(/templates/_default_/images/midbgl.jpg) repeat-y;">&nbsp;</td>
        <td valign="top" style="background:#FFF">
		<div>
		 <?php cmsPathway('&raquo;'); ?>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" id="bodytable">
          <tr>
            <?php if (cmsCountModules("left")) { ?>
			<td width="235" valign="top" id="left"><?php cmsModule("left"); ?></td>
			<?php } ?>
            <td valign="top" id="center">
				<div><?php cmsModule("top"); ?></div>
				<div><?php cmsBody(); ?></div>
				<div><?php cmsModule("bottom"); ?></div>	
			</td>
            <?php if (cmsCountModules("right")) { ?>			
            <td width="235" valign="top" id="right"><?php cmsModule("right"); ?></td>
			<?php } ?>
          </tr>
        </table></td>
        <td width="15" height="126" style="background:url(/templates/_default_/images/midbgr.jpg) repeat-y;">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="80" valign="top"><table width="100%" height="80" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="15">&nbsp;</td>
		<!-- FOOTER ---> 
        <td id="footertd" style="background:url(/templates/_default_/images/footerbg.jpg) repeat-x;"><table width="100%" height="80" border="0" cellpadding="15" cellspacing="0">
          <tr>
            <td>
				<div id="footer">
					<a href="/"><?php cmsPrintSitename(); ?></a> &copy; <?php echo date('Y')?>
				</div>
			</td>
            <td width="250" align="right">
				<a href="http://www.instantcms.ru/">InstantCMS</a>
			</td>
          </tr>
        </table></td>
        <td width="15">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>