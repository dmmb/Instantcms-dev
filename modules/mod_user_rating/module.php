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

	function mod_user_rating($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);

        if (!isset($cfg['count'])) { $cfg['count'] = 20; }
		if (!isset($cfg['view_type'])) { $cfg['view_type'] = 'rating'; }	
		if ($cfg['view_type']!='rating' && $cfg['view_type']!='karma') {
			$cfg['view_type'] = 'rating';
		}
		if ($cfg['view_type'] == 'rating') { $target = 'Рейтинг'; } else { $target = 'Карма'; }

		$sql = "SELECT u.id, u.login, u.nickname, u.rating as rating, u.is_deleted, p.karma as karma, p.user_id, p.imageurl, u.status
				FROM cms_users u
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
				WHERE u.is_deleted = 0 AND u.is_locked = 0
				ORDER BY ".$cfg['view_type']." DESC
				LIMIT ".$cfg['count'];		
		$result = $inDB->query($sql);
		
		$users = array();
		$is_usr = false;
		
		if ($inDB->num_rows($result)){
			
				$is_usr=true;
				
				if (!function_exists('usrImageNOdb')){ //if not included earlier
				include_once(PATH.'/components/users/includes/usercore.php');
				}
			
				while($usr = $inDB->fetch_assoc($result)){
					$usr['profileurl'] = cmsUser::getProfileURL($usr['login']);
					$usr['usrimage']   = usrImageNOdb($usr['id'], 'small', $usr['imageurl'], $usr['is_deleted']);
					$users[] = $usr;
							}
				}
		
		$smarty = $inCore->initSmarty('modules', 'mod_user_rating.tpl');			
		$smarty->assign('users', $users);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_usr', $is_usr);
		$smarty->display('mod_user_rating.tpl');
				
		return true;
	
	}
?>