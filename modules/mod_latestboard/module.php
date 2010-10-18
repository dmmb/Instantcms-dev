<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latestboard($module_id){	
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		$cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
		if (!isset($cfg['shownum'])){
			echo '<p>'.$_LANG['LATESTBOARD_CONFIG_TEXT'].'</p>';
			return true;
		}
		
		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//select from category
				$catsql = ' AND i.category_id = '.$cfg['cat_id']; $rssid = $cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat = dbGetFields('cms_board_cats', 'id='.$cfg['cat_id'], 'NSLeft, NSRight');
				$catsql = "AND (i.category_id = cat.id AND cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";
			}		
			$rssid = $cfg['cat_id'];
		} else { $catsql = 'AND i.category_id = cat.id'; $rssid = 'all'; } 
		
		$sql = "SELECT i.title, i.id, i.city as city, u.id as user_id, u.nickname as nickname,
					   i.pubdate as pubdate
				FROM cms_board_items i, cms_users u, cms_board_cats cat
				WHERE i.user_id = u.id AND i.published=1 $catsql
				ORDER BY i.pubdate DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql) or die(mysql_error().'<pre>'.$sql);
		
		$items = array();
		if ($inDB->num_rows($result)){	
		
			while($con = $inDB->fetch_assoc($result)){
				$con['pubdate'] = $inCore->dateFormat($con['pubdate']);
				$items[] = $con;
			}
	
			$smarty = $inCore->initSmarty('modules', 'mod_latestboard.tpl');			
			$smarty->assign('items', $items);
			$smarty->assign('cfg', $cfg);
			$smarty->display('mod_latestboard.tpl');
			
		} else { echo '<p>'.$_LANG['LATESTBOARD_NOT_ADV'].'</p>'; }
		
		return true;				
}
?>