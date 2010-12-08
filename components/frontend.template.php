<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by                      2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function my_component(){

    $inCore = cmsCore::getInstance();       //ядро
    $inPage = cmsPage::getInstance();       //страница
    $inDB   = cmsDatabase::getInstance();   //база данных

	$cfg    = сmsLoadComponentConfig('my_component');
	
	$id = $inCore->request('id', 'int', 0);
	$do = $inCore->request('do', 'str', 'view');

    //Подключаем JS к странице
	$inPage->addHeadJS('components/my_component/js/common.js');

    //Подключаем CSS к странице
	$inPage->addHeadCSS('components/my_component/css/styles.css');

//============================================================================//
	if ($do=='view'){

		$inPage->printHeading('ЗАГОЛОВОК КОМПОНЕНТА');
			
		$smarty = $inCore->initSmarty('components', 'com_mycomponent_view.tpl');			
		$smarty->display('com_mycomponent_view.tpl');
        
	}
//============================================================================//

} //end of component
?>