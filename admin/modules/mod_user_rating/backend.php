<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Рейтинг пользователей', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Рейтинг пользователей</h3>';
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
	
	if (!isset($cfg['count'])) { $cfg['count'] = 20; }
	if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0; }
	if (!isset($cfg['view_type'])) { $cfg['view_type'] = 'rating'; }	

	if($opt=='save'){
	
		$cfg = array();
		$cfg['count'] = (int)$_REQUEST['count'];
		$cfg['view_type'] = $_REQUEST['view_type'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
	
		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><strong>Количество пользователей: </strong></td>
            <td width="250"><input name="count" type="text" id="count" value="<?php if (@$cfg) { echo $cfg['count']; } ?>" style="width:250px" /></td>
          </tr>
          <tr>
            <td><strong>Показывать как:</strong> </td>
            <td><select name="view_type" id="view_type" style="width:250px">
              <option value="karma" <?php if (@$cfg['view_type']=='karma') {echo 'selected';} ?>>Карма</option>
              <option value="rating" <?php if (@$cfg['view_type']=='rating') {echo 'selected';} ?>>Рейтинг</option>
            </select></td>
          </tr>          
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>