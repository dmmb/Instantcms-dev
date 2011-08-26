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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function actions(){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	$inPage = cmsPage::getInstance();

    $inCore->loadModel('actions');
    $model = new cms_model_actions();

    $inCore->loadClass('actions');
    $inActions = cmsActions::getInstance();

    global $_LANG;

    $do   = $inCore->request('do', 'str', 'view');
	$page = $inCore->request('page', 'int', 1);

//======================================================================================================================//

    if ($do=='delete'){

        $id = $inCore->request('id', 'int', 0);

        if (!$id) { cmsCore::error404(); }

        if (!$inUser->is_admin) { cmsCore::error404(); }
        
        $model->deleteAction($id);
        $inCore->redirectBack();

    }

//======================================================================================================================//

    if ($do=='view'){

		$inPage->setTitle($_LANG['FEED_EVENTS']);
		$inPage->addPathway($_LANG['FEED_EVENTS']);

        $inActions->showTargets($model->config['show_target']);

		if($model->config['act_type'] && !$model->config['is_all']){
        	$inActions->onlySelectedTypes($model->config['act_type']);
		}

		$total = $inActions->getCountActions();

        $inActions->limitPage($page, $model->config['perpage']);

        $actions = $inActions->getActionsLog();

        $smarty = $inCore->initSmarty('components', 'com_actions_view.tpl');
        $smarty->assign('actions', $actions);
		$smarty->assign('total', $total);
		$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $model->config['perpage'], '/actions/page-%page%'));
        $smarty->display('com_actions_view.tpl');


    }

//======================================================================================================================//

} //function
?>