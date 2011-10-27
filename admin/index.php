<?php Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	session_start();

	define("VALID_CMS", 1);
	define("VALID_CMS_ADMIN", 1);

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('ADMIN_PATH', getcwd());
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);
	define('ADMIN_DIR', basename(dirname(__FILE__)));

	require("../core/cms.php");
	require("includes/cp.php");

	require("../includes/config.inc.php");
	require("../includes/database.inc.php");
	require("../includes/tools.inc.php");

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');         //��������
    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('plugin');       //�������
    $inCore->loadClass('user');       //�������
	$inCore->loadClass('actions');    //����� �������

    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    date_default_timezone_set($inConf->timezone);

    if ( !$inUser->update() ) { cmsCore::error404(); }

    if(!defined('TEMPLATE_DIR'))define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');
    if(!defined('DEFAULT_TEMPLATE_DIR'))define('DEFAULT_TEMPLATE_DIR', PATH.'/templates/_default_/');

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
        $applet = $inCore->request('view', 'str');
        if (!preg_match('/^([a-z0-9]+)$/i', $applet)) { $inCore->halt('WRONG PARAMETER'); }
		$GLOBALS['applet'] = $applet;
	} else {
		$GLOBALS['applet'] = 'main';
	}

	$GLOBALS['cp_page_title'] = '';
	$GLOBALS['cp_page_head'] = array();
	$GLOBALS['cp_page_body'] = '';

	$GLOBALS['cp_pathway'] = array();
	$GLOBALS['cp_pathway'][0]['title'] = '�������';
	$GLOBALS['cp_pathway'][0]['link'] = 'index.php';

	$GLOBALS['mainmenu'] = array();

	$GLOBALS['cp_page_head'][] = '<script type="text/javascript">var adminDir = "'.$inCore->adminDir.'";</script>';

    $inCore->loadLanguage('lang');

	cpGenerateMenu();

	cpProceedBody();

	include("template.php");

?>
