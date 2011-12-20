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
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $q = $inCore->request('q', 'str', '');

    if (!$q) return;

	$q = mb_strtolower($q);

    $inCore->loadClass('config');           //конфигурация
    $inCore->loadClass('db');               //база данных

    $inDB   = cmsDatabase::getInstance();

	$sql 	= "SELECT tag FROM cms_tags WHERE LOWER(tag) LIKE '{$q}%' GROUP BY tag";
	$rs 	= $inDB->query($sql);

	while ($item = $inDB->fetch_assoc($rs)){
		echo $item['tag']."\n";
	}

    return;

?>