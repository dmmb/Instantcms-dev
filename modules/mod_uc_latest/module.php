<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_uc_latest($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
	
		$cfg = $inCore->loadModuleConfig($module_id);
		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}
		
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
		
		$showtype = $cfg['showtype'];

		$sql = "SELECT i.*, i.pubdate as fdate, c.view_type as viewtype
				FROM cms_uc_items i, cms_uc_cats c
				WHERE i.published = 1 AND i.category_id = c.id ".$catsql."
				ORDER BY i.pubdate DESC
				LIMIT ".$cfg['newscount'];		

		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){	
			if ($showtype=='thumb'){
					while($item = $inDB->fetch_assoc($result)){
						echo '<div class="uc_latest_item">';
							echo '<table border="0" cellspacing="2" cellpadding="0" width="100%">';
								echo '<tr><td height="110" align="center" valign="middle">';
									echo '<a href="/catalog/'.$menuid.'/item'.$item['id'].'.html">';
									if (strlen($item['imageurl'])>4) {
										echo '<img alt="'.$item['title'].'" src="/images/catalog/small/'.$item['imageurl'].'.jpg" border="0" />';
									} else {
										echo '<img alt="'.$item['title'].'" src="/images/catalog/small/nopic.jpg" border="0" />';								
									}
									echo '</a>';
								echo '</td></tr>';
								echo '<tr><td align="center" valign="middle">';
									echo '<a class="uc_latest_link" href="/catalog/'.$menuid.'/item'.$item['id'].'.html">'.$item['title'].'</a>';								
								echo '</td></tr>';	
								if ($item['viewtype']=='shop'){
									echo '<tr><td align="center" valign="middle">';
										$price = number_format($item['price'], 2, '.', ' ');
										echo '<div id="uc_latest_price">'.$price.' руб.</div>';								
									echo '</td></tr>';	
								}													
							echo '</table>';
						echo '</div>';				
					}
			}
			
			if ($showtype='list'){
				echo '<table width="100%" cellspacing="0" cellpadding="4" class="uc_latest_list">';
					while($item = $inDB->fetch_assoc($result)){
						$fdata = unserialize($item['fieldsdata']);
						echo '<tr>';
							echo '<td width="" valign="top"><a class="uc_latest_link" href="/catalog/'.$menuid.'/item'.$item['id'].'.html">'.substr($item['title'], 0, 40).'...</a></td>';
							for($f = 0; $f<$cfg['showf']; $f++){
								echo '<td valign="top">'.$inCore->getUCSearchLink($item['category_id'], $menuid, $f, $fdata[$f]).'</td>';
							}							
							echo '<td width="" align="right" valign="top">'.$inCore->dateFormat($item['fdate']).'</td>';
								echo '<td align="right">';
							if ($item['viewtype']=='shop'){
									$price = number_format($item['price'], 2, '.', ' ');
									echo '<div id="uc_latest_price">'.$price.' руб.</div>';	
							}	
								echo '</td>';
						echo '</tr>';
					}				
				echo '</table>';
				if ($cfg['fulllink']){
					echo '<div style="margin-top:5px; text-align:right; clear:both"><a href="/catalog/'.$menuid.'">Весь каталог</a> &rarr;</div>';
				}
			}
		} else { echo '<p>Нет объектов для отображения.</p>'; }
		
		return true;
}
?>