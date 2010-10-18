<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_uc_random($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['cat_id']>0){

            if (!$cfg['subs']){
				//select from category
				$catsql = ' AND i.category_id = '.$cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat  = $inDB->get_fields('cms_uc_cats', 'id='.$cfg['cat_id'], 'NSLeft, NSRight');
				$catsql   = "AND (i.category_id = c.id AND c.NSLeft >= {$rootcat['NSLeft']} AND c.NSRight <= {$rootcat['NSRight']})";
			}

		} else {
			$catsql = '';
		}

		$sql = "SELECT i.*, c.title as category, c.view_type as viewtype
				FROM cms_uc_items i, cms_uc_cats c
				WHERE i.category_id = c.id AND i.imageurl != '' AND i.published = 1 ".$catsql."
				ORDER BY RAND()
				LIMIT ".$cfg['count'];
		
		$result = $inDB->query($sql) ;
		
		$items = array();
		$is_uc = false;
		
		if ($inDB->num_rows($result)){
			$is_uc = true;
			while ($item=$inDB->fetch_assoc($result)){
				if (strlen($item['imageurl'])<4) {
					$item['imageurl'] = 'nopic';
				} elseif (!file_exists(PATH.'/images/catalog/small/'.$item['imageurl'].'.jpg')) {
					$item['imageurl'] = 'nopic';
					}
						
				if ($item['viewtype']=='shop'){
					$item['price'] = number_format($item['price'], 2, '.', ' ');
				}
						
				$items[] = 	$item;												
			}
		}

		$smarty = $inCore->initSmarty('modules', 'mod_uc_random.tpl');			
		$smarty->assign('items', $items);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_uc', $is_uc);
		$smarty->display('mod_uc_random.tpl');
		

		return true;	
	}
?>