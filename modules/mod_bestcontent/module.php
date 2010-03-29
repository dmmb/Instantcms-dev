<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

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

		$sql = "SELECT c.*, IF(DATE_FORMAT(c.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'),	DATE_FORMAT(c.pubdate, '<strong>{$_LANG['TODAY']}</strong> â %H:%i'),
						DATE_FORMAT(c.pubdate, '%d-%m-%Y'))  as fpubdate, 
                        IFNULL(r.total_rating, 0) as points,
						u.nickname as author, u.login as author_login
				FROM cms_users u, cms_content c
				LEFT JOIN cms_ratings_total r ON r.item_id=c.id AND r.target='content'
				WHERE c.published = 1 AND c.user_id = u.id AND c.canrate = 1
                      AND (c.is_end=0 OR (c.is_end=1 AND c.enddate >= NOW() AND c.pubdate <= NOW()))
				GROUP BY r.item_id
				ORDER BY points DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql) or die('<pre>'.$sql.'</pre>'.mysql_error());
		
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
				$articles[$next]['date'] = $con['fpubdate'];
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