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

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');
	// Грузим конфиг
	include(PATH.'/includes/config.inc.php');
    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
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