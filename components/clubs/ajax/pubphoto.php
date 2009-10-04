<?php
	session_start();	

	define("VALID_CMS", 1);

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inDB = cmsDatabase::getInstance();

    $inUser = cmsUser::getInstance();
    $inUser->update();

	$inCore->loadLib('clubs');
	$inCore->loadLib('photos');
	$inCore->loadLib('tags');

	$id = $inCore->request('id', 'int');
	$clubid = $inCore->request('clubid', 'int');

	if (!$id || !$clubid) return;

	$club = dbGetFields('cms_clubs', 'id='.$clubid, '*');
	
	if ($inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($clubid, $inUser->id) || clubUserIsRole($clubid, $inUser->id, 'moderator')){
		$ok = albumDelete($id, 'club');
	} else {
		$ok = false;
	}

    if($ok){
		echo 'ok';
	} else {
		echo 'error';
	}	
?>