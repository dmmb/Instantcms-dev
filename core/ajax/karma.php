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

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	session_start();

	if (!isset($_REQUEST['target'])) { die(2); }
	if (!isset($_REQUEST['item_id'])) { die(3); }
	if (!isset($_REQUEST['opt'])) { die(4); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('page');
    $inCore->loadClass('user');

    $inUser = cmsUser::getInstance();
	$inUser->update();
    if (!$inUser->id) { $inCore->halt(); }

	$target  = $inCore->request('target', 'str');
	$item_id = $inCore->request('item_id', 'int');
	$opt     = $inCore->request('opt', 'str');
	
	if (!preg_match('/^([a-zA-Z0-9\_]+)$/i', $target)) { return; }

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