<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	session_start();
	define("VALID_CMS", 1);	
	require("../../includes/config.inc.php");
	require("../../includes/database.inc.php");
	require("../../core/cms.php");

    $inCore = cmsCore::getInstance();
	
	if(isset($_POST['back']))
	{
		$back = $_POST['back'];
	} else { 
				if (isset($_SERVER['HTTP_REFERER']))
				{		
					$back = $_SERVER['HTTP_REFERER']; 
				} else { $back = "/"; }
		   }		   
	
	if(isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { die(); }

///////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='delcache'){
		
		if (isset($_REQUEST['target'])){
			$target = $_REQUEST['target'];
		} else { die(); }
	
		if (isset($_REQUEST['id'])){
			$target_id = $_REQUEST['id'];
		} else { die(); }
		
		$inCore->deleteCache($target, $target_id);

	}
///////////////////////////////////////////////////////////////////////////////////////////////////		
	header('location:'.$back);	
?>