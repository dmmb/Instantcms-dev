<!xml version="1.0" encoding="windows-1251">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
	define("VALID_CMS", 1);	
	define('PATH', $_SERVER['DOCUMENT_ROOT']);
	require("../core/cms.php");
    $inCore = cmsCore::getInstance();
    define('HOST', 'http://' . $inCore->getHost());
	$inCore->loadClass('user');
	$inCore->loadClass('db');
	$inCore->loadClass('config');
	$inConf = cmsConfig::getInstance();
	$inUser = cmsUser::getInstance();

	$domain = str_replace('http://', '', HOST);
	if(!strstr($_SERVER['HTTP_REFERER'], $domain)) { cmsCore::error404(); }

	if ( !$inUser->update() ) { cmsCore::error404(); }
	// проверяем доступ по Ip
	if(!$inCore->checkAccessByIp($inConf->allow_ip)) { $inCore->halt('ACCESS DENIED'); }
	date_default_timezone_set($inConf->timezone);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>InstantCMS - Авторизация</title>
<style type="text/css">
<!--
    html{ height:100%; }
    body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; height:100%; margin:0px; background:#FFF; }
    #top{ display:block; width:368px; height:30px; background:url(images/auth/top.jpg) no-repeat center top; }
    #form{ display:block; width:368px; height:178px; background:url(images/auth/form.jpg) no-repeat center top; }
    #fields { padding-top: 25px; }
    #fields .field { display:block; width:270px; height:34px; text-align: right; margin-bottom: 6px; }
    #fields .field input { width:230px; font-size:16px; margin-right:10px; margin-top:6px; border:none; background: none; }
    #fields .login { background:url(images/auth/login.png) no-repeat; }
    #fields .passw { background:url(images/auth/passw.png) no-repeat; }
    .button{ display:block; width:100px; height: 32px; background:url(images/auth/btn.png) no-repeat; cursor: pointer; margin-top:25px; }
    .button:hover{ background:url(images/auth/btn_hover.png) no-repeat; }
    #copy { margin-top:15px; }
    #copy a { color: #316294; }
}
-->
</style>
</head>

<body onLoad="document.loginform.login.focus();">
    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" valign="middle">

                <div id="top"></div>
                <div id="form">
                    <form action="/login" method="post" name="loginform" target="_top" id="loginform">
                        <input type="hidden" name="is_admin" value="1" />
                        <div id="fields">
                            <div class="field login"><input name="login" type="text" id="login" /></div>
                            <div class="field passw"><input name="pass" type="password" id="pass" /></div>
                        </div>
                        <div class="button" onclick="document.loginform.submit()"></div>
                        <input type="submit" name="go" style="display:none" />
                    </form>
                </div>
                <div id="copy"><a href="http://www.instantcms.ru/">InstantCMS</a> &copy; 2007-<?php echo date('Y'); ?></div>

            </td>
        </tr>
    </table>
</body>
</html>
