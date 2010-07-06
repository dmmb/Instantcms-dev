<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Календарь', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
    
	//LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	echo '<h3>Календарь</h3>';

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

		$cfg['cat_id'] = $inCore->request('cat_id', 'int', 0);
		$cfg['source'] = $inCore->request('source', 'str', 'arhive');
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$msg = 'Настройки сохранены.';
		
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform">
		<input type="hidden" name="opt" value="save"/>
        <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="239"><strong>Источник статей:</strong> </td>
            <td width="204"><select name="source" id="source">
              <option value="content" <?php if(@$cfg['source']=='content') { echo 'selected'; } ?>>Каталог статей</option>
              <option value="arhive" <?php if(@$cfg['source']=='arhive') { echo 'selected'; } ?>>Архив статей</option>
              <option value="both" <?php if(@$cfg['source']=='both') { echo 'selected'; } ?>>Каталог и архив</option>
            </select></td>
          </tr>
          <tr>
            <td><strong>Показывать статьи из раздела:</strong></td>
            <td>
                <select name="cat_id" id="cat_id">
                    <option value="-1" <?php if ($cfg['cat_id']==-1){ ?>selected="selected"<?php } ?>>-- Все разделы --</option>
                    <?php
                        if (isset($cfg['cat_id'])) {
                            echo $inCore->getListItemsNS('cms_category', $cfg['cat_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_category');
                        }
                    ?>
                </select>
            </td>
          </tr>          
        </table>
        <p>
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>
