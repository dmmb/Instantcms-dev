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

	function mod_search(){

        $inCore = cmsCore::getInstance();
	    $inCore->loadModel('search');
		cmsCore::loadLanguage('components/search');
		$model = cms_model_search::initModel();
		if(!$model->config['component_enabled']) { return false; }

		$smarty = $inCore->initSmarty('modules', 'mod_search.tpl');
		$smarty->assign('enable_components', $model->getEnableComponentsWithSupportSearch());
		$smarty->display('mod_search.tpl');

		return true;	

	}
?>