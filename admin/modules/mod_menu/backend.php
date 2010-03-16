<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('����', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>����</h3>';
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
		$cfg['menu'] = $_REQUEST['menu'];
		$cfg['jtree'] = $_REQUEST['jtree'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = '��������� ���������.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="546" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="233" valign="top"><strong>���� ��� ����������� :</strong></td>
            <td width="273" valign="top">
			<select name="menu" id="menu">
              <option value="mainmenu" <?php if (@$cfg['menu']=='mainmenu' || !isset($cfg['menu'])) { echo 'selected'; }?>>������� ����</option>
                <?php for($m=1;$m<=15;$m++){ ?>
                    <option value="menu<?php echo $m; ?>" <?php if (@$cfg['menu']=='menu'.$m) { echo 'selected'; }?>>�������������� ���� <?php echo $m; ?></option>
                <?php } ?>
            </select>
            </td>
          </tr>
          <tr>
            <td valign="top"><strong>������������ JavaScript: </strong></td>
            <td valign="top"><input name="jtree" type="radio" value="1" <?php if (@$cfg['jtree']) { echo 'checked="checked"'; } ?>/>
���
  <input name="jtree" type="radio" value="0" <?php if (@!$cfg['jtree']) { echo 'checked="checked"'; } ?>/>
���� </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>