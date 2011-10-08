<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

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