<?php
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

    Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    setlocale(LC_ALL, 'ru');

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

////////////////////////////// ��������� ��� ������� ����������� /////////////////////////////
//
//if(is_dir('install')||is_dir('migrate')) {
//	if (!file_exists(PATH.'/includes/config.inc.php')){
//		header('location:/install/');
//	} else {
//        include(PATH.'/core/auth/installation.html');
//        die();
//	}
//}

/////////////////////////////////// ���������� //////////////////////////////////////////////
	
	define("VALID_CMS", 1);	
	session_start();    

/////////////////////////////////// �������� ������ /////////////////////////////////////////
	
	$start_time     = microtime();
	$start_array    = explode(" ",$start_time);
	$start_time     = $start_array[1] + $start_array[0];
	
////////////////////////// ��������� ������ ������ //////////////////////////////////////////

	include('core/cms.php');                        //����
    include(PATH.'/includes/config.inc.php');       //������, �������� ��� ������ ��������

    $inCore     = cmsCore::getInstance();
    
    $inCore->loadClass('page');         //��������    
    $inCore->loadClass('plugin');       //�������
    $inCore->loadClass('user');         //������������

    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inUser     = cmsUser::getInstance();

    cmsCore::loadLanguage('lang');      //������� �������� ����

    if ( !$inUser->update() ) { $inCore->redirect('/404'); }

    $home_title = $inConf->hometitle ? $inConf->hometitle : $inConf->sitename;

    $inPage->setTitle( $inConf->sitename );

////////////////////////// ���������, ������� �� ���� ///////////////////////////////////////

	if ($inConf->siteoff && !$inUser->is_admin && !$inCore->request('do', 'str')=='login') {
		$inCore->includeFile('core/auth/siteoff.php');
		$inCore->halt();
	}
	
    if ($inConf->siteoff && $inUser->is_admin) {
       echo $inPage->siteOffNotify();
    }

//////////////////////////// ���������� ������������� ///////////////////////////////////////
	
	$inCore->onlineStats();   //��������� ���������� ��������� �����
	$inUser->autoLogin();     //������������� ���������� ������������, ���� ������ �����
	
/////////////////////////////////// REQUEST PARAMETERS //////////////////////////////////////
	
	//������������� ������� � ��������
    if ($_SESSION['template']) { $inConf->template = $_SESSION['template']; }
	define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');

	//��������� Smarty
	$inCore->loadSmarty();
	$smarty = new Smarty();

	//�������� ID ������ ����
	$menuid = $inCore->menuId();
		
	//������ ����������
	$inPage->addPathway($_LANG['PATH_HOME'], '/');
    $inPage->setTitle( $inCore->menuTitle() );
	if ($menuid > 1) { $inPage->addMenuPathway($menuid); }

	//������ ���� ��������
    $inCore->proceedBody();

	//��������� ������ ������������
	$inCore->�heckMenuAccess();

//////////////////////////////////// ����� ������� //////////////////////////////////////////    

	if($inCore->isSplash()){
		$inPage->showSplash();
	} else {				
		$inPage->showTemplate(TEMPLATE_DIR);
	}

////////////////////////// ��������� � ������� ����� ��������� //////////////////////////////

	$end_time   = microtime();
	$end_array  = explode(" ", $end_time);
	$end_time   = $end_array[1] + $end_array[0];

	$time = $end_time - $start_time;

    echo '<!-- '.$_LANG['DEBUG_TIME_GEN_PAGE'].': '.number_format($time, 4).' '.$_LANG['DEBUG_SEC'].' -->';

    if (strstr($_SERVER['REQUEST_URI'], 'benchmark')){
        echo '<p>';
            echo '<div><strong>'.$_LANG['DEBUG_TIME_GEN_PAGE'].'</strong> '.number_format($time, 4).' '.$_LANG['DEBUG_SEC'].'</div>';
            echo '<div><strong>'.$_LANG['DEBUG_QUERY_DB'].'</strong> '.$inDB->q_count.'</div>';
        echo '</p>';
    }

   $inCore->clearSessionTrash();

?>
