<?php

    //������ ������� ������ ����� ����� �� ��������
    if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

    //��������� ����� � ����������
	cpAddPathway('�������', '?view=components&do=config&id='.$_REQUEST['id']);

    //������� ���������
    echo '<h3>�������</h3>';

    //�������� ������� ��������, ��-��������� ��� list_cats, �.�. "�������� ��� ���������"
    $opt = $inCore->request('opt', 'str', 'list_cats');

    //
    // ��������� ����������� ���� ���������� c ��������. 
    // ������ ������ ����������� ��� ������� �������, � ��������� ������, ��������� � ������.
    // ������ ������ ������ � ����� /admin/images/toolmenu.
    // � ������� ����� ����� ������ ������ ��������� ��������, opt=...
    // � ���� ��������� ���������� ������� �������� (�������� ���������, �������� ������ � ��)
    //
    // � ������� ������ ���������� ����� ����� ��������:
    //   - �������� ��������� (�������� �����) (add_cat) *
    //   - �������� ����������� (�������� �����) (add_item) *
    //   - ������� ��������� � �� (submit_cat)
    //   - ������� ����������� � �� (submit_item)
    //   - �������� ��� ��������� (list_cats) *
    //   - �������� ��� ����������� (list_items) *
    //   - ��������� (�������� �����) (config) *
    //   - ��������� ��������� (saveconfig)
    //   - ������������� ��������� (�������� �����) (edit_cat) **
    //   - ������������� ����������� (�������� �����) (edit_item) **
    //   - �������� ��������� (update_cat)
    //   - �������� ����������� (update_item)
    //   - ������� ��������� (delete_cat)
    //   - ������� ����������� (delete_item)
    //   - ��������/������ ��������� (show_cat/hide_cat) **
    //   - ��������/������ ����������� (show_item/hide_item) **
    //
    // * ��� ���� 5-�� �������� ����� ������ �� ������ ����������,
    // ** ��� 4 �������� ����� ���������� �� ������� ��������� � �����������
    //

	$toolmenu = array();

    if($opt == 'add_cat' || $opt == 'add_item' || $opt == 'edit_cat' || $opt == 'edit_item' || $opt == 'config'){

        // ��� �������� "��������/�������������/���������" ����������
        // ������ ������ "���������" � "��������"

        $toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = '���������';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = '������';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

	} else {

        // ��� ��������� �������� ���������� ������ ����� ������

        $toolmenu[0]['icon'] = 'newfolder.gif';
        $toolmenu[0]['title'] = '����� ���������';
        $toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

        $toolmenu[2]['icon'] = 'newquest.gif';
        $toolmenu[2]['title'] = '����� �����������';
        $toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

        $toolmenu[1]['icon'] = 'folders.gif';
        $toolmenu[1]['title'] = '��������� �����������';
        $toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

        $toolmenu[3]['icon'] = 'listquest.gif';
        $toolmenu[3]['title'] = '��� �����������';
        $toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

        $toolmenu[4]['icon'] = 'config.gif';
        $toolmenu[4]['title'] = '���������';
        $toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

    }

    // ������� ���� �� �����
	cpToolMenu($toolmenu);

	// ��������� ������� ��������� ����������
	$cfg = $inCore->loadComponentConfig('meetings');

// ============================================================================================= //
// ============================================================================================= //

    // �������� "��������� ���������"
	if($opt=='saveconfig'){

        // �������� ��������� ����������, ���������� �� ����� ��������
        // ����������� �� � ������ � ������� ���� ���������
		$cfg                = array();
		$cfg['showtime']    = $inCore->request('showtime', 'int', 1);

        // ������� ���������� �������� � ���� ������� �������
        // ��� ���������� � ������ c �����������
		$inCore->saveComponentConfig('meetings', $cfg);

	}

// ============================================================================================= //
// ============================================================================================= //

    // �������� "���������� �����������"
    // ����� �� ������ ��������� � ������ ����� �������� ����� ������� � �������������
	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_meet_meetings', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_meet_meetings', $_REQUEST['item']);
		}			
		echo '1'; exit;
	}

    // �������� "������ �����������"
    // ����� �� ������ ��������� � ������ ����� �������� ����� ������� � �������������
	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_meet_meetings', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_meet_meetings', $_REQUEST['item']);
		}			
		echo '1'; exit;
	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������� �����������"
    // ����� ���������� ����� ���������� ������ �����������
    //
	if ($opt == 'submit_item'){	

        // �������� �������� ����� ����� � ���������� �� � ������

        $item['category_id']    = $inCore->request('category_id', 'int');
		$item['title']          = $inCore->request('title', 'int');
        $item['pubdate']        = $inCore->request('pubdate', 'str');
		$item['published']      = $inCore->request('published', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� �������� �� ���������� ������ � �����������
        // ������ ������� ������ ����������� � ����

        $model->addMeeting($item);

        // �������������� ������������ � ������ �����������

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}	  

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "�������� �����������"
    // ����� ���������� ����� �������������� �����������
    //
	if ($opt == 'update_item'){

        // �������� ID �������������� �����������

        $id = $inCore->request('id', 'int');

        // �������� �������� ����� ����� � ���������� �� � ������

        $item['category_id']    = $inCore->request('category_id', 'int');
		$item['title']          = $inCore->request('title', 'int');
        $item['pubdate']        = $inCore->request('pubdate', 'str');
		$item['published']      = $inCore->request('published', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� �������� �� ID � ����� ������ � �����������
        // ������ ������� ����������� � ����

        $model->updateMeeting($id, $item);

        // �������������� ������������ � ������ �����������

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������� �����������"
    //
	if($opt == 'delete_item'){

        // �������� ID ���������� �����������

        $id = $inCore->request('id', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� ������ �� ������� ����������� � ��������� ID

        $model->deleteMeeting($id);

        // �������������� ������������ � ������ �����������

		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}

// ============================================================================================= //
// ============================================================================================= //

    // �������� "���������� ���������"
    // ����� �� ������ ��������� � ������ ����� �������� ����� ������� � �����������
	if ($opt == 'show_cat'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_meet_category', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_meet_category', $_REQUEST['item']);
		}
		echo '1'; exit;
	}

    // �������� "������ ���������"
    // ����� �� ������ ��������� � ������ ����� �������� ����� ������� � �����������
	if ($opt == 'hide_cat'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_meet_category', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_meet_category', $_REQUEST['item']);
		}
		echo '1'; exit;
	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������� ���������"
    // ����� ���������� ����� ���������� ����� ���������
    //
	if ($opt == 'submit_cat'){	

        // �������� �������� ����� ����� � ���������� �� � ������

		$item['title']          = $inCore->request('title', 'int');
		$item['published']      = $inCore->request('published', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� �������� �� ���������� ������ � �����������
        // ������ ������� ������ ����������� � ����

        $model->addCategory($item);

        // �������������� ������������ � ������ ���������

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "�������� ���������"
    // ����� ���������� ����� �������������� ���������
    //
	if ($opt == 'update_cat'){

        // �������� ID ������������� ���������

        $id = $inCore->request('id', 'int');

        // �������� �������� ����� ����� � ���������� �� � ������

		$item['title']          = $inCore->request('title', 'int');
		$item['published']      = $inCore->request('published', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� �������� �� ID � ����� ������ � ���������
        // ������ ������� ������ ��������� � ����

        $model->updateCategory($id, $item);

        // �������������� ������������ � ������ ���������

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������� ���������"
    //
	if($opt == 'delete_cat'){

        // �������� ID ��������� ���������

        $id = $inCore->request('id', 'int');

        // ��������� ������ ������ ����������...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...� ������ �� ������� ��������� � ��������� ID

        $model->deleteCategory($id);

        // �������������� ������������ � ������ ���������

		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}


// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������ ���������"
    //
	if ($opt == 'list_cats'){

        // ������ �����������
		cpAddPathway('��������� ��������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

        // ���������
		echo '<h3>��������� ��������</h3>';

        //
        // ����� ������ (�������) ��������� �������������� ������������� ��������
        // ��� ����� ���� ������� ����� ���� ������� �� (�������) �� ����� ��������
        // � ����� �������� (�������������/�������) ������ ���� � ������ ������ (������)
        //

        // ������� ������ �� ������� �������� � ��������� ���
		$fields = array();

        // ������� ID, ������������� ���� id � �������
		$fields[0]['title']     = 'id';
        $fields[0]['field']     = 'id';
        $fields[0]['width']     = '30'; //������ ������� � ��������

        // ������� "��������", ������������� ���� title � �������
        // ��� ����� ������� ��� �� ��������� ������, ������ � 20 ��������
        // � ������� ��� �������� ������ �������������� ��������, �������� �
        // �������������� �������
		$fields[1]['title']     = '��������';
        $fields[1]['field']     = 'title';
        $fields[1]['width']     = '';
		$fields[1]['filter']    = 20;
		$fields[1]['link']      = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

        // ������� "�����", ������������� ���� published � �������
        // ��� ���� published ������� ������������� ����� ���������� �������(1) ��� �������(0) �������
        // ������ �� ���� �������� ����� �������� �������� hide_cat � show_cat ��������������
		$fields[3]['title']     = '�����';
        $fields[3]['field']     = 'published';
        $fields[3]['width']     = '100';
		$fields[3]['do']        = 'opt';
        $fields[3]['do_suffix'] = '_cat';

        //
		// ������� ������ �� ������� �������� ��� ������ ������ �������
        // (�.�. � ����� ������ ��� ������ ���������)
        // � ���������� � ������ ������ ����� ������ ��������� � �������� ��������
        // ������ � ���� ������ ����� �������� �������� edit_cat � delete_cat ��������������
        //
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� ��������� ��������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';

        //
		// ������� ������� �� �����
        // � �������� ���������� �������� �������� ������� � ��, ������ � ������ � ������ � ���������� ��� �����
        //
		cpListTable('cms_faq_cats', $fields, $actions);

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // �������� "������ �����������"
    //
	if ($opt == 'list_items'){

        //����� ��� ���������� ������ ������ ���������

		cpAddPathway('�������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		echo '<h3>�������</h3>';
		
		$fields = array();

		$fields[0]['title'] = 'id';			
        $fields[0]['field'] = 'id';
        $fields[0]['width'] = '30';

		$fields[1]['title']     = '�����������';
        $fields[1]['field']     = 'title';
        $fields[1]['width']     = '';
		$fields[1]['link']      = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
		$fields[1]['filter']    = 15;
		$fields[1]['maxlen']    = 80; // ���� ��������� ����������� ����� ������� 80 ��������, �� ��������� �� ���� �����
		
        //
        // ����� ������� �������� � ������� "���������" �� �����, � ��������
        // �� ��������� �������� 'prc', � ������� �������� �������� ������� cpMeetCatById
        // ��� ������� ���������� � ������ ����� � ����� ���������� �������� ��������� �� �� ������
        //
		$fields[2]['title']         = '���������';
        $fields[2]['field']         = 'category_id';
        $fields[2]['width']         = '300';
		$fields[2]['prc']           = 'cpMeetCatById';
        $fields[2]['filter']        = 1;
        $fields[2]['filterlist']    = cpGetList('cms_meet_category'); //��� ���� �����, ����� ������� ���������� ������ � ������� �� ����������

		$fields[3]['title']     = '�����';
        $fields[3]['field']     = 'published';
        $fields[3]['width']     = '100';
		$fields[3]['do']        = 'opt';
        $fields[3]['do_suffix'] = '_item';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� ������?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_faq_quests', $fields, $actions, '', 'pubdate DESC');

	}

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'add_item' || $opt == 'edit_item'){
		if ($opt=='add_item'){
		 echo '<h3>�������� ������</h3>';
		 cpAddPathway('�������� ������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
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
		
		
					 $sql = "SELECT *, DATE_FORMAT(pubdate, '%d.%m.%Y') as pubdate, DATE_FORMAT(answerdate, '%d.%m.%Y') as answerdate
					 		 FROM cms_faq_quests 
							 WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>�������� �������</h3>';
					 cpAddPathway('�������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

		?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
          <tr>
            <td><strong>��������� �������:</strong></td>
            <td width="220"><select name="category_id" id="category_id" style="width:220px">
                <?php
                    if (isset($mod['category_id'])) {
                        echo $inCore->getListItems('cms_faq_cats', $mod['category_id']);
                    } else {
                        if (isset($_REQUEST['addto'])){
                            echo $inCore->getListItems('cms_faq_cats', $_REQUEST['addto']);
                        } else {
                            echo $inCore->getListItems('cms_faq_cats');
                        }
                    }
                ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>����� �������:</strong></td>
            <td><select name="user_id" id="user_id" style="width:220px">
              <?php
                  if (isset($mod['user_id'])) {
                        echo $inCore->getListItems('cms_users', $mod['user_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  } else {
                        echo $inCore->getListItems('cms_users', $inUser->id, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  }
              ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>����������� ������?</strong></td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
              ��
              <label>
        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                ���</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>���� ������ �������: </strong></td>
            <td valign="top"><input name="pubdate" type="text" style="width:190px" id="pubdate" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
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
                <input type="hidden" name="oldpubdate" value="<?php echo @$mod['pubdate']?>"/>            </td>
          </tr>
          <tr>
            <td valign="top"><strong>���� ������: </strong></td>
            <td valign="top"><input name="answerdate" style="width:190px" type="text" id="answerdate" <?php if(@!$mod['answerdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['answerdate'].'"'; } ?>/>
                <?php 
					//include javascript
					if (@!$mod['answerdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['answerdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                <input type="hidden" name="oldanswerdate" value="<?php echo @$mod['answerdate']?>"/>
            </td>
          </tr>
  </table>
        <table width="507" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="377">
			<div style="margin-bottom:10px"><strong>����� �������:</strong></div>
			<div>
				<textarea name="quest" rows="6" id="quest" style="border:solid 1px gray;width:605px"><?php echo @$mod['quest'];?></textarea>
			</div>			</td>
          </tr>
          <tr>
            <td>
			<div style="margin-bottom:10px"><strong>����� �� ������:</strong></div>
			<div>
			<?php
                $inCore->insertEditor('answer', $mod['answer'], '300', '605');
			?>
			</div>			</td>
          </tr>
        </table>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="�������� ������"'; } else { echo 'value="��������� ���������"'; } ?> />
          </label>
          <label>
          <input name="back2" type="button" id="back2" value="������" onclick="window.location.href='index.php?view=components';"/>
          </label>
          <input name="opt" type="hidden" id="do" <?php if ($opt=='add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
          <?php
		  	if ($opt=='edit_item'){
				 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
			}
		  ?>
        </p>
</form>
		<?php
	}

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'add_cat' || $opt == 'edit_cat'){		
		if ($opt=='add_cat'){
			 echo '<h3>�������� ���������</h3>';
			 cpAddPathway('�������� ���������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat');	 
			} else {
				 if(isset($_REQUEST['item_id'])){
					 $id = $_REQUEST['item_id'];
					 $sql = "SELECT * FROM cms_faq_cats WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
				 }
				
				 echo '<h3>���������: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('��������� ��������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>
		<form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>">
			<table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
			  <tr>
				<td><strong>�������� ���������: </strong></td>
				<td width="220"><input name="title" type="text" id="title" style="width:220px" value="<?php echo @$mod['title'];?>"/></td>
			  </tr>
			  <tr>
			    <td><strong>������������ ���������</strong>: </td>
			    <td><select name="parent_id" id="parent_id" style="width:220px">
					<option value="0" <?php if (!isset($mod['parent_id'])||@$mod['parent_id']==0){ echo 'selected'; } ?>>--</option>
				<?php if (isset($mod['parent_id'])) 
					  { 
							echo $inCore->getListItems('cms_faq_cats', $mod['id']);
					  }	else { 
								echo $inCore->getListItems('cms_faq_cats');
							 }
				?>
                </select></td>
		      </tr>
			  <tr>
				<td><strong>����������� ���������?</strong></td>
				<td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				  ��
				  <label>
			  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
					���</label></td>
			  </tr>
			</table>
			<table width="100%" border="0">
			  <tr>
				<?php
				if(!isset($mod['user']) || @$mod['user']==1){
					echo '<td width="52%" valign="top">';
					echo '�������� ���������:<br/>';

                    $inCore->insertEditor('description', $mod['description'], '260', '605');
					
					echo '</td>';
				}
				?>
			  </tr>
			</table>	
			<p>
			  <label>
			  <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add_cat') { echo 'value="������� ���������"'; } else { echo 'value="��������� ���������"'; } ?> />
			  </label>
			  <label>
			  <input name="back3" type="button" id="back3" value="������" onclick="window.location.href='index.php?view=components';"/>
			  </label>
			  <input name="opt" type="hidden" id="do" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
			  <?php
				if ($opt=='edit_cat'){
				 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
				}
			  ?>
			</p>
</form>
		 <?php	
	}

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'config') {
		?>
<?php
	}


?>