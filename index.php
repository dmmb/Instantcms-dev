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

    Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    header('Content-Type: text/html; charset=utf-8');
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

////////////////////////////// Проверяем что система установлена /////////////////////////////

    if(is_dir('install')||is_dir('migrate')) {
        if (!file_exists(PATH.'/includes/config.inc.php')){
            header('location:/install/');
        } else {
            include(PATH.'/core/messages/installation.html');
            die();
        }
    }

/////////////////////////////////// Подготовка //////////////////////////////////////////////
	
	define("VALID_CMS", 1);	
	session_start();    

	include('core/cms.php');                        //ядро
    include(PATH.'/includes/config.inc.php');       //конфиг, оставлен для старых плагинов

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

/////////////////////////////////// Включаем таймер /////////////////////////////////////////

    $inCore->startGenTimer();
    
////////////////////////// Загружаем нужные классы //////////////////////////////////////////
   
    $inCore->loadClass('page');         //страница    
    $inCore->loadClass('plugin');       //плагины
    $inCore->loadClass('user');         //пользователь
    $inCore->loadClass('actions');      //лента активности    

    $inDB   = cmsDatabase::getInstance();
    $inPage = cmsPage::getInstance();
    $inConf = cmsConfig::getInstance();
    $inUser = cmsUser::getInstance();

    date_default_timezone_set($inConf->timezone);

	$inUser->autoLogin();     //автоматически авторизуем пользователя, если найден кукис

    //проверяем что пользователь не удален и не забанен
    if (!$inUser->update() && !$_SERVER['REQUEST_URI']!=='/logout') { $inCore->halt(); }

    //определяем заголовок главной страницы
    $home_title = $inConf->hometitle ? $inConf->hometitle : $inConf->sitename;

    //устанавливаем заголовок браузера в название сайта
    $inPage->setTitle( $inConf->sitename );

////////////////////////// Проверяем, включен ли сайт //////////////////////////

    //Если сайт выключен и пользователь не администратор,
    //то показываем шаблон сообщения о том что сайт отключен
	if ( $inConf->siteoff &&
        !$inUser->is_admin &&
        $_SERVER['REQUEST_URI']!='/login' &&
        $_SERVER['REQUEST_URI']!='/logout'
       ) {
            $inPage->includeTemplateFile('special/siteoff.php');
            $inCore->halt();
	}

    //Если сайт выключен, но пользователь - администратор,
    //то выводим полоску с сообщением "Внимание, сайт отключен"
    if ($inConf->siteoff && $inUser->is_admin) {
       echo $inPage->siteOffNotify();
    }

//////////////////////////// Мониторинг пользователей //////////////////////////
	
	$inCore->onlineStats();   //обновляем статистику посещений сайта
	
////////////////////////////// Генерация страницы //////////////////////////////
	
	//Получаем ID текущего пункта меню
	$menuid = $inCore->menuId();
		
	//Строим глубиномер
	$inPage->addPathway($_LANG['PATH_HOME'], '/');
    $inPage->setTitle( $inCore->menuTitle() );
	if ($menuid > 1) { $inPage->addMenuPathway($menuid); }

	//Проверяем доступ пользователя
    //При положительном результате
	//Строим тело страницы (запускаем текущий компонент)
    if ($inCore->checkMenuAccess()) $inCore->proceedBody();

//////////////////////////////////// Вывод шаблона /////////////////////////////

    //Проверяем нужно ли показать входную страницу (splash)
	if($inCore->isSplash()){
        //Показываем входную страницу
		if (!$inPage->showSplash()){
            //Если шаблон входной страницы не был найден,
            //показываем обычный шаблон сайта
            $inPage->showTemplate();
        }
	} else {
        //показываем шаблон сайта
		$inPage->showTemplate();
	}

////////////// Вычисляем и выводим время генерации, запросы к базе /////////////

	if ($inDB->q_count && $inConf->debug) {
		$time = $inCore->getGenTime();
		echo $_LANG['DEBUG_TIME_GEN_PAGE'].' '.number_format($time, 4).' '.$_LANG['DEBUG_SEC'];
		echo '<br />'.$_LANG['DEBUG_QUERY_DB'];
		echo ' '.$inDB->q_count.'<br />';
		echo $inDB->q_dump;
	}
//////////////////////// Очищаем временные переменные //////////////////////////

    $inCore->clearSessionTrash();

?>
