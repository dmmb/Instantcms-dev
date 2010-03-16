<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_user_image($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}

		$sql = "SELECT u.id uid, u.nickname author, u.login as login, p.*, pr.gender gender
				FROM cms_users u, cms_user_photos p, cms_user_profiles pr
				WHERE p.user_id = u.id AND p.allow_who = 'all' AND pr.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0
				ORDER BY RAND()
				LIMIT 1
				";
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){

				echo '<a href="/users/'.$menuid.'/'.$item['uid'].'/photo'.$item['id'].'.html">
					  <div align="center"><img src="/images/users/photos/small/'.$item['imageurl'].'" border="0"/></div></a>';
				if($cfg['showtitle']){
					echo '<div style="margin-top:5px" align="center"><strong>'.$item['title'].'</strong></div>';
					echo '<div align="center">'.cmsUser::getGenderLink($item['uid'], $item['author'], $menuid, $item['gender'], $item['login']).'</a></div>';
				}
			
			}
		}

		return true;	
	}
?>