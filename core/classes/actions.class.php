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

class cmsActions {

    private static $instance;

    public $show_targets = true;

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

        if (!$inUser->id){ return false; }

        $action = self::getAction($action_name);

        if (!$action) { return false; }

		$params['object']      =  $inDB->escape_string(stripslashes($params['object']));
		$params['target']      =  $inDB->escape_string(stripslashes($params['target']));
		$params['description'] =  $inDB->escape_string(stripslashes($params['description']));
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
     * Управляет показом категорий, в которых находятся объекты вызвавшие события
     * @param bool $show Показывать категории?
     *
     */
    public function showTargets($show) {
        $this->show_targets = $show;
        return;
    }

    /**
     * Возвращает массив событий для ленты активности
     * @return array
     */
    public function getActionsLog(){

        $inDB   = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

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

                FROM cms_actions_log log
				INNER JOIN cms_actions a ON a.id = log.action_id AND a.is_visible = 1
				LEFT JOIN cms_users u ON u.id = log.user_id

                ORDER BY log.id DESC

                LIMIT 100
                
                ";

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
    static function removeOldLog($pubdays = 30){

        $inDB   = cmsDatabase::getInstance();
		
        $sql = "DELETE FROM cms_actions_log WHERE DATEDIFF(NOW(), pubdate) > pubdays";

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
