<?php
/*********************************************************/
// InstantCMS v1.5   (c) 2009 FREEWARE                   //
// http://www.instantcms.ru/, info@instantcms.ru         //
// LICENSED BY GNU/GPL v2                                //
//                                                       //
// Written by: Vladimir E. Obukhov, 2007-2009            //
//             Maxim M. Kostjukevich aka MaxiSoft, 2009  //
/*********************************************************/

	cpAddPathway('Файлы пользователей', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>Файлы пользователей</h3>';
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
		$cfg['menuid']      = $_REQUEST['menuid'];
		$cfg['sw_stats']    = $_REQUEST['sw_stats'];
		$cfg['sw_latest']   = $_REQUEST['sw_latest'];
		$cfg['sw_popular']  = $_REQUEST['sw_popular'];
        $cfg['sw_list']     = $_REQUEST['sw_list'];
		$cfg['num_latest']  = $_REQUEST['num_latest'];
		$cfg['num_popular'] = $_REQUEST['num_popular'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = 'Настройки сохранены.';

	}

    if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0; }
    if (!isset($cfg['sw_stats'])) { $cfg['sw_stats'] = 1; }
    if (!isset($cfg['sw_latest'])) { $cfg['sw_latest'] = 1; }
    if (!isset($cfg['sw_popular'])) { $cfg['sw_popular'] = 1; }
    if (!isset($cfg['num_latest'])) { $cfg['num_latest'] = 5; }
    if (!isset($cfg['num_popular'])) { $cfg['num_popular'] = 5; }
    if (!isset($cfg['sw_list'])) { $cfg['sw_list'] = 1; }
    

	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

<form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
    <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td width="288"><strong>Показывать новые файлы: </strong></td>
            <td width="218">
                <input name="sw_latest" type="radio" value="1" <?php if (@$cfg['sw_latest']) { echo 'checked="checked"'; } ?>/> Да
                <input name="sw_latest" type="radio" value="0" <?php if (@!$cfg['sw_latest']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td><strong>Количество новых файлов: </strong></td>
            <td>
                <input name="num_latest" type="text" id="num_latest" value="<?php if (@$cfg) { echo $cfg['num_latest']; } ?>" size="5" /> шт.
            </td>
        </tr>
        <tr>
            <td width="288"><strong>Показывать популярные файлы: </strong></td>
            <td width="218">
                <input name="sw_popular" type="radio" value="1" <?php if (@$cfg['sw_popular']) { echo 'checked="checked"'; } ?>/> Да
                <input name="sw_popular" type="radio" value="0" <?php if (@!$cfg['sw_popular']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td><strong>Количество популярных файлов: </strong></td>
            <td>
                <input name="num_popular" type="text" id="num_popular" value="<?php if (@$cfg) { echo $cfg['num_popular']; } ?>" size="5" /> шт.
            </td>
        </tr>
        <tr>
            <td width="288"><strong>Показывать счетчик файлов: </strong></td>
            <td width="218">
                <input name="sw_stats" type="radio" value="1" <?php if (@$cfg['sw_stats']) { echo 'checked="checked"'; } ?>/> Да
                <input name="sw_stats" type="radio" value="0" <?php if (@!$cfg['sw_stats']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td width="288"><strong>Показать список всех файлов: </strong></td>
            <td width="218">
                <input name="sw_list" type="radio" value="1" <?php if (@$cfg['sw_list']) { echo 'checked="checked"'; } ?>/> Да
                <input name="sw_list" type="radio" value="0" <?php if (@!$cfg['sw_list']) { echo 'checked="checked"'; } ?>/> Нет
            </td>
        </tr>
        <tr>
            <td>
                <strong>Переходить в меню: </strong><br/>
                <span class="hinttext">Открытие объектов из модуля будет сопровождаться сменой пункта главного меню</span>
            </td>
            <td valign="top">
                <select name="menuid" id="menuid">
                    <option value="0">-- не переходить --</option>
                    <?php
                        if (isset($cfg['menuid'])){
                            echo $inCore->getListItemsNS('cms_menu', $cfg['menuid']);
                        } else {
                            echo $inCore->getListItemsNS('cms_menu');
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" id="do" value="save" />
        <input name="save" type="submit" id="save" value="Сохранить" />
        <input name="back" type="button" id="back" value="Назад" onclick="window.location.href='index.php?view=modules';"/>
    </p>
</form>