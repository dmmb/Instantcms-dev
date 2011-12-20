<?php if(!defined('VALID_CMS')) { die('ACCESS DENIED'); } $inConf     = cmsConfig::getInstance(); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="20;URL=/">
<title>Сайт временно недоступен</title>
<style>
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	background-color:#EBEBEB;
}

</style>
<meta http-equiv="refresh" content="5;URL=/">
</head>						
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle">
		<div style="padding:20px;border:solid 1px gray;background-color:#FFFFFF;text-align:center;width:50%">
			<h2 style="color:#FF6600">Сайт временно недоступен</h2>			
			<div style="padding:20px"><?php echo $inConf->offtext; ?></div>
		</div>
    </td>
  </tr>
</table>
</body></html>