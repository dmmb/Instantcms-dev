<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latestclubs($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);

        global $_LANG;
        
        if (!function_exists('clubTotalMembers')){ //if not included earlier
		$inCore->loadLib('clubs');
        }
	
		if (!isset($cfg['count'])) { $cfg['count'] = 5;}

		$sql =  "SELECT c.*
				 FROM cms_clubs c
				 WHERE c.published = 1
				 ORDER BY c.id DESC
				 LIMIT ".$cfg['count'];
 	
		$result = $inDB->query($sql);
						
        $is_clubs = false;
		
		if ($inDB->num_rows($result)){	
		
		    $is_clubs = true;
			
			while ($club = $inDB->fetch_assoc($result)){
				if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
					if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/clubs/small/'.$club['imageurl'])){
						$club['imageurl'] = 'nopic.jpg';
					}
				}
				$club['members'] = clubTotalMembers($club['id']);
				$clubs[] = $club;
			}
		
		}

			$smarty = $inCore->initSmarty('modules', 'mod_clubs.tpl');			
			$smarty->assign('clubs', $clubs);
		$smarty->assign('is_clubs', $is_clubs);
			$smarty->display('mod_clubs.tpl');
						
		return true;
	
}
?>