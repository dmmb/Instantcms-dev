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

cpAddPathway('�����������', '?view=components&do=config&id='.$_REQUEST['id']);

echo '<h3>�����������</h3>';

if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

$toolmenu = array();

$toolmenu[0]['icon'] = 'save.gif';
$toolmenu[0]['title'] = '���������';
$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

$toolmenu[1]['icon'] = 'cancel.gif';
$toolmenu[1]['title'] = '������';
$toolmenu[1]['link'] = '?view=components';

cpToolMenu($toolmenu);

//LOAD CURRENT CONFIG
$cfg = $inCore->loadComponentConfig('registration');

//CONFIG DEFAULTS
if (!isset($cfg['is_on'])) { $cfg['is_on'] = 1; }
if (!isset($cfg['auth_redirect'])) { $cfg['auth_redirect'] = 'none'; }
if (!isset($cfg['name_mode'])) { $cfg['name_mode'] = 'nickname'; }
if (!isset($cfg['first_auth_redirect'])) { $cfg['first_auth_redirect'] = 'profile'; }
if (!isset($cfg['ask_icq'])) { $cfg['ask_icq'] = 1; }
if (!isset($cfg['ask_birthdate'])) { $cfg['ask_birthdate'] = 1; }

//SAVE CONFIG
if($opt=='saveconfig'){	
    $cfg = array();

    $cfg['is_on'] = $_REQUEST['is_on'];
    $cfg['act'] = $_REQUEST['act'];
    $cfg['send'] = $_REQUEST['send'];
    $cfg['offmsg'] = $_REQUEST['offmsg'];
    $cfg['himsg'] = $_REQUEST['himsg'];
    $cfg['first_auth_redirect'] = $_REQUEST['first_auth_redirect'];
    $cfg['auth_redirect'] = $_REQUEST['auth_redirect'];

    $cfg['name_mode'] = $_REQUEST['name_mode'];
    $cfg['ask_icq'] = $_REQUEST['ask_icq'];
    $cfg['ask_birthdate'] = $_REQUEST['ask_birthdate'];

    $inCore->saveComponentConfig('registration', $cfg);

    $msg = '��������� ���������.';
}

//SHOW CONFIG FORM
if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td width="308"><strong>����������� ��������: </strong></td>
            <td width="313">
                <input name="is_on" type="radio" value="1" <?php if (@$cfg['is_on']) { echo 'checked="checked"'; } ?>/> ��
                <input name="is_on" type="radio" value="0" <?php if (@!$cfg['is_on']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>��������� ��� ����������� �����������:</strong> </td>
            <td valign="top"><textarea  name="offmsg" type="text" id="offmsg" rows="2" style="border: solid 1px gray;width:300px;"><?php echo @$cfg['offmsg'];?></textarea></td>
        </tr>
        <tr>
            <td><strong>��������� ������� ������� �� e-mail: </strong></td>
            <td>
                <input name="act" type="radio" value="1" <?php if (@$cfg['act']) { echo 'checked="checked"'; } ?>/> ��
                <input name="act" type="radio" value="0" <?php if (@!$cfg['act']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������ � ����������� �� ���������:</strong> </td>
            <td><a href="/includes/letters/activation.txt">/includes/letters/activation.txt</a></td>
        </tr>
        <tr>
            <td><strong>����� ������ ����������� �� �����:</strong></td>
            <td>
                <select name="first_auth_redirect" id="first_auth_redirect" style="width:300px">
                    <option value="none" <?php if (@$cfg['first_auth_redirect']=='none') {echo 'selected';} ?>>������ �� ������</option>
                    <option value="index" <?php if (@$cfg['first_auth_redirect']=='index') {echo 'selected';} ?>>������� ������� ��������</option>
                    <option value="profile" <?php if (@$cfg['first_auth_redirect']=='profile') {echo 'selected';} ?>>������� �������</option>
                    <option value="editprofile" <?php if (@$cfg['first_auth_redirect']=='editprofile') {echo 'selected';} ?>>������� ��������� �������</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>����� ��������� ����������� �� �����:</strong></td>
            <td>
                <select name="auth_redirect" id="auth_redirect" style="width:300px">
                    <option value="none" <?php if (@$cfg['auth_redirect']=='none') {echo 'selected';} ?>>������ �� ������</option>
                    <option value="index" <?php if (@$cfg['auth_redirect']=='index') {echo 'selected';} ?>>������� ������� ��������</option>
                    <option value="profile" <?php if (@$cfg['auth_redirect']=='profile') {echo 'selected';} ?>>������� �������</option>
                    <option value="editprofile" <?php if (@$cfg['auth_redirect']=='editprofile') {echo 'selected';} ?>>������� ��������� �������</option>
                </select>
            </td>
        </tr>
    </table>
    <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td width="308"><strong>������ ����� �������������:</strong></td>
            <td>
                <select name="name_mode" id="name_mode" style="width:300px">
                    <option value="nickname" <?php if (@$cfg['name_mode']=='nickname') {echo 'selected';} ?>>�������</option>
                    <option value="realname" <?php if (@$cfg['name_mode']=='realname') {echo 'selected';} ?>>��� � �������</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>����������� ICQ ��� �����������:</strong> </td>
            <td>
                <input name="ask_icq" type="radio" value="1" <?php if (@$cfg['ask_icq']) { echo 'checked="checked"'; } ?>/> ��
                <input name="ask_icq" type="radio" value="0" <?php if (@!$cfg['ask_icq']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>����������� ���� �������� ��� �����������:</strong> </td>
            <td>
                <input name="ask_birthdate" type="radio" value="1" <?php if (@$cfg['ask_birthdate']) { echo 'checked="checked"'; } ?>/> ��
                <input name="ask_birthdate" type="radio" value="0" <?php if (@!$cfg['ask_birthdate']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
    </table>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>