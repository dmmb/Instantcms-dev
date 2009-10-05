<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('board');

	cpAddPathway('����� ����������', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>����� ����������</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }

    $inUser = cmsUser::getInstance();

	$toolmenu = array();

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['maxcols'] = $_REQUEST['maxcols'];
		$cfg['obtypes'] = $_REQUEST['obtypes'];
		$cfg['showlat'] = $_REQUEST['showlat'];
		$cfg['public'] = $_REQUEST['public'];
		$cfg['photos'] = $_REQUEST['photos'];
		$cfg['srok'] = $_REQUEST['srok'];
		$cfg['pubdays'] = $_REQUEST['pubdays'];
		$cfg['watermark'] = $_REQUEST['watermark'];

		$inCore->saveComponentConfig('board', $cfg);
		
		$msg = '��������� ���������.';
		$opt = 'config';
	}

	if ($opt=='list_items' || $opt=='list_cats' || $opt=='config'){
	
		$toolmenu[0]['icon'] = 'newfolder.gif';
		$toolmenu[0]['title'] = '����� �������';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';
	
		$toolmenu[1]['icon'] = 'newform.gif';
		$toolmenu[1]['title'] = '����� ����������';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

		$toolmenu[3]['icon'] = 'folders.gif';
		$toolmenu[3]['title'] = '��� �������';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';
	
		$toolmenu[4]['icon'] = 'listforms.gif';
		$toolmenu[4]['title'] = '��� ����������';
		$toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

	}
	if($opt=='list_items'){

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = '������������� ���������';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_item&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = '����������� ���������';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_item&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = '������ ���������';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_item&multiple=1');";

	}

	if ($opt=='list_items' || $opt=='list_cats' || $opt=='config'){

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

	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_board_items', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_board_items', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_board_items', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_board_items', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	if ($opt == 'submit_item'){	
	
			if (!empty($_REQUEST['category_id'])) { $category_id = $_REQUEST['category_id']; } else { $category_id = 1; }
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { $title = '����������'; }
			$content = $_REQUEST['content'];
			$published = $_REQUEST['published'];
			$city = $_REQUEST['city'];
			$obtype = $_REQUEST['obtype'];

			$pubdays = $_REQUEST['pubdays'];	
			
			$title = $obtype .' '. $title;

			$pubdate = $_REQUEST['pubdate'];			
			$pubdate = explode('.', $pubdate);
			$pubdate = $pubdate[2] . '-' . $pubdate[1] . '-' . $pubdate[0];
					
			$user_id = $inUser->id;
												
			if (isset($_FILES['picture']['tmp_name'])){
				if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
						$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/board/';		
						$realfile = $_FILES['picture']['name'];
			
						$lid = dbGetFields('cms_board_items', 'id>0', 'id', 'id DESC');
						$lastid = $lid['id']+1;	
						$filename = md5($lastid).'.jpg';
			
						$uploadfile = $uploaddir . $realfile;
						$uploadphoto = $uploaddir . $filename;
						$uploadthumb = $uploaddir . 'small/' . $filename;
						$uploadthumb2 = $uploaddir . 'medium/' . $filename;
			
						if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }

						$sql_album = "SELECT thumb1, thumb2, thumbsqr FROM cms_board_cats WHERE id = $category_id";
						$rs_album = dbQuery($sql_album) or die('Error retrieving category information');			
						if (mysql_num_rows($rs_album)==1){ $cat = mysql_fetch_assoc($rs_album); } else { die('Error retrieving category information'); }
				
						@img_resize($uploadphoto, $uploadthumb, $cat['thumb1'], $cat['thumb1'], $cat['thumbsqr']);
						@img_resize($uploadphoto, $uploadthumb2, $cat['thumb2'], $cat['thumb2'], false, $cfg['watermark']);
						if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
						
						@unlink($uploadphoto);											
				}		
			}										
			$sql = "INSERT INTO cms_board_items (category_id, user_id, obtype, title , content, city, pubdate, pubdays, published, file)
					VALUES ('$category_id', '$user_id', '$obtype', '$title', '$content', '$city', '$pubdate', '$pubdays', '$published', '$filename')";	
			dbQuery($sql) or die(mysql_error().'<pre>'.$sql.'</pre>');
										
			header('location:?view=components&do=config&opt=list_items&id='.$_REQUEST['id']);		
	}	  

	if ($opt == 'update_item'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['category_id'])) { $category_id = $_REQUEST['category_id']; } else { $category_id = 1; }
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { $title = '����������'; }
			$content = $_REQUEST['content'];
			$published = $_REQUEST['published'];
			$city = $_REQUEST['city'];
			$obtype = $_REQUEST['obtype'];

			$pubdays = $_REQUEST['pubdays'];
			
			$title = $obtype .' '. $title;	
			
			$pubdate = $_REQUEST['pubdate'];			
			if(!strstr($pubdate, '-')){
				$pubdate = explode('.', $pubdate);
				$pubdate = $pubdate[2] . '-' . $pubdate[1] . '-' . $pubdate[0];
			}
						
			$sql = "UPDATE cms_board_items
					SET category_id = $category_id,
						title='$title', 
						content='$content',
						published=$published,
						obtype='$obtype',
						city='$city',
						pubdate='$pubdate',
						pubdays='$pubdays'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql);		
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item');
		}
	}

	if($opt == 'delete_item'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];		
			$sql = "SELECT * FROM cms_board_items WHERE id = $id LIMIT 1";
			$result = dbQuery($sql) ;			
			if (mysql_num_rows($result)){			
				$f = mysql_fetch_assoc($result);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/'.$f['file']);	
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/small/'.$f['file']);	
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/medium/'.$f['file']);					
				$sql = "DELETE FROM cms_board_items WHERE id = $id";
				dbQuery($sql) ;		
				header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
			}
		}
	}


	if ($opt == 'config') {

		if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
			
		echo '<div style="padding:10px">';

		echo '<table border="0" cellpadding="0" cellspacing="0"><tr>';		
			echo '<td width="200" valign="top">';
				$cats_total = dbRowsCount('cms_board_cats', 'id>0');
				$cats_pub = dbRowsCount('cms_board_cats', 'published=1');
				echo '<div><strong><a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats">������:</a></strong> '.$cats_total.'</div>';
				echo '<div>����������� ������: '.$cats_pub.'</div>';		
			echo '</td>';
			echo '<td width="200"  valign="top">';
				$items_total = dbRowsCount('cms_board_items', 'id>0');
				$items_pub = dbRowsCount('cms_board_items', 'published=1');
				$items_unpub = $items_total - $items_pub;
				echo '<div><strong><a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items">����������:</a></strong> '.$items_total.'</div>';
				echo '<div>����������� ����������: '.$items_pub.'</div>';	
			echo '</td>';	
		echo '</tr></table>';	
		
		if ($items_unpub) {
			echo '<div style="margin-top:10px;color:#FF3333" ><strong>���������������� ����������:</strong> '.$items_unpub.' - <a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items">��������</a></div>';
		}
		
		echo '</div>';
		
		//DEFAULT VALUES	
		if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
		if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
		if (!isset($cfg['maxcols'])) { $cfg['maxcols'] = 1; }
		if (!isset($cfg['public'])) { $cfg['public'] = 1; }
		if (!isset($cfg['srok'])) { $cfg['srok'] = 1; }
		if (!isset($cfg['pubdays'])) { $cfg['pubdays'] = 14; }
		if (!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
		?>
		<?php cpCheckWritable('/images/board', 'folder'); ?>
		<?php cpCheckWritable('/images/board/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/board/small', 'folder'); ?>				

        <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
            <table width="600" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td><strong>����������:</strong></td>
                    <td width="220">
                        <input name="photos" type="radio" value="1" <?php if (@$cfg['photos']) { echo 'checked="checked"'; } ?>/> ���������
                        <input name="photos" type="radio" value="0" <?php if (@!$cfg['photos']) { echo 'checked="checked"'; } ?>/>  ���������
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>�������� ������� ����:</strong><br />
                        <span class="hinttext">���� ��������, �� �� ��� ����������� ���������� ����� ��������� ����������� �� ����� &quot;<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>&quot;</span>
                    </td>
                    <td valign="top">
                        <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="watermark" type="radio" value="0" <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                <tr>
                    <td><strong>���������� ������� ��� ������ ������ ������: </strong></td>
                    <td width="250"><input name="maxcols" type="text" id="maxcols" size="5" value="<?php echo @$cfg['maxcols'];?>"/> ��</td>
                </tr>
                <tr>
                    <td>
                        <strong>���������� ���������� ��������������: </strong><br/>
                        <span class="hinttext">������������� ������� �� �������� ���� ������ ������������</span>
                    </td>
                    <td>
                        <select name="public" id="public" style="width:220px">
                                <option value="0" <?php if(@$cfg['public']=='0') { echo 'selected'; } ?>>���������</option>
                                <option value="1" <?php if(@$cfg['public']=='1') { echo 'selected'; } ?>>��������� � �������������</option>
                                <option value="2" <?php if(@$cfg['public']=='2') { echo 'selected'; } ?>>��������� ��� ���������</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>���� ���������� ���������������� ����������:</strong></td>
                    <td valign="top">
                        <div><input name="srok" type="radio" value="1" <?php if (@$cfg['srok']) { echo 'checked="checked"'; } ?>/>��������� �����</div>
                        <div><input name="srok" type="radio" value="0" <?php if (@!$cfg['srok']) { echo 'checked="checked"'; } ?>/>�������������: <input name="pubdays" type="text" id="pubdays" size="3" value="<?php echo @$cfg['pubdays'];?>"/> ����</div>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <div><strong>���� ����������:</strong></div>
                        <div class="hinttext">������ ��� � ����� ������</div>
                    </td>
                    <td valign="top">
                        <textarea name="obtypes" style="width:220px" rows="6"><?php echo @$cfg['obtypes'];?></textarea>
                    </td>
                </tr>
            </table>
            <p>
                <input name="opt" type="hidden" id="do" value="saveconfig" />
                <input name="save" type="submit" id="save" value="���������" />
                <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
            </p>
        </form>
		<?php
	}
	
	if ($opt == 'show_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_board_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_board_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}
	
	function reorder(){
		$sql = "SELECT * FROM cms_board_cats ORDER BY NSLeft";
		$rs = dbQuery($sql);
		if (mysql_num_rows($rs)){
			$level = array();
			while ($item = mysql_fetch_assoc($rs)){
				if (isset($level[$item['NSLevel']])){
					$level[$item['NSLevel']] += 1;
				} else {
					$level[] = 1;
				}
				dbQuery("UPDATE cms_board_cats SET ordering = ".$level[$item['NSLevel']]." WHERE id=".$item['id']);
			}				
		}
	}
	
	if ($opt == 'submit_cat'){	
		if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; }
		$description = $_REQUEST['description'];
		$published = $_REQUEST['published'];
		$showdate = $_REQUEST['showdate'];		
		$parent_id = $_REQUEST['parent_id'];

		$public = $_REQUEST['public'];
		$orderby = $_REQUEST['orderby'];
		$orderto = $_REQUEST['orderto'];
		$perpage = $_REQUEST['perpage'];
		$thumb1 = intval($_REQUEST['thumb1']);
		$thumb2 = intval($_REQUEST['thumb2']);
		$thumbsqr = $_REQUEST['thumbsqr'];
		$uplimit = $_REQUEST['uplimit'];
		$maxcols = $_REQUEST['maxcols'];
		$orderform = $_REQUEST['orderform'];		
	
		$ns = $inCore->nestedSetsInit('cms_board_cats');
		$myid = $ns->AddNode($parent_id);
		
		if ($myid){
			$sql = "UPDATE cms_board_cats
					SET title='$title', 
						description='$description', 
						published='$published', 
						showdate='$showdate', 
						pubdate=NOW(), 
						orderby='$orderby', 
						orderto='$orderto', 
						public='$public', 
						perpage='$perpage', 
						thumb1=$thumb1, 
						thumb2=$thumb2, 
						thumbsqr=$thumbsqr,
						uplimit='$uplimit', 
						maxcols='$maxcols', 
						orderform=$orderform
					WHERE id = $myid";
			dbQuery($sql) ;
		}
		reorder();
				
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "SELECT id, file FROM cms_board_items WHERE category_id = $id";
			$result = dbQuery($sql) ;
			//DELETE ALL PHOTOS IN ALBUM
			if (mysql_num_rows($result)){
				while($photo = mysql_fetch_assoc($result)){
					dbQuery("DELETE FROM cms_board_items WHERE id = ".$photo['id']) ;			
					@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/'.$photo['file']);	
					@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/medium/'.$photo['file']);	
					@unlink($_SERVER['DOCUMENT_ROOT'].'/images/board/small/'.$photo['file']);	
				}			
			}
			//DELETE ALBUM
			dbDeleteNS('cms_board_cats', $id);	
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("��������� �������� �� ����� ���� ������!"); }
			$description = $_REQUEST['description'];
			$published = $_REQUEST['published'];
			$showdate = $_REQUEST['showdate'];		
			$parent_id = $_REQUEST['parent_id'];	
				
			$public = $_REQUEST['public'];
			$orderby = $_REQUEST['orderby'];
			$orderto = $_REQUEST['orderto'];
			$perpage = $_REQUEST['perpage'];
			$thumb1 = intval($_REQUEST['thumb1']);
			$thumb2 = intval($_REQUEST['thumb2']);
			$thumbsqr = $_REQUEST['thumbsqr'];
			$uplimit = $_REQUEST['uplimit'];
			$maxcols = $_REQUEST['maxcols'];
			$orderform = $_REQUEST['orderform'];		
								
			$ns = $inCore->nestedSetsInit('cms_board_cats');
			$ns->MoveNode($id, $parent_id);									
								
			$sql = "UPDATE cms_board_cats
					SET title='$title', 
						description='$description',
						published=$published,
						showdate=$showdate,
						public='$public',
						orderby='$orderby',
						orderto='$orderto',
						perpage='$perpage',
						thumb1='$thumb1',
						thumb2='$thumb2',
						thumbsqr=$thumbsqr,
						uplimit=$uplimit,
						maxcols=$maxcols,
						orderform=$orderform
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql);							
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		}
	}
	
	if ($opt == 'list_cats'){
		cpAddPathway('�������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>�������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';
		
		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_cat'; //����� ������ 'do=hide&id=1' ���� 'opt=hide_cat&item_id=1'

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������ � �������� ����� ������� ��� ����������. ������� �������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_board_cats', $fields, $actions, 'parent_id>0', 'NSLeft');		
	}

	if ($opt == 'list_items'){
		cpAddPathway('����������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		echo '<h3>����������</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '����';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '80';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title'] = '���������';	$fields[2]['field'] = 'title';		$fields[2]['width'] = '';
		$fields[2]['filter'] = 15;
		$fields[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$fields[3]['title'] = '�����';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';
		$fields[3]['do'] = 'opt'; $fields[3]['do_suffix'] = '_item';

		$fields[4]['title'] = '����������';	$fields[4]['field'] = 'hits';		$fields[4]['width'] = '90';
		
		$fields[5]['title'] = '�������';		$fields[5]['field'] = 'category_id';	$fields[5]['width'] = '250';
		$fields[5]['prc'] = 'cpBoardCatById';  $fields[5]['filter'] = 1;  $fields[5]['filterlist'] = cpGetList('cms_board_cats');
	
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� ����������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_board_items', $fields, $actions, '', 'pubdate DESC');		
	}
	
	if ($opt == 'add_cat' || $opt == 'edit_cat'){
		if ($opt=='add_cat'){
			 echo '<h3>�������� �������</h3>';
		} else {
					 if(isset($_REQUEST['item_id'])){
						 $id = $_REQUEST['item_id'];
						 $sql = "SELECT * FROM cms_board_cats WHERE id = $id LIMIT 1";
						 $result = dbQuery($sql) ;
						 if (mysql_num_rows($result)){
							$mod = mysql_fetch_assoc($result);
							
						 }
					 }
					
					 echo '<h3>������������� �������</h3>';
			   }

		//DEFAULT VALUES
		if (!isset($mod['thumb1'])) { $mod['thumb1'] = 64; }
		if (!isset($mod['thumb2'])) { $mod['thumb2'] = 400; }
		if (!isset($mod['thumbsqr'])) { $mod['thumbsqr'] = 0; }
		if (!isset($mod['maxcols'])) { $mod['maxcols'] = 1; }
		if (!isset($mod['perpage'])) { $mod['perpage'] = '20'; }		
		if (!isset($mod['uplimit'])) { $mod['uplimit'] = 10; }		
		if (!isset($mod['public'])) { $mod['public'] = -1; }		
		if (!isset($mod['published'])) { $mod['published'] = 1; }	
		if (!isset($mod['showdate'])) { $mod['showdate'] = 1; }		
		if (!isset($mod['orderform'])) { $mod['orderform'] = 1; }
		if (!isset($mod['orderby'])) { $mod['orderby'] = 'pubdate'; }		
		if (!isset($mod['orderto'])) { $mod['orderto'] = 'desc'; }				
		?>
		
		<form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>">
			<table width="610" border="0" cellpadding="0" cellspacing="10" class="proptable">
			  <tr>
				<td><strong>�������� �������: </strong></td>
				<td width="250"><input name="title" type="text" id="title" style="width:250px" value="<?php echo @$mod['title'];?>"/></td>
			  </tr>
			  <tr>
                <td valign="top"><strong>������������ �������:</strong></td>
			    <td valign="top"><select name="parent_id" id="parent_id" style="width:250px">
                    <?php  //FIND BOARD ROOT
                        $rootid = dbGetField('cms_board_cats', 'parent_id=0', 'id');
                    ?>
                    <option value="<?php echo $rootid?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- �������� ������� --</option>
                    <?php
                        if (isset($mod['parent_id'])){
                            echo $inCore->getListItemsNS('cms_board_cats', $mod['parent_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_board_cats');
                        }
					?>
                </select></td>
		      </tr>
			  <tr>
				<td><strong>����������� �������?</strong></td>
				<td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				  ��
				  <label>
			  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
					���</label></td>
			  </tr>
			  <tr>
				<td><strong>���������� ����? </strong></td>
				<td><input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> />
					��
				  <label>
				  <input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> />
					���</label></td>
			  </tr>
			  <tr>
			    <td><strong>����������� ���������: </strong></td>
			    <td><select name="orderby" id="orderby" style="width:250px">
                  <option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>�� ��������</option>
                  <option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>�� ����</option>
                  <option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>�� ����������</option>
                  <option value="obtype" <?php if(@$mod['orderby']=='obtype') { echo 'selected'; } ?>>�� ����</option>
                  <option value="user_id" <?php if(@$mod['orderby']=='user_id') { echo 'selected'; } ?>>�� ������</option>
                </select>
                  <select name="orderto" id="orderto" style="width:250px">
                    <option value="desc" <?php if(@$mod['orderto']=='desc') { echo 'selected'; } ?>>�� ��������</option>
                    <option value="asc" <?php if(@$mod['orderto']=='asc') { echo 'selected'; } ?>>�� �����������</option>
                  </select></td>
		      </tr>
			  <tr>
                <td><strong>����� ����������: </strong></td>
			    <td><input name="orderform" type="radio" value="1" checked="checked" <?php if (@$mod['orderform']) { echo 'checked="checked"'; } ?> />
			      ��������
			      <label>
      <input name="orderform" type="radio" value="0"  <?php if (@!$mod['orderform']) { echo 'checked="checked"'; } ?> />
			        ������ </label></td>
		      </tr>
			  <tr>
			    <td><strong>����� ������� ��� ������:</strong></td>
			    <td><input name="maxcols" type="text" id="maxcols" size="5" value="<?php echo @$mod['maxcols'];?>"/>
��</td>
		      </tr>
			  <tr>
			    <td><strong>���������� ���������� ��������������: </strong></td>
			    <td><select name="public" id="select" style="width:250px">
					  <option value="0" <?php if(@$mod['public']=='0') { echo 'selected'; } ?>>���������</option>
					  <option value="1" <?php if(@$mod['public']=='1') { echo 'selected'; } ?>>��������� � �������������</option>
					  <option value="2" <?php if(@$mod['public']=='2') { echo 'selected'; } ?>>��������� ��� ���������</option>
					  <option value="-1" <?php if(@$mod['public']=='-1') { echo 'selected'; } ?>>��-���������</option>
                  </select></td>
		      </tr>
			  <tr>
			    <td><strong>�������� ���������� </strong> <br />
		        <span class="hinttext">�� ������ ������������ � �����</span></td>
			    <td><input name="uplimit" type="text" id="uplimit" size="5" value="<?php echo @$mod['uplimit'];?>"/>
��</td>
		      </tr>
			  <tr>
			    <td><strong>���������� �� ��������: </strong></td>
			    <td><input name="perpage" type="text" id="perpage" size="5" value="<?php echo @$mod['perpage'];?>"/>
		        ��</td>
		      </tr>
			  <tr>
                <td><p><strong>���������� � �����������: </strong></p>                </td>
			    <td><input name="is_photos" type="radio" value="1" checked="checked" <?php if (@$mod['is_photos']) { echo 'checked="checked"'; } ?> />
			      ��
			        <label>
      <input name="is_photos" type="radio" value="0"  <?php if (@!$mod['is_photos']) { echo 'checked="checked"'; } ?> />
			        ��� </label></td>
		      </tr>
			  <tr>
			    <td><strong>������ ��������� ����� ����: </strong><br/><span class="hinttext">� ��������</span></td>
			    <td><table border="0" cellspacing="0" cellpadding="1">
                  <tr>
                    <td width="60" valign="middle"><input name="thumb1" type="text" id="thumb1" size="5" value="<?php echo @$mod['thumb1'];?>"/></td>
                    <td width="100" align="center" valign="middle" style="background-color:#EBEBEB">����������:</td>
                    <td width="115" align="center" valign="middle" style="background-color:#EBEBEB"><input name="thumbsqr" type="radio" value="1" checked="checked" <?php if (@$mod['thumbsqr']) { echo 'checked="checked"'; } ?> />��  
                      <label>  
					<input name="thumbsqr" type="radio" value="0"  <?php if (@!$mod['thumbsqr']) { echo 'checked="checked"'; } ?> />���</label></td>
                  </tr>
                </table></td>
		      </tr>
			  <tr>
			    <td><strong>������ ������� ����� ����: </strong><br/><span class="hinttext">� ��������</span></td>
			    <td><input name="thumb2" type="text" id="thumb2" size="5" value="<?php echo @$mod['thumb2'];?>"/></td>
		      </tr>
		  </table>
			<table width="100%" border="0">
			  <tr>
			  	<h3>�������� �������</h3>
				<textarea name="description" style="width:580px" rows="4"><?php echo @$mod['description']?></textarea>
			  </tr>
			</table>
			<p>
			  <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
			  <label>
			  <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_cat') { echo 'value="������� ������"'; } else { echo 'value="��������� ������"'; } ?> />
			  </label>
			  <label>
			  <input name="back2" type="button" id="back2" value="������" onclick="window.location.href='index.php?view=components';"/>
			  </label>
			  <?php
				if ($opt=='edit_cat'){
				 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
				}
			  ?>
			</p>
</form>
		<?php
	}

	if ($opt == 'add_item' || $opt == 'edit_item'){	
			
		if ($opt=='add_item'){
			 echo '<h3>�������� ����������</h3>';
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
		
		
					 $sql = "SELECT * FROM cms_board_items WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('����������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);		
						
					 $mod['title'] = str_replace($mod['obtype'].' ', '', $mod['title']); 
					
			}

		//DEFAULT VALUES
		if (!isset($mod['city'])) { $mod['city'] = dbGetField('cms_user_profiles', 'id='.$inUser->id, 'city'); }
		if (!isset($mod['published'])) { $mod['published'] = 1; }	
		if (!isset($mod['pubdays'])) { $mod['pubdays'] = 14; }		
		
		?>
		<?php cpCheckWritable('/images/board', 'folder'); ?>
		<?php cpCheckWritable('/images/board/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/board/small', 'folder'); ?>			
			
		<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
				<table width="600" border="0" cellspacing="5" class="proptable">
				  <tr>
					<td width="177"><strong>��������� ����������: </strong></td>
					<td width="311">
					  <select name="obtype" id="obtype" style="width:120px">
					  	<?php echo $inCore->boardTypesList($mod['obtype']); ?>
				      </select>
				    <input name="title" type="text" id="title" size="30" value="<?php echo @$mod['title'];?>"/></td>
				  </tr>
				  <tr>
                    <td valign="top"><strong>�������:</strong></td>
				    <td valign="top"><select name="category_id" size="8" id="category_id" style="width:330px">
                      <?php  //FIND BOARD ROOT
			$rootid = dbGetField('cms_board_cats', 'parent_id=0', 'id');
		?>
                      <?php if (isset($mod['category_id'])){ 
								echo $inCore->getListItemsNS('cms_board_cats', $mod['category_id']);
						  } else {
						  		echo $inCore->getListItemsNS('cms_board_cats');
						  }								
					?>
                    </select></td>
			      </tr>
				  <tr>
				    <td><strong>�����:</strong></td>
				    <td><input name="city" type="text" id="city" size="30" value="<?php echo @$mod['city'];?>"/></td>
			      </tr>
					<?php if ($do=='add_item'){ ?>
				  <?php } ?>				  
				</table>
				<table width="600" border="0" cellspacing="5" class="proptable">
				  <tr>
				  	<td>
						<strong>������ ����� ����������:</strong>
					</td>
				  </tr>
				  <tr>
					<?php
					if(!isset($mod['user']) || @$mod['user']==1){
						echo '<td width="52%" valign="top">';

                        $inCore->insertEditor('content', $mod['content'], '250', '590');
						
						echo '</td>';
					}
					?>
				  </tr>
				</table>
				<table width="600" border="0" cellspacing="5" class="proptable">
                  <?php if ($opt=='add_item') { ?>
				  <tr>
                    <td width="177"><strong>���� ����������: </strong></td>
                    <td width="311"><?php if (@$mod['file']) { 
							echo '<div><img src="/images/photos/small/'.$mod['file'].'" border="1" /></div>';
							echo '<div><a href="/images/photos/'.$mod['file'].'" title="���������� ����">'.$mod['file'].'</a></div>'; 
						} else { ?>
                        <input name="picture" type="file" id="picture" size="30" />
                        <?php } ?></td>
                  </tr>
				  <?php } ?>
                  <tr>
                    <td><strong>����������� ����������?</strong></td>
                    <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
                      ��
                      <label>
        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                        ���</label></td>
                  </tr>
                  <tr>
                    <td valign="top"><strong>���� ����������: </strong></td>
                    <td valign="top"><input name="pubdate" type="text" id="pubdate" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
                        <?php 
					//include javascript
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/date_ru_win1251.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/datepicker.js"></script>';		
					$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/datepicker/datepicker.css" rel="stylesheet" type="text/css" />';
					if (@!$mod['pubdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['pubdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                        <input type="hidden" name="olddate" value="<?php echo @$mod['pubdate']?>"/>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>���� ����������: </strong></td>
                    <td><input name="pubdays" type="text" id="pubdays" size="5" value="<?php echo @$mod['pubdays'];?>"/>
                      ����</td>
                  </tr>

                </table>
				<p>
				  <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="�������"'; } else { echo 'value="���������"'; } ?> />
				  <input name="back3" type="button" id="back3" value="������" onclick="window.location.href='index.php?view=components';"/>
				  <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
				  <?php
					if ($opt=='edit_item'){
					 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
					}
				  ?>
				</p>
</form>
	 <?php	
	}
	
	
		
?>