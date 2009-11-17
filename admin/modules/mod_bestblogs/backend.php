<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('���������� � ������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������� � ������', '?view=modules&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	echo '<h3>���������� � ������</h3>';
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
		$cfg['menuid'] = $_REQUEST['menuid'];
        $cfg['namemode']    = $_REQUEST['namemode'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		$msg = '��������� ���������.';

	}

    if (!isset($cfg['namemode'])) { $cfg['namemode'] = 'blog';}
    if (!isset($cfg['shownum'])) { $cfg['shownum'] = 10; }
    if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0; }

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
<form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
  <table width="501" border="0" cellpadding="10" cellspacing="0" class="proptable">
    <tr>
      <td width="">
            <strong>���������� � ����: </strong><br/>
            <span class="hinttext">�������� �������� �� ������ ����� �������������� ������ ������ �������� ����</span>
      </td>
      <td width="250" valign="top">
          <select name="menuid" id="menuid" style="width:250px">
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
      <td width="">
            <strong>������ ����� ������ ������: </strong><br/>
            <span class="hinttext">���������� �������� ����� ��� ��� ��� �������</span>
      </td>
      <td width="205" valign="top">
        <select name="namemode" id="namemode" style="width:250px">
          <option value="blog" <?php if ($cfg['namemode']=='blog') { echo 'selected="selected"'; } ?>>�������� �����</option>
          <option value="user" <?php if ($cfg['namemode']=='user') { echo 'selected="selected"'; } ?>>��� ������������</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><strong>���������� ���������� �������: </strong></td>
      <td>
        <input type="text" size="5" name="shownum" value="<?php echo @$cfg['shownum']?>"/> ��.
      </td>
    </tr>
  </table>
  <p>
    <input name="opt" type="hidden" id="do" value="save" />
    <input name="save" type="submit" id="save" value="���������" />
    <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
  </p>
</form>