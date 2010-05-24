<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Корзина покупателя', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Корзина покупателя</h3>';
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
		$cfg['showtype'] = $_REQUEST['showtype'];
		$cfg['showqty'] = $_REQUEST['showqty'];
		$cfg['source'] = $_REQUEST['source'];
		
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$msg = 'Настройки сохранены.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table width="524" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td valign="top" bgcolor="#EBEBEB"><strong>Источник товаров (компонент):</strong> </td>
            <td valign="top" bgcolor="#EBEBEB"><select name="source">
              <option value="price" <?php if (@$cfg['source']=='price' || !isset($cfg['source'])) { echo 'selected="selected"'; } ?>>Прайс-лист</option>
              <option value="catalog" <?php if (@$cfg['source']=='catalog') { echo 'selected="selected"'; } ?>>Универсальный каталог</option>
            </select></td>
          </tr>
          <tr>
            <td width="245" valign="top"><strong>Показывать: </strong></td>
            <td width="239" valign="top"><label>
              <select name="showtype">
                <option value="list" <?php if (@$cfg['showtype']=='thumb' || !isset($cfg['showtype'])) { echo 'selected="selected"'; } ?>>Список товаров в корзине</option>
                <option value="qty" <?php if (@$cfg['showtype']=='qty') { echo 'selected="selected"'; } ?>>Количество товаров в корзине</option>
                <option value="qtyprice" <?php if (@$cfg['showtype']=='qtyprice') { echo 'selected="selected"'; } ?>>Количество и общую сумму</option>
              </select>
            </label></td>
          </tr>
          <tr>
            <td valign="top"><strong>Показывать количество единиц товара в списке:</strong></td>
            <td valign="top"><input name="showqty" type="radio" value="1" <?php if (@$cfg['showqty']) { echo 'checked="checked"'; } ?>/>
Да
  <input name="showqty" type="radio" value="0" <?php if (@!$cfg['showqty']) { echo 'checked="checked"'; } ?>/>
Нет </td>
          </tr>          
        </table>
        <p>
          <input name="opt" type="hidden" id="opt" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>