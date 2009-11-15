<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Авторизация', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Авторизация</h3>';
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
		$cfg['autolog'] = $_REQUEST['autolog'];
		$cfg['passrem'] = $_REQUEST['passrem'];
		$cfg['showtype'] = $_REQUEST['showtype'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
        
		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233" valign="top"><strong>Запоминание (автологин):</strong> </td>
            <td width="273" valign="top"><input name="autolog" type="radio" value="1" <?php if (@$cfg['autolog']) { echo 'checked="checked"'; } ?>/>
              Вкл
                <input name="autolog" type="radio" value="0" <?php if (@!$cfg['autolog']) { echo 'checked="checked"'; } ?>/>
Выкл </td>
          </tr>
          <tr>
            <td valign="top"><strong>Напоминание паролей: </strong></td>
            <td valign="top"><input name="passrem" type="radio" value="1" <?php if (@$cfg['passrem']) { echo 'checked="checked"'; } ?>/>
              Вкл
                <input name="passrem" type="radio" value="0" <?php if (@!$cfg['passrem']) { echo 'checked="checked"'; } ?>/>
            Выкл</td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>