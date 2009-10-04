<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
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

    $code = md5($user['email']);
    $codelink = 'http://'.$_SERVER['HTTP_HOST'].'/activate/'.$code;

    $sql = "INSERT cms_users_activate (pubdate, user_id, code)
            VALUES (NOW(), '".$user['id']."', '$code')";
    $inDB->query($sql) or die('Ошибка отправки письма активации!');
    $user['password'] = $send_pass;
    $letter_path = PATH.'/includes/letters/activation.txt';
    $letter = file_get_contents($letter_path);
    foreach($user as $key=>$value){
        $letter= str_replace('{'.$key.'}', $value, $letter);
    }
    $letter= str_replace('{sitename}', $inConf->sitename, $letter);
    $letter= str_replace('{codelink}', $codelink, $letter);

    $inCore->mailText($user['email'], 'Активация аккаунта - '.$inConf->sitename, $letter);

    return true;
}

function registration(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    
    global $_CFG;

    $menuid = $inCore->menuId();    
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

        $inPage->setTitle('Напоминание пароля');
        $inPage->addPathway('Напоминание пароля', $_SERVER['REQUEST_URI']);

        echo '<div class="con_heading">Напоминание пароля</div>';

        if (!isset($_POST['goremind'])){
            //PRINT QUERY FORM
            echo '<form name="prform" action="" method="POST">';
            echo '<table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="9"><tr>';
            echo '<td>Введите e-mail, указанный при регистрации: </td>';
            echo '<td><input name="email" type="text" size="25" /></td>';
            echo '<td><input name="goremind" type="submit" value="Отправить"/></td>';
            echo '</tr></table>';
            echo '</form>';
        } else {
            //SEND NEW PASSWORD TO EMAIL
            $email = $_POST['email'];

            if (!eregi("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$", $email)){
                echo '<p style="color:red">Введен не корректный адрес email!</p>';
            } else {

                $sql = "SELECT * FROM cms_users WHERE email = '$email' LIMIT 1";
                $result = $inDB->query($sql) ;

                if ($inDB->num_rows($result)>0){
                    $usr = $inDB->fetch_assoc($result);

                    $newpassword = substr(md5(microtime()), 0, 6);
                    $inDB->query("UPDATE cms_users SET password = '".md5($newpassword)."' WHERE id = ".$usr['id']) ;

                    $mail_message = 'Здравствуйте, ' . $usr['nickname'] . '!'. "\n\n";
                    $mail_message .= 'Вы, либо кто-то еще запросили напоминание пароля по email на сайте "'.$inConf->sitename.'".' . "\n\n";
                    $mail_message .= 'Пароли на нашем сайте хранятся в зашифрованном виде' . "\n";
                    $mail_message .= 'поэтому мы не можем напомнить вам старый пароль.' . "\n\n";
                    $mail_message .= '########## Ваш логин: ' .$usr['login']. "\n\n";
                    $mail_message .= '########## Ваш новый пароль: ' .$newpassword . "\n\n";
                    $mail_message .= 'Вы всегда можете сменить этот пароль на более удобный'."\n";
                    $mail_message .= 'в настройках своего профиля: '. cmsUser::getProfileURL($usr['login']) . "\n\n";
                    $mail_message .= 'C уважением, '. $inConf->sitename . ' ('.HOST.').' . "\n";
                    $mail_message .= date('d-m-Y (H:i)');

                    $inCore->mailText($email, $inConf->sitename.' - Напоминание пароля', $mail_message);
                    echo '<p>Новый пароль был выслан на указанный e-mail.</p>';

                } else {
                    echo '<p style="color:red">Адрес "'.$email.'" не встречается в нашей базе данных.</p>';
                }

            }
        }

    }

//======================================================================================================================//

    if ($do=='register'){

        $inPage->setTitle('Регистрация');

        $msg = '';
        if(strlen($inCore->request('login'))>=2) { $login = $inCore->request('login', 'str', ''); } else { $msg .= 'Укажите логин (не короче 2х символов)!<br/>'; }
        if($inCore->request('pass')) { $pass = $inCore->request('pass', 'str', ''); } else { $msg .= 'Укажите пароль!<br/>'; }
        if($inCore->request('pass2')) { $pass2 = $inCore->request('pass2', 'str', ''); } else { $msg .= 'Укажите пароль дважды!<br/>'; }

        if (!eregi("^[a-zA-Z0-9]+\$", $login)){
            $msg  .= 'Логин должен состоять только из латинских букв и цифр!<br/>';
        }

        if($cfg['name_mode']=='nickname'){
            if($inCore->request('nickname', 'str', '')) { $nickname = $inCore->request('nickname', 'str', ''); } else { $msg .= 'Укажите никнейм!<br/>'; }
        } else {
            $namemsg = '';
            if($inCore->request('realname1', 'str', '')) { $realname1 = $inCore->request('realname1', 'str', ''); } else { $namemsg .= 'Укажите ваше имя!<br/>'; }
            if($inCore->request('realname2', 'str', '')) { $realname2 = $inCore->request('realname2', 'str', ''); } else { $namemsg .= 'Укажите вашу фамилию!<br/>'; }
            if (!$namemsg){
                $nickname = trim($realname1) . ' ' . trim($realname2);
            } else {
                $msg .= $namemsg;
            }
        }

        if(!$inCore->inRequest('email')) {
            $msg .= 'Укажите e-mail!<br/>';
        }

        if($inCore->inRequest('email')) {
            $email = $inCore->request('email', 'str', '');
            if (!eregi("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$", $email)){
                $msg  .= 'Введен не корректный адрес email!<br/>';
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

        if($_REQUEST['code']) { $code = $_REQUEST['code']; } else { $msg .= 'Введите код, указанный на картинке!<br/>'; }
        if(@$pass != @$pass2) { $msg .= 'Пароли не совпали!<br/>'; }

        if($msg==''){

            if ($inCore->checkCaptchaCode($code)){
                $sql = "SELECT id, login, email FROM cms_users WHERE (login LIKE '$login' OR email LIKE '$email') AND (is_deleted = 0)";
                $result = $inDB->query($sql) ;
                if($inDB->num_rows($result)==0){

                    $is_locked = $cfg['act'];
                    $send_pass = $pass;
                    $pass = md5($pass);
                    $sql = "INSERT INTO cms_users (login, nickname, password, email, icq, regdate, logdate, birthdate, is_locked)
                            VALUES ('$login', '$nickname', '$pass', '$email', '$icq', NOW(), '', '$birthdate', '$is_locked')";
                    $inDB->query($sql) ;

                    $new_user_id = dbLastId('cms_users');

                    //create advanced user profile
                    if ($new_user_id){
                        $usr = $inDB->fetch_assoc($result);
                        $sql = "INSERT INTO cms_user_profiles (user_id, city, description, showmail, showbirth, showicq, karma, imageurl, allow_who)
                                VALUES (".$new_user_id.", '', '', '0', '0', '1', '0', '', 'all')";
                        $inDB->query($sql) ;
                    }

                    if ($is_locked){
                        sendActivationNotice($send_pass, $new_user_id);
                        header('location:/registration/activate.html');
                    } else {
                        header('location:/registration/complete.html');
                    }

                } else {
                    $u = $inDB->fetch_assoc($result);
                    if ($login == $u['login']) { $msg .= 'Логин "'.$login.'" уже занят!'; }
                    else { $msg .= 'Указанный email уже зарегистрирован!'; }
                }
            } else {
                $msg = 'Неверно указан код на картинке!';
            }

        }

    }

//======================================================================================================================//

    if ($do=='view' || @$msg!=''){

        $inPage->setTitle('Регистрация');

        $do = 'view';
        echo '<div class="con_heading">Регистрация</div>';

        if ($cfg['is_on']){

            $inPage->addHeadJS('components/registration/js/check.js');

            if (isset($msg)) { if($msg!='') { echo '<p><font color="red">'.$msg.'</font></p>'; } }           

            $smarty = $inCore->initSmarty('components', 'com_registration.tpl');
                $smarty->assign('menuid', $menuid);
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

            $inDB->query("UPDATE cms_users SET logdate = NOW() WHERE id = ".$user_id);
            $inDB->query("DELETE FROM cms_online WHERE user_id = ".$user_id);
            $inDB->query("DELETE FROM cms_search WHERE session_id = '".$sess_id."'");

            session_unregister('user');
            session_destroy();

            $inUser->dropStatTimer();

            $inCore->redirect('/');
        }

        if( !$inCore->inRequest('logout') ) {
                if ($inCore->inRequest('login')) { $login = $inCore->request('login', 'str'); } else { $inCore->redirect($back); }
                if ($inCore->inRequest('pass')) { $passw = $inCore->request('pass', 'str'); } else { $inCore->redirect($back); }

                $remember_pass = $inCore->inRequest('remember');

                $sql    = "SELECT * FROM cms_users WHERE login = '$login' AND password = md5('$passw') AND is_deleted = 0 AND is_locked = 0";
                $result = $inDB->query($sql);

                if($inDB->num_rows($result)==1) {
                    $current_ip     = $_SERVER['REMOTE_ADDR'];
                    $user           = $inDB->fetch_assoc($result);

                    if (!cmsUser::isBanned($user['id'])) {
                        session_register('user');
                        $_SESSION['user'] = cmsUser::createUser($user);
                        if ($remember_pass){
                            $cookie_code    = md5($user['id'] . $user['password']);
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
                            case 'editprofile': $url = '/users/0/'.$user['id'].'/editprofile.html'; break;
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
            $inDB->query($sql) or die('Ошибка активации! Обратитесь к администрации сайта.');

            $sql = "DELETE FROM cms_users_activate WHERE code = '$code'";
            $inDB->query($sql) or die('Ошибка активации! Обратитесь к администрации сайта.');

            $inCore->redirect('/registration/complete.html');
        } else {
            $inCore->redirect('/');
        }

    }

//======================================================================================================================//

} //function
?>