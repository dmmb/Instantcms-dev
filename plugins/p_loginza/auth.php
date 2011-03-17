<?php

	session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
   	define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

	$inCore = cmsCore::getInstance();
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('page');
    $inCore->loadClass('user');
    $inCore->loadClass('plugin');
    
    cmsCore::callEvent('LOGINZA_AUTH', array());

    exit;

?>