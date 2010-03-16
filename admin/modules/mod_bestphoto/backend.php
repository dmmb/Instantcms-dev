<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Популярные фотографии', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Популярные фотографии</h3>';
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
		$cfg['maxcols'] = $_REQUEST['maxcols'];
		$cfg['showalbum'] = $_REQUEST['showalbum'];
		$cfg['showdate'] = $_REQUEST['showdate'];
		$cfg['showcom'] = $_REQUEST['showcom'];	
		$cfg['album_id'] = $_REQUEST['album_id'];
		$cfg['sort'] = $_REQUEST['sort'];		
		$cfg['menuid'] = $_REQUEST['menuid'];
		$cfg['showtype'] = $_REQUEST['showtype'];
		$cfg['showmore'] = $_REQUEST['showmore'];
	
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><strong>Количество фотографий: </strong></td>
            <td><input name="shownum" type="text" id="shownum" value="<?php if (@$cfg) { echo $cfg['shownum']; } ?>" size="5" />
              шт. </td>
          </tr>
          <tr>
            <td><strong>Число колонок для вывода:</strong> </td>
            <td><input name="maxcols" type="text" id="maxcols" value="<?php if (@$cfg) { echo $cfg['maxcols']; } ?>" size="5" />
шт. </td>
          </tr>
          <tr>
            <td width="288"><strong>Показывать название альбома: </strong></td>
            <td width="218"><input name="showalbum" type="radio" value="1" <?php if (@$cfg['showalbum']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showalbum" type="radio" value="0" <?php if (@!$cfg['showalbum']) { echo 'checked="checked"'; } ?>/>
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
            <td><strong>Фотографии из альбома:</strong> </td>
            <td>
                <select name="album_id" id="album_id">
                    <option value="0">-- Все альбомы --</option>
                    <?php
                        if (isset($cfg['album_id'])) {
                            echo $inCore->getListItemsNS('cms_photo_albums', $cfg['album_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_photo_albums');
                        }
                    ?>
                </select>
            </td>
          </tr>
          <tr>
            <td><strong>Режим вывода: </strong></td>
            <td><select name="showtype" id="showtype">
              <option value="full" <?php if (@$cfg['showtype']=='full' || !isset($cfg['showtype'])) { echo 'selected="selected"'; } ?>>Подробный</option>
              <option value="short" <?php if (@$cfg['showtype']=='short') { echo 'selected="selected"'; } ?>>Только фото</option>
            </select></td>
          </tr>
          <tr>
            <td><strong>Критерий популярности:</strong> </td>
            <td><select name="sort" id="sort">
              <option value="rating" <?php if (@$cfg['sort']=='rating') { echo 'selected="selected"'; } ?>>Рейтинг</option>
              <option value="hits" <?php if (@$cfg['sort']=='hits') { echo 'selected="selected"'; } ?>>Просмотры</option>
            </select></td>
          </tr>
          <tr>
            <td><strong>Показывать рейтинг (просмотры): </strong></td>
            <td><input name="showdate" type="radio" value="1" <?php if (@$cfg['showdate']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showdate" type="radio" value="0" <?php if (@!$cfg['showdate']) { echo 'checked="checked"'; } ?>/>
              Нет </td>
          </tr>
          <tr>
            <td><strong>Переходить в меню: </strong><br/>
                <span class="hinttext">Открытие объектов из модуля будет сопровождаться сменой пункта главного меню</span></td>
            <td valign="top"><select name="menuid" id="menuid">
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
          <tr>
            <td><strong>Показывать ссылку на галерею: </strong></td>
            <td><input name="showmore" type="radio" value="1" <?php if (@$cfg['showmore'] || !isset($cfg['showmore'])) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="showmore" type="radio" value="0" <?php if (@!$cfg['showmore']) { echo 'checked="checked"'; } ?>/>
              Нет </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>