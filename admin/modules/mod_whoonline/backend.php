<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Кто онлайн?', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Кто онлайн?</h3>';
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
		$cfg['show_today'] = $_REQUEST['show_today'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

<form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
    <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
      <tr>
        <td width="233">
            <strong>Показывать кто был сегодня: </strong><br/>
            <span class="hinttext">Выводит дополнительно список всех посетителей за сегодня</span>
        </td>
        <td width="273" valign="top">
            <input name="show_today" type="radio" value="1" <?php if (@$cfg['show_today']) { echo 'checked="checked"'; } ?>/> Да
            <input name="show_today" type="radio" value="0" <?php if (@!$cfg['show_today']) { echo 'checked="checked"'; } ?>/> Нет
        </td>
      </tr>
    </table>
    <p>
      <input name="opt" type="hidden" id="do" value="save" />
      <input name="save" type="submit" id="save" value="Сохранить" />
      <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
    </p>
</form>