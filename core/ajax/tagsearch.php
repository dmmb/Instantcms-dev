<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $q = $inCore->request('q', 'str', '');

    if (!$q) return;

	$q = iconv('UTF-8//IGNORE', 'WINDOWS-1251//IGNORE', $q);
	$q = strtolower($q);

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