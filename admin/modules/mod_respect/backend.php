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

	cpAddPathway('����� ������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>����� ������</h3>';
    
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = '���������';
		$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = '������������� ����������� ������';
		$toolmenu[1]['link'] = '?view=modules&do=edit&id='.$_REQUEST['id'];				

		$toolmenu[2]['icon'] = 'cancel.gif';
		$toolmenu[2]['title'] = '������';
		$toolmenu[2]['link'] = '?view=modules';
		
		cpToolMenu($toolmenu);

    //LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	if($opt=='save'){
	
		$cfg = array();
		$cfg['view']        = $inCore->request('view_aw', 'str', 'all');
		$cfg['show_awards'] = $inCore->request('show_awards', 'int', 1);
		$cfg['order']       = $inCore->request('order', 'str', 'desc');
		$cfg['limit']       = $inCore->request('limit', 'int', 5);

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = '��������� ���������.';

	}
    
    if (!isset($cfg['view'])) { $cfg['view'] = 'all'; }
    if (!isset($cfg['limit'])) { $cfg['limit'] = 5; }
    if (!isset($cfg['order'])) { $cfg['order'] = 'desc'; }
    if (!isset($cfg['show_awards'])) { $cfg['show_awards'] = 1; }
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

<form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
    <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>���������� �������������: </strong></td>
            <td><input name="limit" type="text" id="limit" value="<?php if (@$cfg) { echo $cfg['limit']; } ?>" size="5" /></td>
        </tr>
        <tr>
            <td width="288"><strong>���������� �������:</strong></td>
            <td width="218">
                <input name="show_awards" type="radio" value="1" <?php if (@$cfg['show_awards']) { echo 'checked="checked"'; } ?>/> ��
                <input name="show_awards" type="radio" value="0" <?php if (@!$cfg['show_awards']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>���������� ������������� � ��������: </strong></td>
            <td>
                <select name="view_aw" id="order" style="width:220px">
                    <option value="all" <?php if (@$cfg['view']=='all'){ echo 'selected="selected"'; } ?>/>-- ����� ������� --
                    <?php echo cmsUser::getFullAwardsList($cfg['view']); ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>�����������: </strong></td>
            <td>
                <select name="order" id="order" style="width:220px">
                    <option value="desc" <?php if (@$cfg['order']=='desc' || !isset($cfg['order'])) { echo 'selected="selected"'; } ?>>�� ���������� ������</option>
                    <option value="rand" <?php if (@$cfg['order']=='rand') { echo 'selected="selected"'; } ?>>��������� �������</option>
                </select>
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" id="do" value="save" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
    </p>
</form>