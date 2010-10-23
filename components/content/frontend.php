<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
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

    $inCore->loadModel('content');
    $model = new cms_model_content();

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
			$sub                = $model->getSubCatsCount($subcat['id']);
            $subtext            = $sub ? $subtext = '/'.$sub : '';
			$subcat['subtext']  = $subtext;
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
			$con['user_access']  = $inCore->checkUserAccess('material', $con['id']);
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

	$smarty = $inCore->initSmarty('components', 'com_content_view.tpl');			
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
	$smarty->display('com_content_view.tpl');
				
}
///////////////////////////////////// READ ARTICLE ////////////////////////////////////////////////////////////////////////////////
if ($do=='read'){

	$inCore->includeConfig();

    $seolink = $inCore->request('seolink', 'str', '');

    $seolink = preg_replace ('/[^a-z0-9_\/\-]/i', '', $seolink);

    if ($seolink) { $article = $model->getArticleByLink($seolink); }

    if ($id) { $article = $model->getArticle($id); }

    if ( !$article ) {
        cmsCore::error404();
    }

	if( !$inCore->checkUserAccess('material', $article['id']) ){
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
    if (!$forum_thread_id && $cfg['af_on'] && $category_id != $cfg['af_hidecat_id'] && !$inCore->userIsAdmin($article['user_id'])){
        $forum_thread_id = cmsAutoCreateThread($article, $cfg);
    }

    $smarty = $inCore->initSmarty('components', 'com_content_read.tpl');

    if($cfg['rating'] && $article['canrate']){
        $inCore->loadLib('karma');
        $karma = cmsKarma('content', $article['id']);

        $smarty->assign('karma_points', cmsKarmaFormatSmall($karma['points']));
        $smarty->assign('karma_votes', $karma['votes']);

        $btns = cmsKarmaButtonsText('content', $article['id']);
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

    $smarty->assign('article_content', $article_content);
    $smarty->assign('forum_thread_id', $forum_thread_id);
    $smarty->assign('tagbar', cmsTagBar('content', $article['id']));

    $smarty->display('com_content_read.tpl');

    //show user comments
    if($article['comments'] && $inCore->isComponentInstalled('comments')){
        $inCore->includeComments();
        comments('article', $article['id']);
    }
	
}
///////////////////////////////////// ADD ARTICLE //////////////////////////////////////////////////////////////////////////////////
if ($do=='addarticle' || $do=='editarticle'){

	if (!$inCore->isUserCan('content/add')){
        $inPage->printHeading($_LANG['ACCESS_DENIED']);
        echo '<p>'.$_LANG['ACCESS_DENIED'].'</p>';
        return;
    }

    $user_id    = $inUser->id;
    $smarty     = $inCore->initSmarty('components', 'com_content_edit.tpl');

    //Moderation notice
    $add_notice = '';

    if ($do=='editarticle'){
        $sql = "SELECT * FROM cms_content WHERE id = $id AND user_id = $user_id LIMIT 1";
        $rs = $inDB->query($sql);
        $mod = $inDB->fetch_assoc($rs);
    }

    if ( !$inCore->inRequest('add_mod') ){

        $inPage->addPathway($_LANG['MY_ARTICLES'], '/content/my.html');
        if ($do=='addarticle'){
            $inPage->setTitle($_LANG['ADD_ARTICLE']);
            $inPage->addPathway($_LANG['ADD_ARTICLE']);
            $pagetitle = $_LANG['ADD_ARTICLE'];
        }

        if ($do=='editarticle'){
            $inPage->setTitle($_LANG['EDIT_ARTICLE']);
            $inPage->addPathway($_LANG['EDIT_ARTICLE']);
            $pagetitle = $_LANG['EDIT_ARTICLE'];

            $mod['tags'] = cmsTagLine('content', $mod['id'], false);
            $mod['image'] = (file_exists(PATH.'/images/photos/small/article'.$mod['id'].'.jpg') ? 'article'.$mod['id'].'.jpg' : '');

            $smarty->assign('mod', $mod);

            if (!$inCore->isUserCan('content/autoadd')){
                $add_notice = '<p><strong>'.$_LANG['ATTENTION'].':</strong> '.$_LANG['EDIT_ARTICLE_PREMODER'].'</p>';
            }
        }

        if (isset($mod['category_id'])){
            $pubcats = $inCore->getListItemsNS('cms_category', $mod['category_id'], '', 'is_public');
        } else {
            $pubcats = $inCore->getListItemsNS('cms_category', 0, '', 'is_public');
        }

        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');

        //display form
        $smarty->assign('do', $do);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('pubcats', $pubcats);
        $smarty->assign('pagetitle', $pagetitle);
        $smarty->assign('add_notice', $add_notice);
        $smarty->assign('is_admin', $inCore->userIsAdmin($inUser->id));
        $smarty->assign('autocomplete_js', $autocomplete_js);
        $smarty->display('com_content_edit.tpl');

    }

    if ( $inCore->inRequest('add_mod') ){

        $errors                         = false;

        //SUBMIT & UPDATE
        $article['category_id']         = $inCore->request('category_id', 'int', 1);
        $article['user_id']             = $inUser->id;
        $article['title']               = $inCore->request('title', 'str', '');
        $article['tags']                = $inCore->request('tags', 'str', '');
        $article['description']         = $inCore->request('description', 'html', '');
        $article['content']             = $inCore->request('content', 'html', '');

        $article['description']         = $inDB->escape_string($article['description']);
        $article['content']             = $inDB->escape_string($article['content']);

        $article['published']           = $inCore->isUserCan('content/autoadd') ? 1 : 0;
        $article['pubdate']             = date('Y-m-d H:i');
        $article['enddate']             = $article['pubdate'];
        $article['is_end']              = 0;
        $article['showtitle']           = 1;
        $article['meta_desc']           = strtolower($article['title']);
        $article['meta_keys']           = $inCore->getKeywords($inCore->strClear($article['content']));
        $article['showdate']            = 1;
        $article['showlatest']          = 1;
        $article['showpath']            = 1;
        $article['comments']            = 1;
        $article['canrate']             = 1;
        $article['pagetitle']           = $article['title'];

        if (!$article['title']){ cmsCore::addSessionMessage($_LANG['REQ_TITLE'], 'error'); $errors = true; }
        if (!$article['content']){ cmsCore::addSessionMessage($_LANG['REQ_CONTENT'], 'error'); $errors = true; }

        if ($do=='addarticle' && !$errors){

            //Убираем <meta http-equiv="refresh">
            $article['description'] = str_replace('http-equiv=', '', $article['description']);
            $article['content']     = str_replace('http-equiv=', '', $article['content']);

            $article['id'] = $model->addArticle($article);

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
			
            $is_published = $inCore->isUserCan('content/autoadd');
			
            if (!$is_published){
                echo '<p>'.$_LANG['ARTICLE_PREMODER_TEXT'].'</p>';
            } else {

                //регистрируем событие
                $article['seolink']     = $inDB->get_field('cms_content', "id={$id}", 'seolink');
                $article['category']    = $inDB->get_fields('cms_category', "id={$article['category_id']}", 'title, seolink');

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

            $article['pubdate'] = $mod['pubdate'];

            $model->updateArticle($id, $article);

            cmsInsertTags($article['tags'], 'content', $id);

        }

        if (!$errors){

            $file       = 'article'.$id.'.jpg';

            if ($inCore->request('delete_image', 'int', 0)){
                @unlink($_SERVER['DOCUMENT_ROOT']."/images/photos/small/$file");
                @unlink($_SERVER['DOCUMENT_ROOT']."/images/photos/medium/$file");
            } else {

                if (isset($_FILES["picture"]["name"]) && @$_FILES["picture"]["name"]!=''){
                    //generate image file
                    $tmp_name   = $_FILES["picture"]["tmp_name"];
                    //upload image and insert record in db
                    if (@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/images/photos/$file")){
                        $inCore->includeGraphics();
						if ($cfg['watermark'] && !$cfg['watermark_only_big']) { @img_add_watermark($_SERVER['DOCUMENT_ROOT']."/images/photos/$file"); }
                        @img_resize($_SERVER['DOCUMENT_ROOT']."/images/photos/$file", $_SERVER['DOCUMENT_ROOT']."/images/photos/small/$file", $cfg['img_small_w'], $cfg['img_small_w'], $cfg['img_sqr']);
                        @img_resize($_SERVER['DOCUMENT_ROOT']."/images/photos/$file", $_SERVER['DOCUMENT_ROOT']."/images/photos/medium/$file", $cfg['img_big_w'], $cfg['img_big_w'], $cfg['img_sqr']);
						if ($cfg['watermark'] && $cfg['watermark_only_big']) { @img_add_watermark($_SERVER['DOCUMENT_ROOT']."/images/photos/medium/$file"); }
						
                        @unlink($_SERVER['DOCUMENT_ROOT']."/images/photos/$file");
                        @chmod($_SERVER['DOCUMENT_ROOT']."/images/photos/small/$file", 0755);
                        @chmod($_SERVER['DOCUMENT_ROOT']."/images/photos/medium/$file", 0755);
                    }
                }

            }

        }

        if ($do=='editarticle' || $errors){
			$mess = $is_published ? $_LANG['ARTICLE_SAVE'] : $_LANG['ARTICLE_SAVE'].' '.$_LANG['ARTICLE_PREMODER_TEXT'];
			cmsCore::addSessionMessage($mess, 'info');
            $inCore->redirect('/content/my.html');
        }


    }

}
///////////////////////////////////// DELETE ARTICLE ///////////////////////////////////////////////////////////////////////////////////
if ($do=='deletearticle'){
	if ($inCore->isUserCan('content/delete')){	
		$ismy = dbRowsCount('cms_content', 'id='.$id.' AND user_id='.$inUser->id);		
		if ($ismy){
            $inCore->includeFile('components/forum/includes/forumcore.php'); //needs for auto-thread deleting
			$model->deleteArticle($id, $cfg['af_delete']);
			cmsCore::addSessionMessage($_LANG['ARTICLE_DELETED'], 'info');
		}
	}
	$inCore->redirectBack();
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
    $inPage->addPathway($_LANG['MY_ARTICLES']);

    $inPage->printHeading($_LANG['MY_ARTICLES']);

    //total count
    $sql = "SELECT con.id
            FROM cms_content con
            WHERE con.user_id = '{$user_id}'
            LIMIT 200";

   $rs = $inDB->query($sql); $total = $inDB->num_rows($rs);

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

    $inPage->addHeadJS('components/content/js/my.js');

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