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

cpAddPathway('������� �������������', '?view=components&do=config&id='.$_REQUEST['id']);

echo '<h3>������� �������������</h3>';

if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

$toolmenu = array();

$toolmenu[0]['icon'] = 'save.gif';
$toolmenu[0]['title'] = '���������';
$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

$toolmenu[1]['icon'] = 'cancel.gif';
$toolmenu[1]['title'] = '������';
$toolmenu[1]['link'] = '?view=components';

cpToolMenu($toolmenu);

$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';

//LOAD CURRENT CONFIG
$cfg = $inCore->loadComponentConfig('users');

if (!isset($cfg['sw_search']))  { $cfg['sw_search'] = 1; }
if (!isset($cfg['sw_guest']))  { $cfg['sw_guest'] = 1; }
if (!isset($cfg['sw_clubs']))  { $cfg['sw_clubs'] = 1; }
if (!isset($cfg['karmatime'])) { $cfg['karmatime'] = 3; }
if (!isset($cfg['karmaint']))  { $cfg['karmaint'] = 'HOUR'; }

if (!isset($cfg['sw_feed'])) { $cfg['sw_feed'] = 1; }
if (!isset($cfg['sw_awards'])) { $cfg['sw_awards'] = 1; }

if (!isset($cfg['smallw'])) { $cfg['smallw'] = 64; }
if (!isset($cfg['medw'])) { $cfg['medw'] = 200; }
if (!isset($cfg['medh'])) { $cfg['medh'] = 200; }

if(!isset($cfg['deltime'])) { $cfg['deltime']=6;	}

if($opt=='saveconfig'){	
    $cfg = array();
    $cfg['sw_comm']     = $_REQUEST['sw_comm'];
    $cfg['sw_search']   = $_REQUEST['sw_search'];
    $cfg['sw_forum']    = $_REQUEST['sw_forum'];
    $cfg['sw_photo']    = $_REQUEST['sw_photo'];
    $cfg['sw_wall']     = $_REQUEST['sw_wall'];
    $cfg['sw_blogs']    = $_REQUEST['sw_blogs'];
    $cfg['sw_clubs']    = $_REQUEST['sw_clubs'];
    $cfg['sw_feed']     = $_REQUEST['sw_feed'];
    $cfg['sw_awards']   = $_REQUEST['sw_awards'];
    $cfg['sw_board']    = $_REQUEST['sw_board'];
    $cfg['sw_msg']      = $_REQUEST['sw_msg'];
    $cfg['sw_guest']    = $_REQUEST['sw_guest'];

    $cfg['karmatime']   = (int)$_REQUEST['karmatime'];
    $cfg['karmaint']    = $_REQUEST['karmaint'];

    $cfg['photosize']   = $_REQUEST['photosize'];
    $cfg['watermark']   = $_REQUEST['watermark'];

    $cfg['smallw']      = $inCore->request('smallw', 'int', 64);
    $cfg['medw']        = $inCore->request('medw', 'int', 200);
    $cfg['medh']        = $inCore->request('medh', 'int', 200);

    $cfg['sw_files']    = $_REQUEST['sw_files'];
    $cfg['filessize']   = $_REQUEST['filessize'];
	$cfg['filestype']   = trim(strtolower($_REQUEST['filestype']));

    $cfg['privforms']   = $_REQUEST['privforms'];

	$cfg['deltime']     = $_REQUEST['deltime'];

    $inCore->saveComponentConfig('users', $cfg);

    $msg = '��������� ���������.';
}


if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
<?php cpCheckWritable('/images/users/avatars', 'folder'); ?>
<?php cpCheckWritable('/images/users/avatars/small', 'folder'); ?>
<?php cpCheckWritable('/images/users/photos', 'folder'); ?>
<?php cpCheckWritable('/images/users/photos/small', 'folder'); ?>
<?php cpCheckWritable('/images/users/photos/medium', 'folder'); ?>

<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">

    <div id="config_tabs" style="margin-top:12px;">

    <ul id="tabs">
        <li><a href="#basic"><span>��������� ��������</span></a></li>
        <li><a href="#avatars"><span>�������</span></a></li>
        <li><a href="#proftabs"><span>������� ��������</span></a></li>
        <li><a href="#forms"><span>�������������� ����</span></a></li>
        <li><a href="#photos"><span>�����������</span></a></li>
        <li><a href="#files"><span>�������� ������</span></a></li>
        <li><a href="#reg"><span>�����������</span></a></li>
    </ul>

        <div id="basic">
            <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td><strong>��������� ������ ������������� �������: </strong></td>
                    <td width="182">
                        <input name="sw_guest" type="radio" value="1" <?php if (@$cfg['sw_guest']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="sw_guest" type="radio" value="0" <?php if (@!$cfg['sw_guest']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                <tr>
                    <td><strong>����� �������������: </strong></td>
                    <td>
                        <input name="sw_search" type="radio" value="1" <?php if (@$cfg['sw_search']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_search" type="radio" value="0" <?php if (@!$cfg['sw_search']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <!--
                <tr>
                    <td><strong>���������� ������ ������������ � �������: </strong></td>
                    <td width="182">
                        <input name="showgroup" type="radio" value="1" <?php if (@$cfg['showgroup']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="showgroup" type="radio" value="0" <?php if (@!$cfg['showgroup']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
                -->
                <tr>
                    <td><strong>���������� ����� ������������: </strong></td>
                    <td width="182">
                        <input name="sw_comm" type="radio" value="1" <?php if (@$cfg['sw_comm']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_comm" type="radio" value="0" <?php if (@!$cfg['sw_comm']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>���������� ����� ��������� �� ������: </strong></td>
                    <td>
                        <input name="sw_forum" type="radio" value="1" <?php if (@$cfg['sw_forum']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_forum" type="radio" value="0" <?php if (@!$cfg['sw_forum']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>����� ������������: </strong></td>
                    <td>
                        <input name="sw_wall" type="radio" value="1" <?php if (@$cfg['sw_wall']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_wall" type="radio" value="0" <?php if (@!$cfg['sw_wall']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>������ �����:</strong></td>
                    <td>
                        <input name="sw_blogs" type="radio" value="1" <?php if (@$cfg['sw_blogs']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_blogs" type="radio" value="0" <?php if (@!$cfg['sw_blogs']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>���������� ���������� ������������:</strong></td>
                    <td>
                        <input name="sw_board" type="radio" value="1" <?php if (@$cfg['sw_board']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_board" type="radio" value="0" <?php if (@!$cfg['sw_board']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>������ ���������:</strong> </td>
                    <td>
                        <input name="sw_msg" type="radio" value="1" <?php if (@$cfg['sw_msg']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_msg" type="radio" value="0" <?php if (@!$cfg['sw_msg']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>����� ����������� � ����� ����������: </strong></td>
                    <td><a href="/includes/letters/newmessage.txt">/includes/letters/newmessage.txt</a></td>
                </tr>
                <tr>
                    <td>
                        <strong>������ ����������� �� �����:</strong><br />
                        <span class="hinttext">������������ ����� �������� ����� ������� ������������ ������ 1 ��� �� ��������� ����� </span>
                    </td>
                    <td valign="top">
                        <input name="karmatime" type="text" id="int_1" size="5" value="<?php echo @(int)$cfg['karmatime']?>"/>
                        <select name="karmaint" id="int_2">
                            <option value="MINUTE"  <?php if(@strstr($cfg['karmaint'], 'MINUTE')) { echo 'selected'; } ?>>�����</option>
                            <option value="HOUR"  <?php if(@strstr($cfg['karmaint'], 'HOUR')) { echo 'selected'; } ?>>�����</option>
                            <option value="DAY" <?php if(@strstr($cfg['karmaint'], 'DAY')) { echo 'selected'; } ?>>����</option>
                            <option value="MONTH" <?php if(@strstr($cfg['karmaint'], 'MONTH')) { echo 'selected'; } ?>>�������</option>
                        </select>
                    </td>
                </tr>
				<tr>
                    <td>
                        <strong>������ �������� ���������� ���������:</strong><br />
                        <span class="hinttext">��������, ���� ������� CRON � ���������� �������� ������</span>
                    </td>
                    <td valign="top">
                        <input name="deltime" type="text" id="deltime" size="5" value="<?php echo @(int)$cfg['deltime']?>"/> �������
                    </td>
                </tr>
            </table>
        </div>

        <div id="avatars">
            <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td><strong>������ ���������� �������: </strong></td>
                    <td><input name="smallw" type="text" id="smallw" size="5" value="<?php echo @$cfg['smallw'];?>"/> ����.</td>
                </tr>
                <tr>
                    <td><strong>������ �������� �������: </strong></td>
                    <td><input name="medw" type="text" id="medw" size="5" value="<?php echo @$cfg['medw'];?>"/> ����.</td>
                </tr>
                <tr>
                    <td><strong>������ �������� �������: </strong></td>
                    <td><input name="medh" type="text" id="medh" size="5" value="<?php echo @$cfg['medh'];?>"/> ����.</td>
                </tr>
            </table>
        </div>


        <div id="proftabs">
            <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td><strong>������� "�����":</strong></td>
                    <td>
                        <input name="sw_feed" type="radio" value="1" <?php if (@$cfg['sw_feed']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_feed" type="radio" value="0" <?php if (@!$cfg['sw_feed']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>������� "�����":</strong></td>
                    <td>
                        <input name="sw_clubs" type="radio" value="1" <?php if (@$cfg['sw_clubs']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_clubs" type="radio" value="0" <?php if (@!$cfg['sw_clubs']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td><strong>������� "�������":</strong></td>
                    <td>
                        <input name="sw_awards" type="radio" value="1" <?php if (@$cfg['sw_awards']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_awards" type="radio" value="0" <?php if (@!$cfg['sw_awards']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
            </table>
        </div>

        <div id="forms">
            <table width="605" border="0" cellspacing="0" cellpadding="10" class="proptable" style="border:none">
                <tr>
                    <td valign="top">
                        <p>��������, ����� ����� ������ �������������� ��� ���������� �������������� � ��������: </p>
                        <p>
                            <select name="privforms[]" size="10" style="width:100%; border:solid 1px silver;" multiple="multiple">
                                <?php
                                if (!isset($cfg['privforms'])) { $cfg['privforms']=array(); }

                                $sql = "SELECT * FROM cms_forms";
                                $rs = dbQuery($sql);

                                if (mysql_num_rows($rs)){
                                    while($f = mysql_fetch_assoc($rs)){
                                        if (in_array($f['id'], $cfg['privforms'])) { $selected='selected="selected"'; } else { $selected = ''; }
                                        echo '<option value="'.$f['id'].'" '.$selected.'>'.$f['title'].'</option>';
                                    }
                                }

                                ?>
                            </select>
                        </p>
                        <p>����� ������� ��������� ����, ��������� CTRL.</p>
                        <p>����� ����� ������������� � ���������� ���������� <a href="index.php?view=components&do=config&id=<?php echo dbGetField('cms_components', "link='forms'", 'id');?>">����������� ����</a>.</p>
                    </td>
                </tr>
            </table>
        </div>

        <div id="photos">
            <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td><strong>�����������: </strong></td>
                    <td width="182">
                        <input name="sw_photo" type="radio" value="1" <?php if (@$cfg['sw_photo']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_photo" type="radio" value="0" <?php if (@!$cfg['sw_photo']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>�������� ������� ����:</strong> <br />
                        <span class="hinttext">���� ��������, �� �� ��� ����������� ���������� ����� ��������� ����������� �� ����� &quot;<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>&quot;</span>
                    </td>
                    <td valign="top">
                        <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="watermark" type="radio" value="0" <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>�������� ���������� � �������:</strong><br />
                        <span class="hinttext">���������� &quot;0&quot; ��� ������������ ����������</span>
                    </td>
                    <td><input name="photosize" type="text" id="photosize" size="5" value="<?php echo @$cfg['photosize'];?>"/> ��.</td>
                </tr>
            </table>
        </div>

        <div id="files">
             <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td><strong>����� ������������: </strong></td>
                    <td width="182">
                        <input name="sw_files" type="radio" value="1" <?php if (@$cfg['sw_files']) { echo 'checked="checked"'; } ?>/> ���
                        <input name="sw_files" type="radio" value="0" <?php if (@!$cfg['sw_files']) { echo 'checked="checked"'; } ?>/> ����
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>�������� ������� ������������ �� �����:</strong><br />
                        <span class="hinttext">���������� &quot;0&quot; ��� ������������ �������</span>
                    </td>
                    <td><input name="filessize" type="text" id="filessize" size="5" value="<?php echo @$cfg['filessize'];?>"/> ��</td>
                </tr>
                <tr>
                    <td>
                        <strong>��������� ���� ������:</strong><br />
                        <span class="hinttext">������� ����� ������� ���������� ��� ��������� ����� ������</span>
                    </td>
                    <td><input name="filestype" type="text" id="filestype" size="30" value="<?php echo $cfg['filestype'] ? $cfg['filestype'] : 'jpeg,gif,png,jpg,bmp,zip,rar,tar';?>"/></td>
                </tr>
            </table>
        </div>

        <div id="reg">
            <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable" style="border:none">
                <tr>
                    <td>
                        <a href="index.php?view=components&do=config&link=registration">������� � ���������� �����������</a>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>

<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>