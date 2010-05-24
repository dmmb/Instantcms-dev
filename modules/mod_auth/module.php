<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_auth($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();

        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);

		if ($inUser->id){ return false; }
		
        $smarty = $inCore->initSmarty('modules', 'mod_auth.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('LANG', $_LANG);
        $smarty->display('mod_auth.tpl');
		
		return true;
	}
?>