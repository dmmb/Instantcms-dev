<?php

	session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');
    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config'); 
	$inCore->loadClass('db');
	$inCore->loadClass('page');
    $inCore->loadClass('user');

    $inUser = cmsUser::getInstance();
	$inDB   = cmsDatabase::getInstance();

	$inUser->update();
    if (!$inUser->id) { $inCore->halt(); }

    $user_id = $inCore->request('user_id', 'int');

    if (!$user_id) return;

	$last_ip = $inDB->get_field('cms_users', "id = '$user_id'", 'last_ip');
	
	echo $last_ip;
    
?>