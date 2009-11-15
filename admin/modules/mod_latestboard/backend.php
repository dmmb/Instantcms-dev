<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Новые объявления', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Новые объявления</h3>';
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
		$cfg['showcity'] = $_REQUEST['showcity'];
		$cfg['showrss'] = $_REQUEST['showrss'];
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['menuid'] = $_REQUEST['menuid'];

		if (!isset($_REQUEST['subs'])) { $cfg['subs'] = 0; } else { $cfg['subs'] = 1; }
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
        
		$msg = 'Настройки сохранены.';

	}
	if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
	if (!isset($cfg['subs'])) { $cfg['subs'] = 0;}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><strong>Количество объявлений: </strong></td>
            <td><input name="shownum" type="text" id="shownum" value="<?php if (@$cfg) { echo $cfg['shownum']; } ?>" size="5" />
              шт. </td>
          </tr>
          <tr>
            <td width="288"><strong>Показывать город: </strong></td>
            <td width="218"><input name="showcity" type="radio" value="1" <?php if (@$cfg['showcity']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showcity" type="radio" value="0" <?php if (@!$cfg['showcity']) { echo 'checked="checked"'; } ?>/>
            Нет </td>
          </tr>
          <tr>
            <td><strong>Ссылка на RSS: </strong></td>
            <td><input name="showrss" type="radio" value="1" <?php if (@$cfg['showrss']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showrss" type="radio" value="0" <?php if (@!$cfg['showrss']) { echo 'checked="checked"'; } ?>/>
              Нет </td>
          </tr>
          <tr>
            <td valign="top"><strong>Объявления из рубрики:</strong> </td>
            <td valign="top"><div><select name="cat_id" id="cat_id" style="width:230px">
              <option value="-1">-- Все рубрики --</option>
              <?php
                  if (isset($cfg['cat_id'])) {
                        echo $inCore->getListItemsNS('cms_board_cats', $cfg['cat_id']);
                    } else {
                       echo $inCore->getListItemsNS('cms_board_cats');
                    }
              ?>
            </select></div>
              <div style="margin-top:5px">
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><input name="subs" type="checkbox" value="1" <?php if (@$cfg['subs']) { echo 'checked'; } ?>/></td>
                    <td>Вместе с подрубриками</td>
                  </tr>
                </table>
            </div></td>
          </tr>
          <tr>
            <td><strong>Переходить в меню: </strong><br/>
                <span class="hinttext">Открытие объектов из модуля будет сопровождаться сменой пункта главного меню</span></td>
            <td valign="top">
                <select name="menuid" id="menuid" style="width:230px">
                <option value="0">-- не переходить --</option>
                <?php
                        if (isset($cfg['menuid'])){
                            echo $inCore->getListItemsNS('cms_menu', $cfg['menuid']);
                        } else {
                            echo $inCore->getListItemsNS('cms_menu');
                        }
                    ?>
            </select></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>