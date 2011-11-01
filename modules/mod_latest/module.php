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

function mod_latest($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);

        global $_LANG;
		
		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1; }
		if (!isset($cfg['subs'])) { $cfg['subs'] = 1; }
		if (!isset($cfg['cat_id'])) { $cfg['cat_id'] = 1; }
		
		// опции постраничной разбивки
		$page    = 1;
		$perpage = $cfg['newscount'];
		
		$today = date("Y-m-d H:i:s");
		
		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//select from category
				$catsql = ' AND con.category_id = '.$cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat = $inDB->get_fields('cms_category', "id='{$cfg['cat_id']}'", 'NSLeft, NSRight');
				if(!$rootcat) { return false; }
				$catsql = "AND (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";
			}		
			$rssid = $cfg['cat_id'];

		} else { $catsql = ''; $rssid = 'all'; } 

		$sql = "SELECT con.*,
                       con.pubdate as fdate,
                       u.nickname as author,
                       u.login as author_login
                       FROM cms_content con
				       LEFT JOIN cms_category cat ON cat.id = con.category_id
				       LEFT JOIN cms_users u ON u.id = con.user_id
                       WHERE con.published = 1 AND con.showlatest = 1 AND con.is_arhive = 0 AND con.pubdate <= '$today'
                      AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today'))
                      ".$catsql."
				ORDER BY con.pubdate DESC
				LIMIT ".$cfg['newscount'];
 	
		$result = $inDB->query($sql);
			
		$is_con = false;
	
		if ($cfg['is_pag']) {
			// Считаем общее количество материалов если опция пагинация включена
			$sql_total = "SELECT 1
					FROM cms_content con
					LEFT JOIN cms_category cat ON cat.id = con.category_id
					WHERE con.published = 1 AND con.showlatest = 1 AND con.is_arhive = 0 AND con.pubdate <= '$today' AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today')) ".$catsql."";
			$result_total = $inDB->query($sql_total) ;
			$total_page = $inDB->num_rows($result_total);
		}
	
		if ($inDB->num_rows($result)){

			$is_con = true;

            $inCore->loadModel('content');
            $model = new cms_model_content();

			$articles = array();					
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($articles);				
				$articles[$next]['id']          = $con['id'];
				$articles[$next]['title']       = $con['title'];
				$articles[$next]['hits']        = $con['hits'];
				$articles[$next]['href']        = $model->getArticleURL(null, $con['seolink']);
				$articles[$next]['author']      = $con['author'];
				$articles[$next]['authorhref']  = cmsUser::getProfileURL($con['author_login']);
				$articles[$next]['comments']    = $cfg['showcom'] ? $inCore->getCommentsCount('article', $con['id']) : false;
				$articles[$next]['date']        = $inCore->dateformat($con['fdate']);
				$articles[$next]['description'] = $con['description'];
                $articles[$next]['image']       = (file_exists(PATH.'/images/photos/small/article'.$con['id'].'.jpg') ? 'article'.$con['id'].'.jpg' : '');                
			}

		}
		
			$smarty = $inCore->initSmarty('modules', 'mod_latest.tpl');			
			$smarty->assign('articles', $articles);
			$smarty->assign('rssid', $rssid);
		if ($cfg['is_pag']) {
			$smarty->assign('pagebar_module', cmsPage::getPagebar($total_page, $page, $perpage, 'javascript:conPage(%page%, '.$module_id.')'));
		}
		$smarty->assign('is_ajax', false);
		$smarty->assign('is_con', $is_con);
		$smarty->assign('module_id', $module_id);
			$smarty->assign('cfg', $cfg);
			$smarty->display('mod_latest.tpl');			
			
		return true;
}
?>