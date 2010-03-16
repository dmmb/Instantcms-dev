<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latestclubs($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
        
		$inCore->loadLib('clubs');
	
		$cfg = $inCore->loadModuleConfig($module_id);
		if ($cfg['menuid']>0) {	$menuid = $cfg['menuid']; } else { $menuid = 0; }
	
		if (!isset($cfg['count'])) { $cfg['count'] = 5;}
		if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0;}

		$sql =  "SELECT c.*, 					
						IF(DATE_FORMAT(c.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(c.pubdate, '{$_LANG['TODAY']}'),
						IF(DATEDIFF(NOW(), c.pubdate)=1, DATE_FORMAT(c.pubdate, '{$_LANG['YESTERDAY']}'),DATE_FORMAT(c.pubdate, '%d/%m/%Y') ))  as pubdate
				 FROM cms_clubs c
				 WHERE c.published = 1
				 ORDER BY c.pubdate DESC
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
			$smarty->assign('menuid', $menuid);			
			$smarty->display('mod_clubs.tpl');
						
		} else { echo '<p>'.$_LANG['LATESTCLUBS_NOT_CLUBS'].'</p>'; }
		
		return true;
	
}
?>