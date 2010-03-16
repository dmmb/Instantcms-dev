<?php Error_Reporting(E_ALL & ~E_NOTICE);
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

	session_start();
	
	define("VALID_CMS", 1);
	define("VALID_CMS_ADMIN", 1);

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	require("../core/cms.php");
	require("includes/cp.php");
					
	require("../includes/config.inc.php");	
	require("../includes/database.inc.php");
	require("../includes/tools.inc.php");	

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('plugin');       //плагины
    $inCore->loadClass('user');       //плагины

    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    if ( !$inUser->update() ) { $inCore->redirect('/404'); }

    define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');

	//-------CHECK AUTHENTICATION--------------------------------------//
	if (!$inUser->id ) {
		$inCore->redirect('login.php');
	} else {	
		if (!$inCore->userIsAdmin($inUser->id)){			
			if ($inCore->userIsEditor($inUser->id)){
				$inCore->redirect('editor/index.php');
			} else { $inCore->redirect('login.php'); }
		}
	}
	//--------LOAD ACCESS OPTIONS LIST---------------------------------//
	
	$adminAccess = $inCore->checkAdminAccess();	

	//------------------------------------------------------------------//

	$inCore->onlineStats();

	if (isset($_REQUEST['view'])){
		$GLOBALS['applet'] = $_REQUEST['view'];
	} else {
		$GLOBALS['applet'] = 'main';
	}
	
	$GLOBALS['cp_page_title'] = '';
	$GLOBALS['cp_page_head'] = array();
	$GLOBALS['cp_page_body'] = '';
	
	$GLOBALS['cp_pathway'] = array();
	$GLOBALS['cp_pathway'][0]['title'] = 'Главная';
	$GLOBALS['cp_pathway'][0]['link'] = 'index.php';
	
	$GLOBALS['mainmenu'] = array();

	cpGenerateMenu();

	cpProceedBody();

	include("template.php");

?>
