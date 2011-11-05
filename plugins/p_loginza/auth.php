<?php

	session_start();

	define("VALID_CMS", 1);
    define('PATH', str_replace('/plugins/p_loginza', '', dirname(__FILE__)));	

	include(PATH.'/core/cms.php');

	$inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('page');
    $inCore->loadClass('user');
    $inCore->loadClass('plugin');
    
    cmsCore::callEvent('LOGINZA_AUTH', array());

    exit;

?>