<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_bestclubs($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		
		if (!function_exists('clubTotalMembers')){ //if not included earlier
		$inCore->loadLib('clubs');
		}
	
		$cfg = $inCore->loadModuleConfig($module_id);
	
		if (!isset($cfg['count'])) { $cfg['count'] = 5;}
		if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0;}

		$sql =  "SELECT c.*, c.pubdate as pubdate
				 FROM cms_clubs c
				 WHERE c.published = 1
				 ORDER BY c.rating DESC
				 LIMIT ".$cfg['count'];
 	
		$result = $inDB->query($sql);
						
		if ($inDB->num_rows($result)){	
			while ($club = $inDB->fetch_assoc($result)){
				if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
					if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/clubs/small/'.$club['imageurl'])){
						$club['imageurl'] = 'nopic.jpg';
					}
				}
				$club['members'] = clubTotalMembers($club['id']);
				$clubs[] = $club;
			}
		
			$smarty = $inCore->initSmarty('modules', 'mod_clubs.tpl');			
			$smarty->assign('clubs', $clubs);
			$smarty->display('mod_clubs.tpl');
						
		} else { echo '<p>'.$_LANG['BESTCLUBS_NOT_CLUBS'].'</p>'; }
		
		return true;
	
}
?>