<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	function mod_user_image($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT u.id uid, u.nickname author, u.login as login, p.imageurl, p.title, p.id, pr.gender gender
				FROM cms_user_photos p
				LEFT JOIN cms_users u ON u.id = p.user_id
				LEFT JOIN cms_user_profiles pr ON pr.user_id = u.id
				LEFT JOIN cms_user_albums a ON a.id = p.album_id
				WHERE p.allow_who = 'all' AND u.is_deleted = 0 AND u.is_locked = 0
                      AND p.album_id > 0 AND a.allow_who = 'all'
				ORDER BY RAND()
				LIMIT 1";
		
		$result = $inDB->query($sql) ;
		
		$users = array();
		$is_usr = false;
		
		if ($inDB->num_rows($result)){
			$is_usr=true;

			while ($usr = $inDB->fetch_assoc($result)){
				
				$usr['genderlink'] = cmsUser::getGenderLink($usr['uid'], $usr['author'], null, $usr['gender'], $usr['login']);
				
				$users[] = $usr;
			
			}
		}

		$smarty = $inCore->initSmarty('modules', 'mod_user_image.tpl');			
		$smarty->assign('users', $users);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_usr', $is_usr);
		$smarty->display('mod_user_image.tpl');
				
		return true;	
		
	}
?>