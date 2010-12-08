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

    function cpBlogOwner($blog_id){
        $inDB = cmsDatabase::getInstance();
        $blog = $inDB->get_fields('cms_blogs', "id={$blog_id}", 'owner, user_id');
        if($blog['owner']=='user'){
            $nickname = $inDB->get_field('cms_users', "id={$blog['user_id']}", 'nickname');
            $link = '<a href="?view=users&do=edit&id='.$blog['user_id'].'" class="user_link" target="_blank">
                     '.$nickname.'
                     </a>';
        } else {
            $title = $inDB->get_field('cms_clubs', "id={$blog['user_id']}", 'title');
            $link = '<a href="?view=components&do=config&link=clubs&opt=edit&item_id='.$blog['user_id'].'" class="club_link" target="_blank">
                     '.$title.'
                     </a>';
        }
        return $link;
    }

	cpAddPathway('�����', '?view=components&do=config&id='.(int)$_REQUEST['id']);
	
	echo '<h3>�����</h3>';

    $opt = $inCore->request('opt', 'str', 'config');

	$toolmenu = array();

    if ($opt=='config' || $opt=='saveconfig'){

        $toolmenu[0]['icon'] = 'save.gif';
        $toolmenu[0]['title'] = '���������';
        $toolmenu[0]['link'] = 'javascript:document.optform.submit();';

        $toolmenu[1]['icon'] = 'cancel.gif';
        $toolmenu[1]['title'] = '������';
        $toolmenu[1]['link'] = '?view=components';

        $toolmenu[2]['icon'] = 'listblogs.gif';
        $toolmenu[2]['title'] = '������ ������';
        $toolmenu[2]['link'] = '?view=components&do=config&link=blogs&opt=list_blogs';
        cpToolMenu($toolmenu);

    }

    if ($opt=='list_blogs'){

        cpAddPathway('������ ������', $_SERVER['REQUEST_URI']);

		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = '������������� ���������';
		$toolmenu[1]['link'] = "javascript:checkSel('?view=components&do=config&link=blogs&opt=edit_blog&multiple=1');";

		$toolmenu[2]['icon'] = 'delete.gif';
		$toolmenu[2]['title'] = '������� ���������';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=components&do=config&link=blogs&opt=delete_blog&multiple=1');";

        $toolmenu[3]['icon'] = 'config.gif';
        $toolmenu[3]['title'] = '��������� ����������';
        $toolmenu[3]['link'] = '?view=components&do=config&link=blogs&opt=config';
        cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';				$fields[0]['width'] = '30';

		$fields[1]['title'] = '������';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '80';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title']  = '��������';	$fields[2]['field'] = 'title';			$fields[2]['width'] = '';		$fields[2]['link'] = '?view=components&do=config&link=blogs&opt=edit_blog&item_id=%id%';
		$fields[2]['filter'] = 15;

		$fields[3]['title'] = '��������';		$fields[3]['field'] = 'id';         $fields[3]['width'] = '300';
		$fields[3]['prc']   = 'cpBlogOwner';

		//ACTIONS
		$actions = array();
		$actions[1]['title'] = '�������������';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&link=blogs&opt=edit_blog&item_id=%id%';

		$actions[2]['title'] = '�������';
		$actions[2]['icon']  = 'delete.gif';
		$actions[2]['confirm'] = '������� ����?';
		$actions[2]['link']  = '?view=components&do=config&link=blogs&opt=delete_blog&item_id=%id%';

		//Print table
		cpListTable('cms_blogs', $fields, $actions, '', 'pubdate DESC');

    }

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('blogs');

    $inCore->loadModel('blogs');
    $model = new cms_model_blogs();

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['perpage']             = $inCore->request('perpage', 'int');
		$cfg['perpage_blog'] 		= $inCore->request('perpage_blog', 'int');
		$cfg['update_date']         = $inCore->request('update_date', 'int');
		$cfg['update_seo_link']     = $inCore->request('update_seo_link', 'int');
		
		$cfg['min_karma_private'] 	= $inCore->request('min_karma_private', 'int');
		$cfg['min_karma_public'] 	= $inCore->request('min_karma_public', 'int');
		$cfg['min_karma'] 			= $inCore->request('min_karma', 'int');
		
		$cfg['watermark'] 			= $inCore->request('watermark', 'int');
		$cfg['img_on'] 				= $inCore->request('img_on', 'int');
		
		$cfg['rss_all']             = $inCore->request('rss_all', 'int');
		$cfg['rss_one']             = $inCore->request('rss_one', 'int');
		$cfg['j_code']              = $inCore->request('j_code', 'int');
			
		$inCore->saveComponentConfig('blogs', $cfg);
		
		$msg = '��������� ���������.';

        $opt = 'config';
	}

	if(!isset($cfg['j_code'])) { $cfg['j_code']=1;	}
	if(!isset($cfg['perpage_blog'])) { $cfg['perpage_blog']=15;	}
	if (!isset($cfg['min_karma_private'])) { $cfg['min_karma_private'] = 0; }
	if (!isset($cfg['min_karma_public'])) {	 $cfg['min_karma_public'] = 0; }
	if (!isset($cfg['min_karma'])) { 		 $cfg['min_karma'] = 0; 		}
	if (!isset($cfg['update_date'])) { 		 $cfg['update_date'] = 1; 		}
	if (!isset($cfg['update_seo_link'])) { 	 $cfg['update_seo_link'] = 0; 		}
	
	if (!isset($cfg['watermark'])) { 	 	$cfg['watermark'] = 1; 		}
	if (!isset($cfg['img_on'])) { 	 		$cfg['img_on'] = 1; 		}

	if (!isset($cfg['rss_all'])) { $cfg['rss_all'] = 1; }
	if (!isset($cfg['rss_one'])) { $cfg['rss_one'] = 1; }

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if ($opt == 'delete_blog'){
        $id = $inCore->request('item_id', 'int', 0);
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){
				$model->deleteBlog($id);
			}
		} else {
			$model->deleteBlogs($_REQUEST['item']);
		}
		header('location:?view=components&do=config&link=blogs&opt=list_blogs');
	}

	if ($opt == 'update_blog'){
		if($inCore->request('item_id', 'int', 0)) {

            $inDB = cmsDatabase::getInstance();

			$id                        = $inCore->request('item_id', 'int', 0);

            $blog                      = $inDB->get_fields('cms_blogs', "id={$id}", '*');

            $blog['title']             = $inCore->request('title', 'str');

			$model->updateBlog($id, $blog);

			if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
				header('location:?view=components&do=config&link=blogs&opt=list_blogs');
			} else {
				header('location:?view=components&do=config&link=blogs&opt=edit_blog');
			}
		}
	}


?>

<?php
    if ($opt=='config'){
?>
<form action="index.php?view=components&do=config&id=<?php echo (int)$_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>�������� ����� </h4></td>
        </tr>
        <tr>
            <td valign="top"><strong>������ �� �������� � �����: </strong></td>
            <td width="100" valign="top">
                <input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" size="5" /> ��.
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>���������� ������ �� �������� � ������ ������: </strong></td>
            <td width="100" valign="top">
                <input name="perpage_blog" type="text" id="perpage_blog" value="<?php echo @$cfg['perpage_blog'];?>" size="5" /> ��.
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>�������� ��������� ���������� ����: </strong></td>
            <td width="100" valign="top"><input name="j_code" type="radio" value="1" <?php if (@$cfg['j_code']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
      <input name="j_code" type="radio" value="0"  <?php if (!$cfg['j_code']) { echo 'checked="checked"'; } ?> />
              ���</label></td>
        </tr>
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>����� ����������</h4></td>
        </tr>
        <tr>
            <td valign="top"><strong>��������� �������� ���������� � ������ � �����:</strong></td>
            <td width="100" valign="top">
                <input name="img_on" type="radio" value="1" <?php if (@$cfg['img_on']) { echo 'checked="checked"'; } ?> /> ��
                <input name="img_on" type="radio" value="0" <?php if (@!$cfg['img_on']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>�������� ������� ����:</strong>  <br />���� ��������, �� �� ��� �����������
			      ���������� � ������ ����� ��������� ����������� 
			      �� ����� "<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>"</td>
            <td width="100" valign="top">
                <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?> /> ��
                <input name="watermark" type="radio" value="0" <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>

        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>��������� ��������������</h4></td>
        </tr>
        <tr>
            <td valign="top">
                <strong>��������� ���� ����� ����� ��������������:</strong><br />
                <span class="hinttext">
                    ���� ��������, ����� �������������� ����� ��� ���� ����� ��������������� � �������.
                </span>
            </td>
            <td valign="top">
                <input name="update_date" type="radio" value="1" <?php if (@$cfg['update_date']) { echo 'checked="checked"'; } ?> /> ��
                <input name="update_date" type="radio" value="0" <?php if (@!$cfg['update_date']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>��������� ������ ����� ����� �������������� ��� ����� ���������:</strong><br />
                <span class="hinttext">
                    ���� ��������, ����� �������������� ����� ��� ������ ����� �������� �������� ������ ���������.
                </span>
            </td>
            <td valign="top">
                <input name="update_seo_link" type="radio" value="1" <?php if (@$cfg['update_seo_link']) { echo 'checked="checked"'; } ?> /> ��
                <input name="update_seo_link" type="radio" value="0" <?php if (@!$cfg['update_seo_link']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>����������� �� �����</h4></td>
        </tr>

        <tr>
            <td valign="top">
                <strong>������������ �����������:</strong><br />
                <span class="hinttext">���� ���������, �� ����� ������������ ������ ������� ����,<br />���������� �� �������� ����� �����</span>
            </td>
            <td valign="top">
                <input name="min_karma" type="radio" value="1" <?php if (@$cfg['min_karma']) { echo 'checked="checked"'; } ?> /> ��
                <input name="min_karma" type="radio" value="0" <?php if (@!$cfg['min_karma']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>�������� ������� �����:</strong><br />
                <span class="hinttext">������� ����� ����� ����� ��� �������� ������� ����� </span>
            </td>
            <td valign="top">
                <input name="min_karma_private" type="text" id="min_karma_private" value="<?php echo @$cfg['min_karma_private'];?>" size="5" />
            </td>
        </tr>
        <tr>
            <td valign="top">
                <strong>�������� ������������� �����:</strong><br />
                <span class="hinttext">������� ����� ����� ����� ��� �������� ������������� ����� </span>
            </td>
            <td valign="top">
                <input name="min_karma_public" type="text" id="min_karma_public" value="<?php echo @$cfg['min_karma_public'];?>" size="5" />
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>RSS ����� </h4></td>
        </tr>
        <tr>
            <td valign="top"><strong>���������� ������ RSS ��� ���� ������: </strong></td>
            <td valign="top">
                <input name="rss_all" type="radio" value="1" <?php if (@$cfg['rss_all']) { echo 'checked="checked"'; } ?> /> ��
                <input name="rss_all" type="radio" value="0" <?php if (@!$cfg['rss_all']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>���������� ������ RSS ��� ������� �����: </strong></td>
            <td valign="top">
                <input name="rss_one" type="radio" value="1" <?php if (@$cfg['rss_one']) { echo 'checked="checked"'; } ?> /> ��
                <input name="rss_one" type="radio" value="0" <?php if (@!$cfg['rss_one']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>
<?php } ?>

<?php
    if ($opt=='edit_blog'){
        
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
	
					 $sql = "SELECT id, title
					 		 FROM cms_blogs
							 WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					
					 echo '<h3>������������� ���� '.$ostatok.'</h3>';
 					 cpAddPathway($mod['title'], $_SERVER['REQUEST_URI']);
    
?>
<form action="index.php?view=components&do=config&link=blogs&opt=update_blog&item_id=<?php echo $mod['id']; ?>" method="post" name="optform" target="_self" id="form1">
    <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td width="120"><strong>�������� �����: </strong></td>
            <td>
                <input name="title" type="text" id="title" value="<?php echo $mod['title'];?>" style="width:99%" />
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" value="update_blog" />
        <input name="item_id" type="hidden" value="<?php echo $mod['id']; ?>" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components&do=config&link=blogs&opt=list_blogs';"/>
    </p>
</form>
<?php } ?>