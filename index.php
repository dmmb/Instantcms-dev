<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

    Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    setlocale(LC_ALL, 'ru');

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

////////////////////////////// ��������� ��� ������� ����������� /////////////////////////////

//    if(is_dir('install')||is_dir('migrate')) {
//        if (!file_exists(PATH.'/includes/config.inc.php')){
//            header('location:/install/');
//        } else {
//            include(PATH.'/core/messages/installation.html');
//            die();
//        }
//    }

/////////////////////////////////// ���������� //////////////////////////////////////////////
	
	define("VALID_CMS", 1);	
	session_start();    

	include('core/cms.php');                        //����
    include(PATH.'/includes/config.inc.php');       //������, �������� ��� ������ ��������

    $inCore = cmsCore::getInstance();

/////////////////////////////////// �������� ������ /////////////////////////////////////////

    $inCore->startGenTimer();
    
////////////////////////// ��������� ������ ������ //////////////////////////////////////////
   
    $inCore->loadClass('page');         //��������    
    $inCore->loadClass('plugin');       //�������
    $inCore->loadClass('user');         //������������
    $inCore->loadClass('actions');      //����� ����������

    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inUser     = cmsUser::getInstance();

    cmsCore::loadLanguage('lang');      //������� �������� ����

	$inUser->autoLogin();     //������������� ���������� ������������, ���� ������ �����

    //��������� ��� ������������ �� ������ � �� �������
    if (!$inUser->update() && !$inCore->request('uri', 'str')=='/logout') { $inCore->halt(); }

    //���������� ��������� ������� ��������
    $home_title = $inConf->hometitle ? $inConf->hometitle : $inConf->sitename;

    //������������� ��������� �������� � �������� �����
    $inPage->setTitle( $inConf->sitename );

//////////////////////// ���������� �������� �������� //////////////////////////

    //��������� ��� �� ������������� ������ ����� ������
    //��������, �� ������ "����� �������"
    if ($_SESSION['template']) { $inConf->template = $_SESSION['template']; }

    define('TEMPLATE', $inConf->template);
	define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');
	define('DEFAULT_TEMPLATE_DIR', PATH.'/templates/_default_/');

////////////////////////// ���������, ������� �� ���� //////////////////////////

    //���� ���� �������� � ������������ �� �������������,
    //�� ���������� ������ ��������� � ��� ��� ���� ��������
	if ( $inConf->siteoff &&
        !$inUser->is_admin &&
        !$inCore->request('uri', 'str')=='/login' &&
        !$inCore->request('uri', 'str')=='/logout'
       ) {
            $inPage->includeTemplateFile('special/siteoff.php');
            $inCore->halt();
	}

    //���� ���� ��������, �� ������������ - �������������,
    //�� ������� ������� � ���������� "��������, ���� ��������"
    if ($inConf->siteoff && $inUser->is_admin) {
       echo $inPage->siteOffNotify();
    }

//////////////////////////// ���������� ������������� //////////////////////////
	
	$inCore->onlineStats();   //��������� ���������� ��������� �����
	
////////////////////////////// ��������� �������� //////////////////////////////
	
	//��������� Smarty
	$inCore->loadSmarty();
	$smarty = new Smarty();

	//�������� ID �������� ������ ����
	$menuid = $inCore->menuId();
		
	//������ ����������
	$inPage->addPathway($_LANG['PATH_HOME'], '/');
    $inPage->setTitle( $inCore->menuTitle() );
	if ($menuid > 1) { $inPage->addMenuPathway($menuid); }

	//������ ���� �������� (��������� ������� ���������)
    $inCore->proceedBody();

	//��������� ������ ������������
    //���� �������� ���������� ��������, �� ����� ���������� �����
    //������� ���������� "������ ��������"
	$inCore->�heckMenuAccess();

//////////////////////////////////// ����� ������� /////////////////////////////

    //��������� ����� �� �������� ������� �������� (splash)
	if($inCore->isSplash()){
        //���������� ������� ��������
		if (!$inPage->showSplash()){
            //���� ������ ������� �������� �� ��� ������,
            //���������� ������� ������ �����
            $inPage->showTemplate();
        }
	} else {
        //���������� ������ �����
		$inPage->showTemplate();
	}

//////////////////////// ��������� � ������� ����� ��������� ///////////////////

	$time = $inCore->getGenTime();

    echo '<!-- '.$_LANG['DEBUG_TIME_GEN_PAGE'].': '.number_format($time, 4).' '.$_LANG['DEBUG_SEC'].' -->';

//////////////////////// ������� ��������� ���������� //////////////////////////

    $inCore->clearSessionTrash();

?>
