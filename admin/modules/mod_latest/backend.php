<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Последние материалы', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Последние материалы</h3>';
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
		$cfg['newscount'] = $_REQUEST['newscount'];
		$cfg['showdesc'] = $_REQUEST['showdesc'];
		$cfg['showdate'] = $_REQUEST['showdate'];
		$cfg['showcom'] = $_REQUEST['showcom'];	
		$cfg['showrss'] = $_REQUEST['showrss'];
		$cfg['cat_id'] = $_REQUEST['cat_id'];

		if (!isset($_REQUEST['subs'])) { $cfg['subs'] = 0; } else { $cfg['subs'] = 1; }
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
	if (!isset($cfg['subs'])) { $cfg['subs'] = 0;}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><strong>Количество материалов : </strong></td>
            <td><input name="newscount" type="text" id="newscount" value="<?php if (@$cfg) { echo $cfg['newscount']; } ?>" size="5" />
              шт. </td>
          </tr>
          <tr>
            <td width="288"><strong>Показывать анонсы: </strong></td>
            <td width="218"><input name="showdesc" type="radio" value="1" <?php if (@$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showdesc" type="radio" value="0" <?php if (@!$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
            Нет </td>
          </tr>
          <tr>
            <td><strong>Показывать даты и авторов: </strong></td>
            <td><input name="showdate" type="radio" value="1" <?php if (@$cfg['showdate']) { echo 'checked="checked"'; } ?>/>
Да
  <input name="showdate" type="radio" value="0" <?php if (@!$cfg['showdate']) { echo 'checked="checked"'; } ?>/>
Нет </td>
          </tr>
          <tr>
            <td><strong>Показывать число комментариев: </strong></td>
            <td><input name="showcom" type="radio" value="1" <?php if (@$cfg['showcom']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showcom" type="radio" value="0" <?php if (@!$cfg['showcom']) { echo 'checked="checked"'; } ?>/>
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
            <td valign="top"><strong>Материалы из раздела:</strong> </td>
            <td valign="top"><div><select name="cat_id" id="cat_id">
              <option value="-1">-- Все разделы --</option>
              <?php
                  if (isset($cfg['cat_id'])) {
                        echo $inCore->getListItemsNS('cms_category', $cfg['cat_id']);
                    } else {
                       echo $inCore->getListItemsNS('cms_category');
                    }
              ?>
            </select></div>
              <div style="margin-top:5px">
                <table border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><input name="subs" type="checkbox" value="1" <?php if (@$cfg['subs']) { echo 'checked'; } ?>/></td>
                    <td>Вместе с подразделами </td>
                  </tr>
                </table>
            </div></td>
          </tr>          
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>