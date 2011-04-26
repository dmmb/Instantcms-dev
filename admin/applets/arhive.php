<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_arhive(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = 'Архив статей';
	
	$cfg = $inCore->loadComponentConfig('content');
    $inCore->loadModel('content');
    $model = new cms_model_content();

	cpAddPathway('Статьи сайта', 'index.php?view=tree');
	cpAddPathway('Архив статей', 'index.php?view=arhive');
	
	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'cancel.gif';
		$toolmenu[0]['title'] = 'Отмена';
		$toolmenu[0]['link'] = "?view=content";

		$toolmenu[1]['icon'] = 'delete.gif';
		$toolmenu[1]['title'] = 'Удалить выбранные';
		$toolmenu[1]['link'] = "javascript:checkSel('?view=arhive&do=delete&multiple=1');";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';				$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Создан';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '80';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title'] = 'Название';	$fields[2]['field'] = 'title';			$fields[2]['width'] = '';		$fields[2]['link'] = '?view=content&do=edit&id=%id%';
		$fields[2]['filter'] = 15;

		$fields[3]['title'] = 'Раздел';		$fields[3]['field'] = 'category_id';	$fields[3]['width'] = '100';	$fields[3]['filter'] = 1;
		$fields[3]['prc'] = 'cpCatById';	$fields[3]['filterlist'] = cpGetList('cms_category');
				
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'В каталог статей';
		$actions[0]['icon']  = 'arhive_off.gif';
		$actions[0]['link']  = '?view=arhive&do=arhive_off&id=%id%';

		$actions[2]['title'] = 'Удалить';
		$actions[2]['icon']  = 'delete.gif';
		$actions[2]['confirm'] = 'Удалить материал?';
		$actions[2]['link']  = '?view=content&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_content', $fields, $actions, 'is_arhive=1');		
	}
	
	if ($do == 'arhive_off'){
		if(isset($_REQUEST['id'])) { 
			$id = (int)$_REQUEST['id'];
			$sql = "UPDATE cms_content SET is_arhive = 0 WHERE id = $id";
			dbQuery($sql) ;
			header('location:?view=arhive');

		}
	}
	
	if ($do == 'delete'){
		if ($cfg['af_delete']){ include_once($_SERVER['DOCUMENT_ROOT'].'/components/forum/includes/forumcore.php'); }
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){
				$model->deleteArticle($id, $cfg['af_delete']);
			}
		} else {
			$model->deleteArticles($_REQUEST['item'], $cfg['af_delete']);
		}
		header('location:?view=arhive');
	}
	
}

?>