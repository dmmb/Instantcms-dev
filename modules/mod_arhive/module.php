<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_arhive($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		global $_LANG;
		$cfg = $inCore->loadModuleConfig($module_id);
		$sql = "SELECT DATE_FORMAT( pubdate, '%M, %Y' ) fdate, DATE_FORMAT( pubdate, '%Y' ) year, DATE_FORMAT( pubdate, '%m' ) month, COUNT( id ) num
				FROM cms_content"."\n";
		if($cfg['cat_id']>0){
			$sql .= "WHERE category_id = ".$cfg['cat_id'];
			if ($cfg['source']!='both'){
				if ($cfg['source']=='arhive'){
					$sql .= " AND is_arhive = 1". "\n";
				} else {
					$sql .= " AND is_arhive = 0". "\n";
				}				
			} else {
				$sql .= "\n";
			}
		}
		$sql .= "GROUP BY DATE_FORMAT(pubdate, '%M, %Y')"."\n";
		$sql .= "ORDER BY pubdate DESC";
		$result = $inDB->query($sql) ;
		if ($inDB->num_rows($result)>0){		
			while ($item = $inDB->fetch_assoc($result)){
				$item['fdate'] = $inCore->getRusDate($item['fdate']);
			}
			$smarty = $inCore->initSmarty('modules', 'mod_arhive.tpl');			
			$smarty->assign('items', $item);
			$smarty->display('mod_arhive.tpl');
		} else { echo '<p>'.$_LANG['ARHIVE_NOT_MATERIAL'].'</p>'; }
		return true;
}
?>