<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('������� �����', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>������� �����</h3>';
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
		$cfg['category_id'] = $_REQUEST['category_id'];
		$cfg['icon'] = $_REQUEST['icon'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = '��������� ���������.';

	}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
<form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
  <table width="501" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td width="254"><strong>���������� ��������: </strong></td>
      <td width="205"><input name="showdesc" type="radio" value="1" <?php if (@$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
        ��
        <input name="showdesc" type="radio" value="0" <?php if (@!$cfg['showdesc']) { echo 'checked="checked"'; } ?>/>
        ��� </td>
    </tr>
    <tr>
      <td><strong>������������ ������: </strong></td>
      <td><select name="category_id" id="category_id">
        <?php 
            if (isset($cfg['category_id'])) {
                echo $inCore->getListItemsNS('cms_category', $cfg['category_id']);
            } else {
                echo $inCore->getListItemsNS('cms_category');
            }
        ?>
      </select></td>
    </tr>
    <tr>
      <td valign="top"><strong>������:</strong><br />
          <div class="hinttext">�������� ���� ������, ���� ������ �� �����</div></td>
      <td valign="top"><label>
        <input name="icon" type="text" id="icon" value="<?php echo @$cfg['icon'];?>" size="30" />
        <?php if (@$cfg['icon'] && !file_exists($_SERVER['DOCUMENT_ROOT'].$cfg['icon'])){ ?>
        <br/>
        <span style="color:#FF0000">���� ������ �� ������!</span>
        <?php } ?>
      </label></td>
    </tr>
  </table>
  <p>
    <input name="opt" type="hidden" id="do" value="save" />
    <input name="save" type="submit" id="save" value="���������" />
    <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
  </p>
</form>