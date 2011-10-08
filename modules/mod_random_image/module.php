<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	function mod_random_image($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

        $catsql = '';

		if ($cfg['album_id'] != 0) {
			if ($cfg['subs']) {
				$rootcat = $inDB->get_fields('cms_photo_albums', 'id='.$cfg['album_id'], 'NSLeft, NSRight');
				$catsql = " AND a.NSLeft >= {$rootcat['NSLeft']} AND a.NSRight <= {$rootcat['NSRight']}";
		} else {
				$catsql = "AND f.album_id = ". $cfg['album_id'];
			}
		}
	
		$sql = "SELECT f.*, a.title album_title
				FROM cms_photo_files f
				LEFT JOIN cms_photo_albums a ON a.id = f.album_id
				WHERE f.published = 1 ".$catsql."
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