<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $opt  = $inCore->request('opt', 'str', '');
    $data = $inCore->request('data', 'str', '');

    if (!$opt) { return; }

    $inDB = cmsDatabase::getInstance();

	if ($opt=='checklogin'){

		$sql    = "SELECT id, login FROM cms_users WHERE (login LIKE '$data') AND (is_deleted = 0) LIMIT 1";
		$result = $inDB->query($sql);

		if($inDB->num_rows($result)==0){
			echo '<span style="color:green">Вы можете использовать этот логин</span>';		
		} else {
			echo '<span style="color:red">Выбранный логин занят!</span>';				
		}

	}

    return;

?>