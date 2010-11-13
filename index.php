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

////////////////////////////// Проверяем что система установлена /////////////////////////////

//    if(is_dir('install')||is_dir('migrate')) {
//        if (!file_exists(PATH.'/includes/config.inc.php')){
//            header('location:/install/');
//        } else {
//            include(PATH.'/core/messages/installation.html');
//            die();
//        }
//    }

/////////////////////////////////// Подготовка //////////////////////////////////////////////
	
	define("VALID_CMS", 1);	
	session_start();    

	include('core/cms.php');                        //ядро
    include(PATH.'/includes/config.inc.php');       //конфиг, оставлен для старых плагинов

    $inCore = cmsCore::getInstance();

/////////////////////////////////// Включаем таймер /////////////////////////////////////////

    $inCore->startGenTimer();
    
////////////////////////// Загружаем нужные классы //////////////////////////////////////////
   
    $inCore->loadClass('page');         //страница    
    $inCore->loadClass('plugin');       //плагины
    $inCore->loadClass('user');         //пользователь
    $inCore->loadClass('actions');      //лента активности

    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inUser     = cmsUser::getInstance();

    cmsCore::loadLanguage('lang');      //главный языковый файл

	$inUser->autoLogin();     //автоматически авторизуем пользователя, если найден кукис

    //проверяем что пользователь не удален и не забанен
    if (!$inUser->update() && !$inCore->request('uri', 'str')=='/logout') { $inCore->halt(); }

    //определяем заголовок главной страницы
    $home_title = $inConf->hometitle ? $inConf->hometitle : $inConf->sitename;

    //устанавливаем заголовок браузера в название сайта
    $inPage->setTitle( $inConf->sitename );

//////////////////////// Определяем каталоги шаблонов //////////////////////////

    //проверяем был ли переопределен шаблон через сессию
    //например, из модуля "выбор шаблона"
    if ($_SESSION['template']) { $inConf->template = $_SESSION['template']; }

    define('TEMPLATE', $inConf->template);
	define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');
	define('DEFAULT_TEMPLATE_DIR', PATH.'/templates/_default_/');

////////////////////////// Проверяем, включен ли сайт //////////////////////////

    //Если сайт выключен и пользователь не администратор,
    //то показываем шаблон сообщения о том что сайт отключен
	if ( $inConf->siteoff &&
        !$inUser->is_admin &&
        !$inCore->request('uri', 'str')=='/login' &&
        !$inCore->request('uri', 'str')=='/logout'
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
	
	//Загружаем Smarty
	$inCore->loadSmarty();
	$smarty = new Smarty();

	//Получаем ID текущего пункта меню
	$menuid = $inCore->menuId();
		
	//Строим глубиномер
	$inPage->addPathway($_LANG['PATH_HOME'], '/');
    $inPage->setTitle( $inCore->menuTitle() );
	if ($menuid > 1) { $inPage->addMenuPathway($menuid); }

	//Строим тело страницы (запускаем текущий компонент)
    $inCore->proceedBody();

	//Проверяем доступ пользователя
    //Если проверка завершится неудачей, то вывод компонента будет
    //замещен сообщением "Доступ запрещен"
	$inCore->сheckMenuAccess();

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

//////////////////////// Вычисляем и выводим время генерации ///////////////////

	$time = $inCore->getGenTime();

    echo '<!-- '.$_LANG['DEBUG_TIME_GEN_PAGE'].': '.number_format($time, 4).' '.$_LANG['DEBUG_SEC'].' -->';

//////////////////////// Очищаем временные переменные //////////////////////////

    $inCore->clearSessionTrash();

?>
