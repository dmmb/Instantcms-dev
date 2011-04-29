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

function mod_latest_faq($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		$cfg = $inCore->loadModuleConfig($module_id);

		global $_LANG;

		if (!isset($cfg['newscount'])) { $cfg['newscount'] = 2;}
		if (!isset($cfg['cat_id']) || !$cfg['cat_id']) { $cfg['cat_id'] = -1;}
		if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0;}
		if (!isset($cfg['maxlen'])) { $cfg['maxlen'] = 120;}

		if ($cfg['cat_id'] != '-1') {					
			$catsql = 'AND category_id = '.$cfg['cat_id'];	
		} else { $catsql = ''; } 

		$sql = "SELECT *
				FROM cms_faq_quests
				WHERE published = 1 ".$catsql."
				ORDER BY cms_faq_quests.pubdate DESC
				LIMIT ".$cfg['newscount'];
 	
		$result = $inDB->query($sql) ;
				
		$is_faq = false;
				
		if ($inDB->num_rows($result)){	
		
			$faq = array();
			$is_faq = true;
			
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($faq);
				if(strlen($con['quest'])>$cfg['maxlen']){
					$con['quest'] = substr($con['quest'], 0, $cfg['maxlen']) . '...';
				}
				$faq[$next]['quest'] = $con['quest'];
				$faq[$next]['date']  = $inCore->dateformat($con['pubdate']);
				$faq[$next]['href'] = '/faq/quest'.$con['id'].'.html';
			}
			
		}
		
			$smarty = $inCore->initSmarty('modules', 'mod_latest_faq.tpl');			
			$smarty->assign('faq', $faq);
		$smarty->assign('is_faq', $is_faq);
			$smarty->display('mod_latest_faq.tpl');
						
		return true;
	
}
?>