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

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'userphoto': $photo = $this->inDB->get_fields('cms_user_photos', "id={$target_id}", 'user_id, title');
                              if (!$photo) { return false; }
                              $result['link']  = '/users/'.$photo['user_id'].'/photo'.$target_id.'.html';
                              $result['title'] = $photo['title'];
                              break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getUser($user_id){

		$sql = "SELECT		        
				u.id as id,
				u.login,
				u.nickname,
				u.email,
				u.icq,
		        u.is_deleted as is_deleted,
                u.regdate fregdate,
                u.birthdate as birthdate,
                u.status as status_text,
				DATE_FORMAT(u.status_date, '%d-%m-%Y %H:%i') as status_date,
                u.logdate as flogdate,
				u.rating as user_rating,
                p.city, p.description, p.showmail, p.showbirth, p.showicq,
				p.karma, p.imageurl, p.allow_who,
				p.gender as gender,	p.formsdata,			
				u.group_id,
				g.title as grp,
				o.user_id as status,
				b.user_id as banned
                FROM cms_users u
				LEFT JOIN cms_user_profiles p ON u.id = p.id
				LEFT JOIN cms_user_groups g ON u.group_id = g.id
				LEFT JOIN cms_online o ON u.id = o.user_id
				LEFT JOIN cms_banlist b ON u.id = b.user_id
                WHERE u.is_locked = 0 AND u.id = $user_id
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }
        
        $user = $this->inDB->fetch_assoc($result);

        $user = cmsCore::callEvent('GET_USER', $user);

        return $user;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getUserShort($user_id = 0){
				
		$inUser = cmsUser::getInstance();
				
		if ($inUser->id == $user_id) { 
		
			$user['id'] 		= $user_id;
			$user['login'] 		= $inUser->login;
			$user['nickname'] 	= $inUser->nickname;
		
		} else {
		
			$user = $this->inDB->get_fields('cms_users', "id = '$user_id'", 'id, nickname, login');
        
		}
        
		if (!$user){ cmsCore::error404(); }

        return $user;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getUserTotal($online = false){
		
		if (!$online) {
			$total = $this->inDB->rows_count('cms_users', 'is_locked=0 AND is_deleted=0');
		} else {
			$total = $this->inDB->rows_count('cms_online o LEFT JOIN cms_users u ON  u.id = o.user_id', 'u.is_locked = 0 AND u.is_deleted = 0 GROUP BY o.user_id');
		}
		if (!$total ){ return false; }

        return $total ;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function isNewFriends($user_id, $from_id=0){

        if (!$from_id){
            $sql = "SELECT 1 FROM cms_user_friends WHERE to_id = $user_id AND is_accepted = 0";
        } else {
            $sql = "SELECT 1 FROM cms_user_friends WHERE to_id = $user_id AND from_id = $from_id AND is_accepted = 0";
        }

        $result = $this->inDB->query($sql);

        return (bool)$this->inDB->num_rows($result);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getNewFriends($user_id){

        $friends = array();

		$sql = "SELECT f.*, u.nickname as sender, u.login as sender_login, p.imageurl as sender_img
                FROM cms_user_friends f
				LEFT JOIN cms_users u ON f.from_id = u.id
				LEFT JOIN cms_user_profiles p ON p.id = u.id
                WHERE f.to_id = $user_id AND f.is_accepted = 0";
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

    public function addInvite($invite) {

        $sql = "INSERT INTO cms_user_invites (code, owner_id, createdate, is_used)
                VALUES ('{$invite['code']}', '{$invite['owner_id']}', NOW(), 0)";

        $this->inDB->query($sql);

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function giveInvites($count, $has_karma) {

        $sql = "SELECT  u.id as id,
                        SUM(k.points) as karma
                FROM cms_users u, cms_user_karma k
                WHERE k.user_id = u.id
                GROUP BY u.id";

        $res = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($res)) { return false; }

        while($user = $this->inDB->fetch_assoc($res)){

            if ($user['karma'] < $has_karma){ continue; }

            for($c=1; $c<=$count; $c++){

                $invite['code'] = md5($user['id'] .'$'. rand(10000,65535) . '$' . time() . '$' . $c);
                $invite['owner_id'] = $user['id'];
                $this->addInvite($invite);
                
            }

            $this->inDB->query("UPDATE cms_users SET invdate = NOW() WHERE id = '{$user['id']}'");

        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteInvites() {

        $this->inDB->query('DELETE FROM cms_user_invites WHERE is_used = 0');

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}