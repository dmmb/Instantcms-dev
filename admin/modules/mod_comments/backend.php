<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('��������� �����������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('��������� �����������', '?view=modules&do=config&id='.$_REQUEST['id']);
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
		$cfg['shownum']     = $_REQUEST['shownum'];
		$cfg['showrss']     = $_REQUEST['showrss'];
		$cfg['showguest']   = $_REQUEST['showguest'];
		$cfg['menuid']      = $_REQUEST['menuid'];

        $cfg['minrate']     = $_REQUEST['minrate'];
        if ($cfg['minrate'] < 0) { $cfg['minrate'] = 0; }

		if (sizeof($_REQUEST['targets'])){
			$cfg['targets'] = $_REQUEST['targets'];
		}

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = '��������� ���������.';

	}
	
	if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
    if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}
    if (!isset($cfg['showguest'])) { $cfg['showguest'] = 0;}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>

<form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <table width="501" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td width="254">
                <strong>���������� � ����: </strong><br/>
                <span class="hinttext">�������� �������� �� ������ ����� �������������� ������ ������ �������� ����</span>
            </td>
            <td width="205" valign="top">
                <select name="menuid" id="menuid">
                    <option value="0">-- �� ���������� --</option>
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
        <tr>
            <td><strong>���������� ������������: </strong></td>
            <td><input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/> ��.</td>
        </tr>
        <tr>
          <td>
              <strong>����������� �� ��������:</strong><br/>
              <span class="hinttext">���������� ����������� � ��������� ���� ����������</span>
          </td>
          <td><input type="text" size="5" name="minrate" value="<?php echo @$cfg['minrate']?>"/></td>
        </tr>
        <tr>
            <td><strong>���������� ����������� ������: </strong></td>
            <td>
                <input name="showguest" type="radio" value="1" <?php if (@$cfg['showguest']) { echo 'checked="checked"'; } ?>/> ��
                <input name="showguest" type="radio" value="0" <?php if (@!$cfg['showguest']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������ �� RSS: </strong></td>
            <td>
                <input name="showrss" type="radio" value="1" <?php if (@$cfg['showrss']) { echo 'checked="checked"'; } ?>/> ��
                <input name="showrss" type="radio" value="0" <?php if (@!$cfg['showrss']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>���������� ����������� ���: </strong></td>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tr>
                        <td>
                            <label>
                                <input name="targets[article]" type="checkbox" id="article" value="article" <?php if (@$cfg['targets']['article']) { echo 'checked="checked"'; }?>/> ������
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="targets[photo]" type="checkbox" id="t_photo" value="photo" <?php if (@$cfg['targets']['photo']) { echo 'checked="checked"'; }?>/> ���� � �������
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="targets[palbum]" type="checkbox" id="t_photo" value="palbum" <?php if (@$cfg['targets']['palbum']) { echo 'checked="checked"'; }?>/> �������� �������
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="targets[blog]" type="checkbox" id="t_blog" value="blog" <?php if (@$cfg['targets']['blog']) { echo 'checked="checked"'; }?>/> ������� � ������
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="targets[catalog]" type="checkbox" id="t_catalog" value="catalog" <?php if (@$cfg['targets']['catalog']) { echo 'checked="checked"'; }?>/> ������� ��������
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input name="targets[userphoto]" type="checkbox" id="t_userphoto" value="userphoto" <?php if (@$cfg['targets']['userphoto']) { echo 'checked="checked"'; }?>/> ���� �������������
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" id="do" value="save" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
    </p>
</form>