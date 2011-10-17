<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

class p_loginza extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // Информация о плагине

        $this->info['plugin']           = 'p_loginza';
        $this->info['title']            = 'Авторизация Loginza';
        $this->info['description']      = 'Позволяет посетителям авторизоваться на сайте, используя аккаунты популярных социальных сетей';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // Настройки по-умолчанию

        $this->config['Провайдеры']         = 'vkontakte,facebook,mailruapi,google,yandex,openid,twitter,webmoney,rambler,flickr,mailru,loginza,myopenid,lastfm,verisign,aol,steam';
        $this->config['Язык (ru/uk/en)']    = 'ru';

        // События, которые будут отлавливаться плагином

        $this->events[]                 = 'LOGINZA_BUTTON';
        $this->events[]                 = 'LOGINZA_AUTH';

    }

// ==================================================================== //

    /**
     * Процедура установки плагина
     * @return bool
     */
    public function install(){

        $inDB = cmsDatabase::getInstance();

        if (!$inDB->isFieldExists('cms_users', 'openid')){

            $inDB->query("ALTER TABLE `cms_users` ADD `openid` VARCHAR( 250 ) NULL, ADD INDEX ( `openid` )");

        }

        return parent::install();

    }

// ==================================================================== //

    /**
     * Процедура обновления плагина
     * @return bool
     */
    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    /**
     * Обработка событий
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
        $providers  = $this->config['Провайдеры'];
        $lang       = $this->config['Язык (ru/uk/en)'];

        $providers_list = explode(',', $providers);
        $providers = '';

        foreach($providers_list as $p){
            $providers .= $p . ',';
        }

        $providers = rtrim($providers, ',');

        $html  = '<div class="lf_title">Вход через социальный сети</div><p style="margin:15px 0">Если у Вас есть регистрация в других социальных сетях или аккаунт OpenID, то Вы можете войти на сайт без регистрации.</p><p><script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
                 <a href="http://loginza.ru/api/widget?token_url='.$token_url.'&providers_set='.$providers.'&lang='.$lang.'" class="loginza">
                     <img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/>
                 </a></p>';

        echo $html;

        return;

    }

// ==================================================================== //

    private function loginzaAuth(){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$inUser = cmsUser::getInstance();
        
        $token = $inCore->request('token', 'str', '');
        if (!$token){ exit; }

        $loginza_api_url = 'http://loginza.ru/api/authinfo';

        // получение профиля
        $profile = $this->loginzaRequest($loginza_api_url.'?token='.$token);

        $profile = json_decode($profile);

        // проверка на ошибки
        if (!is_object($profile) || !empty($profile->error_message) || !empty($profile->error_type)) {
            exit;
        }

        // ищем такого пользователя
        $user_id = $this->getUserByIdentity($profile->identity);

        // если пользователя нет, создаем
        if (!$user_id){
            $user_id = $this->createUser($profile);
        }

        // если пользователь уже был или успешно создан, авторизуем
        if ($user_id){
			$user = $inDB->get_fields('cms_users', "id = '{$user_id}'", 'login, password');
			if(!$user) { return false; }

			$back_url = $inUser->signInUser($user['login'], $user['password'], 1, 1);
	
			$inCore->redirect($back_url); exit;

        }

        // если авторизация не удалась, редиректим на сообщение об ошибке
        $inCore->redirect('/auth/error.html');  exit;

    }

// ==================================================================== //

    private function createUser($profile){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

        if ($profile->name->full_name){

            // указано полное имя
            $nickname   = $profile->name->full_name;
            $nickname   = iconv('utf-8', 'cp1251', $nickname);

        } elseif($profile->name->first_name) {
        
            // указано имя и фамилия по-отдельности
            $nickname   = $profile->name->first_name;
            if ($profile->name->last_name){ $nickname .= ' '. $profile->name->last_name; }
            $nickname   = iconv('utf-8', 'cp1251', $nickname);

        } elseif(preg_match('/^(http:\/\/)([a-zA-Z0-9\-_]+)\.([a-zA-Z0-9\-_]+)\.([a-zA-Z]{2,6})([\/]?)$/i', $profile->identity)) {
            
            // не указано имя, но передан идентификатор в виде домена 3-го уровня
            $nickname = str_replace('http://', '', $profile->identity);
            $nickname = substr($nickname, 0, strpos($nickname, '.'));

        } else {

            // не указано вообще ничего
            $max = $inDB->get_fields('cms_users', 'id>0', 'id', 'id DESC');
            $nickname = 'user' . ($max['id'] + 1);

        }

        $login      = str_replace('-', '', cmsCore::strToURL($nickname));
        $pass       = md5(substr(md5(rand(0, 9999)), 0, 8));
        $email      = $profile->email;
        $birthdate  = $profile->dob;

        $already_email = $inDB->get_field('cms_users', "email='{$email}' AND is_deleted=0", 'email');
		$already_login = $inDB->get_field('cms_users', "login='{$login}' AND is_deleted=0", 'login');

		//
		// проверяем наличие и занятость email
		//
		if ($email && $already_email == $email){
			$inCore->redirect('/auth/error.html');  exit;
		}

		//
		// проверяем занятость логина
		//
		if ($already_login == $login){
			// если логин занят, добавляем к нему ID
			$max = $inDB->get_fields('cms_users', 'id>0', 'id', 'id DESC');
			$login .= ($max['id']+1);
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

        // создаем профиль пользователя
        if ($user_id){

            $filename = 'nopic.jpg';

            // если есть аватар, пробуем скачать
            if ($profile->photo){
                $photo_path = $this->downloadAvatar($profile->photo);
                if ($photo_path){
                    
                    $inCore->includeGraphics();

                    $uploaddir 		= PATH.'/images/users/avatars/';
                    $filename 		= md5($photo_path . '-' . $user_id . '-' . time()).'.jpg';
                    $uploadavatar 	= $uploaddir . $filename;
                    $uploadthumb 	= $uploaddir . 'small/' . $filename;

                    $cfg = $inCore->loadComponentConfig('users');

					if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
					if (isset($cfg['medw'])) { 	 $medw = $cfg['medw']; } else { $medw = 200; }
					if (isset($cfg['medh'])) { 	 $medh = $cfg['medh']; } else { $medh = 200; }

                    @img_resize($photo_path, $uploadavatar, $medw, $medh);
                    @img_resize($photo_path, $uploadthumb, $smallw, $smallw);

                    @unlink($photo_path);

                }
            }

            $sql = "INSERT INTO cms_user_profiles (user_id, city, description, showmail, showbirth, showicq, karma, imageurl, allow_who)
                    VALUES ('{$user_id}', '', '', '0', '0', '1', '0', '{$filename}', 'all')";
            $inDB->query($sql);

            $user_array['id'] = $user_id;
            cmsCore::callEvent('USER_REGISTER', $user_array);

            return $user_id;

        }

        return false;

    }

// ==================================================================== //

    private function downloadAvatar($url){

        $tempfile   = PATH.'/images/users/avatars/'.md5(session_id()).'.jpg';

        if (function_exists('curl_init')){

            $curl = curl_init();
            $user_agent = 'Loginza-API/InstantCMS';

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, false);
            $raw_data = curl_exec($curl);
            curl_close($curl);

        } else {

            $raw_data = file_get_contents($url);

        }

		if($f = @fopen($tempfile, 'w')){

			@fwrite($f, $raw_data);
			@fclose($f);

			return $tempfile;

		} else {

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
