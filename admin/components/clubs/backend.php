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

function error($msg){
    //
}

function usersClubByID($id){
    $b = dbRowsCount('cms_user_clubs', 'club_id='.$id);
    return($b);
}

$cfg = $inCore->loadComponentConfig('clubs');

    if(!isset($cfg['enabled_blogs'])) { $cfg['enabled_blogs'] = 1; }
    if(!isset($cfg['enabled_photos'])) { $cfg['enabled_photos'] = 1; }
    if(!isset($cfg['thumb1'])) { $cfg['thumb1'] = 48; }
    if(!isset($cfg['thumb2'])) { $cfg['thumb2'] = 200; }
    if(!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1; }
    if(!isset($cfg['cancreate'])) { $cfg['cancreate'] = 0; }
    if(!isset($cfg['perpage'])) { $cfg['perpage'] = 10; }
    if(!isset($cfg['create_min_karma'])) { $cfg['create_min_karma'] = 1; }
    if(!isset($cfg['create_min_rating'])) { $cfg['create_min_rating'] = 0; }
    if(!isset($cfg['notify_in'])) { $cfg['notify_in'] = 1; }
    if(!isset($cfg['notify_out'])) { $cfg['notify_out'] = 1; }

if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

$inCore->loadLib('clubs');

$inCore->loadModel('clubs');

$model = new cms_model_clubs();

if($opt=='list'){

    $toolmenu = array();

    $toolmenu[0]['icon'] = 'new.gif';
    $toolmenu[0]['title'] = '����� ����';
    $toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add';

    $toolmenu[3]['icon'] = 'list.gif';
    $toolmenu[3]['title'] = '��� �����';
    $toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list';

    $toolmenu[4]['icon'] = 'config.gif';
    $toolmenu[4]['title'] = '���������';
    $toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

    $toolmenu[11]['icon'] = 'edit.gif';
    $toolmenu[11]['title'] = '������������� ���������';
    $toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit&multiple=1');";

    $toolmenu[12]['icon'] = 'show.gif';
    $toolmenu[12]['title'] = '�������� ���������';
    $toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_club&multiple=1');";

    $toolmenu[13]['icon'] = 'hide.gif';
    $toolmenu[13]['title'] = '��������� ���������';
    $toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_club&multiple=1');";

}

if ($opt=='list' || $opt=='config'){

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

if($opt=='saveconfig'){	
    $cfg = array();
    $cfg['enabled_blogs'] = $_REQUEST['enabled_blogs'];
    $cfg['enabled_photos'] = $_REQUEST['enabled_photos'];

    $cfg['thumb1'] = $_REQUEST['thumb1'];
    $cfg['thumb2'] = $_REQUEST['thumb2'];
    $cfg['thumbsqr'] = $_REQUEST['thumbsqr'];
    $cfg['cancreate'] = $_REQUEST['cancreate'];
    $cfg['perpage'] = $_REQUEST['perpage'];

    $cfg['create_min_karma'] = $_REQUEST['create_min_karma'];
    $cfg['create_min_rating'] = $_REQUEST['create_min_rating'];

    $inCore->saveComponentConfig('clubs', $cfg);

    $msg = '��������� ���������.';
    $opt = 'config';
}

if ($opt == 'show_club'){
    if (!isset($_REQUEST['item'])){
        if (isset($_REQUEST['item_id'])){ dbShow('cms_clubs', $_REQUEST['item_id']);  }
        echo '1'; exit;
    } else {
        dbShowList('cms_clubs', $_REQUEST['item']);
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
}

if ($opt == 'hide_club'){
    if (!isset($_REQUEST['item'])){
        if (isset($_REQUEST['item_id'])){ dbHide('cms_clubs', $_REQUEST['item_id']);  }
        echo '1'; exit;
    } else {
        dbHideList('cms_clubs', $_REQUEST['item']);
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
}

if ($opt == 'submit'){	
    $title 			= $inCore->request('title', 'str');
    $description 	= $inCore->request('description', 'html');
    $published 		= $inCore->request('published', 'int');
    $admin_id 		= $inCore->request('admin_id', 'int');
    $clubtype		= $inCore->request('clubtype', 'str');
    $maxsize 		= $inCore->request('maxsize', 'int');
    $enabled_blogs	= $inCore->request('enabled_blogs', 'int');
    $enabled_photos	= $inCore->request('enabled_photos', 'int');

    $date = explode('.', $_REQUEST['pubdate']);
    $pubdate = $date[2] . '-' . $date[1] . '-' . $date[0];

    //upload logo
    if (isset($_FILES['picture'])){
        require($_SERVER['DOCUMENT_ROOT'].'/includes/graphic.inc.php');
        $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/clubs/';
        if (!is_dir($uploaddir)) { @mkdir($uploaddir); }

        $realfile = $_FILES['picture']['name'];
        $lid = dbGetFields('cms_clubs', 'id>0', 'id', 'id DESC');
        $lastid = $lid['id']+1;
        $filename = md5($lastid).'.jpg';

        $uploadphoto = $uploaddir . $filename;
        $uploadthumb = $uploaddir . 'small/' . $filename;

        if ($inCore->moveUploadedFile($_FILES['picture']['tmp_name'], $uploadphoto, $_FILES['picture']['error'])) {
            if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
            @img_resize($uploadphoto, $uploadthumb, $cfg['thumb1'], $cfg['thumb1'], $cfg['thumbsqr']);
            @img_resize($uploadphoto, $uploadphoto, $cfg['thumb2'], $cfg['thumb2'], $cfg['thumbsqr']);
        } else { $msg = $inCore->uploadError(); }
    } else {
        $filename = '';
    }

    //insert into db
    $sql = "INSERT INTO cms_clubs (admin_id, title, description, imageurl, pubdate, clubtype, published, maxsize, enabled_blogs, enabled_photos)
                    VALUES ($admin_id, '$title', '$description', '$filename', '$pubdate', '$clubtype', '$published', '$maxsize', '$enabled_blogs', '$enabled_photos')";
    dbQuery($sql);

    $id = dbLastId('cms_clubs');

    //create blog
    $sql = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, view_type, showcats, ownertype, premod, forall, owner)
                    VALUES ('$id', '����', NOW(), 'all', 'list', 1, 'multi', 0, 0, 'club')";
    dbQuery($sql);

    $moders 		= $_POST['moderslist'];
    $members 		= $_POST['memberslist'];

    if (array_search($admin_id, $moders)) { unset($moders[array_search($admin_id, $moders)]); }
    if (array_search($admin_id, $members)) { unset($members[array_search($admin_id, $members)]); }

    clubSaveUsers($id, $moders, 'moderator');
    clubSaveUsers($id, $members, 'member');

    header('location:?view=components&do=config&opt=list&id='.$_REQUEST['id']);
}	  

if ($opt == 'update'){
    if(isset($_REQUEST['item_id'])) {
        $id 			= (int)$_REQUEST['item_id'];
        $title 			= $inCore->request('title', 'str');
        $description 	= $inCore->request('description', 'html');
        $published 		= $inCore->request('published', 'int');
        $admin_id 		= (int)$_REQUEST['admin_id'];
        $clubtype		= $inCore->request('clubtype', 'str');
        $maxsize 		= $inCore->request('maxsize', 'int');
        $enabled_blogs	= (int)$_REQUEST['enabled_blogs'];
        $enabled_photos	= (int)$_REQUEST['enabled_photos'];

        $olddate 		= $_REQUEST['olddate'];
        $pubdate 		= $_REQUEST['pubdate'];

        if ($olddate != $pubdate){
            $date = explode('.', $pubdate);
            $newdate = $date[2] . '-' . $date[1] . '-' . $date[0];
            $sql = "UPDATE cms_clubs SET pubdate = '$newdate' WHERE id=$id";
            dbQuery($sql);
        }

        //upload logo
        if ($_FILES['picture']['name']){
            require($_SERVER['DOCUMENT_ROOT'].'/includes/graphic.inc.php');
            $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/clubs/';
            if (!is_dir($uploaddir)) { @mkdir($uploaddir); }

            $filename = md5($id).'.jpg';
            $uploadphoto = $uploaddir . $filename;
            $uploadthumb = $uploaddir . 'small/' . $filename;

            if ($inCore->moveUploadedFile($_FILES['picture']['tmp_name'], $uploadphoto, $_FILES['picture']['error'])) {
                if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
                @img_resize($uploadphoto, $uploadthumb, $cfg['thumb1'], $cfg['thumb1'], $cfg['thumbsqr']);
                @img_resize($uploadphoto, $uploadphoto, $cfg['thumb2'], $cfg['thumb2'], $cfg['thumbsqr']);
            } else { $msg = $inCore->uploadError(); }

            $sql = "UPDATE cms_clubs SET imageurl = '$filename' WHERE id=$id";
            dbQuery($sql);
        }

        //insert into db
        $sql = "UPDATE cms_clubs
                    SET admin_id = '$admin_id',
                        title = '$title',
                        description = '$description',
                        clubtype = '$clubtype',
                        published = '$published',
                        maxsize = '$maxsize',
                        enabled_blogs = '$enabled_blogs',
                        enabled_photos = '$enabled_photos'
                    WHERE id = $id";
        dbQuery($sql) ;

        $moders 		= $_POST['moderslist'];
        $members 		= $_POST['memberslist'];

        if (array_search($admin_id, $moders)) { unset($moders[array_search($admin_id, $moders)]); }
        if (array_search($admin_id, $members)) { unset($members[array_search($admin_id, $members)]); }

        clubSaveUsers($id, $moders, 'moderator', $clubtype);
        clubSaveUsers($id, $members, 'member', $clubtype);
    }
    if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
        $inCore->redirect('index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
    } else {
        $inCore->redirect('index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit');
    }
}

if($opt == 'delete'){
    if(isset($_REQUEST['item_id'])) {
        $id     = (int)$_REQUEST['item_id'];
        $model->deleteClub($id);
    }
    $inCore->redirect('index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');    
}

cpToolMenu($toolmenu);

if ($opt == 'list'){
    cpAddPathway('����� �������������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
    echo '<h3>����� �������������</h3>';

    //TABLE COLUMNS
    $fields = array();

    $fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

    $fields[1]['title'] = '����';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '100';		$fields[1]['filter'] = 15;
    $fields[1]['fdate'] = '%d/%m/%Y';

    $fields[2]['title'] = '��������';	$fields[2]['field'] = 'title';		$fields[2]['width'] = '';
    $fields[2]['filter'] = 15;
    $fields[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

    $fields[3]['title'] = '����������';	$fields[3]['field'] = 'id';		$fields[3]['width'] = '120';
    $fields[3]['prc'] = 'usersClubByID';

    $fields[4]['title'] = '�������';		$fields[4]['field'] = 'published';		$fields[4]['width'] = '100';
    $fields[4]['do'] = 'opt'; $fields[4]['do_suffix'] = '_club';

    //ACTIONS
    $actions = array();
    $actions[0]['title'] = '�������������';
    $actions[0]['icon']  = 'edit.gif';
    $actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

    $actions[1]['title'] = '�������';
    $actions[1]['icon']  = 'delete.gif';
    $actions[1]['confirm'] = '������� ����?';
    $actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete&item_id=%id%';

    //Print table
    cpListTable('cms_clubs', $fields, $actions, '', 'pubdate DESC');
}

if ($opt == 'add' || $opt == 'edit'){	

    if ($opt=='add'){
        echo '<h3>�������� ����</h3>';
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


        $sql = "SELECT *, DATE_FORMAT(pubdate, '%d.%m.%Y') as pubdate FROM cms_clubs WHERE id = $id LIMIT 1";
        $result = dbQuery($sql) ;
        if (mysql_num_rows($result)){
            $mod = mysql_fetch_assoc($result);
            $mod['title'] = str_replace('"', '&quot;', $mod['title']);
        }

        echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
        cpAddPathway('����� �������������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
        cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$id);

    }

    if(!isset($mod['maxsize'])) { $mod['maxsize'] = 0; }
    if(!isset($mod['admin_id'])) { $mod['admin_id'] = $inUser->id; }
    if(!isset($mod['clubtype'])) { $mod['clubtype'] = 'public'; }

    require('../includes/jwtabs.php');
    $GLOBALS['cp_page_head'][] = jwHeader();

    $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/admin/js/clubs.js"></script>';

    ob_start(); ?>
    
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
    {tab=����� ���������}
    <table width="625" border="0" cellspacing="5" class="proptable">
        <tr>
            <td width="298"><strong>�������� �����: </strong><br />
            <span class="hinttext">������������ �� �����</span>					</td>
            <td width="308"><input name="title" type="text" id="title" style="width:300px" value="<?php echo @$mod['title'];?>"/></td>
        </tr>
        <tr>
            <td><strong>������� �����:</strong><br />
            <span class="hinttext">������ GIF, JPG, JPEG, PNG </span>					</td>
            <td>
                <?php if (@$mod['imageurl']){ echo '<div style="margin-bottom:5px;"><img src="/images/clubs/small/'.$mod['imageurl'].'" /></div>'; } ?>
                <input name="picture" type="file" id="picture" size="33" />
            </td>
        </tr>
        <tr>
            <td><strong>������������ ������: </strong><br />
                <span class="hinttext">������� &quot;0&quot; ��� ������������ <br />
            ����� ���������� </span></td>
            <td><input name="maxsize" type="text" id="maxsize" style="width:300px" value="<?php echo @$mod['maxsize'];?>"/></td>
        </tr>
        <tr>
            <td><strong>���� �������� �����:</strong><br />
            <span class="hinttext">������������ �� �����</span></td>
            <td><input name="pubdate" type="text" id="pubdate" style="width:278px" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
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
            <input type="hidden" name="olddate" value="<?php echo @$mod['pubdate']?>"/></td>
        </tr>
        <tr>
            <td>
                <strong>����������� ����?</strong><br />
                <span class="hinttext">��� ���������� ���� �� ������������ � ����� ������<br />
                � �� ��������</span>
            </td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
                ��
                <label>
                    <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
            ���</label></td>
        </tr>
        <tr>
            <td><strong>����:</strong><br />
            <span class="hinttext">��������/��������� ���� �����</span></td>
            <td>
                <select name="enabled_blogs" id="enabled_blogs" style="width:300px">
                    <option value="0" <?php if (@$mod['enabled_blogs']=='0') { echo 'selected="selected"'; } ?>>��-���������</option>
                    <option value="1" <?php if (@$mod['enabled_blogs']=='1') { echo 'selected="selected"'; } ?>>�������</option>
                    <option value="-1" <?php if (@$mod['enabled_blogs']=='-1') { echo 'selected="selected"'; } ?>>��������</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>�����������:</strong><br />
            <span class="hinttext">��������/��������� �����������</span></td>
            <td>
                <select name="enabled_photos" id="enabled_photos" style="width:300px">
                    <option value="0" <?php if (@$mod['enabled_photos']=='0') { echo 'selected="selected"'; } ?>>��-���������</option>
                    <option value="1" <?php if (@$mod['enabled_photos']=='1') { echo 'selected="selected"'; } ?>>��������</option>
                    <option value="-1" <?php if (@$mod['enabled_photos']=='-1') { echo 'selected="selected"'; } ?>>���������</option>
                </select>
            </td>
        </tr>
    </table>
    {tab=��������}
    <table width="625" border="0" cellspacing="5" class="proptable">
        <tr>
            <td width="606"><strong>��������:</strong> <span class="hinttext">������������ �� ������ �������� ��� ��������� ����� </span></td>
        </tr>
        <tr>
            <td>
                <?php

                    $inCore->insertEditor('description', $mod['description'], '250', '100%');
                
                ?>
            </td>
        </tr>
    </table>
    {tab=����� �������}
    <table width="625" border="0" cellspacing="5" class="proptable">
        <tr>
            <td width="298"><strong>������� ������������� �����:</strong><br />
            <span class="hinttext">��������� ����������� </span></td>
            <td width="308">
                <select name="admin_id" id="admin_id" style="width:300px">
                    <?php
                        if (isset($mod['admin_id'])) {
                            echo $inCore->getListItems('cms_users', $mod['admin_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                        } else {
                            echo $inCore->getListItems('cms_users', 0, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>��� �����:</strong><br />
            <span class="hinttext">��� ���� ������ ���� ���� </span></td>
            <td>
                <select name="clubtype" id="clubtype" style="width:300px" onchange="toggleMembers()">
                    <option value="public" <?php if (@$mod['clubtype']=='public') { echo 'selected="selected"'; } ?>>������ ��� ���� (public)</option>
                    <option value="private" <?php if (@$mod['clubtype']=='private') { echo 'selected="selected"'; } ?>>������ ��� ��������� (private)</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <?php if (isset($mod['id'])) { $moderators = clubModerators(@$mod['id']); } else { $moderators = array(); }?>
            <?php if (isset($mod['id'])) { $members = clubMembers(@$mod['id']); } else { $members = array(); } ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="10" id="multiuserscfg" style="margin-top:5px;border:dotted 1px silver;display: {if $blog.ownertype=='single' || $blog.forall}none;{else}table;{/if}">
            <td align="center" valign="top"><strong>���������� �����: </strong><br/>
                <select name="moderslist[]" size="10" multiple id="moderslist" style="width:200px">
                    <?php echo cmsUser::getAuthorsList($moderators); ?>
            </select>					  </td>
            <td align="center">
                <div><input name="moderator_add" type="button" id="moderator_add" value="&lt;&lt;"></div>
            <div><input name="moderator_remove" type="button" id="moderator_remove" value="&gt;&gt;" style="margin-top:4px"></div>					  </td>
            <td align="center" valign="top"><strong>��� ������������:</strong><br/>
                <select name="userslist1" size="10" multiple id="userslist1" style="width:200px">
                    <?php echo cmsUser::getUsersList(false, array_merge($moderators, $members)); ?>
                </select>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    </table>
    {tab=���������}
    <table width="625" border="0" cellspacing="5" class="proptable">
        <tr>
            <td width="606">
                <p id="nomembers" style="display:<?php @$mod['clubtype']=='public' ? 'block' : 'none' ?>">���� ������ ��� ����, ������� ������ ������������������ ������������ ����� ����� ��� ����������.</p>
                <div id="members">
                    <table width="100%" border="0" align="center" cellpadding="10" cellspacing="0" style="display:<?php (@$mod['clubtype']=='public') ?'none' : 'block' ?>">
                        <tr>
                            <td align="center" valign="top"><strong>��������� �����: </strong><br/>
                                <select name="memberslist[]" size="12" multiple="multiple" id="memberslist" style="width:200px">
                                    <?php echo cmsUser::getAuthorsList($members); ?>
                            </select>                    </td>
                            <td align="center"><div>
                                    <input name="member_add" type="button" id="member_add" value="&lt;&lt;" />
                                </div>
                                <div>
                                    <input name="member_remove" type="button" id="member_remove" value="&gt;&gt;" style="margin-top:4px" />
                            </div></td>
                            <td align="center" valign="top"><strong>��� ������������:</strong><br/>
                                <select name="userslist2" size="12" multiple="multiple" id="userslist2" style="width:200px">
                                    <?php echo cmsUser::getUsersList(false, array_merge($moderators, $members)); ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    {/tabs}
    <p>
        <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add') { echo 'value="������� ����"'; } else { echo 'value="��������� ����"'; } ?> />
        <input name="back3" type="button" id="back3" value="������" onclick="window.location.href='index.php?view=components';"/>
        <input name="opt" type="hidden" id="opt" <?php if ($opt=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
        <?php
        if ($opt=='edit'){
            echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
        }
        ?>
    </p>
</form>

    <?php	echo jwTabs(ob_get_clean());

    echo '<script type="text/javascript">toggleMembers();</script>';
}

if ($opt=='config') {

    cpAddPathway('����� �������������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
    cpAddPathway('���������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config');
    echo '<h3>����� �������������</h3>';

    if (@$msg) { echo '<p style="color:green">'.$msg.'</p>'; }
    ?>

<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <table width="680" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>����� ������:</strong><br />
            <span class="hinttext">��������/��������� �����</span></td>
            <td width="300">
                <select name="enabled_blogs" id="enabled_blogs" style="width:300px">
                    <option value="1" <?php if (@$cfg['enabled_blogs']=='1') { echo 'selected="selected"'; } ?>>��������</option>
                    <option value="-1" <?php if (@$cfg['enabled_blogs']=='-1') { echo 'selected="selected"'; } ?>>���������</option>
            </select>			</td>
        </tr>
        <tr>
            <td><strong>���������� ������ �� ��������:</strong><br /></td>
            <td><input name="perpage" type="text" id="perpage" style="width:300px" value="<?php echo @$cfg['perpage'];?>"/></td>
        </tr>
        <tr>
            <td><strong>����������� ������:</strong><br />
            <span class="hinttext">��������/��������� ����������� </span></td>
            <td>
                <select name="enabled_photos" id="enabled_photos" style="width:300px">
                    <option value="1" <?php if (@$cfg['enabled_photos']=='1') { echo 'selected="selected"'; } ?>>��������</option>
                    <option value="-1" <?php if (@$cfg['enabled_photos']=='-1') { echo 'selected="selected"'; } ?>>���������</option>
            </select>            </td>
        </tr>
        <tr>
            <td><strong>������ ��������� ����� ����:</strong><br />
            <span class="hinttext">� ��������</span></td>
            <td><input name="thumb1" type="text" id="thumb1" style="width:300px" value="<?php echo @$cfg['thumb1'];?>"/></td>
        </tr>
        <tr>
            <td><strong>������ �������� ����� ����:</strong><br />
            <span class="hinttext">� ��������</span></td>
            <td><input name="thumb2" type="text" id="thumb2" style="width:300px" value="<?php echo @$cfg['thumb2'];?>"/></td>
        </tr>
        <tr>
            <td><strong>���������� ��������:</strong></td>
            <td>
                <select name="thumbsqr" id="select" style="width:300px">
                    <option value="1" <?php if (@$cfg['thumbsqr']=='1') { echo 'selected="selected"'; } ?>>��</option>
                    <option value="0" <?php if (@$cfg['thumbsqr']=='0') { echo 'selected="selected"'; } ?>>���</option>
            </select>			</td>
        </tr>
        <tr>
            <td><strong>�������� ������ ��������������:</strong><br />
                <span class="hinttext">���� ��������, ������ ������������ �����<br />
            ������� ����������� ����, �� ������ ���� </span></td>
            <td valign="top">
                <input name="cancreate" type="radio" value="1"  <?php if (@$cfg['cancreate']) { echo 'checked="checked"'; } ?> />��
            <input name="cancreate" type="radio" value="0"  <?php if (@!$cfg['cancreate']) { echo 'checked="checked"'; } ?> /> ���			</td>
        </tr>
        <tr>
            <td><strong>����������� �� ����� �� �������� ������:</strong><br />
            <span class="hinttext">������������ ������ �����  ����� �� ���� ���������, ����� ����� ����������� ��������� ����� </span></td>
            <td valign="top"><input name="create_min_karma" type="text" id="create_min_karma" style="width:300px" value="<?php echo @$cfg['create_min_karma'];?>"/></td>
        </tr>
        <tr>
            <td><strong>����������� �� �������� �� �������� ������:</strong><br />
            <span class="hinttext">������������ ������ ����� ������� �� ���� ����������, ����� ����� ����������� ��������� �����</span></td>
            <td valign="top"><input name="create_min_rating" type="text" id="create_min_rating" style="width:300px" value="<?php echo @$cfg['create_min_rating'];?>"/></td>
        </tr>
        <tr>
            <td>
                <strong>����������� � �������� � ����:</strong><br />
                <span class="hinttext">�������� ������ ��������� ������������,<br/>��������� � ��������� ����</span>
            </td>
            <td valign="top">
                <input name="notify_in" type="radio" value="1"  <?php if (@$cfg['notify_in']) { echo 'checked="checked"'; } ?> /> ��
                <input name="notify_in" type="radio" value="0"  <?php if (@!$cfg['notify_in']) { echo 'checked="checked"'; } ?> /> ���
            </td>
        </tr>
        <tr>
            <td>
                <strong>����������� � ���������� �� �����:</strong><br />
                <span class="hinttext">�������� ������ ��������� ������������, ������������ �� ���������� �����</span>
            </td>
            <td valign="top">
                <input name="notify_out" type="radio" value="1"  <?php if (@$cfg['notify_out']) { echo 'checked="checked"'; } ?> /> ��
                <input name="notify_out" type="radio" value="0"  <?php if (@!$cfg['notify_out']) { echo 'checked="checked"'; } ?> /> ���
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