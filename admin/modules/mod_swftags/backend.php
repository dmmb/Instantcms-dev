<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	cpAddPathway('SWF Облако тегов', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>SWF Облако тегов</h3>';
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
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['sortby'] = $_REQUEST['sortby'];
		$cfg['menuid'] = $_REQUEST['menuid'];
		$cfg['minfreq'] = $_REQUEST['minfreq'];
		$cfg['minlen'] = $_REQUEST['minlen'];
		$cfg['maxtags'] = $_REQUEST['maxtags'];
		$cfg['swf_bg_color'] = $_REQUEST['swf_bg_color'];
		$cfg['swf_height'] = $_REQUEST['swf_height'];
		$cfg['swf_width'] = $_REQUEST['swf_width'];

		if (sizeof($_REQUEST['targets'])){
			$cfg['targets']= $_REQUEST['targets'];
		}

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if(!isset($cfg['minfreq'])) { $cfg['minfreq'] = 0; }
    if(!isset($cfg['maxtags'])) { $cfg['maxtags'] = 20; }
	if(!isset($cfg['swf_bg_color'])) { $cfg['swf_bg_color'] = "FFFFFF"; }
    if(!isset($cfg['swf_width'])) { $cfg['swf_width'] = 200; }
    if(!isset($cfg['swf_height'])) { $cfg['swf_height'] = 200; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="544" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
          	<td colspan="2">
          	Не забудьте опубликовать так же и плагин
          	</td>
          </tr>
          <tr>
            <td width="227"><strong>Сортировать теги : </strong></td>
            <td width="277"><select name="sortby" id="sortby">
              <option value="tag" <?php if(@$cfg['sortby']=='tag') { echo 'selected'; } ?>>По алфавиту</option>
              <option value="num" <?php if(@$cfg['sortby']=='num') { echo 'selected'; } ?>>По популярности</option>
            </select>            </td>
          </tr>
          <tr>
            <td><strong>Минимальная частота тега: </strong></td>
            <td>
				<select name="minfreq" id="minfreq">
				  <option value="0" <?php if(@$cfg['minfreq']=='0') { echo 'selected'; } ?>>Без ограничений</option>
				  <option value="10" <?php if(@$cfg['minfreq']=='10') { echo 'selected'; } ?>>10</option>
				  <option value="20" <?php if(@$cfg['minfreq']=='20') { echo 'selected'; } ?>>20</option>
				  <option value="50" <?php if(@$cfg['minfreq']=='50') { echo 'selected'; } ?>>50</option>
				  <option value="100" <?php if(@$cfg['minfreq']=='100') { echo 'selected'; } ?>>100</option>
				  <option value="200" <?php if(@$cfg['minfreq']=='200') { echo 'selected'; } ?>>200</option>
				  <option value="500" <?php if(@$cfg['minfreq']=='500') { echo 'selected'; } ?>>500</option>
				</select>			</td>
          </tr>
          <tr>
            <td><strong>Минимальная длина тега: </strong></td>
			<?php if(!isset($cfg['minlen'])) { $cfg['minlen'] = 3; } ?>
            <td><input name="minlen" type="text" id="minlen" size="5" value="<?php echo @$cfg['minlen'];?>"/>
              симв.
</td>
          </tr>
          <tr>
            <td><strong>Ширина ФЛЭШ модуля: </strong></td>
			<?php if(!isset($cfg['swf_width'])) { $cfg['swf_width'] = 200; } ?>
            <td><input name="swf_width" type="text" id="swf_width" size="5" value="<?php echo @$cfg['swf_width'];?>"/>
              пикс.
</td>
          </tr>
          <tr>
            <td><strong>Высота ФЛЭШ модуля: </strong></td>
			<?php if(!isset($cfg['swf_height'])) { $cfg['swf_height'] = 200; } ?>
            <td><input name="swf_height" type="text" id="swf_height" size="5" value="<?php echo @$cfg['swf_height'];?>"/>
              пикс.
</td>
          </tr>
          <tr>
            <td><strong>Цвет фона ФЛЭШ модуля: </strong></td>
			<?php if(!isset($cfg['swf_bg_color'])) { $cfg['swf_bg_color'] = "FFFFFF"; } ?>
            <td><input name="swf_bg_color" type="text" id="swf_bg_color" size="6" value="<?php echo @$cfg['swf_bg_color'];?>"/>
              в HEX.
</td>
          </tr>
          <tr>
            <td><strong>Максимальное количество тегов: </strong></td>
            <td><input name="maxtags" type="text" id="maxtags" size="5" value="<?php echo @$cfg['maxtags'];?>"/></td>
          </tr>
          <tr>
            <td valign="top"><strong>Показывать теги для: </strong></td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td><label>
                    <input name="targets[content]" type="checkbox" id="content" value="content" <?php if (@$cfg['targets']['content']) { echo 'checked="checked"'; }?>/>
                    Статей</label></td>
                </tr>
                <tr>
                  <td><input name="targets[photo]" type="checkbox" id="t_photo" value="photo" <?php if (@$cfg['targets']['photo']) { echo 'checked="checked"'; }?>/>
                    Фото в галерее </td>
                </tr>
                <tr>
                  <td><input name="targets[blogpost]" type="checkbox" id="t_blog" value="blog" <?php if (@$cfg['targets']['blogpost']) { echo 'checked="checked"'; }?>/>
                    Записей в блогах </td>
                </tr>
                <tr>
                  <td><input name="targets[catalog]" type="checkbox" id="t_catalog" value="catalog" <?php if (@$cfg['targets']['catalog']) { echo 'checked="checked"'; }?>/>
                    Записей каталога</td>
                </tr>
                <tr>
                  <td><input name="targets[userphoto]" type="checkbox" id="t_userphoto" value="userphoto" <?php if (@$cfg['targets']['userphoto']) { echo 'checked="checked"'; }?>/>
                    Фото пользователей </td>
                </tr>
            </table></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>