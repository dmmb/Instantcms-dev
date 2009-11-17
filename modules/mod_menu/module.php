<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_menu($module_id){

        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        $inUser     = cmsUser::getInstance();
		$menuid     = $inCore->menuId();
		$cfg        = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['menu'])) { $menu = 'mainmenu'; } else { $menu = $cfg['menu']; }

		$sql         = "SELECT NSLeft, NSRight, NSLevel FROM cms_menu WHERE id = $menuid";
		$result      = $inDB->query($sql);
		$currentmenu = $inDB->fetch_assoc($result);

		$root_id     = dbGetField('cms_menu', 'parent_id=0', 'id');

		$nested_sets = $inCore->nestedSetsInit('cms_menu');
		$rs_rows     = $nested_sets->SelectSubNodes($root_id);

        $items       = array();

		while ($row = $inDB->fetch_assoc($rs_rows)){
			if ($row['menu'] == $menu){
                $item               = $row;
                $item['url']        = $inCore->menuSeoLink($row['link'], $row['linktype'], $row['id']);

                if (!$item['iconurl']) {
                    $item['fileicon']   = '/includes/jquery/treeview/images/file.gif';
                    $item['foldericon'] = '/includes/jquery/treeview/images/folder-closed.gif';
                } else {
                    $item['fileicon']   = '/images/menuicons/'.$item['iconurl'];
                    $item['foldericon'] = '/images/menuicons/'.$item['iconurl'];
                }

                if ($menuid != $item['id']){
                    $item['link'] = '<a target="'.$item['target'].'" class="" href="'.$item['url'].'" >'.$item['title'].'</a>';
                } else {
                    $item['link'] = $item['title'];
                }

                $items[]            = $item;
            }
        }

        $smarty = $inCore->initSmarty('modules', 'mod_menu.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('currentmenu', $currentmenu);
        $smarty->assign('menu', $menu);
        $smarty->assign('items', $items);
        $smarty->assign('last_level', -1);
        $smarty->assign('hide_parent', 0);
        $smarty->assign('user_id', $inUser->id);
        $smarty->assign('user_group', $inUser->group_id);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('root_id', $root_id);
        $smarty->assign('cfg', $cfg);
        $smarty->display('mod_menu.tpl');
	
		return true;
	
	}

?>