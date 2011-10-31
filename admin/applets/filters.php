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

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_filters(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/plugins', $adminAccess)) { cpAccessDenied(); }
	if (!$inCore->isAdminCan('admin/filters', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = '�������';
 	cpAddPathway('�������', 'index.php?view=filters');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'hide'){
		dbHide('cms_filters', $id); 
		echo '1'; exit;
	}

	if ($do == 'show'){
		dbShow('cms_filters', $id); 
		echo '1'; exit;		
	}

		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[1]['icon'] = 'replace.gif';
		$toolmenu[1]['title'] = '������� ����������';
		$toolmenu[1]['link'] = '?view=frules';
		
//		$toolmenu[2]['icon'] = 'install.gif';
//		$toolmenu[2]['title'] = '���������� ������';
//		$toolmenu[2]['link'] = '?view=install&do=filter';
//
//		cpToolMenu($toolmenu);
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '250';

		$fields[2]['title'] = '��������';	$fields[2]['field'] = 'description';$fields[2]['width'] = '';

		$fields[3]['title'] = '�������';	$fields[3]['field'] = 'published';		$fields[3]['width'] = '100';
		
		//ACTIONS
		$actions = array();
			
		//Print table
		cpListTable('cms_filters', $fields, $actions);		
	}
		
}

?>