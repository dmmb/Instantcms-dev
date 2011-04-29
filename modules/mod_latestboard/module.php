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

function mod_latestboard($module_id){	

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

        global $_LANG;

		if (!isset($cfg['shownum'])){
			echo '<p>'.$_LANG['LATESTBOARD_CONFIG_TEXT'].'</p>';
			return true;
		}
		
		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//select from category
				$catsql  = ' AND i.category_id = '.$cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat = dbGetFields('cms_board_cats', 'id='.$cfg['cat_id'], 'NSLeft, NSRight');
				$catsql  = "AND (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";
			}		
		} else { $catsql = ''; }
		
		$sql = "SELECT i.title, i.id, i.city as city, u.id as user_id, u.nickname as nickname,
					   i.pubdate as pubdate, i.is_vip as is_vip
				FROM cms_board_items i
				LEFT JOIN cms_board_cats cat ON cat.id = i.category_id
				LEFT JOIN cms_users u ON u.id = i.user_id
				WHERE i.published=1 $catsql
				ORDER BY i.id DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql);
		
		$items = array();
		$is_items = false;

		if ($inDB->num_rows($result)){	
		
			$is_items = true;

			while($con = $inDB->fetch_assoc($result)){
				$con['pubdate'] = $inCore->dateFormat($con['pubdate']);
				$items[] = $con;
			}
	
		}

        $smarty = $inCore->initSmarty('modules', 'mod_latestboard.tpl');
        $smarty->assign('items', $items);
        $smarty->assign('cfg', $cfg);
		$smarty->assign('is_items', $is_items);
        $smarty->display('mod_latestboard.tpl');
			
		return true;				
}
?>