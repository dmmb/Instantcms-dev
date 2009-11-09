<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

    $q = @iconv('UTF-8//IGNORE', 'WINDOWS-1251//IGNORE', htmlspecialchars($_GET['q'], ENT_QUOTES));
	$q = strtolower($q);

	if (!$q) return;

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $inDB = cmsDatabase::getInstance();

	$sql 	= "SELECT city FROM cms_user_profiles WHERE LOWER(city) LIKE '{$q}%' GROUP BY city LIMIT 10";
	$rs 	= $inDB->query($sql);

	while ($item = mysql_fetch_assoc($rs)){
        echo $item['city'];
	}

    return;
    
?>