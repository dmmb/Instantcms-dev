<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Календарь', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);

    //LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	echo '<h3>Поиск пользователей</h3>';

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

	
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
		
	if($opt == 'save'){	
		$cfg = array();
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['source'] = $_REQUEST['source'];
		$cfg['menuid'] = $_REQUEST['menuid'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$msg = 'Настройки сохранены.';
		
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform">
		<input type="hidden" name="opt" value="save"/>
        <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="239"><strong>Переходить в меню: </strong><br/>
                <span class="hinttext">Открытие объектов из модуля будет сопровождаться сменой пункта главного меню</span></td>
            <td width="204" valign="top"><select name="menuid" id="menuid">
                <option value="0" <?php if (!isset($cfg['menuid']) || @$cfg['menuid']==0) { echo 'selected'; } ?>>-- не переходить --</option>
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
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>
