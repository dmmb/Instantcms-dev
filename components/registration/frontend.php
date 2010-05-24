<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
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
    
    global $_LANG;

    $cfg = $inCore->loadComponentConfig('registration');

    //config defaults
    if (!isset($cfg['name_mode'])) { $cfg['name_mode'] = 'nickname'; }
    if (!isset($cfg['first_auth_redirect'])) { $cfg['first_auth_redirect'] = 'profile'; }
    if (!isset($cfg['ask_icq'])) { $cfg['ask_icq'] = 1; }
    if (!isset($cfg['ask_birthdate'])) { $cfg['ask_birthdate'] = 1; }

    //request params
    if (isset($_REQUEST['id'])){ if(is_numeric($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { die('HACKING ATTEMPT BLOCKED'); } } else { $id = 0; }
    if (isset($_REQUEST['do'])){ $do = htmlentities($_REQUEST['do'], ENT_QUOTES); } else { $do = 'view'; 	}

//======================================================================================================================//

    if ($do=='passremind'){

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
            $email = $_POST['email'];

            if (!preg_match("/^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$/i", $email)){
                echo '<p style="color:red">'.$_LANG['ERR_EMAIL'].'</p>';
            } else {

                $sql = "SELECT * FROM cms_users WHERE email = '$email' LIMIT 1";
                $result = $inDB->query($sql) ;

                if ($inDB->num_rows($result)>0){
                    $usr = $inDB->fetch_assoc($result);

                    $newpassword = substr(md5(microtime()) . md5(rand(0, 9999)), 0, 8);
                    $inDB->query("UPDATE cms_users SET password = '".md5($newpassword)."' WHERE id = ".$usr['id']) ;

                    $mail_message = $_LANG['HELLO'].', ' . $usr['nickname'] . '!'. "\n\n";
                    $mail_message .= $_LANG['REMINDER_TEXT'].' "'.$inConf->sitename.'".' . "\n\n";
                    $mail_message .= $_LANG['OUR_PASS_IS_MD5'] . "\n";
                    $mail_message .= $_LANG['OUR_PASS_IS_MD5_TEXT'] . "\n\n";
                    $mail_message .= '########## '.$_LANG['YOUR_LOGIN'].': ' .$usr['login']. "\n\n";
                    $mail_message .= '########## '.$_LANG['YOUR_NEW_PASS'].': ' .$newpassword . "\n\n";
                    $mail_message .= $_LANG['YOU_CAN_CHANGE_PASS']."\n";
                    $mail_message .= $_LANG['IN_CONFIG_PROFILE'].': '. cmsUser::getProfileURL($usr['login']) . "\n\n";
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
        if(strlen($inCore->request('login'))>=2) { $login = $inCore->request('login', 'str', ''); } else { $msg .= $_LANG['TYPE_LOGIN'].'<br/>'; }
        if($inCore->request('pass')) { $pass = $inCore->request('pass', 'str', ''); } else { $msg .= $_LANG['TYPE_PASS'].'<br/>'; }
        if($inCore->request('pass2')) { $pass2 = $inCore->request('pass2', 'str', ''); } else { $msg .= $_LANG['TYPE_PASS_TWICE'].'<br/>'; }

        if (!preg_match("/^[a-zA-Z0-9]+\$/i", $login)){
            $msg  .= $_LANG['ERR_LOGIN'].'<br/>';
        }

        if($cfg['name_mode']=='nickname'){
            if($inCore->request('nickname', 'str', '')) { $nickname = $inCore->request('nickname', 'str', ''); } else { $msg .= $_LANG['TYPE_NICKNAME'].'<br/>'; }
        } else {
            $namemsg = '';
            if($inCore->request('realname1', 'str', '')) { $realname1 = $inCore->request('realname1', 'str', ''); } else { $namemsg .= $_LANG['TYPE_NAME'].'<br/>'; }
            if($inCore->request('realname2', 'str', '')) { $realname2 = $inCore->request('realname2', 'str', ''); } else { $namemsg .= $_LANG['TYPE_SONAME'].'<br/>'; }
            if (!$namemsg){
                $nickname = trim($realname1) . ' ' . trim($realname2);
            } else {
                $msg .= $namemsg;
            }
        }

        if(!$inCore->inRequest('email')) {
            $msg .= $_LANG['TYPE_EMAIL'].'<br/>';
        }

        if($inCore->inRequest('email')) {
            $email = $inCore->request('email', 'str', '');
            if (!preg_match("/^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$/i", $email)){
                $msg  .= $_LANG['ERR_EMAIL'].'<br/>';
            }
        }

        if ($cfg['ask_birthdate']){
            $birthdate = (int)$_REQUEST['birthdate']['year'].'-'.(int)$_REQUEST['birthdate']['month'].'-'.(int)$_REQUEST['birthdate']['day'];
        } else { 
            $birthdate = '1980-01-01';
        }

        if ($cfg['ask_icq']){
            $icq = $inCore->request('icq', 'str', '');
        } else {
            $icq = '';
        }

        if($_REQUEST['code']) { $code = $_REQUEST['code']; } else { $msg .= $_LANG['TYPE_CAPTCHA'].'<br/>'; }
        if(@$pass != @$pass2) { $msg .= $_LANG['WRONG_PASS'].'<br/>'; }

        if($inDB->rows_count('cms_users', 'LOWER(nickname) LIKE "'.strtolower($nickname).'"', 1)){
            $msg .= $_LANG['ERR_NICK_EXISTS'].'<br/>';
        }

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

                    $sql = "INSERT INTO cms_users (login, nickname, password, email, icq, regdate, logdate, birthdate, is_locked)
                            VALUES ('$login', '$nickname', '$pass', '$email', '$icq', NOW(), NOW(), '$birthdate', '$is_locked')";
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
                        $inCore->halt();
                    }

                } else {
                    $u = $inDB->fetch_assoc($result);
                    if ($login == $u['login']) { $msg .= $_LANG['LOGIN'].' "'.$login.'" '.$_LANG['IS_BUSY']; }
                    else { $msg .= $_LANG['EMAIL_IS_BUSY']; }
                }
            } else {
                $msg = $_LANG['ERR_CAPTCHA'];
            }

        }

    }

//======================================================================================================================//

    if ($do=='view' || @$msg!=''){

        $inPage->setTitle($_LANG['REGISTRATION']);

        $do = 'view';
        echo '<div class="con_heading">'.$_LANG['REGISTRATION'].'</div>';

        if ($cfg['is_on']){

            $inPage->addHeadJS('components/registration/js/check.js');

            if (isset($msg)) { if($msg!='') { echo '<p><font color="red">'.$msg.'</font></p>'; } }           

            $smarty = $inCore->initSmarty('components', 'com_registration.tpl');
                $smarty->assign('cfg', $cfg);
                if(isset($login)){ $smarty->assign('login', $login); }
                if(isset($nickname)){ $smarty->assign('nickname', $nickname); }
                if(isset($realname1)){ $smarty->assign('realname1', $realname1); }
                if(isset($realname2)){ $smarty->assign('realname2', $realname2); }
                if(isset($email)){ $smarty->assign('email', $email); }
                if(isset($icq)){ $smarty->assign('icq', $icq); }
            $smarty->display('com_registration.tpl');

        } else {
            echo '<div style="margin-top:10px">'.$cfg['offmsg'].'</div>';
        }

    }

//======================================================================================================================//

    if ($do=='auth'){

        if($inCore->inRequest('is_admin')){
            $back = '/admin/';
        } else {
            $back = $inCore->getBackURL();
        }

        if( $inCore->inRequest('logout') ) {
            $inCore->unsetCookie('userid');

            $user_id = $inUser->id;
            $sess_id = session_id();

            cmsUser::updateStats($user_id);

            cmsCore::callEvent('USER_LOGOUT', $user_id);

            $inDB->query("UPDATE cms_users SET logdate = NOW() WHERE id = ".$user_id);
            $inDB->query("DELETE FROM cms_online WHERE user_id = ".$user_id);
            $inDB->query("DELETE FROM cms_search WHERE session_id = '".$sess_id."'");

            session_destroy();

            $inUser->dropStatTimer();

            $inCore->redirect('/');
        }

        if( !$inCore->inRequest('logout') ) {
            
                if ($inCore->inRequest('login')) { $login = $inCore->request('login', 'str'); } else { $inCore->redirect($back); }
                if ($inCore->inRequest('pass')) { $passw = $inCore->request('pass', 'str'); } else { $inCore->redirect($back); }

                $remember_pass = $inCore->inRequest('remember');

                if (!preg_match("/^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$/i", $login)){
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

                    cmsUser::updateStats($user['id']);

                    $first_time_auth = ($user['logdate']=='0000-00-00 00:00:00' || intval($user['logdate']==0));

                    $inDB->query("UPDATE cms_users SET logdate = NOW(), last_ip = '$current_ip' WHERE id = ".$user['id']) ;

                    $cfg = $inCore->loadComponentConfig('registration');
                    if (!isset($cfg['auth_redirect']))          {  $cfg['auth_redirect'] = 'index';            }
                    if (!isset($cfg['first_auth_redirect']))    {  $cfg['first_auth_redirect'] = 'profile';    }

                    if (!$inCore->userIsAdmin($user['id'])){
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

}
?>
