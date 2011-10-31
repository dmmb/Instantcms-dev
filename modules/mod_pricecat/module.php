<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

function mod_pricecat($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT cat.*, COUNT(con.id) as content_count 
				FROM cms_price_cats cat, cms_price_items con
				WHERE con.category_id = cat.id AND cat.published = 1
				GROUP BY con.category_id
				ORDER BY cat.title DESC
				";		
		
		$result = $inDB->query($sql) ;
		
		$items = array();
		
		$is_item = false;
		
		if ($inDB->num_rows($result)){	
			$is_item = true;
			
			while($item = $inDB->fetch_assoc($result)){
			
				$item['link'] = '/price/'.$item['id'];
				
				if (strstr($_SERVER['REQUEST_URI'], $link)){ $item['is_current'] = true; } else { $item['is_current'] = false; }			
				
				$item['is_icon'] = ($cfg['icon'] && file_exists($_SERVER['DOCUMENT_ROOT'].$cfg['icon']));
				
				$items[]=$item;

				}
			
			}
		
		$smarty = $inCore->initSmarty('modules', 'mod_pricecat.tpl');			
		$smarty->assign('items', $items);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_item', $is_item);
		
		$smarty->display('mod_pricecat.tpl');
				
		return true;
}
?>