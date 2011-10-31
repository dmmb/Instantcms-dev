<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
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


	cpAddPathway('RSS генератор', '?view=components&do=config&id='.$_REQUEST['id']);
	
	echo '<h3>RSS генератор</h3>';
	
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = 'Сохранить';
	$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

	$toolmenu[1]['icon'] = 'cancel.gif';
	$toolmenu[1]['title'] = 'Отмена';
	$toolmenu[1]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('rssfeed');

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['addsite'] = $_REQUEST['addsite'];
		$cfg['maxitems'] = $_REQUEST['maxitems'];		
		
		$cfg['icon_on'] = $_REQUEST['icon_on'];
		$cfg['icon_url'] = $_REQUEST['icon_url'];
		$cfg['icon_title'] = $_REQUEST['icon_title'];
		$cfg['icon_link'] = $_REQUEST['icon_link'];								
			
		$inCore->saveComponentConfig('rssfeed', $cfg);
		
		$msg = 'Настройки сохранены.';
	}

	global $_CFG;

	if(!isset($cfg['addsite'])) { $cfg['addsite'] = 1; }
	if(!isset($cfg['icon_url'])) { $cfg['icon_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/images/rss.png'; }
	if(!isset($cfg['icon_link'])) { $cfg['icon_link'] = 'http://'.$_SERVER['HTTP_HOST'].'/'; }
	if(!isset($cfg['icon_title'])) { $cfg['icon_title'] = $_CFG['sitename']; }
	if(!isset($cfg['maxitems'])) { $cfg['maxitems'] = 50; }

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table width="650" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td colspan="2" bgcolor="#EBEBEB"><strong>Каналы</strong></td>
          </tr>
          <tr>
            <td>Добавлять название сайта в заголовки RSS-каналов:</td>
            <td width="300" valign="top"><input name="addsite" type="radio" value="1" <?php if (@$cfg['addsite']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="addsite" type="radio" value="0" <?php if (@!$cfg['addsite']) { echo 'checked="checked"'; } ?>/>
            Нет </td>
          </tr>
          <tr>
            <td>Максимальное число записей для вывода: </td>
            <td valign="top"><input name="maxitems" type="text" id="maxitems" size="6" value="<?php echo @$cfg['maxitems'];?>"/> 
              шт. </td>
          </tr>
        </table>
        <table width="650" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-top:2px">
          <tr>
            <td colspan="2" bgcolor="#EBEBEB"><strong>Иконка RSS-каналов </strong></td>
          </tr>
          <tr>
            <td>Использовать иконку:</td>
            <td width="300" valign="top"><input name="icon_on" type="radio" value="1" <?php if (@$cfg['icon_on']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="icon_on" type="radio" value="0" <?php if (@!$cfg['icon_on']) { echo 'checked="checked"'; } ?>/>
            Нет </td>
          </tr>
          <tr>
            <td>URL иконки (включая адрес сайта): </td>
            <td valign="top"><input name="icon_url" type="text" id="icon_url" size="45" value="<?php echo @$cfg['icon_url'];?>"/></td>
          </tr>
          <tr>
            <td>Заголовок:</td>
            <td valign="top"><input name="icon_title" type="text" id="icon_title" size="45" value="<?php echo @$cfg['icon_title'];?>"/></td>
          </tr>
          <tr>
            <td>Ссылка:</td>
            <td valign="top"><input name="icon_link" type="text" id="icon_link" size="45" value="<?php echo @$cfg['icon_link'];?>"/></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" value="saveconfig" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
        </p>
</form>