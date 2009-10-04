<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function obTypesLinks($cat_id, $types){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	global $menuid;
	$html = '';
	$types = explode("\n", $types);
	$total = sizeof($types); $c = 0;
	foreach($types as $id=>$type){
		$c++;
		$type = trim($type);
		$html .= '<a href="/board/'.$menuid.'/'.$cat_id.'/type/'.urlencode(ucfirst($type)).'">'.ucfirst($type).'</a>';
		if ($c < $total){
			$html .= ', ';
		}
	}			
	return $html;
}

function orderForm($orderby, $orderto){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	if (isset($_SESSION['board_type'])) { $btype = $_SESSION['board_type']; } else { $btype = ''; }
	if (isset($_SESSION['board_city'])) { $bcity = $_SESSION['board_city']; } else { $bcity = ''; }	
	$smarty = $inCore->initSmarty('components', 'com_board_order_form.tpl');				
		$smarty->assign('btype', $btype);
		$smarty->assign('btypes', $inCore->boardTypesList($btype));
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

function pageBar($cat_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$html   = '';
	
	$result = $inDB->query("SELECT id FROM cms_board_items WHERE category_id = $cat_id") ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$link = '/board/'.$inCore->menuId().'/'.@$_REQUEST['id'].'-'.$p;
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function board(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$menuid     = $inCore->menuId();
	$cfg        = $inCore->loadComponentConfig('board');

	//DEFAULT VALUES	
	if (!isset($cfg['showlat'])) { $cfg['showlat'] = 1; }		
	if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
	if (!isset($cfg['photos'])) { $cfg['photos'] = 1; }
	if (!isset($cfg['maxcols'])) { $cfg['maxcols'] = 1; }
	if (!isset($cfg['public'])) { $cfg['public'] = 1; }
	if (!isset($cfg['srok'])) { $cfg['srok'] = 1; }
	if (!isset($cfg['pubdays'])) { $cfg['pubdays'] = 14; }
    if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }

    $inCore->loadModel('board');
    $model      = new cms_model_board();

	$root       = $model->getRootCategory();
	
	$inPage->addHeadCSS('components/board/css/styles.css');

	$id         = $inCore->request('id', 'int', $root['id']);
	$userid     = $inCore->request('userid', 'int');
	$do         = $inCore->request('do', 'str', 'view');
	
/////////////////////////////// SET CITY ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='city'){ 
	$city = urldecode($inCore->request('city', 'str')); 

	if (!empty($city)){ $_SESSION['board_city'] = $city; } else { unset($_SESSION['board_city']); }
    $inCore->redirectBack();
}

/////////////////////////////// VIEW CATEGORY ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){ 

	unset($_SESSION['board_type']);
	unset($_SESSION['board_city']);

   	$col        = 1;
    $maxcols    = isset($cfg['maxcols']) ? $cfg['maxcols'] : 1;
    $perpage    = $category['perpage'] ? $category['perpage'] : 20;
    $page       = $inCore->request('page', 'int', 1);

	//SHOW CATEGORY LIST
	$category   = $model->getCategory($id);

    if ( $category['public'] == -1 ) { $category['public'] = $cfg['public']; }

	//PAGE HEADING		
	if ($id == $root['id']){
		$pagetitle  = $inCore->menuTitle();
		if ($pagetitle) { $inPage->setTitle($pagetitle); } 
		if (!$pagetitle) { $inPage->setTitle('Доска объявлений'); $pagetitle = 'Доска объявлений'; }
	}

    if ($id != $root['id']) {
		$pagetitle      =  $category['title'];		

        $left_key       = $category['NSLeft'];
		$right_key      = $category['NSRight'];
        $category_path  = $model->getCategoryPath($left_key, $right_key);
		foreach($category_path as $pcat){
            $inPage->addPathway($pcat['title'], '/board/'.$menuid.'/'.$pcat['id']);
		}        
        
        $inPage->setTitle($pagetitle  . ' - Доска объявлений');
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
			$cat['ob_links']    = obTypesLinks($cat['id'], $cfg['obtypes']);
            $cats[]             = $cat;
		}
	}

	$smarty = $inCore->initSmarty('components', 'com_board_cats.tpl');			
		$smarty->assign('menuid', $menuid);
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
    if ($category['orderform'] && $root['id']!=$id){
        echo orderForm($orderby, $orderto);
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
    
    //DISPLAY
    $smarty = $inCore->initSmarty('components', 'com_board_items.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('is_items', $is_items);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('cat', $category);
        $smarty->assign('root_id', $root['id']);
        $smarty->assign('items', $items);
        $smarty->assign('maxcols', $maxcols);
        $smarty->assign('colwidth', $colwidth);
        $smarty->assign('pagebar', pageBar($id, $page, $perpage));
        $smarty->assign('is_user', $inUser->id);
    $smarty->display('com_board_items.tpl');
						
}
/////////////////////////////// VIEW ITEM ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='read'){
	$item   = $model->getRecord($id);

	if ($item){
		
		//PATHWAY ENTRY
		$pagetitle =  $item['title'];
		$inPage->setTitle($pagetitle  . ' - Доска объявлений');

		//PATHWAY ENTRY
		$left_key       = $item['NSLeft'];
		$right_key      = $item['NSRight'];
		$category_path  = $model->getCategoryPath($left_key, $right_key);
		foreach($category_path as $pcat){
            $inPage->addPathway($pcat['title'], '/board/'.$menuid.'/'.$pcat['id']);
		}
		$inPage->addPathway($item['title']);

		$inPage->setTitle($item['title']);
		$inPage->printHeading($item['title']);

		$model->increaseHits($id);

        //encode city
        $item['enc_city']   = urlencode($item['city']);

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
			$smarty->assign('menuid', $menuid);
			$smarty->assign('moderator', $moderator);
			$smarty->assign('item', $item);
			$smarty->assign('cfg', $cfg);
			$smarty->assign('is_user', $inUser->id);
			$smarty->assign('user_id', $inUser->id);
		$smarty->display('com_board_item.tpl');				
	}
}
/////////////////////////////// NEW BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='additem'){
	$max_mb = 2; //max filesize in Mb

	$inPage->backButton(false);

    $cat = $model->getCategory($id);

    if ( !$cat ) { $inCore->halt('Рубрика не найдена'); }
	
    if ( $cat['public'] == -1 ) { $cat['public'] = $cfg['public']; }

    $inPage->addPathway($cat['title'], '/board/'.$menuid.'/'.$cat['id']);
	$inPage->addPathway('Добавить объявление');

    if ( !$inUser->id ) {
		$inPage->printHeading('Требуется регистрация');
		echo '<div>Возможность добавления объявлений есть только у зарегистрированных пользователей.</div>';
		echo '<div><a href="/registration">Перейти к регистрации</a></div>';
        return;
	}

    $inPage->printHeading('Добавить объявление');

    if ( !(loadedByUser24h($inUser->id, $cat['id'])<$cat['uplimit'] || $cat['uplimit'] == 0) ){       
        echo '<p>Достигнут предел добавлений в сутки. Вы сможете добавить объявление в эту рубрику через 24 часа.</p>';
        return;
    }
   
    if ( !$cat['public'] ){
        echo '<p>Вы не можете добавлять объявления в эту рубрику</p>';
        return;
    }
    
    ///////////// first upload step ////////////////////////////////////////////
    if ( !$inCore->inRequest('submit') ) {

        $inPage->setTitle('Добавить объявление');

        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/{$menuid}/{$cat['id']}/add.html");
        $smarty->assign('form_do', 'add');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('obtypes', $inCore->boardTypesList(''));
        $smarty->assign('title', '');
        $smarty->assign('city', $inDB->get_field('cms_user_profiles', 'id='.$inUser->id, 'city'));
        $smarty->assign('cities', $inCore->boardCities('', '-- не выбран --'));
        $smarty->assign('content', '');
        $smarty->assign('pubdays', '');
        $smarty->assign('file', '');
        $smarty->assign('category_id', '');
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('catslist', $inCore->getListItemsNS('cms_board_cats'));
        $smarty->display('com_board_edit.tpl');
        return;

    }

    ///////////// final upload step ////////////////////////////////////////////
    if ( $inCore->inRequest('submit') ) {

        $errors     = '';
        $user_id    = $inUser->id;

        //params
        $obtype     = $inCore->request('obtype', 'str');
        $title 		= $inCore->request('title', 'str', '');
        $title      = $obtype .' '. $title;
        $content 	= $inCore->request('content', 'str', '');

        $captcha    = $inCore->request('code', 'str', '');

        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = $city ? $city : $city_ed;

        $published  = 0;

        if ($cat['public']==-1) { $cat['public'] = $cfg['public']; }
        $published  = ($cat['public']==2) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/autoadd')) { $published = 1; }
        if ($cfg['srok']){  $pubdays = $inCore->request('pubdays', 'int'); }
        if (!$cfg['srok']){ $pubdays = isset($cfg['pubdays']) ? $cfg['pubdays'] : 14; }

        if (empty($title)) 	 { $errors .= '<div style="color:red">Необходимо указать заголовок объявления!</div>'; }
        if (empty($content)) { $errors .= '<div style="color:red">Необходимо указать текст объявления!</div>'; }
        if (empty($city))    { $errors .= '<div style="color:red">Необходимо указать город!</div>'; }

        if (!$inCore->checkCaptchaCode($captcha) && !$inUser->is_admin){	$errors .= '<div style="color:red">Неправильно указан код с картинки!</div>';		}

        if ($errors){
            //finish
            echo '<p><strong>Объявление не добавлено.</strong></p>';
            echo '<p>'.$errors.'</p>';
            echo '<p>&larr; <a href="/board/'.$menuid.'/'.$id.'/add.html">Повторить добавление</a><br/>';
            echo '&larr; <a href="/board/'.$menuid.'/'.$id.'">Вернуться к доске объявлений</a><br/>';
            return;
        }

        $filename = '';
        if (isset($_FILES['picture'])){
            $inCore->includeGraphics();
            //dirs
            $uploaddir      = PATH.'/images/board/';
            $realfile       = $_FILES['picture']['name'];
            //next id
            $filename       = md5($realfile . $user_id . time()).'.jpg';
            //filenames
            $uploadfile     = $uploaddir . $realfile;
            $uploadphoto    = $uploaddir . $filename;
            $uploadthumb    = $uploaddir . 'small/' . $filename;
            $uploadthumb2   = $uploaddir . 'medium/' . $filename;
            //uploading
            if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
                @img_resize($uploadphoto, $uploadthumb, $cat['thumb1'], $cat['thumb1'], $cat['thumbsqr']);
                @img_resize($uploadphoto, $uploadthumb2, $cat['thumb2'], $cat['thumb2'], false, $cfg['watermark']);
                if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
                @unlink($uploadphoto);
            } else {
                echo '<p>Файл фотографии не был загружен.</p>';
            }
        }

        $model->addRecord(array(
                                    'category_id'=>$id,
                                    'user_id'=>$user_id,
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
                                    'city'=>$city,
                                    'pubdays'=>$pubdays,
                                    'published'=>$published,
                                    'file'=>$filename
                                ));

        //finish
        echo '<p><strong>Объявление успешно добавлено.</strong></p>';
        if (!$published) { echo '<p>Объявление будет опубликовано после проверки администратором.</p>'; }
        echo '<p>&larr; <a href="/board/'.$menuid.'/'.$id.'/add.html">Добавить еще объявление</a><br/>';
        echo '&larr; <a href="/board/'.$menuid.'/'.$id.'">Вернуться к доске объявлений</a><br/>';

        return;
    }
	
}
/////////////////////////////// EDIT BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='edititem'){

	//Load data
    $item = $model->getRecord($id);

	if (!$item){
   		$inPage->printHeading('Объявление не найдено');
		echo '<p>Возможно оно было удалено.</p>';
		return;
    }

    $inPage->setTitle('Редактировать объявление');
    $inPage->addPathway($item['category'], '/board/'.$menuid.'/'.$item['cat_id']);
    $inPage->addPathway('Редактировать объявление');

    $inPage->printHeading('Редактировать объявление');

	//Check user access
	if ($inUser->id){	
        $moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
    } else {
        $moderator = false;
    }
				
	//Show data only for moderators and owners
	if (!$moderator){
		echo '<div class="con_heading">Доступ запрещен</div>';
		echo '<p>Вы не имеете доступа к этой странице.</p>';
        return;
	}

    if (!$inCore->inRequest('submit')){
        //show form
        $smarty = $inCore->initSmarty('components', 'com_board_edit.tpl');
        $smarty->assign('action', "/board/{$menuid}/edit{$id}.html");
        $smarty->assign('form_do', 'edit');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('obtypes', $inCore->boardTypesList($item['obtype']));
        $smarty->assign('title', trim(str_replace($item['obtype'], '', $item['title'])));
        $smarty->assign('city', $item['city']);
        $smarty->assign('cities', $inCore->boardCities('', '-- не выбран --'));
        $smarty->assign('content', $item['content']);
        $smarty->assign('pubdays', $item['pubdays']);
        $smarty->assign('file', $item['file']);
        $smarty->assign('category_id', $item['cat_id']);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('catslist',  $inCore->getListItemsNS('cms_board_cats'));
        $smarty->display('com_board_edit.tpl');
    }

    if ($inCore->inRequest('submit')){
        $errors = '';
        $uid        = $inUser->id;

        $obtype     = $inCore->request('obtype', 'str');
        $title 		= $inCore->request('title', 'str', '');
        $title      = $obtype .' '. $title;
        $content 	= $inCore->request('content', 'str', '');

        $captcha    = $inCore->request('code', 'str', '');

        $city_ed    = $inCore->request('city_ed', 'str', '');
        $city       = $inCore->request('city', 'str', '');
        $city       = $city ? $city : $city_ed;

        if ($cat['public']==-1) { $cat['public'] = $cfg['public']; }
        $published  = ($cat['public']==2) ? 1 : 0;
        if ($inUser->is_admin || $inCore->isUserCan('board/autoadd')) { $published = 1; }

        if ($cfg['srok']){  $pubdays = $inCore->request('pubdays', 'int'); }
        if (!$cfg['srok']){ $pubdays = isset($cfg['pubdays']) ? $cfg['pubdays'] : 14; }

        if (empty($title)) 	 { $errors .= '<div style="color:red">Необходимо указать заголовок объявления!</div>'; }
        if (empty($content)) { $errors .= '<div style="color:red">Необходимо указать текст объявления!</div>'; }
        if (empty($city)) { $errors .= '<div style="color:red">Необходимо указать город!</div>'; }
        
        if (!$inCore->checkCaptchaCode($captcha) && !$inUser->is_admin){	$errors .= '<div style="color:red">Неправильно указан код с картинки!</div>';		}

        if ($errors){
            echo '<p><strong>Объявление не изменено.</strong></p>';
            echo '<p>'.$errors.'</p>';
            echo '<p>&larr; <a href="/board/'.$menuid.'/edit'.$id.'.html">Повторить редактирование</a><br/>';
            echo '&larr; <a href="/board/'.$menuid.'/'.$item['cat_id'].'">Вернуться к доске объявлений</a><br/>';
            return;
        }

        $filename   = $item['file'];
        $uploaddir  = PATH.'/images/board/';

        if (isset($_FILES['picture']['name'])){
            $inCore->includeGraphics();
            $realfile       = $_FILES['picture']['name'];
            $filename       = md5($id . $realfile . time()).'.jpg';
            $uploadfile     = $uploaddir . $realfile;
            $uploadphoto    = $uploaddir . $filename;
            $uploadthumb    = $uploaddir . 'small/' . $filename;
            $uploadthumb2   = $uploaddir . 'medium/' . $filename;

            if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
                @img_resize($uploadphoto, $uploadthumb, $item['thumb1'], $item['thumb1'], $item['thumbsqr']);
                @img_resize($uploadphoto, $uploadthumb2, $item['thumb2'], $item['thumb2'], false, $cfg['watermark']);
                if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
                @unlink($uploadphoto);
            } else {
                $filename = $item['file'];
            }
        }

        if ($inCore->request('delphoto', 'int', 0)){
            $filename = '';
            @unlink($uploaddir.'medium/'.$item['file']);
            @unlink($uploaddir.'small/'.$item['file']);
        }

        $model->updateRecord($id, array(
                                    'category_id'=>$item['cat_id'],
                                    'obtype'=>$obtype,
                                    'title'=>$title,
                                    'content'=>$content,
                                    'city'=>$city,
                                    'published'=>$published,
                                    'file'=>$filename
                                ));

        //finish
        echo '<p><strong>Объявление изменено.</strong></p>';
        if (!$published) { echo '<p>Объявление скрыто и будет вновь опубликовано после проверки администратором.</p>'; }
        echo '<p>&larr; <a href="/board/'.$menuid.'/'.$item['cat_id'].'">Вернуться к доске объявлений</a></p>';

    }
}
/////////////////////////////// DELETE BOARD ITEM /////////////////////////////////////////////////////////////////////////////////////////
if ($do == 'delete'){
	//Check user access
	$item = $model->getRecord($id);

    if (!$item){
        $inPage->printHeading('Объявление не найдено');
        echo '<p>Возможно оно уже было удалено.</p>';
        return false;
    }

	if ($inUser->id){
        $moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $item['user_id'] == $inUser->id);
    } else {
        $moderator = false;
    }

	if (!$moderator){ $inCore->halt(); }

        if (!$inCore->inRequest('godelete')){
			//confirmation
            $inPage->setTitle('Удалить объявление');
            $inPage->addPathway($item['category'], '/board/'.$menuid.'/'.$item['cat_id']);
            $inPage->addPathway('Удалить объявление');

            $confirm['title']               = 'Удаление объявления';
            $confirm['text']                = 'Вы действительно желаете удалить объявление "'.$item['title'].'"?';
            $confirm['action']              = $_SERVER['REQUEST_URI'];
            $confirm['yes_button']['name']  = 'godelete';

            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
		}

        if ($inCore->inRequest('godelete')){
			//deleting
            $model->deleteRecord($id);
            $inCore->redirect('/board/'.$menuid.'/'.$item['cat_id']);
		}

}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
} //function
?>