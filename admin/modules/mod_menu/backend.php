<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
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

    if (!$cfg['tpl']) { $cfg['tpl'] = 'mod_menu.tpl'; }
    if (!isset($cfg['show_home'])) { $cfg['show_home'] = 1; }

	if($opt=='save'){
	
		$cfg = array();
		$cfg['menu']        = $inCore->request('menu', 'str', 'mainmenu');
		$cfg['tpl']         = $inCore->request('tpl', 'str', 'tpl');
		$cfg['show_home']   = $inCore->request('show_home', 'int', '1');
		
        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);
		
		$msg = '��������� ���������.';

	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
    
?>

      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">
    <table width="" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="200"><strong>���� ��� �����������:</strong></td>
            <td width="280">
                <select name="menu" id="menu" style="width:100%">
              <option value="mainmenu" <?php if (@$cfg['menu']=='mainmenu' || !isset($cfg['menu'])) { echo 'selected'; }?>>������� ����</option>
                <?php for($m=1;$m<=15;$m++){ ?>
                    <option value="menu<?php echo $m; ?>" <?php if (@$cfg['menu']=='menu'.$m) { echo 'selected'; }?>>�������������� ���� <?php echo $m; ?></option>
                <?php } ?>
            </select>
            </td>
          </tr>
          <tr>
            <td><strong>���������� ����� &laquo;�������&raquo;:</strong></td>
            <td>
                <label><input type="radio" name="show_home" value="1" <?php if ($cfg['show_home']){ ?>checked="checked"<?php } ?>/> ��</label>
                <label><input type="radio" name="show_home" value="0" <?php if (!$cfg['show_home']){ ?>checked="checked"<?php } ?>/> ���</label>
            </td>
          </tr>
          <tr>
            <td><strong>������������ ������:</strong></td>
            <td>
                <input type="text" name="tpl" value="<?php echo $cfg['tpl']; ?>" style="width:99%" />
            </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="�����" onclick="window.location.href='index.php?view=modules';"/>
        </p>
    </form>