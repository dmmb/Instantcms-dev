<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_invite($module_id){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inConf = cmsConfig::getInstance();

    global $_LANG;

    $errors     = '';
    $success    = '';

    if ($inCore->inRequest('send_invite_email')){

        $username   = $inCore->request('username', 'str', '');
        $email      = $inCore->request('friend_email', 'str', '');

        if (!$username && !$inUser->id){ $errors .= $_LANG['ERR_NEED_NAME'].'<br/>'; }
        if ($username==$_LANG['YOUR_NAME'] && !$inUser->id){ $errors .= $_LANG['ERR_NEED_NAME'].'<br/>'; }

        if ($inUser->id) { $username = $inUser->nickname; }

        if ($email==$_LANG['FRIEND_EMAIL'] || !$email){ $errors .= $_LANG['ERR_NEED_MAIL'].'<br/>'; }

        if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $email)){
            $errors .= $_LANG['ERR_WRONG_MAIL'].'<br/>';
        }

        if (!$errors){

            $letter_path    = PATH.'/includes/letters/mail_invite.txt';
            $letter         = file_get_contents($letter_path);

            $letter = str_replace('{sitename}', $inConf->sitename, $letter);
            $letter = str_replace('{site_url}', HOST, $letter);
            $letter = str_replace('{username}', $username, $letter);

            $inCore->mailText($email, sprintf($_LANG['INVITE_SUBJECT'], $username), $letter);

            $success = $_LANG['INVITE_SENDED'];

        }

    }

    $smarty = $inCore->initSmarty('modules', 'mod_invite.tpl');
    $smarty->assign('user_id', $inUser->id);
    $smarty->assign('errors', $errors);
    $smarty->assign('success', $success);
    $smarty->assign('LANG', $_LANG);
    $smarty->display('mod_invite.tpl');

    return true;

}
    
?>