<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

function mod_latestphoto($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

        $catsql = '';

		if ($cfg['album_id'] != 0) {
            $rootcat = $inDB->get_fields('cms_photo_albums', "id='{$cfg['album_id']}'", 'NSLeft, NSRight');
			if(!$rootcat) { return false; }
            $catsql = " AND a.NSLeft >= {$rootcat['NSLeft']} AND a.NSRight <= {$rootcat['NSRight']}";
        }

		if (!isset($cfg['showtype'])) { $cfg['showtype'] = 'full'; }
		if (!isset($cfg['showmore'])) { $cfg['showmore'] = 1; }
        if (!isset($cfg['showclubs'])) { $cfg['showclubs'] = 1; }

        if (!$cfg['showclubs']) { $catsql .= " AND a.NSDiffer=''"; }
		
		$sql = "SELECT f.*, a.id as album_id, a.title as album
				FROM cms_photo_files f
				LEFT JOIN cms_photo_albums a ON a.id = f.album_id
				WHERE f.published = 1 ".$catsql."
				ORDER BY f.id DESC
				LIMIT ".$cfg['shownum'];		
 	
		$result = $inDB->query($sql);
		$is_photo = false;	
			
		if ($inDB->num_rows($result)){	
			$photos = array();			
			$is_photo = true;	
				
			while($con = $inDB->fetch_assoc($result)){
				if ($cfg['showtype']=='full'){
						if($cfg['showcom'] || $cfg['showdate']){
								if ($cfg['showdate']){
									$con['fpubdate'] = $inCore->dateFormat($con['pubdate']);
								}
								if ($cfg['showcom']){
									$con['comments'] = $inCore->getCommentsCount('photo', $con['id']);
								}
						}
				}
				$photos[] = $con;	
			}			
			}
		
		$smarty = $inCore->initSmarty('modules', 'mod_latestphoto.tpl');			
		$smarty->assign('photos', $photos);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_photo', $is_photo);			
		$smarty->display('mod_latestphoto.tpl');
				
		return true;
	
}
?>