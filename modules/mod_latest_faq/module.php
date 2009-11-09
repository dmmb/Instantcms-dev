<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latest_faq($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);
		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}
	
		if (!isset($cfg['newscount'])) { $cfg['newscount'] = 2;}
		if (!isset($cfg['cat_id'])) { $cfg['cat_id'] = -1;}
		if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0;}
		if (!isset($cfg['maxlen'])) { $cfg['maxlen'] = 120;}

		if ($cfg['cat_id'] != '-1') {					
			$catsql = 'AND category_id = '.$cfg['cat_id'];	
		} else { $catsql = ''; } 

		$sql = "SELECT *, DATE_FORMAT(pubdate, '%d/%m/%Y') as pubdate
				FROM cms_faq_quests
				WHERE published = 1 ".$catsql."
				ORDER BY pubdate DESC
				LIMIT ".$cfg['newscount'];
 	
		$result = $inDB->query($sql) ;
				
		if ($inDB->num_rows($result)){	
			$faq = array();
			
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($faq);
				if(strlen($con['quest'])>$cfg['maxlen']){
					$con['quest'] = substr($con['quest'], 0, $cfg['maxlen']) . '...';
				}
				$faq[$next]['quest'] = $con['quest'];
				$faq[$next]['date'] = $con['pubdate'];
				$faq[$next]['href'] = '/faq/'.$menuid.'/quest'.$con['id'].'.html';
			}
			
			$smarty = $inCore->initSmarty('modules', 'mod_latest_faq.tpl');			
			$smarty->assign('faq', $faq);
			$smarty->display('mod_latest_faq.tpl');
						
		} else { echo '<p>'.$_LANG['LATEST_FAQ_NOT_QUES'].'</p>'; }
		
				
		return true;
	
}
?>