<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_random_image($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}

		if ($cfg['album_id']>0){
			$s = "AND f.album_id = ". $cfg['album_id'];			
		} else {
			$s = "";
		}
	
		$sql = "SELECT f.*, a.title album_title
				FROM cms_photo_files f, cms_photo_albums a
				WHERE f.album_id = a.id ".$s." AND f.published = 1
				ORDER BY RAND()
				LIMIT 1
				";
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){

				echo '<a href="/photos/'.$menuid.'/photo'.$item['id'].'.html">
					  <p align="center"><img src="/images/photos/small/'.$item['file'].'" border="0"/></p>';
				if($cfg['showtitle']){
					echo '<p align="center">'.$item['title'].'</p>';
				}
				echo '</a>';				

			
			}
		}

		return true;	
	}
?>