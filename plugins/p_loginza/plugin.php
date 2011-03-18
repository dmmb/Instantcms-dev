<?php

class p_loginza extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // ���������� � �������

        $this->info['plugin']           = 'p_loginza';
        $this->info['title']            = '����������� Loginza';
        $this->info['description']      = '��������� ����������� �������������� �� �����, ��������� �������� ���������� ���������� �����';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // ��������� ��-���������

        $this->config['����������']         = 'vkontakte,facebook,mailruapi,google,yandex,openid,twitter,webmoney,rambler,flickr,mailru,loginza,myopenid,lastfm,verisign,aol,steam';
        $this->config['���� (ru/uk/en)']    = 'ru';

        // �������, ������� ����� ������������� ��������

        $this->events[]                 = 'LOGINZA_BUTTON';
        $this->events[]                 = 'LOGINZA_AUTH';

    }

// ==================================================================== //

    /**
     * ��������� ��������� �������
     * @return bool
     */
    public function install(){

//        if (!function_exists('curl_init')){
//            $curl_error  = '<h4>������ ��������� �������</h4>';
//            $curl_error .= '<p>���������� CURL ��� PHP �� ������� ���� �� ��������.<br/>';
//            $curl_error .= '������ ������� ��� ��������� CURL ����������.</p>';
//            $curl_error .= '<p>���������� � ����������� ��������� ������ ��������.</p>';
//            $curl_error .= '<p><a href="/admin/index.php?view=plugins">�����</a></p>';
//            die($curl_error);
//        }

        $inDB = cmsDatabase::getInstance();

        if (!$inDB->isFieldExists('cms_users', 'openid')){

            $inDB->query("ALTER TABLE `cms_users` ADD `openid` VARCHAR( 250 ) NULL, ADD INDEX ( `openid` )");

        }

        return parent::install();

    }

// ==================================================================== //

    /**
     * ��������� ���������� �������
     * @return bool
     */
    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    /**
     * ��������� �������
     * @param string $event
     * @param mixed $item
     * @return mixed
     */
    public function execute($event, $item){

        parent::execute();

        switch ($event){
            case 'LOGINZA_BUTTON':  $item = $this->showLoginzaButton(); break;
            case 'LOGINZA_AUTH':    $item = $this->loginzaAuth(); break;
        }

        return true;

    }

// ==================================================================== //

    private function showLoginzaButton() {

        $token_url  = urlencode(HOST . '/plugins/p_loginza/auth.php');
        $providers  = $this->config['����������'];
        $lang       = $this->config['���� (ru/uk/en)'];

        $providers_list = explode(',', $providers);
        $providers = '';

        foreach($providers_list as $p){
            $providers .= $p . ',';
        }

        $providers = rtrim($providers, ',');

        $html = '<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
                 <a href="http://loginza.ru/api/widget?token_url='.$token_url.'&providers_set='.$providers.'&lang='.$lang.'" class="loginza">
                     <img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="����� ����� loginza"/>
                 </a>';

        echo $html;

        return;

    }

// ==================================================================== //

    private function loginzaAuth(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        
        $token = $inCore->request('token', 'str', '');

        if (!$token){ exit; }

        $loginza_api_url = 'http://loginza.ru/api/authinfo';

        // ��������� �������
        $profile = $this->loginzaRequest($loginza_api_url.'?token='.$token);

        $profile = json_decode($profile);

        // �������� �� ������
        if (!is_object($profile) || !empty($profile->error_message) || !empty($profile->error_type)) {
            exit;
        }

        // ���� ������ ������������
        $user_id = $this->getUserByIdentity($profile->identity);

        // ���� ������������ ���, �������
        if (!$user_id){
            $user_id = $this->createUser($profile);
        }

        // ���� ������������ ��� ��� ��� ������� ������, ����������
        if ($user_id){
            $this->loginUser($user_id);
        }

        // ���� ����������� �� �������, ���������� �� ��������� �� ������
        $inCore->redirect('/auth/error.html');  exit;

    }
    
// ==================================================================== //

    private function loginUser($user_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

        if (isset($_SESSION['auth_back_url'])){
            $back = $_SESSION['auth_back_url'];
            $is_sess_back = true;
            unset($_SESSION['auth_back_url']);
        } else {
            $is_sess_back = false;
        }

        $sql    = "SELECT *
                   FROM cms_users
                   WHERE id = '{$user_id}'";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)==1) {

            $current_ip     = $_SERVER['REMOTE_ADDR'];
            $user           = $inDB->fetch_assoc($result);

            if (!cmsUser::isBanned($user['id'])) {

                $_SESSION['user'] = cmsUser::createUser($user);

                cmsCore::callEvent('USER_LOGIN', $_SESSION['user']);

            } else {
                $inDB->query("UPDATE cms_banlist SET ip = '$current_ip' WHERE user_id = ".$user['id']." AND status = 1");
            }

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

            $inCore->redirect($url); exit;

        }

        return false;

    }

// ==================================================================== //

    private function createUser($profile){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

        if ($profile->name->full_name){

            // ������� ������ ���
            $nickname   = $profile->name->full_name;
            $nickname   = iconv('utf-8', 'cp1251', $nickname);

        } elseif($profile->name->first_name) {
        
            // ������� ��� � ������� ��-�����������
            $nickname   = $profile->name->first_name;
            if ($profile->name->last_name){ $nickname .= ' '. $profile->name->last_name; }
            $nickname   = iconv('utf-8', 'cp1251', $nickname);

        } elseif(preg_match('/^(http:\/\/)([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+)\.([a-zA-Z]{2,6})([\/]?)$/i', $profile->identity)) {
            
            // �� ������� ���, �� ������� ������������� � ���� ������ 3-�� ������
            $nickname = str_replace('http://', '', $profile->identity);
            $nickname = substr($nickname, 0, strpos($nickname, '.'));

        } else {

            // �� ������� ������ ������
            $max = $inDB->get_fields('cms_users', 'id>0', 'id', 'id DESC');
            $nickname = 'user' . ($max['id'] + 1);

        }

        $login      = str_replace('-', '', cmsCore::strToURL($nickname));
        $pass       = md5(substr(md5(rand(0, 9999)), 0, 8));
        $email      = $profile->email;
        $birthdate  = $profile->dob;

        $already = $inDB->get_fields('cms_users', "(login='{$login}' OR email='{$email}') AND is_deleted=0", 'login, email');

        if ($already){

            //
            // ��������� ��������� email
            //
            if ($already['email']==$email && $email){
                $inCore->redirect('/auth/error.html');  exit;
            }

            //
            // ��������� ��������� ������
            //
            if ($already['login']==$login){
                // ���� ����� �����, ��������� � ���� ID
                $max = $inDB->get_fields('cms_users', 'id>0', 'id', 'id DESC');
                $login .= ($max['id']+1);
            }

        }

        $user_array = array(
                                'login'=>$login,
                                'nickname'=>$nickname,
                                'email'=>$email,
                                'birthdate'=>$birthdate,
                           );

        $sql = "INSERT INTO cms_users (login, nickname, password, email, regdate, birthdate, openid)
                VALUES ('$login', '$nickname', '$pass', '$email', NOW(), '$birthdate', '{$profile->identity}')";
                
        $inDB->query($sql) ;

        $user_id = $inDB->get_last_id('cms_users');

        // ������� ������� ������������
        if ($user_id){

            $filename = 'nopic.jpg';

            // ���� ���� ������, ������� �������
            if ($profile->photo){
                $photo_path = $this->downloadAvatar($profile->photo);
                if ($photo_path){
                    
                    $inCore->includeGraphics();

                    $uploaddir 		= $_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/';

                    $filename 		= md5($photo_path . '-' . $user_id . '-' . time()).'.jpg';
                    $uploadfile		= $photo_path;
                    $uploadavatar 	= $uploaddir . $filename;
                    $uploadthumb 	= $uploaddir . 'small/' . $filename;

                    $cfg = $inCore->loadComponentConfig('users');

                    if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
                    if (isset($cfg['medw'])) { $medw = $cfg['medw']; } else { $medw = 200; }

                    @img_resize($uploadfile, $uploadavatar, $medw, $medh);
                    @img_resize($uploadfile, $uploadthumb, $smallw, $smallw);

                    @unlink($photo_path);

                }
            }

            $usr = $inDB->fetch_assoc($result);
            $sql = "INSERT INTO cms_user_profiles (user_id, city, description, showmail, showbirth, showicq, karma, imageurl, allow_who)
                    VALUES ('{$user_id}', '', '', '0', '0', '1', '0', '{$filename}', 'all')";
            $inDB->query($sql) ;

            $user_array['id'] = $user_id;
            cmsCore::callEvent('USER_REGISTER', $user_array);

            return $user_id;

        }

        return false;

    }

// ==================================================================== //

    private function downloadAvatar($url){

        $tempfile   = PATH.'/upload/'.  session_id() . '.jpg';

        $f_remote   = @fopen($url, "rb");
        $f_local    = @fopen($tempfile, "w");

        if ($f_remote && $f_local) {
            
            while (!feof($f_remote)) {
                $buff = fread($f_remote, 1024);
                fwrite($f_local, $buff);
            }

            @fclose($f_remote);
            @fclose($f_local);

            return $tempfile;

        } else {

            @fclose($f_remote);
            @fclose($f_local);

            return false;

        }
        
    }

// ==================================================================== //

    private function getUserByIdentity($identity){
        
        $inDB   = cmsDatabase::getInstance();

        return $inDB->get_field('cms_users', "openid='{$identity}'", 'id');
        
    }
    
// ==================================================================== //

    private function loginzaRequest($url) {

        if (function_exists('curl_init')){

            $curl = curl_init($url);
            $user_agent = 'Loginza-API/InstantCMS';

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $raw_data = curl_exec($curl);
            curl_close($curl);

            return $raw_data;

        } else {

            return file_get_contents($url);

        }

    }

// ==================================================================== //

}

?>
