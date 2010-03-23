<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latest($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		$cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1; }
		if (!isset($cfg['subs'])) { $cfg['subs'] = 1; }

		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//select from category
				$catsql = ' AND con.category_id = '.$cfg['cat_id']; $rssid = $cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat = $inDB->get_fields('cms_category', 'id='.$cfg['cat_id'], 'NSLeft, NSRight');
				$catsql = "AND (con.category_id = cat.id AND cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";
			}		
			$rssid = $cfg['cat_id'];
		} else { $catsql = 'AND con.category_id = cat.id'; $rssid = 'all'; } 

		$sql = "SELECT DISTINCT con.*, DATE_FORMAT(con.pubdate, '%d-%m-%Y (%H:%i)') as fdate, u.nickname as author, u.login as author_login
				FROM cms_content con, cms_category cat, cms_users u
				WHERE con.published = 1 AND con.showlatest = 1 AND con.user_id = u.id 
                      AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= NOW() AND con.pubdate <= NOW()))
                      ".$catsql."
				ORDER BY con.pubdate DESC
				LIMIT ".$cfg['newscount'];
 	
		$result = $inDB->query($sql) ;
			
		if ($inDB->num_rows($result)){

            $inCore->loadModel('content');
            $model = new cms_model_content();

			$articles = array();					
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($articles);				
				$articles[$next]['id']          = $con['id'];
				$articles[$next]['title']       = $con['title'];
				$articles[$next]['href']        = $model->getArticleURL(null, $con['seolink']);
				$articles[$next]['author']      = $con['author'];
				$articles[$next]['authorhref']  = cmsUser::getProfileURL($con['author_login']);
				$articles[$next]['comments']    = $inCore->getCommentsCount('article', $con['id']);
				$articles[$next]['date']        = $con['fdate'];
				$articles[$next]['description'] = $con['description'];
                $articles[$next]['image']       = (file_exists(PATH.'/images/photos/small/article'.$con['id'].'.jpg') ? 'article'.$con['id'].'.jpg' : '');                
			}

			$smarty = $inCore->initSmarty('modules', 'mod_latest.tpl');			
			$smarty->assign('articles', $articles);
			$smarty->assign('rssid', $rssid);
			$smarty->assign('cfg', $cfg);
			$smarty->display('mod_latest.tpl');			
			
		} else { 
			echo '<p>'.$_LANG['LATEST_NOT_MATERIAL'].'</p>';
		}

		return true;
}
?>