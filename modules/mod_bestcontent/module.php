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

function mod_bestcontent($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		if (!function_exists('cmsKarmaFormat')){ //if not included earlier
			include($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
		}		
		
		$cfg = $inCore->loadModuleConfig($module_id);
		
		if (!isset($cfg['shownum'])){
			echo '<p>'.$_LANG['BESTCONTENT_CONFIG_TEXT'].'</p>';
			return;
		}
		if (!isset($cfg['subs'])) { $cfg['subs'] = 1; }
		if (!isset($cfg['cat_id'])) { $cfg['cat_id'] = 1; }
		$today = date("Y-m-d H:i:s");
		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//выбираем из категории
				$catsql = ' AND c.category_id = '.$cfg['cat_id'];
			} else {
				//выбираем из категории и подкатегорий
				$rootcat = $inDB->get_fields('cms_category', "id='{$cfg['cat_id']}'", 'NSLeft, NSRight');
				if(!$rootcat) { return false; }
				$catsql = "AND (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";
			}		
		} else { $catsql = ''; } 


		$sql = "SELECT c.*, c.pubdate as fpubdate, 
                        IFNULL(r.total_rating, 0) as points,
						u.nickname as author, u.login as author_login
				FROM cms_content c
				LEFT JOIN cms_category cat ON cat.id = c.category_id
				LEFT JOIN cms_users u ON u.id = c.user_id
				LEFT JOIN cms_ratings_total r ON r.item_id=c.id AND r.target='content'
				WHERE c.published = 1 AND c.canrate = 1
                AND (c.is_end=0 OR (c.is_end=1 AND c.enddate >= '$today' AND c.pubdate <= '$today')) 
				".$catsql."
				ORDER BY points DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){

            $inCore->loadModel('content');
            $model = new cms_model_content();

			$articles = array();

			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($articles);
				$text = strip_tags($con['title']);							
				if (strlen($text)>70) { $text = substr($text, 0, 70). '...'; }
							
				$articles[$next]['title'] = $text;
				$articles[$next]['href'] = $model->getArticleURL($cfg['menuid'], $con['seolink']);
				$articles[$next]['karma'] = cmsKarmaFormat($con['points']);
				$articles[$next]['date'] = cmsCore::dateFormat($con['fpubdate']);
				$articles[$next]['author'] = $con['author'];
				$articles[$next]['authorhref'] = cmsUser::getProfileURL($con['author_login']);
				$articles[$next]['description'] = $con['description'];							
			}			
			$smarty = $inCore->initSmarty('modules', 'mod_bestcontent.tpl');			
			$smarty->assign('articles', $articles);
			$smarty->assign('cfg', $cfg);			
			$smarty->display('mod_bestcontent.tpl');
							
		} else { echo '<p>'.$_LANG['BESTCONTENT_NOT_ARTICLES'].'</p>'; }
				
		return true;
}
?>