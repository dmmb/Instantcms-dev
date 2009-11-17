<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Блоги', '?view=components&do=config&id='.$_REQUEST['id']);
	
	echo '<h3>Блоги</h3>';
	
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
	$cfg = $inCore->loadComponentConfig('blog');

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['perpage'] = $inCore->request('perpage', 'int');
		$cfg['update_date'] = $inCore->request('update_date', 'int');
		
		$cfg['min_karma_private'] 	= (int)$_REQUEST['min_karma_private'];
		$cfg['min_karma_public'] 	= (int)$_REQUEST['min_karma_public'];	
		$cfg['min_karma'] 			= (int)$_REQUEST['min_karma'];	
		
		$cfg['rss_all']             = $inCore->request('rss_all', 'int');
		$cfg['rss_one']             = $inCore->request('rss_one', 'int');
			
		$inCore->saveComponentConfig('blog', $cfg);
		
		$msg = 'Настройки сохранены.';
	}

	if (!isset($cfg['min_karma_private'])) { $cfg['min_karma_private'] = 0; }
	if (!isset($cfg['min_karma_public'])) {	 $cfg['min_karma_public'] = 0; }
	if (!isset($cfg['min_karma'])) { 		 $cfg['min_karma'] = 0; 		}
	if (!isset($cfg['update_date'])) { 		 $cfg['update_date'] = 1; 		}

	if (!isset($cfg['rss_all'])) { $cfg['rss_all'] = 1; }
	if (!isset($cfg['rss_one'])) { $cfg['rss_one'] = 1; }

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
    
?>
<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
  <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Просмотр блога </h4></td>
    </tr>
    <tr>
      <td valign="top"><strong>Постов на странице в блоге: </strong></td>
      <td width="100" valign="top"><input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" size="5" />
      шт.</td>
    </tr>
    <tr>
      <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Настройки редактирования</h4></td>
    </tr>
    <tr>
      <td valign="top">
            <strong>Обновлять дату поста после редактирования:</strong><br />
            <span class="hinttext">
                Если включено, после редактирования поста его дата будет устанавливаться в текущую
            </span>
      </td>
      <td valign="top">
        <input name="update_date" type="radio" value="1" <?php if (@$cfg['update_date']) { echo 'checked="checked"'; } ?> /> Да
        <input name="update_date" type="radio" value="0" <?php if (@!$cfg['update_date']) { echo 'checked="checked"'; } ?>/> Нет
      </td>
    </tr>
    <tr>
      <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Ограничения по карме</h4></td>
    </tr>

    <tr>
      <td valign="top"><strong>Использовать ограничения:</strong><br />
          <span class="hinttext">Если выключено, то любой пользователь сможет создать блог,<br /> 
          независимо от значения своей кармы </span></td>
      <td valign="top"><input name="min_karma" type="radio" value="1" <?php if (@$cfg['min_karma']) { echo 'checked="checked"'; } ?> />
        Да
        <input name="min_karma" type="radio" value="0" <?php if (@!$cfg['min_karma']) { echo 'checked="checked"'; } ?>/>
        Нет</td>
    </tr>
    <tr>
      <td valign="top"><strong>Создание личного блога:</strong><br />
          <span class="hinttext">Сколько очков кармы нужно для создания личного блога </span></td>
      <td valign="top"><input name="min_karma_private" type="text" id="min_karma_private" value="<?php echo @$cfg['min_karma_private'];?>" size="5" /></td>
    </tr>
    <tr>
      <td valign="top"><strong>Создание коллективного блога:</strong><br />
        <span class="hinttext">Сколько очков кармы нужно для создания коллективного блога </span></td>
      <td valign="top"><input name="min_karma_public" type="text" id="min_karma_public" value="<?php echo @$cfg['min_karma_public'];?>" size="5" /></td>
    </tr>
    <tr>
      <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>RSS лента </h4></td>
    </tr>
    <tr>
      <td valign="top"><strong>Показывать ссылку RSS для всех блогов: </strong></td>
      <td valign="top"><input name="rss_all" type="radio" value="1" <?php if (@$cfg['rss_all']) { echo 'checked="checked"'; } ?> />
Да
  <input name="rss_all" type="radio" value="0" <?php if (@!$cfg['rss_all']) { echo 'checked="checked"'; } ?>/> 
  Нет</td>
    </tr>
    <tr>
      <td valign="top"><strong>Показывать ссылку RSS для каждого блога: </strong></td>
      <td valign="top"><input name="rss_one" type="radio" value="1" <?php if (@$cfg['rss_one']) { echo 'checked="checked"'; } ?> />
Да
  <input name="rss_one" type="radio" value="0" <?php if (@!$cfg['rss_one']) { echo 'checked="checked"'; } ?>/>
Нет</td>
    </tr>
  </table>
  <p>
          <input name="opt" type="hidden" value="saveconfig" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
  </p>
</form>