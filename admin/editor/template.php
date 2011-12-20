<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php editorHead(); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles.css" rel="stylesheet" type="text/css" />

</head>

<body>
<table width="100%" height="50" border="0" cellpadding="0" cellspacing="0" style="background-color:#2D557D">
  <tr>
    <td style="padding-left:10px">
        <h2><a style="color:#fff;text-decoration:none;" href="index.php"><?php echo $inConf->sitename; ?> &mdash; Панель редактора</a></h2>
    </td>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td align="right">&nbsp;</td>
        <td width="200" align="right" style="padding-right:15px">
            <div style="color:#FFFFFF"><span class="style1"><a style="color:#0CF" href="http://www.instantcms.ru/">InstantCMS</a> v<?php echo CORE_VERSION; ?></div>
            <a href="/logout" target="_top" class="exitlink">Выход из системы</a></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" height="40" border="0" cellpadding="1" cellspacing="0" bgcolor="#EBEBEB" style="border-bottom:double 3px gray">
  <tr>
    <td><?php editorMenu(); ?></td>
  </tr>
</table>
<table width="100%" height="515" border="0" cellpadding="10" cellspacing="0" class="maintable">
  <tr>
    <td valign="top"><?php editorPage(); ?></td>
  </tr>
</table>
<table width="100%" height="50" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle"><a href="http://www.instantcms.ru/"><strong>InstantCMS</strong></a><strong> v<?php echo CORE_VERSION; ?> &copy; <?php echo date('Y'); ?></strong>      
  </tr>
</table>
</body>
</html>
