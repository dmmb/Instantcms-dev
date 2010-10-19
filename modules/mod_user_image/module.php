<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_user_image($module_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT u.id uid, u.nickname author, u.login as login, p.imageurl, p.title, pr.gender gender
				FROM cms_user_photos p
				LEFT JOIN cms_users u ON u.id = p.user_id
				LEFT JOIN cms_user_profiles pr ON pr.user_id = u.id
				WHERE p.allow_who = 'all' AND u.is_deleted = 0 AND u.is_locked = 0
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