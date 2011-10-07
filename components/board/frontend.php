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

function obTypesLinks($cat_id, $types){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$html = '';
	$types = explode("\n", $types);
	$total = sizeof($types); $c = 0;
	foreach($types as $id=>$type){
		$c++;
		$type = trim($type);
		$html .= '<a href="/board/'.$cat_id.'/type/'.urlencode(ucfirst($type)).'">'.ucfirst($type).'</a>';
		if ($c < $total){
			$html .= ', ';
		}
	}			
	return $html;
}

function obTypesOptions($types, $selected=''){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$html = '';
	$types = explode("\n", $types);
	$total = sizeof($types); $c = 0;
	foreach($types as $id=>$type){
		$c++;
		$type = trim($type);
        if ($selected == $type){ $sel = 'selected="selected"'; } else { $sel = ''; }
		$html .= '<option value="'.ucfirst($type).'" '.$sel.'>'.ucfirst($type).'</option>';
		if ($c < $total){
			$html .= ', ';
		}
	}
	return $html;
}

function orderForm($orderby, $orderto, $obtypes){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	if (isset($_SESSION['board_type'])) { $btype = $_SESSION['board_type']; } else { $btype = ''; }
	if (isset($_SESSION['board_city'])) { $bcity = $_SESSION['board_city']; } else { $bcity = ''; }	
	$smarty = $inCore->initSmarty('components', 'com_board_order_form.tpl');				
		$smarty->assign('btype', $btype);
		$smarty->assign('btypes', $inCore->boardTypesList($btype, $obtypes));
		$smarty->assign('bcity', $bcity);
		$smarty->assign('bcities', $inCore->boardCities($bcity));
		$smarty->assign('orderby', $orderby);
		$smarty->assign('orderto', $orderto);		
		$smarty->assign('action_url', $_SERVER['REQUEST_URI']);
	ob_start();
		$smarty->display('com_board_order_form.tpl');
	return ob_get_clean();
}

function loadedByUser24h($user_id, $album_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$sql = "SELECT id FROM cms_board_items WHERE user_id = $user_id AND category_id = $album_id AND pubdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
	$result = $inDB->query($sql) ;
	$loaded = $inDB->num_rows($result);	
	return $loaded;
}

function board(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$cfg        = $inCore->loadComponentConfig('board');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    global $_LANG;

    //DEFAULT VALUES
	if (!isset($cfg['showlat'])) { $cfg['showlat'] = 1; }		
	if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
	if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
	if (!isset($cfg['maxcols'])) { $cfg['maxcols'] = 1; }
	if (!isset($cfg['public'])) { $cfg['public'] = 1; }
	if (!isset($cfg['srok'])) { $cfg['srok'] = 1; }
	if (!isset($cfg['pubdays'])) { $cfg['pubdays'] = 14; }
    if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
    if(!isset($cfg['comments'])) { $cfg['comments'] = 1; }
    if (!isset($cfg['aftertime'])) { $cfg['aftertime'] = ''; }
	if (!isset($cfg['extend'])) { $cfg['extend'] = 0; }
    if (!isset($cfg['vip_enabled'])) { $cfg['vip_enabled'] = 0; }
    if (!isset($cfg['vip_prolong'])) { $cfg['vip_prolong'] = 0; }
    if (!isset($cfg['vip_max_days'])) { $cfg['vip_max_days'] = 30; }
    if (!isset($cfg['vip_day_cost'])) { $cfg['vip_day_cost'] = 5; }

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    $inCore->loadModel('board');
    $model      = new cms_model_board();

	$root       = $model->getRootCategory();
	
	$id         = $inCore->request('id', 'int', $root['id']);
	$userid     = $inCore->request('userid', 'int');
	$do         = $inCore->request('do', 'str', 'view');

	$is_404 = $inCore->request('is_404', 'str', '');
	if ($is_404) { cmsCore::error404(); }

/////////////////////////////// SET CITY ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='city'){ 
	$city = urldecode($inCore->request('city', 'str')); 

	if (!empty($city)){ $_SESSION['board_city'] = $city; } else { unset($_SESSION['board_city']); }
    $inCore->redirectBack();
}

/////////////////////////////// VIEW CATEGORY ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){ 

   	$col        = 1;
    $maxcols    = isset($cfg['maxcols']) ? $cfg['maxcols'] : 1;

	//SHOW CATEGORY LIST
	$category   = $model->getCategory($id);
	if (!$category) { cmsCore::error404(); }
    $perpage    = $category['perpage'] ? $category['perpage'] : 20;
    $page       = $inCore->request('page', 'int', 1);	
    if ( $category['public'] == -1 ) { $category['public'] = $cfg['public']; }

	//PAGE HEADING		
	if ($id == $root['id']){
		$pagetitle  = $inCore->menuTitle();
		if ($pagetitle) { $inPage->setTitle($pagetitle); } 
		if (!$pagetitle) { $inPage->setTitle($_LANG['BOARD']); $pagetitle = $_LANG['BOARD']; }
	}

    if ($id != $root['id']) {
		$pagetitle      =  $category['title'];		

        $left_key       = $category['NSLeft'];
		$right_key      = $category['NSRight'];
        $category_path  = $model->getCategoryPath($left_key, $right_key);
		foreach($category_path as $pcat){
            $inPage->addPathway($pcat['title'], '/board/'.$pcat['id']);
		}        
        
        $inPage->setTitle($pagetitle  . ' - '.$_LANG['BOARD']);
		$inPage->addPathway($category['title']);
	}		
	
	$inPage->printHeading($pagetitle);

	//BUILD SUB-CATS LIST	
	$subcats_list   = $model->getSubCats($id);
    $is_subcats     = (bool)sizeof($subcats_list);

	if ($is_subcats){
		$cats = array();
		foreach($subcats_list as $cat) {
            $sub                = $model->getSubCatsCount($cat['id']);
			$cat['subtext']     = $sub ? '/'.$sub : '';
			$cat['ob_links']    = obTypesLinks($cat['id'], $cat['obtypes']);
            $cats[]             = $cat;
		}
	}

	$smarty = $inCore->initSmarty('components', 'com_board_cats.tpl');			
		$smarty->assign('is_subcats', $is_subcats);
		$smarty->assign('cats', $cats);
		$smarty->assign('maxcols', $maxcols);
	$smarty->display('com_board_cats.tpl');
	
	//SHOW CAT CONTENT

    //ORDERING
    if ($inCore->inRequest('orderby')) {
        $orderby                = $inCore->request('orderby', 'str');
        $_SESSION['ph_orderby'] = $orderby;
    } elseif(isset($_SESSION['ph_orderby'])) {
        $orderby                = $_SESSION['ph_orderby'];
    } else {
        $orderby                = $category['orderby'];
    }
    if ($inCore->inRequest('orderto')) {
        $orderto                = $inCore->request('orderto', 'str');
        $_SESSION['ph_orderto'] = $orderto;
    } elseif(isset($_SESSION['ph_orderto'])) {
        $orderto                = $_SESSION['ph_orderto'];
    } else {
        $orderto                = $category['orderto'];
    }

    if (!$orderby) { $orderby = 'pubdate'; }
    if (!$orderto) { $orderto = 'DESC'; }

    //CITY FILTER
    if ($inCore->inRequest('city')) {
        $city = urldecode($inCore->request('city', 'str'));
        if (!empty($city)){ $_SESSION['board_city'] = $city; } else { unset($_SESSION['board_city']); }
    }

    //OBTYPE FILTER
    if ($inCore->inRequest('obtype')) {
        $obtype = urldecode($inCore->request('obtype', 'str'));
        if (!empty($obtype)){ $_SESSION['board_type'] = $obtype; } else { unset($_SESSION['board_type']); }
    }

    //DISPLAY ORDER FORM
    if ($category['orderform']){
        echo orderForm($orderby, $orderto, $category['obtypes']);
    }

    $items      = array();
    $items_list = $model->getRecords($id, $page, $perpage, $orderby, $orderto);
    $is_items   = (bool)sizeof($items_list);

    //FETCH ITEMS
    if ($is_items){        
        foreach($items_list as $item) {
            $item['enc_city'] = urlencode($item['city']);
            //Check item image
            $file = 'nopic.jpg';
            if ($item['file']){
                if (file_exists(PATH.'/images/board/small/'.$item['file'])){
                    $file = $item['file'];
                }
            }
            $item['file'] = $file;
			$item['content'] = $cfg['auto_link'] ? $inCore->convertToLink($item['content']) : $item['content'];
			$item['title'] = $item['obtype'].' '.$item['title'];
            //Check user access
            if ($inUser->id){
                $item['moderator'] = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
            } else {
                $item['moderator'] = false;
            }
            $items[] = $item;
        }
    }

    $col = 1; $maxcols = $category['maxcols']; $colwidth = round(100/$maxcols);
    $total = $inDB->rows_count('cms_board_items', 'category_id = '.$id.' AND published = 1');
    //DISPLAY
    $smarty = $inCore->initSmarty('components', 'com_board_items.tpl');
        $smarty->assign('is_items', $is_items);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('cat', $category);
        $smarty->assign('root_id', $root['id']);
        $smarty->assign('items', $items);
        $smarty->assign('maxcols', $maxcols);
        $smarty->assign('colwidth', $colwidth);		
        $smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/board/%catid%-%page%', array('catid'=>$id)));
        $smarty->assign('is_user', $inUser->id);
    $smarty->display('com_board_items.tpl');
						
}
/////////////////////////////// VIEW ITEM ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='read'){
	$item   = $model->getRecord($id);

	if (!$item){ cmsCore::error404(); }
	if ($item['published'] != 1) {
		$inPage->printHeading($_LANG['ADV_IS_MODER']);
        return;
	}

	$item['title'] = $item['obtype'].' '.$item['title'];

	//PATHWAY ENTRY
	$pagetitle =  $item['title'];
	$inPage->setTitle($pagetitle  . ' - '.$_LANG['BOARD']);

	//PATHWAY ENTRY
	$left_key       = $item['NSLeft'];
	$right_key      = $item['NSRight'];
	$category_path  = $model->getCategoryPath($left_key, $right_key);
	foreach($category_path as $pcat){
		$inPage->addPathway($pcat['title'], '/board/'.$pcat['id']);
	}
	$inPage->addPathway($item['title']);

	$inPage->setTitle($item['title']);
	$inPage->printHeading($item['title']);

	$model->increaseHits($id);

	//encode city
	$item['enc_city'] = urlencode($item['city']);

	$item['content'] = nl2br($item['content']);
	$item['content'] = $cfg['auto_link'] ? $inCore->convertToLink($item['content']) : $item['content'];

	//Check user access
	if ($inUser->id){
		$moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
	} else {
		$moderator = false;		
	}

	//check photo
	if ($item['file']){
		if (!file_exists(PATH.'/images/board/medium/'.$item['file'])){
			$item['file'] = '';
		}
	}
	
	//DISPLAY
	$smarty = $inCore->initSmarty('components', 'com_board_item.tpl');						
		$smarty->assign('moderator', $moderator);
		$smarty->assign('item', $item);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_user', $inUser->id);
		$smarty->assign('user_id', $inUser->id);
	$smarty->display('com_board_item.tpl');
        
}
/////////////////////////////// NEW BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='additem'){

    if ( !$inUser->id ) { cmsUser::goToLogin();	}

	$inPage->backButton(false);

    $cat = $model->getCategory($id);

    if (!$cat) { cmsCore::error404(); }
	
    if ( $cat['public'] == -1 ) { $cat['public'] = $cfg['public']; }

    $inPage->addPathway($cat['title'], '/board/'.$cat['id']);
	$inPage->addPathway($_LANG['ADD_ADV']);
    $inPage->printHeading($_LANG['ADD_ADV']);

	// Проверяем права доступа
    if ( !(loadedByUser24h($inUser->id, $cat['id'])<$cat['uplimit'] || $cat['uplimit'] == 0) ){       
		cmsCore::addSessionMessage($_LANG['MAX_VALUE_OF_ADD_ADV'], 'error');
		$inCore->redirect('/board/'.$id);      
    }
   
    if ( !$cat['public'] ){
		cmsCore::addSessionMessage($_LANG['YOU_CANT_ADD_ADV'], 'error');
		$inCore->redirect('/board/'.$id);  
    }
    
    if ( !$inCore->inRequest('submit') ) {

        if (IS_BILLING) { cmsBilling::checkBalance('board', 'add_item'); }
        $inPage->setTitle($_LANG['ADD_ADV']);

		$item = cmsUser::sessionGet('item');
		if ($item) { cmsUser::sessionDel('item'); }

		$item['city'] = $item['city'] ? $item['city'] : $inDB->get_field('cms_user_profiles', 'id='.$inUser->id, 'city');

        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/{$cat['id']}/add.html");
        $smarty->assign('form_do', 'add');
        $smarty->assign('cfg', $cfg);
		$smarty->assign('cat', $cat);
		$smarty->assign('item', $item);
        $smarty->assign('obtypes', obTypesOptions($cat['obtypes']));
        $smarty->assign('cities', $inCore->boardCities('', '-- '.$_LANG['NOT_SELECT'].' --'));
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('catslist', $inCore->getListItemsNS('cms_board_cats'));
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->display('com_board_edit.tpl');
        return;

    }

    if ( $inCore->inRequest('submit') ) {

        // входные данные
        $obtype     = $inCore->request('obtype', 'str');
        $title      = $inCore->request('title', 'str', '');
        $content 	= $inCore->request('content', 'str', '');
        $captcha    = $inCore->request('code', 'str', '');
        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = $city ? $city : $city_ed;

        $vipdays    = $inCore->request('vipdays', 'int', 0);

        $published  = 0;

        if ($cat['public']==-1) { $cat['public'] = $cfg['public']; }

        $published  = ($cat['public']==2 && $inCore->isUserCan('board/autoadd')) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/moderate')) { $published = 1; }

        if ($cfg['srok']){  $pubdays = ($inCore->request('pubdays', 'int') <= 50) ? $inCore->request('pubdays', 'int') : 50; }
        if (!$cfg['srok']){ $pubdays = isset($cfg['pubdays']) ? $cfg['pubdays'] : 14; }

		$errors = false;
        if (!$title) 	 { cmsCore::addSessionMessage($_LANG['NEED_TITLE'], 'error'); $errors = true; }
        if (!$content) { cmsCore::addSessionMessage($_LANG['NEED_TEXT_ADV'], 'error'); $errors = true; }
        if (!$city)    { cmsCore::addSessionMessage($_LANG['NEED_CITY'], 'error'); $errors = true; }

        if (!$inCore->checkCaptchaCode($captcha) && !$inUser->is_admin){ cmsCore::addSessionMessage($_LANG['ERR_CAPTCHA'], 'error'); $errors = true; }

        if ($errors){
			$item['content'] = $_REQUEST['content'];
			$item['city']    = $city;
			$item['title']   = $title;
			cmsUser::sessionPut('item', $item);
			$inCore->redirect('/board/'.$id.'/add.html');
        }

		// Загружаем фото
        $file = $model->uploadPhoto('', $cfg, $cat);
		
		if(!$file) { cmsCore::addSessionMessage($_LANG['PHOTO_NOT_UPLOAD'], 'info'); }

        $item_id = $model->addRecord(array(
                                    'category_id'=>$id,
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
            if ($cfg['vip_enabled'] && $vipdays && $cfg['vip_day_cost']){
                if ($vipdays > $cfg['vip_max_days']) { $vipdays = $cfg['vip_max_days']; }
                $summ = $vipdays * $cfg['vip_day_cost'];
                if ($inUser->balance >= $summ){
                    cmsBilling::pay($inUser->id, $summ, $_LANG['VIP_BUY_LOG']);
                    $model->setVip($item_id, $vipdays);
                }                
            }
        }

		if ($published == 1) {
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
		}

        //finish
		if (!$published) { $prmoder = '<p>'.$_LANG['ADV_PREMODER_TEXT'].'</p>'; }
		cmsCore::addSessionMessage('<p><strong>'.$_LANG['ADV_IS_ADDED'].'</strong></p>'.$prmoder, 'info');
		$inCore->redirect('/board/'.$id);

    }
	
}
/////////////////////////////// EDIT BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='edititem'){

	//Load data
    $item = $model->getRecord($id);
    $cat  = $model->getCategory($item['category_id']);
	if (!$cat) { cmsCore::error404(); }
	if (!$item) { cmsCore::error404(); }

    $inPage->setTitle($_LANG['EDIT_ADV']);
    $inPage->addPathway($item['category'], '/board/'.$item['cat_id']);
    $inPage->addPathway($_LANG['EDIT_ADV']);

    $inPage->printHeading($_LANG['EDIT_ADV']);

	//Check user access
	if ($inUser->id){	
        $moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
    } else {
        $moderator = false;
    }
				
	//Show data only for moderators and owners
	if (!$moderator){
		cmsCore::addSessionMessage($_LANG['YOU_HAVENT_ACCESS'], 'error');
		$inCore->redirect('/board/'.$item['cat_id']);  
	}

    if (!$inCore->inRequest('submit')){
        //show form
        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/edit{$id}.html");
        $smarty->assign('form_do', 'edit');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('cat', $cat);
        $smarty->assign('obtypes', obTypesOptions($cat['obtypes'], $item['obtype']));
        $smarty->assign('cities', $inCore->boardCities('', '-- '.$_LANG['NOT_SELECT'].' --'));
        $smarty->assign('item', $item);
        $smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('is_billing', IS_BILLING);
        if (IS_BILLING){ $smarty->assign('balance', $inUser->balance); }
        $smarty->assign('catslist',  $inCore->getListItemsNS('cms_board_cats'));
        $smarty->display('com_board_edit.tpl');
    }

    if ($inCore->inRequest('submit')){

        $obtype     = $inCore->request('obtype', 'str');
        $title      = $inCore->request('title', 'str', '');
        $content 	= $inCore->request('content', 'str', '');
        $captcha    = $inCore->request('code', 'str', '');
        $vipdays    = $inCore->request('vipdays', 'int', 0);

        $new_cat_id     = $inCore->request('category_id', 'int', 0);
        if ($new_cat_id){ $item['category_id'] = $new_cat_id; }

        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = $city ? $city : $city_ed;

        if ($cat['public']==-1) { $cat['public'] = $cfg['public']; }

        $published  = ($cat['public']==2 && $inCore->isUserCan('board/autoadd')) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/moderate')) { $published = 1; }

		if ($item['is_overdue'] && !$item['published']) {
			if ($cfg['srok']){  $pubdays = ($inCore->request('pubdays', 'int') <= 50) ? $inCore->request('pubdays', 'int') : 50; }
        if (!$cfg['srok']){ $pubdays = isset($cfg['pubdays']) ? $cfg['pubdays'] : 14; }
			$pubdate = date("Y-m-d H:i:s");
		} else {
			$pubdays = $item['pubdays'];
			$pubdate = $item['fpubdate'];
		}

		$errors = false;
        if (!$title) { cmsCore::addSessionMessage($_LANG['NEED_TITLE'], 'error'); $errors = true; }
        if (!$content) { cmsCore::addSessionMessage($_LANG['NEED_TEXT_ADV'], 'error'); $errors = true; }
        if (!$city)    { cmsCore::addSessionMessage($_LANG['NEED_CITY'], 'error'); $errors = true; }
        if (!$inCore->checkCaptchaCode($captcha) && !$inUser->is_admin){ cmsCore::addSessionMessage($_LANG['ERR_CAPTCHA'], 'error'); $errors = true; }

		if ($errors){ $inCore->redirect('/board/edit'.$id.'.html'); }

		// Загружаем фото
        $file = $model->uploadPhoto($item['file'], $cfg, $cat);
		$file['filename'] = $file['filename'] ? $file['filename'] : $item['file'];

        $model->updateRecord($id, array(
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

        if ($inUser->is_admin && $vipdays){
            $model->setVip($id, $vipdays);
        }

        if (IS_BILLING) {
            if ($cfg['vip_enabled'] && $cfg['vip_prolong'] && $vipdays && $cfg['vip_day_cost']){
                if ($vipdays > $cfg['vip_max_days']) { $vipdays = $cfg['vip_max_days']; }
                $summ = $vipdays * $cfg['vip_day_cost'];
                if ($inUser->balance >= $summ){
                    cmsBilling::pay($inUser->id, $summ, $_LANG['VIP_BUY_LOG']);
                    $model->setVip($id, $vipdays);
                }
            }
        }

        //finish
		if (!$published) { $prmoder = '<p>'.$_LANG['ADV_EDIT_PREMODER_TEXT'].'</p>'; }
		cmsCore::addSessionMessage('<p><strong>'.$_LANG['ADV_MODIFIED'].'</strong></p>'.$prmoder, 'info');
		$inCore->redirect('/board/read'.$id.'.html');

    }
}
/////////////////////////////// DELETE BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do == 'delete'){
	//Check user access
	$item = $model->getRecord($id);
    if (!$item){ cmsCore::error404(); }

	if ($inUser->id){
        $moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
    } else {
        $moderator = false;
    }

	if (!$moderator){
		cmsCore::addSessionMessage($_LANG['YOU_HAVENT_ACCESS'], 'error');
		$inCore->redirect('/board/'.$item['cat_id']);  
	}

        if (!$inCore->inRequest('godelete')){
			//confirmation
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
			//deleting
            $model->deleteRecord($id);
			cmsCore::addSessionMessage($_LANG['ADV_IS_DELETED'], 'info');
            $inCore->redirect('/board/'.$item['cat_id']);
		}

}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
} //function
?>