<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('��������� �����������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>��������� �����������</h3>';
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
		$cfg['newscount'] = $_REQUEST['newscount'];
		$cfg['view_type'] = $_REQUEST['view_type'];
		$cfg['maxcool']   = $_REQUEST['maxcool'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = '��������� ���������.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233"><strong>���������� ��� ������: </strong></td>
            <td width="273"><input name="newscount" type="text" id="newscount" value="<?php if (@$cfg) { echo $cfg['newscount']; } ?>" size="5" /></td>
          </tr>
          <tr>
            <td><strong>���������� ���:</strong> </td>
            <td><select name="view_type" id="view_type">
              <option value="table" <?php if (@$cfg['view_type']=='table') {echo 'selected';} ?>>�������</option>
              <option value="hr_table" <?php if (@$cfg['view_type']=='hr_table') {echo 'selected';} ?>>�������������� �������</option>
              <option value="list" <?php if (@$cfg['view_type']=='list') {echo 'selected';} ?>>������</option>
            </select></td>
          </tr>
          <tr>
            <td width="233"><strong>���������� �������: </strong><br> ������ ��� ����� "�������������� �������"</td>
            <td width="273"><input name="maxcool" type="text" id="maxcool" value="<?php if (@$cfg) { echo $cfg['maxcool']; } ?>" size="5" /></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>