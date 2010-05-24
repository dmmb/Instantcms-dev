<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_hmenu($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$menuid = $inCore->menuId();
		$cfg = $inCore->loadModuleConfig($module_id);
		
		if (!isset($cfg['menu'])) { $menu = 'mainmenu'; } else { $menu = $cfg['menu']; }
		
		$sql = "SELECT * 
				FROM cms_menu 
				WHERE menu='$menu' AND published = 1 AND NSLevel = 1
				ORDER BY NSLeft ASC";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			echo '<table align="center" cellpadding="0" cellspacing="0" border="0"><tr>';
			while ($item=$inDB->fetch_assoc($result)){
				//make link URL
				$link = $item['link'];
								
				if ($item['id']!=$menuid){
					$link = '<td class="menutd"><a target="'.$target.'" class="menulink" href="'.$link.'" >'.$item['title'].'</a></td>';	
				} else {
					$link = '<td class="menutd_active"><a target="'.$target.'" class="menulink_active" href="'.$link.'">'.$item['title'].'</a></td>';					
				}
				
				echo $link."\n";
				
			}
			echo '</tr></table>';
		}
		return true;	
	}
?>