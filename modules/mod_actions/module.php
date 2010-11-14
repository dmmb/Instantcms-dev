<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_actions($module_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

        global $_LANG;

        $cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['show_target'])) { $cfg['show_target'] = 0; }
		if (!isset($cfg['limit'])) { $cfg['limit'] = 15; }
        if (!isset($cfg['action_types'])) { echo $_LANG['MODULE_NOT_CONFIGURED']; return true; }

        $inActions = cmsActions::getInstance();

        if (!$cfg['show_target']){ $inActions->showTargets(false); }

        $inActions->onlySelectedTypes($cfg['action_types']);
        $inActions->limitIs($cfg['limit']);

        $actions = $inActions->getActionsLog();

        $smarty = $inCore->initSmarty('modules', 'mod_actions.tpl');
        $smarty->assign('actions', $actions);
        $smarty->display('mod_actions.tpl');
			
		return true;
        
}
?>
