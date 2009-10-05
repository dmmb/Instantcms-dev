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

function blog(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    
	$inCore->includeFile("components/users/includes/usercore.php");
	$inCore->includeFile("components/blog/includes/blogcore.php");

	$inCore->loadLib("users");
	$inCore->loadLib("tags");
	$inCore->loadLib('clubs');
	$inCore->loadLib('karma');
	
	$inPage->addHeadCSS('components/blog/css/styles.css');
	$inPage->addHeadJS('includes/jquery/jquery.jcorners.js');
    $inPage->addHeadJS('core/js/karma.js');

    $inCore->loadModel('blog');
    $model = new cms_model_blog();
		
	//�������� �������� ������
	$cfg = $inCore->loadComponentConfig('blog');
	
	//�������� �������� ��-���������
	$cfg['fa_ext'] = 'gif jpeg jpg png bmp';		
	if (!isset($cfg['rss_all'])) { $cfg['rss_all'] = 1; }
	if (!isset($cfg['rss_one'])) { $cfg['rss_one'] = 1; }
    if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; }
	
	//�������� ���������
	$menuid 	= $inCore->menuId();
	$id 		= $inCore->request('id', 'int', 0);	
	$bloglink   = $inCore->request('bloglink', 'str', 0);
	$seolink    = $inCore->request('seolink', 'str', 0);
	$do 		= $inCore->request('do', 'str', 'latest');

    //���������� ��� ������� �����, � �������� ����������
	if ($inCore->inRequest('ownertype')){
		$ownertype = $inCore->request('ownertype', 'str'); 
		$_SESSION['blogs_ownertype'] = $ownertype;
	} else { 
		if (isset($_SESSION['blogs_ownertype'])){
			$ownertype = $_SESSION['blogs_ownertype'];
		} else { $ownertype = 'all'; }
	}

    //���� �����, ��������� ������ �� ��������� �� ������� ���������� �����
	if ($do!='view'){

        if ($bloglink){     $blog   = $model->getBlogByLink($bloglink);     }
        if ($id){           $blog   = $model->getBlog($id);                 }
    
        $error  = $blog ? '' : '���� �� ������';

		if ($blog){
			$owner = $blog['owner'];
			if ($owner=='user') { $blog['author'] = dbGetField('cms_users', 'id='.$blog['user_id'], 'nickname');	}
			if ($owner=='club') { $blog['author'] = dbGetField('cms_clubs', 'id='.$blog['user_id'], 'title');       }
		}

	}

////////// �������� ����� ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='create'){

    //��������� �����������
    if ( !$inUser->id ){ $inCore->redirectBack(); }

    //���� � ������������ ��� ���� ����, �� �������
    if ($model->getUserBlogId($inUser->id)) { $inCore->redirectBack(); }

	$inPage->addHeadJS('components/blog/js/blog.js');
	$inPage->addPathway('�����', '/blogs/'.$menuid);			
	$inPage->addPathway('�������� �����');

    //����� ����� �������� �����
    if (!$inCore->inRequest('goadd')){
        $inPage->setTitle('������� ����');
        $inPage->backButton(false);

        //������ ��� ������, ��-���������?
        $friends = $usr['allow_who'] == 'friends' ? 1 : 0;

        //��������� ����������� �� �����
        $min_karma_private  = '';
        $min_karma_public   = '';
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            if ($cfg['min_karma_private'] >0) { $min_karma_private  = '(��������� �����: '.$cfg['min_karma_private'].')';	}
            if ($cfg['min_karma_public']  >0) { $min_karma_public   = '(��������� �����: '.$cfg['min_karma_public'] .')';	}
        }

        //������� �����
        $smarty = $inCore->initSmarty('components', 'com_blog_create.tpl');
        $smarty->assign('friends', $friends);
        $smarty->assign('min_karma_private', $min_karma_private);
        $smarty->assign('min_karma_public', $min_karma_public);
        $smarty->display('com_blog_create.tpl');
    }

    //��� ������� �������� �����
    if ($inCore->inRequest('goadd')){

        $error_msg = '';

        $user_id    = $inUser->id;
        $title      = $inCore->request('title', 'str');
        $allow_who  = $inCore->request('allow_who', 'str', 'all');
        $ownertype  = $inCore->request('ownertype', 'str', 'single');

        //��������� ��������
        if (strlen($title)<5) { $error_msg .= '<p>������� �������� �����! �������� ������ ���� �� ������ 5-�� ��������.</p>'; }

        //��������� ������� �� �����, �� ������ ���� ��� �� �����
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            $user_karma = cmsUser::getKarma($inUser->id);
            if ($ownertype=='single' && ($user_karma < $cfg['min_karma_private']))
                $error_msg = '<p>��� �� ������� <a href="/users/0/'.$user_id.'/karma.html">�����</a> ��� �������� ������� �����. ��������� &mdash; '.$cfg['min_karma_private'].', ������� &mdash; '.$user_karma.'.</p>';
            if ($ownertype=='multi' && ($user_karma < $cfg['min_karma_public']))
                $error_msg = '<p>��� �� ������� <a href="/users/0/'.$user_id.'/karma.html">�����</a> ��� �������� ������������� �����. ��������� &mdash; '.$cfg['min_karma_public'].', ������� &mdash; '.$user_karma.'.</p>';
        }

        //���� ������ �� ���� �������
        if(!$error_msg){
            $inPage->backButton(false);
            $inPage->setTitle('������� ������');
            //��������� ���� � ����
            $blog_id = $model->addBlog(array('user_id'=>$user_id, 'title'=>$title, 'allow_who'=>$allow_who, 'ownertype'=>$ownertype));
            //������� ��������� � ��� ��� ���� ������
            $smarty  = $inCore->initSmarty('components', 'com_blog_create_ok.tpl');
            $smarty->assign('menuid', $menuid);
            $smarty->assign('blogid', $blog_id);
            $smarty->assign('userid', $user_id);
            $smarty->display('com_blog_create_ok.tpl');
        }

        //���� ������� ������
        if($error_msg){
            $inPage->setTitle('������ �������� �����!');
            $smarty = $inCore->initSmarty('components', 'com_blog_create_error.tpl');
            $smarty->assign('error_msg', $error_msg);
            $smarty->display('com_blog_create_error.tpl');
        }
    }
	

}
////////// ��������� ����� ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='config'){

    //��������� ����������� ������������
    if ( !$inUser->id ) { $inCore->redirectBack(); }

    //�������� ID ������������
    $user_id = $inUser->id;

	//��������� �������� ������������ �������� ����� ��� �������
    if ( $blog['user_id']!=$user_id && !$inCore->userIsAdmin($user_id) ) { $inCore->redirectBack(); }

	$inPage->addPathway($blog['title'],'/blogs/'.$menuid.'/'.$blog['id'].'/blog.html');			        
    $inPage->addHeadJS('components/blog/js/blog.js');

    //���� ��� ������� �� ����������, ���������� ����� �������� �����
    if ( !$inCore->inRequest('goadd') ){
        $inPage->setTitle('��������� �����');
        $inPage->printHeading('��������� �����');
        $inPage->backButton(false);
        //�������� ������ ������� �����
        $authors = blogAuthors($blog['id']);
        //�������� ����������� �� �����
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            if ($cfg['min_karma_private'] >0 && $blog['owner_type']!='single') { $min_karma_private = '(��������� �����: '.$cfg['min_karma_private'].')';	} else { $min_karma_private = ''; }
            if ($cfg['min_karma_public']  >0 && $blog['owner_type']!='multi') { $min_karma_public = '(��������� �����: '.$cfg['min_karma_public'] .')';	} else { $min_karma_public = ''; }
        }
        //������� �����
        $smarty = $inCore->initSmarty('components', 'com_blog_config.tpl');
        $smarty->assign('blog', $blog);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('id', $blog['id']);
        $smarty->assign('authors_list', cmsUser::getAuthorsList($authors));
        $smarty->assign('users_list', cmsUser::getUsersList(false, $authors));
        $smarty->assign('min_karma_private', $min_karma_private);
        $smarty->assign('min_karma_public', $min_karma_public);
        $smarty->display('com_blog_config.tpl');
    }

    //���� ������ ������ �� ����������
    if ( $inCore->inRequest('goadd') ){

        $error_msg 	= '';

        //�������� ���������
        $title 		= $inCore->request('title', 'str');
        $allow_who 	= $inCore->request('allow_who', 'str', 'all');
        $ownertype 	= $inCore->request('ownertype', 'str', 'single');
        $premod		= $inCore->request('premod', 'int', 0);
        $forall 	= $inCore->request('forall', 'int', 1);
        $showcats 	= $inCore->request('showcats', 'int', 1);

        //��������� ���������
        if (strlen($title)<5) { $error_msg .= '������� �������� �����! �������� ������ ���� �� ������ 5-�� ��������.'; }

        //��������� ����������� �� ����� (��� ����� ���� �����)
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            $user_karma = cmsUser::getKarma($inUser->id);
            if ($ownertype=='single' && ($user_karma < $cfg['min_karma_private']))
                $error_msg = '<p>��� �� ������� <a href="/users/0/'.$user_id.'/karma.html">�����</a> ��� �������� ������� �����. ��������� &mdash; '.$cfg['min_karma_private'].', ������� &mdash; '.$user_karma.'.</p>';
            if ($ownertype=='multi' && ($user_karma < $cfg['min_karma_public']))
                $error_msg = '<p>��� �� ������� <a href="/users/0/'.$user_id.'/karma.html">�����</a> ��� �������� ������������� �����. ��������� &mdash; '.$cfg['min_karma_public'].', ������� &mdash; '.$user_karma.'.</p>';
        }

        //���� ������ �� �������
        if(!$error_msg){
            //�������� ����� ������ ������� �����
            $authors = $inCore->request('authorslist', 'array');
            //��������� �������
            $model->updateBlogAuthors($blog['id'], $authors);
            //��������� ��������� �����
            $model->updateBlog($blog['id'], array('title'=>$title, 'allow_who'=>$allow_who, 'showcats'=>$showcats, 'ownertype'=>$ownertype, 'premod'=>$premod, 'forall'=>$forall));
            //�������������� �� ������� �������� �����
            $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/blog.html');
        }

        //���� ������� ������
        if($error_msg) {
            //���������� ��������� �� �������
            $inPage->setTitle('������ ��������� �����!');
            $smarty = $inCore->initSmarty('components', 'com_blog_create_ok.tpl');
            $smarty->assign('error_msg', $error_msg);
            $smarty->display('com_blog_save_error.tpl');
        }

    }
	
}
////////// ������ ������ ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	$inPage->setTitle('�����');

    //�������� ID ������������
	$user_id 		= $inUser->id;

    //������� ���������� ������������ � ������������ ������
	$single_blogs	= $model->getSingleBlogsCount();
	$multi_blogs 	= $model->getMultiBlogsCount();

    //�������� ������ ������
    $blogs_list     = $model->getBlogs($ownertype);
  	
	$blogs      = array();   //������ ������ ��� ������
    $is_blogs   = false;     //����, ������������ ���� �� �����, ������� ����� ������ �������� ������������

    //�������� �����
    foreach($blogs_list as $blog){
        //���������� ����� �� ���������� ���� ���� ������������
		$blog['can_view']   = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $blog['user_id']==$user_id);
		//���� ���� �������� ��� ������������
		if ($blog['can_view']) { 
            //�������� ������ �� ����
            $blog['url']        = $model->getBlogURL($menuid, $blog['seolink']);
            //������� ����� ������������
            $blog['comments']   = blogComments($blog['id']);
            //����������� �������� ����� �����
            $blog['karma']      = cmsKarmaFormatSmall($blog['points']);
            //�������� ���� ������� ������� ������
            $is_blogs           = true;
            //��������� ���� � ������
            $blogs[]            = $blog;
        }
	}	

    //������� ������ ������
	$smarty = $inCore->initSmarty('components', 'com_blog_view_all.tpl');				
	$smarty->assign('cfg', $cfg);
	$smarty->assign('single_blogs', $single_blogs);
	$smarty->assign('multi_blogs', $multi_blogs);
	$smarty->assign('ownertype', $ownertype);
	$smarty->assign('menuid', $menuid);
	$smarty->assign('is_admin', $inCore->userIsAdmin($user_id));
	$smarty->assign('blogs', $blogs);
	$smarty->assign('is_blogs', $is_blogs);	
	$smarty->display('com_blog_view_all.tpl');

}
////////// �������� ����� ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='blog'){

    $error = '';

    //�������� ID ������������
    $user_id = $inUser->id;

    $cat_id  = $inCore->request('cat_id', 'int', -1);

    //�������������� ������
	$smarty = $inCore->initSmarty('components', 'com_blog_view.tpl');						
	
	//���� ������ ����� - ������������
	if($owner=='user'){
        //����������, ���� �� ������ � ����� �����
		$can_view       = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $blog['user_id']==$user_id || $inCore->userIsAdmin($user_id));
        //�������� html-��� ������ �� ������ � ������� ��� ����
		$blog['author'] = cmsUser::getGenderLink($blog['user_id'], $blog['author'], $menuid);
        //������������� ��������� �������� � ����������
		$inPage->setTitle($blog['title']);
		$inPage->addPathway($blog['title']);	
	}

    //���� ������ ����� - ����
    if ($owner=='club'){
        //����� ������ ������� ������ � ��� ����
		$can_view 		= true;
        //�������� ��������� ����� � ������ �� ������� ��������������
		$blog['title'] 	= '<a href="/clubs/'.$menuid.'/'.$blog['user_id'].'">'.$blog['author'].'</a> &rarr; ����';		
		$blog['author'] = clubAdminLink($blog['user_id']);
        $blog['club']   = $inDB->get_field('cms_clubs', "id={$blog['user_id']}", 'title');
        //������������� ��������� �������� � ����������
	    $inPage->setTitle('���� - '.$blog['author']);
        $inPage->addPathway($blog['club'], '/clubs/'.$menuid.'/'.$blog['user_id']);
		$inPage->addPathway('����');
	}

    //���� ������� ���, ��������� ��������� �� ������
	if ( !($can_view || $inCore->userIsAdmin($user_id)) ) {
        $error = '<h1 class="con_heading">�������� ����</h1><p>������������ ��������� ������ � ������ ����� ����������� ������������.</p>';
    }

    //���� ���� ������ - ���������� � �������
    if ($error){
		echo '<p style="color:red">'.$error.'</p>';
        return;
	}

    //���� ������������ �� �����������, ��������� ��� ���
    if (!$user_id) {
        $myblog     = false;
        $is_author  = false;
        $is_config  = false;
    }

    //���� �����������, ��������� �������� �� �� �������� ����� ��� ��� ���������������
    if ($user_id){
        if ($owner=='user'){
            $myblog     = ($inUser->id == $blog['user_id']) ;
            $is_author  = (((!$myblog) && $blog['ownertype']=='multi' && $inDB->get_field('cms_blog_authors', 'blog_id='.$blog['id'].' AND user_id='.$user_id, 'id')) || ($blog['ownertype']=='multi' && $blog['forall']));
            $is_admin   = $inCore->userIsAdmin($user_id);
            $is_config  = $myblog || $is_admin;
        }
        if ($owner=='club'){
            $myblog     = clubUserIsMember($blog['user_id'], $user_id);
            $is_author  = $myblog;
            $is_admin   = clubUserIsAdmin($blog['user_id'], $user_id) || $inCore->userIsAdmin($user_id);
            $is_config  = false;
        }
    }

    //�������� ����� �������� � ����� ������� �� ���� ��������
    $perpage    = isset($cfg['perpage']) ? $cfg['perpage'] : 20;
    $page       = $inCore->request('page', 'int', 1);

    //���������� ������ ��������� �� ������� �����
    $authors_status = '';
    if ($blog['owner'] == 'user') {
        if ($blog['forall']){
            $authors_status = '<span class="blog_authorsall">���� ������ ��� ����</span>';
        } else {
            $authors_status = '<a href="/blogs/'.$menuid.'/'.$blog['id'].'/authors.html" class="blog_authorslink">������ �����</a>';
        }
    }

    //������� ���������� ������, ��������� ���������
    $on_moderate = dbRowsCount('cms_blog_posts', 'blog_id='.$blog['id'].' AND published = 0');

    //���� �����, �������� ������ ������ (���������) ����� �����
    $blogcats   = $blog['showcats'] ? blogCats($blog['id'], $blog['seolink'], $cat_id) : false;

    //������� ���������� ������� � ���� �����
    $total  = $model->getPostsCount($blog['id'], $cat_id, $owner);

    //���� ��� �������
    if (!$total){
        $smarty->assign('is_posts', false);
    }

    //���� ������ ����
    if ($total){

        $posts_list = $model->getPosts($blog['id'], $page, $perpage, $cat_id, $owner);

        //���������� ������ �� ����������
        if ($cat_id != -1){
            $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%menuid%/%bloglink%/page-%page%/cat-%catid%', array('menuid'=>$menuid, 'bloglink'=>$blog['seolink'], 'catid'=>$cat_id));
        } else {
            $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%menuid%/%bloglink%/page-%page%', array('menuid'=>$menuid, 'bloglink'=>$blog['seolink']));
        }

        //��������� ������
        if ($posts_list){
            $posts = array();
            foreach($posts_list as $post){
                //��������� ����� �� ���������� ��� ������ ������������
                $can_view = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));
                if ($can_view){

                    $post['url']        = $model->getPostURL($menuid, $blog['seolink'], $post['seolink']);
                    $post['comments']   = dbRowsCount('cms_comments', "target='blog' AND target_id=".$post['id']);
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);
                    
                    $msg                = $post['content'];
                    $msg                = $inCore->parseSmiles($msg, true);
                    $msg                = str_replace("&amp;", '&', $msg);                                      

                    //��������� ����� ����� �� 2 ����� �� ���� [cut=...] � ��������� ������ ������ �� ���
                    if (strstr($msg, '[cut')){
                        $msg = $model->getPostShort($msg, $post['url']);                        
                    }
                    
                    $post['msg']        = $msg;

                    $post['tagline']    = cmsTagLine('blogpost', $post['id']);
                    $post['author']     = cmsUser::getGenderLink($post['author_id'], $post['author']);
                    $posts[] = $post;

                }
            }
        }

        //�������� � ������ ���� � ������� �������
        $smarty->assign('is_posts', (bool)sizeof($posts));
    }

    //������� ����
    $smarty->assign('myblog', $myblog);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('is_author', $is_author);
    $smarty->assign('is_config', $is_config);
    $smarty->assign('authors_status', $authors_status);
    $smarty->assign('on_moderate', $on_moderate);
    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('blogcats', $blogcats);
    $smarty->assign('total', $total);
    $smarty->assign('menuid', $menuid);
    $smarty->assign('uid', $user_id);
    $smarty->assign('blog', $blog);
    if (@$posts)        { $smarty->assign('posts', $posts);           }
    if (@$pagination)   { $smarty->assign('pagination', $pagination); }
    $smarty->assign('id', $blog['id']);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('round_corners_js', '$.jcorners(".blog_comments",{radius:10});');
    $smarty->display('com_blog_view.tpl');

}
////////// BLOG MODERATE ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='moderate'){
	$error = '';
						
    //�������� ID ������������
    $user_id = $inUser->id;

    //���� ������������ �����������, ��������� �������� �� �� �������� �����, ����������� ��� �������
	if ($user_id){
		if ($owner=='user'){
			$myblog     = ($user_id == $blog['user_id']);
			$is_admin   = $inCore->userIsAdmin($user_id);
		} elseif ($owner=='club') {
			$myblog     = clubUserIsRole($blog['user_id'], $user_id, 'member') || clubUserIsRole($blog['user_id'], $user_id, 'moderator') || clubUserIsAdmin($blog['user_id'], $user_id);
			$is_admin   = $inCore->userIsAdmin($user_id) || clubUserIsAdmin($blog['user_id'], $user_id);
		}
	}

    //��������� ��� ��� �� ��������������
    if (!$user_id) {
		$myblog     = false;
		$is_admin   = false;
	}

    //��������� ���������� �������
    if (!$myblog && !$is_admin){
		echo '<p style="color:red">������ ��������</p>';
        return;
	}

    //������������� ���������� � ��������� ��������
    if ($owner=='club') { $inPage->addPathway($blog['author'], '/clubs/'.$menuid.'/'.$blog['user_id']); }
    $inPage->addPathway($blog['title'], '/blogs/'.$menuid.'/'.$id.'/blog.html');
    $inPage->addPathway('������ �� ���������', $_SERVER['REQUEST_URI']);
    $inPage->setTitle('��������� - '.$blog['title']);

    //������� ����� �������, ��������� ���������
    $total = $model->getModerationCount($blog['id']);

    //���� ������� ���, ���������� �� ������� �����
    if (!$total){
        $inCore->redirect('/blogs/'.$menuid.'/'.$blog['id'].'/blog.html');
    }

    //�������� ��������� ������ �� ����
    $posts_list = $model->getModerationPosts($blog['id']);
    $records    = sizeof($posts_list);

    //��������, ���� ������� ���
    if (!$records){
        echo '<p style="clear:both">��� ������� �� ���������.</p>';
        return;
    }

    //��������� ������
    $posts = array();
    foreach($posts_list as $post){
        $msg                = $post['content'];
        $msg                = $inCore->parseSmiles($msg, true);
        $msg                = str_replace("&amp;", '&', $msg);
        $post['msg']        = $msg;
        $post['tagline']    = cmsTagLine('blogpost', $post['id']);
        $posts[]            = $post;
    }

    //������� ������
    $smarty = $inCore->initSmarty('components', 'com_blog_moderate.tpl');
        $smarty->assign('myblog', $myblog);
        $smarty->assign('total', $total);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('id', $id);
        $smarty->assign('uid', $user_id);
        $smarty->assign('posts', $posts);
        $smarty->assign('blog', $blog);
    $smarty->display('com_blog_moderate.tpl');

}
////////// ������ ����� ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='authors'){

    //������������� ��������� ��������
	$inPage->setTitle($blog['title'].' - ������');

    $authors        = array();

    $authors_list   = $model->getBlogAuthors($id);

    //��������� �������
	if ($authors_list){
		foreach($authors_list as $author) {
            //���������� ������
			$author['avatar']   = $author['imageurl'] ? $author['imageurl'] : 'nopic.jpg';
            //��������� � ������
            $authors[]          = $author;
		}
	}

    $is_authors     = sizeof($authors);

    //������� ������
    $smarty = $inCore->initSmarty('components', 'com_blog_authors.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('blog', $blog['title']);
        $smarty->assign('is_authors', $is_authors);
        $smarty->assign('authors', $authors);
    $smarty->display('com_blog_authors.tpl');
    
}

////////// ����� ���� / �������������� ����� //////////////////////////////////////////////////////////////////
if ($do=='newpost' || $do=='editpost'){

	//�������� ID ������������
    $user_id    = $inUser->id;

   	$post_id 	= $inCore->request('post_id', 'int', 0);

    if (!$user_id){
		$inCore->redirectBack();
	}

    //�������� ����� ������������
	$user_karma = cmsUser::getKarma($user_id);

    $post = array();

    //���������� ������� ������� � ����� (�����, ������, �����) � ����������� �� ���� ���������
    if ($owner=='user'){
        $myblog     = ($user_id == $blog['user_id']);
		$is_author  = $model->isUserAuthor($blog['id'], $user_id) || ($blog['ownertype']=='multi' && $blog['forall']);
        $is_admin   = $inCore->userIsAdmin($user_id);
        $min_karma  = false;
    }
    if ($owner=='club') {
        $myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
        $is_author  = clubUserIsRole($blog['user_id'], $user_id, 'member');
        $is_admin   = $inCore->userIsAdmin($user_id) || clubUserIsAdmin($blog['user_id'], $user_id);
        $min_karma  = $model->getClubBlogMinKarma($blog['user_id']);
        $inPage->addPathway($blog['author'], '/clubs/'.$menuid.'/'.$blog['user_id']);
    }

    //���������, ������� �� �����
    if ( !($min_karma === false || $user_karma>=$min_karma || clubUserIsAdmin($blog['user_id'], $user_id) || clubUserIsRole($blog['user_id'], $user_id, 'moderator')) ){
        $inPage->printHeading('�� ������� �����');
        echo '<p><strong>� ��� �� ������� ����� ��� �������� ����� � ���� �����.</strong></p>';
        echo '<p>��������� '.$min_karma.', � ������� ������ '.$user_karma.'.</p>';
        echo '<p>������ ���������� <a href="/users/'.$menuid.'/'.$user_id.'/karma.html">������� ����� �����</a>?</p>';
        return;
    }

    //��� ������ �����
	if ($do=='newpost'){
        //��������� ������
		if (!$myblog && !$is_author && !$is_admin) { $inCore->redirectBack(); }
        //������������� ���������
        $inPage->addPathway('����� ������', $_SERVER['REQUEST_URI']);
		$inPage->setTitle('����� ������');
		$inPage->printHeading('����� ������');
	} 

    //��� �������������� �����
    if ($do=='editpost'){
        //��������� ������
        $is_post_author = $model->isUserPostAuthor($post_id, $user_id);
		if (!$myblog && !$is_post_author && !$is_admin) { $inCore->redirectBack(); }
        //������������� ���������
        $inPage->addPathway('������������� ������');
		$inPage->setTitle('������������� ������');
		$inPage->printHeading('������������� ������');
        //�������� �������� ���� �� ����
        $post = $model->getPost($post_id);
        if (!$post){ $inCore->redirectBack(); }
	}

	$inPage->addPathway($blog['title'], '/blogs/'.$menuid.'/'.$blog['id'].'/blog.html');
	$inPage->initAutocomplete();

    //������� ������������� ������ � ����������� ������������
    $inCore->flushUpload();

    //���� ��� �� ���� ������� �� ����������
    if ( !$inCore->inRequest('goadd') ){

        //�������� ������� �����
        $cat_list   = blogCategoryList($post['cat_id'], $id);

        //�������� ��� ������� bbcode � �������
        $bb_toolbar = cmsPage::getBBCodeToolbar('message',$cfg['img_on'], 'blog');
        $smilies    = cmsPage::getSmilesPanel('message');

        //�������������� ����� �����, ���� ���� ��������
        if (isset($post['content'])){
            $msg = $post['content'];
            $msg = str_replace('&amp;', "&", $msg);
            $msg = str_replace('<br/>', "\n", $msg);
            $msg = str_replace('<br />', "\n", $msg);
            $msg = str_replace('<br>', "\n", $msg);
         } else {
            $msg = '';
         }

        $inCore->initAutoGrowText('#message');
        $inPage->backButton(false);

        //��������� ���� �����, ���� �� ��� ��������
        $tagline = isset($post['id']) ? cmsTagLine('blogpost', $post['id'], false) : '';

        $autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');

        //���������� �����
        $smarty = $inCore->initSmarty('components', 'com_blog_edit_post.tpl');
            $smarty->assign('blog', $blog);
            $smarty->assign('mod', $post);
            $smarty->assign('cat_list', $cat_list);
            $smarty->assign('bb_toolbar', $bb_toolbar);
            $smarty->assign('smilies', $smilies);
            $smarty->assign('autogrow', $autogrow);
            $smarty->assign('msg', $msg);
            $smarty->assign('tagline', $tagline);
            $smarty->assign('autocomplete_js', $autocomplete_js);
        $smarty->display('com_blog_edit_post.tpl');

    }

    //���� ���� ������ �� ����������
    if ( $inCore->inRequest('goadd') ) {
        $error_msg = '';;

        //�������� ���������
        $title 		= $inCore->request('title', 'str');
        $content 	= $inCore->request('content', 'html');
        $feel 		= $inCore->request('feel', 'str', '');
        $music 		= $inCore->request('music', 'str', '');
        $cat_id 	= $inCore->request('cat_id', 'int');
        $allow_who 	= $inCore->request('allow_who', 'int');
        $tags 		= $inCore->request('tags', 'str', '');

        //��������� ��
        if (strlen($title)<2) { $error_msg .= '������� ��������� ������! �������� ������ ���� �� ������ 2-� ��������.<br/>'; }
        if (strlen($content)<5) { $error_msg .= '������� ����� ������! ����� ������ ���� �� ������ 5-�� ��������.<br/>'; }

        //���� ������� ������ - ���������� � �������
        if($error_msg) {
            $inPage->setTitle('������ �������� ������!');
            $inPage->printHeading('������ �������� ������!');
            echo '<p style="color:red">'.$error_msg.'</p>';
            return;
        }

        //���� ������ �� ������� 
        if(!$error_msg){
            //��������� ����� ����...
            if ($do=='newpost'){

                if ($blog['owner']=='user'){
                    if ($myblog || (!$blog['premod'])){	$published = 1;	} else { $published = 0; }
                }

                if ($blog['owner']=='club'){
                    $club_premod = dbGetField('cms_clubs', 'id='.$blog['user_id'], 'blog_premod');
                    $published = 0;
                    if ($inCore->userIsAdmin($inUser->id) || clubUserIsRole($blog['user_id'], $inUser->id, 'moderator') || clubUserIsAdmin($blog['user_id'], $inUser->id) || (!$club_premod)){
                        $published = 1;
                    }
                }

                $post_id = $model->addPost(array(
                                                    'user_id'=>$user_id,
                                                    'cat_id'=>$cat_id,
                                                    'id'=>$id,
                                                    'title'=>$title,
                                                    'feel'=>$feel,
                                                    'music'=>$music,
                                                    'content'=>$content,
                                                    'allow_who'=>$allow_who,
                                                    'published'=>$published,
                                                    'tags'=>$tags
                                                 ));

                $inCore->registerUploadImages(session_id(), $post_id, 'blog');
                cmsUser::checkAwards($user_id);

                if ($published) {
                    $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/post'.$post_id.'.html');
                }

                if (!$published) {
                    $inPage->backButton(false);
                    $inPage->printHeading('������ ������� ���������');
                    echo '<p>������ ����� ������������ � ����� ����� �������� ���������������.</p>';
                    echo '<p><a href="/blogs/'.$menuid.'/'.$blog['id'].'/blog.html">����������</a> &rarr;</p>';
                    return;
                }
            }

            //...��� ��������� ���� ����� ��������������
            if ($do=='editpost') {

                $model->updatePost($post_id, array(
                                                    'cat_id'=>$cat_id,
                                                    'title'=>$title,
                                                    'feel'=>$feel,
                                                    'music'=>$music,
                                                    'content'=>$content,
                                                    'allow_who'=>$allow_who,
                                                    'published'=>$published,
                                                    'tags'=>$tags
                                                 ));

                $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/blog.html');
            }
        } 
    }

}
////////// ����� ������� / �������������� ������� //////////////////////////////////////////////////////
if ($do=='newcat' || $do=='editcat'){

    $cat_id 	= $inCore->request('cat_id', 'int', 0);

    //��������� �����������
	if (!$inUser->id) { $inCore->redirectBack(); }
    //��������� ��� �� ���� ������ ��� �������� ������ � �����
	if ( $error ) { $inCore->redirectBack(); }

	$user_id    = $inUser->id;
    $cat        = array();

    //��������� ��� ������������ �������� �������� ��� ����������� �����
	if ($owner=='user') { 		
		$myblog = ($blog['user_id']==$user_id) || $inUser->is_admin;
	}
    if ($owner=='club'){
		$myblog = clubUserIsRole($blog['user_id'], $user_id, 'moderator') || clubUserIsAdmin($blog['user_id'], $user_id);
	}
	if (!$myblog) { $inCore->redirectBack(); }
	
	$inPage->addPathway($blog['title'], '/blogs/'.$menuid.'/'.$blog['id'].'/blog.html');

    //����� �������
	if ($do=='newcat'){
        //������������� ��������� � ����������
		$inPage->addPathway('����� �������');
		$inPage->setTitle('����� �������');
        $inPage->printHeading('����� �������');
	}
    //�������������� �������
    if ($do=='editcat'){
        //������������� ��������� � ����������
		$inPage->addPathway('������������� ������');
		$inPage->setTitle('������������� �������');
		$inPage->printHeading('������������� �������');
        //��������� �������
        $cat    = $model->getBlogCategory($cat_id);
        if (!$cat) {
			$inCore->redirectBack();
		}
	}

    //���� ��� ������� �� ����������
    if (!$inCore->inRequest('goadd')){
        $inPage->backButton(false);
        //���������� �����
        $smarty = $inCore->initSmarty('components', 'com_blog_edit_cat.tpl');
        $smarty->assign('mod', $cat);
        $smarty->display('com_blog_edit_cat.tpl');
    }

    //���� ���� ������ �� ����������
    if ($inCore->inRequest('goadd')){
        $error_msg = '';;
        //�������� � ��������� �������� �������
        $title = $inCore->request('title', 'str');
        if (strlen($title)<2) { $error_msg .= '������� ��������� �������! �������� ������ ���� �� ������ 2-� ��������.<br/>'; }

        //���� ���� ������
        if ($error_msg){
            $inPage->setTitle('������ �������� �������!');
            $inPage->printHeading('������ �������� �������!');
            echo '<p style="color:red">'.$error_msg.'</p>';
            return;
        }

        //���� �� ���� ������
        if(!$error_msg){
            //����� �������
            if ($do=='newcat'){
                $model->addBlogCategory(array('id'=>$id, 'title'=>$title));
            }
            //�������������� �������
            if ($do=='editcat'){
                $model->updateBlogCategory($cat_id, array('title'=>$title));
            }
            $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/blog.html');
        }

    }

}
////////////////////////// �������� ����� /////////////////////////////////////////////////////////////////////////
if($do=='post'){

	$post_id 	= $inCore->request('post_id', 'int', 0);
    $user_id    = $inUser->id;

	if($owner=='user'){	
		$inPage->addPathway($blog['title'], $model->getBlogURL($menuid, $blog['seolink']));
	}
    if ($owner=='club'){
		$inPage->addPathway($blog['author'], '/clubs/'.$menuid.'/'.$blog['user_id']);	
		$inPage->addPathway('����', '/blogs/'.$menuid.'/'.$blog['id'].'/blog.html');	
		$blog['title'] 		= $blog['author'];
		$blog['author'] 	= clubAdminLink($blog['user_id']);
	}
	
    if ($post_id) { $post = $model->getPost($post_id); }
    if ($seolink) { $post = $model->getPostByLink($seolink); }

    if (!$post){
		$inPage->printHeading('������ �� �������');
        echo '<p>�������� ��� ���� ������� ��� ����������.</p>'; return;
	}

    $can_view = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));

    if (!$can_view){
        $inPage->printHeading('�������� ������');
        echo '<p>������������ ��������� ������ � ���� ������ ��������.</p>'; return;
    }

    $post['fpubdate'] = cmsCore::dateDiffNow($post['fpubdate']).' ����� ('.$post['fpubdate'].')';
    $post['feditdate'] = cmsCore::dateDiffNow($post['feditdate']).' �����';

    if ($post['cat_id']){
        $cat = $model->getBlogCategory($post['cat_id']);
        $cat = $cat['title']; 
    } else { 
        $cat = '��� �������';
    }

    $inPage->setTitle($post['title']);
    $inPage->addPathway($post['title']);

    //������ bb-���
    $msg = $post['content'];
    $msg = $inCore->parseSmiles($msg, true);
    $msg = str_replace("&amp;", '&', $msg);

    //������� ��� [cut]
    $regex  = '/\[(cut=)\s*(.*?)\]/i';
    $msg    = preg_replace($regex, '', $msg);

    if (sizeof($inCore->strClear($msg))>30){
        $keywords = $inCore->getKeywords($inCore->strClear($msg));
        $inPage->setKeywords($keywords);
    }

    $post['author'] = cmsUser::getGenderLink($post['author_id'], $post['author']);

    //display post
    $smarty = $inCore->initSmarty('components', 'com_blog_view_post.tpl');
        $smarty->assign('post', $post);
        $smarty->assign('blog', $blog);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('id', $id);
        $smarty->assign('cat', $cat);
        $smarty->assign('karma_form', cmsKarmaForm('blogpost', $post['id']));
        $smarty->assign('msg', $msg);
        $smarty->assign('nav', blogPostNav($post['pubdate'], $id));
        $smarty->assign('tag_bar', cmsTagBar('blogpost', $post['id']));
    $smarty->display('com_blog_view_post.tpl');

    //show user comments
    if($inCore->isComponentInstalled('comments')){
        $inCore->includeComments();
        comments('blog', $post['id']);
    }

}
///////////////////////// DELETE POST /////////////////////////////////////////////////////////////////////////////
if ($do == 'delpost'){

    $post_id 	= $inCore->request('post_id', 'int', 0);
    $user_id    = $inUser->id;

    if (!$user_id || !$post_id) { $inCore->redirectBack(); }

    $post = $model->getPost($post_id);

    if (!$post){ $inCore->redirectBack(); }

    if ($owner=='user'){
        $myblog     = ($user_id == $blog['user_id']);
        $is_author  = (((!$myblog) && $inDB->get_field('cms_blog_authors', 'blog_id='.$id.' AND user_id='.$user_id, 'id')) || $blog['forall']);
    }
    if($owner=='club') {
        $myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
        $is_author  = clubUserIsRole($blog['user_id'], $user_id, 'member');
    }

    if ( !$inCore->inRequest('confirm') ) {
        //MENU
        if ($myblog || $is_author || $post['user_id']==$user_id || $inUser->is_admin){
            $inPage->setTitle('�������� ������');
            $inPage->backButton(false);
            $confirm['title'] = '�������� ������';
            $confirm['text'] = '�� ������������� ������ ������� ������ "<a href="/blogs/'.$menuid.'/'.$id.'/post'.$post_id.'.html">'.$post['title'].'</a>" �� �����?';
            $confirm['action'] = 'javascript:void(0);';
            $confirm['yes_button'] = array();
            $confirm['yes_button']['type'] = 'button';
            $confirm['yes_button']['onclick'] = "window.location.href='/blogs/".$menuid."/".$id."/delpost".$post_id."-yes.html';";
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
        } else {
            $inCore->redirectBack();
        }
    }

    if ( $inCore->inRequest('confirm') ){
        if ($myblog || $is_author || $post['user_id']==$user_id || $inUser->is_admin){
            
            $model->deletePost($post_id);

            if ($user_id != $post['user_id']){
                if ($blog['owner']=='club') { $blog['title'] = dbGetField('cms_clubs', 'id='.$blog['user_id'], 'title'); }
                cmsUser::sendMessage(-1, $post['user_id'], '���� ������ <b>&laquo;'.$post['title'].'&raquo;</b> ���� ������� �� ����� <b>&laquo;<a href="/blogs/'.$menuid.'/'.$blog['id'].'/blog.html">'.$blog['title'].'</a>&raquo;</b>');
            }
        }
        $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/blog.html');
    }

}
///////////////////////// PUBLISH POST /////////////////////////////////////////////////////////////////////////////
if ($do == 'publishpost'){
	$inPage->backButton(false);
    $user_id = $inUser->id;

	if (!$user_id){ $inCore->halt(); }

    if ($post_id){
        $post   = $model->getPost($post_id);
        if ($post){
            $model->publishPost($post_id);
            if ($blog['owner']=='club') { $blog['title'] = $inDB->get_field('cms_clubs', 'id='.$blog['user_id'], 'title'); }
            cmsUser::sendMessage(-1, $post['author_id'], '���� ������ <b>&laquo;<a href="/blogs/'.$menuid.'/'.$blog['id'].'/post'.$post['id'].'.html">'.$post['title'].'</a>&raquo;</b> ������������ � ����� <b>&laquo;<a href="/blogs/'.$menuid.'/'.$blog['id'].'/blog.html">'.$blog['title'].'</a>&raquo;</b>');
        }
    }
    
    $inCore->redirect('/blogs/'.$menuid.'/'.$blog['id'].'/moderate.html');
    
}

///////////////////////// DELETE BLOG /////////////////////////////////////////////////////////////////////////////
if ($do == 'delblog'){
	$inPage->backButton(false);

    $user_id = $inUser->id;

    if (!$user_id){ $inCore->halt(); }

    $blog = $model->getBlog($id);

    if (!$blog){ $inCore->halt(); }

    if ( $inCore->inRequest('confirm') ){
        if ($user_id == $data['user_id'] || $inCore->userIsAdmin($user_id)){
            $model->deleteBlog($id);
        }        
    }

    if ( !$inCore->inRequest('confirm') ){
        if ($user_id == $blog['user_id'] || $inCore->userIsAdmin($user_id)){
            $inPage->setTitle('�������� �����');
            $confirm['title']                   = '�������� �����';
            $confirm['text']                    = '�� ������������� ������ ������� ���� ����?';
            $confirm['action']                  = 'javascript:void(0);';
            $confirm['yes_button']              = array();
            $confirm['yes_button']['type']      = 'button';
            $confirm['yes_button']['onclick']   = "window.location.href='/blogs/".$menuid."/".$id."/delblog-yes.html';";
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
            return;
        }
    }

    $inCore->redirect('/blogs/'.$menuid);

}

///////////////////////// DELETE CAT /////////////////////////////////////////////////////////////////////////////
if ($do == 'delcat'){
	$inPage->backButton(false);
    
    $user_id    = $inUser->id;
    $cat_id 	= $inCore->request('cat_id', 'int', 0);

    if (!$user_id){ $inCore->halt(); }
    if (!$cat_id){ $inCore->halt(); }

    if($blog['owner']=='user'){
        $sql = "SELECT c.*, u.id as user_id
                FROM cms_blog_cats c, cms_blogs b, cms_users u
                WHERE c.id = $cat_id AND c.blog_id = b.id AND b.user_id = u.id
                LIMIT 1";
    }
    if($blog['owner']=='club'){
        $sql = "SELECT c.*
                FROM cms_blog_cats c, cms_blogs b
                WHERE c.id = $cat_id AND c.blog_id = b.id
                LIMIT 1";
    }

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)){
        $data = $inDB->fetch_assoc($result);

        if($blog['owner']=='user'){
            $can_delete = $inCore->userIsAdmin($user_id) || $user_id == $data['user_id'];
        }
        if($blog['owner']=='club'){
            $can_delete = $inCore->userIsAdmin($user_id) || clubUserIsAdmin($blog['user_id'], $user_id) || clubUserIsRole($blog['user_id'], $user_id, 'moderator');
        }

        if ($inCore->inRequest('confirm')){
            if ($can_delete){
                $model->deleteBlogCategory($cat_id);
            }
            $inCore->redirect('/blogs/'.$menuid.'/'.$id.'/blog.html');
        }

        if (!$inCore->inRequest('confirm')){
            if ($can_delete){
                $inPage->setTitle('�������� �������');
                $confirm['title'] = '�������� �������';
                $confirm['text'] = '<p>�� ������������� ������ ������� ������� "<a href="/blogs/'.$menuid.'/'.$id.'/blog'.$cat_id.'.html">'.$data['title'].'</a>" �� �����?</p><p>��� ������ �� ���� ������� ����� ������� ��� ������������ ��������������.</p>';
                $confirm['action'] = 'javascript:void(0);';
                $confirm['yes_button'] = array();
                $confirm['yes_button']['type'] = 'button';
                $confirm['yes_button']['onclick'] = "window.location.href='/blogs/".$menuid."/".$id."/delcat".$cat_id."-yes.html';";
                $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
                $smarty->assign('confirm', $confirm);
                $smarty->display('action_confirm.tpl');
            } else { usrAccessDenied(); }
        }
    }

}

////////// VIEW LATEST POSTS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='latest'){

	$smarty     = $inCore->initSmarty('components', 'com_blog_view_posts.tpl');
	$error      = '';
				
	$user_id    = $inUser->id;
	$can_view   = true;

    $posts      = array();

    if ($error) {
        echo '<p style="color:red">'.$error.'</p>';
        return;
    }

	if (!$error){

        //������� ���������� ������������ � ������������ ������
        $single_blogs	= $model->getSingleBlogsCount();
        $multi_blogs 	= $model->getMultiBlogsCount();

		$is_admin = $inCore->userIsAdmin($user_id);
	
		//TITLES
		$inPage->setTitle('����� ������');
		$inPage->addPathway('����� ������');

		//PAGINATION
		$perpage = isset($cfg['perpage']) ? $cfg['perpage'] : 10;
		$page = $inCore->request('page', 'int', 1);
							
        $total = $model->getLatestCount();
					
        //GET ENTRIES
        $posts_list = $model->getLatestPosts($page, $perpage);

        //PAGINATION
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%menuid%/latest-%page%.html', array('menuid'=>$menuid));

        //FETCH ENTRIES
        if ($posts_list){
            foreach($posts_list as $post){
                $can_view = ($post['blog_allow_who']=='all' || ($post['blog_allow_who']=='friends' && usrIsFriends($post['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));
                if ($can_view){

                    $post['url']        = $model->getPostURL($menuid, $post['bloglink'], $post['seolink']);
                    $post['comments']   = $inDB->rows_count('cms_comments', "target='blog' AND target_id=".$post['id']);
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);

                    $post['blog_url']   = $model->getBlogURL($menuid, $post['bloglink']);

                    $msg = $post['content'];
                    $msg = $inCore->parseSmiles($msg, true);
                    $msg = str_replace("&amp;", '&', $msg);

                    //��������� ����� ����� �� 2 ����� �� ���� [cut=...] � ��������� ������ ������ �� ���
                    if (strstr($msg, '[cut')){
                        $msg = $model->getPostShort($msg, $post['url']);
                    }

                    $post['msg']        = $msg;
                    $post['tagline']    = cmsTagLine('blogpost', $post['id']);
                    $post['author']     = cmsUser::getGenderLink($post['author_id'], $post['author']);
                    $posts[]            = $post;
                }
            }
        }

        $smarty->assign('is_posts', (bool)sizeof($posts));
        $smarty->assign('is_latest', (bool)sizeof($posts));
        $smarty->assign('pagetitle', '�����');
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('total', $total);
        $smarty->assign('menuid', $menuid);
        $smarty->assign('uid', $user_id);

        $smarty->assign('single_blogs', $single_blogs);
        $smarty->assign('multi_blogs', $multi_blogs);

        if ($posts) { $smarty->assign('posts', $posts); }
        if ($pagination) { $smarty->assign('pagination', $pagination); }

        $smarty->assign('id', $id);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('round_corners_js', '$.jcorners(".blog_comments",{radius:10});');

        $smarty->display('com_blog_view_posts.tpl');

    }

}
////////// VIEW POPULAR POSTS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){

	$smarty = $inCore->initSmarty('components', 'com_blog_view_posts.tpl');
	$error = '';
				
	$user_id = $inUser->id;
	$can_view = true;

    $posts = array();

    if ($error) {
        echo '<p style="color:red">'.$error.'</p>';
        return;
    }
	
	if (!$error){

		$is_admin = $inCore->userIsAdmin($user_id);
	
		//TITLES
		$inPage->setTitle('���������� � ������');
		$inPage->addPathway('���������� � ������');

		//PAGINATION
		$perpage    = isset($cfg['perpage']) ? $cfg['perpage'] : 20;
		$page       = $inCore->request('page', 'int', 1);
							
		//COUNT ENTRIES
        $total = $model->getBestCount();
        
        //GET ENTRIES
        $posts_list = $model->getBestPosts($page, $perpage);

        //PAGINATION
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%menuid%/popular-%page%.html', array('menuid'=>$menuid));

        //FETCH ENTRIES
        if ($posts_list){
            foreach($posts_list as $post){
                $can_view = ($post['blog_allow_who']=='all' || ($post['blog_allow_who']=='friends' && usrIsFriends($post['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));
                if ($can_view){
                    $post['url']        = $model->getPostURL($menuid, $post['bloglink'], $post['seolink']);

                    $post['comments']   = $inDB->rows_count('cms_comments', "target='blog' AND target_id=".$post['id']);
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);

                    $msg = $post['content'];
                    $msg = $inCore->parseSmiles($msg, true);
                    $msg = str_replace("&amp;", '&', $msg);

                    //��������� ����� ����� �� 2 ����� �� ���� [cut=...] � ��������� ������ ������ �� ���
                    if (strstr($msg, '[cut')){
                        $msg = $model->getPostShort($msg, $post['url']);
                    }

                    $post['msg']        = $msg;
                    $post['tagline']    = cmsTagLine('blogpost', $post['id']);
                    $post['author']     = cmsUser::getGenderLink($post['author_id'], $post['author']);
                    $posts[]            = $post;
                }
            }
        }
    }

    $smarty->assign('is_posts', (bool)sizeof($posts));

    $smarty->assign('pagetitle', '���������� � ������');
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('total', $total);
    $smarty->assign('menuid', $menuid);
    $smarty->assign('uid', $user_id);
    if (@$posts) { $smarty->assign('posts', $posts); }
    if (@$pagination) { $smarty->assign('pagination', $pagination); }
    $smarty->assign('id', $id);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('round_corners_js', '$.jcorners(".blog_comments",{radius:10});');
    $smarty->display('com_blog_view_posts.tpl');

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
?>