<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function sendActivationNotice($send_pass, $user_id){
    $inCore = cmsCore::getInstance();
    $inConf = cmsConfig::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $user   = dbGetFields('cms_users', 'id='.$user_id, '*');
    global $_LANG;
    $code = md5($user['email']);
    $codelink = 'http://'.$_SERVER['HTTP_HOST'].'/activate/'.$code;

    $sql = "INSERT cms_users_activate (pubdate, user_id, code)
            VALUES (NOW(), '".$user['id']."', '$code')";
    $inDB->query($sql) or die($_LANG['ERR_SEND_ACTIVATION_MAIL']);
    $user['password'] = $send_pass;
    $letter_path = PATH.'/includes/letters/activation.txt';
    $letter = file_get_contents($letter_path);
    foreach($user as $key=>$value){
        $letter= str_replace('{'.$key.'}', $value, $letter);
    }
    $letter= str_replace('{sitename}', $inConf->sitename, $letter);
    $letter= str_replace('{codelink}', $codelink, $letter);

    $inCore->mailText($user['email'], $_LANG['ACTIVATION_ACCOUNT'].' - '.$inConf->sitename, $letter);

    return true;
}

function registration(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    $inConf     = cmsConfig::getInstance();
    
    $inCore->loadModel('registration');
    $model = new cms_model_registration();

    $inCore->loadModel('users');
    $users_model = new cms_model_users();
	
    global $_LANG;

    $cfg = $inCore->loadComponentConfig('registration');

    //config defaults
    if (!isset($cfg['name_mode'])) { $cfg['name_mode'] = 'nickname'; }
    if (!isset($cfg['first_auth_redirect'])) { $cfg['first_auth_redirect'] = 'profile'; }
    if (!isset($cfg['ask_icq'])) { $cfg['ask_icq'] = 1; }
    if (!isset($cfg['ask_birthdate'])) { $cfg['ask_birthdate'] = 1; }
    if (!isset($cfg['send_greetmsg'])) { $cfg['send_greetmsg'] = 0; }

    //request params
	$id     =   $inCore->request('id', 'int', 0);
	$do     =   $inCore->request('do', 'str', 'view');

//======================================================================================================================//

    if ($do=='sendremind'){

        $inPage->setTitle($_LANG['REMINDER_PASS']);
        $inPage->addPathway($_LANG['REMINDER_PASS'], $_SERVER['REQUEST_URI']);

        echo '<div class="con_heading">'.$_LANG['REMINDER_PASS'].'</div>';

        if (!isset($_POST['goremind'])){
            //PRINT QUERY FORM
            echo '<form name="prform" action="" method="POST">';
            echo '<table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="9"><tr>';
            echo '<td>'.$_LANG['WRITE_REGISTRATION_EMAIL'].': </td>';
            echo '<td><input name="email" type="text" size="25" /></td>';
            echo '<td><input name="goremind" type="submit" value="'.$_LANG['SEND'].'"/></td>';
            echo '</tr></table>';
            echo '</form>';
        } else {
            //SEND NEW PASSWORD TO EMAIL
            $email = $inCore->request('email', 'str');

            if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $email)){
                echo '<p style="color:red">'.$_LANG['ERR_EMAIL'].'</p>';
            } else {

                $sql = "SELECT *, DATE_FORMAT(logdate, '%d-%m-%Y-%H-%i-%s') as logdate
                        FROM cms_users WHERE email = '$email' LIMIT 1";
                $result = $inDB->query($sql) ;

                if ($inDB->num_rows($result)>0){
                    $usr = $inDB->fetch_assoc($result);

                    $usercode = md5($usr['id'] . '-' . $usr['login'] . '-' . $usr['password'] . '-' . $usr['logdate']);
                    $newpass_link = HOST.'/registration/remind/' . $usercode;

                    $mail_message = $_LANG['HELLO'].', ' . $usr['nickname'] . '!'. "\n\n";
                    $mail_message .= $_LANG['REMINDER_TEXT'].' "'.$inConf->sitename.'".' . "\n\n";
                    $mail_message .= $_LANG['YOUR_LOGIN'].': ' .$usr['login']. "\n\n";
                    $mail_message .= $_LANG['NEW_PASS_LINK'].":\n" .$newpass_link . "\n\n";
                    $mail_message .= $_LANG['LINK_EXPIRES']. "\n\n";
                    $mail_message .= $_LANG['SIGNATURE'].', '. $inConf->sitename . ' ('.HOST.').' . "\n";
                    $mail_message .= date('d-m-Y (H:i)');

                    $inCore->mailText($email, $inConf->sitename.' - '.$_LANG['REMINDER_PASS'], $mail_message);
                    echo '<p>'.$_LANG['NEW_PAS_SENDED'].'</p>';

                } else {
                    echo '<p style="color:red">'.$_LANG['ADRESS'].' "'.$email.'" '.$_LANG['NOT_IN_OUR_BASE'].'</p>';
                }

            }
        }

    }

//======================================================================================================================//

    if ($do=='register'){

        $inPage->setTitle($_LANG['REGISTRATION']);

        $msg = '';
		// Проверяем логин и пароль
		$login 	= $inCore->request('login', 'str', '');
		$pass 	= $inCore->request('pass', 'str', '');
		$pass2	= $inCore->request('pass2', 'str', '');

        if(strlen($login)<2) 					{ $msg .= $_LANG['TYPE_LOGIN'].'<br/>'; }
		if ((!preg_match("/^([a-zA-Z0-9])+$/i", $login)) && strlen($login)>=2)	{$msg  .= $_LANG['ERR_LOGIN'].'<br/>'; }
        if(!$pass) 								{ $msg .= $_LANG['TYPE_PASS'].'<br/>'; }
        if($pass && !$pass2) 					{ $msg .= $_LANG['TYPE_PASS_TWICE'].'<br/>'; }
		if($pass && $pass2 && strlen($pass)<6) 	{ $msg .= $_LANG['PASS_SHORT'].'<br/>'; }
		if($pass && $pass2 && $pass != $pass2) 	{ $msg .= $_LANG['WRONG_PASS'].'<br/>'; }

		// Проверяем nickname или имя и фамилию
        if($cfg['name_mode']=='nickname'){
			$nickname = $inCore->request('nickname', 'str', '');
            if(!$nickname) 						{ $msg .= $_LANG['TYPE_NICKNAME'].'<br/>'; }
        } else {
            $namemsg = '';
			$realname1 = $inCore->request('realname1', 'str', '');
			$realname2 = $inCore->request('realname2', 'str', '');
            if(!$realname1) { $namemsg .= $_LANG['TYPE_NAME'].'<br/>'; }
            if(!$realname2) { $namemsg .= $_LANG['TYPE_SONAME'].'<br/>'; }
            if (!$namemsg){
                $nickname = trim($realname1) . ' ' . trim($realname2);
            } else {
                $msg .= $namemsg;
            }
        }
		if($model->getBadNickname($nickname)){
            $msg .= $_LANG['ERR_NICK_EXISTS'].'<br/>';
        }
		// Проверяем email
        $email = $inCore->request('email', 'email');
        if(!$email) {
            $msg  .= $_LANG['ERR_EMAIL'].'<br/>';
        }
		// Если есть опция показывать ДР при регистрации, то проверяем
        if ($cfg['ask_birthdate']){
            $birthdate = (int)$_REQUEST['birthdate']['year'].'-'.(int)$_REQUEST['birthdate']['month'].'-'.(int)$_REQUEST['birthdate']['day'];
        } else { 
            $birthdate = '1980-01-01';
        }
		// Если есть опция показывать icq при регистрации, то проверяем
        if ($cfg['ask_icq']){
            $icq = $inCore->request('icq', 'str', '');
			$icq = preg_replace('/([^0-9])/i', '', $icq);
        } else {
            $icq = '';
        }
		// Проверяем каптчу
		$code = $inCore->request('code', 'str');
        if(!$code) { $msg .= $_LANG['TYPE_CAPTCHA'].'<br/>'; }

        if($msg==''){

            if ($inCore->checkCaptchaCode($code)){
                $sql = "SELECT id, login, email FROM cms_users WHERE (login LIKE '$login' OR email LIKE '$email') AND (is_deleted = 0)";
                $result = $inDB->query($sql) ;
                if($inDB->num_rows($result)==0){

                    $is_locked = $cfg['act'];
                    $send_pass = $pass;
                    $pass = md5($pass);

                    $user_array = array(
                                            'login'=>$login,
                                            'nickname'=>$nickname,
                                            'email'=>$email,
                                            'icq'=>$icq,
                                            'birthdate'=>$birthdate,
                                            'is_locked'=>$is_locked
                                       );

                    cmsCore::callEvent('USER_BEFORE_REGISTER', $user_array);

                    if (cmsUser::sessionGet('invite_code')){

                        $invite_code    = cmsUser::sessionGet('invite_code');
                        $invited_by     = (int)$users_model->getInviteOwner($invite_code);

                        if ($invited_by){ $users_model->closeInvite($invite_code); }

                        cmsUser::sessionDel('invite_code');

                    } else {
                        $invited_by = 0;
                    }

                    $sql = "INSERT INTO cms_users (login, nickname, password, email, icq, regdate, logdate, birthdate, is_locked, invited_by)
                            VALUES ('$login', '$nickname', '$pass', '$email', '$icq', NOW(), NOW(), '$birthdate', '$is_locked', '{$invited_by}')";
                    $inDB->query($sql) ;

                    $new_user_id = dbLastId('cms_users');

                    //create advanced user profile
                    if ($new_user_id){

                        $usr = $inDB->fetch_assoc($result);
                        $sql = "INSERT INTO cms_user_profiles (user_id, city, description, showmail, showbirth, showicq, karma, imageurl, allow_who)
                                VALUES (".$new_user_id.", '', '', '0', '0', '1', '0', '', 'all')";
                        $inDB->query($sql) ;

                        $user_array['id'] = $new_user_id;
                        cmsCore::callEvent('USER_REGISTER', $user_array);

                    }

                    if ($is_locked){
                        sendActivationNotice($send_pass, $new_user_id);
                        $inPage->includeTemplateFile('special/regactivate.php');
                        $inCore->halt();
                    } else {                        
                        $inPage->includeTemplateFile('special/regcomplete.php');

                        if ($cfg['send_greetmsg']){ $model->sendGreetsMessage($new_user_id, $cfg['greetmsg']); }

                        $inCore->halt();
                    }

                } else {
                    $u = $inDB->fetch_assoc($result);
                    if ($login == $u['login']) { $msg .= $_LANG['LOGIN'].' "'.$login.'" '.$_LANG['IS_BUSY']; }
                    elseif ($email == $u['email']) { $msg .= $_LANG['EMAIL_IS_BUSY']; }
                }
            } else {
                $msg = $_LANG['ERR_CAPTCHA'];
            }

        }

    }

//======================================================================================================================//

    if ($do=='view' || @$msg!=''){

        $inPage->setTitle($_LANG['REGISTRATION']);

        $do             = 'view';

        $correct_invite = (cmsUser::sessionGet('invite_code') ? true : false);

        if ($cfg['reg_type']=='invite' && $inCore->inRequest('invite_code')){

            $invite_code    = $inCore->request('invite_code', 'str', '');
            $correct_invite = $users_model->checkInvite($invite_code);

            if ($correct_invite) {
                cmsUser::sessionPut('invite_code', $invite_code);
            } else {
                $msg = $_LANG['INCORRECT_INVITE'];
            }

        }

        $smarty = $inCore->initSmarty('components', 'com_registration.tpl');
            $smarty->assign('cfg', $cfg);
            if(isset($login)){ $smarty->assign('login', $login); }
            if(isset($nickname)){ $smarty->assign('nickname', $nickname); }
            if(isset($realname1)){ $smarty->assign('realname1', $realname1); }
            if(isset($realname2)){ $smarty->assign('realname2', $realname2); }
            if(isset($email)){ $smarty->assign('email', $email); }
            if(isset($icq)){ $smarty->assign('icq', $icq); }
            if(isset($msg)){ $smarty->assign('msg', $msg); }
            $smarty->assign('correct_invite', $correct_invite);
        $smarty->display('com_registration.tpl');

    }

//======================================================================================================================//

    if ($do=='auth'){

        if($inCore->inRequest('is_admin')){
            $back = '/admin/';
        } else {
            $back = ($_SERVER['HTTP_REFERER'] == 'http://'.$_SERVER['HTTP_HOST'].'/login') ? '/' : $inCore->getBackURL();
        }

        if( $inCore->inRequest('logout') ) {
            $inCore->unsetCookie('userid');

            $user_id = $inUser->id;
            $sess_id = session_id();

//            cmsUser::updateStats($user_id);

            cmsCore::callEvent('USER_LOGOUT', $user_id);

            $inDB->query("UPDATE cms_users SET logdate = NOW() WHERE id = ".$user_id);
            $inDB->query("DELETE FROM cms_online WHERE user_id = ".$user_id);
            $inDB->query("DELETE FROM cms_search WHERE session_id = '".$sess_id."'");

            session_unregister('user');
            session_destroy();

            $inUser->dropStatTimer();

            $inCore->redirect('/');
        }

        if( !$inCore->inRequest('logout') ) {

                if (isset($_SESSION['auth_back_url'])){
                    $back = $_SESSION['auth_back_url'];
                    $is_sess_back = true;
                    unset($_SESSION['auth_back_url']);
                } else {
                    $is_sess_back = false;
                }

                if ($inCore->inRequest('login')) { $login = $inCore->request('login', 'str'); }
                if ($inCore->inRequest('pass')) { $passw = $inCore->request('pass', 'str'); }

                if (!$login && !$passw){
                    $_SESSION['auth_back_url'] = $back;

                    $inPage->setTitle($_LANG['SITE_LOGIN']);
                    $inPage->addPathway($_LANG['SITE_LOGIN']);

                    $smarty = $inCore->initSmarty('components', 'com_registration_login.tpl');
                    $smarty->assign('cfg', $cfg);
                    $smarty->assign('is_sess_back', $is_sess_back);
                    $smarty->display('com_registration_login.tpl');
                    return;
                }

                $remember_pass = $inCore->inRequest('remember');

                if (!preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $login)){
                    $where_login = "login = '{$login}'";
                } else {
                    $where_login = "email = '{$login}'";
                }

                $sql    = "SELECT * 
                           FROM cms_users
                           WHERE $where_login AND password = md5('$passw') AND is_deleted = 0 AND is_locked = 0";
                $result = $inDB->query($sql);

                if($inDB->num_rows($result)==1) {
                    $current_ip     = $_SERVER['REMOTE_ADDR'];
                    $user           = $inDB->fetch_assoc($result);

                    if (!cmsUser::isBanned($user['id'])) {

                        $_SESSION['user'] = cmsUser::createUser($user);
                        
                        cmsCore::callEvent('USER_LOGIN', $_SESSION['user']);

                        if ($remember_pass){
                            $cookie_code = md5($user['id'] . $user['password']);
                            $inCore->setCookie('userid', $cookie_code, time()+60*60*24*30);
                        }
                        
                    } else {
                        $inDB->query("UPDATE cms_banlist SET ip = '$current_ip' WHERE user_id = ".$user['id']." AND status = 1");
                    }

                    $inUser->dropStatTimer();

                    //cmsUser::updateStats($user['id']);

                    $first_time_auth = ($user['logdate']=='0000-00-00 00:00:00' || intval($user['logdate']==0));

                    $inDB->query("UPDATE cms_users SET logdate = NOW(), last_ip = '$current_ip' WHERE id = ".$user['id']) ;

                    $cfg = $inCore->loadComponentConfig('registration');
                    if (!isset($cfg['auth_redirect']))          {  $cfg['auth_redirect'] = 'index';            }
                    if (!isset($cfg['first_auth_redirect']))    {  $cfg['first_auth_redirect'] = 'profile';    }

                    if (!$inCore->userIsAdmin($user['id']) && !$is_sess_back){
                        if ($first_time_auth) { $cfg['auth_redirect'] = $cfg['first_auth_redirect']; }
                        switch($cfg['auth_redirect']){
                            case 'none': $url = $back; break;
                            case 'index': $url = '/'; break;
                            case 'profile': $url = cmsUser::getProfileURL($user['login']); break;
                            case 'editprofile': $url = '/users/'.$user['id'].'/editprofile.html'; break;
                        }
                    } else { $url = $back; }

                    //Редиректим назад
                    $inCore->redirect($url);

                } else {
                    $inCore->redirect('/auth/error.html');
                }

            }

    }

//======================================================================================================================//

    if ($do=='activate'){

        $code = $inCore->request('code', 'str', '');

        if (!$code) { $inCore->redirect('/'); }

        $user_id = $inDB->get_field('cms_users_activate', "code = '$code'", 'user_id');
        
        if ($user_id){

            $sql = "UPDATE cms_users SET is_locked = 0 WHERE id = $user_id";
            $inDB->query($sql) or die($_LANG['ERR_ACTIVATION']);

            $sql = "DELETE FROM cms_users_activate WHERE code = '$code'";
            $inDB->query($sql) or die($_LANG['ERR_ACTIVATION']);

            cmsCore::callEvent('USER_ACTIVATED', $user_id);

            if ($cfg['send_greetmsg']){ $model->sendGreetsMessage($user_id, $cfg['greetmsg']); }

            $inPage->includeTemplateFile('special/regcomplete.php');
            $inCore->halt();
            
        } else {
            $inCore->redirect('/');
        }

    }

//======================================================================================================================//

    if ($do=='autherror'){

        $inPage->includeTemplateFile('special/autherror.php');
        $inCore->halt();

    }

//======================================================================================================================//

    if ($do=='remind'){

        $usercode = $inCore->request('code', 'str', '');

        //проверяем формат кода
        if (!preg_match('/^([a-z0-9]{32})$/i', $usercode)) { $inCore->halt(); }

        //ищем пользователя по коду
        $sql = "SELECT * FROM cms_users
                WHERE MD5(CONCAT(id,'-',login,'-',password,'-',DATE_FORMAT(logdate, '%d-%m-%Y-%H-%i-%s'))) = '{$usercode}'";
        $result = $inDB->query($sql);

        //если пользователь не найден, редирект на главную
        if (!$inDB->num_rows($result)){ $inCore->redirect('/'); }

        //получаем пользователя
        $user = $inDB->fetch_assoc($result);

        $errors = '';

        $is_changed = false;

        if ($inCore->inRequest('submit')){

            $pass   = $inCore->request('pass', 'str', '');
            $pass2  = $inCore->request('pass2', 'str', '');
            if (!$pass) { $errors .= $_LANG['TYPE_PASS'].'<br/>'; }
            if (!$pass2) { $errors .= $_LANG['TYPE_PASS_TWICE'].'<br/>'; }
            if ($pass != $pass2) { $errors .= $_LANG['WRONG_PASS'].'<br/>'; }

            if (!$errors){
                $pass = md5($pass);
                $inDB->query("UPDATE cms_users SET password = '{$pass}', logdate = NOW() WHERE id = {$user['id']}");
                $is_changed = true;
            }
            
        }

        $inPage->backButton(false);
        $inPage->setTitle($_LANG['RECOVER_PASS']);
        $smarty = $inCore->initSmarty('components', 'com_registration_remind.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('user', $user);
        $smarty->assign('errors', $errors);
        $smarty->assign('is_changed', $is_changed);
        $smarty->display('com_registration_remind.tpl');

    }

//======================================================================================================================//

}
?>
