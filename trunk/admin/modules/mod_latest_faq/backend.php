<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Последние вопросы FAQ', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Последние вопросы FAQ</h3>';
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
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['menuid'] = $_REQUEST['menuid'];
		$cfg['maxlen'] = $_REQUEST['maxlen'];

		if (!isset($_REQUEST['subs'])) { $cfg['subs'] = 0; } else { $cfg['subs'] = 1; }
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	if (!isset($cfg['newscount'])) { $cfg['newscount'] = 2;}
	if (!isset($cfg['cat_id'])) { $cfg['cat_id'] = -1;}
	if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0;}
	if (!isset($cfg['maxlen'])) { $cfg['maxlen'] = 120;}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="288"><strong>Количество вопросов : </strong></td>
            <td width="218"><input name="newscount" type="text" id="newscount" value="<?php if (@$cfg) { echo $cfg['newscount']; } ?>" size="5" />
              шт. </td>
          </tr>
          <tr>
            <td><strong>Максимальная длина вопроса: </strong></td>
            <td><input name="maxlen" type="text" id="maxlen" value="<?php if (@$cfg) { echo $cfg['maxlen']; } ?>" size="8" />
            симв. </td>
          </tr>
          <tr>
            <td valign="top"><strong>Вопросы из категории:</strong> </td>
            <td valign="top">
                <div>
                    <select name="cat_id" id="cat_id">
                        <option value="-1">-- Все категории --</option>
                        <?php
                            if (isset($cfg['cat_id'])) {
                                echo $inCore->getListItems('cms_faq_cats', $cfg['cat_id']);
                            } else {
                                echo $inCore->getListItems('cms_faq_cats');
                            }
                        ?>
                    </select>
                </div>
                <div style="margin-top:5px"></div>
            </td>
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
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>