<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	session_start();

	if (!isset($_REQUEST['target'])) { die(2); } else { $target = $_REQUEST['target']; }
	if (!isset($_REQUEST['item_id'])) { die(3); } else { $item_id = $_REQUEST['item_id']; }	
	if (!isset($_REQUEST['opt'])) { die(4); } else { $opt = $_REQUEST['opt']; }	

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inUser = cmsUser::getInstance();
    if (!$inUser->update()) { $inCore->halt(); }

	$inCore->loadLib('karma');
	
	if ($opt=='plus'){
		cmsSubmitKarma($target, $item_id, 1);
	}
	if ($opt=='minus'){
		cmsSubmitKarma($target, $item_id, -1);
	}

	$postkarma = cmsKarma($target, $item_id);
	
	setUsersRating();
	
	//PREPARE POINTS
	$points = cmsKarmaFormat($postkarma['points']);
	echo $points;
?>