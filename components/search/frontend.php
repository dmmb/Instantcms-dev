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

function search(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();

    global $_LANG;

	$menuid = $inCore->menuId();

    $inCore->loadModel('search');
    $model = cms_model_search::initModel();

	// Проверяем включен ли компонент
	if(!$model->config['component_enabled']) { cmsCore::error404(); }

	// тип поиска
    $mode = $inCore->request('mode', 'str', 'text');

	$inPage->setTitle($_LANG['SEARCH']);
	if ($menuid==0){
        $inPage->addPathway($_LANG['SEARCH'], '/search');
	}
	
/* ==================================================================================================== */
/* ==================================================================================================== */
	if ($mode == 'text'){

		if (strlen($model->query)<=3 && strlen($model->query)>=1){ 
			cmsCore::addSessionMessage($_LANG['ERROR'].': '.$_LANG['SHORT_QUERY'], 'error');
			$inCore->redirect('/search');
		}

		if($model->query){
			$inPage->addPathway($model->query);
		}

		// если параметры запроса изменились
		// делаем полный поиск, заполняя кеш
		// иначе берем из кеша результаты
		if(!$model->isChangedParams()){

			// Удаляем записи поиска от текущей сессии
			$model->deleteResultsFromThisSession();
	
			// Готовим поиск
			// выполняется поиск по индексу фултекст
			if(!$model->prepareSearch()) { cmsCore::error404(); }
	
			// Кладем в сессию текущие параметры запроса
			cmsUser::sessionPut('query_params', $model->parametrs_array);
			// кладем в сессию слова запроса
			cmsUser::sessionPut('searchquery', $model->words);

		}

		// формируем условия выборки
		$model->whereSessionIs(session_id());
		$model->wherePeriodIs();
		if($model->order_by_date){
			$model->orderBy('pubdate', 'DESC');
		} else {
			$model->orderBy();
		}

		// Получаем общее количество результатов
		$total = $model->getCountResults();

		// Получаем сами результаты поиска
		if($total){
			$results = $model->getResults();
		}

		$smarty = $inCore->initSmarty('components', 'com_search_text.tpl');
		$smarty->assign('query', $model->query);
		$smarty->assign('look', $model->look);
		$smarty->assign('order_by_date', $model->order_by_date);
		$smarty->assign('from_pubdate', $model->from_pubdate);
		$smarty->assign('results', $results);
		$smarty->assign('total', $total);
		$smarty->assign('enable_components', $model->getEnableComponentsWithSupportSearch());
		$smarty->assign('from_component', $model->from_component);
		$smarty->assign('external_link', str_replace('%q%', urlencode($model->query), $_LANG['FIND_EXTERNAL_URL']));
		$smarty->assign('host', HOST);
		$smarty->assign('pagebar', cmsPage::getPagebar($total, $model->page, $model->config['perpage'], 'javascript:paginator(%page%)'));
		$smarty->display('com_search_text.tpl');

	}

/* ==================================================================================================== */
/* ==================================================================================================== */
	if ($mode == 'tag'){

		if (strlen($model->query)<=3 && strlen($model->query)>=1){ 
			cmsCore::addSessionMessage($_LANG['EMPTY_QUERY'], 'error');
			$inCore->redirect('/search');
		}

		$inPage->setTitle($_LANG['SEARCH_BY_TAG'].' "'.$model->query.'"');

		if($model->query){
			$inPage->addPathway($_LANG['SEARCH_BY_TAG'].' "'.$model->query.'"');
		}
		$inPage->initAutocomplete();

		$total   = $model->getCountTags();

		$results = $model->searchByTag();

		$smarty = $inCore->initSmarty('components', 'com_search_tag.tpl');
		$smarty->assign('query', $model->query);
		$smarty->assign('results', $results);
		$smarty->assign('total', $total);
		$smarty->assign('autocomplete_js', $inPage->getAutocompleteJS('tagsearch', 'query', false));
		$smarty->assign('external_link', '/index.php?view=search&query='.urlencode($model->query).'&look=allwords');
		$smarty->assign('pagebar', cmsPage::getPagebar($total, $model->page, $model->config['perpage'], '/search/tag/'.urlencode($model->query).'/page%page%.html'));
		$smarty->display('com_search_tag.tpl');

	}
	$inCore->executePluginRoute($mode);
	return true;
}
?>