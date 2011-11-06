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

	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    
	include(PATH.'/core/cms.php');
    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

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
