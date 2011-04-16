<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

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