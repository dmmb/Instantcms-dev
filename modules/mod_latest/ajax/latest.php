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

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }
	header('Content-Type: text/html; charset=windows-1251'); 
	session_start();

	if (!isset($_REQUEST['module_id'])) { die(2); }
	if (!isset($_REQUEST['page'])) { die(4); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	// Грузим ядро и классы
	include(PATH.'/core/cms.php');
	// Грузим конфиг
	include(PATH.'/includes/config.inc.php');
    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());
    
    $inCore->loadClass('config'); 
    $inCore->loadClass('db'); 
    $inCore->loadClass('user');
    $inCore->loadClass('page');
    $inDB   = cmsDatabase::getInstance();

	// Подгружаем языковой файл
	$inCore->loadLanguage('lang');
	$inCore->loadLanguage('modules/mod_latest');

	// Грузим шаблонизатор
    $smarty = $inCore->initSmarty();

	// Входные данные
	$page	    = $inCore->request('page', 'int', 1);	
	$module_id	= $inCore->request('module_id', 'int', '');

	// Грузим конфиг модуля
	$cfg = $inCore->loadModuleConfig($module_id);
	// Если пагинация отключена, выходим
	if (!$cfg['is_pag']) { die(); }

	$perpage    = $cfg['newscount'];

	if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1; }
	if (!isset($cfg['subs'])) { $cfg['subs'] = 1; }

	$today = date("Y-m-d H:i:s");
	
	if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				//select from category
				$catsql = ' AND con.category_id = '.$cfg['cat_id'];
			} else {
				//select from category and subcategories
				$rootcat = $inDB->get_fields('cms_category', "id='{$cfg['cat_id']}'", 'NSLeft, NSRight');
				if(!$rootcat) { exit; }
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
	                   LIMIT ".(($page-1)*$perpage).", $perpage";

	$result = $inDB->query($sql);

	$is_con = false;

	// Считаем общее количество материалов
	$sql_total = "SELECT 1
			FROM cms_content con
			LEFT JOIN cms_category cat ON cat.id = con.category_id
			WHERE con.published = 1 AND con.showlatest = 1 AND con.is_arhive = 0 AND con.pubdate <= '$today' AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today')) ".$catsql."";
	$result_total = $inDB->query($sql_total) ;
	$total_page = $inDB->num_rows($result_total);

	// если есть записи выводим их	
	if ($total_page){

		$is_con = true;

		// грузим модель компонента контент
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

	// Отдаем в шаблон
	ob_start();
	$smarty = $inCore->initSmarty('modules', 'mod_latest.tpl');			
	$smarty->assign('articles', $articles);
	$smarty->assign('rssid', $rssid);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('is_ajax', true);
	$smarty->assign('is_con', $is_con);
	$smarty->assign('module_id', $module_id);
	$smarty->assign('pagebar_module', cmsPage::getPagebar($total_page, $page, $perpage, 'javascript:conPage(%page%, '.$module_id.')'));
	$smarty->display('mod_latest.tpl');			
	$html = ob_get_clean();
	echo $html;
?>
