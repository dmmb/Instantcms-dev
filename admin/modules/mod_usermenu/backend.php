<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('���� ������������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>�����������</h3>';
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
		$cfg['avatar'] = $_REQUEST['avatar'];
		$cfg['showtype'] = $_REQUEST['showtype'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
	
		$msg = '��������� ���������.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
	
	if (!isset($cfg['avatar'])){ $cfg['avatar'] = true;	}
	if (!isset($cfg['showtype'])){ $cfg['showtype'] = 'text';	}
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233" valign="top"><strong>��� ����: </strong></td>
            <td width="273" valign="top"><label>
              <select name="showtype" id="showtype">
                <option value="text" <?php if (@$cfg['showtype']=='text') { echo 'selected'; } ?>>������ � �������</option>
                <option value="icons" <?php if (@$cfg['showtype']=='icons') { echo 'selected'; } ?>>������ ������</option>
              </select>
            </label></td>
          </tr>
          <tr>
            <td valign="top"><strong>���������� ������: </strong></td>
            <td valign="top"><input name="avatar" type="radio" value="1" <?php if (@$cfg['avatar']) { echo 'checked="checked"'; } ?>/>
              ��
                <input name="avatar" type="radio" value="0" <?php if (@!$cfg['avatar']) { echo 'checked="checked"'; } ?>/>
              ���</td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>