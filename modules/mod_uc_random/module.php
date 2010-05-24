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
		
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){
				echo '<div align="center" id="uc_random_img"><a href="/catalog/item'.$item['id'].'.html"><img src="/images/catalog/small/'.$item['imageurl'].'.jpg" border="0"/></a></div>';
				if($cfg['showtitle']){
					echo '<div style="margin-top:10px" id="uc_random_title" align="center"><a href="/catalog/item'.$item['id'].'.html"><strong>'.$item['title'].'</strong></a></div>';
					if ($item['viewtype'] == 'shop'){
						$price = number_format($item['price'], 2, '.', ' ');
						echo '<div style="margin-bottom:10px" align="center" id="uc_random_price">'.$price.' руб.</div>';
					}
				}
				if($cfg['showcat']){
					echo '<div align="center" id="uc_random_cat">Рубрика: <a href="/catalog/'.$item['category_id'].'">'.$item['category'].'</a></div>';
				}
			}
		}

		return true;	
	}
?>