<?php

	$q = iconv('UTF-8//IGNORE', 'WINDOWS-1251//IGNORE', htmlspecialchars($_GET['q'], ENT_QUOTES));
	$q = strtolower($q);

	if (!$q) return;

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

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