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

function board(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    global $_LANG;

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    $inCore->loadModel('board');
    $model = new cms_model_board();

	// Проверяем включен ли компонент
	if(!$model->config['component_enabled']) { cmsCore::error404(); }

	$do = $inCore->request('do', 'str', 'view');

	if ($inCore->menuId()==0){
        $inPage->addPathway($_LANG['BOARD'], '/board');
	}

/////////////////////////////// VIEW CATEGORY ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){ 

	//Получаем текущую категорию
	$category = $model->getCategory($model->category_id);
	if (!$category) { cmsCore::error404(); }

	// Заголовки страницы	
	if ($category['id'] == $model->root_cat['id']){

		$pagetitle = $inCore->menuTitle();
		$pagetitle = $pagetitle ? $pagetitle : $_LANG['BOARD'];

	}
    if ($category['id'] != $model->root_cat['id']) {

		$pagetitle = $category['title'];		

        $category_path  = $model->getCategoryPath($category['NSLeft'], $category['NSRight']);
		if($category_path){
			foreach($category_path as $pcat){
				$inPage->addPathway($pcat['title'], '/board/'.$pcat['id']);
			}
		}

	}

	//Формируем категории
	$cats = $model->getSubCats($category['id']);

	// Формируем список объявлений
	// Устанавливаем категорию
	if ($category['id'] != $model->root_cat['id']) {
		$model->whereCatIs($category['id']);
	}

	//Город
	if ($model->city && in_array($model->city, $category['cat_city'])) {
    	$model->whereCityIs($model->city);
		$pagetitle .= ' :: '.$model->city;
	}

    // Типы объявлений
	if ($model->obtype && stristr($category['obtypes'], $model->obtype)) {
    	$model->whereTypeIs($model->obtype);
		$pagetitle .= ' :: '.$model->obtype;
	}

	// Проставляем заголовки страницы и описание согласно выборки
	$inPage->setDescription($pagetitle);
	$inPage->setTitle($pagetitle);

	// модератор или админ
	$is_moder = $inUser->is_admin || $inCore->isUserCan('board/moderate');

    // Общее количество объявлений по заданным выше условиям
    $total = $model->getAdvertsCount($is_moder);

    //устанавливаем сортировку
	$orderby = $model->getOrder('orderby', $category['orderby']); 
	$orderto = $model->getOrder('orderto', $category['orderto']);
    if (!$orderby) { $orderby = 'id'; }
    if (!$orderto) { $orderto = 'DESC'; }
    $model->orderBy($orderby, $orderto);

    //устанавливаем номер текущей страницы и кол-во объявлений на странице
    $model->limitPage($model->page, $category['perpage']);

	// Получаем объявления
	$items = $model->getAdverts($is_moder, true);

    // Отдаем в шаблон категории
	$smarty = $inCore->initSmarty('components', 'com_board_cats.tpl');			
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('cats', $cats);
	$smarty->assign('cat', $category);
	$smarty->assign('root_id', $model->root_cat['id']);
    $smarty->assign('is_user', $inUser->id);
	$smarty->assign('maxcols', $model->config['maxcols']);
	$smarty->display('com_board_cats.tpl');

	// Отдаем в шаблон объявления
    $smarty = $inCore->initSmarty('components', 'com_board_items.tpl');
    // Если необходимо, отдаем в шаблон html формы сортировки
    if ($category['orderform']){
		 $smarty->assign('order_form', $model->orderForm($orderby, $orderto, $category));
    }

	$pagebar = ($category['id'] != $model->root_cat['id']) ? cmsPage::getPagebar($total, $model->page, $category['perpage'], '/board/%catid%-%page%', array('catid'=>$category['id'])) : false;

    $smarty->assign('cfg', $model->config);
    $smarty->assign('root_id', $model->root_cat['id']);
    $smarty->assign('items', $items);
	$smarty->assign('cat', $category);
    $smarty->assign('maxcols', $category['maxcols']);
    $smarty->assign('colwidth', round(100/$category['maxcols']));
    $smarty->assign('pagebar', $pagebar);
    $smarty->display('com_board_items.tpl');
						
}
/////////////////////////////// VIEW ITEM ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='read'){

	$item = $model->getRecord($model->item_id);
	if (!$item){ cmsCore::error404(); }

	if (!$item['published'] && !$item['moderator']) { cmsCore::error404(); } 

	if (!$item['published']) {
		$info_text = $item['is_overdue'] ? $_LANG['ADV_IS_EXTEND'] : $_LANG['ADV_IS_MODER'];
		cmsCore::addSessionMessage($info_text, 'info');
	} else {
		$model->increaseHits($model->item_id);
	}

	$item['title']   = $item['obtype'].' '.$item['title'];
	$item['content'] = nl2br($item['content']);
	$item['content'] = $model->config['auto_link'] ? $inCore->parseSmiles($item['content']) : $item['content'];

	$category_path = $model->getCategoryPath($item['NSLeft'], $item['NSRight']);
	if($category_path){
		foreach($category_path as $pcat){
			$inPage->addPathway($pcat['title'], '/board/'.$pcat['id']);
		}
	}
	$inPage->addPathway($item['title']);
	$inPage->setTitle($item['title']);
	$inPage->setDescription($item['title']);

	$smarty = $inCore->initSmarty('components', 'com_board_item.tpl');						
	$smarty->assign('item', $item);
	$smarty->assign('cfg', $model->config);
	$smarty->assign('user_id', $inUser->id);
	$smarty->display('com_board_item.tpl');
        
}
/////////////////////////////// NEW BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='additem'){

	$inPage->addPathway($_LANG['ADD_ADV']);

    if ( !$inCore->inRequest('submit') ) {

        if (IS_BILLING) { cmsBilling::checkBalance('board', 'add_item'); }
        $inPage->setTitle($_LANG['ADD_ADV']);

		$item = cmsUser::sessionGet('item');
		if ($item) { cmsUser::sessionDel('item'); }

		$item['city'] = $item['city'] ? $item['city'] : $inDB->get_field('cms_user_profiles', 'id='.$inUser->id, 'city');

        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/add.html");
        $smarty->assign('form_do', 'add');
        $smarty->assign('cfg', $model->config);
		$smarty->assign('cat', $cat);
		$smarty->assign('item', $item);
		$smarty->assign('pagetitle', $_LANG['ADD_ADV']);
        $smarty->assign('cities', $model->getBoardCities($item['city']));
        $smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('is_user', $inUser->id);
        $smarty->assign('catslist', $inCore->getListItemsNS('cms_board_cats', $model->category_id));
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->display('com_board_edit.tpl');
        return;

    }

    if ( $inCore->inRequest('submit') ) {

		// проверяем на заполненость скрытое поле
		$title_fake = $inCore->request('title_fake', 'str', '');
		// если оно заполнено, считаем что это бот, 404
		if ($title_fake) { cmsCore::error404(); }

		$errors = false;

		// проверяем наличие категории
		$cat = $model->getCategory($model->category_id);
		if (!$cat) { cmsCore::addSessionMessage($_LANG['NEED_CAT_ADV'], 'error'); $errors = true; }

		// Проверяем количество добавленных за сутки
		if (!$model->checkLoadedByUser24h($cat)){       
			cmsCore::addSessionMessage($_LANG['MAX_VALUE_OF_ADD_ADV'], 'error'); $errors = true;
		}
		// Можем ли добавлять в эту рубрику
		if (!$cat['public'] && !$inUser->is_admin){
			cmsCore::addSessionMessage($_LANG['YOU_CANT_ADD_ADV'], 'error'); $errors = true;
		}

        // входные данные
        $obtype     = $inCore->request('obtype', 'str', '');
        $title      = $inCore->request('title', 'str', '');
		$title      = str_ireplace($obtype, '', $title);
		$title      = trim($title);
        $content 	= $inCore->request('content', 'str', '');
        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = ($city && $city!='all') ? $city : $city_ed;

        $vipdays    = $inCore->request('vipdays', 'int', 0);

        $published  = 0;

        $published  = ($cat['public']==2 && $inCore->isUserCan('board/autoadd')) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/moderate')) { $published = 1; }

        if ($model->config['srok']){  $pubdays = ($inCore->request('pubdays', 'int') <= 50) ? $inCore->request('pubdays', 'int') : 50; }
        if (!$model->config['srok']){ $pubdays = isset($model->config['pubdays']) ? $model->config['pubdays'] : 14; }

        if (!$title) 	 { cmsCore::addSessionMessage($_LANG['NEED_TITLE'], 'error'); $errors = true; }
        if (!$content) { cmsCore::addSessionMessage($_LANG['NEED_TEXT_ADV'], 'error'); $errors = true; }
        if (!$city)    { cmsCore::addSessionMessage($_LANG['NEED_CITY'], 'error'); $errors = true; }
		if (!$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { cmsCore::addSessionMessage($_LANG['ERR_CAPTCHA'], 'error'); $errors = true; }

        if ($errors){
			$item['content'] = htmlspecialchars(stripslashes($_REQUEST['content']));
			$item['city']    = stripslashes($city);
			$item['title']   = stripslashes($title);
			$item['obtype']  = $obtype;
			cmsUser::sessionPut('item', $item);
			$inCore->redirect('/board/'.$model->category_id.'/add.html');
        }

		// Загружаем фото
        $file = $model->uploadPhoto('', $cat);

        $item_id = $model->addRecord(array(
                                    'category_id'=>$model->category_id,
                                    'user_id'=>$inUser->id,
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
                                    'city'=>$city,
                                    'pubdays'=>$pubdays,
                                    'published'=>$published,
                                    'file'=>$file['filename']
                                ));

        if ($inUser->is_admin && $vipdays){
            $model->setVip($item_id, $vipdays);
        }

        if (IS_BILLING) {
            cmsBilling::process('board', 'add_item');
            if ($model->config['vip_enabled'] && $vipdays && $model->config['vip_day_cost']){
                if ($vipdays > $model->config['vip_max_days']) { $vipdays = $model->config['vip_max_days']; }
                $summ = $vipdays * $model->config['vip_day_cost'];
                if ($inUser->balance >= $summ){
                    cmsBilling::pay($inUser->id, $summ, $_LANG['VIP_BUY_LOG']);
                    $model->setVip($item_id, $vipdays);
                }                
            }
        }

		unset($_SESSION['icms']);

		if ($published) {
			//регистрируем событие
			cmsActions::log('add_board', array(
						'object' => $obtype.' '.$title,
						'object_url' => '/board/read'.$item_id.'.html',
						'object_id' => $item_id,
						'target' => $cat['title'],
						'target_url' => '/board/'.$cat['id'],
						'target_id' => $cat['id'], 
						'description' => ''
			));
			cmsCore::addSessionMessage($_LANG['ADV_IS_ADDED'], 'info');
			$inCore->redirect('/board/read'.$item_id.'.html');
		}

		if (!$published) {
			cmsCore::addSessionMessage($_LANG['ADV_IS_ADDED'].'<br>'.$_LANG['ADV_PREMODER_TEXT'], 'info');
			$inCore->redirect('/board/'.$model->category_id);
		}

    }
	
}
/////////////////////////////// EDIT BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='edititem'){

    $item = $model->getRecord($model->item_id);
    $cat  = $model->getCategory($item['category_id']);
	if (!$cat) { cmsCore::error404(); }
	if (!$item) { cmsCore::error404(); }

    $inPage->setTitle($_LANG['EDIT_ADV']);
    $inPage->addPathway($item['category'], '/board/'.$item['cat_id']);
    $inPage->addPathway($_LANG['EDIT_ADV']);

	if (!$item['moderator']){
		cmsCore::addSessionMessage($_LANG['YOU_HAVENT_ACCESS'], 'error');
		$inCore->redirect('/board/read'.$item['id'].'.html');
	}

    if (!$inCore->inRequest('submit')){

        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/edit{$item['id']}.html");
        $smarty->assign('form_do', 'edit');
        $smarty->assign('cfg', $model->config);
        $smarty->assign('cat', $cat);
        $smarty->assign('obtypes', $model->getTypesOptions($cat['obtypes'], $item['obtype']));
        $smarty->assign('cities', $model->getBoardCities($item['city'], $cat));
        $smarty->assign('item', $item);
		$smarty->assign('pagetitle', $_LANG['EDIT_ADV']);
        $smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->assign('catslist',  $inCore->getListItemsNS('cms_board_cats'));
        $smarty->display('com_board_edit.tpl');
    }

    if ($inCore->inRequest('submit')){

        $obtype     = $inCore->request('obtype', 'str');
        $title      = $inCore->request('title', 'str', '');
		$title      = str_ireplace($obtype, '', $title);
		$title      = trim($title);
        $content 	= $inCore->request('content', 'str', '');
        $vipdays    = $inCore->request('vipdays', 'int', 0);

        $new_cat_id = $inCore->request('category_id', 'int', 0);
        if ($new_cat_id){ $item['category_id'] = $new_cat_id; }

        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = ($city && $city!='all') ? $city : $city_ed;

        $published  = ($cat['public']==2 && $inCore->isUserCan('board/autoadd')) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/moderate')) { $published = 1; }

		if ($item['is_overdue'] && !$item['published']) {
			if ($model->config['srok']){
				$pubdays = ($inCore->request('pubdays', 'int') <= 50) ? $inCore->request('pubdays', 'int') : 50;
			}
        	if (!$model->config['srok']){
				$pubdays = isset($model->config['pubdays']) ? $model->config['pubdays'] : 14;
			}
			$pubdate = date("Y-m-d H:i:s");
		} else {
			$pubdays = $item['pubdays'];
			$pubdate = $item['fpubdate'];
		}

        if (!$title) { cmsCore::addSessionMessage($_LANG['NEED_TITLE'], 'error'); $errors = true; }
        if (!$content) { cmsCore::addSessionMessage($_LANG['NEED_TEXT_ADV'], 'error'); $errors = true; }
        if (!$city)    { cmsCore::addSessionMessage($_LANG['NEED_CITY'], 'error'); $errors = true; }

		if ($errors){ $inCore->redirect('/board/edit'.$item['id'].'.html'); }

		// Загружаем фото
        $file = $model->uploadPhoto($item['file'], $cat);
		$file['filename'] = $file['filename'] ? $file['filename'] : $item['file'];

        $model->updateRecord($model->item_id, array(
                                    'category_id'=>$item['category_id'],
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
                                    'city'=>$city,
									'pubdate'=>$pubdate,
									'pubdays'=>$pubdays,
                                    'published'=>$published,
                                    'file'=>$file['filename']
                                ));

		cmsActions::updateLog('add_board', array('object' => $obtype.' '.$title), $item['id']);

        if ($inUser->is_admin && $vipdays){
            $model->setVip($item['id'], $vipdays);
        }

        if (IS_BILLING) {
            if ($model->config['vip_enabled'] && $model->config['vip_prolong'] && $vipdays && $model->config['vip_day_cost']){
                if ($vipdays > $model->config['vip_max_days']) { $vipdays = $model->config['vip_max_days']; }
                $summ = $vipdays * $model->config['vip_day_cost'];
                if ($inUser->balance >= $summ){
                    cmsBilling::pay($inUser->id, $summ, $_LANG['VIP_BUY_LOG']);
                    $model->setVip($item['id'], $vipdays);
                }
            }
        }

        //finish
		if (!$published) { $prmoder = '<p>'.$_LANG['ADV_EDIT_PREMODER_TEXT'].'</p>'; }
		cmsCore::addSessionMessage('<p><strong>'.$_LANG['ADV_MODIFIED'].'</strong></p>'.$prmoder, 'info');
		$inCore->redirect('/board/read'.$item['id'].'.html');

    }
}
/////////////////////////////// DELETE BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do == 'delete'){

	$item = $model->getRecord($model->item_id);
    if (!$item){ cmsCore::error404(); }

	if (!$item['moderator']){
		cmsCore::addSessionMessage($_LANG['YOU_HAVENT_ACCESS'], 'error');
		$inCore->redirect('/board/'.$item['cat_id']);  
	}

	if (!$inCore->inRequest('godelete')){

		$inPage->setTitle($_LANG['DELETE_ADV']);
		$inPage->addPathway($item['category'], '/board/'.$item['cat_id']);
		$inPage->addPathway($_LANG['DELETE_ADV']);

		$confirm['title']               = $_LANG['DELETING_ADV'];
		$confirm['text']                = $_LANG['YOU_SURE_DELETE_ADV'].' "'.$item['title'].'"?';
		$confirm['action']              = $_SERVER['REQUEST_URI'];
		$confirm['yes_button']['name']  = 'godelete';

		$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
		$smarty->assign('confirm', $confirm);
		$smarty->display('action_confirm.tpl');
	}

	if ($inCore->inRequest('godelete')){
		$model->deleteRecord($model->item_id);
		cmsCore::addSessionMessage($_LANG['ADV_IS_DELETED'], 'info');
		$inCore->redirect('/board/'.$item['cat_id']);
	}

}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
$inCore->executePluginRoute($do);
} //function
?>