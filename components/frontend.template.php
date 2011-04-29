<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by                                               //
//                produced by                                                 //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function my_component(){

    $inCore = cmsCore::getInstance();       //����
    $inPage = cmsPage::getInstance();       //��������
    $inDB   = cmsDatabase::getInstance();   //���� ������

	$cfg    = �msLoadComponentConfig('my_component');
	
	$id = $inCore->request('id', 'int', 0);
	$do = $inCore->request('do', 'str', 'view');

    //���������� JS � ��������
	$inPage->addHeadJS('components/my_component/js/common.js');

    //���������� CSS � ��������
	$inPage->addHeadCSS('components/my_component/css/styles.css');

//============================================================================//
	if ($do=='view'){

		$inPage->printHeading('��������� ����������');
			
		$smarty = $inCore->initSmarty('components', 'com_mycomponent_view.tpl');			
		$smarty->display('com_mycomponent_view.tpl');
        
	}
//============================================================================//

} //end of component
?>