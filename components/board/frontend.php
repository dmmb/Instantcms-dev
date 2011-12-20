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

	// rss в адресной строке
	$rss_cat_id = $category['id'] == $model->root_cat['id'] ? 'all' : $category['id'];
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.$_LANG['BOARD'].'" href="'.HOST.'/rss/board/'.$rss_cat_id.'/feed.rss">');

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
	$is_moder = $inUser->is_admin || $model->is_moderator_by_group;

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
/////////////////////////////// VIEW USER ADV ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='by_user'){

	// логин пользователя
	$login = $inCore->request('login', 'str', ''.$inUser->login.'');
	// получаем данные пользователя
	$user = cmsUser::getUserBylogin($login);
	if (!$user) { cmsCore::error404(); }

	$myprofile = $model->checkAccess($user['id']);

	$inPage->addPathway($user['nickname']);
    $inPage->setTitle($_LANG['BOARD'].' - '.$user['nickname']);
	$inPage->setDescription($_LANG['BOARD'].' - '.$user['nickname']);

	// Формируем список объявлений
	$model->whereUserIs($user['id']);

    // Общее количество объявлений по заданным выше условиям
    $total = $model->getAdvertsCount($myprofile);

    //устанавливаем сортировку
    $model->orderBy('pubdate', 'DESC');

    //устанавливаем номер текущей страницы и кол-во объявлений на странице
    $model->limitPage($model->page, 15);

	// Получаем объявления
	$items = $model->getAdverts($myprofile, true, false, true);

	// Пагинация
	$pagebar = cmsPage::getPagebar($total, $model->page, 15, '/board/by_user_'.$login.'/page-%page%');

	// Показываем даты
	$category['showdate'] = 1;

	$smarty = $inCore->initSmarty('components', 'com_board_items.tpl');
    $smarty->assign('cfg', $model->config);
	$smarty->assign('page_title', $_LANG['BOARD'].' - '.$user['nickname']);
    $smarty->assign('root_id', $model->root_cat['id']);
    $smarty->assign('items', $items);
	$smarty->assign('cat', $category);
    $smarty->assign('maxcols', 1);
    $smarty->assign('colwidth', 100);
    $smarty->assign('pagebar', $pagebar);
    $smarty->display('com_board_items.tpl');

}
/////////////////////////////// VIEW ITEM ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='read'){

	// получаем объявление
	$item = $model->getRecord($model->item_id);
	if (!$item){ cmsCore::error404(); }

	// неопубликованные показываем админам, модераторам и автору
	if (!$item['published'] && !$item['moderator']) { cmsCore::error404(); } 

	// для неопубликованного показываем инфо: просрочено/на модерации
	if (!$item['published']) {
		$info_text = $item['is_overdue'] ? $_LANG['ADV_IS_EXTEND'] : $_LANG['ADV_IS_MODER'];
		cmsCore::addSessionMessage($info_text, 'info');
	} else {
		$model->increaseHits($model->item_id);
	}

	// формируем заголовок и тело сообщения
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
	$smarty->assign('is_admin', $inUser->is_admin);
	$smarty->assign('formsdata', $model->getFormData($item['form_id'], $item['form_array']));
	$smarty->assign('is_moder', $model->is_moderator_by_group);
	$smarty->display('com_board_item.tpl');
        
}
/////////////////////////////// NEW BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='additem'){

	// Получаем категории, в которые может загружать пользователь
	$catslist = $model->getPublicCats($model->category_id);
	if(!$catslist) { 
		cmsCore::addSessionMessage($_LANG['YOU_CANT_ADD_ADV_ANY'], 'error');
		$inCore->redirect('/board');
	}

	$cat['is_photos'] = 1;
	if ($model->category_id && $model->category_id != $model->root_cat['id']) {
		$cat = $model->getCategory($model->category_id);
	}

	$inPage->addPathway($_LANG['ADD_ADV']);

    if ( !$inCore->inRequest('submit') ) {

        if (IS_BILLING) { cmsBilling::checkBalance('board', 'add_item'); }
        $inPage->setTitle($_LANG['ADD_ADV']);

		$item = cmsUser::sessionGet('item');
		if ($item) { cmsUser::sessionDel('item'); }

		$item['city'] = $item['city'] ? $item['city'] : $inDB->get_field('cms_user_profiles', 'id='.$inUser->id, 'city');
		$item['form_array'] = $item['form_array'] ? $item['form_array'] : array();

        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/add.html");
        $smarty->assign('form_do', 'add');
        $smarty->assign('cfg', $model->config);
		$smarty->assign('cat', $cat);
		$smarty->assign('item', $item);
		$smarty->assign('pagetitle', $_LANG['ADD_ADV']);
        $smarty->assign('cities', $model->getBoardCities($item['city']));
		$smarty->assign('formsdata', $model->getFormDataEdit($cat['form_id'], $item['form_array']));
        $smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('is_user', $inUser->id);
        $smarty->assign('catslist', $catslist);
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->display('com_board_edit.tpl');

		cmsUser::sessionClearAll();
        return;

    }

    if ( $inCore->inRequest('submit') ) {

		// проверяем на заполненость скрытое поле
		$title_fake = $inCore->request('title_fake', 'str', '');
		// если оно заполнено, считаем что это бот, 404
		if ($title_fake) { cmsCore::error404(); }

		$errors = false;

		// проверяем наличие категории
		if (!$cat['id']) { cmsCore::addSessionMessage($_LANG['NEED_CAT_ADV'], 'error'); $errors = true; }

		// Проверяем количество добавленных за сутки
		if (!$model->checkLoadedByUser24h($cat)){       
			cmsCore::addSessionMessage($_LANG['MAX_VALUE_OF_ADD_ADV'], 'error'); $errors = true;
		}
		// Можем ли добавлять в эту рубрику
		if (!$model->checkAdd($cat)){
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

		$form_array = $inCore->request('field', 'array');
		$formsdata  = $inCore->strClear($inCore->arrayToYaml($form_array));

        $vipdays    = $inCore->request('vipdays', 'int', 0);

        $published  = $model->checkPublished($cat);

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
			$item['form_array'] = $form_array;
			cmsUser::sessionPut('item', $item);
			$inCore->redirect('/board/'.$model->category_id.'/add.html');
        }

		if($cat['is_photos']){
			// Загружаем фото
			$file = $model->uploadPhoto('', $cat);
		} else {
			$file['filename'] = '';
			cmsCore::addSessionMessage($_LANG['INFO_CAT_NO_PHOTO'], 'info');
		}

        $item_id = $model->addRecord(array(
                                    'category_id'=>$model->category_id,
                                    'user_id'=>$inUser->id,
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
									'formsdata'=>$formsdata,
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

		cmsUser::sessionClearAll();

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
			cmsCore::addSessionMessage($_LANG['ADV_IS_ADDED'], 'success');
			cmsCore::callEvent('ADD_BOARD_DONE', array('id'=>$item_id));
			$inCore->redirect('/board/read'.$item_id.'.html');
		}

		if (!$published) {

			$link = '<a href="/board/read'.$item_id.'.html">'.$obtype.' '.$title.'</a>';
			if($inUser->id){
				$user = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';
			} else {
				$user = $_LANG['BOARD_GUEST'].', ip: '.$inUser->ip;
			}
			$message = str_replace('%user%', $user, $_LANG['MSG_ADV_SUBMIT']);
			$message = str_replace('%link%', $link, $message);
			cmsUser::sendMessage(USER_UPDATER, 1, $message);

			cmsCore::addSessionMessage($_LANG['ADV_IS_ADDED'].'<br>'.$_LANG['ADV_PREMODER_TEXT'], 'success');
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
        $smarty->assign('cities', $model->getBoardCities($item['city'], $cat));
        $smarty->assign('item', $item);
		$smarty->assign('pagetitle', $_LANG['EDIT_ADV']);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('catslist', $model->getPublicCats($item['category_id'], true));
		$smarty->assign('formsdata', $model->getFormDataEdit($cat['form_id'], $item['form_array']));
		$smarty->assign('is_user', $inUser->id);
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->display('com_board_edit.tpl');

		cmsUser::sessionClearAll();

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

		$form_array = $inCore->request('field', 'array');
		$formsdata  = $inCore->strClear($inCore->arrayToYaml($form_array));

        $published  = $model->checkPublished($cat, true);

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

		if($cat['is_photos']){
			// Загружаем фото
			$file = $model->uploadPhoto($item['file'], $cat);
		}

		$file['filename'] = $file['filename'] ? $file['filename'] : $item['file'];

		// обновляем объявление
        $model->updateRecord($item['id'], array(
                                    'category_id'=>$item['category_id'],
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
									'formsdata'=>$formsdata,
                                    'city'=>$city,
									'pubdate'=>$pubdate,
									'pubdays'=>$pubdays,
                                    'published'=>$published,
                                    'file'=>$file['filename']
                                ));
		// обновляем запись в ленте активности
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

		cmsUser::sessionClearAll();

		if (!$published) {

			$link = '<a href="/board/read'.$item['id'].'.html">'.$obtype.' '.$title.'</a>';
			$user = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';

			$message = str_replace('%user%', $user, $_LANG['MSG_ADV_EDITED']);
			$message = str_replace('%link%', $link, $message);
			cmsUser::sendMessage(USER_UPDATER, 1, $message);

			cmsCore::addSessionMessage($_LANG['ADV_EDIT_PREMODER_TEXT'], 'info');

		}

		cmsCore::addSessionMessage($_LANG['ADV_MODIFIED'], 'success');
		$inCore->redirect('/board/read'.$item['id'].'.html');

    }
}
///////////////////////// PUBLISH BOARD ITEM /////////////////////////////////////////////////////////////////////////////
if ($do == 'publish'){

	$item = $model->getRecord($model->item_id);
    if (!$item){ cmsCore::error404(); }

	// если уже опубликовано, 404
	if ($item['published']) { cmsCore::error404(); }

	// публиковать могут админы и модераторы доски
	if(!$inUser->is_admin && !$model->is_moderator_by_group) { cmsCore::error404(); }

    $model->publishRecord($model->item_id);

	cmsCore::callEvent('ADD_BOARD_DONE', $item);
 
 	if($item['user_id']){
		//регистрируем событие
		cmsActions::log('add_board', array(
					'object' => $item['obtype'].' '.$item['title'],
					'user_id' => $item['user_id'],
					'object_url' => '/board/read'.$item['id'].'.html',
					'object_id' => $item['id'],
					'target' => $item['category'],
					'target_url' => '/board/'.$item['cat_id'],
					'target_id' => $item['cat_id'], 
					'description' => ''
		));

		$link = '<a href="/board/read'.$item['id'].'.html">'.$item['obtype'].' '.$item['title'].'</a>';
		$message = str_replace('%link%', $link, $_LANG['MSG_ADV_ACCEPTED']);
		cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);
	}

	cmsCore::addSessionMessage($_LANG['ADV_IS_ACCEPTED'], 'success');

    $inCore->redirect('/board/read'.$item['id'].'.html');

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
		cmsCore::addSessionMessage($_LANG['ADV_IS_DELETED'], 'success');
		$inCore->redirect('/board/'.$item['cat_id']);
	}

}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
$inCore->executePluginRoute($do);
} //function
?>