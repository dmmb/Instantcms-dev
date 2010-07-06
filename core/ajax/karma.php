<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	session_start();

	if (!isset($_REQUEST['target'])) { die(2); } else { $target = $_REQUEST['target']; }
	if (!isset($_REQUEST['item_id'])) { die(3); } else { $item_id = intval($_REQUEST['item_id']); }
	if (!isset($_REQUEST['opt'])) { die(4); } else { $opt = $_REQUEST['opt']; }	

    if (!preg_match('/^([a-zA-Z0-9\_]+)$/i', $target)) { return; }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');
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
	
	//PREPARE POINTS
	$points = cmsKarmaFormat($postkarma['points']);
	echo $points;
    
?>