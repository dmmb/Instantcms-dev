<?php
	session_start();
    if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }      
	if (!isset($_REQUEST['user_id'])) { die(1); } else { $user_id = $_REQUEST['user_id']; }

	define("VALID_CMS", 1);	
	include($_SERVER['DOCUMENT_ROOT'].'/includes/config.inc.php');
	include($_SERVER['DOCUMENT_ROOT'].'/includes/database.inc.php');
	include($_SERVER['DOCUMENT_ROOT'].'/core/cms.php');

	$last_ip = dbGetField('cms_users', 'id='.$user_id, 'last_ip');
	
	echo $last_ip;
?>