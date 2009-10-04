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

function pageBar($cat_id, $cat_seolink, $model, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

    $html = '';
	
	$result = $inDB->query("SELECT id FROM cms_content WHERE category_id = $cat_id AND published = 1") ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>��������: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {
					$link = $model->getCategoryURL($inCore->menuId(), $cat_seolink, $p);
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

function pageBarMy($records, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$html = '';
		if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>��������: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {								
					$link = '/content/my'.$p.'.html';					
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

function content(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inConf     = cmsConfig::getInstance();
    
	$inCore->loadLib('tags');
    $inCore->loadLib('content');

	$menuid = $inCore->menuId();
	$cfg = $inCore->loadComponentConfig('content');

    $inCore->loadModel('content');
    $model = new cms_model_content();
	
	if(!isset($cfg['perpage'])) { $cfg['perpage'] = 20; }
	if(!isset($cfg['autokeys'])) { $cfg['autokeys'] = 1; }
    if(!isset($cfg['af_showlink'])) { $cfg['af_showlink'] = 1; }
    if(!isset($cfg['readdesc'])) { $cfg['readdesc'] = 0; }
    if(!isset($cfg['rating'])) { $cfg['rating'] = 1; }
	
	$id = $inCore->request('id', 'int', 0);
	$do = $inCore->request('do', 'str', 'view');

	$inPage->addHeadCSS('components/content/css/styles.css');
	
///////////////////////////////////// VIEW CATEGORY ////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

    $seolink = $inCore->request('seolink', 'str', '');

    $seolink = preg_replace ('/[^a-z0-9_\/\-]/i', '', $seolink);

    if ($seolink) { $cat = $model->getCategoryByLink($seolink); }

	if ($id) { $cat = $model->getCategory($id); }

    if (!$cat) { return; }

	//PAGE HEADING
	if($cat['id']>0){
		$inPage->setTitle($cat['title']);
		$pagetitle  = $cat['title'];
		$showdate   = $cat['showdate'];
		$showcomm   = $cat['showcomm'];
	}

    if ($cat['id']<=0){
		$inPage->setTitle('������� ������');
		$pagetitle  = '������� ������';
	}

	//PATHWAY ENTRY
	$left_key   = $cat['NSLeft'];
	$right_key  = $cat['NSRight'];

    $path_list  = $model->getCategoryPath($left_key, $right_key);

    if ($path_list){
        foreach($path_list as $pcat){
            if ($pcat['id']!=1){
                $inPage->addPathway($pcat['title'], $model->getCategoryURL($menuid, $pcat['seolink']));
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
			$subcat['url']      = $model->getCategoryURL($menuid, $subcat['seolink']);
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
            $con['url']          = $model->getArticleURL($menuid, $con['seolink']);
			$cons[]              = $con;
		}
		$is_articles = true;
	} else {
		$is_articles = false;
	}
	
	$smarty = $inCore->initSmarty('components', 'com_content_view.tpl');			
	$smarty->assign('menuid', $menuid);
	$smarty->assign('id', $cat['id']);
	$smarty->assign('cat', $cat);
	$smarty->assign('showdate', $showdate);
	$smarty->assign('showcomm', $showcomm);
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('is_subcats', $is_subcats);
	if(@$subcats) { $smarty->assign('subcats', $subcats); }
	$smarty->assign('photos_html', $photos_html);
	$smarty->assign('is_articles', $is_articles);
	if(@$cons) { $smarty->assign('articles', $cons); }
	$smarty->assign('pagebar', pageBar($cat['id'], $cat['seolink'], $model, $page, $perpage));
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
        $inPage->setTitle('�������� �� �������');
        $inPage->printHeading('�������� �� �������');
        echo '<p>�������� ��� ���� ������� ��� ����������.</p>';
        return;
    }

	if( !$inCore->checkUserAccess('material', $article['id']) ){
		$inPage->setTitle('��� ���� ��� ���������');
		$inPage->printHeading('��� ���� ��� ���������');
		echo '<p><b>� ��� ��� ���� ��� ��������� ����� ���������.</b></p>';
		echo '<p>���������� � ������������� ����� ��� ��������� �������, ���� �����������������.</p>';
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
                    $inPage->addPathway($pcat['title'], $model->getCategoryURL($menuid, $pcat['seolink']));
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
        cmsAutoCreateThread($article, $cfg);
        //get related thread id again
        $forum_thread_id = $model->getRelatedThread($article['id']);
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

    $smarty->assign('menuid', $menuid);
    $smarty->assign('id', $article['id']);
    if (@$cat) { $smarty->assign('cat', $cat); }
    $smarty->assign('article', $article);
    $smarty->assign('cfg', $cfg);

    $page = $inCore->request('page', 'int', 1);
    $smarty->assign('page', $page);
    $smarty->assign('is_pages', sizeof($GLOBALS['pt']));
    $smarty->assign('pt_pages', $GLOBALS['pt']);

    if ($cfg['pt_disp']) { $disp_style = 'display: block;'; } else { $disp_style = 'display: none;'; }
    $smarty->assign('pt_disp_style', $disp_style);

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
        $inPage->printHeading('������ ��������');
        echo '<p>������ ��������.</p>';
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

        $inPage->addPathway('��� ������', '/content/my.html');
        if ($do=='addarticle'){
            $inPage->setTitle('�������� ������');
            $inPage->addPathway('�������� ������');
            $pagetitle = '�������� ������';
        }

        if ($do=='editarticle'){
            $inPage->setTitle('������������� ������');
            $inPage->addPathway('������������� ������');
            $pagetitle = '������������� ������';
            $mod['tags'] = cmsTagLine('content', $mod['id'], false);
            $smarty->assign('mod', $mod);

            if (!$inCore->isUserCan('content/autoadd')){
                $add_notice = '<p><strong>��������:</strong> ����� ���������� ��������� ������ ���������� �� �������� ��������!</p>';
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
        $smarty->assign('menuid', $menuid);
        $smarty->assign('pubcats', $pubcats);
        $smarty->assign('pagetitle', $pagetitle);
        $smarty->assign('add_notice', $add_notice);
        $smarty->assign('is_admin', $inCore->userIsAdmin($inUser->id));
        $smarty->assign('autocomplete_js', $autocomplete_js);
        $smarty->display('com_content_edit.tpl');

    }

    if ( $inCore->inRequest('add_mod') ){
        //SUBMIT & UPDATE
        $article['category_id']         = $inCore->request('category_id', 'int', 1);
        $article['user_id']             = $inUser->id;
        $article['title']               = $inCore->request('title', 'str');
        $article['tags']                = $inCore->request('tags', 'str');
        $article['description']         = $inCore->request('description', 'html');
        $article['content']             = $inCore->request('content', 'html');

        $article['published']           = $inCore->isUserCan('content/autoadd') ? 1 : 0;
        $article['pubdate']             = date('Y-m-d H:i');
        $article['enddate']             = $article['pubdate'];
        $article['is_end']              = 0;
        $article['showtitle']           = 1;
        $article['meta_desc']           = strtolower($title);
        $article['meta_keys']           = $inCore->getKeywords($inCore->strClear($content));
        $article['showdate']            = 1;
        $article['showlatest']          = 1;
        $article['showpath']            = 1;
        $article['comments']            = 1;
        $article['canrate']             = 1;
        $article['pagetitle']           = $article['title'];

        if ($do=='addarticle'){

            $article['id'] = $model->addArticle($article);

            cmsUser::checkAwards($user_id);

            cmsInsertTags($article['tags'], 'content', $article['id']);

            //autoforum
            if ($cfg['af_on'] && $category_id != $cfg['af_hidecat_id'] && $article['published']){
                cmsAutoCreateThread($article, $cfg);
            }

            //MESSAGE
            $inPage->setTitle('������ ����������');
            $inPage->backButton(false);
            $inPage->addPathway('������ ����������');
            $inPage->printHeading('������ ����������');

            if (!$inCore->isUserCan('content/autoadd')){
                echo '<p>����� �������� ��������������� ��� ����� ������������ �� �����.</p>';
            }

            echo '<p><a href="/">����������</a></p>';
            return;

        }

        if ($do=='editarticle'){

            $article['pubdate'] = $mod['pubdate'];

            $model->updateArticle($id, $article);

            cmsInsertTags($article['tags'], 'content', $id);

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
		}
	}
	$inCore->redirectBack();
}
///////////////////////////////////// MY ARTICLES ///////////////////////////////////////////////////////////////////////////////////
if ($do=='my'){

    if (!$inCore->isUserCan('content/add')){
        $inPage->printHeading('������ ��������');
        echo '<p>������ ��������.</p>';
        return;
    }
    
    $user_id = $inUser->id;
	
    $inPage->setTitle('��� ������');
    $inPage->addPathway('��� ������');

    $inPage->printHeading('��� ������');

    //total count
    $sql = "SELECT con.id
            FROM cms_content con
            WHERE con.user_id = '{$user_id}'
            LIMIT 200";

   $rs = $inDB->query($sql); $total = $inDB->num_rows($rs);

    //current page
    $sql = "SELECT con.*, cat.title as category, DATE_FORMAT(con.pubdate, '%d-%m-%Y') as pubdate,
                   IF(con.published=1, '<span style=\"color:green\">��������</span>', '<span style=\"color:#CC0000\">�� ��������</span>') as status
            FROM cms_content con, cms_category cat
            WHERE con.user_id = $user_id AND con.category_id = cat.id
            ORDER BY con.pubdate DESC
            ";

    $perpage = 20;
    $page = $inCore->request('page', 'int', 1);
    $sql .= "LIMIT ".(($page-1)*$perpage).", $perpage";

    $rs = $inDB->query($sql);

    if (!$inDB->num_rows($rs)){
        echo '<p>�� ����� ��� ����� ������. <a href="/content/add.html">�������� ������?</a></p>';
        return;
    }

    $inPage->addHeadJS('components/content/js/my.js');

    $articles = array(); $row=0;
    while($con = $inDB->fetch_assoc($rs)){
        $row++;
        $articles[$row] = $con;
        if ($row %2) { $articles[$row]['class']="search_row1"; } else { $articles[$row]['class']="search_row2"; }
        $articles[$row]['comments'] = $inCore->getCommentsCount('content', $con['id']);
    }

    $smarty = $inCore->initSmarty('components', 'com_content_my.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('articles', $articles);
        $smarty->assign('user_can_delete', $inCore->isUserCan('content/delete'));
        $smarty->assign('pagebar', pageBarMy($total, $page, $perpage));
    $smarty->display('com_content_my.tpl');

}
///////////////////////////////////// BEST ARTICLES ///////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){
    $inCore->loadLib('karma');

    $user_id = $inUser->id;

    $inPage->setTitle('������� ������');
    $inPage->addPathway('������� ������');

    $inPage->printHeading('������� ������');

    $sql = "SELECT c.*, IF(DATE_FORMAT(c.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'),	DATE_FORMAT(c.pubdate, '<strong>�������</strong> � %H:%i'),
                    DATE_FORMAT(c.pubdate, '%d-%m-%Y'))  as pubdate, IFNULL(SUM(r.points), 0) as points, IFNULL(COUNT(r.points), 0) as votes,
                    u.nickname as author, cat.title as category
            FROM cms_users u, cms_category cat, cms_content c
            LEFT JOIN cms_ratings r ON r.item_id=c.id AND r.target='content'
            WHERE c.published = 1 AND c.canrate = 1 AND c.user_id = u.id AND c.category_id = cat.id
            GROUP BY r.item_id
            ORDER BY points DESC, votes ASC
            LIMIT 50";
    $rs = $inDB->query($sql);

    if (!$inDB->num_rows($rs)){
        ////NO ARTICLES
        echo '<p>�� ����� ��� ��������� ������.</p>';
        return;
    }

    $articles = array(); $row=0;
    while($con = $inDB->fetch_assoc($rs)){
        $row++;
        $articles[$row] = $con;
        if ($row%2) { $articles[$row]['class']="search_row1"; } else { $articles[$row]['class']="search_row2"; }
        $articles[$row]['comments'] = $inCore->getCommentsCount('content', $con['id']);
        $articles[$row]['karma'] 	= cmsKarmaFormat($con['points']);
    }

    $smarty = $inCore->initSmarty('components', 'com_content_rating.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('articles', $articles);
    $smarty->display('com_content_rating.tpl');
		
		
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //function
?>