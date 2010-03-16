<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Меню', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Меню</h3>';
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
		$cfg['menu'] = $_REQUEST['menu'];
		$cfg['jtree'] = $_REQUEST['jtree'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	if (!isset($cfg['menu'])) { $cfg['menu'] = 'mainmenu'; }
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233" valign="top"><strong>Меню для отображения:</strong></td>
            <td width="273" valign="top">
			<select name="menu" id="menu">
              <option value="mainmenu" <?php if (@$cfg['menu']=='mainmenu' || !isset($cfg['menu'])) { echo 'selected'; }?>>Главное меню</option>
              <option value="menu1" <?php if (@$cfg['menu']=='menu1') { echo 'selected'; }?>>Дополнительно меню 1</option>
              <option value="menu2" <?php if (@$cfg['menu']=='menu2') { echo 'selected'; }?>>Дополнительно меню 2</option>
              <option value="menu3" <?php if (@$cfg['menu']=='menu3') { echo 'selected'; }?>>Дополнительно меню 3</option>
              <option value="menu4" <?php if (@$cfg['menu']=='menu4') { echo 'selected'; }?>>Дополнительно меню 4</option>
              <option value="menu5" <?php if (@$cfg['menu']=='menu5') { echo 'selected'; }?>>Дополнительно меню 5</option>
            </select></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>