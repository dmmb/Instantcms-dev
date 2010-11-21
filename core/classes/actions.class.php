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

class cmsActions {

    private static $instance;

    private $show_targets = true;
	private $where = '';
    private $limit = 100;
    private $only_friends = false;

// ============================================================================ //
// ============================================================================ //

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Регистрирует новый тип действия
     * @param str $component
     * @param array $action (name, title, message)
     * @return bool
     */
    public static function registerAction($component, $action){

        $inDB = cmsDatabase::getInstance();

        $is_tracked = 1;
        $is_visible = 1;

        $sql = "INSERT INTO cms_actions (component, name, title, message, is_tracked, is_visible)
                VALUES ('{$component}', '{$action['name']}', '{$action['title']}',
                        '{$action['message']}', '{$is_tracked}', '{$is_visible}')";

        $inDB->query($sql);

        return true;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Находит описание действия по его названию
     * @param str $action_name
     * @param bool $only_tracked
     * @return array | false
     */
    public static function getAction($action_name, $only_tracked=true){

        $inDB = cmsDatabase::getInstance();

        $tracked = $only_tracked ? 'AND is_tracked=1' : '';

        $action = $inDB->get_fields('cms_actions', "name='{$action_name}' {$tracked}", '*');

        return is_array($action) ? $action : false;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Добавляет действие в ленту активности
     * @param str $action_name
     * @param array $params (object, object_url, target, target_url, description)
     * @return bool
     */
    public static function log($action_name, $params){

        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        if (!$inUser->id && $action_name != 'add_user'){ return false; }

        $action = self::getAction($action_name);

        if (!$action) { return false; }

		$params['object']      =  $inDB->escape_string(stripslashes(str_replace(array('\r', '\n'), ' ', $params['object'])));
		$params['target']      =  $inDB->escape_string(stripslashes(str_replace(array('\r', '\n'), ' ', $params['target'])));
		$params['description'] =  $inDB->escape_string(stripslashes(str_replace(array('\r', '\n'), ' ', $params['description'])));
		$params['user_id']     =  $params['user_id'] ? $params['user_id'] : $inUser->id;
		
        $sql = "INSERT INTO cms_actions_log (action_id, pubdate, user_id, object, object_url, object_id,
                                             target, target_url, target_id, description, is_friends_only, is_users_only)
                VALUES ('{$action['id']}', NOW(), '{$params['user_id']}',
                        '{$params['object']}', '{$params['object_url']}', '{$params['object_id']}',
                        '{$params['target']}', '{$params['target_url']}', '{$params['target_id']}',
                        '{$params['description']}', '{$params['is_friends_only']}', '{$params['is_users_only']}')";

        $inDB->query($sql);

        return true;

    }

    /**
     * Удаляет из ленты активности все события определенного типа для указанного объекта
     * @param string $action_name Тип события
     * @param int $object_id Идентификатор объекта
     * @return bool
     */
    public static function removeObjectLog($action_name, $object_id, $user_id = false){
        
        $inDB = cmsDatabase::getInstance();
        
        $action = self::getAction($action_name);
        
        $usr_sql = $user_id ? "AND user_id = {$user_id}" : '';
        
        $sql = "DELETE 
                FROM cms_actions_log 
                WHERE action_id = '{$action['id']}' AND object_id = '{$object_id}' $usr_sql";

        $inDB->query($sql);

        return true;
        
    }

// ============================================================================ //
// ============================================================================ //

	/**
	 * Добавляет условие в запрос ленты событий
     * @param str $condition Условие
     *
     */
	public function where($condition){
		$this->where .= "AND ({$condition})";
	}

    /**
     * Управляет показом категорий, в которых находятся объекты вызвавшие события
     * @param bool $show Показывать категории?
     *
     */
    public function showTargets($show) {
        $this->show_targets = $show;
        return;
    }

    /**
     * Устанавливает лимит на количество получаемых действий
     * @param int $from C какого
     * @param int $howmany Сколько (необязательно)
     */
    public function limitIs($from, $howmany=0) {
        $this->limit = (int)$from;
        if ($howmany){
            $this->limit .= ', '.$howmany;
        }
    }

    /**
     * Устанавливает лимит для действий выводимых на одной странице
     * @param int $page Страница
     * @param int $perpage Действий на странице
     */
    public function limitPage($page, $perpage) {
        $this->limitIs(($page-1)*$perpage, $perpage);
    }


    /**
     * Включает режим показа только событий друзей
     *
     */
	public function onlyMyFriends(){

		$inUser = cmsUser::getInstance();

		$friends = cmsUser::getFriends($inUser->id); 

		if (!is_array($friends)){ $this->where('1=0'); return; }

		$f_list = array();

		foreach($friends as $friend){
			$f_list[] = $friend['id']; 
		}

		$f_list = rtrim(implode(',', $f_list), ',');

        if ($f_list){
            $this->where("log.user_id IN ({$f_list})");
        } else {
            $this->where('1=0');
        }

        $this->only_friends = true;

		return;

	}

    public function onlySelectedTypes($types) {

        if (!is_array($types)){ $this->where('1=0'); return; }

        $t_list = array();

		foreach($types as $type){
			$t_list[] = $type;
		}

		$t_list = rtrim(implode(',', $t_list), ',');

		$this->where("a.id IN ({$t_list})");

		return;

    }

    /**
     * Возвращает массив событий для ленты активности
     * @return array
     */
    public function getActionsLog(){

        $inDB   = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        if (!$this->only_friends){ $this->where('log.is_friends_only = 0'); }
        if (!$inUser->id) { $this->where('log.is_users_only = 0'); }
        
        $sql = "SELECT log.object as object,
                       log.object_url as object_url,
                       log.target as target,
                       log.target_url as target_url,
                       log.pubdate as pubdate,
                       log.description as description,
                       a.message as message,
                       a.name as name,
                       u.nickname as user_nickname,
                       u.login as user_login

                FROM cms_actions_log log,
                     cms_actions a,
                     cms_users u
                     
                WHERE   log.user_id = u.id AND 
                        log.action_id = a.id AND
                        a.is_visible = 1
						{$this->where}

                ORDER BY log.pubdate DESC
                ";

        if ($this->limit){
            $sql .= "LIMIT {$this->limit}";
        }

        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return false; }

        $actions = array();

        while($action = $inDB->fetch_assoc($result)){

            if ($this->show_targets){
                $action['message'] = str_replace('|', '', $action['message']);
            } else {
                $action['message'] = substr($action['message'], 0, strpos($action['message'], '|'));
            }

            if ($action['object']){
                $action['object_link']  = $action['object_url'] ? '<a href="'.$action['object_url'].'" class="act_obj_'.$action['name'].'">'.$action['object'].'</a>' : $action['object'];
            }
            if ($action['target']){
                $action['target_link']  = '<a href="'.$action['target_url'].'" class="act_tgt_'.$action['name'].'">'.$action['target'].'</a>';
            }
            if ($action['message']){
                $action['message']      = sprintf($action['message'], $action['object_link'], $action['target_link']);
            }

            $action['user_url']     = cmsUser::getProfileURL($action['user_login']);
            $action['pubdate']      = cmsCore::dateDiffNow($action['pubdate']);

            $actions[] = $action;

        }

        return $actions;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Удаляет старые записи ленты
     * @param int $pubdays
     * @return bool
     */
    static function removeOldLog($pubdays = 60){

        $inDB   = cmsDatabase::getInstance();
		
        $sql = "DELETE FROM cms_actions_log WHERE DATEDIFF(NOW(), pubdate) > '{$pubdays}'";

        $inDB->query($sql);

        return true;

    }
	
    /**
     * Удаляет из ленты записи одного пользователя
     * @param int $user_id
     * @return bool
     */
    static function removeUserLog($user_id){
		
		if (!$user_id) { return false; }

        $inDB = cmsDatabase::getInstance();
		
        $sql  = "DELETE FROM cms_actions_log WHERE user_id = '$user_id'";

        $inDB->query($sql);

        return true;

    }
    
    
}
?>
