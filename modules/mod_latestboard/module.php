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

function mod_latestboard($module_id){	

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);

		$inCore->loadModel('board');
		$model = new cms_model_board();

        global $_LANG;

		if (!isset($cfg['shownum'])){ $cfg['shownum'] = 5; }
		if (!isset($cfg['onlyvip'])){ $cfg['onlyvip'] = 0; }
		
		if ($cfg['cat_id'] != '-1') {
			if (!$cfg['subs']){
				$model->whereCatIs($cfg['cat_id']);
			} else {
				$cat = $inDB->get_fields('cms_board_cats', "id='{$cfg['cat_id']}'", 'NSLeft, NSRight');
				if(!$cat) { return false; }
				$model->whereThisAndNestedCats($cat['NSLeft'], $cat['NSRight']);
			}		
		}
		// ������ ���
		if($cfg['onlyvip'] && !$cfg['butvip']){
			$model->whereVip(1);
		}
		// ����� ���
		if($cfg['butvip'] && !$cfg['onlyvip']){
			$model->whereVip(0);
		}
		$model->orderBy('pubdate', 'DESC');
	    $model->limitPage(1, $cfg['shownum']);

		$items = $model->getAdverts(false, true, false, true);

        $smarty = $inCore->initSmarty('modules', 'mod_latestboard.tpl');
        $smarty->assign('items', $items);
        $smarty->assign('cfg', $cfg);
        $smarty->display('mod_latestboard.tpl');
			
		return true;				
}
?>