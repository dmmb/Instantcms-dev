<?php

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('page');
    $inCore->loadClass('user');
   
    $user_id    = $inCore->request('user_id', 'int', 0);
    $usertype   = $inCore->request('usertype', 'str', '');
    $page       = $inCore->request('page', 'int', 1);

    $inUser = cmsUser::getInstance();
    if ( !$inUser->update() ) { return; }

    $inCore->loadSmarty();
	$smarty = new Smarty();

    cmsCore::loadLanguage('lang');

    echo cmsUser::getUserWall($user_id, $usertype, $page);

    return;
    
?>