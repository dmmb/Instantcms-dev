<?php

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');
	// ������ ������
	include(PATH.'/includes/config.inc.php');
    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('page');
    $inCore->loadClass('user');
    $inCore->loadClass('plugin');
   
    $user_id    = $inCore->request('user_id', 'int', 0);
    $usertype   = $inCore->request('usertype', 'str', '');
    $page       = $inCore->request('page', 'int', 1);

    $inUser = cmsUser::getInstance();
    if ( !$inUser->update() ) { return; }

	$smarty = $inCore->initSmarty();

    cmsCore::loadLanguage('lang');

    echo cmsUser::getUserWall($user_id, $usertype, $page);

    return;
    
?>