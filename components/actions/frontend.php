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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function actions(){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	$inPage = cmsPage::getInstance();

    $inCore->loadModel('actions');
    $model = new cms_model_actions();

	if(!$model->config['component_enabled']) { cmsCore::error404(); }

    $inCore->loadClass('actions');
    $inActions = cmsActions::getInstance();

    global $_LANG;
	$inCore->loadLanguage('components/users');
	$inCore->includeFile('components/users/includes/usercore.php');

    $do   = $inCore->request('do', 'str', 'view');
	$page = $inCore->request('page', 'int', 1);
	$user_id = $inCore->request('user_id', 'int', 0);

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

    if ($do=='view_user_feed'){

		if(!$inUser->id) { exit; }

		if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { exit; }

		// Получаем друзей
		$friends = usrFriends($inUser->id, $friends_total, 6);

		if($friends_total){

			$inActions->onlyMyFriends();

			$inActions->showTargets($model->config['show_target']);
			$inActions->limitIs($model->config['perpage_tab']);
        	$actions = $inActions->getActionsLog();
			// получаем первый элемент массива для выборки оттуда имя пользователя и ссылку на профиль.
		}

		$total_pages = ceil($friends_total / 6);

        $smarty = $inCore->initSmarty('components', 'com_actions_view_tab.tpl');
        $smarty->assign('actions', $actions);
		$smarty->assign('friends', $friends);
		$smarty->assign('user_id', $user_id);
		$smarty->assign('page', $page);
		$smarty->assign('cfg', $model->config);
		$smarty->assign('total_pages', $total_pages);
		$smarty->assign('friends_total', $friends_total);
        $smarty->display('com_actions_view_tab.tpl');
		echo ob_get_clean(); exit;

    }
//======================================================================================================================//
    if ($do=='view_user_feed_only'){

		if(!$inUser->id) { exit; }

		if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { exit; }

		if($user_id){
			if(!usrIsFriends($user_id, $inUser->id)) { exit; }
			$inActions->whereUserIs($user_id);
		} else {
			$inActions->onlyMyFriends();
		}

		$inActions->showTargets($model->config['show_target']);
		$inActions->limitIs($model->config['perpage_tab']);
		$actions = $inActions->getActionsLog();
		// получаем последний элемент массива для выборки имя пользователя и ссылки на профиль.
		$user = end($actions);

        $smarty = $inCore->initSmarty('components', 'com_actions_tab.tpl');
        $smarty->assign('actions', $actions);
		$smarty->assign('user_id', $user_id);
		$smarty->assign('user', $user);
		$smarty->assign('cfg', $model->config);
        $smarty->display('com_actions_tab.tpl');
		echo ob_get_clean(); exit;

    }
//======================================================================================================================//
    if ($do=='view_user_friends_only'){

		if(!$inUser->id) { exit; }

		if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { exit; }

		// Получаем друзей
		$friends = usrFriends($inUser->id, $friends_total, 6, $page);

		$total_pages = ceil($friends_total / 6);

        $smarty = $inCore->initSmarty('components', 'com_actions_friends.tpl');
		$smarty->assign('friends', $friends);
		$smarty->assign('page', $page);
		$smarty->assign('user_id', $user_id);
		$smarty->assign('total_pages', $total_pages);
		$smarty->assign('friends_total', $friends_total);
        $smarty->display('com_actions_friends.tpl');
		echo ob_get_clean(); exit;

    }
//======================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>