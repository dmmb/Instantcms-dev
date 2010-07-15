<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	cpAddPathway('�����', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>�����</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	
	$toolmenu = array();

	if ($opt=='list_forums' || $opt=='list_cats' || $opt=='config' || $opt=='saveconfig'){
	
		$toolmenu[0]['icon'] = 'newfolder.gif';
		$toolmenu[0]['title'] = '����� ���������';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';
	
		$toolmenu[2]['icon'] = 'newforum.gif';
		$toolmenu[2]['title'] = '����� �����';
		$toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_forum';
	
		$toolmenu[1]['icon'] = 'folders.gif';
		$toolmenu[1]['title'] = '��������� �������';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';
	
		$toolmenu[3]['icon'] = 'listforums.gif';
		$toolmenu[3]['title'] = '��� ������';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_forums';

		$toolmenu[4]['icon'] = 'ranks.gif';
		$toolmenu[4]['title'] = '������ �� ������';
		$toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_ranks';

	}
	if($opt=='list_forums'){

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = '������������� ���������';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_forum&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = '����������� ���������';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_forum&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = '������ ���������';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_forum&multiple=1');";

	}
	
	if ($opt=='list_forums' || $opt=='list_cats' || $opt=='config'){

		$toolmenu[15]['icon'] = 'cancel.gif';
		$toolmenu[15]['title'] = '������';
		$toolmenu[15]['link'] = '?view=components';
	
	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = '���������';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = '������';
		$toolmenu[21]['link'] = 'javascript:history.go(-1);';
	
	}

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG

	if($opt=='saveconfig'){	
        
		$cfg = array();
		$cfg['is_on']       = $inCore->request('is_on', 'int', 1);
		$cfg['karma']       = $inCore->request('karma', 'int', 1);
		$cfg['is_rss']      = $inCore->request('is_rss', 'int', 1);
		$cfg['pp_thread']   = $inCore->request('pp_thread', 'int', 15);
		$cfg['pp_forum']    = $inCore->request('pp_forum', 'int', 15);

		$cfg['showimg']     = $inCore->request('showimg', 'int', 1);

		$cfg['img_on']      = $inCore->request('img_on', 'int', 1);
		$cfg['img_max']     = $inCore->request('img_max', 'int', 1);
		
		$cfg['fast_on']     = $inCore->request('fast_on', 'int', 1);
		$cfg['fast_bb']     = $inCore->request('fast_bb', 'int', 1);
		
		$cfg['fa_on']       = $inCore->request('fast_on', 'int');
		$cfg['fa_allow']    = $inCore->request('fa_allow', 'int');
		$cfg['fa_max']      = $inCore->request('fa_max', 'int');
		$cfg['fa_ext']      = $inCore->request('fa_ext', 'str');
		$cfg['fa_size']     = $inCore->request('fa_size', 'int');
			
		$inCore->saveComponentConfig('forum', $cfg);
		
		$msg = '��������� ���������.';
		$opt = 'config';

	}

    $cfg = $inCore->loadComponentConfig('forum');

    if (!isset($cfg['is_rss'])) { $cfg['is_rss'] = 1; }

	if($opt=='saveranks'){	
		
		$cfg['ranks']   = $_REQUEST['rank'];
		$cfg['modrank'] = $_REQUEST['modrank'];
		
		$inCore->saveComponentConfig('forum', $cfg);

		$msg = '��������� ���������.';
		$opt = 'list_ranks';		
		
	}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if ($opt == 'show_forum'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_forums', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_forums', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	if ($opt == 'hide_forum'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_forums', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_forums', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	function reorder(){
		$sql = "SELECT * FROM cms_forums ORDER BY NSLeft";
		$rs = dbQuery($sql);
		
		if (mysql_num_rows($rs)){
			$level = array();
			while ($item = mysql_fetch_assoc($rs)){
				if (isset($level[$item['NSLevel']])){
					$level[$item['NSLevel']] += 1;
				} else {
					$level[$item['NSLevel']] = 1;
				}
				$sql = "UPDATE cms_forums SET ordering = ".$level[$item['NSLevel']]." WHERE id=".$item['id'];
				dbQuery($sql) or die(mysql_error().'<pre>'.$sql);			
			}				
		}
	}
	
	if ($opt == 'submit_forum'){	
			$category_id = (int)$_REQUEST['category_id'];
			$title = $inCore->strClear($_REQUEST['title']);
			$published = $_REQUEST['published'];
			$auth_group = $_REQUEST['auth_group'];
			$parent_id = $_REQUEST['parent_id'];			
			$description = $_REQUEST['description'];
			
			$ns = $inCore->nestedSetsInit('cms_forums');
			$myid = $ns->AddNode($parent_id);
			
			$sql = "UPDATE cms_forums 
					SET category_id=$category_id, 
						title='$title', 
						description='$description', 
						auth_group='$auth_group', 
						published=$published
					WHERE id = $myid";	
			dbQuery($sql) ;	
			reorder();
			header('location:?view=components&do=config&opt=list_forums&id='.$_REQUEST['id']);		
	}	  
	
	if ($opt == 'update_forum'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['category_id'])) { $category_id = $_REQUEST['category_id']; } else { error("������� ���������!"); }
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("������� ��������� ����������!"); }
			$published = $_REQUEST['published'];
			$auth_group = $_REQUEST['auth_group'];
			$parent_id = $_REQUEST['parent_id'];
			$description = $_REQUEST['description'];
			
			$ns = $inCore->nestedSetsInit('cms_forums');
			$ns->MoveNode($id, $parent_id);			

			$sql = "UPDATE cms_forums
					SET category_id = $category_id,
						title='$title', 
						description='$description',
						published=$published,
						auth_group=$auth_group
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;		
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_forums');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_forum');
		}
	}

	if($opt == 'delete_forum'){
		include($_SERVER['DOCUMENT_ROOT'].'/components/forum/includes/forumcore.php');
		
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];		
			//DELETE POSTS WITH ALL DATA
			$sql = "SELECT p.* 
					FROM cms_forum_posts p, cms_forum_threads t, cms_forums f
					WHERE p.thread_id = t.id AND t.forum_id = f.id AND f.id = $id";
			$rs = dbQuery($sql);
			if (mysql_num_rows($rs)){
				while ($post = mysql_fetch_assoc($rs)){
					uploadDeletePost(0, $post['id']);
					$inCore->deleteUploadImages($post['id'], 'forum');	
				}
			}
			//DELETE THREADS
			dbQuery("DELETE FROM cms_forum_threads WHERE forum_id = $id");
			//DELETE FORUM
			dbDeleteNS('cms_forums', $id);
			dbQuery($sql) ;			
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_forums');
	}


	if ($opt == 'config') {

        require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();

		?>
        <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="addform" target="_self" id="form1" style="margin-top:10px">
            <?php ob_start(); ?>
            {tab=�����}
            <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">                
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>����� ��������� </h4></td>
                </tr>
                <tr>
                    <td width="259" valign="top"><strong>����� �������: </strong></td>
                    <td width="310" valign="top">
                        <input name="is_on" type="radio" value="1" <?php if (@$cfg['is_on']) { echo 'checked="checked"'; } ?> /> ��
                        <input name="is_on" type="radio" value="0" <?php if (@!$cfg['is_on']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
           </table>
           {tab=��������}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>�������� ������ </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>��� �� ��������: </strong></td>
                    <td valign="top"><input name="pp_forum" type="text" id="pp_forum" value="<?php echo @$cfg['pp_forum'];?>" size="5" /> ��.</td>
                </tr>                
                <tr>
                    <td valign="top"><strong>������ RSS: </strong></td>
                    <td valign="top">
                        <input name="is_rss" type="radio" value="1" <?php if (@$cfg['is_rss']) { echo 'checked="checked"'; } ?> /> ���
                        <input name="is_rss" type="radio" value="0" <?php if (@!$cfg['is_rss']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>�������� ����  </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>��������� �� ��������: </strong></td>
                    <td valign="top"><input name="pp_thread" type="text" id="pp_thread" value="<?php echo @$cfg['pp_thread'];?>" size="5" /> ��.</td>
                </tr>
                <tr>
                    <td valign="top"><strong>���������� ����������� ������������� �����������: </strong></td>
                    <td valign="top">
                        <input name="showimg" type="radio" value="1" <?php if (@$cfg['showimg']) { echo 'checked="checked"'; } ?> /> ���
                        <input name="showimg" type="radio" value="0" <?php if (@!$cfg['showimg']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>����� ��� �������� ������: </strong></td>
                    <td valign="top">
                        <input name="fast_on" type="radio" value="1" <?php if (@$cfg['fast_on'] || !isset($cfg['fast_on'])) { echo 'checked="checked"'; } ?> /> ���
                        <input name="fast_on" type="radio" value="0" <?php if (@!$cfg['fast_on']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td valign="top"><p><strong>��-��� � ������� ������</strong><strong>: </strong></p></td>
                    <td valign="top">
                        <input name="fast_bb" type="radio" value="1" <?php if (@$cfg['fast_bb'] || !isset($cfg['fast_bb'])) { echo 'checked="checked"'; } ?> /> ���
                        <input name="fast_bb" type="radio" value="0" <?php if (@!$cfg['fast_bb']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
           </table>
           {tab=�����������}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>������� ����������� � ���������: </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>������� �����������: </strong></td>
                    <td valign="top">
                        <input name="img_on" type="radio" value="1" <?php if (@$cfg['img_on']) { echo 'checked="checked"'; } ?> /> ���
                        <input name="img_on" type="radio" value="0" <?php if (@!$cfg['img_on']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>�������� ������:</strong><br />
                        <span class="hinttext">������� ����������� ����� �������� � ���� ���������</span>
                    </td>
                    <td valign="top"><input name="img_max" type="text" id="img_max" value="<?php echo @$cfg['img_max'];?>" size="5" /> ��.</td>
                </tr>
           </table>
           {tab=��������}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>������������ ������ (������) </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>������������ ������: </strong></td>
                    <td valign="top">
                        <input name="fa_on" type="radio" value="1" <?php if (@$cfg['fa_on']) { echo 'checked="checked"'; } ?> /> ���
                        <input name="fa_on" type="radio" value="0" <?php if (@!$cfg['fa_on']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>�������� ��� ������:</strong><br/>
                        <span class="hinttext">����� ������ ������ ������������ ������������, ����� ����� ����������� ����������� �����</span>
                    </td>
                    <td valign="top">
                        <select name="fa_allow" id="fa_allow">
                            <option value="-1" <?php if (@$cfg['fa_allow']==-1 || !isset($cfg['fa_allow'])) { echo 'selected="selected"'; } ?>>-- ��� ������ --</option>
                            <?php
                                if (isset($cfg['fa_allow'])) {
                                    echo $inCore->getListItems('cms_user_groups', $cfg['fa_allow']);
                                } else {
                                    echo $inCore->getListItems('cms_user_groups');
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>�������� ������:</strong><br />
                        <span class="hinttext">������� ������ ����� ���������� � ������ ���������</span>
                    </td>
                    <td valign="top">
                        <input name="fa_max" type="text" id="fa_max" value="<?php echo @$cfg['fa_max'];?>" size="5" /> ��.
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>����������� ����������: </strong><br />
                        <span class="hinttext">������ ���������� ����������, ����� ������</span>
                    </td>
                    <td valign="top">
                        <textarea name="fa_ext" cols="35" rows="3" id="fa_ext"><?php echo @$cfg['fa_ext'];?></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>������������ ������ �����: </strong></td>
                    <td valign="top">
                        <input name="fa_size" type="text" id="fa_size" value="<?php echo @$cfg['fa_size'];?>" size="10" /> ��
                    </td>
                </tr>
            </table>
            {/tabs}
            <?php echo jwTabs(ob_get_clean()); ?>
            <p>
                <input name="opt" type="hidden" id="do" value="saveconfig" />
                <input name="save" type="submit" id="save" value="���������" />
                <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
            </p>
        </form>
		<?php
	}
	
	if ($opt == 'list_ranks') {
		cpAddPathway('������', $_SERVER['REQUEST_URI']);
		echo '<h3>������ �� ������</h3>';
		
		if(!isset($cfg['ranks'])) { $cfg['ranks'] = array(); }
		if(!isset($cfg['modrank'])) { $cfg['modrank'] = 1; }
		?>
            <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="addform" target="_self" id="form1">
                <table width="500" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-bottom:2px">
                    <tr>
                        <td align="center" valign="middle"><strong>���������� ������ ��� �����������: </strong></td>
                        <td width="120" align="center" valign="middle">
                            <input name="modrank" type="radio" value="1" <?php if (@$cfg['modrank']) { echo 'checked="checked"'; } ?> /> ��
                            <input name="modrank" type="radio" value="0" <?php if (@!$cfg['modrank']) { echo 'checked="checked"'; } ?>/> ���
                        </td>
                    </tr>
                </table>
                <table width="500" border="0" cellpadding="10" cellspacing="0" class="proptable">
                    <tr>
                        <td align="center" valign="middle" bgcolor="#EBEBEB"><strong>������</strong></td>
                        <td width="120" align="center" valign="middle" bgcolor="#EBEBEB"><strong>����������� ����� ��������� </strong></td>
                    </tr>
                    <?php for($r = 1; $r <= 10; $r++){ ?>
                    <tr>
                        <td align="center" valign="top"><input type="text" name="rank[<?php echo $r?>][title]" style="width:250px;" value="<?php echo @$cfg['ranks'][$r]['title']?>"></td>
                        <td align="center" valign="top"><input name="rank[<?php echo $r?>][msg]" type="text" id="" value="<?php echo @$cfg['ranks'][$r]['msg']?>" size="10" /></td>
                    </tr>
                    <?php } ?>
                </table>
                <p>
                    <input name="opt" type="hidden" id="do" value="saveranks" />
                    <input name="save" type="submit" id="save" value="���������" />
                    <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>';"/>
                </p>
            </form>
		<?php
	}
	
	
	if ($opt == 'show_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_forum_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_forum_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}
	
	if ($opt == 'submit_cat'){	
		if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("������� ��������� ���������!"); }
		$published = $_REQUEST['published'];
		$auth_group = $_REQUEST['auth_group'];
		$ordering = $_REQUEST['ordering'];		
				
		$sql = "INSERT INTO cms_forum_cats (title, published, auth_group, ordering)
				VALUES ('$title', '$published', '$auth_group', '$ordering')";
		dbQuery($sql) ;
		
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			dbQuery("DELETE FROM cms_forums WHERE category_id = $id");			
			dbQuery("DELETE FROM cms_forum_cats WHERE id = $id");			
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("������� ��������� ���������!"); }
			$published = $_REQUEST['published'];
			$auth_group = $_REQUEST['auth_group'];
			$ordering = $_REQUEST['ordering'];		
				
			$sql = "UPDATE cms_forum_cats
					SET title='$title', 
						published=$published,
						auth_group=$auth_group,
						ordering=$ordering
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
							
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		
		}
	}
	
	if ($opt == 'list_cats'){
		cpAddPathway('��������� �������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>��������� �������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';
		
		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_cat'; //����� ������ 'do=hide&id=1' ���� 'opt=hide_albun&item_id=1'
		
	//	$fields[3]['title'] = '�������';	$fields[3]['field'] = 'ordering';	$fields[3]['width'] = '100';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������ � ���������� ����� ������� ��� �� ������. ������� ���������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_forum_cats', $fields, $actions);		
	}

	if ($opt == 'list_forums'){
		cpAddPathway('������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_forums');
		echo '<h3>������</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['filter'] = 15;
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_forum&item_id=%id%';

		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_forum';
	
		$fields[4]['title'] = '���������';		$fields[4]['field'] = 'category_id';	$fields[4]['width'] = '150';
		$fields[4]['prc'] = 'cpForumCatById';  $fields[4]['filter'] = 1;  $fields[4]['filterlist'] = cpGetList('cms_forum_cats');
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_forum&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� �����?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_forum&item_id=%id%';
				
		//Print table
		cpListTable('cms_forums', $fields, $actions, 'parent_id>0', 'NSLeft');		
	}
	
	if ($opt == 'add_cat' || $opt == 'edit_cat'){
		if ($opt=='add_cat'){
			 echo '<h3>�������� ���������</h3>';
		} else {
					 if(isset($_REQUEST['item_id'])){
						 $id = $_REQUEST['item_id'];
						 $sql = "SELECT * FROM cms_forum_cats WHERE id = $id LIMIT 1";
						 $result = dbQuery($sql) ;
						 if (mysql_num_rows($result)){
							$mod = mysql_fetch_assoc($result);
						 }
					 }
					
					 echo '<h3>������������� ���������</h3>';
			   }
		?>
        <form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>">
            <table width="600" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="211" valign="top">��������� ���������: </td>
                    <td width="195" valign="top"><input name="title" type="text" id="title" size="30" value="<?php echo @$mod['title'];?>"/></td>
                    <td width="168" valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">����������� ���������?</td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> ��
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> ���
                    </td>
                    <td valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">���������� ������: </td>
                    <td valign="top">
                        <select name="auth_group" id="auth_group">
                            <option value="0" <?php if (@$mod['auth_group']=='0') { echo "selected"; }?>>���� �������</option>
                            <?php
                                if (isset($mod['auth_group'])) {
                                    echo $inCore->getListItems('cms_user_groups', $mod['auth_group']);
                                } else {
                                    echo $inCore->getListItems('cms_user_groups');
                                }
                            ?>
                        </select>
                    </td>
                    <td valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">���������� �����: </td>
                    <td valign="top"><input name="ordering" type="text" id="ordering" value="<?php echo @$mod['ordering'];?>" size="5" /></td>
                    <td valign="top">&nbsp;</td>
                </tr>
            </table>
            <p>
                <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
                <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_cat') { echo 'value="������� ���������"'; } else { echo 'value="��������� ���������"'; } ?> />
                <input name="back2" type="button" id="back2" value="������" onclick="window.location.href='index.php?view=components';"/>
                <?php
                    if ($opt=='edit_cat'){
                        echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                    }
                ?>
            </p>
        </form>
		<?php
	}

	if ($opt == 'add_forum' || $opt == 'edit_forum'){	
			
		if ($opt=='add_forum'){
			 echo '<h3>�������� �����</h3>';
		} else {
					if(isset($_REQUEST['multiple'])){				 
						if (isset($_REQUEST['item'])){					
							$_SESSION['editlist'] = $_REQUEST['item'];
						} else {
							echo '<p class="error">��� ��������� ��������!</p>';
							return;
						}				 
					 }
						
					 $ostatok = '';
					
					 if (isset($_SESSION['editlist'])){
						$id = array_shift($_SESSION['editlist']);
						if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else 
						{ $ostatok = '(�� �������: '.sizeof($_SESSION['editlist']).')'; }
					 } else { $id = $_REQUEST['item_id']; }
		
		
					 $sql = "SELECT * FROM cms_forums WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_forums');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_forum&item_id='.$id);		
						
			}
		?>
        <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="addform" id="addform">
            <table width="514" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="236" valign="top">�������� ������: </td>
                    <td width="259" valign="top"><input name="title" type="text" id="title" size="30" value="<?php echo @$mod['title'];?>" style="width:250px"/></td>
                </tr>
                <tr>
                    <td valign="top">�������� ������: </td>
                    <td valign="top"><textarea name="description" cols="35" rows="2" id="description" style="width:250px"><?php echo @$mod['description']?></textarea></td>
                </tr>
                <tr>
                    <td valign="top">������������ �����: </td>
                    <td valign="top">
                        <?php $rootid = dbGetField('cms_forums', 'parent_id=0', 'id'); ?>
                        <select name="parent_id" size="8" id="parent_id" style="width:250px">
                                <option value="<?php echo $rootid?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- ������ ������� --</option>
                        <?php
                            if (isset($mod['parent_id'])){
                               echo $inCore->getListItemsNS('cms_forums', $mod['parent_id']);
                            } else {
                               echo $inCore->getListItemsNS('cms_forums');
                            }
                        ?>
                        </select>
                   </td>
                </tr>
                <tr>
                    <td valign="top">���������:</td>
                    <td valign="top">
                        <select name="category_id" id="category_id" style="width:250px">
                        <?php
                            if (isset($mod['category_id'])) { 
                                echo $inCore->getListItems('cms_forum_cats', $mod['category_id'], 'ordering');
                            } else {
                                if (isset($_REQUEST['addto'])){
                                    echo $inCore->getListItems('cms_forum_cats', $_REQUEST['addto'], 'ordering');
                                } else { 
                                   echo $inCore->getListItems('cms_forum_cats', 0, 'ordering');
                                }
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">����������� �����?</td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> ��
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> ���
                    </td>
                </tr>
                <tr>
                <td valign="top">���������� ������: </td>
                <td valign="top">
                    <select name="auth_group" id="auth_group" style="width:250px">
                        <option value="0" <?php if (@$mod['auth_group']=='0') { echo "selected"; }?>>���� �������</option>
                        <?php
                            if (isset($mod['auth_group'])) {
                                echo $inCore->getListItems('cms_user_groups', $mod['auth_group']);
                            } else {
                                echo $inCore->getListItems('cms_user_groups');
                            }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <p>
            <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_forum') { echo 'value="������� �����"'; } else { echo 'value="��������� �����"'; } ?> />
            <input name="back3" type="button" id="back3" value="������" onclick="window.location.href='index.php?view=components';"/>
            <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_forum') { echo 'value="submit_forum"'; } else { echo 'value="update_forum"'; } ?> />
            <?php
            if ($opt=='edit_forum'){
                echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
            }
            ?>
        </p>
        </form>
	 <?php	
	}
?>