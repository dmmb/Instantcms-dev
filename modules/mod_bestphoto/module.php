<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_bestphoto($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);
		
    	global $_LANG;
		
		if (!isset($cfg['showtype'])) { $cfg['showtype'] = 'full'; }
		if (!isset($cfg['showmore'])) { $cfg['showmore'] = 1; }
		if (!isset($cfg['album_id'])) { $cfg['album_id'] = 0; }
		
		if ($cfg['album_id']) { $catsql = ' AND album_id = '.$cfg['album_id']; }
		else { $catsql = ''; }
		
		$sql = "SELECT f.*, f.id as fid, f.pubdate as fpubdate,
					   a.id as album_id, a.title as album, 
					   IFNULL(r.total_rating, 0) as rating
				FROM cms_photo_files f
				LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target = 'photo'
				LEFT JOIN cms_photo_albums a ON f.album_id = a.id
				WHERE f.published = 1 ".$catsql."
				ORDER BY ".$cfg['sort']." DESC 
				LIMIT ".$cfg['shownum'];		
 	
		$result = $inDB->query($sql) ;

		$is_best = false;	

		if (!function_exists('cmsKarmaFormat') && $cfg['showdate']){ //if not included earlier
			include_once($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
		}

		if ($inDB->num_rows($result)){	
			$is_best = true;
			$cons = array();
			while($con = $inDB->fetch_assoc($result)){
				if ($cfg['showtype']=='full'){
						if($cfg['showcom'] || $cfg['showdate']){
								if ($cfg['showdate']){
									if ($cfg['sort'] == 'rating'){
										$con['votes'] = cmsKarmaFormat($con['rating']);
									} else {
										$con['votes'] = $con[$cfg['sort']];
									}
								}
								if ($cfg['showcom']){
									$con['comments'] = $inCore->getCommentsCount('photo', $con['id']);
								}
						}
				}
				$cons[] = $con;
			}			
			}
		$smarty = $inCore->initSmarty('modules', 'mod_bestphoto.tpl');			
		$smarty->assign('cons', $cons);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_best', $is_best);			
		$smarty->display('mod_bestphoto.tpl');
				
		return true;
	
}
?>