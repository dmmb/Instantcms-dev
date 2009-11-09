<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function arhive(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();
    $inUser     = cmsUser::getInstance();

	$menu_id    = $inCore->menuId();
	$cfg        = $inCore->loadComponentConfig('arhive');

    $id         = $inCore->request('id', 'int', 0);
    $do         = $inCore->request('do', 'str', 'view');

    $year       = $inCore->request('y', 'int', 'all');
    $month      = $inCore->request('m', 'int', 'all');
    $day        = $inCore->request('d', 'int', 'all');
    global $_LANG;

	$inPage->setTitle($_LANG['ARCHIVE_MATERIALS']);

 //======================================================================================================//
    //echo $_LANG['ARCHIVE_MATERIALS'];

	if ($year == 'all'){

		$sql = "SELECT DATE_FORMAT( pubdate, '%M, %Y' ) fdate,
                       DATE_FORMAT( pubdate, '%Y' ) year,
                       DATE_FORMAT( pubdate, '%m' ) month,
                       COUNT( id ) num
				FROM cms_content
				GROUP BY DATE_FORMAT(pubdate, '%M, %Y') 
				ORDER BY pubdate DESC";

		$result         = $inDB->query($sql);
        $items_count    = $inDB->num_rows($result);
        $items = array();

		if ($items_count){
			while ($item = $inDB->fetch_assoc($result)){
                $item['fdate']  = $inCore->getRusDate($item['fdate']);
                $items[]        = $item;
			}
		}

        $smarty = $inCore->initSmarty('components', 'com_arhive_dates.tpl');
        $smarty->assign('heading', $_LANG['ARCHIVE_MATERIALS']);
        $smarty->assign('menuid', $menu_id);
        $smarty->assign('items', $items);
        $smarty->assign('items_count', $items_count);
        $smarty->display('com_arhive_dates.tpl');

        return;
	}

//======================================================================================================//

    if($year != 'all' && $day=='all' && $month=='all'){

        $inPage->addPathway($year, '/arhive/'.$menuid.'/'.$year);

        $sql = "SELECT DATE_FORMAT( pubdate, '%M' ) fdate, DATE_FORMAT( pubdate, '%Y' ) year, DATE_FORMAT( pubdate, '%m' ) month, COUNT( id ) num
                FROM cms_content
                WHERE DATE_FORMAT(pubdate, '%Y') LIKE '$year'
                GROUP BY DATE_FORMAT(pubdate, '%M')
                ORDER BY pubdate DESC
                ";
        $result = $inDB->query($sql);

        $items_count    = $inDB->num_rows($result);
        $items = array();

		if ($items_count){
			while ($item = $inDB->fetch_assoc($result)){
                $item['fdate']  = $inCore->getRusDate($item['fdate']);
                $items[]        = $item;
			}
		}

        $smarty = $inCore->initSmarty('components', 'com_arhive_dates.tpl');
        $smarty->assign('heading', $_LANG['MATERIALS_FROM'].$year.$_LANG['ARHIVE_YEAR']);
        $smarty->assign('menuid', $menu_id);
        $smarty->assign('items', $items);
        $smarty->assign('items_count', $items_count);
        $smarty->display('com_arhive_dates.tpl');

        return;

    }

//======================================================================================================//

    $month_name = $inCore->getRusDate(date('F', mktime(0,0,0,$month,1,$year)));

    if ($year != 'all' && $month != 'all' &&  $day == 'all') {
        $inPage->addPathway($year, '/arhive/'.$menuid.'/'.$year);
        $inPage->addPathway($month_name, '/arhive/'.$menuid.'/'.$year.'/'.$month);
        $heading    = $_LANG['MATERIALS_FROM'].$month_name.' '.$year.$_LANG['ARHIVE_YEARS'];
        $date_str   = $year.'-'.$month;
        $date_where = "DATE_FORMAT(con.pubdate, '%Y-%c') LIKE '$date_str'";
    }

    if ($year != 'all' && $month != 'all' &&  $day != 'all') {
        $inPage->addPathway($year, '/arhive/'.$menuid.'/'.$year);
        $inPage->addPathway($month_name, '/arhive/'.$menuid.'/'.$year.'/'.$month);
        $inPage->addPathway($day, '/arhive/'.$menuid.'/'.$year.'/'.$month.'/'.$day);
        $heading    = $_LANG['MATERIALS_FROM'].$day.', '.$inCore->getRusDate(date('F', mktime(0,0,0,$month,1,$year))).' '.$year.$_LANG['ARHIVE_YEARS'];
        $date_str   = $year.'-'.$month.'-'.$day;
        $date_where = "DATE_FORMAT(con.pubdate, '%Y-%c-%e') LIKE '$date_str'";
    }

    $sql = "SELECT con.*,
                   DATE_FORMAT(con.pubdate, '%d-%m-%Y') fdate,
                   cat.title category,
                   cat.id cid
            FROM cms_content con, cms_category cat
            WHERE $date_where AND con.category_id = cat.id
            ORDER BY con.pubdate DESC";

    $result         = $inDB->query($sql);
    $items_count    = $inDB->num_rows($result);
    $items          = array();

    if ($items_count){
        while($item = $inDB->fetch_assoc($result)){
            if($inCore->checkUserAccess('material', $item['id'])){
                $items[] = $item;
            }
        }
    }

    $smarty = $inCore->initSmarty('components', 'com_arhive_list.tpl');
    $smarty->assign('heading', $heading);
    $smarty->assign('menuid', $menu_id);
    $smarty->assign('items', $items);
    $smarty->assign('items_count', $items_count);
    $smarty->display('com_arhive_list.tpl');

//======================================================================================================//
} //function
?>