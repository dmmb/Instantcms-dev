<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_whoonline($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT DISTINCT o.user_id, u.*, DATE_FORMAT(u.regdate, '%d-%m-%Y (%H:%i)') as fdate, p.gender as gender
				FROM cms_users u, cms_online o, cms_user_profiles p
				WHERE o.user_id = u.id AND p.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0
				ORDER BY u.regdate DESC
				";		
		
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){	
				$total = $inDB->num_rows($result);
				$now = 0;
				while($usr = $inDB->fetch_assoc($result)){					
					if ($inCore->userIsAdmin($usr['id'])){
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:red");
					} elseif ($inCore->userIsEditor($usr['id'])) {	
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:green");
					} else {
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
					}
					
					if ($now < $total-1) { echo ', '; }
					$now ++;
				}			
		} else { echo '<div><strong>Пользователей:</strong> 0</div>'; }
		
		$sql = "SELECT id FROM cms_online WHERE user_id = 0";
		$result = $inDB->query($sql) ;
		
		echo '<div style="margin-top:10px"><strong>Гостей:</strong> '.@$inDB->num_rows($result).'</div>';
						
		return true;	
	}
?>