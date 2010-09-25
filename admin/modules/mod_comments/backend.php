<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Последние комментарии', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Последние комментарии', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }

	echo '<h3>Последние комментарии</h3>';

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

    $comment_targets = $inCore->getCommentsTargets();

	if($opt=='save'){
	
		$cfg = array();
		$cfg['shownum']     = $_REQUEST['shownum'];
		$cfg['showrss']     = $_REQUEST['showrss'];
		$cfg['showguest']   = $_REQUEST['showguest'];
		$cfg['showtarg']   = $_REQUEST['showtarg'];

        $cfg['minrate']     = $_REQUEST['minrate'];
        if ($cfg['minrate'] < 0) { $cfg['minrate'] = 0; }

		if (sizeof($_REQUEST['targets'])){
			$cfg['targets'] = $_REQUEST['targets'];
		}

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}
	
	if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
    if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}
    if (!isset($cfg['showguest'])) { $cfg['showguest'] = 0;}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>

<form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <table width="501" border="0" cellpadding="10" cellspacing="0" class="proptable">        
        <tr>
            <td><strong>Количество комментариев: </strong></td>
            <td><input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/> шт.</td>
        </tr>
        <tr>
          <td>
              <strong>Ограничение по рейтингу:</strong><br/>
              <span class="hinttext">Показывать комментарии с рейтингом выше указанного</span>
          </td>
          <td><input type="text" size="5" name="minrate" value="<?php echo @$cfg['minrate']?>"/></td>
        </tr>
        <tr>
            <td><strong>Показывать цель и рейтинг комментария: </strong></td>
            <td>
                <input name="showtarg" type="radio" value="1" <?php if (@$cfg['showtarg']) { echo 'checked="checked"'; } ?>/> Да
                <input name="showtarg" type="radio" value="0" <?php if (@!$cfg['showtarg']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td><strong>Показывать комментарии гостей: </strong></td>
            <td>
                <input name="showguest" type="radio" value="1" <?php if (@$cfg['showguest']) { echo 'checked="checked"'; } ?>/> Да
                <input name="showguest" type="radio" value="0" <?php if (@!$cfg['showguest']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td><strong>Ссылка на RSS: </strong></td>
            <td>
                <input name="showrss" type="radio" value="1" <?php if (@$cfg['showrss']) { echo 'checked="checked"'; } ?>/> Да
                <input name="showrss" type="radio" value="0" <?php if (@!$cfg['showrss']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>Показывать комментарии для: </strong></td>
            <td>
                <?php if ($comment_targets){ ?>
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <?php foreach($comment_targets as $target){ ?>
                    <tr>
                        <td width="20">
                            <input name="targets[<?php echo $target['target']; ?>]" type="checkbox" id="<?php echo $target['target']; ?>" value="<?php echo $target['target']; ?>" <?php if (@$cfg['targets'][$target['target']]) { echo 'checked="checked"'; }?>/>
                        </td>
                        <td>
                            <label for="<?php echo $target['target']; ?>"><?php echo $target['title']; ?></label>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
                <?php } ?>
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" id="do" value="save" />
        <input name="save" type="submit" id="save" value="Сохранить" />
        <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
    </p>
</form>