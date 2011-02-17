<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_user_friend($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);
		
		$user_id = $inUser->id;
		
		if (!$user_id){ return false; }
		
		if ($cfg['view_type'] == 'table') {
			if (!function_exists('usrLink') && !function_exists('usrImageNOdb')){ //if not included earlier
			$inCore->includeFile('components/users/includes/usercore.php');
			}
			
			$sql = "SELECT
					CASE
					WHEN f.from_id = $user_id
					THEN f.to_id
					WHEN f.to_id = $user_id
					THEN f.from_id
					END AS user_id, u.login, u.nickname, u.is_deleted, p.imageurl
					FROM cms_user_friends f
					INNER JOIN cms_online o ON o.user_id = CASE WHEN f.from_id = $user_id THEN f.to_id WHEN f.to_id = $user_id THEN f.from_id END
					LEFT JOIN cms_users u ON u.id = o.user_id
					LEFT JOIN cms_user_profiles p ON p.user_id = u.id
					WHERE (from_id = $user_id OR to_id = $user_id) AND is_accepted =1 LIMIT ".$cfg['limit'];
		} else {
			$sql = "SELECT
					CASE
					WHEN f.from_id = $user_id
					THEN f.to_id
					WHEN f.to_id = $user_id
					THEN f.from_id
					END AS user_id, u.login, u.nickname
					FROM cms_user_friends f
					INNER JOIN cms_online o ON o.user_id = CASE WHEN f.from_id = $user_id THEN f.to_id WHEN f.to_id = $user_id THEN f.from_id END
					LEFT JOIN cms_users u ON u.id = o.user_id
					WHERE (from_id = $user_id OR to_id = $user_id) AND is_accepted =1 LIMIT ".$cfg['limit'];

		}

		$result = $inDB->query($sql) ;
		$total	= $inDB->num_rows($result);

        if ($total){
			$friends = array();
            while($friend = $inDB->fetch_assoc($result)){
                $friend['avatar'] = ($cfg['view_type'] == 'table') ? usrLink(usrImageNOdb($friend['user_id'], 'small', $friend['imageurl'], $friend['is_deleted']), $friend['login']) : false;
                $friend['user_link'] = cmsUser::getProfileLink($friend['login'], $friend['nickname']);
				$friends[$friend['user_id']] = $friend;
            }
        }
        
		$smarty = $inCore->initSmarty('modules', 'mod_user_friend.tpl');
		$smarty->assign('friends', $friends);
		$smarty->assign('total', sizeof($friends));
		$smarty->assign('cfg', $cfg);
		$smarty->display('mod_user_friend.tpl');
		return true;

	}
?>