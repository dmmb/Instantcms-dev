<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_random_image($module_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);

		if ($cfg['album_id']>0){
			$s = "AND f.album_id = ". $cfg['album_id'];			
		} else {
			$s = "";
		}
	
		$sql = "SELECT f.*, a.title album_title
				FROM cms_photo_files f
				LEFT JOIN cms_photo_albums a ON a.id = f.album_id
				WHERE f.published = 1 ".$s."
				ORDER BY RAND()
				LIMIT 1
				";

		$result = $inDB->query($sql) ;

		$is_img = false;

		if ($inDB->num_rows($result)){
			$is_img = true;
			
			$item=$inDB->fetch_assoc($result);

		}
	
		$smarty = $inCore->initSmarty('modules', 'mod_random_image.tpl');			
		$smarty->assign('item', $item);
		$smarty->assign('is_img', $is_img);
		$smarty->assign('cfg', $cfg);
		$smarty->display('mod_random_image.tpl');

		return true;	

}
?>