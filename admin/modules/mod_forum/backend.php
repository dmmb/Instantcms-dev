<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('���� �� ������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>���� �� ������</h3>';
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
		$cfg['shownum'] = $_REQUEST['shownum'];		
		$cfg['showtype'] = $_REQUEST['showtype'];		
		$cfg['showforum'] = $_REQUEST['showforum'];		
		$cfg['showlink'] = $_REQUEST['showlink'];		
		$cfg['showtext'] = $_REQUEST['showtext'];		
		$cfg['menuid'] = $_REQUEST['menuid'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$msg = '��������� ���������.';

	}
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
	
	if (!isset($cfg['showtext'])){ $cfg['showtext'] = 0; }
?>

      <form action="index.php?view=modules&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
        <table width="516" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="321"><strong>���������� ���: </strong></td>
            <td width="155"><input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/> ��.</td>
          </tr>
          <tr>
            <td><strong>���������� ����� ���������:</strong><br />
                <span class="hinttext">������ ��� ������ Web 2.0 </span></td>
            <td><input name="showtext" type="radio" value="1" <?php if (@$cfg['showtext']) { echo 'checked="checked"'; } ?>/>
              ��
              <input name="showtext" type="radio" value="0" <?php if (@!$cfg['showtext']) { echo 'checked="checked"'; } ?>/>
              ��� </td>
          </tr>
          <tr>
            <td><strong>����� ������ ���:</strong> </td>
            <td><select name="showtype" id="showtype">
              <option value="classic" <?php if (@$cfg['showtype']=='classic') { echo 'selected="selected"'; } ?>>������������</option>
              <option value="web2" <?php if (@$cfg['showtype']=='web2') { echo 'selected="selected"'; } ?>>Web 2.0</option>
            </select></td>
          </tr>
          <tr>
            <td><strong>���������� �������� ������:</strong><br />
            <span class="hinttext">������ ��� ������ Web 2.0 </span></td>
            <td><input name="showforum" type="radio" value="1" <?php if (@$cfg['showforum']) { echo 'checked="checked"'; } ?>/>
��
  <input name="showforum" type="radio" value="0" <?php if (@!$cfg['showforum']) { echo 'checked="checked"'; } ?>/>
��� </td>
          </tr>
          <tr>
            <td><strong>���������� � ����: </strong><br/>
                <span class="hinttext">�������� �������� �� ������ ����� �������������� ������ ������ �������� ����</span></td>
            <td valign="top"><select name="menuid" id="menuid">
                <option value="0">-- �� ���������� --</option>
                <?php
                        if (isset($cfg['menuid'])){
                            echo $inCore->getListItemsNS('cms_menu', $cfg['menuid']);
                        } else {
                            echo $inCore->getListItemsNS('cms_menu');
                        }
                    ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>���������� ������ �� �����:</strong><br />
                <span class="hinttext">������ ��� ������ Web 2.0 </span></td>
            <td><input name="showlink" type="radio" value="1" <?php if (@$cfg['showlink']) { echo 'checked="checked"'; } ?>/>
              ��
              <input name="showlink" type="radio" value="0" <?php if (@!$cfg['showlink']) { echo 'checked="checked"'; } ?>/>
              ���</td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>