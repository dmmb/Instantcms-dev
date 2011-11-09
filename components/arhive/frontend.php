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
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function arhive(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();
    $inUser     = cmsUser::getInstance();

	$cfg        = $inCore->loadComponentConfig('arhive');

    // Проверяем включен ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    $id         = $inCore->request('id', 'int', 0);
    $do         = $inCore->request('do', 'str', 'view');

    $year       = $inCore->request('y', 'int', 'all');
    $month      = $inCore->request('m', 'int', 'all');
    $day        = $inCore->request('d', 'int', 'all');
    $today = date("Y-m-d H:i:s");
    global $_LANG;

	$inPage->setTitle($_LANG['ARCHIVE_MATERIALS']);
	$inPage->addPathway($_LANG['ARCHIVE_MATERIALS'], '/arhive');

	if ($cfg['source']!='both'){
		if ($cfg['source']=='arhive'){
			$cfg_sql = " AND con.is_arhive = 1";
		} else {
			$cfg_sql = " AND con.is_arhive = 0";
		}				
	} else {
		$cfg_sql = '';
	}

 //======================================================================================================//

	if ($year == 'all'){

		$sql = "SELECT DATE_FORMAT( con.pubdate, '%M, %Y' ) fdate,
                       DATE_FORMAT( con.pubdate, '%Y' ) year,
                       DATE_FORMAT( con.pubdate, '%m' ) month,
                       COUNT( con.id ) num
				FROM cms_content con
                WHERE con.published = 1 AND con.pubdate <= '$today' {$cfg_sql}
				GROUP BY DATE_FORMAT(con.pubdate, '%M, %Y') 
				ORDER BY con.pubdate DESC";

		$result         = $inDB->query($sql);
        $items_count    = $inDB->num_rows($result);
        $items = array();

		if ($items_count){
			while ($item = $inDB->fetch_assoc($result)){
                if ($item['fdate']){
                $item['fdate']  = $inCore->getRusDate($item['fdate']);
                $items[]        = $item;
                }
			}
		}

        $smarty = $inCore->initSmarty('components', 'com_arhive_dates.tpl');
        $smarty->assign('heading', $_LANG['ARCHIVE_MATERIALS']);
        $smarty->assign('items', $items);
        $smarty->assign('items_count', $items_count);
        $smarty->display('com_arhive_dates.tpl');

        return;
	}

//======================================================================================================//

    if($year != 'all' && $day=='all' && $month=='all'){

        $inPage->addPathway($year, '/arhive/'.$year);

        $sql = "SELECT DATE_FORMAT( con.pubdate, '%M' ) fdate, DATE_FORMAT( con.pubdate, '%Y' ) year, DATE_FORMAT( con.pubdate, '%m' ) month, COUNT( con.id ) num
                FROM cms_content con
                WHERE con.published = 1 AND con.pubdate <= '$today' AND DATE_FORMAT(con.pubdate, '%Y') LIKE '$year' {$cfg_sql}
                GROUP BY DATE_FORMAT(con.pubdate, '%M')
                ORDER BY con.pubdate DESC
                ";
        $result = $inDB->query($sql);

        $items_count    = $inDB->num_rows($result);
        $items = array();

		if ($items_count){
			while ($item = $inDB->fetch_assoc($result)){
                if ($item['fdate']){
                    $item['fdate']  = $inCore->getRusDate($item['fdate']);
                    $items[]        = $item;
                }
			}
		}

        $smarty = $inCore->initSmarty('components', 'com_arhive_dates.tpl');
        $smarty->assign('heading', $_LANG['MATERIALS_FROM'].$year.$_LANG['ARHIVE_YEAR']);
        $smarty->assign('items', $items);
        $smarty->assign('items_count', $items_count);
        $smarty->display('com_arhive_dates.tpl');

        return;

    }

//======================================================================================================//

    $month_name = $inCore->getRusDate(date('F', mktime(0,0,0,$month,1,$year)));

    if ($year != 'all' && $month != 'all' &&  $day == 'all') {
        $inPage->addPathway($year, '/arhive/'.$year);
        $inPage->addPathway($month_name, '/arhive/'.$year.'/'.$month);
        $heading    = $_LANG['MATERIALS_FROM'].$month_name.' '.$year.$_LANG['ARHIVE_YEARS'];
        $date_str   = $year.'-'.$month;
        $date_where = "DATE_FORMAT(con.pubdate, '%Y-%c') LIKE '$date_str'";
    }

    if ($year != 'all' && $month != 'all' &&  $day != 'all') {
        $inPage->addPathway($year, '/arhive/'.$year);
        $inPage->addPathway($month_name, '/arhive/'.$year.'/'.$month);
        $inPage->addPathway($day, '/arhive/'.$year.'/'.$month.'/'.$day);
        $heading    = $_LANG['MATERIALS_FROM'].$day.', '.$inCore->getRusDate(date('F', mktime(0,0,0,$month,1,$year))).' '.$year.$_LANG['ARHIVE_YEARS'];
        $date_str   = $year.'-'.$month.'-'.$day;
        $date_where = "DATE_FORMAT(con.pubdate, '%Y-%c-%e') LIKE '$date_str'";
    }

    $sql = "SELECT con.*,
                   cat.title category,
                   cat.seolink as cat_seolink, 
                   cat.id cid
            FROM cms_content con
			INNER JOIN cms_category cat ON cat.id = con.category_id
            WHERE $date_where AND con.published = 1 AND con.pubdate <= '$today' {$cfg_sql}
            ORDER BY con.pubdate DESC";

    $result         = $inDB->query($sql);
    $items_count    = $inDB->num_rows($result);
    $items          = array();

    if ($items_count){
        $inCore->loadModel('content');
        $content_model = new cms_model_content();

        while($item = $inDB->fetch_assoc($result)){
            if($inCore->checkUserAccess('material', $item['id'])){
                $item['url'] = $content_model->getArticleURL(0, $item['seolink']);
                $item['category_url'] = $content_model->getCategoryURL(0, $item['cat_seolink']);
				$item['fdate'] = $inCore->dateFormat($item['pubdate'], true, true);
                $items[] = $item;
            }
        }
    }

    $smarty = $inCore->initSmarty('components', 'com_arhive_list.tpl');
    $smarty->assign('heading', $heading);
    $smarty->assign('items', $items);
    $smarty->assign('items_count', $items_count);
    $smarty->display('com_arhive_list.tpl');

//======================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>