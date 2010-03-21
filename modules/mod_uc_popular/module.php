<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
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
				$catsql   = "AND (i.category_id = c.id AND c.NSLeft >= {$rootcat['NSLeft']} AND c.NSRight <= {$rootcat['NSRight']})";
			}

		} else {
			$catsql = '';
		}

		$sql = "SELECT i.* , IFNULL(AVG( r.points ), 0) AS rating, c.view_type as viewtype
				FROM cms_uc_cats c, cms_uc_items i
                LEFT JOIN cms_uc_ratings r ON r.item_id = i.id
				WHERE i.category_id = c.id AND i.published = 1 $catsql
				GROUP BY i.id
				ORDER BY $orderby 
				LIMIT ".$cfg['num'];

		$result = $inDB->query($sql) or die(mysql_error()."<br><br>".$sql);
		
		if ($inDB->num_rows($result)){	
			if ($showtype=='thumb'){
					while($item = $inDB->fetch_assoc($result)){
						echo '<div class="uc_latest_item">';
							echo '<table border="0" cellspacing="2" cellpadding="0" width="100%">';
								echo '<tr><td height="110" align="center" valign="middle">';
									echo '<a href="/catalog/item'.$item['id'].'.html">';
									if (strlen($item['imageurl'])>4) {
										echo '<img alt="'.$item['title'].'" src="/images/catalog/small/'.$item['imageurl'].'.jpg" border="0" />';
									} else {
										echo '<img alt="'.$item['title'].'" src="/images/catalog/small/nopic.jpg" border="0" />';								
									}
									echo '</a>';
								echo '</td></tr>';
								echo '<tr><td align="center" valign="middle">';
									echo '<a class="uc_latest_link" href="/catalog/item'.$item['id'].'.html">'.$item['title'].'</a>';								
								echo '</td></tr>';			
								if ($item['viewtype']=='shop'){
									echo '<tr><td align="center" valign="middle">';
										$price = number_format($item['price'], 2, '.', ' ');
										echo '<div id="uc_popular_price">'.$price.' руб.</div>';								
									echo '</td></tr>';	
								}			
							echo '</table>';
						echo '</div>';				
					}
			}
			
			if ($showtype='list'){
				echo '<table width="100%" cellspacing="0" cellpadding="4" class="uc_latest_list">';
					while($item = $inDB->fetch_assoc($result)){
						if($cfg['sort']=='rating') { 
							$key = '<a href="/catalog/item'.$item['id'].'.html" title="Рейтинг: '.round($item['rating'], 2).'">'.buildRating(round($item['rating'], 2)).'</a>'; 
						}
						else { 
							$key = 'Просмотры: <a href="/catalog/item'.$item['id'].'.html" title="Просмотры">'.$item['hits'].'</a>'; 
						}
			
						$fdata = unserialize($item['fieldsdata']);
						echo '<tr>';
							echo '<td width="" valign="top"><a class="uc_latest_link" href="/catalog/item'.$item['id'].'.html">'.$item['title'].'</a></td>';
							for($f = 0; $f<$cfg['showf']; $f++){
								echo '<td valign="top">'.$inCore->getUCSearchLink($item['category_id'], null, $f, $fdata[$f]).'</td>';
							}							
							echo '<td width="" align="right" valign="top">'.$key.'</td>';
							if ($item['viewtype']=='shop'){
								echo '<td align="right">';
									$price = number_format($item['price'], 2, '.', ' ');
									echo '<div id="uc_popular_price">'.$price.' руб.</div>';								
								echo '</td>';	
							}			
						echo '</tr>';
					}				
				echo '</table>';
				if ($cfg['fulllink']){
					echo '<div style="margin-top:5px; text-align:right;clear:both"><a href="/catalog">Весь каталог</a> &rarr;</div>';
				}
			}
		} else { echo '<p>Нет объектов для отображения.</p>'; }
		
		return true;
}
?>