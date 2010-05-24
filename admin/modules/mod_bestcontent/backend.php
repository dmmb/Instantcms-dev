<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Популярные статьи', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Популярные статьи', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Популярные статьи</h3>';
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = 'Редактировать отображение модуля';
		$toolmenu[1]['link'] = '?view=modules&do=edit&id='.$_REQUEST['id'];				

		$toolmenu[2]['icon'] = 'cancel.gif';
		$toolmenu[2]['title'] = 'Отмена';
		$toolmenu[2]['link'] = '?view=modules';
		
		cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	if($opt=='save'){
	
		$cfg = array();
		$cfg['shownum'] = $_REQUEST['shownum'];
		$cfg['showlink'] = $_REQUEST['showlink'];
		$cfg['showdesc'] = $_REQUEST['showdesc'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
<form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
  <table width="501" border="0" cellpadding="10" cellspacing="0" class="proptable">    
    <tr>
      <td><strong>Количество популярных статей: </strong></td>
      <td><input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/>
        шт.</td>
    </tr>
    <tr>
      <td valign="top"><strong>Показывать ссылку на полный рейтинг: </strong></td>
      <td valign="top"><input name="showlink" type="radio" value="1" <?php if (@$cfg['showlink']) { echo 'checked="checked"'; } ?>/>
        Да
          <input name="showlink" type="radio" value="0" <?php if (@!$cfg['showlink']) { echo 'checked="checked"'; } ?>/>
        Нет</td>
    </tr>
    <tr>
      <td valign="top"><strong>Показывать анонсы статей:</strong> </td>
      <td valign="top"><input name="showdesc" type="radio" value="1" <?php if (@$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
Да
  <input name="showdesc" type="radio" value="0" <?php if (@!$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
Нет</td>
    </tr>
  </table>
  <p>
    <input name="opt" type="hidden" id="do" value="save" />
    <input name="save" type="submit" id="save" value="Сохранить" />
    <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
  </p>
</form>