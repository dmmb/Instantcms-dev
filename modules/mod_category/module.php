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

function mod_category($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
	
		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT cat.*, COUNT(con.id) as content_count 
				FROM cms_category cat, cms_content con
				WHERE (cat.parent_id = ".$cfg['category_id']." AND cat.id > 0) AND con.category_id = cat.id AND cat.published = 1
				GROUP BY con.category_id
				ORDER BY cat.title DESC
				";		
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){

            $inCore->loadModel('content');
            $model = new cms_model_content();

			echo '<table cellspacing="2" border="0">';
			while($con = $inDB->fetch_assoc($result)){
                $link = $model->getCategoryURL(null, $con['seolink']);
				if (strstr($_SERVER['REQUEST_URI'], $link)){ $is_current = true; } else { $is_current = false; }			
				$is_icon = ($cfg['icon'] && file_exists(PATH.$cfg['icon']));
				echo '<tr>';
					if ($is_icon){
						echo '<td width="12" valign="top"><img src="'.$cfg['icon'].'" border="0" /></td>';
					}
					echo '<td width="" valign="top">';
						if (!$is_current) { echo '<a href="'.$link.'" class="mod_cat_link">'; } else { echo '<div class="mod_cat_current">'; }
						echo $con['title'];
						if (!$is_current) { echo '</a>'; } else { echo '</div>'; }
					echo '</td>';				
				echo '</tr>';
				if ($cfg['showdesc']){
					echo '<tr>';
					if($is_icon){ echo '<td>&nbsp;</td>'; }
					echo '<td><div class="mod_cat_desc">'.$con['description'].'</div></td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		} else { echo '<p>'.$_LANG['CATEGORY_NOT_CAT'].'</p>'; }
				
		return true;
}
?>