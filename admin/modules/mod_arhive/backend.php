<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('���������', '?view=modules&do=edit&id='.$_REQUEST['id']);
	cpAddPathway('���������', '?view=modules&do=config&id='.$_REQUEST['id']);
    
	//LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	echo '<h3>���������</h3>';

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

	
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
		
	

	if($opt == 'save'){	
		$cfg = array();
		$cfg['cat_id'] = $_REQUEST['cat_id'];
		$cfg['source'] = $_REQUEST['source'];
		$cfg['menuid'] = $_REQUEST['menuid'];
			
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

		$sql = "UPDATE cms_modules SET config = '".serialize($cfg)."' WHERE id = ".$_REQUEST['id'];
		dbQuery($sql) ;
		
		$msg = '��������� ���������.';
		
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

?>
      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform">
		<input type="hidden" name="opt" value="save"/>
        <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="239"><strong>�������� ������:</strong> </td>
            <td width="204"><select name="source" id="source">
              <option value="content" <?php if(@$cfg['source']=='content') { echo 'selected'; } ?>>������� ������</option>
              <option value="arhive" <?php if(@$cfg['source']=='arhive') { echo 'selected'; } ?>>����� ������</option>
              <option value="both" <?php if(@$cfg['source']=='both') { echo 'selected'; } ?>>������� � �����</option>
            </select></td>
          </tr>
          <tr>
            <td><strong>���������� ������ �� �������:</strong></td>
            <td>
                <select name="cat_id" id="cat_id">
                    <option value="-1" selected>-- ��� ������� --</option>
                    <?php
                        if (isset($cfg['cat_id'])) {
                            echo $inCore->getListItemsNS('cms_category', $cfg['cat_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_category');
                        }
                    ?>
                </select>
            </td>
          </tr>
          <tr>
            <td>
                <strong>���������� � ����: </strong><br/>
                <span class="hinttext">�������� �������� �� ������ ����� �������������� ������ ������ �������� ����</span>
            </td>
            <td valign="top">
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
        </table>
        <p>
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>
