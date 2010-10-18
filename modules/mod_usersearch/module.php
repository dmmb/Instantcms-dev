<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
function mod_usersearch($module_id){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inPage     = cmsPage::getInstance();

    $cfg        = $inCore->loadModuleConfig($module_id);

    if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0; }
   
    $autocomplete_js = $inPage->getAutocompleteJS('citysearch', 'city', false);

    $smarty = $inCore->initSmarty('modules', 'mod_usersearch.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('autocomplete_js', $autocomplete_js);
    $smarty->display('mod_usersearch.tpl');

    return true;

}
?>