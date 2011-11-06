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

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $q = $inCore->request('q', 'str', '');

    if (!$q) return;

	$q = iconv('UTF-8//IGNORE', 'WINDOWS-1251//IGNORE', $q);
	$q = strtolower($q);

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $inDB = cmsDatabase::getInstance();

	$sql 	= "SELECT city FROM cms_user_profiles WHERE LOWER(city) LIKE '{$q}%' GROUP BY city LIMIT 10";
	$rs 	= $inDB->query($sql);

	while ($item = $inDB->fetch_assoc($rs)){
        echo $item['city']."\n";
	}

    return;
    
?>