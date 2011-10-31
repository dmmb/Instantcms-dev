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

	function mod_menu($module_id){

        $inCore      = cmsCore::getInstance();
        $inDB        = cmsDatabase::getInstance();
        $inUser      = cmsUser::getInstance();
		$nested_sets = $inCore->nestedSetsInit('cms_menu');
		$menuid      = $inCore->menuId();
		$cfg         = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['menu'])) { $menu = 'mainmenu'; } else { $menu = $cfg['menu']; }
		if (!isset($cfg['show_home'])) { $cfg['show_home'] = 1; }
		if (!isset($cfg['is_sub_menu'])) { $cfg['is_sub_menu'] = 0; }

		// “екущий пункт меню
		$currentmenu = $inDB->get_fields('cms_menu', "id = '$menuid'", 'NSLeft, NSRight, NSLevel');

		// id корн€ меню если обычный вывод меню, $menuid если режим подменю
		if($cfg['is_sub_menu']){
			// в подменю не должно быть ссылки на главную
			$cfg['show_home'] = 0;
			$rs_rows = $menuid != 1 ? $nested_sets->SelectSubNodes($menuid) : false;
		} else {
			$root_id = $inDB->get_field('cms_menu', 'parent_id=0', 'id');
			$rs_rows = $nested_sets->SelectSubNodes($root_id);
		}

		if(!$rs_rows) { return false; }
		if(!$inDB->num_rows($rs_rows)) { return false; }

        $items       = array();
        
		while ($row = $inDB->fetch_assoc($rs_rows)){
			if ($row['menu'] == $menu && $inCore->checkContentAccess($row['access_list']) && $row['published']){
                $row['url'] = $inCore->menuSeoLink($row['link'], $row['linktype'], $row['id']);
                $items[]    = $row;
            }
        }

		if(!$items) { return false; }

        $template = ($cfg['tpl'] ? $cfg['tpl'] : 'mod_menu.tpl');

        $smarty = $inCore->initSmarty('modules', $template);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('currentmenu', $currentmenu);
        $smarty->assign('menu', $menu);
        $smarty->assign('items', $items);
        $smarty->assign('last_level', 0);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('root_id', $root_id);
        $smarty->assign('cfg', $cfg);
        $smarty->display($template);
	
		return true;
	
	}

?>