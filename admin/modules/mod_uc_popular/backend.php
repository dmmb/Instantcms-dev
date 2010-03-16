<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Популярное в каталоге', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Популярное в каталоге</h3>';
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
		$cfg['num'] = $_REQUEST['num'];
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['menuid'] = $_REQUEST['menuid'];
		$cfg['showf'] = $_REQUEST['showf'];
		$cfg['showtype'] = $_REQUEST['showtype'];
		$cfg['fulllink'] = $_REQUEST['fulllink'];
		$cfg['sort'] = $_REQUEST['sort'];

        if (!isset($_REQUEST['subs'])) { $cfg['subs'] = 0; } else { $cfg['subs'] = 1; }
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = 'Настройки сохранены.';	

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233" valign="top"><strong>Количество популярных: </strong></td>
            <td width="273" valign="top"><input name="num" type="text" id="num" value="<?php if (@$cfg) { echo $cfg['num']; } ?>" size="5" />
              шт. </td>
          </tr>
          <tr>
            <td valign="top"><strong>Критерий популярности:</strong> </td>
            <td valign="top">
						<select name="sort" id="sort">
						  <option value="rating" <?php if (@$cfg['sort']=='rating') { echo 'selected="selected"'; } ?>>Рейтинг</option>
						  <option value="hits" <?php if (@$cfg['sort']=='hits') { echo 'selected="selected"'; } ?>>Просмотры</option>
                        </select>
			</td>
          </tr>
          <tr>
            <td valign="top"><strong>Показывать как:</strong> </td>
            <td valign="top"><label>
              <select name="showtype" id="showtype">
                <option value="thumb" <?php if (@$cfg['showtype']=='thumb') { echo 'selected="selected"'; } ?>>Галерея</option>
                <option value="list" <?php if (@$cfg['showtype']=='list') { echo 'selected="selected"'; } ?>>Список (таблица)</option>
                </select>
            </label></td>
          </tr>
          <tr>
            <td valign="top"><strong>Показывать полей:</strong> <br />
                <span class="hinttext">Только для режима &quot;Список (таблица)&quot; </span><br />
            <br /></td>
            <td valign="top"><input name="showf" type="text" id="showf" value="<?php if (@$cfg['showf']) { echo $cfg['showf']; } ?>" size="5" />
шт. </td>
          </tr>
          <tr>
            <td valign="top"><strong>Популярные из рубрики:</strong> </td>
            <td valign="top">
                <div>
                    <select name="cat_id" id="cat_id">
                        <option value="0" <?php if (!isset($cfg['cat_id']) || $cfg['cat_id']==0) { echo "selected"; } ?>>-- Все рубрики --</option>
                        <?php
                            if (isset($cfg['cat_id'])) {
                                echo $inCore->getListItems('cms_uc_cats', $cfg['cat_id']);
                            } else {
                                echo $inCore->getListItems('cms_uc_cats');
                            }
                        ?>
                    </select>
                </div>
                <div style="margin-top:5px">
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><input name="subs" id="subs" type="checkbox" value="1" <?php if (@$cfg['subs']) { echo 'checked'; } ?>/></td>
                        <td><label for="subs">Вместе с подрубриками</label></td>
                      </tr>
                    </table>
                </div>
            </td>
          </tr>
          <tr>
            <td valign="top"><strong>Переходить в меню: </strong><br/>
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
            <td><strong>Ссылка на каталог: </strong></td>
            <td><input name="fulllink" type="radio" value="1" <?php if (@$cfg['fulllink']) { echo 'checked="checked"'; } ?>/>
              Показать
                <input name="fulllink" type="radio" value="0" <?php if (@!$cfg['fulllink']) { echo 'checked="checked"'; } ?>/>
              Скрыть </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>