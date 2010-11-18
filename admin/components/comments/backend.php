<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function cpStripComment($text){

    if (sizeof($text) < 120) { return $text; }

    return substr($text, 0, 120) . '...';

}

	cpAddPathway('����������� �������������', '?view=components&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
		$toolmenu = array();

        if ($opt!='list'){
            $toolmenu[1]['icon'] = 'listcomments.gif';
            $toolmenu[1]['title'] = '��� �����������';
            $toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list';
        }

        if ($opt!='config'){
            $toolmenu[2]['icon'] = 'config.gif';
            $toolmenu[2]['title'] = '��������� ����������';
            $toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';
        }

		$toolmenu[3]['icon'] = 'cancel.gif';
		$toolmenu[3]['title'] = '������';
		$toolmenu[3]['link'] = '?view=components';
	
		cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('comments');
    $inCore->loadModel('comments');
    $model = new cms_model_comments();

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['email']           = $inCore->strClear($_REQUEST['email']);
		$cfg['canguests']       = (int)$_REQUEST['canguests'];
		$cfg['regcap']          = (int)$_REQUEST['regcap'];
		$cfg['publish']         = (int)$_REQUEST['publish'];
		$cfg['smiles']          = (int)$_REQUEST['smiles'];
		$cfg['bbcode']          = (int)$_REQUEST['bbcode'];
		$cfg['selfdel']         = (int)$_REQUEST['selfdel'];
		$cfg['subscribe']       = (int)$_REQUEST['subscribe'];
		$cfg['anchors']         = (int)$_REQUEST['anchors'];
		$cfg['recode']          = (int)$_REQUEST['recode'];
		$cfg['min_karma'] 		= (int)$_REQUEST['min_karma'];
		$cfg['min_karma_show'] 	= (int)$_REQUEST['min_karma_show'];
		$cfg['min_karma_add'] 	= (int)$_REQUEST['min_karma_add'];
		$cfg['perpage'] 		= (int)$_REQUEST['perpage'];
		$cfg['j_code'] 			= (int)$_REQUEST['j_code'];
		$cfg['cmm_ajax'] 		= (int)$_REQUEST['cmm_ajax'];
		$cfg['cmm_ip'] 		    = (int)$_REQUEST['cmm_ip'];
		$cfg['max_level'] 		= (int)$_REQUEST['max_level'];
			
		$inCore->saveComponentConfig('comments', $cfg);
        
        $inCore->redirect('index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
	}

	if ($opt == 'show'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_comments SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_comments SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'update'){
		if(isset($_REQUEST['item_id'])) { 
			
			$id = $inCore->request('item_id', 'int');

			if (isset($_REQUEST['guestname']) && @!empty($_REQUEST['guestname'])) { $guestname = $inCore->request('guestname', 'str'); }
			else { $guestname = ''; }
			
			$pubdate   = $inCore->request('pubdate', 'str');
			$published = $inCore->request('published', 'int');
			$content   = $inCore->request('content', 'html');
						
			$sql = "UPDATE cms_comments
					SET guestname = '$guestname',
						pubdate = '$pubdate', 						
						published=$published,
						content='$content'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
			header('location:index.php?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list');				
		}
	}

	if($opt == 'delete'){
		if(isset($_REQUEST['item_id'])) { 
			$id = (int)$_REQUEST['item_id'];		
            $model->deleteComment($id);
			header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
		}
	}
	
	if ($opt == 'list'){
		cpAddPathway('��� �����������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		echo '<h3>��� �����������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '����';		$fields[1]['field'] = 'pubdate';	$fields[1]['width'] = '100';

		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'content';	$fields[2]['width'] = ''; 
        $fields[2]['prc'] = 'cpStripComment';

		$fields[3]['title'] = 'IP';			$fields[3]['field'] = 'ip';			$fields[3]['width'] = '80'; 

		$fields[4]['title'] = '�����';		$fields[4]['field'] = 'id';			$fields[4]['width'] = '180'; 
		$fields[4]['prc'] = 'cpCommentAuthor';

		$fields[5]['title'] = '����';		$fields[5]['field'] = 'id';			$fields[5]['width'] = '220'; 
		$fields[5]['prc'] = 'cpCommentTarget';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� �����������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete&item_id=%id%';
				
		//Print table
		cpListTable('cms_comments', $fields, $actions, '', 'pubdate DESC');		
	}
	
	if($opt=='edit'){	
			 if(isset($_REQUEST['item_id'])){
				 $id = $_REQUEST['item_id'];
				 $sql = "SELECT * FROM cms_comments WHERE id = $id LIMIT 1";
				 $result = dbQuery($sql) ;
				 if (mysql_num_rows($result)){
					$mod = mysql_fetch_assoc($result);				 
					if($mod['user_id']==0) { $author = '<input name="guestname" type="text" id="title" size="30" value="'.@$mod['guestname'].'"/>'; }
					else {
						$usersql = "SELECT * FROM cms_users WHERE id = ".$mod['user_id'];
						$userres = dbQuery($usersql) ;
						$u = mysql_fetch_assoc($userres);
						$author = $u['nickname'].' (<a target="_blank" href="/admin/index.php?view=users&do=edit&id='.$u['id'].'">'.$u['login'].'</a>)';
					}
					$target='N/A';
					switch($mod['target']){
						case 'article': $target = '<a href="/index.php?view=content&do=read&id='.$mod['target_id'].'">������</a> (ID='.$mod['target_id'].')'; break;
						case 'photo': $target = '<a href="/index.php?view=content&do=viewphoto&id='.$mod['target_id'].'">����</a> (ID='.$mod['target_id'].')'; break;
						case 'user': $target = '<a href="/index.php?view=profile&do=view&id='.$mod['target_id'].'">������������</a> (ID='.$mod['user_id'].')'; break;
					}
				}
								 
			 }

			cpAddPathway('������������� �����������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add');
		    echo '<h3>������������� �����������</h3>';
		
?>

	<form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
        <table width="662" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="200"><strong>����� �����������: </strong></td>
            <td><?php echo $author?></td>
          </tr>
          <tr>
            <td><strong>���� ������: </strong></td>
            <td><input name="pubdate" type="text" id="title3" size="30" value="<?php echo @$mod['pubdate'];?>"/></td>
          </tr>
          <tr>
            <td><strong>����������� �����������?</strong></td>
            <td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
          <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
        </table>
        <div class="usr_msg_bbcodebox" style="width:660px">
            <?php
                $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/core/js/smiles.js"></script>';
                echo cmsPage::getBBCodeToolbar('content', false);
            ?>
        </div>
        <div style="width:660px;">
            <?php
                echo cmsPage::getSmilesPanel('content');
            ?>
        </div>
        <textarea id="content" name="content" style="width:650px;height:200px"><?php echo $mod['content']; ?></textarea>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" value="��������� ���������"/>
          </label>
          <label>
          <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
          </label>
          <input name="opt" type="hidden" id="do" value="update" />
		  <input name="item_id" type="hidden" value="<?php echo $mod['id']?>" />
        </p>
</form>
        <?php	

	}//if (add || edit)

	if($opt=='config'){

    if(!isset($cfg['bbcode'])) { 			$cfg['bbcode']=1; }
	if(!isset($cfg['recode'])) { 			$cfg['recode']=1; }
	if(!isset($cfg['min_karma'])) { 		$cfg['min_karma']=0; 		}
	if(!isset($cfg['min_karma_add'])) { 	$cfg['min_karma_add']=0; 	}
	if(!isset($cfg['min_karma_show'])) { 	$cfg['min_karma_show']=0;	}
	if(!isset($cfg['regcap'])) { 			$cfg['regcap']=1;	}
	if(!isset($cfg['perpage'])) { 			$cfg['perpage']=20;	}
	if(!isset($cfg['j_code'])) { 			$cfg['j_code']=1;	}
	if(!isset($cfg['cmm_ajax'])) { 			$cfg['cmm_ajax']=0;	}
	if(!isset($cfg['cmm_ip'])) { 			$cfg['cmm_ip']=1;	}
	if(!isset($cfg['max_level'])) { 		$cfg['max_level']=5;	}

	cpAddPathway('���������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config');	
	echo '<h3>��������� ������������</h3>';
	
	?>
	<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table width="671" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>�������� ������������ </h4></td>
          </tr>
          <tr>
            <td width="316" valign="top"><strong>E-mail ��� ������������: <br />              
            </strong><span class="hinttext">�������� ������, ���� �� �� ������ �������� ����������� �� �����</span></td>
            <td width="313" valign="top"><input name="email" type="text" id="title2" size="30" value="<?php echo @$cfg['email'];?>"/></td>
          </tr>
          <tr>
            <td valign="top"><strong>�������� �� �����������: </strong><br />
                <span class="hinttext">��������� ������������� �������� ������ ��������� � ������������� � ����� ������������</span> </td>
            <td valign="top"><input name="subscribe" type="radio" value="1" <?php if (@$cfg['subscribe']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="subscribe" type="radio" value="0"  <?php if (@!$cfg['subscribe']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
          <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>������ ������  </h4></td>
          </tr>
          <tr>
            <td valign="top"><strong>��������� �����������, ��������� ajax?</strong> </td>
            <td valign="top"><input name="cmm_ajax" type="radio" value="1" <?php if (@$cfg['cmm_ajax']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="cmm_ajax" type="radio" value="0"  <?php if (!$cfg['cmm_ajax']) { echo 'checked="checked"'; } ?> />
              ���</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>������ � ������������:</strong> </td>
            <td valign="top"><input name="smiles" type="radio" value="1" <?php if (@$cfg['smiles']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="smiles" type="radio" value="0"  <?php if (@!$cfg['smiles']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>BB-Code � ������������:</strong> </td>
            <td valign="top"><input name="bbcode" type="radio" value="1" <?php if (@$cfg['bbcode']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="bbcode" type="radio" value="0"  <?php if (@!$cfg['bbcode']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>����� ����������� � ����� ������������:</strong></td>
            <td valign="top"><a href="/includes/letters/newcomment.txt">/includes/letters/newcomment.txt</a></td>
          </tr>
          <tr>
            <td valign="top"><strong>������������ ������� �����������:</strong></td>
            <td valign="top"><input name="max_level" type="text" id="max_level" value="<?php echo @$cfg['max_level'];?>" size="3" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>���������� ������������ �� �������� ��� ��������� ���� ������������ �����:</strong></td>
            <td valign="top"><input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" size="3" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>�������� ��������� ���������� ����:</strong></td>
            <td valign="top"><input name="j_code" type="radio" value="1" <?php if (@$cfg['j_code']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="j_code" type="radio" value="0"  <?php if (!$cfg['j_code']) { echo 'checked="checked"'; } ?> />
              ���</label>
			</td>
          </tr>
          <tr>
            <td valign="middle"><strong>���������� ip ������������� ���������������: </strong></td>
            <td>
				<select name="cmm_ip" id="cmm_ip" style="width:220px">
					<option value="0" <?php if($cfg['cmm_ip']==0) { echo 'selected'; } ?>>�� ����������</option>
					<option value="1" <?php if($cfg['cmm_ip']==1) { echo 'selected'; } ?>>������ ������</option>
                    <option value="2" <?php if($cfg['cmm_ip']==2) { echo 'selected'; } ?>>����</option>
				</select>
			</td>
          </tr>
          <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>����������� ������������  </h4></td>
          </tr>
          <tr>
            <td valign="middle"><strong>����������� ��������: </strong></td>
            <td>
				<select name="canguests" id="canguests" style="width:220px">
					<option value="0" <?php if(@$cfg['canguests']==0) { echo 'selected'; } ?>>��� ������������������</option>
					<option value="1" <?php if(@$cfg['canguests']==1) { echo 'selected'; } ?>>��� ����</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="top">
				<strong>��������� �������� ���:</strong><br />
				<span class="hinttext">����� ������������� ���������� ����� ��� ���������� ����������� </span>
			</td>
            <td valign="top">
				<select name="regcap" id="regcap" style="width:220px">
				  <option value="0" <?php if(@$cfg['regcap']==0) { echo 'selected'; } ?>>��� ������</option>
				  <option value="1" <?php if(@$cfg['regcap']==1) { echo 'selected'; } ?>>��� ����</option>
				</select>
			</td>
          </tr>
          <tr>
            <td valign="top"><strong>����������� �����:</strong><br />
            <span class="hinttext">���������, ���� �� ������ ��������� ����������� ����� ����������� �� ����</span></td>
            <td valign="top"><input name="publish" type="radio" value="1" <?php if (@$cfg['publish']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="publish" type="radio" value="0"  <?php if (@!$cfg['publish']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>��������� ����� ���������� �����������:</strong><br />
                <span class="hinttext">�����������, ���� � ����� &quot;�������� �����������&quot; ����� ������������ ����������� </span></td>
            <td valign="top"><select name="recode" id="recode" style="width:220px">
                <option value="1" selected="selected"  <?php if(@$cfg['recode']==1) { echo 'selected'; } ?>>UTF8</option>
                <option value="0" <?php if(@$cfg['recode']==0) { echo 'selected'; } ?>>CP1251</option>
                        </select></td>
          </tr>
          
          <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>����������� �� ����� </h4></td>
          </tr>
          <tr>
            <td valign="top"><strong>������������ �����������:</strong><br />
                <span class="hinttext">���� ���������, ����������� ������������ ������ ��������� �����������, ���������� �� �������� ����� ����� </span></td>
            <td valign="top"><input name="min_karma" type="radio" value="1" <?php if (@$cfg['min_karma']) { echo 'checked="checked"'; } ?> />
              ��
              <input name="min_karma" type="radio" value="0" <?php if (@!$cfg['min_karma']) { echo 'checked="checked"'; } ?>/>
              ���</td>
          </tr>
          <tr>
            <td valign="top"><strong>���������� �����������:</strong><br />
                <span class="hinttext">������� ����� ����� ����� ��� ���������� ����������� </span></td>
            <td valign="top"><input name="min_karma_add" type="text" id="min_karma_add" value="<?php echo @$cfg['min_karma_add'];?>" size="5" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>����������� �����������, � ��������� ����:</strong><br />
                <span class="hinttext">����������� c ��������� ���� ���������� ����� ��������� � ��������� ���� </span></td>
            <td valign="top"><input name="min_karma_show" type="text" id="min_karma_show" value="<?php echo @$cfg['min_karma_show'];?>" size="5" /></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="saveconfig" />
          <input name="save" type="submit" id="save" value="���������" />
          <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
        </p>
</form>    <?php
	
	}

?>