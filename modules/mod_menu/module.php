<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_menu($module_id){

        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        $inUser     = cmsUser::getInstance();
		$menuid     = $inCore->menuId();
		$cfg        = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['menu'])) { $menu = 'mainmenu'; } else { $menu = $cfg['menu']; }
		if (!isset($cfg['show_home'])) { $cfg['show_home'] = 1; }

		$sql         = "SELECT NSLeft, NSRight, NSLevel FROM cms_menu WHERE id = '$menuid'";
		$result      = $inDB->query($sql);
		$currentmenu = $inDB->fetch_assoc($result);

		$root_id     = $inDB->get_field('cms_menu', 'parent_id=0', 'id');

		$nested_sets = $inCore->nestedSetsInit('cms_menu');
		$rs_rows     = $nested_sets->SelectSubNodes($root_id);

        $items       = array();
        
		while ($row = $inDB->fetch_assoc($rs_rows)){
			if ($row['menu'] == $menu){
                $item             = $row;
                $item['url']      = $inCore->menuSeoLink($row['link'], $row['linktype'], $row['id']);
				$item['is_allow'] = $inCore->checkContentAccess($item['access_list']);
                $items[]          = $item;
            }
        }

        $template = ($cfg['tpl'] ? $cfg['tpl'] : 'mod_menu.tpl');

        $smarty = $inCore->initSmarty('modules', $template);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('currentmenu', $currentmenu);
        $smarty->assign('menu', $menu);
        $smarty->assign('items', $items);
        $smarty->assign('last_level', -1);
        $smarty->assign('hide_parent', 0);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('root_id', $root_id);
        $smarty->assign('cfg', $cfg);
        $smarty->display($template);
	
		return true;
	
	}

?>