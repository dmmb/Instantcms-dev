<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
if (!function_exists('buildRating')){
	function buildRating($rating){
		$rating = round($rating, 2);
		$html = '';	
		for($r = 0; $r < 5; $r++){
			if (round($rating) > $r){
				$html .= '<img src="/images/ratings/starfull.gif" border="0" />';
			} else {
				$html .= '<img src="/images/ratings/starhalf.gif" border="0" />';		
			}
		}	
		return $html;
	}
}

function mod_uc_popular($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		$cfg = $inCore->loadModuleConfig($module_id);
			
		global $_LANG;
		
		$showtype = $cfg['showtype'];
		
		if($cfg['sort']=='rating') { $orderby = 'rating DESC'; }
		else { $orderby = 'hits DESC'; }
		
		if ($cfg['cat_id']>0){

            if (!$cfg['subs']){
				//select from category
				$catsql = ' AND i.category_id = '.$cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat  = $inDB->get_fields('cms_uc_cats', 'id='.$cfg['cat_id'], 'NSLeft, NSRight');
				$catsql   = "AND (c.NSLeft >= {$rootcat['NSLeft']} AND c.NSRight <= {$rootcat['NSRight']})";
			}

		} else {
			$catsql = '';
		}

		$sql = "SELECT i.* , IFNULL(AVG( r.points ), 0) AS rating, c.view_type as viewtype
				FROM cms_uc_items i
				LEFT JOIN cms_uc_cats c ON c.id = i.category_id
                LEFT JOIN cms_uc_ratings r ON r.item_id = i.id
				WHERE i.published = 1 $catsql
				GROUP BY i.id
				ORDER BY $orderby 
				LIMIT ".$cfg['num'];

		$result = $inDB->query($sql);
		
		$items = array();
		$is_uc = false;
		
		if ($inDB->num_rows($result)){	
			$is_uc = true;
			if ($cfg['showtype']=='thumb'){
					while($item = $inDB->fetch_assoc($result)){
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
			
			if ($cfg['showtype']=='list'){
					while($item = $inDB->fetch_assoc($result)){
							$item['fieldsdata'] = unserialize($item['fieldsdata']);
							$item['title'] = substr($item['title'], 0, 40);
							
							for($f = 0; $f<$cfg['showf']; $f++){
								$item['fdata'][] = $inCore->getUCSearchLink($item['category_id'], null, $f, $item['fieldsdata'][$f]);
							}							
									
						if($cfg['sort']=='rating') { 
								$item['key'] = '<a href="/catalog/item'.$item['id'].'.html" title="'.$_LANG['UC_POPULAR_RATING'].': '.round($item['rating'], 2).'">'.buildRating(round($item['rating'], 2)).'</a>'; 
						}
						else { 
								$item['key'] = $_LANG['UC_POPULAR_VIEWS'].': <a href="/catalog/item'.$item['id'].'.html" title="'.$_LANG['UC_POPULAR_VIEWS'].'">'.$item['hits'].'</a>'; 
						}
			
							$item['fdate'] = $inCore->dateFormat($item['fdate']);
							if ($item['viewtype']=='shop'){
								$item['price'] = number_format($item['price'], 2, '.', ' ');
							}			
							$items[] = 	$item;	
					}				
				}
			}
		
		$smarty = $inCore->initSmarty('modules', 'mod_uc_popular.tpl');			
		$smarty->assign('items', $items);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_uc', $is_uc);			
		$smarty->display('mod_uc_popular.tpl');
		
		return true;
		
}
?>