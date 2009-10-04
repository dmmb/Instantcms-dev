<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by                      2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function my_component(){

    $inCore = cmsCore::getInstance();
    $inCore = cmsPage::getInstance();

	$menuid = $inCore->menuId();
	$cfg = ЯmsLoadComponentConfig('my_component');
	
	$id = $inCore->request('id', 'int', 0);
	$do = $inCore->request('do', 'str', 'view');

	$inPage->addHeadCSS('components/my_component/css/styles.css');
	$inPage->addHeadJS('components/my_component/js/common.js');

	if ($do=='view'){
		$inPage->printHeading('гюцнкнбнй йнлонмемрю');		
			
		$smarty = $inCore->initSmarty('components', 'com_mycomponent_view.tpl');			
		$smarty->assign('menuid', $menuid);
		$smarty->display('com_mycomponent_view.tpl');		
	}

} //end of component
?>