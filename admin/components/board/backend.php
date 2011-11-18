<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    function uploadCategoryIcon($file='') {

        $inCore = cmsCore::getInstance();

		// ��������� ����� �������� ����
		$inCore->loadClass('upload_photo');
		$inUploadPhoto = cmsUploadPhoto::getInstance();
		// ���������� ���������������� ���������
		$inUploadPhoto->upload_dir    = PATH.'/upload/board/';
		$inUploadPhoto->dir_medium    = 'cat_icons/';
		$inUploadPhoto->medium_size_w = 32;
		$inUploadPhoto->medium_size_h = 32;
		$inUploadPhoto->only_medium   = true;
		$inUploadPhoto->is_watermark  = false;
		// ������� �������� ����
		$files = $inUploadPhoto->uploadPhoto($file);

		$icon = $files['filename'] ? $files['filename'] : $file;

        return $icon;

    }

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('board');
    $inCore->loadModel('board');
    $model = new cms_model_board();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

	cpAddPathway('����� ����������', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>����� ����������</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $inCore->request('opt', 'str'); } else { $opt = 'list_items'; }

    $inUser = cmsUser::getInstance();
	$inDB   = cmsDatabase::getInstance();

	$toolmenu = array();

	if($opt=='saveconfig'){	

        $cfg = array();
		$cfg['maxcols']   = $inCore->request('maxcols', 'int');
		$cfg['obtypes']   = $inCore->request('obtypes', 'html');
		$cfg['showlat']   = $inCore->request('showlat', 'str');
		$cfg['public']    = $inCore->request('public', 'int');
		$cfg['photos']    = $inCore->request('photos', 'int');
		$cfg['srok']      = $inCore->request('srok', 'int');
		$cfg['pubdays']   = $inCore->request('pubdays', 'int');
		$cfg['watermark'] = $inCore->request('watermark', 'int');
		$cfg['aftertime'] = $inCore->request('aftertime', 'str');
		$cfg['comments']  = $inCore->request('comments', 'int');
		$cfg['extend']    = $inCore->request('extend', 'int');
		$cfg['auto_link'] = $inCore->request('auto_link', 'int');
		$cfg['vip_enabled']    = $inCore->request('vip_enabled', 'int', 0);
		$cfg['vip_prolong']    = $inCore->request('vip_prolong', 'int', 0);
		$cfg['vip_max_days']   = $inCore->request('vip_max_days', 'int', 30);
		$cfg['vip_day_cost']   = $inCore->request('vip_day_cost', 'str', 5);
		$cfg['home_perpage']   = $inCore->request('home_perpage', 'int', 15);

        $cfg['vip_day_cost'] = str_replace(',', '.', trim($cfg['vip_day_cost']));
        
		$inCore->saveComponentConfig('board', $cfg);
		
		$msg = '��������� ���������.';
		$opt = 'config';

	}

	if ($opt=='list_items' || $opt=='list_cats' || $opt=='config' || $opt == 'show_item' || $opt == 'hide_item'){

		$toolmenu[0]['icon'] = 'newstuff.gif';
		$toolmenu[0]['title'] = '����� ����������';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=add_item';
	
		$toolmenu[1]['icon'] = 'newfolder.gif';
		$toolmenu[1]['title'] = '����� �������';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=add_cat';
	
		$toolmenu[2]['icon'] = 'liststuff.gif';
		$toolmenu[2]['title'] = '��� ����������';
		$toolmenu[2]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items';

        $toolmenu[3]['icon'] = 'folders.gif';
		$toolmenu[3]['title'] = '��� �������';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats';

	}
	if($opt=='list_items' || $opt == 'show_item' || $opt == 'hide_item'){

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = '������������� ���������';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=edit_item&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = '����������� ���������';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=show_item&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = '������ ���������';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".(int)$_REQUEST['id']."&opt=hide_item&multiple=1');";

	}

	if ($opt=='add_cat' || $opt=='add_item' || $opt=='edit_item' || $opt=='edit_cat'){

		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = '���������';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = '������';
		$toolmenu[21]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'];
	
	} else {

        if ($opt!='config'){
            $toolmenu[40]['icon'] = 'config.gif';
            $toolmenu[40]['title'] = '���������';
            $toolmenu[40]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=config';

        }

    }

	cpToolMenu($toolmenu);

	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_board_items', (int)$_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_board_items', $_REQUEST['item']);				
			$opt = 'list_items';			
		}			
	}

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_board_items', (int)$_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_board_items', $_REQUEST['item']);				
			$opt = 'list_items';			
		}			
	}

	if ($opt == 'submit_item'){	
	
			if (!empty($_REQUEST['category_id'])) { $category_id = $inCore->request('category_id', 'int'); } else { $category_id = 1; }
			if (!empty($_REQUEST['title'])) { $title = $inCore->request('title', 'str'); } else { $title = '����������'; }
			$content   = $inCore->request('content', 'str');
			$published = $inCore->request('published', 'int');
			$city      = $inCore->request('city', 'str');
			$obtype    = $inCore->request('obtype', 'str');

			$title     = str_ireplace($obtype, '', $title);

			$pubdays   = $inCore->request('pubdays', 'int');	

			$pubdate   = $inCore->request('pubdate', 'str');
			$pubdate   = explode('.', $pubdate);
			$pubdate   = $pubdate[2] . '-' . $pubdate[1] . '-' . $pubdate[0];
					
			$user_id = $inUser->id;
												
			if (isset($_FILES['picture']['tmp_name'])){
				if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
						$uploaddir = PATH.'/images/board/';		
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
			$sql = "INSERT INTO cms_board_items (category_id, user_id, obtype, title , content, city, pubdate, pubdays, published, file, ip)
					VALUES ('$category_id', '$user_id', '$obtype', '$title', '$content', '$city', '$pubdate', '$pubdays', '$published', '$filename', INET_ATON('{$inUser->ip}'))";	
			dbQuery($sql) or die(mysql_error().'<pre>'.$sql.'</pre>');
										
			header('location:?view=components&do=config&opt=list_items&id='.(int)$_REQUEST['id']);		
	}	  

	if ($opt == 'update_item'){
		if(isset($_REQUEST['item_id'])) { 
			
			$id = (int)$_REQUEST['item_id'];

			if (!empty($_REQUEST['category_id'])) { $category_id = $inCore->request('category_id', 'int'); } else { $category_id = 1; }
			if (!empty($_REQUEST['title'])) { $title = $inCore->request('title', 'str'); } else { $title = '����������'; }
			$content   = $inCore->request('content', 'str');
			$published = $inCore->request('published', 'int');
			$city      = $inCore->request('city', 'str');
			$obtype    = $inCore->request('obtype', 'str');

			$pubdays   = $inCore->request('pubdays', 'int');
			
			$title     = str_ireplace($obtype, '', $title);
			
			$pubdate = $inCore->request('pubdate', 'str');
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
			header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
		} else {
			header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item');
		}
	}

	if($opt == 'delete_item'){
		if(isset($_REQUEST['item_id'])) { 
			$id = (int)$_REQUEST['item_id'];		
			$model->deleteRecord($id);				
			header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
		}
	}


	if ($opt == 'config') {

		if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
			
        $items_total = $inDB->rows_count('cms_board_items', 'id>0');
        $items_pub = $inDB->rows_count('cms_board_items', 'published=1');
        $items_unpub = $items_total - $items_pub;

		if ($items_unpub) {
			echo '<div style="margin-top:10px;color:#FF3333" ><strong>���������������� ����������:</strong> '.$items_unpub.' - <a href="index.php?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items">��������</a></div>';
		}
		
        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
        $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
        $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';

		//DEFAULT VALUES	
		if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
		if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
		if (!isset($cfg['maxcols'])) { $cfg['maxcols'] = 1; }
		if (!isset($cfg['public'])) { $cfg['public'] = 1; }
		if (!isset($cfg['srok'])) { $cfg['srok'] = 1; }
		if (!isset($cfg['comments'])) { $cfg['comments'] = 1; }
		if (!isset($cfg['pubdays'])) { $cfg['pubdays'] = 14; }
		if (!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
		if (!isset($cfg['aftertime'])) { $cfg['aftertime'] = ''; }
		if (!isset($cfg['extend'])) { $cfg['extend'] = 0; }
		if (!isset($cfg['vip_enabled'])) { $cfg['vip_enabled'] = 0; }
		if (!isset($cfg['vip_prolong'])) { $cfg['vip_prolong'] = 0; }
		if (!isset($cfg['vip_max_days'])) { $cfg['vip_max_days'] = 30; }
		if (!isset($cfg['vip_day_cost'])) { $cfg['vip_day_cost'] = 5; }
		if (!isset($cfg['home_perpage'])) { $cfg['home_perpage'] = 15; }
        
		?>
		<?php cpCheckWritable('/images/board', 'folder'); ?>
		<?php cpCheckWritable('/images/board/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/board/small', 'folder'); ?>				

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">

    <div id="config_tabs" style="margin-top:12px;">

        <ul id="tabs">
            <li><a href="#basic"><span>�����</span></a></li>
            <li><a href="#access"><span>������</span></a></li>
            <li><a href="#types"><span>����</span></a></li>
            <li><a href="#vip"><span>VIP</span></a></li>
        </ul>

        <div id="basic">
            <table width="600" border="0" cellpadding="0" cellspacing="10" class="proptable" style="border:none">
                <tr>
                    <td><strong>��������� ����������:</strong></td>
                    <td width="250">
                        <input name="photos" type="radio" value="1" <?php if (@$cfg['photos']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="photos" type="radio" value="0" <?php if (@!$cfg['photos']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>�������� ������� ����:</strong>
                    </td>
                    <td valign="top">
                        <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="watermark" type="radio" value="0" <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>����������� ��� ����������:</strong>
                    </td>
                    <td valign="top">
                        <input name="comments" type="radio" value="1" <?php if (@$cfg['comments']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="comments" type="radio" value="0" <?php if (@!$cfg['comments']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                <tr>
                    <td><strong>���������� ���������� �� ������� �������� ����������: </strong></td>
                    <td width="250"><input name="home_perpage" type="text" id="home_perpage" size="5" value="<?php echo @$cfg['home_perpage'];?>"/> ��</td>
                </tr>
                <tr>
                    <td><strong>���������� ������� ��� ������ ������ ������: </strong></td>
                    <td width="250"><input name="maxcols" type="text" id="maxcols" size="5" value="<?php echo @$cfg['maxcols'];?>"/> ��</td>
                </tr>
                <tr>
                    <td><strong>�������� ����������:</strong></td>
                    <td width="250">
                        <input name="auto_link" type="radio" value="1" <?php if (@$cfg['auto_link']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="auto_link" type="radio" value="0" <?php if (@!$cfg['auto_link']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
            </table>
        </div>

        <div id="access">
            <table width="600" border="0" cellpadding="0" cellspacing="10" class="proptable" style="border:none">
                <tr>
                    <td width="250">
                        <strong>���������� ����������: </strong><br/>
                        <span class="hinttext">������������� ������� �� ��������<br/>���� ������ ������������</span>
                    </td>
                    <td valign="top">
                        <select name="public" id="public" style="width:260px">
                                <option value="0" <?php if(@$cfg['public']=='0') { echo 'selected'; } ?>>���������</option>
                                <option value="1" <?php if(@$cfg['public']=='1') { echo 'selected'; } ?>>��������� � �������������</option>
                                <option value="2" <?php if(@$cfg['public']=='2') { echo 'selected'; } ?>>��������� ��� ���������</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>���� ���������� ����������:</strong></td>
                    <td valign="top">
                        <div><input name="srok" type="radio" value="1" <?php if (@$cfg['srok']) { echo 'checked="checked"'; } ?>/>��������� �����</div>
                        <div><input name="srok" type="radio" value="0" <?php if (@!$cfg['srok']) { echo 'checked="checked"'; } ?>/>�������������: <input name="pubdays" type="text" id="pubdays" size="3" value="<?php echo @$cfg['pubdays'];?>"/> ����</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>������������ ����������: </strong><br/>
                        <span class="hinttext">�������� ��������</span>
                    </td>
                    <td valign="top">
                        <select name="aftertime" id="aftertime" style="width:260px">
                                <option value="delete" <?php if(@$cfg['aftertime']=='delete') { echo 'selected'; } ?>>�������</option>
                                <option value="hide" <?php if(@$cfg['aftertime']=='hide') { echo 'selected'; } ?>>��������</option>
                                <option value="" <?php if(@$cfg['aftertime']=='') { echo 'selected'; } ?>>������ �� ������</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>��������� ������������� ���������� ����� ������ ����������: </strong><br/>
                        <span class="hinttext">��������, ���� ������� ����� "��������" ��� ������������ ����������.</span>
                    </td>
                    <td valign="top">
                        <input name="extend" type="radio" value="1" <?php if (@$cfg['extend']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="extend" type="radio" value="0" <?php if (@!$cfg['extend']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
            </table>
        </div>

        <div id="types">
            <table width="600" border="0" cellpadding="0" cellspacing="10" class="proptable" style="border:none">
                <tr>
                    <td width="250" valign="top">
                        <div><strong>���� ����������:</strong></div>
                        <div class="hinttext">������ ��� � ����� ������</div>
                        <div class="hinttext">�� ������ ������ ������ ����<br/>� ���������� ������ �������</div>
                    </td>
                    <td valign="top">
                        <textarea name="obtypes" style="width:250px" rows="10"><?php echo @$cfg['obtypes'];?></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div id="vip">
            <?php if (!IS_BILLING){ ?>
                <p>
                    ��� ��������� VIP-���������� ��������� ��������� &laquo;<a href="http://www.instantcms.ru/billing/about.html">������� �������������</a>&raquo;
                </p>
                <p>
                    ������������ ������ �������� VIP-������� ��� ����� ����������.<br/>
                    VIP-���������� ���������� ������, ������ ��������� � ������ ������ <br/>
                    � ����� ���� �������� �� ������� � ��������� ������ &laquo;VIP-����������&raquo;
                </p>
                <p>
                    ��� ���������� &laquo;<a href="http://www.instantcms.ru/billing/about.html">������� �������������</a>&raquo; VIP-������� ��� ����������<br/>
                    ����� ������������� ������ �������������, ��� �������� ��� �������������� <br/>
                    ������ ���������� �� �����.
                </p>
            <?php } else { ?>
                <table width="550" border="0" cellpadding="0" cellspacing="10" class="proptable" style="border:none">
                    <tr>
                        <td><strong>��������� VIP-����������:</strong></td>
                        <td width="250">
                            <input name="vip_enabled" type="radio" value="1" <?php if (@$cfg['vip_enabled']) { echo 'checked="checked"'; } ?>/> ��
                            <input name="vip_enabled" type="radio" value="0" <?php if (@!$cfg['vip_enabled']) { echo 'checked="checked"'; } ?>/> ���
                        </td>
                    </tr>
                    <tr>
                        <td><strong>��������� ���������� VIP-������:</strong></td>
                        <td width="250">
                            <input name="vip_prolong" type="radio" value="1" <?php if (@$cfg['vip_prolong']) { echo 'checked="checked"'; } ?>/> ��
                            <input name="vip_prolong" type="radio" value="0" <?php if (@!$cfg['vip_prolong']) { echo 'checked="checked"'; } ?>/> ���
                        </td>
                    </tr>
                    <tr>
                        <td><strong>������������ ���� VIP-�������: </strong></td>
                        <td width="250">
                            <input name="vip_max_days" type="text" id="vip_max_days" size="5" value="<?php echo @$cfg['vip_max_days'];?>"/> ����
                        </td>
                    </tr>
                    <tr>
                        <td><strong>��������� VIP-�������: </strong></td>
                        <td width="250">
                            <input name="vip_day_cost" type="text" id="vip_day_cost" size="5" value="<?php echo @$cfg['vip_day_cost'];?>"/> ������ �� 1 ����
                        </td>
                    </tr>
                </table>
            <?php } ?>
        </div>
    </div>
    
    <script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>

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
			$id = (int)$_REQUEST['item_id'];
			$sql = "UPDATE cms_board_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = (int)$_REQUEST['item_id'];
			$sql = "UPDATE cms_board_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}
	
	if ($opt == 'submit_cat'){	
		$title = $inCore->request('title', 'str', '������� ��� ��������');
		$description = $inCore->request('description', 'str');
		$published   = $inCore->request('published', 'int');
		$showdate    = $inCore->request('showdate', 'int');
		$parent_id   = $inCore->request('parent_id', 'int');

		$public      = $inCore->request('public', 'str');
		$orderby     = $inCore->request('orderby', 'str');
		$orderto     = $inCore->request('orderto', 'str');
		$perpage     = $inCore->request('perpage', 'int');
		$is_photos   = $inCore->request('is_photos', 'int');
		$thumb1      = $inCore->request('thumb1', 'int');
		$thumb2      = $inCore->request('thumb2', 'int');
		$thumbsqr    = $inCore->request('thumbsqr', 'int');
		$uplimit     = $inCore->request('uplimit', 'int');
		$maxcols     = $inCore->request('maxcols', 'int');
		$orderform   = $inCore->request('orderform', 'int');
		$form_id     = $inCore->request('form_id', 'int');	
	
        $obtypes     = $inCore->request('obtypes', 'str', '');

		$icon = uploadCategoryIcon();

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
						thumb1='$thumb1', 
						thumb2='$thumb2', 
						thumbsqr=$thumbsqr,
						uplimit='$uplimit', 
						is_photos='$is_photos',
						maxcols='$maxcols', 
						orderform='$orderform',
						icon='$icon',
						form_id='$form_id',
                        obtypes='$obtypes'
					WHERE id = '$myid'";
			dbQuery($sql) ;
		}
				
		header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = (int)$_REQUEST['item_id'];
			$sql = "SELECT id, file FROM cms_board_items WHERE category_id = $id";
			$result = dbQuery($sql);
			//DELETE ALL PHOTOS IN ALBUM
			if (mysql_num_rows($result)){
				while($photo = mysql_fetch_assoc($result)){
					$model->deleteRecord($photo['id']);
				}			
			}
			$f_icon = $inDB->get_field('cms_board_cats', "id = '$id'", 'icon');
			//DELETE ALBUM
			dbDeleteNS('cms_board_cats', $id);
			if(file_exists(PATH.'/upload/board/cat_icons/'.$f_icon)){
				@chmod(PATH.'/upload/board/cat_icons/'.$f_icon, 0777);
				@unlink(PATH.'/upload/board/cat_icons/'.$f_icon);
			}
		}
		header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = (int)$_REQUEST['item_id'];

			 $sql = "SELECT * FROM cms_board_cats WHERE id = '$id' LIMIT 1";
			 $result = dbQuery($sql) ;
			 if (mysql_num_rows($result)){
				$mod = mysql_fetch_assoc($result);
				$mod['icon'] = $mod['icon'] == 'folder_grey.png' ? '' : $mod['icon'];
			 }

			$title = $inCore->request('title', 'str', '������� ��� ��������');
			$description = $inCore->request('description', 'str');
			$published   = $inCore->request('published', 'int');
			$showdate    = $inCore->request('showdate', 'int');
			$parent_id   = $inCore->request('parent_id', 'int');
	
			$public      = $inCore->request('public', 'str');
			$orderby     = $inCore->request('orderby', 'str');
			$orderto     = $inCore->request('orderto', 'str');
			$perpage     = $inCore->request('perpage', 'int');
			$is_photos   = $inCore->request('is_photos', 'int');
			$thumb1      = $inCore->request('thumb1', 'int');
			$thumb2      = $inCore->request('thumb2', 'int');
			$thumbsqr    = $inCore->request('thumbsqr', 'int');
			$uplimit     = $inCore->request('uplimit', 'int');
			$maxcols     = $inCore->request('maxcols', 'int');
			$orderform   = $inCore->request('orderform', 'int');
			$form_id     = $inCore->request('form_id', 'int');
		
			$obtypes     = $inCore->request('obtypes', 'str');
			$icon = uploadCategoryIcon($mod['icon']);
			$icon = $icon ? $icon : $mod['icon'];
			$ns = $inCore->nestedSetsInit('cms_board_cats');
			$ns->MoveNode($id, $parent_id);									
								
			$sql = "UPDATE cms_board_cats
					SET title='$title', 
						description='$description',
						published='$published',
						showdate='$showdate',
						public='$public',
						orderby='$orderby',
						orderto='$orderto',
						perpage='$perpage',
						thumb1='$thumb1',
						thumb2='$thumb2',
						thumbsqr='$thumbsqr',
						uplimit='$uplimit',
						is_photos='$is_photos',
						maxcols='$maxcols',
						orderform='$orderform',
						icon='$icon',
						form_id='$form_id',
                        obtypes='$obtypes'
					WHERE id = '$id'
					LIMIT 1";
			dbQuery($sql);							
			header('location:?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
		}
	}
	
	if ($opt == 'list_cats'){
		cpAddPathway('�������', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>�������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';
		
		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_cat'; //����� ������ 'do=hide&id=1' ���� 'opt=hide_cat&item_id=1'

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������ � �������� ����� ������� ��� ����������. ������� �������?';
		$actions[1]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_board_cats', $fields, $actions, 'parent_id>0', 'NSLeft');		
	}

	if ($opt == 'list_items'){
		cpAddPathway('����������', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
		echo '<h3>����������</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '����';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '80';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title'] = '���';		$fields[2]['field'] = 'obtype';		$fields[2]['width'] = '80';		$fields[2]['filter'] = 15;

		$fields[3]['title'] = '���������';	$fields[3]['field'] = 'title';		$fields[3]['width'] = '';
		$fields[3]['filter'] = 15;
		$fields[3]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$fields[4]['title'] = '�����';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '50';
		$fields[4]['do'] = 'opt'; $fields[4]['do_suffix'] = '_item';

		$fields[5]['title'] = '����������';	$fields[5]['field'] = 'hits';		$fields[5]['width'] = '80';

		$fields[6]['title'] = 'IP';	$fields[6]['field'] = 'ip';		$fields[6]['width'] = '80'; $fields[6]['prc'] = 'long2ip';

		$fields[7]['title'] = '�������';		$fields[7]['field'] = 'category_id';	$fields[7]['width'] = '230';
		$fields[7]['prc'] = 'cpBoardCatById';  $fields[7]['filter'] = 1;  $fields[7]['filterlist'] = cpGetList('cms_board_cats');
	
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� ����������?';
		$actions[1]['link']  = '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_board_items', $fields, $actions, '', 'pubdate DESC');		
	}
	
	if ($opt == 'add_cat' || $opt == 'edit_cat'){
		if ($opt=='add_cat'){
			cpAddPathway('�������� �������', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=add_cat');
			 echo '<h3>�������� �������</h3>';
		} else {
			 if(isset($_REQUEST['item_id'])){
				 $id = (int)$_REQUEST['item_id'];
				 $sql = "SELECT * FROM cms_board_cats WHERE id = $id LIMIT 1";
				 $result = dbQuery($sql) ;
				 if (mysql_num_rows($result)){
					$mod = mysql_fetch_assoc($result);
					
				 }
			 }
			
			 echo '<h3>������������� �������</h3>';
			 cpAddPathway('������������� ������� '.$mod['title'], '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_cat&item_id='.(int)$_REQUEST['item_id']);
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
		
		<form id="addform" name="addform" enctype="multipart/form-data" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>">
			<table width="610" border="0" cellpadding="0" cellspacing="10" class="proptable">
			  <tr>
				<td><strong>�������� �������: </strong></td>
				<td width="250"><input name="title" type="text" id="title" style="width:250px" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
			  </tr>
			  <tr>
                <td valign="top"><strong>������������ �������:</strong></td>
			    <td valign="top"><select name="parent_id" id="parent_id" style="width:250px">
                    <?php  //FIND BOARD ROOT
                        $rootid = $inDB->get_field('cms_board_cats', 'parent_id=0', 'id');
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
                    <td><strong>������ �������:</strong><br/>
                        <span class="hinttext">���� �������� 32px � ����� ����������� ����������</span></td>
                    <td valign="middle"> <?php if (@$mod['icon']) { ?><img src="/upload/board/cat_icons/<?php echo @$mod['icon'];?>" border="0" /><?php } ?> 
                        <input name="Filedata" type="file" style="width:215px; margin:0 0 0 5px; vertical-align:top" />
                    </td>
                </tr>
			  <tr>
				<td><strong>��������� �����</strong><br/>
                    <span class="hinttext">���� ����� ����� �������������� � ����������� ������ �������</span></td>
				<td>
                    <select name="form_id" style="width:250px">
                    	<option value="" <?php if (@!$mod['form_id']) { echo 'selected'; }?>>-- �� ����������� --</option>
                        <?php
                        $sql = "SELECT id, title FROM cms_forms";
                        $rs = dbQuery($sql);

                        if (mysql_num_rows($rs)){
                            while($f = mysql_fetch_assoc($rs)){
                                if ($f['id']==$mod['form_id']) { $selected='selected="selected"'; } else { $selected = ''; }
                                echo '<option value="'.$f['id'].'" '.$selected.'>'.$f['title'].'</option>';
                            }
                        }

                        ?>
                    </select>
                </td>
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
		        <span class="hinttext">�� ������ ������������ � �����, 0 ������������</span></td>
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
		      </tr
              ><tr>
                    <td valign="top">
                        <div><strong>���� ����������:</strong></div>
                        <div class="hinttext">������ ��� � ����� ������</div>
                        <div class="hinttext">���� �� �������, ����� ������� �� ������������ ���������</div>
                    </td>
                    <td valign="top">
                        <textarea name="obtypes" style="width:220px" rows="6"><?php echo @$mod['obtypes'];?></textarea>
                    </td>
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
			  <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_cat') { echo 'value="������� ���������"'; } else { echo 'value="��������� ���������"'; } ?> />
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
					 } else { $id = (int)$_REQUEST['item_id']; }
		
		
					 $sql = "SELECT * FROM cms_board_items WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('����������', '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.(int)$_REQUEST['id'].'&opt=edit_item&item_id='.$id);		
						
					 $mod['title'] = str_replace($mod['obtype'].' ', '', $mod['title']); 
					
			}

		//DEFAULT VALUES
		if (!isset($mod['city'])) { $mod['city'] = $inDB->get_field('cms_user_profiles', 'id='.$inUser->id, 'city'); }
		if (!isset($mod['published'])) { $mod['published'] = 1; }	
		if (!isset($mod['pubdays'])) { $mod['pubdays'] = 14; }		
		
		?>
		<?php cpCheckWritable('/images/board', 'folder'); ?>
		<?php cpCheckWritable('/images/board/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/board/small', 'folder'); ?>			
			
		<form action="index.php?view=components&amp;do=config&amp;id=<?php echo (int)$_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
				<table width="600" border="0" cellspacing="5" class="proptable">
				  <tr>
					<td width="177"><strong>��������� ����������: </strong></td>
					<td width="311">
					  <select name="obtype" id="obtype" style="width:120px">
					  	<?php echo $model->getTypesOptions($mod['obtype']); ?>
				      </select>
				    <input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
				  </tr>
				  <tr>
                    <td valign="top"><strong>�������:</strong></td>
				    <td valign="top"><select name="category_id" size="8" id="category_id" style="width:330px">
                      <?php  //FIND BOARD ROOT
			$rootid = $inDB->get_field('cms_board_cats', 'parent_id=0', 'id');
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
				    <td><input name="city" type="text" id="city" size="30" value="<?php echo htmlspecialchars($mod['city']);?>"/></td>
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