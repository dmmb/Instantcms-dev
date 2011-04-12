<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function content(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inConf     = cmsConfig::getInstance();
    
    $inCore->loadLib('tags');
    $inCore->loadLib('content');

    $cfg = $inCore->loadComponentConfig('content');

    // Проверяем включени ли компонент
    if(!$cfg['component_enabled']) { cmsCore::error404(); }

    $inCore->loadModel('content');
    $model = new cms_model_content();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    global $_LANG;

    if(!isset($cfg['perpage'])) { $cfg['perpage'] = 20; }
    if(!isset($cfg['autokeys'])) { $cfg['autokeys'] = 1; }
    if(!isset($cfg['af_showlink'])) { $cfg['af_showlink'] = 1; }
    if(!isset($cfg['readdesc'])) { $cfg['readdesc'] = 0; }
    if(!isset($cfg['rating'])) { $cfg['rating'] = 1; }

    if(!isset($cfg['img_small_w'])) { $cfg['img_small_w'] = 100; }
    if(!isset($cfg['img_big_w'])) { $cfg['img_big_w'] = 200; }
    if(!isset($cfg['img_sqr'])) { $cfg['img_sqr'] = 1; }
    if(!isset($cfg['img_users'])) { $cfg['img_users'] = 1; }

    $id = $inCore->request('id', 'int', 0);
    $do = $inCore->request('do', 'str', 'view');

///////////////////////////////////// VIEW CATEGORY ////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

    $seolink = $inCore->request('seolink', 'str', '');

    $seolink = preg_replace ('/[^a-z0-9_\/\-]/i', '', $seolink);

    if ($seolink) { 
        $cat = $model->getCategoryByLink($seolink);
    } elseif($id) {
        $cat = $model->getCategory($id); 
    }

    if (!$cat && $inCore->menuId() !== 1) { cmsCore::error404(); }

	if( !$inCore->checkUserAccess('category', $cat['id']) ){
		$inPage->setTitle($_LANG['NO_PERM_FOR_VIEW']);
		$inPage->printHeading($_LANG['NO_PERM_FOR_VIEW']);
		echo '<p><b>'.$_LANG['NO_PERM_FOR_VIEW_TEXT'].'</b></p>';
		echo '<p>'.$_LANG['NO_PERM_FOR_VIEW_RULES'].'</p>';
        return;
	}

	//PAGE HEADING
	if($cat['id']>0){
		$inPage->setTitle($cat['title']);
		$pagetitle  = $cat['title'];
		$showdate   = $cat['showdate'];
		$showcomm   = $cat['showcomm'];
	}

    if ($cat['id']<=0){
		$inPage->setTitle($_LANG['CATALOG_ARTICLES']);
		$pagetitle  = $_LANG['CATALOG_ARTICLES'];
	}

	//PATHWAY ENTRY
	$left_key   = $cat['NSLeft'];
	$right_key  = $cat['NSRight'];

    $path_list  = $model->getCategoryPath($left_key, $right_key);

    if ($path_list){
        foreach($path_list as $pcat){
			if( !$inCore->checkUserAccess('category', $pcat['id']) ){
				$inPage->setTitle($_LANG['NO_PERM_FOR_VIEW']);
				$inPage->printHeading($_LANG['NO_PERM_FOR_VIEW']);
				echo '<p><b>'.$_LANG['NO_PERM_FOR_VIEW_TEXT'].'</b></p>';
				echo '<p>'.$_LANG['NO_PERM_FOR_VIEW_RULES'].'</p>';
				return;
			}
            if ($pcat['id']!=1){
                $inPage->addPathway($pcat['title'], $model->getCategoryURL(null, $pcat['seolink']));
            }
        }
    }

	$inPage->addPathway($cat['title'], $_SERVER['REQUEST_URI']);
	
	//LIST OF SUBCATEGORIES
    $subcats_list   = $model->getSubCats($cat['id'], $left_key, $right_key);

	if ($subcats_list){
		$is_subcats = true;
		$subcats    = array();
		foreach($subcats_list as $subcat){
			$subcat['url']      = $model->getCategoryURL(null, $subcat['seolink']);
            $subcats[]          = $subcat;
		}
	} else {
		$is_subcats = false;
	}

	//LINKED PHOTOS
	$photos_html = '';
	if (isset($cat['photoalbum'])){	
		if($cat['photoalbum']){
			$album = unserialize($cat['photoalbum']);
			$inCore->loadLib('photos');
			$photos_html = cmsPhotoList($album);			
		}
	}

	//CURRENT CATEGORY CONTENT
	$perpage        = $cfg['perpage'] ? $cfg['perpage'] : 20;
	$page           = $inCore->request('page', 'int', 1);

    $cons           = array();
    $content_list   = $model->getArticles($cat['id'], $page, $perpage, $cat['orderby'], $cat['orderto']);

	if ($content_list){
		foreach($content_list as $con){
			$con['tagline']		 = cmsTagLine('content', $con['id'], true);
			$con['comments'] 	 = $inCore->getCommentsCount('article', $con['id']);
			$con['user_access']  = true; // оставлено для совместимости со старыми шаблонами, убрать в след версиях.
            $con['url']          = $model->getArticleURL(null, $con['seolink']);
            $con['image']       = (file_exists(PATH.'/images/photos/small/article'.$con['id'].'.jpg') ? 'article'.$con['id'].'.jpg' : '');
			$cons[]              = $con;
		}
		$is_articles = true;
	} else {
		$is_articles = false;
	}

	$total      = $inDB->rows_count('cms_content', 'published=1 AND category_id='.$cat['id']);
    $pagelink   = $model->getCategoryURL($inCore->menuId(), $cat['seolink'], 0, true);
    $pagebar    = cmsPage::getPagebar($total, $page, $perpage, $pagelink);

	$template = ($cat['tpl'] ? $cat['tpl'] : 'com_content_view.tpl');

	$smarty = $inCore->initSmarty('components', $template);			
	$smarty->assign('id', $cat['id']);
	$smarty->assign('cat', $cat);
    $smarty->assign('is_homepage', (bool)($inCore->menuId()==1));
	$smarty->assign('showdate', $showdate);
	$smarty->assign('showcomm', $showcomm);
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('is_subcats', $is_subcats);
	if(@$subcats) { $smarty->assign('subcats', $subcats); }
	$smarty->assign('photos_html', $photos_html);
	$smarty->assign('is_articles', $is_articles);
	if(@$cons) { $smarty->assign('articles', $cons); }
	if ($content_list){
        $smarty->assign('pagebar', $pagebar);
	}
	$smarty->assign('maxcols', $cat['maxcols']);
	$smarty->display($template);
				
}
///////////////////////////////////// READ ARTICLE ////////////////////////////////////////////////////////////////////////////////
if ($do=='read'){

	$inCore->includeConfig();

    $seolink = $inCore->request('seolink', 'str', '');

    $seolink = preg_replace ('/[^a-z0-9_\/\-]/i', '', $seolink);

    if ($seolink) { 
        $article = $model->getArticleByLink($seolink);
    } elseif($id) {
        $article = $model->getArticle($id);
    }

    if ( !$article ) { cmsCore::error404(); }

	if ($inUser->id) {
		$is_admin      = $inUser->is_admin;
		$is_author     = $inUser->id == $article['user_id'];
		$is_author_del = $inCore->isUserCan('content/delete');
		$is_editor     = ($article['modgrp_id'] == $inUser->group_id && $inCore->isUserCan('content/autoadd'));
    }

	if (!$article['published'] && !$is_admin && !$is_editor && !$is_author) { cmsCore::error404(); } 	

	if( !$inCore->checkUserAccess('material', $article['id']) || !$inCore->checkUserAccess('category', $article['category_id']) ){
		$inPage->setTitle($_LANG['NO_PERM_FOR_VIEW']);
		$inPage->printHeading($_LANG['NO_PERM_FOR_VIEW']);
		echo '<p><b>'.$_LANG['NO_PERM_FOR_VIEW_TEXT'].'</b></p>';
		echo '<p>'.$_LANG['NO_PERM_FOR_VIEW_RULES'].'</p>';
        return;
	}

    $model->increaseHits($article['id']);

    if (!$article['pagetitle']){
        $inPage->setTitle($article['title']);
    } else {
        $inPage->setTitle($article['pagetitle']);
    }

    $inPage->addHeadJS('core/js/karma.js');

    //PREPARE CONTENT
    $article_content = $cfg['readdesc'] ? $article['description'].$article['content'] : $article['content'];

    //PATHWAY ENTRY
    //GET PATH TO ARTICLE
    if ($article['showpath']){
        $left_key   = $article['leftkey'];
        $right_key  = $article['rightkey'];

        $path_list  = $model->getCategoryPath($left_key, $right_key);

        if ($path_list){
            foreach($path_list as $pcat){
				if( !$inCore->checkUserAccess('category', $pcat['id']) ){
					$inPage->setTitle($_LANG['NO_PERM_FOR_VIEW']);
					$inPage->printHeading($_LANG['NO_PERM_FOR_VIEW']);
					echo '<p><b>'.$_LANG['NO_PERM_FOR_VIEW_TEXT'].'</b></p>';
					echo '<p>'.$_LANG['NO_PERM_FOR_VIEW_RULES'].'</p>';
					return;
				}
                if ($pcat['id']!=1){
                    $inPage->addPathway($pcat['title'], $model->getCategoryURL(null, $pcat['seolink']));
                }
            }
        }
    }

    $inPage->addPathway($article['title'], $_SERVER['REQUEST_URI']);

    //SET META KEYWORDS AND DESCRIPTION
    if (strlen($article['meta_keys'])){ $inPage->setKeywords($article['meta_keys']); }
    elseif ( !$cfg['autokeys'] ) { $inPage->setKeywords($inConf->keywords); }
    else {
        if (sizeof($inCore->strClear($article_content))>30){
            $inPage->setKeywords($inCore->getKeywords($inCore->strClear($article_content)));
        }
    }

    if (strlen($article['meta_desc'])){ $inPage->setDescription($article['meta_desc']); } else { $inPage->setDescription($inConf->metadesc); }

    //PROCESS FILTERS
    $inCore->processFilters($article_content);

    //HIGHLIGHT USER LAST SEARCH QUERY IF NECCESSARY
    if (isset($_SESSION['squery'])){
        $regex = '/('.$_SESSION['squery'].')\s*(.*?)/i';
        $matches = array();
        preg_match_all( $regex, $article_content, $matches, PREG_SET_ORDER );
        foreach ($matches as $elm) {
            $article_content = preg_replace( $regex, '<span class="search_match">'.$_SESSION['squery'].'</span>', $article_content );
        }
        unset($_SESSION['squery']);
    }
    
    $cfg['showtitle'] = (isset($cfg['showtitle']) || @$cfg['showtitle']);

    //CHECK RELATED FORUM THREAD, create if neccessary
    $forum_thread_id = $model->getRelatedThread($article['id']);
    
    //if thread not found, create it again, only if author is not admin
    if (!$forum_thread_id && $cfg['af_on'] && $category_id != $cfg['af_hidecat_id'] && !$inCore->userIsAdmin($article['user_id']) && $article['published']){
        $forum_thread_id = cmsAutoCreateThread($article, $cfg);
    }

	$template = ($article['tpl'] ? $article['tpl'] : 'com_content_read.tpl');

    $smarty = $inCore->initSmarty('components', $template);

    if($cfg['rating'] && $article['canrate']){
        $inCore->loadLib('karma');
        $karma = cmsKarma('content', $article['id']);

        $smarty->assign('karma_points', cmsKarmaFormatSmall($karma['points']));
        $smarty->assign('karma_votes', $karma['votes']);

        $btns = cmsKarmaButtonsText('content', $article['id'], $karma['points'], $is_author);
        if ($btns) { $smarty->assign('karma_buttons', $btns); }
    }
	$article['pubdate'] = $inCore->dateformat($article['pubdate']);
    $smarty->assign('id', $article['id']);
    if (@$cat) { $smarty->assign('cat', $cat); }
    $smarty->assign('article', $article);
    $smarty->assign('cfg', $cfg);

    foreach($GLOBALS['pt'] as $num=>$page_title){
        $pt_pages[$num]['title']    = $page_title;
        $pt_pages[$num]['url']      = $model->getArticleURL(null, $article['seolink'], $num+1);
    }

    $page = $inCore->request('page', 'int', 1);
    $smarty->assign('page', $page);
    $smarty->assign('is_pages', sizeof($GLOBALS['pt']));
    $smarty->assign('pt_pages', $pt_pages);

    if ($cfg['pt_disp']) { $disp_style = 'display: block;'; } else { $disp_style = 'display: none;'; }
    $smarty->assign('pt_disp_style', $disp_style);

    $article_image = (file_exists(PATH.'/images/photos/medium/article'.$article['id'].'.jpg') ? 'article'.$article['id'].'.jpg' : '');
    $smarty->assign('article_image', $article_image);

	$smarty->assign('is_admin', $is_admin);
	$smarty->assign('is_editor', $is_editor);
	$smarty->assign('is_author', $is_author);
	$smarty->assign('is_author_del', $is_author_del);

    $smarty->assign('article_content', $article_content);
    $smarty->assign('forum_thread_id', $forum_thread_id);
    $smarty->assign('tagbar', cmsTagBar('content', $article['id']));

    $smarty->display($template);

    //show user comments
    if($article['comments'] && $inCore->isComponentInstalled('comments')){
        $inCore->includeComments();
        comments('article', $article['id']);
    }
	
}
///////////////////////////////////// ADD ARTICLE //////////////////////////////////////////////////////////////////////////////////
if ($do=='addarticle' || $do=='editarticle'){

	$is_add      = $inCore->isUserCan('content/add');     // может добавлять статьи
	$is_auto_add = $inCore->isUserCan('content/autoadd'); // добавлять статьи без модерации
	$is_admin    = $inUser->is_admin;                     // админ
	$user_id     = $inUser->id;                           // id текущего юзера
	
	if (!$is_add && !$is_auto_add){
        $inPage->printHeading($_LANG['ACCESS_DENIED']);
        echo '<p>'.$_LANG['ACCESS_DENIED'].'</p>';
        return;
    }

    $smarty     = $inCore->initSmarty('components', 'com_content_edit.tpl');

    //Moderation notice
    $add_notice = '';

    if ($do=='editarticle'){
		$is_autoadd = $is_auto_add ? '' : "AND con.user_id = '$user_id'";
		$mod = $inDB->get_fields('cms_content con LEFT JOIN cms_category cat ON cat.id = con.category_id', "con.id = '$id' $is_autoadd", 'con.*, cat.modgrp_id', 'con.id ASC');
		if (!$mod) { cmsCore::error404(); }

		$is_editor = $mod['modgrp_id'] == $inUser->group_id && $is_auto_add; // редактор если id группы редакторов раздела совпадает с id группы текущего пользователя
		$is_author = $user_id == $mod['user_id'];                            // автор, если текущий id пользователя совпадает с id автора статьи
		if (!$is_editor && !$is_author && !$is_admin) { cmsCore::error404(); };
    }

    if ( !$inCore->inRequest('add_mod') ){

        $inPage->addPathway($inUser->nickname, cmsUser::getProfileURL($inUser->login));
        $inPage->addPathway($_LANG['MY_ARTICLES'], '/content/my.html');

        if ($do=='addarticle'){
            $inPage->setTitle($_LANG['ADD_ARTICLE']);
            $inPage->addPathway($_LANG['ADD_ARTICLE']);
            $pagetitle = $_LANG['ADD_ARTICLE'];

            $pubcats        = $model->getPublicCats();

            // поддержка биллинга
            $dynamic_cost = false;
            if (IS_BILLING){                             
                $action         = cmsBilling::getAction('content', 'add_content');
                foreach($pubcats as $p=>$pubcat){
                    if ($pubcat['cost']){
                        $dynamic_cost = true;
                    } else {
                        $pubcats[$p]['cost'] = $action['point_cost'][$inUser->group_id];
                    }
                }
                cmsBilling::checkBalance('content', 'add_content', $dynamic_cost);
            }

        }

        if ($do=='editarticle'){
            $inPage->setTitle($_LANG['EDIT_ARTICLE']);
            $inPage->addPathway($_LANG['EDIT_ARTICLE']);
            $pagetitle = $_LANG['EDIT_ARTICLE'];

            $mod['tags'] = cmsTagLine('content', $mod['id'], false);
            $mod['image'] = (file_exists(PATH.'/images/photos/small/article'.$mod['id'].'.jpg') ? 'article'.$mod['id'].'.jpg' : '');

            $smarty->assign('mod', $mod);

            if (!$is_auto_add){
                $add_notice = '<p><strong>'.$_LANG['ATTENTION'].':</strong> '.$_LANG['EDIT_ARTICLE_PREMODER'].'</p>';
            }
        }

        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');

        //display form
        $smarty->assign('do', $do);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('pubcats', $pubcats);
        $smarty->assign('pagetitle', $pagetitle);
        $smarty->assign('add_notice', $add_notice);
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('is_billing', IS_BILLING);
        $smarty->assign('dynamic_cost', $dynamic_cost);
        $smarty->assign('autocomplete_js', $autocomplete_js);
        $smarty->display('com_content_edit.tpl');

    }

    if ( $inCore->inRequest('add_mod') ){

        $errors                         = false;

        //SUBMIT & UPDATE
        $article['category_id']         = $inCore->request('category_id', 'int', 1);
        $article['user_id']             = $mod['user_id'] ? $mod['user_id'] : $user_id;
        $article['title']               = $inCore->request('title', 'str', '');
        $article['tags']                = $inCore->request('tags', 'str', '');

        $article['description']         = $inCore->request('description', 'html', '');
        $article['content']             = $inCore->request('content', 'html', '');

        $article['description']         = $inDB->escape_string($article['description']);
        $article['content']             = $inDB->escape_string($article['content']);

        $article['description']         = $inCore->badTagClear($article['description']);
        $article['content']             = $inCore->badTagClear($article['content']);

        $article['published']           = $is_auto_add ? 1 : 0;
        if ($do=='editarticle'){
           $article['published']           = ($mod['published'] == 0) ? $mod['published'] : $article['published'];
        }
        $article['pubdate']             = $mod['pubdate'] ? $mod['pubdate'] : date('Y-m-d H:i');
        $article['enddate']             = $article['pubdate'];
        $article['is_end']              = 0;
        $article['showtitle']           = $do=='editarticle' ? $mod['showtitle'] : 1;
        $article['meta_desc']           = strtolower($article['title']);
        $article['meta_keys']           = $inCore->getKeywords($inCore->strClear($article['content']));
        $article['showdate']            = $do=='editarticle' ? $mod['showdate'] : 1;
        $article['showlatest']          = $do=='editarticle' ? $mod['showlatest'] : 1;
        $article['showpath']            = $do=='editarticle' ? $mod['showpath'] : 1;
        $article['comments']            = $do=='editarticle' ? $mod['comments'] : 1;
        $article['canrate']             = $do=='editarticle' ? $mod['canrate'] : 1;
        $article['pagetitle']           = $article['title'];

        if (!$article['title']){ cmsCore::addSessionMessage($_LANG['REQ_TITLE'], 'error'); $errors = true; }
        if (!$article['content']){ cmsCore::addSessionMessage($_LANG['REQ_CONTENT'], 'error'); $errors = true; }

        if ($do=='addarticle' && !$errors){

            $article['id'] = $model->addArticle($article);

            if (IS_BILLING){            
                $category_cost = $inDB->get_field('cms_category', "id='{$article['category_id']}'", 'cost');
                $category_cost = $category_cost==='' ? false : (int)$category_cost;
                cmsBilling::process('content', 'add_content', $category_cost);
            }

            $id = $article['id'];

            cmsUser::checkAwards($user_id);

            //autoforum
            if ($cfg['af_on'] && $category_id != $cfg['af_hidecat_id'] && $article['published']){
                cmsAutoCreateThread($article, $cfg);
            }

            //MESSAGE
            $inPage->setTitle($_LANG['ARTICLE_SEND']);
            $inPage->backButton(false);
            $inPage->addPathway($_LANG['ARTICLE_SEND']);
            $inPage->printHeading($_LANG['ARTICLE_SEND']);
			
            $article['seolink']     = $inDB->get_field('cms_content', "id='$id'", 'seolink');
            $article['category']    = $inDB->get_fields('cms_category', "id='{$article['category_id']}'", 'title, seolink');
            
			if (!$article['published']){
			
                echo '<p>'.$_LANG['ARTICLE_PREMODER_TEXT'].'</p>';

				$link = '<a href="/'.$article['seolink'].'.html">'.$article['title'].'</a>';
				$user = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';
				$message = str_replace('%user%', $user, $_LANG['MSG_ARTICLE_SUBMIT']);
				$message = str_replace('%link%', $link, $message);
                cmsUser::sendMessage(USER_UPDATER, 1, $message);

            } else {

                //регистрируем событие
                cmsActions::log('add_article', array(
                    'object' => $article['title'],
                    'object_url' =>  "/{$article['seolink']}.html",
                    'object_id' =>  $article['id'],
                    'target' => $article['category']['title'],
                    'target_url' => "/{$article['category']['seolink']}",
                    'target_id' =>  $article['category_id'],
                    'description' => ''
                ));

            }

            echo '<p><a href="/">'.$_LANG['CONTINUE'].'</a></p>';

        }

        if ($do=='editarticle' && !$errors){

            $model->updateArticle($id, $article, true);

			if (!$article['published'] && !$is_auto_add){
                $article['seolink']  = $inDB->get_field('cms_content', "id='$id'", 'seolink');
                $article['category'] = $inDB->get_fields('cms_category', "id='{$article['category_id']}'", 'title, seolink');
				$link = '<a href="/'.$article['seolink'].'.html">'.$article['title'].'</a>';
				$user = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';
				$message = str_replace('%user%', $user, $_LANG['MSG_ARTICLE_EDITED']);
				$message = str_replace('%link%', $link, $message);
                cmsUser::sendMessage(USER_UPDATER, 1, $message);
			}

            cmsInsertTags($article['tags'], 'content', $id);

        }

        if (!$errors){

            $file       = 'article'.$id.'.jpg';

            if ($inCore->request('delete_image', 'int', 0)){
                @unlink(PATH."/images/photos/small/$file");
                @unlink(PATH."/images/photos/medium/$file");
            } else {

                if (isset($_FILES["picture"]["name"]) && @$_FILES["picture"]["name"]!=''){
                    //generate image file
                    $tmp_name   = $_FILES["picture"]["tmp_name"];
                    //upload image and insert record in db
                    if (@move_uploaded_file($tmp_name, PATH."/images/photos/$file")){
                        $inCore->includeGraphics();
						if ($cfg['watermark'] && !$cfg['watermark_only_big']) { @img_add_watermark(PATH."/images/photos/$file"); }
                        @img_resize(PATH."/images/photos/$file", PATH."/images/photos/small/$file", $cfg['img_small_w'], $cfg['img_small_w'], $cfg['img_sqr']);
                        @img_resize(PATH."/images/photos/$file", PATH."/images/photos/medium/$file", $cfg['img_big_w'], $cfg['img_big_w'], $cfg['img_sqr']);
						if ($cfg['watermark'] && $cfg['watermark_only_big']) { @img_add_watermark(PATH."/images/photos/medium/$file"); }
						
                        @unlink(PATH."/images/photos/$file");
                        @chmod(PATH."/images/photos/small/$file", 0755);
                        @chmod(PATH."/images/photos/medium/$file", 0755);
                    }
                }

            }

        }

        if ($do=='editarticle' || $errors){
			$mess = ($article['published'] || $is_auto_add) ? $_LANG['ARTICLE_SAVE'] : $_LANG['ARTICLE_SAVE'].' '.$_LANG['ARTICLE_PREMODER_TEXT'];
			cmsCore::addSessionMessage($mess, 'info');
            $inCore->redirect('/content/my.html');
        }

    }
}
///////////////////////// PUBLISH ARTICLE /////////////////////////////////////////////////////////////////////////////
if ($do == 'publisharticle'){

    $user_id = $inUser->id;

	if (!$user_id || !$id){ $inCore->halt(); }
	
    $article = $model->getArticle($id);
	if (!$article) { $inCore->halt(); }

    //Если пользователь авторизован, проверяем является ли он админом или редактором
	$is_editor  = ($article['modgrp_id'] == $inUser->group_id && $inCore->isUserCan('content/autoadd'));
	$is_admin   = $inUser->is_admin;

    if (!$is_editor && !$is_admin) { $inCore->halt(); }

    $model->publishArticle($id);

	cmsCore::callEvent('ADD_ARTICLE_DONE', $article);

    //регистрируем событие
    cmsActions::log('add_article', array(
           'object' => $article['title'],
		   'user_id' => $article['user_id'],
           'object_url' =>  "/{$article['seolink']}.html",
           'object_id' =>  $article['id'],
           'target' => $article['cat_title'],
           'target_url' => "/{$article['catseolink']}",
           'target_id' =>  $article['cat_id'],
           'description' => ''
    ));

	$link = '<a href="/'.$article['seolink'].'.html">'.$article['title'].'</a>';
	$message = str_replace('%link%', $link, $_LANG['MSG_ARTICLE_ACCEPTED']);
    cmsUser::sendMessage(USER_UPDATER, $article['user_id'], $message);

    $inCore->redirectBack();

}
///////////////////////////////////// DELETE ARTICLE ///////////////////////////////////////////////////////////////////////////////////
if ($do=='deletearticle'){
	
	$user_id = $inUser->id;
	if (!$user_id || !$id){ $inCore->halt(); }

	// получаем id редактора и ссылку категории удаляемой статьи
	$article = $inDB->get_fields('cms_content con LEFT JOIN cms_category cat ON cat.id = con.category_id', "con.id = '$id'", 'con.seolink as con_seolink, con.title, cat.modgrp_id, cat.seolink', 'con.id ASC');
	if (!$article) { $inCore->halt(); }

	// Определяем права доступа
	$is_admin  = $inUser->is_admin;
	$is_author = $inCore->isUserCan('content/delete') && $inDB->rows_count('cms_content', "id='$id' AND user_id='{$user_id}'");
	$is_editor = ($article['modgrp_id'] == $inUser->group_id && $inCore->isUserCan('content/autoadd'));
	
	if (!$is_author && !$is_editor && !$is_admin){	$inCore->halt(); }

	if (!isset($_POST['goadd'])){

		$inPage->setTitle($_LANG['ARTICLE_REMOVAL']);
		$inPage->addPathway($_LANG['ARTICLE_REMOVAL']);

		$confirm['title']              = $_LANG['ARTICLE_REMOVAL'];
		$confirm['text']               = $_LANG['ARTICLE_REMOVAL_TEXT'].' <a href="/'.$article['con_seolink'].'.html">'.$article['title'].'</a>?';
		$confirm['action']             = $_SERVER['REQUEST_URI'];
		$confirm['yes_button']         = array();
		$confirm['yes_button']['type'] = 'submit';
		$confirm['yes_button']['name'] = 'goadd';
		$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
		$smarty->assign('confirm', $confirm);
		$smarty->display('action_confirm.tpl');
		$inPage->backButton(false);

	} else {

            $inCore->includeFile('components/forum/includes/forumcore.php'); //needs for auto-thread deleting
			$model->deleteArticle($id, $cfg['af_delete']);
		if ($_SERVER['HTTP_REFERER'] == '/my.html' ) { 
			$inCore->redirectBack();
			cmsCore::addSessionMessage($_LANG['ARTICLE_DELETED'], 'info');
		} else { 
			if ($is_editor || $is_admin) {
				$link = '<a href="/'.$article['con_seolink'].'.html">'.$article['title'].'</a>';
				$message = str_replace('%link%', $link, $_LANG['MSG_ARTICLE_REJECTED']);
				cmsUser::sendMessage(USER_UPDATER, $article['user_id'], $message);
		}
        $inCore->redirect('/content/my.html');
	}

	}

}
///////////////////////////////////// MY ARTICLES ///////////////////////////////////////////////////////////////////////////////////
if ($do=='my'){

    if (!$inCore->isUserCan('content/add')){
        $inPage->printHeading($_LANG['ACCESS_DENIED']);
        echo '<p>'.$_LANG['ACCESS_DENIED'].'</p>';
        return;
    }
    
    $user_id = $inUser->id;
	
    $inPage->setTitle($_LANG['MY_ARTICLES']);
	$inPage->addPathway($inUser->nickname, cmsUser::getProfileURL($inUser->login));
    $inPage->addPathway($_LANG['MY_ARTICLES']);

    //total count
    $total = $inDB->rows_count('cms_content con', "con.user_id = '$user_id'");

    $inPage->printHeading($_LANG['MY_ARTICLES'].' ('.$total.')' );	

    //current page
    $sql = "SELECT  con.*,
                    cat.title as category,
                    cat.seolink as category_seolink
            FROM cms_content con
			LEFT JOIN cms_category cat ON cat.id = con.category_id
            WHERE con.user_id = $user_id
            ORDER BY con.pubdate DESC
            ";

    $perpage = 10;
    $page = $inCore->request('page', 'int', 1);
    $sql .= "LIMIT ".(($page-1)*$perpage).", $perpage";

    $rs = $inDB->query($sql);

    if (!$inDB->num_rows($rs)){
        echo '<p>'.$_LANG['NO_YOUR_ARTICL_ON_SITE'].'. <a href="/content/add.html">'.$_LANG['ADD_ARTICLE'] .'?</a></p>';
        return;
    }

    $articles = array(); $row=0;
    while($con = $inDB->fetch_assoc($rs)){
        $row++;
        $articles[$row] = $con;
        if ($row %2) { $articles[$row]['class']="search_row1"; } else { $articles[$row]['class']="search_row2"; }
        $articles[$row]['category_href']    = $model->getCategoryURL(0, $con['category_seolink']);
        $articles[$row]['href']             = $model->getArticleURL(0, $con['seolink']);
		$articles[$row]['comments']         = $inCore->getCommentsCount('article', $articles[$row]['id']);
		$articles[$row]['pubdate'] 	        = $inCore->dateFormat($articles[$row]['pubdate']);
		$articles[$row]['status']           = $articles[$row]['published'] ? '<span style="color:green">'.$_LANG['PUBLISHED'].'</span>' : '<span style="color:#CC0000">'.$_LANG['NO_PUBLISHED'].'</span>';
    }

    $messages = cmsCore::getSessionMessages();

    $smarty = $inCore->initSmarty('components', 'com_content_my.tpl');
        $smarty->assign('articles', $articles);
        $smarty->assign('messages', $messages);
		$smarty->assign('total', $total);
        $smarty->assign('user_can_delete', $inCore->isUserCan('content/delete'));
        $smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/content/my%page%.html'));
    $smarty->display('com_content_my.tpl');

}
///////////////////////////////////// BEST ARTICLES ///////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){
    $inCore->loadLib('karma');

    $user_id = $inUser->id;

    $inPage->setTitle($_LANG['ARTICLES_RATING']);
    $inPage->addPathway($_LANG['ARTICLES_RATING']);

    $inPage->printHeading($_LANG['ARTICLES_RATING']);

    $sql = "SELECT c.*, 
                    IFNULL(r.total_rating, 0) as points, IFNULL(r.total_votes, 0) as votes,
                   cat.title as category, cat.seolink as category_seolink
            FROM cms_content c
			LEFT JOIN cms_category cat ON cat.id = c.category_id
            LEFT JOIN cms_ratings_total r ON r.item_id=c.id AND r.target='content'
            WHERE c.published = 1 AND c.canrate = 1
            ORDER BY points DESC, votes ASC
            LIMIT 30";
    $rs = $inDB->query($sql);

    if (!$inDB->num_rows($rs)){
        ////NO ARTICLES
        echo '<p>'.$_LANG['NO_ARTICLES_PUBL_ON_SITE'].'</p>';
        return;
    }

    $articles = array(); $row=0;
    while($con = $inDB->fetch_assoc($rs)){
        $row++;
        $articles[$row] = $con;
        if ($row%2) { $articles[$row]['class']="search_row1"; } else { $articles[$row]['class']="search_row2"; }
        $articles[$row]['comments'] = $inCore->getCommentsCount('article', $con['id']);
        $articles[$row]['karma'] 	= cmsKarmaFormat($con['points']);
		$articles[$row]['pubdate'] 	= $inCore->dateFormat($articles[$row]['pubdate']);
        $articles[$row]['url']      = $model->getArticleURL(null, $con['seolink']);
    }

    $smarty = $inCore->initSmarty('components', 'com_content_rating.tpl');
    $smarty->assign('articles', $articles);
    $smarty->display('com_content_rating.tpl');
		
		
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //function
?>