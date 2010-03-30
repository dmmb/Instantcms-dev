<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_users{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getUser($user_id){
        global $_LANG;

        $sql = "SELECT u.*, p.*, u.id as id, u.is_deleted as is_deleted, IFNULL(p.gender, 0) as gender, u.rating as user_rating,
                g.title as grp,
                u.regdate fregdate,
                u.birthdate as birthdate,
                u.logdate as flogdate,
                p.gender as gender,
                u.status as status_text,
                DATE_FORMAT(u.status_date, '%d-%m-%Y %H:%i') as status_date
                FROM cms_users u, cms_user_profiles p, cms_user_groups g
                WHERE u.is_locked = 0 AND p.user_id = u.id AND u.id = $user_id AND u.group_id = g.id
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }
        
        $user = $this->inDB->fetch_assoc($result);

        $user = cmsCore::callEvent('GET_USER', $user);

        return $user;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function isNewFriends($user_id, $from_id=0){

        if (!$from_id){
            $sql = "SELECT * FROM cms_user_friends WHERE to_id = $user_id AND is_accepted = 0";
        } else {
            $sql = "SELECT * FROM cms_user_friends WHERE to_id = $user_id AND from_id = $from_id AND is_accepted = 0";
        }

        $result = $this->inDB->query($sql);

        return (bool)$this->inDB->num_rows($result);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getNewFriends($user_id){

        $friends = array();

        $sql = "SELECT f.*, u.*, u.nickname as sender, u.login as sender_login, p.imageurl as sender_img
                FROM cms_user_friends f, cms_users u, cms_user_profiles p
                WHERE f.to_id = $user_id AND f.is_accepted = 0 AND f.from_id = u.id AND p.user_id = u.id";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($friend = $this->inDB->fetch_assoc($result)){
            $friends[] = $friend;
        }

        $friends = cmsCore::callEvent('GET_NEW_FRIENDS', $friends);

        return $friends;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteUser($user_id){

        cmsCore::callEvent('DELETE_USER', $user_id);

        if ($user_id == 1) { return false; }

        $this->inDB->query("UPDATE cms_users SET is_deleted = 1 WHERE id=$user_id");
        $this->inDB->query("DELETE FROM cms_user_friends WHERE to_id = $user_id OR from_id = $user_id");
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteUsers($id_list){

        foreach($id_list as $key=>$id){
            $this->deleteUser($id);
        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteGroup($group_id){

        cmsCore::callEvent('DELETE_USER_GROUP', $group_id);

        $sql = "SELECT id FROM cms_users WHERE group_id = {$group_id}";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($user = $this->inDB->fetch_assoc($result)){
                $this->deleteUser($user['id']);
            }
        }

        $this->inDB->query("DELETE FROM cms_user_groups WHERE id = {$group_id}");

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteGroups($id_list){

        foreach($id_list as $key=>$id){
            $this->deleteGroup($id);
        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPluginsOutput($user){

        $inCore         = cmsCore::getInstance();

        $plugins_list   = array();

        $plugins        = $inCore->getEventPlugins('USER_PROFILE');

        foreach($plugins as $plugin_name){

            $html   = '';

            $plugin = $inCore->loadPlugin( $plugin_name );

            if ($plugin!==false){                
                $html = $plugin->execute($event, $user);
            }

            if ($html){

                $p['name']      = $plugin_name;
                $p['title']     = $plugin->info['tab'] ? $plugin->info['tab'] : $plugin->info['title'];
                $p['html']      = $html;

                $plugins_list[] = $p;

                $inCore->unloadPlugin($plugin);

            }

        }

        return $plugins_list;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}