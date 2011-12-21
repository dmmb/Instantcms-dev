<?php
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

class cmsActions {

    private static $instance;

	private static $defaultLogArray = array('user_id'=>'','object'=>'','object_url'=>'','object_id'=>'','target'=>'','target_url'=>'','target_id'=>'','description'=>'','is_friends_only'=>'','is_users_only'=>'');

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

    private function resetConditions(){

        $this->where        = '';
        $this->limit        = '';

    }

// ============================================================================ //
// ============================================================================ //

    public static function checkLogArrayValues($input_array = array()) {

		if(!$input_array || !is_array($input_array)) { return array(); }

		$inDB = cmsDatabase::getInstance();

		// убираем ненужные ячейки массива
		foreach($input_array as $k=>$v){
		   	if (!isset(self::$defaultLogArray[$k])) { unset($input_array[$k]); continue; }
			$input_array[$k] =  $inDB->escape_string(stripslashes(str_replace(array('\r', '\n'), ' ', $input_array[$k])));
			$input_array[$k] =  preg_replace('/\[hide\](.*?)\[\/hide\]/iu', '', $input_array[$k]);
			$input_array[$k] =  preg_replace('/\[hide\](.*?)$/iu', '', $input_array[$k]);
		}

		return $input_array;

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

		$params = self::checkLogArrayValues($params);
		if(!$params) { return false; }

        $inDB   = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        if (!$inUser->id && $action_name != 'add_user'){ return false; }

        $action = self::getAction($action_name);
        if (!$action) { return false; }

		$params['user_id'] =  $params['user_id'] ? $params['user_id'] : $inUser->id;
		
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

    /**
     * Удаляет из ленты активности все события определенного типа для указанной цели
     * @param string $action_name Тип события
     * @param int $target_id Идентификатор цели
     * @return bool
     */
    public static function removeTargetLog($action_name, $target_id, $user_id = false){
        
        $inDB = cmsDatabase::getInstance();
        
        $action = self::getAction($action_name);
        
        $usr_sql = $user_id ? "AND user_id = {$user_id}" : '';
        
        $sql = "DELETE 
                FROM cms_actions_log 
                WHERE action_id = '{$action['id']}' AND target_id = '{$target_id}' $usr_sql";

        $inDB->query($sql);

        return true;
        
    }

    public static function removeLogById($id){

        $inDB = cmsDatabase::getInstance();

        $inDB->query("DELETE FROM cms_actions_log WHERE id = '{$id}'");

    }

    /**
     * Обновляет запись ленты
     * @return bool
     */
    public static function updateLog($action_name, $params, $object_id=0, $target_id=0){

		$inDB = cmsDatabase::getInstance();

		$params = self::checkLogArrayValues($params);

		if(!$params) { return false; }
		if(!$object_id && !$target_id) { return false; }

		// Получаем id записи 
		$action = self::getAction($action_name);
		if (!$action) { return false; }

		// формируем запрос на вставку в базу
		foreach($params as $field=>$value){
			$set .= "{$field} = '{$value}',";
		}
		$set = rtrim($set, ',');

		// если обновляем сам объект
		if($object_id){
			$inDB->query("UPDATE cms_actions_log SET {$set} WHERE action_id='{$action['id']}' AND object_id='{$object_id}' LIMIT 1");
		}
		// если обновляем все место назначения
		if($target_id){
			$inDB->query("UPDATE cms_actions_log SET {$set} WHERE action_id='{$action['id']}' AND target_id='{$target_id}'");
		}

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
		if ($this->where) {
		$this->where .= "AND ({$condition})";
		} else {
		    $this->where  = "({$condition})";
		}
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

    /**
     * Показывает события определенного юзера
     *
     */
	public function whereUserIs($user_id){

        if ($user_id){
            $this->where("log.user_id = '$user_id'");
        } else {
            $this->where('1=0');
        }

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

// ============================================================================ //
// ============================================================================ //

    /**
     * Возвращает количество записей по условиям
     * @return int
     */
    public function getCountActions() {

        $inDB   = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        if (!$this->only_friends){ $this->where('log.is_friends_only = 0'); }
        if (!$inUser->id) { $this->where('log.is_users_only = 0'); }

        $sql = "SELECT 1
                FROM cms_actions_log log
				LEFT JOIN cms_actions a ON a.id = log.action_id AND a.is_visible = 1
                WHERE {$this->where}
                ";

		$result = $inDB->query($sql);

		return $inDB->num_rows($result);

    }

// ============================================================================ //
// ============================================================================ //
    /**
     * Возвращает массив событий для ленты активности
     * @return array
     */
    public function getActionsLog(){

        $inDB   = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        if (!$this->only_friends){ $this->where('log.is_friends_only = 0'); }
        if (!$inUser->id) { $this->where('log.is_users_only = 0'); }

        $sql = "SELECT log.id as id,
                       log.object as object,
                       log.object_url as object_url,
                       log.target as target,
                       log.target_url as target_url,
                       log.pubdate as pubdate,
                       log.description as description,
                       a.message as message,
                       a.name as name,
                       u.nickname as user_nickname,
                       u.login as user_login

                FROM cms_actions_log log
                LEFT JOIN cms_actions a ON a.id = log.action_id AND a.is_visible = 1
                LEFT JOIN cms_users u ON u.id = log.user_id
   
                WHERE {$this->where}

                ORDER BY log.id DESC
                ";

        if ($this->limit){
            $sql .= "LIMIT {$this->limit}";
        }

        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return false; }

		// Сбрасываем условия
        $this->resetConditions();

        $actions = array();

        while($action = $inDB->fetch_assoc($result)){

            if ($this->show_targets){
                $action['message'] = str_replace('|', '', $action['message']);
            } else {
                $action['message'] = mb_substr($action['message'], 0, mb_strpos($action['message'], '|'));
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

            $action['is_new'] = false;

            if ($inUser->id){
                $action['is_new'] = (bool)(strtotime($action['pubdate']) > strtotime($inUser->logdate));
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
    public static function removeOldLog($pubdays = 60){

        $inDB   = cmsDatabase::getInstance();
		
        $sql = "DELETE FROM cms_actions_log WHERE DATEDIFF(NOW(), pubdate) > '{$pubdays}'";

        $inDB->query($sql);

        return true;

    }

// ============================================================================ //
// ============================================================================ //
    /**
     * Удаляет из ленты записи одного пользователя
     * @param int $user_id
     * @return bool
     */
    public static function removeUserLog($user_id){
		
		if (!$user_id) { return false; }

        $inDB = cmsDatabase::getInstance();
		
        $sql  = "DELETE FROM cms_actions_log WHERE user_id = '$user_id'";

        $inDB->query($sql);

        return true;

    }

// ============================================================================ //
// ============================================================================ //
    /**
     * Получает массив компонентов, зарегистрированных в ленте активности
     * @return array
     */
    public static function getActionsComponents(){

        $inDB = cmsDatabase::getInstance();
		
        $components = $inDB->get_table('cms_components com INNER JOIN cms_actions act ON act.component = com.link', 'com.internal=0 AND com.published=1 GROUP BY com.link', 'com.title, com.link');

        return $components;

    }    
    
}
?>
