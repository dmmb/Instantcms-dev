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
		$items = array();
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){
				$items[] = $item;
				}
		}
		$smarty = $inCore->initSmarty('modules', 'mod_hmenu.tpl');			
		$smarty->assign('items', $items);
		$smarty->assign('menuid', $menuid);
		$smarty->display('mod_hmenu.tpl');	
		return true;	
	}
?>