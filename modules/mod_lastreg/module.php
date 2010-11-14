<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_lastreg($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT u.*, p.imageurl 
				FROM cms_users u
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
				WHERE u.is_deleted = 0 AND u.is_locked=0
				ORDER BY u.regdate DESC
				LIMIT ".$cfg['newscount']."
				";		
		
		$result = $inDB->query($sql) ;
		
		$is_last_reg = false;
		
		if ($inDB->num_rows($result)){	
			
			$is_last_reg = true;
			$usrs = array();
			
			if ($cfg['view_type']=='table' || $cfg['view_type']=='hr_table'){
				if (!function_exists('usrImageNOdb')){ //if not included earlier
				include_once($_SERVER['DOCUMENT_ROOT'].'/components/users/includes/usercore.php');
				}	
				while($usr = $inDB->fetch_assoc($result)){
					$usr['avatar'] = usrImageNOdb($usr['id'], 'small', $usr['imageurl'], $usr['is_deleted']);
					$usrs[] = $usr;
				}
			}
			
			if ($cfg['view_type']=='list'){
				$total = $inDB->num_rows($result);
				while($usr = $inDB->fetch_assoc($result)){				
					$usrs[] = $usr;
				}
				$total_all = dbRowsCount('cms_users', 'is_deleted=0 AND is_locked=0');
				}
			}
		
		$smarty = $inCore->initSmarty('modules', 'mod_lastreg.tpl');			
		$smarty->assign('usrs', $usrs);
		$smarty->assign('cfg', $cfg);
		if ($cfg['view_type']=='list'){
			$smarty->assign('total', $total);
			$smarty->assign('total_all', $total_all);
		}
		$smarty->assign('is_last_reg', $is_last_reg);
		$smarty->display('mod_lastreg.tpl');	
				
		return true;
	
	}
?>