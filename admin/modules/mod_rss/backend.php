<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('����� RSS', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>����� RSS</h3>';
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
		$cfg['showdesc'] = $_REQUEST['showdesc'];
		$cfg['showicon'] = $_REQUEST['showicon'];
		$cfg['itemslimit'] = $_REQUEST['itemslimit'];
		$cfg['rssurl'] = $_REQUEST['rssurl'];
		$cfg['cols'] = $_REQUEST['cols'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = '��������� ���������.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td valign="top"><strong>����� RSS-������: </strong></td>
            <td valign="top"><label></label>
                <input name="rssurl" type="text" id="rssurl" value="<?php if (@$cfg['rssurl']) { echo $cfg['rssurl']; } ?>" size="40" /></td>
          </tr>
          <tr>
            <td width="233" valign="top"><strong>���������� ������:</strong> </td>
            <td width="273" valign="top"><input name="showdesc" type="radio" value="1" <?php if (@$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
              ���
                <input name="showdesc" type="radio" value="0" <?php if (@!$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
���� </td>
          </tr>
          <tr>
            <td valign="top"><strong>���������� ��������: </strong></td>
            <td valign="top"><input name="itemslimit" type="text" id="itemslimit" value="<?php if (@$cfg['itemslimit']) { echo $cfg['itemslimit']; } ?>" size="5" />
��. �  
  <select name="cols" id="cat_id">
    <option value="1" <?php if (@$cfg['cols'] == 1) { echo 'selected'; }?>>���� �������</option>
    <option value="2" <?php if (@$cfg['cols'] == 2) { echo 'selected'; }?>>��� �������</option>
    <option value="3" <?php if (@$cfg['cols'] == 3) { echo 'selected'; }?>>��� �������</option>
      </select></td>
          </tr>
          <tr>
            <td valign="top"><strong>���������� ������ RSS:</strong></td>
            <td valign="top"><input name="showicon" type="radio" value="1" <?php if (@$cfg['showicon']) { echo 'checked="checked"'; } ?>/>
���
  <input name="showicon" type="radio" value="0" <?php if (@!$cfg['showicon']) { echo 'checked="checked"'; } ?>/>
���� </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>