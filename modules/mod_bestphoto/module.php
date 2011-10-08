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

function mod_bestphoto($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);
		
    	global $_LANG;
		
		if (!isset($cfg['showtype'])) { $cfg['showtype'] = 'full'; }
		if (!isset($cfg['showmore'])) { $cfg['showmore'] = 1; }
		if (!isset($cfg['album_id'])) { $cfg['album_id'] = 0; }
		if (!isset($cfg['whatphoto'])) { $cfg['whatphoto'] = 'all'; }
		
		$catsql = $cfg['album_id'] ? ' AND album_id = '.$cfg['album_id'] : '';
		
		$today = date("d-m-Y");

		switch ($cfg['whatphoto']){

			case 'all':   $periodsql = ''; break;
			case 'day':   $periodsql = " AND DATE_FORMAT(f.pubdate, '%d-%m-%Y')='$today'"; break;
			case 'week':  $periodsql = " AND DATEDIFF(NOW(), f.pubdate) <= 7"; break;
			case 'month': $periodsql = " AND DATE_SUB(NOW(), INTERVAL 1 MONTH) < f.pubdate"; break;

		}

		$sql = "SELECT f.id, f.album_id, f.title, f.pubdate, f.file, f.hits, f.comments, a.title as album,
					   IFNULL(r.total_rating, 0) as rating
				FROM cms_photo_files f
				LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target = 'photo'
				LEFT JOIN cms_photo_albums a ON f.album_id = a.id
					WHERE f.published = 1 ".$catsql." ".$periodsql." 
				ORDER BY ".$cfg['sort']." DESC 
				LIMIT ".$cfg['shownum'];		
 	
		$result = $inDB->query($sql) ;

		$is_best = false;	

		if (!function_exists('cmsKarmaFormat') && $cfg['showrating']){ //if not included earlier
			include_once($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
		}

		if ($inDB->num_rows($result)){	
			$is_best = true;
			$cons = array();
			while($con = $inDB->fetch_assoc($result)){
				if ($cfg['showtype']=='full'){
					if ($cfg['showrating']){
									if ($cfg['sort'] == 'rating'){
										$con['votes'] = cmsKarmaFormat($con['rating']);
									} else {
										$con['votes'] = $con[$cfg['sort']];
									}
								}
					if ($cfg['showdate']){
						$con['pubdate'] = cmsCore::dateFormat($con['pubdate']);
					}
					if ($cfg['showcom'] && $con['comments']){
									$con['comments'] = $inCore->getCommentsCount('photo', $con['id']);
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