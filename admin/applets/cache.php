<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function applet_cache(){
    $inCore = cmsCore::getInstance();
	
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
	$inCore->redirectBack();

}
?>