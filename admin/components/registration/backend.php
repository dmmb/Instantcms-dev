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


cpAddPathway('Регистрация', '?view=components&do=config&id='.$_REQUEST['id']);

echo '<h3>Регистрация</h3>';

if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

$toolmenu = array();

$toolmenu[0]['icon'] = 'save.gif';
$toolmenu[0]['title'] = 'Сохранить';
$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

$toolmenu[1]['icon'] = 'cancel.gif';
$toolmenu[1]['title'] = 'Отмена';
$toolmenu[1]['link'] = '?view=components';

cpToolMenu($toolmenu);

$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';


//LOAD CURRENT CONFIG
$cfg = $inCore->loadComponentConfig('registration');

//CONFIG DEFAULTS
if (!isset($cfg['reg_type'])) { $cfg['reg_type'] = 'open'; }
if (!isset($cfg['inv_count'])) { $cfg['inv_count'] = 5; }
if (!isset($cfg['inv_karma'])) { $cfg['inv_karma'] = 50; }
if (!isset($cfg['inv_period'])) { $cfg['inv_period'] = 'WEEK'; }

if (!isset($cfg['default_gid'])) { $cfg['default_gid'] = 1; }

if (!isset($cfg['is_on'])) { $cfg['is_on'] = 1; }
if (!isset($cfg['auth_redirect'])) { $cfg['auth_redirect'] = 'none'; }
if (!isset($cfg['name_mode'])) { $cfg['name_mode'] = 'nickname'; }
if (!isset($cfg['first_auth_redirect'])) { $cfg['first_auth_redirect'] = 'profile'; }
if (!isset($cfg['ask_icq'])) { $cfg['ask_icq'] = 1; }
if (!isset($cfg['ask_birthdate'])) { $cfg['ask_birthdate'] = 1; }
if (!isset($cfg['badnickname'])) { $cfg['badnickname'] = "администратор\nадмин\nqwert\nqwerty\n123\nadmin\nвася пупкин"; }

if (!isset($cfg['send_greetmsg'])) { $cfg['send_greetmsg'] = 0; }

//SAVE CONFIG
if($opt=='saveconfig'){
    
    $cfg = array();

    $cfg['reg_type']    = $inCore->request('reg_type', 'str');
    $cfg['inv_count']   = $inCore->request('inv_count', 'int');
    $cfg['inv_karma']   = $inCore->request('inv_karma', 'int');
    $cfg['inv_period']  = $inCore->request('inv_period', 'str');

    $cfg['default_gid'] = $inCore->request('default_gid', 'int');

    $cfg['is_on']       = $inCore->request('is_on', 'int');
    $cfg['act']         = $inCore->request('act', 'int');
    $cfg['send']        = $inCore->request('send', 'int');
    $cfg['offmsg']      = $inCore->request('offmsg', 'html');
    
    $cfg['first_auth_redirect'] = $inCore->request('first_auth_redirect', 'str');
    $cfg['auth_redirect']       = $inCore->request('auth_redirect', 'str');

    $cfg['name_mode']       = $inCore->request('name_mode', 'str');
	$cfg['badnickname']     = trim(mb_strtolower($inCore->request('badnickname', 'html')));
    $cfg['ask_icq']         = $inCore->request('ask_icq', 'int');
    $cfg['ask_birthdate']   = $inCore->request('ask_birthdate', 'int');

    $cfg['send_greetmsg']   = $inCore->request('send_greetmsg', 'int');
    $cfg['greetmsg']        = $inCore->request('greetmsg', 'html');

    $inCore->saveComponentConfig('registration', $cfg);

    if ($inCore->request('inv_now', 'int', 0)){

        $inCore->loadModel('users');
        $model = new cms_model_users();

        $inv_count = $cfg['inv_count'];
        $inv_karma = $cfg['inv_karma'];

        if ($inv_count){
            $invites_given = $model->giveInvites($inv_count, $inv_karma);

            if ($invites_given){
                cmsUser::sessionPut('reg_msg', 'Выдано инвайтов: '.$invites_given);
            } else {
                cmsUser::sessionPut('reg_msg', 'Инвайты не выданы: нет подходящих пользователей');
            }
        }

    }

    if ($inCore->request('inv_delete', 'int', 0)){

        $inCore->loadModel('users');
        $model = new cms_model_users();

        $model->deleteInvites();

        cmsUser::sessionPut('reg_msg', 'Неиспользованные инвайты удалены');

    }

    $inCore->redirectBack();

}

$msg = cmsUser::sessionGet('reg_msg');

if ($msg) { echo '<p class="success">'.$msg.'</p>'; cmsUser::sessionDel('reg_msg'); }
?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="optform">

<div id="config_tabs" style="margin-top:12px;">

    <ul id="tabs">
        <li><a href="#basic"><span>Общие</span></a></li>
        <li><a href="#form"><span>Форма</span></a></li>
        <li><a href="#greets"><span>Приветствие</span></a></li>
    </ul>

    <div id="basic">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
            <tr>
                <td width="110"><strong>Тип регистрации:</strong></td>
                <td>
                    <select name="reg_type" id="name_mode" style="width:300px" onchange="if($(this).val()=='invite'){ $('.inv').show(); } else { $('.inv').hide(); }">
                        <option value="open" <?php if (@$cfg['reg_type']=='open') {echo 'selected';} ?>>открытая</option>
                        <option value="invite" <?php if (@$cfg['reg_type']=='invite') {echo 'selected';} ?>>по инвайтам</option>
                    </select>
                </td>
            </tr>
            <tr class="inv" <?php if($cfg['reg_type']=='open'){ ?>style="display:none"<?php } ?>>
                <td valign="top" style="padding-top:20px"><strong>Выдавать по:</strong></td>
                <td>
                    <table cellpadding="4" cellspacing="0" border="0">
                        <tr>
                            <td style="padding-left:0px;">
                                <input type="text" style="width:30px" name="inv_count" value="<?php echo $cfg['inv_count']; ?>">
                            </td>
                            <td> инвайтов пользователям с кармой &ge; </td>
                            <td>
                                <input type="text" style="width:30px" name="inv_karma" value="<?php echo $cfg['inv_karma']; ?>">
                            </td>
                            <td> один раз в </td>
                            <td>
                                <select name="inv_period">
                                    <option value="DAY" <?php if (@$cfg['inv_period']=='DAY') {echo 'selected';} ?>>день</option>
                                    <option value="WEEK" <?php if (@$cfg['inv_period']=='WEEK') {echo 'selected';} ?>>неделю</option>
                                    <option value="MONTH" <?php if (@$cfg['inv_period']=='MONTH') {echo 'selected';} ?>>месяц</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:0px;" colspan="5">
                                <input type="hidden" id="inv_now" name="inv_now" value="0" />
                                <input type="hidden" id="inv_delete" name="inv_delete" value="0" />
                                <input type="button" value="Выдать сейчас" onclick="if(confirm('Выдать инвайты?')){ $('#inv_now').val('1'); $('#optform').submit(); }" />
                                <input type="button" value="Удалить неиспользованные" onclick="if(confirm('Удалить инвайты?')){ $('#inv_delete').val('1'); $('#optform').submit(); }" />
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
            <tr>
                <td width="308"><strong>Регистрация включена: </strong></td>
                <td width="313">
                    <input name="is_on" type="radio" value="1" <?php if (@$cfg['is_on']) { echo 'checked="checked"'; } ?>/> Да
                    <input name="is_on" type="radio" value="0" <?php if (@!$cfg['is_on']) { echo 'checked="checked"'; } ?>/> Нет
                </td>
            </tr>
            <tr>
                <td valign="top"><strong>Сообщение при выключенной регистрации:</strong> </td>
                <td valign="top"><textarea  name="offmsg" type="text" id="offmsg" rows="2" style="border: solid 1px gray;width:300px;"><?php echo @$cfg['offmsg'];?></textarea></td>
            </tr>
            <tr>
                <td><strong>Активация учетных записей по e-mail: </strong></td>
                <td>
                    <input name="act" type="radio" value="1" <?php if (@$cfg['act']) { echo 'checked="checked"'; } ?>/> Да
                    <input name="act" type="radio" value="0" <?php if (@!$cfg['act']) { echo 'checked="checked"'; } ?>/> Нет
                </td>
            </tr>
            <tr>
                <td><strong>Письмо с инструкцией по активации:</strong> </td>
                <td><a href="/includes/letters/activation.txt">/includes/letters/activation.txt</a></td>
            </tr>
            <tr>
                <td><strong>Группа пользователей по-умолчанию:</strong></td>
                <td>
                    <?php $groups = cmsUser::getGroups(true); ?>
                    <select name="default_gid" id="default_gid" style="width:300px">
                        <?php foreach($groups as $group){ ?>
                        <option value="<?php echo $group['id']; ?>" <?php if ($cfg['default_gid']==$group['id']){ ?>selected="selected"<?php } ?>><?php echo $group['title']; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>После первой авторизации на сайте:</strong></td>
                <td>
                    <select name="first_auth_redirect" id="first_auth_redirect" style="width:300px">
                        <option value="none" <?php if (@$cfg['first_auth_redirect']=='none') {echo 'selected';} ?>>ничего не делать</option>
                        <option value="index" <?php if (@$cfg['first_auth_redirect']=='index') {echo 'selected';} ?>>открыть главную страницу</option>
                        <option value="profile" <?php if (@$cfg['first_auth_redirect']=='profile') {echo 'selected';} ?>>открыть профиль</option>
                        <option value="editprofile" <?php if (@$cfg['first_auth_redirect']=='editprofile') {echo 'selected';} ?>>открыть настройки профиля</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><strong>После следующих авторизаций на сайте:</strong></td>
                <td>
                    <select name="auth_redirect" id="auth_redirect" style="width:300px">
                        <option value="none" <?php if (@$cfg['auth_redirect']=='none') {echo 'selected';} ?>>ничего не делать</option>
                        <option value="index" <?php if (@$cfg['auth_redirect']=='index') {echo 'selected';} ?>>открыть главную страницу</option>
                        <option value="profile" <?php if (@$cfg['auth_redirect']=='profile') {echo 'selected';} ?>>открыть профиль</option>
                        <option value="editprofile" <?php if (@$cfg['auth_redirect']=='editprofile') {echo 'selected';} ?>>открыть настройки профиля</option>
                    </select>
                </td>
            </tr>
        </table>
    </div>

    <div id="form">
        <table width="661" border="0" cellpadding="10" cellspacing="0" class="proptable">
            <tr>
                <td width="308"><strong>Формат имени пользователей:</strong></td>
                <td>
                    <select name="name_mode" id="name_mode" style="width:300px">
                        <option value="nickname" <?php if (@$cfg['name_mode']=='nickname') {echo 'selected';} ?>>никнейм</option>
                        <option value="realname" <?php if (@$cfg['name_mode']=='realname') {echo 'selected';} ?>>имя и фамилия</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="top"><strong>Запрещенные никнеймы или имена и фамилии:</strong><br />Введите запрещенные никнеймы или имена и фамилии, каждое слово или словосочетание с новой строки.</td>
                <td valign="top"><textarea  name="badnickname" type="text" id="badnickname" rows="5" style="border: solid 1px gray;width:300px;"><?php echo @$cfg['badnickname'];?></textarea></td>
            </tr>
            <tr>
                <td><strong>Запрашивать ICQ при регистрации:</strong> </td>
                <td>
                    <input name="ask_icq" type="radio" value="1" <?php if (@$cfg['ask_icq']) { echo 'checked="checked"'; } ?>/> Да
                    <input name="ask_icq" type="radio" value="0" <?php if (@!$cfg['ask_icq']) { echo 'checked="checked"'; } ?>/> Нет
                </td>
            </tr>
            <tr>
                <td><strong>Запрашивать дату рождения при регистрации:</strong> </td>
                <td>
                    <input name="ask_birthdate" type="radio" value="1" <?php if (@$cfg['ask_birthdate']) { echo 'checked="checked"'; } ?>/> Да
                    <input name="ask_birthdate" type="radio" value="0" <?php if (@!$cfg['ask_birthdate']) { echo 'checked="checked"'; } ?>/> Нет
                </td>
            </tr>
        </table>
    </div>

    <div id="greets">
        <table width="800" border="0" cellpadding="10" cellspacing="0" class="proptable">
            <tr>
                <td width="308"><strong>Отправлять личное сообщение после регистрации:</strong></td>
                <td>
                    <input name="send_greetmsg" type="radio" value="1" <?php if (@$cfg['send_greetmsg']) { echo 'checked="checked"'; } ?>/> Да
                    <input name="send_greetmsg" type="radio" value="0" <?php if (@!$cfg['send_greetmsg']) { echo 'checked="checked"'; } ?>/> Нет
                </td>
            </tr>
        </table>
        <?php $inCore->insertEditor('greetmsg', $cfg['greetmsg'], '300', '800'); ?>
    </div>

</div>

<p>
    <input name="opt" type="hidden" value="saveconfig" />
    <input name="save" type="submit" id="save" value="Сохранить" />
    <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
</p>
</form>

<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>