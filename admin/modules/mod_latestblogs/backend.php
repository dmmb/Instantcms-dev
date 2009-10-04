<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Новое в блогах', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Новое в блогах', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Новое в блогах</h3>';
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
		$cfg['shownum']     = $_REQUEST['shownum'];
		$cfg['showrss']     = $_REQUEST['showrss'];
		$cfg['menuid']      = $_REQUEST['menuid'];

        $cfg['minrate']     = $_REQUEST['minrate'];
        if ($cfg['minrate'] < 0) { $cfg['minrate'] = 0; }

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = 'Настройки сохранены.';

	}
	
    if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
    if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
<form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
  <table width="650" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td width=""><strong>Переходить в меню: </strong><br/>
          <span class="hinttext">Открытие объектов из модуля будет сопровождаться<br/>сменой пункта главного меню</span></td>
      <td width="205" valign="top"><select name="menuid" id="menuid">
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
      <td><strong>Количество новых записей:</strong></td>
      <td><input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/> шт.</td>
    </tr>
    <tr>
      <td>
          <strong>Ограничение по рейтингу:</strong><br/>
          <span class="hinttext">Показывать записи с рейтингом выше указанного</span>
      </td>
      <td><input type="text" size="5" name="minrate" value="<?php echo @$cfg['minrate']?>"/></td>
    </tr>
    <tr>
        <td><strong>Ссылка на RSS: </strong></td>
        <td>
            <input name="showrss" type="radio" value="1" <?php if (@$cfg['showrss']) { echo 'checked="checked"'; } ?>/> Да
            <input name="showrss" type="radio" value="0" <?php if (@!$cfg['showrss']) { echo 'checked="checked"'; } ?>/> Нет
        </td>
    </tr>
  </table>
  <p>
    <input name="opt" type="hidden" id="do" value="save" />
    <input name="save" type="submit" id="save" value="Сохранить" />
    <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
  </p>
</form>