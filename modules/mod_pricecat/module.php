<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

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
		
		if ($inDB->num_rows($result)){	
			echo '<table cellspacing="2" border="0">';
			while($con = $inDB->fetch_assoc($result)){
				$link = '/price/'.$con['id'];
				if (strstr($_SERVER['REQUEST_URI'], $link)){ $is_current = true; } else { $is_current = false; }			
				$is_icon = ($cfg['icon'] && file_exists($_SERVER['DOCUMENT_ROOT'].$cfg['icon']));
				echo '<tr>';
					if ($is_icon){
						echo '<td width="12" valign="top"><img src="'.$cfg['icon'].'" border="0" /></td>';
					}
					echo '<td width="" valign="top">';
						if (!$is_current) { echo '<a href="'.$link.'" class="mod_pcat_link">'; } else { echo '<div class="mod_pcat_current">'; }
						echo $con['title'];
						if (!$is_current) { echo '</a>'; } else { echo '</div>'; }
					echo '</td>';				
				echo '</tr>';
				if ($cfg['showdesc']){
					echo '<tr>';
					if($is_icon){ echo '<td>&nbsp;</td>'; }
					echo '<td><div class="mod_pcat_desc">'.$con['description'].'</div></td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		} else { echo '<p>Нет категорий для отображения.</p>'; }
				
		return true;
}
?>