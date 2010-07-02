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

class cmsUser {

    //При изменении этой константы здесь, нужно изменить ее также в .htaccess
    const PROFILE_LINK_PREFIX = 'users/';

    //Интервал в секундах для обновления статистики по пользователю
    const STAT_TIMER_INTERVAL = 40;

    private static $instance;

    private static $guest_group_id;

    public $id          = 0;

    private function __construct() {}

    private function __clone() {}

// ============================================================================ //
// ============================================================================ //

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Обновляет данные пользователя
     * @return bool
     */
    public function update() {

        $inCore = cmsCore::getInstance();

        $user_id    = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

        if (!$user_id){
            $this->id   = 0;
            $this->is_admin = 0;
            $this->group_id = self::getGuestGroupId();
            return true;
        }

        $info       =   $this->loadUser($user_id);

        if (!$info){ return false; }

        foreach($info as $key=>$value){
            $this->{$key}   = $value;
        }

        $this->id           = (int)$user_id;

        $this->checkBan();

        return true;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Загружает данные пользователя из базы
     * @param int $user_id
     * @return array
     */
    public function loadUser($user_id) {

        $inDB       = cmsDatabase::getInstance();
        $inCore     = cmsCore::getInstance();

        $sql    = "SELECT u.*, g.is_admin is_admin
                   FROM cms_users u, cms_user_groups g
                   WHERE u.id={$user_id} AND u.is_deleted = 0 AND u.is_locked = 0 AND u.group_id = g.id";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result) !== 1) { return false; }

        $info   = $inDB->fetch_assoc($result);

        $info['ip']         = $_SERVER['REMOTE_ADDR'];

        return $info;

    }

// ============================================================================ //
// ============================================================================ //

    public static function createUser($_userdata){

        $inDB = cmsDatabase::getInstance();
        $user = $_userdata;

        $sql = "SELECT alias, access, is_admin FROM cms_user_groups WHERE id = ".$user['group_id'];
        $result = $inDB->query($sql);
        $_groupdata = $inDB->fetch_assoc($result);
        $user['group']  = $_groupdata['alias'];

        $access = str_replace(', ', ',', $_groupdata['access']);
        $access = split(',', $access);

        $user['access'] = array();
        $user['access'] = $access;

        $user['is_admin'] = $_groupdata['is_admin'];

        return $user;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет, находится ли текущий посетитель в бан-листе
     * Если да, то показывает сообщение и завершает работу
     */
    public function checkBan(){
        
        $inDB       = cmsDatabase::getInstance();
        $inCore     = cmsCore::getInstance();
        
        $current_ip = $this->ip;

        if ($inDB->rows_count('cms_banlist', "ip = '$current_ip' AND status=1")){

            $ban        = $inDB->get_fields('cms_banlist', "ip = '$current_ip' AND status=1", 'int_num, int_period, autodelete, id, status, bandate');
            $interval   = $ban['int_num'] . ' ' .$ban['int_period'];

            //Check expired
            $sql = "SELECT *
                    FROM cms_banlist
                    WHERE id = {$ban['id']} AND bandate <= DATE_SUB(NOW(), INTERVAL $interval) AND int_num > 0
                    LIMIT 1";

            $rs = $inDB->query($sql);

            if (!$inDB->errno()){
                if ($inDB->num_rows($rs)){
                    if ($ban['autodelete']){
                        //delete
                        $inDB->query("DELETE FROM cms_banlist WHERE id={$ban['id']}");
                    } else {
                        //close
                        $inDB->query("UPDATE cms_banlist SET status=0 WHERE id={$ban['id']}");
                    }
                } else {
                    echo '<div style="color:red"><strong>Ваш доступ к сайту заблокирован</strong></div>';
                    echo '<div style="padding:15px;">';
                        echo '<div><strong>Дата блокировки:</strong> '.$ban['bandate'].'</div>';
                        if ($ban['int_num']<=0){
                            echo '<div><strong>Срок блокировки:</strong> бесконечен</div>';
                        } else {
                            echo '<div><strong>Срок блокировки:</strong> '.$interval.'</div>';
                        }
                    echo '</div>';
                    $inCore->halt();
                }
            }
        }
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет наличие кукиса "запомнить меня" и если он найден - авторизует пользователя
     * @return bool
     */
    public function autoLogin(){

        $inDB       = cmsDatabase::getInstance();
        $inCore     = cmsCore::getInstance();
        
        if ($inCore->getCookie('userid') && !$this->id){

            $cookie_code = $inCore->getCookie('userid');

            if (!preg_match('/([0-9a-zA-Z]{32})/i', $cookie_code)){ return false; }

            $sql = "SELECT * FROM cms_users WHERE md5(CONCAT(id, password)) = '$cookie_code' AND is_deleted=0 AND is_locked=0";
            $res = $inDB->query($sql);

            if($inDB->num_rows($res)==1){
                $userrow = $inDB->fetch_assoc($res);
                session_register('user');
                $_SESSION['user'] = self::createUser($userrow);
                cmsCore::callEvent('USER_LOGIN', $_SESSION['user']);
                $inDB->query("UPDATE cms_users SET logdate = NOW() WHERE id = ".$_SESSION['user']['id']);
            } else {
                $inCore->unsetCookie('user_id');
            }

        }

        return true;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Сбрасывает таймер статистики пользователя
     * @return bool
     */
    public function resetStatTimer() {

        $_SESSION['user']['s_timer'] = time();

        return true;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет, вышло ли время интервала для обновления статистики пользователя
     * @return bool
     */
    public function checkStatTimer() {

        if (!isset($_SESSION['user']['s_timer'])) { return true; }

        $user_time = $_SESSION['user']['s_timer'];
        
        return (bool)(time()-$user_time >= self::STAT_TIMER_INTERVAL);

    }


// ============================================================================ //
// ============================================================================ //

    public function dropStatTimer(){

        unset($_SESSION['user']['s_timer']);
        return true;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает рейтинг пользователя
     * @param int $user_id
     * @return int
     */
    public static function getRating($user_id) {

        $inDB = cmsDatabase::getInstance();

        $sql = "SELECT SUM( r.points ) AS rating
                FROM cms_ratings r
                LEFT JOIN cms_content c ON r.item_id = c.id AND r.target = 'content'
                LEFT JOIN cms_photo_files f ON r.item_id = f.id AND r.target = 'photo'
                LEFT JOIN cms_blog_posts p ON r.item_id = p.id AND r.target = 'blogpost'
                WHERE c.user_id = $user_id OR f.user_id = $user_id OR p.user_id = $user_id";

        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return 0; }

        $data = $inDB->fetch_assoc($result);

        return $data['rating'] * 5;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает значение кармы пользователя
     * @param int $user_id
     * @return int
     */
    public static function getKarma($user_id){
        $inDB = cmsDatabase::getInstance();
        $sql = "SELECT SUM(points) as karma FROM cms_user_karma WHERE user_id = $user_id";
        $result = $inDB->query($sql);
        if ($inDB->num_rows($result)>0){ $data = $inDB->fetch_assoc($result); if ($data['karma']) {$karma = $data['karma']; } else { $karma = 0; } }
        return $karma;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает значение кармы пользователя и ссылки для ее изменения в виде html
     * @param int $user_id
     * @param bool $showtitle
     * @param bool $controls
     * @return html
     */
    public static function getKarmaFormat($user_id, $showtitle=false, $controls=true){
        //calculate positive karma
        $inUser = self::getInstance();

        $karma = self::getKarma($user_id);

        $plus = '';
        $minus = '';

        if ($inUser->id && $controls){
            if(usrCanKarma($user_id, $inUser->id)){
                $plus = '<a href="/users/karma/plus/'.$user_id.'/'.$inUser->id.'" title="Карма +"><img src="/components/users/images/karma_up.gif" border="0" alt="Карма +"/></a>';
                $minus = '<a href="/users/karma/minus/'.$user_id.'/'.$inUser->id.'" title="Карма -"><img src="/components/users/images/karma_down.gif" border="0" alt="Карма -"/></a>';
            }
        }

        $html = '<table cellpadding="2" cellspacing="0"><tr>';
            $html .= '<td style="color:green">'.$plus.'</td>';
            if($karma>0){
                $html .= '<td><span class="user_karma_point">+'.$karma.'</span></td>';
            } elseif ($karma<0){
                $html .= '<td><span class="user_karma_point">'.$karma.'</span></td>';
            } else {
                $html .= '<td><span class="user_karma_point">'.$karma.'</span></td>';
            }
            $html .= '<td style="color:red">'.$minus.'</td>';
        $html .= '</tr></table>';

        return $html;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает ссылки на профили именниников
     * @return html
     */
    public static function getBirthdayUsers() {

        $inDB = cmsDatabase::getInstance();
        $inCore = cmsCore::getInstance();

        $sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, u.birthdate, p.gender as gender
                FROM cms_users u, cms_user_profiles p
                WHERE p.user_id = u.id AND u.is_locked = 0 AND u.is_deleted = 0 AND DATE_FORMAT(u.birthdate, '%d-%m')=DATE_FORMAT(NOW(), '%d-%m')";

        $rs     = $inDB->query($sql);
        $total  = $inDB->num_rows($rs);

        $now=0; $html = '';

        if (!$total){ return false; }
        
        while($usr = mysql_fetch_assoc($rs)){
            $html .= self::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
            if ($now < $total-1) { $html .= ', '; }
            $now ++;
        }

        return $html;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает последние посты в блогах друзей
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public static function getUserFriendsComments($user_id, $limit=10){

        $inDB           = cmsDatabase::getInstance();
        $inCore         = cmsCore::getInstance();

        $friends        = self::getFriends($user_id);

        if (!$friends) { return false; }

        $friends_sql    = '';

        foreach($friends as $id=>$friend){
            $friends_sql .= 'u.id = '.$friend['id'];
            if ($id < sizeof($friends)-1){ $friends_sql .= ' OR '; }
        }

        $sql = "SELECT DISTINCT c.id, c.content, c.target as target, c.target_id as target_id, c.user_id, c.target_link, u.id as user_id, u.nickname as nickname, u.login as login, c.pubdate as pubdate
                FROM cms_comments c, cms_users u
                WHERE c.user_id = u.id AND ({$friends_sql})
                ORDER BY c.pubdate DESC
                ";

        if ($limit) { $sql .= 'LIMIT '.$limit; }

        $result = $inDB->query($sql);

        $comments = array();

        if (!$inDB->num_rows($result)){ return false; }

        while ($comment = $inDB->fetch_assoc($result)){
            $comment['pubdate'] = $inCore->dateFormat($comment['pubdate']);
            if (sizeof($comment['content'])>50){
                $comment['content'] = substr($comment['content'], 0, 50) . '...';
            }
            $comments[] = $comment;
        }

        return $comments;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает последние посты в блогах друзей
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public static function getUserFriendsPosts($user_id, $limit=10){
        
        $inDB           = cmsDatabase::getInstance();
        $inCore         = cmsCore::getInstance();

        $friends        = self::getFriends($user_id);

        if (!$friends) { return false; }

        $friends_sql    = '';

        foreach($friends as $id=>$friend){
            $friends_sql .= 'u.id = '.$friend['id'];
            if ($id < sizeof($friends)-1){ $friends_sql .= ' OR '; }
        }

        $sql = "SELECT DISTINCT p.id,
                                p.title,
                                p.user_id,
                                p.blog_id,
                                p.seolink as seolink,
                                b.seolink as bloglink, 
                                u.id as user_id,
                                u.nickname as nickname,
                                u.login as login,
                       			p.pubdate as pubdate
                FROM cms_blog_posts p, cms_users u, cms_blogs b
                WHERE p.blog_id = b.id AND p.user_id = u.id AND ({$friends_sql})
                ORDER BY p.pubdate DESC
                ";

        if ($limit) { $sql .= 'LIMIT '.$limit; }

        $result = $inDB->query($sql);

        $posts = array();

        if (!$inDB->num_rows($result)){ return false; }

        $inCore->loadModel('blogs');
        $model = new cms_model_blogs();

        while ($post = $inDB->fetch_assoc($result)){
            $post['pubdate']    = $inCore->dateFormat($post['pubdate']);
            $post['url']        = $model->getPostURL(0, $post['bloglink'], $post['seolink']);
            $posts[]            = $post;
        }

        return $posts;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает последние фотографии друзей
     * @param int $user_id
     * @param int $limit
     * @return array
     */
    public static function getUserFriendsPhotos($user_id, $limit=10){

        $inDB           = cmsDatabase::getInstance();
        $inCore         = cmsCore::getInstance();

        $friends        = self::getFriends($user_id);

        if (!$friends) { return false; }

        $friends_sql    = '';

        foreach($friends as $id=>$friend){
            $friends_sql .= 'u.id = '.$friend['id'];
            if ($id < sizeof($friends)-1){ $friends_sql .= ' OR '; }
        }
		// Получаем фото из общей галереи
        $sql = "SELECT p.id, p.title, u.nickname as nickname, u.login as login, p.pubdate as pubdate
                FROM cms_photo_files p, cms_users u
                WHERE p.user_id = u.id AND ({$friends_sql})
                ORDER BY p.pubdate DESC
                ";

        if ($limit) { $sql .= 'LIMIT '.$limit; }
        
        $result = $inDB->query($sql);
		//Получаем личные фотографии
		$private_sql = "SELECT p.id, p.title, p.user_id, u.nickname as nickname, u.login as login, p.pubdate as pubdate
						FROM cms_user_photos p, cms_users u
						WHERE p.user_id = u.id AND ({$friends_sql})
						ORDER BY p.pubdate DESC
						";
		if ($limit) { $private_sql .= 'LIMIT '.$limit; }
		$private_res = $inDB->query($private_sql);
		
		$photos = array();

        if (!$inDB->num_rows($result) && !$inDB->num_rows($private_res)){ return false; }
		
		if ($inDB->num_rows($private_res)) {
			while($photo = $inDB->fetch_assoc($private_res)){
				$photo['pubdate'] = $inCore->dateFormat($photo['pubdate']);
				$photos[]       = $photo;
			}
		}

        while ($photo = $inDB->fetch_assoc($result)){
            $photo['pubdate'] = $inCore->dateFormat($photo['pubdate']);
            $photos[] = $photo;
        }
		//Выбираем последние $limit фото из общего массива
		$total      = sizeof($photos);
	
		if ($total){
			$page_photos    = array();
			for($p=0; $p<$limit; $p++){
				if ($photos[$p]){
					$page_photos[] = $photos[$p];
				}
			}
			$photos = $page_photos; unset($page_photos);
		}
        return $photos;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает элементы <option> для списка пользователей
     * @param int $selected
     * @param array $exclude
     * @return html
     */
    public static function getUsersList($selected=0, $exclude=array()){

        $inDB   = cmsDatabase::getInstance();

        $html   = '';

        $sql    = "SELECT * FROM cms_users WHERE is_locked = 0 AND is_deleted = 0 ORDER BY nickname";
        $rs     = $inDB->query($sql);

        if (!$inDB->num_rows($rs)){ return; }

        while($u = $inDB->fetch_assoc($rs)){
            if(!in_array($u['id'], $exclude)){
                if ($selected){
                    if (in_array($u['id'], $selected)){
                        $html .= '<option value="'.$u['id'].'" selected="selected">'.$u['nickname'].'</option>';
                    } else {
                        $html .= '<option value="'.$u['id'].'">'.$u['nickname'].'</option>';
                    }
                } else {
                    $html .= '<option value="'.$u['id'].'">'.$u['nickname'].'</option>';
                }
            }
        }

        return $html;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает элементы <option> для списка пользователей
     * @param int $selected
     * @param array $exclude
     * @return html
     */
    public static function getAuthorsList($authors, $selected=''){

        if (!$authors) { return; }

        $inDB = cmsDatabase::getInstance();
        $html = '';

        $sql = "SELECT * FROM cms_users WHERE ";

        $a = 1;
        foreach($authors as $key=>$id){
            if ($a == 1) { $sql .= 'id = '.$id; }
            else {
                $sql .= ' OR id = '.$id;
            }
            $a++;
        }

        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)){
            while($u = $inDB->fetch_assoc($rs)){
                if ($selected){
                    if (in_array($u['id'], $selected)){
                        $html .= '<option value="'.$u['id'].'" selected="selected">'.$u['nickname'].'</option>';
                    } else {
                        $html .= '<option value="'.$u['id'].'">'.$u['nickname'].'</option>';
                    }
                } else {
                    $html .= '<option value="'.$u['id'].'">'.$u['nickname'].'</option>';
                }
            }
        }

        return $html;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает элементы <option> для списка пользователей
     * @param int $selected
     * @param array $exclude
     * @return html
     */
    public static function getFullAwardsList($selected=''){

        $inDB = cmsDatabase::getInstance();
        $html = '';

        $awards = array();

        $sql = "SELECT title FROM cms_user_awards GROUP BY title";
        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)){
            while($aw = $inDB->fetch_assoc($rs)){
                $awards[] = $aw['title'];
            }
        }

        $sql = "SELECT title FROM cms_user_autoawards GROUP BY title";
        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)){
            while($aw = $inDB->fetch_assoc($rs)){
                if (!in_array($aw['title'], $awards))
                $awards[] = $aw['title'];
            }
        }

        foreach($awards as $aw){
            if ($selected){
                if ($selected == $aw){
                    $html .= '<option value="'.$aw.'" selected="selected">'.$aw.'</option>';
                } else {
                    $html .= '<option value="'.$aw.'">'.$aw.'</option>';
                }
            } else {
                $html .= '<option value="'.$aw.'">'.$aw.'</option>';
            }
        }

        return $html;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает элементы <option> для списка друзей пользователя
     * @param int $user_id
     * @param int $selected
     * @return html
     */
    public static function getFriendsList($user_id, $selected=0){

        $inDB = cmsDatabase::getInstance();

        $html = '';

        $sql = "SELECT f.*
                FROM cms_user_friends f
                WHERE (f.to_id = $user_id OR f.from_id = $user_id) AND f.is_accepted = 1
                ORDER BY logdate ASC";
        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            while($friend = $inDB->fetch_assoc($result)){

                if ($friend['from_id']==$user_id) { $friend_id = $friend['to_id']; } else { $friend_id = $friend['from_id']; }

                $friend_nickname = $inDB->get_field('cms_users', 'id='.$friend_id, 'nickname');

                if (@$selected==$cat['id']){
                    $s = 'selected';
                } else {
                    $s = '';
                }
                
                $html .= '<option value="'.$friend_id.'" '.$s.'>'.$friend_nickname.'</option>';
            }
        } else {
            $html = '<option value="0" selected>-- Нет друзей --</option>';
        }
        return $html;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает список друзей пользователя
     * @param int $user_id
     * @return array
     */
    public static function getFriends($user_id){

        $inDB       = cmsDatabase::getInstance();

        $friends    = array();

        $sql = "SELECT f.*
                FROM cms_user_friends f
                WHERE (f.to_id = $user_id OR f.from_id = $user_id) AND f.is_accepted = 1
                ORDER BY logdate ASC";
        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            while($friend = $inDB->fetch_assoc($result)){

                $f = array();

                $f['id']        = ($friend['from_id']==$user_id) ? $friend['to_id'] : $friend['from_id'];
                $f['nickname']  = $inDB->get_field('cms_users', 'id='.$f['id'], 'nickname');

                $friends[] = $f;

            }
        }
        return $friends;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает html стены пользователя
     * @param int $selected
     * @param array $exclude
     * @return html
     */
    public static function getUserWall($user_id, $usertype='user', $page=1){

        $inDB       = cmsDatabase::getInstance();
        $inCore     = cmsCore::getInstance();
        $inUser     = self::getInstance();
        
        $myprofile  = false;

        $perpage    = 10;

        if ($usertype=='user'){
            $myprofile = ($inUser->id == $user_id || $inUser->is_admin);
        } else {
            $inCore->loadLib('clubs');
            $myprofile = (clubUserIsRole($user_id, $inUser->id, 'moderator') || clubUserIsAdmin($user_id, $inUser->id) || $inUser->is_admin);
        }

        $records = array();

        $pagebar = '';

        //получаем общее число записей на стене этого пользователя
        $total = $inDB->rows_count('cms_user_wall', "user_id = $user_id AND usertype = '$usertype'");
        $pages = ceil($total / $perpage);
        
        if ($total){
            //получаем нужную страницу записей стены
            $sql = "SELECT w.*, u.nickname as author, u.login as author_login, DATE_FORMAT(w.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate
                    FROM cms_user_wall w, cms_users u
                    WHERE w.user_id = $user_id AND w.author_id = u.id AND w.usertype = '$usertype'
                    ORDER BY w.pubdate DESC
                    LIMIT ".(($page-1)*$perpage).", $perpage";

            $result     = $inDB->query($sql);
            $total_page = $inDB->num_rows($result);

            $inCore->includeFile('components/users/includes/usercore.php');

            while($record = $inDB->fetch_assoc($result)){
                $record['content']  = nl2br($inCore->parseSmiles($record['content'], true));
                $record['avatar']   = usrImage($record['author_id'], 'small');
                $records[]          = $record;
            }

            if ($pages>1){
                $pagebar = cmsPage::getPagebar($total, $page, $perpage, 'javascript:wallPage(%page%)');
            }
        }

        ob_start();

        $smarty = $inCore->initSmarty('components', 'com_users_wall.tpl');

        $smarty->assign('total', $total);
        $smarty->assign('records', $records);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('wall_user_id', $user_id);
        $smarty->assign('myprofile', $myprofile);
        $smarty->assign('usertype', $usertype);
        $smarty->assign('pages', $pages);
        $smarty->assign('page', $page);
        $smarty->assign('total', $total);
        $smarty->assign('total_page', $total_page);
        $smarty->assign('pagebar', $pagebar);

        $smarty->display('com_users_wall.tpl');

        return ob_get_clean();
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает html формы "написать на стене"
     * @param int $user_id
     * @param string $usertype
     * @return html
     */
    public static function getUserAddWall($user_id, $usertype='user'){

        $inCore     = cmsCore::getInstance();

        $my_id      = self::getInstance()->id;

        $bb_toolbar = cmsPage::getBBCodeToolbar('message', true);
        $smilies    = cmsPage::getSmilesPanel('message');

        ob_start();

        $smarty = $inCore->initSmarty('components', 'com_users_addwall.tpl');

        $smarty->assign('user_id', $user_id);
        $smarty->assign('usertype', $usertype);
        $smarty->assign('my_id', $my_id);

        $smarty->assign('bb_toolbar', $bb_toolbar);
    	$smarty->assign('smilies', $smilies);

        $smarty->display('com_users_addwall.tpl');

        return ob_get_clean();
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает список наград пользователя
     * @param int $user_id
     * @return array
     */
    public static function getAwardsList($user_id){

        $inDB   = cmsDatabase::getInstance();

        $list   = false;

        $sql    = "SELECT title FROM cms_user_awards WHERE user_id = $user_id";
        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            $list = array();
            while($record = $inDB->fetch_assoc($result)){ $list[] = $record['title']; }
        }
        
        return $list;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет, голосовал ли текущий пользователь в указанном опросе
     * @param int $poll_id
     * @return bool
     */
    public static function isUserVoted($poll_id){

        $inDB = cmsDatabase::getInstance();

        $user_id    = self::getInstance()->id;
        $ip         = self::getInstance()->ip;

        $sql = "SELECT *
                FROM cms_polls_log
                WHERE ((ip = '$ip' AND user_id = '0') OR (user_id > 0 AND user_id='$user_id')) AND poll_id = $poll_id";

        $result = $inDB->query($sql);
        
        return (bool)$inDB->num_rows($result);
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает ID группы "Гости"
     * @return int
     */
    public static function getGuestGroupId(){

        if (!self::$guest_group_id){

            $inDB = cmsDatabase::getInstance();
            $result = $inDB->query("SELECT id FROM cms_user_groups WHERE alias = 'guest'");
            if ($inDB->num_rows($result)){
                $data = $inDB->fetch_assoc($result);
                self::$guest_group_id = $data['id'];
            } else {
                self::$guest_group_id = 0;
            }

        }

        return self::$guest_group_id;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает массив с количеством гостей и пользователей онлайн
     * @return array
     */
    public static function getOnlineCount(){

        $inDB = cmsDatabase::getInstance();
        $people = array();

        $sql = "SELECT DISTINCT user_id, id FROM cms_online WHERE user_id = '0' OR user_id = '' GROUP BY user_id";
        $result = $inDB->query($sql);
        $people['guests'] = $inDB->num_rows($result);
        $sql = "SELECT DISTINCT user_id, id FROM cms_online WHERE user_id > 0 GROUP BY user_id";
        $result = $inDB->query($sql);
        $people['users'] = $inDB->num_rows($result);

        return $people;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет, на сайте ли указанный пользователь
     * @param int $user_id
     * @return bool
     */
    public static function isOnline($user_id){
        $inDB = cmsDatabase::getInstance();
        $sql = "SELECT id FROM cms_online WHERE user_id = $user_id";
        $result = $inDB->query($sql);
        return (bool)$inDB->num_rows($result);
    }


// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет, находится ли указанный пользователь в бан-листе
     * @param int $user_id
     * @return bool
     */
    public static function isBanned($user_id){
        $inDB = cmsDatabase::getInstance();
        return (bool)$inDB->rows_count('cms_banlist', 'user_id='.$user_id.' AND status=1 LIMIT 1');
    }


// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает ссылку на "Мои сообщения" в виде количества новых сообщений
     * @param int $user_id
     * @return html
     */
    public static function isNewMessages($user_id){
        $inDB = cmsDatabase::getInstance();

        $sql = "SELECT id FROM cms_user_msg WHERE to_id = $user_id AND is_new = 1";
        $result = $inDB->query($sql);

        if($inDB->num_rows($result)) {
            $html =	' (<a style="color:red" href="/users/'.$user_id.'/messages.html">'.$inDB->num_rows($result).'</a>)';
            return $html;
        } else { return false; }
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет условия получения наград и выдает награду пользователю, если нужно
     * @param int $user_id
     * @return bool
     */
    public static function checkAwards($user_id=0){
        $inDB = cmsDatabase::getInstance();
        if ($user_id>0){
            $user = $inDB->get_fields('cms_users', "id={$user_id}", 'login');

            $sql = "SELECT * FROM cms_user_autoawards WHERE published = 1";
            $rs = $inDB->query($sql) or die('Error processing autoawards');
            if (mysql_num_rows($rs)) {
                $p_content = dbRowsCount('cms_content', "user_id=$user_id AND published = 1");
                $p_comment = dbRowsCount('cms_comments', "user_id=$user_id");
                $p_blog = dbRowsCount('cms_blog_posts', "user_id=$user_id AND published = 1");
                $p_forum = dbRowsCount('cms_forum_posts', "user_id=$user_id");
                $p_photo = dbRowsCount('cms_photo_files', "user_id=$user_id AND published = 1");
                $p_privphoto = dbRowsCount('cms_user_photos', "user_id=$user_id");
                $p_karma = dbGetField('cms_user_profiles', "user_id=$user_id", 'karma');
                while ($award = mysql_fetch_assoc($rs)){
                    if (!dbRowsCount('cms_user_awards', "user_id=$user_id AND award_id={$award['id']}")) {
                        $granted = ($award['p_content'] <= $p_content) &&
                                   ($award['p_comment'] <= $p_comment) &&
                                   ($award['p_blog'] <= $p_blog) &&
                                   ($award['p_forum'] <= $p_forum) &&
                                   ($award['p_photo'] <= $p_photo) &&
                                   ($award['p_privphoto'] <= $p_privphoto) &&
                                   ($award['p_karma'] <= $p_karma);
                        if ($granted){
                            $title = $award['title'];
                            $description = $award['description'];
                            $imageurl = $award['imageurl'];
                            $award_id = $award['id'];
                            $sql = "INSERT INTO cms_user_awards (user_id, pubdate, title, description, imageurl, from_id, award_id)
                                    VALUES ('$user_id', NOW(), '$title', '$description', '$imageurl', '0', '$award_id')";
                            $inDB->query($sql) ;
                            self::sendMessage(USER_UPDATER, $user_id, '[b]Получена награда:[/b] [url='.cmsUser::getProfileURL($user['login']).']'.$award['title'].'[/url]');
                        }
                    }
                }
            }
        }
        return true;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Отправляет личное сообщение пользователю
     * @param int $sender_id
     * @param int $receiver_id
     * @param string $message
     * @return bool
     */
    public static function sendMessage($sender_id, $receiver_id, $message){
        $inDB = cmsDatabase::getInstance();
        $sql = "INSERT INTO cms_user_msg (to_id, from_id, senddate, is_new, message)
                VALUES ('$receiver_id', '$sender_id', NOW(), 1, '$message')";
        $inDB->query($sql);
        return true;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Проверяет подписан ли пользователь на обновления контента
     * @param int $user_id
     * @param string $target
     * @param int $target_id
     * @return bool
     */
    public static function isSubscribed($user_id, $target, $target_id){
        $inDB = cmsDatabase::getInstance();
        return (bool)$inDB->rows_count('cms_subscribe', "user_id = $user_id AND target = '$target' AND target_id = $target_id");
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Добавляет/удаляет подписку пользователя на обновления контента
     * @param int $user_id
     * @param string $target
     * @param int $target_id
     * @param bool $subscribe
     * @return bool
     */
    public static function subscribe($user_id, $target, $target_id, $subscribe=true){
        $inDB = cmsDatabase::getInstance();
        if ($subscribe){
            if (!dbRowsCount('cms_subscribe', "user_id = $user_id AND target = '$target' AND target_id = $target_id")){
                $sql = "INSERT INTO cms_subscribe (user_id, target, target_id, pubdate)
                        VALUES ('{$user_id}', '{$target}', '{$target_id}', NOW())";
                $inDB->query($sql) ;
            }
        } else {
            $sql = "DELETE FROM cms_subscribe WHERE user_id = $user_id AND target = '$target' AND target_id = $target_id";
            $inDB->query($sql) ;
        }
        return true;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Рассылает личные сообщения с уведомлениями о новом комментарии
     * @param string $target
     * @param int $target_id
     * @return bool
     */
    public static function sendUpdateNotify($target, $target_id){

        $inUser = cmsUser::getInstance();
        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
        $inConf = cmsConfig::getInstance();

        //получаем последний комментарий и автора
        if ($target != 'forum'){
            $comment_sql    = "SELECT   c.target_title as target_title,
                                        c.target_link as target_link,
                                        c.id as id,
                                        IFNULL(u.nickname, c.guestname) as author
                               FROM cms_comments c
                               LEFT JOIN cms_users u ON c.user_id = u.id
                               WHERE c.target='{$target}' AND c.target_id='{$target_id}'
                               ORDER BY c.pubdate DESC
                               LIMIT 1";
        }

        //либо получаем нужную тему форума и автора последнего сообщения
        if ($target == 'forum'){
            $comment_sql    = "SELECT   ft.title as target_title,
                                        ft.id as thread_id,
                                        fp.id as post_id,
                                        u.nickname as author
                               FROM cms_forum_threads ft, cms_forum_posts fp, cms_users u
                               WHERE fp.thread_id='{$target_id}' AND fp.thread_id=ft.id AND fp.user_id = u.id
                               ORDER BY fp.pubdate DESC
                               LIMIT 1";
        }

        $comment_result = $inDB->query($comment_sql);
        if (!$inDB->num_rows($comment_result)){ return false; }
        $comment = $inDB->fetch_assoc($comment_result);

        //получаем список подписанных пользователей
        $users_sql  = "SELECT   p.cm_subscribe as subscribe_type,
                                u.email as email,
                                u.id as id
                       FROM cms_subscribe s, cms_users u, cms_user_profiles p
                       WHERE p.user_id = u.id AND
                             s.user_id = u.id AND
                             s.target = '{$target}' AND
                             s.target_id = '{$target_id}'";

        $users_result = $inDB->query($users_sql);
        if (!$inDB->num_rows($users_result)){ return false; }

        $postdate       = date('d/m/Y H:i:s');
        $letter_title   = ($target=='forum' ? 'Новое сообщение на форуме' : 'Новый комментарий');
        $letter_file    = ($target=='forum' ? 'newforumpost.txt' : 'newcomment.txt');
        $letter_path    = PATH.'/includes/letters/'.$letter_file;
        $letter         = file_get_contents($letter_path);

        if ($target == 'forum'){
            $comment['target_link'] = '/forum/thread-last'.$comment['thread_id'].'.html';
        } else {
            $comment['target_link'] = $comment['target_link'].'#c'.$comment['id'];
        }

        while ($user = $inDB->fetch_assoc($users_result)){

            if ($user['id'] == $inUser->id) { continue; }
            
            if ($user['subscribe_type']=='priv' || $user['subscribe_type']=='both'){
                $message = 'Произошло обновление: [url='.$comment['target_link'].']'.$comment['target_title'].'[/url]';
                self::sendMessage(USER_UPDATER, $user['id'], $message);
            }

            if ($user['subscribe_type']=='mail' || $user['subscribe_type']=='both'){
                if (!$user['email']) { continue; }
                $user_letter = $letter;
                $user_letter = str_replace('{sitename}', $inConf->sitename, $user_letter);
                $user_letter = str_replace('{answerlink}', 'http://'.$_SERVER['HTTP_HOST'].$comment['target_link'], $user_letter);
                $user_letter = str_replace('{pagetitle}', $comment['target_title'], $user_letter);
                $user_letter = str_replace('{date}', $postdate, $user_letter);
                $user_letter = str_replace('{author}', $comment['author'], $user_letter);
                $inCore->mailText($user['email'], 'Новый комментарий! - '.$inConf->sitename, $user_letter);
                unset($user_letter);
            }

        }

        return;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает тег ссылки на профиль пользователя с иконкой его пола
     * @param int $user_id
     * @param string $nickname
     * @param int $menuid
     * @param char $gender = m / f
     * @param string $css_style
     * @return html
     */
    public static function getGenderLink($user_id, $nickname='', $menuid=0, $gender='m', $login='', $css_style=''){
        $inDB = cmsDatabase::getInstance();
        $gender_img = '/components/users/images/male.gif';
        if (!$gender){
            $user = $inDB->get_field('cms_user_profiles', 'user_id='.$user_id, 'gender');
        }
        if ($gender){
            switch($gender){
                case 'm': $gender_img = '/components/users/images/male.gif'; break;
                case 'f': $gender_img = '/components/users/images/female.gif'; break;
                default : $gender_img = '/components/users/images/male.gif'; break;
            }
        }
        if (!$nickname || !$login){
            $user       = $inDB->get_fields('cms_users', 'id='.$user_id, 'nickname, login');
            $nickname   = $user['nickname'];
            $login      = $user['login'];
        }
        return '<a style="height:16px; line-height:16px; background:url('.$gender_img.') no-repeat left center; padding-left:18px; '.$css_style.'" href="'.cmsUser::getProfileURL($login).'" class="user_gender_link">'.$nickname.'</a>';
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает список <select> с фотографиями из личного альбома указанного пользователя
     * @param int $user_id
     * @return html
     */
    public static function getPhotosList($user_id){
        
        $inDB = cmsDatabase::getInstance();

        $sql = "SELECT *
                FROM cms_user_photos
                WHERE user_id = $user_id
                ORDER BY title ASC";
        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)){
            $html = '<select name="photolist" id="photolist">'."\n";
            while($photo = $inDB->fetch_assoc($rs)){
                $html .= '<option value="'.$photo['imageurl'].'">'.$photo['title'].'</option>'."\n";
            }
            $html .= '</select>'."\n";
        } else {
            $html = '<span style="padding-left:5px;padding:right:5px">В вашем альбоме нет фотографий</span>'."\n";
        }

        return $html;
        
    }

// ============================================================================ //
// ============================================================================ //

    public static function getProfileURL($user_login) {
        return HOST . '/' . self::PROFILE_LINK_PREFIX . urlencode($user_login);
    }

// ============================================================================ //
// ============================================================================ //

    public static function getProfileLink($user_login, $user_nickname) {
        return '<a href="'.self::getProfileURL($user_login).'" title="'.$user_nickname.'">'.$user_nickname.'</a>';
    }

// ============================================================================ //
// ============================================================================ //

    public static function updateStats($user_id){

        $inDB   = cmsDatabase::getInstance();
        $inCore = cmsCore::getInstance();

        $stats  = array();

        $stats['count']                     = array();
        $stats['count']['comments']         = (int)$inDB->rows_count('cms_comments', "user_id={$user_id} AND published = 1");
        $stats['count']['forum']            = (int)$inDB->rows_count('cms_forum_posts', "user_id={$user_id}");
        $stats['count']['photos']           = (int)$inDB->rows_count('cms_user_photos', "user_id={$user_id}");
        $stats['count']['board']            = (int)$inDB->rows_count('cms_board_items', "user_id={$user_id} AND published=1");
        $stats['count']['files_public']     = (int)$inDB->rows_count('cms_user_files', "user_id={$user_id} AND allow_who = 'all'");
        $stats['count']['files_private']    = (int)$inDB->rows_count('cms_user_files', "user_id={$user_id}");

        $stats['rating']                    = self::getRating($user_id);        

        $stats_yaml    = ($stats) ? $inCore->arrayToYaml($stats) : "---\n";
        
        $inDB->query("UPDATE cms_user_profiles SET stats = '{$stats_yaml}' WHERE user_id={$user_id}");
        
    }

// ============================================================================ //
// ============================================================================ //

}

?>
