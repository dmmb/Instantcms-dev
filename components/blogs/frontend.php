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

function blogs(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    
	$inCore->includeFile("components/users/includes/usercore.php");
	$inCore->includeFile("components/blogs/includes/blogcore.php");

	$inCore->loadLib("users");
	$inCore->loadLib("tags");
	$inCore->loadLib('clubs');
	$inCore->loadLib('karma');
	
	$inPage->addHeadJS('includes/jquery/jquery.jcorners.js');

    global $_LANG;

    $inCore->loadModel('blogs');
    $model = new cms_model_blogs();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
		
	//Загрузка настроек блогов
	$cfg = $inCore->loadComponentConfig('blogs');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }
	
	//Значения настроек по-умолчанию
	$cfg['fa_ext'] = 'gif jpeg jpg png bmp';		
	if (!isset($cfg['rss_all'])) { $cfg['rss_all'] = 1; }
	if (!isset($cfg['rss_one'])) { $cfg['rss_one'] = 1; }
    if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; }
    if (!isset($cfg['update_date'])) { $cfg['update_date'] = 1; }
	if (!isset($cfg['update_seo_link'])) { $cfg['update_seo_link'] = 0; }
	if (!isset($cfg['update_seo_link_blog'])) { $cfg['update_seo_link_blog'] = 0; }
	
	//Получаем параметры
	$id 		= $inCore->request('id', 'int', 0);	
	$bloglink   = $inCore->request('bloglink', 'str', '');
	$seolink    = $inCore->request('seolink', 'str', '');
	$do 		= $inCore->request('do', 'str', 'latest');

    //Определяем тип хозяина блога, к которому обращаемся
	if ($inCore->inRequest('ownertype')){
		$ownertype = $inCore->request('ownertype', 'str'); 
		$_SESSION['blogs_ownertype'] = $ownertype;
	} else { 
		if (isset($_SESSION['blogs_ownertype'])){
			$ownertype = $_SESSION['blogs_ownertype'];
		} else { $ownertype = 'all'; }
	}

    //Если нужно, загружаем данные об указанном во входных параметрах блоге
	if ($do!='view'){

        if ($bloglink){
            $blog   = $model->getBlogByLink($bloglink);
            if (!$blog) { cmsCore::error404(); }
        }
        
        if ($id){
            $blog   = $model->getBlog($id);
            if (!$blog) { cmsCore::error404(); }
        }
    
		if ($blog){
			$owner = $blog['owner'];
			if ($owner=='user') { $blog['author'] = $inDB->get_field('cms_users', 'id='.$blog['user_id'], 'nickname');	}
			if ($owner=='club') { $blog['author'] = $inDB->get_field('cms_clubs', 'id='.$blog['user_id'], 'title');       }
		}

	}

////////// СОЗДАНИЕ БЛОГА ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='create'){

    //Проверяем авторизацию
    if ( !$inUser->id ){ $inCore->redirectBack(); }

    //Если у пользователя уже есть блог, то выходим
    if ($model->getUserBlogId($inUser->id)) { $inCore->redirectBack(); }

	$inPage->addHeadJS('components/blogs/js/blog.js');
	$inPage->addPathway($_LANG['BLOGS'], '/blogs');
	$inPage->addPathway($_LANG['PATH_CREATING_BLOG']);

    //Показ формы создания блога
    if (!$inCore->inRequest('goadd')){

        if (IS_BILLING){ cmsBilling::checkBalance('blogs', 'add_blog'); }

        $inPage->setTitle($_LANG['CREATE_BLOG']);
        $inPage->backButton(false);

        //только для друзей, по-умолчанию?
        $friends = $usr['allow_who'] == 'friends' ? 1 : 0;

        //Учитываем ограничения по карме
        $min_karma_private  = '';
        $min_karma_public   = '';
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            if ($cfg['min_karma_private'] >0) { $min_karma_private  = '('.$_LANG['BLOG_KARMA_NEED'].' '.$cfg['min_karma_private'].')';	}
            if ($cfg['min_karma_public']  >0) { $min_karma_public   = '('.$_LANG['BLOG_KARMA_NEED'].' '.$cfg['min_karma_public'] .')';	}
        }

        //выводим форму
        $smarty = $inCore->initSmarty('components', 'com_blog_create.tpl');
        $smarty->assign('friends', $friends);
        $smarty->assign('min_karma_private', $min_karma_private);
        $smarty->assign('min_karma_public', $min_karma_public);
        $smarty->display('com_blog_create.tpl');
    }

    //Сам процесс создания блога
    if ($inCore->inRequest('goadd')){

        $error_msg = '';

        $user_id    = $inUser->id;
        $title      = $inCore->request('title', 'str');
        $allow_who  = $inCore->request('allow_who', 'str', 'all');
        $ownertype  = $inCore->request('ownertype', 'str', 'single');

        //Проверяем название
        if (strlen($title)<5) { $error_msg .= '<p>'.$_LANG['BLOG_ERR_TITLE'].'</p>'; }

        //Проверяем хватает ли кармы, но только если это не админ
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            $user_karma = cmsUser::getKarma($inUser->id);
            if ($ownertype=='single' && ($user_karma < $cfg['min_karma_private']))
                $error_msg .= '<p>'.$_LANG['BLOG_YOU_NEED'].' <a href="/users/'.$user_id.'/karma.html">'.$_LANG['BLOG_KARMS'].'</a> '.$_LANG['FOR_CREATE_PERSON_BLOG'].' &mdash; '.$cfg['min_karma_private'].', '.$_LANG['BLOG_HEAVING'].' &mdash; '.$user_karma.'.</p>';
            if ($ownertype=='multi' && ($user_karma < $cfg['min_karma_public']))
                $error_msg .= '<p>'.$_LANG['BLOG_YOU_NEED'].' <a href="/users/'.$user_id.'/karma.html">'.$_LANG['BLOG_KARMS'].'</a> '.$_LANG['FOR_CREATE_TEAM_BLOG'].' &mdash; '.$cfg['min_karma_public'].', '.$_LANG['BLOG_HEAVING'].' &mdash; '.$user_karma.'.</p>';
        }

        //Если ошибки не были найдены
        if(!$error_msg){
            //Добавляем блог в базу
            $blog_id = $model->addBlog(array('user_id'=>$user_id, 'title'=>$title, 'allow_who'=>$allow_who, 'ownertype'=>$ownertype));
            $blog_link = $inDB->get_field('cms_blogs', "id={$blog_id}", 'seolink');
            //регистрируем событие
            cmsActions::log('add_blog', array(
                'object' => $title,
                'object_url' => $model->getBlogURL(null, $blog_link),
                'object_id' => $blog_id,
                'target' => '',
                'target_url' => '',
                'target_id' => 0, 
                'description' => ''
            ));
            
            if (IS_BILLING){ cmsBilling::process('blogs', 'add_blog'); }

			cmsCore::addSessionMessage($_LANG['BLOG_CREATED_TEXT'], 'info');
			$inCore->redirect($model->getBlogURL(null, $blog_link));

        }

        //Если найдены ошибки
        if($error_msg){
			cmsCore::addSessionMessage($error_msg, 'error');
			$inCore->redirect('/blogs/createblog.html');
        }
    }
	

}
////////// НАСТРОЙКИ БЛОГА ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='config'){

    //Проверяем авторизацию пользователя
    if ( !$inUser->id ) { $inCore->redirectBack(); }

    //Получаем ID пользователя
    $user_id = $inUser->id;

	//Проверяем является пользователь хозяином блога или админом
    if ( $blog['user_id']!=$inUser->id && !$inUser->is_admin ) { $inCore->redirectBack(); }

    $inPage->addPathway($blog['title'], $model->getBlogURL(null, $blog['seolink']));
	$inPage->addPathway($_LANG['CONFIG_BLOG']);
    $inPage->addHeadJS('components/blogs/js/blog.js');

    //Если нет запроса на сохранение, показываем форму настроек блога
    if ( !$inCore->inRequest('goadd') ){
        $inPage->setTitle($_LANG['CONFIG_BLOG']);
        $inPage->printHeading($_LANG['CONFIG_BLOG']);
        $inPage->backButton(false);
        //Получаем список авторов блога
        $authors = blogAuthors($blog['id']);
        //Получаем ограничения по карме
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            if ($cfg['min_karma_private'] >0 && $blog['owner_type']!='single') { $min_karma_private = '('.$_LANG['BLOG_KARMA_NEED'].' '.$cfg['min_karma_private'].')';	} else { $min_karma_private = ''; }
            if ($cfg['min_karma_public']  >0 && $blog['owner_type']!='multi') { $min_karma_public = '('.$_LANG['BLOG_KARMA_NEED'].' '.$cfg['min_karma_public'] .')';	} else { $min_karma_public = ''; }
        }
        //Выводим форму
        $smarty = $inCore->initSmarty('components', 'com_blog_config.tpl');
        $smarty->assign('blog', $blog);
        $smarty->assign('id', $blog['id']);
        $smarty->assign('authors_list', cmsUser::getAuthorsList($authors));
        $smarty->assign('users_list', cmsUser::getUsersList(false, $authors));
        $smarty->assign('min_karma_private', $min_karma_private);
        $smarty->assign('min_karma_public', $min_karma_public);
        $smarty->display('com_blog_config.tpl');
    }

    //Если пришел запрос на сохранение
    if ( $inCore->inRequest('goadd') ){

        $error_msg 	= '';

        //Получаем настройки
        $title 		= $inCore->request('title', 'str');
        $allow_who 	= $inCore->request('allow_who', 'str', 'all');
        $ownertype 	= $inCore->request('ownertype', 'str', 'single');
        $premod		= $inCore->request('premod', 'int', 0);
        $forall 	= $inCore->request('forall', 'int', 1);
        $showcats 	= $inCore->request('showcats', 'int', 1);

        //Проверяем настройки
        if (strlen($title)<5) { $error_msg .= $_LANG['BLOG_ERR_TITLE']; }

        //Проверяем ограничения по карме (для смены типа блога)
        if ($cfg['min_karma'] && !$inCore->userIsAdmin($inUser->id)){
            $user_karma = cmsUser::getKarma($inUser->id);
            if ($ownertype=='single' && ($user_karma < $cfg['min_karma_private']))
                $error_msg .= '<p>'.$_LANG['BLOG_YOU_NEED'].' <a href="/users/'.$user_id.'/karma.html">'.$_LANG['KARMS'].'</a> '.$_LANG['FOR_CREATE_PERSON_BLOG'].' &mdash; '.$cfg['min_karma_private'].', '.$_LANG['BLOG_HEAVING'].' &mdash; '.$user_karma.'.</p>';
            if ($ownertype=='multi' && ($user_karma < $cfg['min_karma_public']))
                $error_msg .= '<p>'.$_LANG['BLOG_YOU_NEED'].' <a href="/users/'.$user_id.'/karma.html">'.$_LANG['KARMS'].'</a> '.$_LANG['FOR_CREATE_TEAM_BLOG'].' &mdash; '.$cfg['min_karma_public'].', '.$_LANG['BLOG_HEAVING'].' &mdash; '.$user_karma.'.</p>';
        }

        //Если ошибки не найдены
        if(!$error_msg){
            //Получаем новый список авторов блога
            $authors = $inCore->request('authorslist', 'array_int');
            //сохраняем авторов
            $model->updateBlogAuthors($blog['id'], $authors);
            //сохраняем настройки блога
            $blog['seolink_new'] = $model->updateBlog($blog['id'], array('title'=>$title, 'allow_who'=>$allow_who, 'showcats'=>$showcats, 'ownertype'=>$ownertype, 'premod'=>$premod, 'forall'=>$forall), $cfg['update_seo_link_blog']);
			
			cmsActions::updateLog('add_post', array('target' => $title), 0, $blog['id']);
			
            //Перенаправляем на главную страницу блога
			$blog_url = $cfg['update_seo_link_blog'] ? $model->getBlogURL(null, $blog['seolink_new']) : $model->getBlogURL(null, $blog['seolink']);
            $inCore->redirect($blog_url);
        }

        //Если найдены ошибки
        if($error_msg) {
			cmsCore::addSessionMessage($error_msg, 'error');
			$inCore->redirect('/blogs/'.$blog['id'].'/editblog.html');
        }

    }
	
}
////////// СПИСОК БЛОГОВ ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	// rss в адресной строке
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.$_LANG['BLOGS'].'" href="'.HOST.'/rss/blogs/all/feed.rss">');

    //Получаем номер страницы и число записей на одну страницу
    $perpage        = isset($cfg['perpage_blog']) ? $cfg['perpage_blog'] : 15;
    $page           = $inCore->request('page', 'int', 1);

    //Получаем ID пользователя
	$user_id 		= $inUser->id;

    //Считаем количество персональных и коллективных блогов
	$single_blogs	= $model->getSingleBlogsCount();
	$multi_blogs 	= $model->getMultiBlogsCount();
	$club_blogs 	= $model->getClubsBlogsCount();
	$total_blogs 	= $single_blogs + $multi_blogs + $club_blogs;

    //Получаем список блогов
    $blogs_list     = $model->getBlogs($ownertype, $page, $perpage);

	$blogs          = array();                        //Массив блогов для вывода
    $is_blogs       = $blogs_list ? true : false;     //Флаг, показывающий есть ли блоги

    //Получаем блоги и показываем все вне зависимости от прав доступа
    foreach($blogs_list as $blog){
            //Получаем ссылку на блог
            $blog['url']        = $model->getBlogURL(null, $blog['seolink']);
            //Считаем число комментариев
            $blog['comments']   = blogComments($blog['id']);
            //Форматируем значение кармы блога
            $blog['karma']      = cmsKarmaFormatSmall($blog['points']);
            //добавляем блог в список
            $blogs[]            = $blog;
	}	
    //Генерируем панель со страницами и устанавливаем заголовки страниц и глубиномера
	switch ($ownertype){
			case 'all': 	$inPage->setTitle($_LANG['ALL_BLOGS']);
							$inPage->addPathway($_LANG['ALL_BLOGS']);
							$pagination = cmsPage::getPagebar($total_blogs, $page, $perpage, '/blogs/all-%page%.html');
							break;
			case 'single':	$inPage->setTitle($_LANG['PERSONALS']);
							$inPage->addPathway($_LANG['PERSONALS']);
							$pagination = cmsPage::getPagebar($single_blogs, $page, $perpage, '/blogs/single-%page%.html');
							break;
			case 'multi':  	$inPage->setTitle($_LANG['COLLECTIVES']);
							$inPage->addPathway($_LANG['COLLECTIVES']);
							$pagination = cmsPage::getPagebar($multi_blogs, $page, $perpage, '/blogs/multi-%page%.html');
							break;
	}
	$inPage->setDescription($_LANG['BLOGS'].' - '.$_LANG['PERSONALS'].', '.$_LANG['COLLECTIVES']);
    //Выводим список блогов
	$smarty = $inCore->initSmarty('components', 'com_blog_view_all.tpl');				
	$smarty->assign('cfg', $cfg);
	$smarty->assign('single_blogs', $single_blogs);
	$smarty->assign('multi_blogs', $multi_blogs);
	$smarty->assign('total_blogs', $total_blogs);
	$smarty->assign('ownertype', $ownertype);
	$smarty->assign('is_admin', $inCore->userIsAdmin($user_id));
	$smarty->assign('blogs', $blogs);
	$smarty->assign('is_blogs', $is_blogs);	
	$smarty->assign('pagination', $pagination);
	$smarty->display('com_blog_view_all.tpl');

}
////////// ПРОСМОТР БЛОГА ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='blog'){

    $error = '';

    //Получаем ID пользователя
    $user_id = $inUser->id;

    $cat_id  = $inCore->request('cat_id', 'int', -1);

    //Подготавливаем шаблон
	$smarty = $inCore->initSmarty('components', 'com_blog_view.tpl');						
	
	//Если хозяин блога - пользователь
	if($owner=='user'){
        //Определяем, есть ли доступ к этому блогу
		$can_view       = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $blog['user_id']==$user_id || $inUser->is_admin);
        //Получаем html-код ссылки на автора с иконкой его пола
		$blog['author'] = cmsUser::getGenderLink($blog['user_id'], $blog['author'], null);
        //Устанавливаем заголовок страницы и глубиномер
		$inPage->setTitle($blog['title']);
		$inPage->addPathway($blog['title']);	
		$inPage->setDescription($blog['title']);
	}

    //Если хозяин блога - клуб
    if ($owner=='club'){
        $blog['club']   = $inDB->get_fields('cms_clubs', "id={$blog['user_id']}", 'title, clubtype');
        //Блоги клубов открыты не всегда и не для всех
		$can_view 		= $blog['club']['clubtype'] == 'public' || ($blog['club']['clubtype'] == 'private' && (clubUserIsMember($blog['user_id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($blog['user_id'], $inUser->id)));
        //Получаем заголовок блога и ссылку на профиль администратора
		$blog['title'] 	= '<a href="/clubs/'.$blog['user_id'].'">'.$blog['author'].'</a> &rarr; Блог';		
		$blog['author'] = clubAdminLink($blog['user_id']);        
        //Устанавливаем заголовок страницы и глубиномер
	    $inPage->setTitle($_LANG['BLOG'].' - '.$blog['club']['title']);
        $inPage->addPathway($blog['club']['title'], '/clubs/'.$blog['user_id']);
		$inPage->addPathway($_LANG['BLOG']);
		$inPage->setDescription($_LANG['BLOG'].' - '.$blog['club']['title']);
	}

    //Если доступа нет, добавляем сообщение об ошибке
	if ( !$can_view && $owner=='user' ) {
        $error = '<h1 class="con_heading">'.$_LANG['CLOSED_BLOG'].'</h1><p>'.$_LANG['CLOSED_BLOG_TEXT'] .'</p>';
    }
	if ( !$can_view && $owner=='club' ) {
        $error = '<h1 class="con_heading">'.$_LANG['CLOSED_BLOG'].'</h1><p>'.str_replace('%link%', '/clubs/'.$blog['user_id'], $_LANG['CLOSED_BLOG_CLUB_TEXT']).'</p>';
    }

    //Если есть ошибки - показываем и выходим
    if ($error){
		echo '<p style="color:red">'.$error.'</p>';
        return;
	}

    //Если пользователь не авторизован, запрещаем ему все
    if (!$user_id) {
        $myblog     = false;
        $is_author  = false;
        $is_config  = false;
    }

    //Если авторизован, проверяем является ли он хозяином блога или его администратором
    if ($user_id){
        if ($owner=='user'){
            $myblog     = ($inUser->id == $blog['user_id']);
			$is_moder   = false;
            $is_author  = (((!$myblog) && $blog['ownertype']=='multi' && $inDB->get_field('cms_blog_authors', 'blog_id='.$blog['id'].' AND user_id='.$user_id, 'id')) || ($blog['ownertype']=='multi' && $blog['forall']));
            $is_admin   = $inCore->userIsAdmin($user_id);
            $is_config  = $myblog || $is_admin;
        }
        if ($owner=='club'){
            $myblog     = false;
            $is_moder   = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
            $is_author  = clubUserIsRole($blog['user_id'], $user_id);
            $is_admin   = clubUserIsAdmin($blog['user_id'], $user_id) || $inCore->userIsAdmin($user_id);
            $is_config  = false;
        }
    }

    //Получаем номер страницы и число записей на одну страницу
    $perpage    = isset($cfg['perpage']) ? $cfg['perpage'] : 20;
    $page       = $inCore->request('page', 'int', 1);

    //Генерируем строку сообщения об авторах блога
    $authors_status = '';
    if ($blog['owner'] == 'user') {
        if ($blog['forall']){
            $authors_status = '<span class="blog_authorsall">'.$_LANG['BLOG_OPENED_FOR_ALL'].'</span>';
        } else {
            $authors_status = '<a href="/blogs/'.$blog['id'].'/authors.html" class="blog_authorslink">'.$_LANG['AVTORS_BLOG'].'</a>';
        }
    }

    //Считаем количество постов, ожидающих модерации
    $on_moderate = ($is_moder || $is_admin) ? $inDB->rows_count('cms_blog_posts', 'blog_id='.$blog['id'].' AND published = 0') : false;

    //Если нужно, получаем список рубрик (категорий) этого блога
    $blogcats   = $blog['showcats'] ? blogCats($blog['id'], $blog['seolink'], $cat_id) : false;

    //Считаем количество записей в этом блоге
    $total  = $model->getPostsCount($blog['id'], $cat_id, $owner);

    //Если нет записей
    if (!$total){
        $smarty->assign('is_posts', false);
    }

    //Если записи есть
    if ($total){

        $posts_list = $model->getPosts($blog['id'], $page, $perpage, $cat_id, $owner);

        //Генерируем панель со страницами
        if ($cat_id != -1){
            $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%bloglink%/page-%page%/cat-%catid%', array('bloglink'=>$blog['seolink'], 'catid'=>$cat_id));
        } else {
            $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/%bloglink%/page-%page%', array('bloglink'=>$blog['seolink']));
        }

        //Извлекаем записи
        if ($posts_list){
            $posts = array();
            foreach($posts_list as $post){
                //Проверяем можно ли показывать эту запись пользователю
                $can_view = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));
                if ($can_view){

                    $post['url']        = $model->getPostURL(null, $blog['seolink'], $post['seolink']);
                    $post['comments']   = $post['comments'] ? $inCore->getCommentsCount('blog', $post['id']) : false;
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);
                    
                    $msg                = $post['content_html'];

                    //Разбиваем текст поста на 2 части по тегу [cut=...] и оставляем только первую из них
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

        //Передаем в шаблон флаг о наличии записей
        $smarty->assign('is_posts', (bool)sizeof($posts));
    }

	// rss в адресной строке
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(strip_tags($blog['title'])).'" href="'.HOST.'/rss/blogs/'.$blog['id'].'/feed.rss">');

    //Выводим блог
    $smarty->assign('myblog', $myblog);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('is_moder', $is_moder);
    $smarty->assign('is_author', $is_author);
    $smarty->assign('is_config', $is_config);
    $smarty->assign('authors_status', $authors_status);
    $smarty->assign('on_moderate', $on_moderate);
    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('blogcats', $blogcats);
    $smarty->assign('total', $total);
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
						
    //Получаем ID пользователя
    $user_id = $inUser->id;

    //Если пользователь авторизован, проверяем является ли он хозяином блога, модератором или админом
	if ($user_id){
		if ($owner=='user'){
			$myblog     = $blog['user_id'] == $user_id;
			$is_admin   = $inCore->userIsAdmin($user_id);
		} elseif ($owner=='club') {
			$myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator') || clubUserIsAdmin($blog['user_id'], $user_id);
			$is_admin   = $inCore->userIsAdmin($user_id);
		}
	}

    //Запрещаем все для не авторизованных
    if (!$user_id) {
		$myblog     = false;
		$is_admin   = false;
	}

    //Устанавливаем глубиномер и заголовок страницы
    if ($owner=='club') { $inPage->addPathway($blog['author'], '/clubs/'.$blog['user_id']); }
    $inPage->addPathway($blog['title'], $model->getBlogURL(null, $blog['seolink']));
    $inPage->addPathway($_LANG['POSTS_ON_MODERATE'], $_SERVER['REQUEST_URI']);
    $inPage->setTitle($_LANG['MODERATING'].' - '.$blog['title']);

    //Проверяем отсутствие доступа
    if (!$myblog && !$is_admin){
		echo '<p style="color:red">'.$_LANG['ACCESS_DENIED'].'</p>';
        return;
	}

    //Считаем число записей, ожидающих модерации
    $total = $model->getModerationCount($blog['id']);

    //Если записей нет, редиректим на главную блога
    if (!$total){
        $inCore->redirect($model->getBlogURL(null, $blog['seolink']));
    }

    //Получаем ожидающие записи из базы
    $posts_list = $model->getModerationPosts($blog['id']);
    $records    = sizeof($posts_list);

    //Сообщаем, если записей нет
    if (!$records){
        echo '<p style="clear:both">'.$_LANG['NOT_POSTS_MODERATE'].'</p>';
        return;
    }

    //Извлекаем записи
    $posts = array();
    foreach($posts_list as $post){
        $post['msg']        = $post['content_html'];
        $post['tagline']    = cmsTagLine('blogpost', $post['id']);
		$post['fpubdate']	= $inCore->dateFormat($post['pubdate']);
        $post['url']        = $model->getPostURL(null, $post['bloglink'], $post['seolink']);
		//Разбиваем текст поста на 2 части по тегу [cut=...] и оставляем только первую из них
		if (strstr($post['msg'], '[cut')){
			$post['msg'] = $model->getPostShort($post['msg'], $post['url']);                        
		}
        $posts[]            = $post;
    }

    $blog['url'] = $model->getBlogURL(null, $blog['seolink']);

    //Выводим записи
    $smarty = $inCore->initSmarty('components', 'com_blog_moderate.tpl');
        $smarty->assign('myblog', $myblog);
        $smarty->assign('total', $total);
        $smarty->assign('id', $id);
        $smarty->assign('uid', $user_id);
        $smarty->assign('posts', $posts);
        $smarty->assign('blog', $blog);
    $smarty->display('com_blog_moderate.tpl');

}
////////// АВТОРЫ БЛОГА ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='authors'){

    //Устанавливаем заголовок страницы
	$inPage->setTitle($blog['title'].' - '.$_LANG['AVTORS']);

    $authors        = array();

    $authors_list   = $model->getBlogAuthors($id);

    //Извлекаем авторов
	if ($authors_list){
		foreach($authors_list as $author) {
            //Определяем аватар
			$author['avatar']   = $author['imageurl'] ? $author['imageurl'] : 'nopic.jpg';
            //Добавляем в список
            $authors[]          = $author;
		}
	}

    $is_authors     = sizeof($authors);

    //Выводим список
    $smarty = $inCore->initSmarty('components', 'com_blog_authors.tpl');
        $smarty->assign('blog', $blog['title']);
        $smarty->assign('is_authors', $is_authors);
        $smarty->assign('authors', $authors);
    $smarty->display('com_blog_authors.tpl');
    
}

////////// НОВЫЙ ПОСТ / РЕДАКТИРОВАНИЕ ПОСТА //////////////////////////////////////////////////////////////////
if ($do=='newpost' || $do=='editpost'){

	//Получаем ID пользователя
    $user_id    = $inUser->id;

   	$post_id 	= $inCore->request('post_id', 'int', 0);

    if (!$user_id){
		$inCore->redirectBack();
	}

    //Получаем карму пользователя
	$user_karma = cmsUser::getKarma($user_id);
	// Может ли пользователь выбирать возможность комментировать
	$user_can_iscomments = $inCore->isUserCan('comments/iscomments');

    $post = array();

    //Определяем уровень доступа к блогу (админ, хозяин, автор) в зависимости от типа владельца
    if ($owner=='user'){
		if ($do=='newpost'){
			$myblog  = $blog['user_id'] ==$user_id ;
		}
		if ($do=='editpost'){
        	$myblog  = $model->isUserBlogAuthor($blog['id'], $post_id, $blog['user_id']);
		}
		$is_author  = $model->isUserAuthor($blog['id'], $user_id) || ($blog['ownertype']=='multi' && $blog['forall']);
        $is_admin   = $inCore->userIsAdmin($user_id);
        $min_karma  = false;
    }
    if ($owner=='club') {
        $myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
        $is_author  = clubUserIsRole($blog['user_id'], $user_id, 'member');
        $is_admin   = $inCore->userIsAdmin($user_id) || clubUserIsAdmin($blog['user_id'], $user_id);
        $min_karma  = $model->getClubBlogMinKarma($blog['user_id']);
        $inPage->addPathway($blog['author'], '/clubs/'.$blog['user_id']);
    }

    //Проверяем, хватает ли кармы
    if ( !($min_karma === false || $user_karma>=$min_karma || clubUserIsAdmin($blog['user_id'], $user_id) || clubUserIsRole($blog['user_id'], $user_id, 'moderator')) ){
        $inPage->printHeading($_LANG['NEED_KARMA']);
        echo '<p><strong>'.$_LANG['NEED_KARMA_TEXT'].'</strong></p>';
        echo '<p>'.$_LANG['NEEDED'].' '.$min_karma.', '.$_LANG['HAVE_ONLY'].' '.$user_karma.'.</p>';
        echo '<p>'.$_LANG['WANT_SEE'].' <a href="/users/'.$user_id.'/karma.html">'.$_LANG['HISTORY_YOUR_KARMA'].'</a>?</p>';
        return;
    }

	$inPage->addPathway($blog['title'], $model->getBlogURL(null, $blog['seolink']));
	
    //для нового поста
	if ($do=='newpost'){
        //Проверяем доступ
		if (!$myblog && !$is_author && !$is_admin) { $inCore->redirectBack(); }

        if (IS_BILLING){ cmsBilling::checkBalance('blogs', 'add_post'); }
        
        //Устанавливаем заголовки
        $inPage->addPathway($_LANG['NEW_POST'], $_SERVER['REQUEST_URI']);
		$inPage->setTitle($_LANG['NEW_POST']);
		$inPage->printHeading($_LANG['NEW_POST']);
		
		$post['cat_id'] = $inCore->request('cat_id', 'int', 0);
	} 

    //для редактирования поста
    if ($do=='editpost'){
        //Проверяем доступ
        $is_post_author = $model->isUserPostAuthor($post_id, $user_id);
		if (!$myblog && !$is_post_author && !$is_admin) { $inCore->redirectBack(); }
        //Получаем исходный пост из базы
        $post = $model->getPost($post_id);
        if (!$post){ $inCore->redirectBack(); }
        //Устанавливаем заголовки
		$inPage->addPathway($post['title'], $model->getPostURL(null, $blog['seolink'], $post['postlink']));
        $inPage->addPathway($_LANG['EDIT_POST'], $_SERVER['REQUEST_URI']);
		$inPage->setTitle($_LANG['EDIT_POST']);
		$inPage->printHeading($_LANG['EDIT_POST']);
	}

	$inPage->initAutocomplete();

    //Удаляем промежуточные данные о загруженных изображениях
    $inCore->flushUpload();

    //Если еще не было запроса на сохранение
    if ( !$inCore->inRequest('goadd') ){

        //получаем рубрики блога
        $cat_list   = blogCategoryList($post['cat_id'], $id);

        //получаем код панелей bbcode и смайлов
        $bb_toolbar = cmsPage::getBBCodeToolbar('message',$cfg['img_on'], 'blogs');
        $smilies    = cmsPage::getSmilesPanel('message');

        $inCore->initAutoGrowText('#message');
        $inPage->backButton(false);

        //загружаем теги поста, если он сам загружен
        $tagline = isset($post['id']) ? cmsTagLine('blogpost', $post['id'], false) : '';

        $autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');

		if ($do=='newpost'){
			$post = cmsUser::sessionGet('mod');
			if ($post) { cmsUser::sessionDel('mod'); }
			if (!$post) { $post['comments'] = 1; }
		}

        //показываем форму
        $smarty = $inCore->initSmarty('components', 'com_blog_edit_post.tpl');
            $smarty->assign('blog', $blog);
            $smarty->assign('mod', $post);
            $smarty->assign('cat_list', $cat_list);
            $smarty->assign('bb_toolbar', $bb_toolbar);
            $smarty->assign('smilies', $smilies);
            $smarty->assign('autogrow', $autogrow);
            $smarty->assign('msg', $msg);
			$smarty->assign('is_admin', $is_admin);
			$smarty->assign('user_can_iscomments', $user_can_iscomments);
            $smarty->assign('tagline', $tagline);
            $smarty->assign('autocomplete_js', $autocomplete_js);
        $smarty->display('com_blog_edit_post.tpl');

    }

    //Если есть запрос на сохранение
    if ( $inCore->inRequest('goadd') ) {

        $error = false;

        //Получаем параметры
        $title 		= $inCore->request('title', 'str');
        $content 	= $inCore->request('content', 'html');
        $feel 		= $inCore->request('feel', 'str', '');
        $music 		= $inCore->request('music', 'str', '');
        $cat_id 	= $inCore->request('cat_id', 'int');
        $allow_who 	= $inCore->request('allow_who', 'str', $blog['allow_who']);
        $tags 		= $inCore->request('tags', 'str', '');
		$comments   = $inCore->request('comments', 'int', 1);

        //Проверяем их
        if (strlen($title)<2) {  cmsCore::addSessionMessage($_LANG['POST_ERR_TITLE'], 'error'); $errors = true; }
        if (strlen($content)<5) { cmsCore::addSessionMessage($_LANG['POST_ERR_TEXT'], 'error'); $errors = true; }

		// Если есть ошибки, возвращаемся назад
		if($errors){
			$mod['content']   = $content;
			$mod['comments']  = $comments;
			$mod['feel']      = $feel;
			$mod['music']     = $music;
			$mod['title']     = $title;
			$mod['allow_who'] = $allow_who;
			cmsUser::sessionPut('mod', $mod);
			$inCore->redirectBack();
        }

        //Если нет ошибок
        if(!$errors){
            //добавляем новый пост...
            if ($do=='newpost'){

                if ($blog['owner']=='user'){
                    if ($myblog || !$blog['premod']){	$published = 1;	} else { $published = 0; }
                    if (IS_BILLING){ cmsBilling::process('blogs', 'add_post'); }
                }

                if ($blog['owner']=='club'){
                    $club = $inDB->get_fields('cms_clubs', 'id='.$blog['user_id'], '*');
                    $published = 0;
                    if ($inCore->userIsAdmin($inUser->id) || clubUserIsRole($blog['user_id'], $inUser->id, 'moderator') || clubUserIsAdmin($blog['user_id'], $inUser->id) || (!$club['blog_premod'])){
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
													'ballow_who'=>$blog['allow_who'],
                                                    'allow_who'=>$allow_who,
                                                    'published'=>$published,
                                                    'tags'=>$tags,
                                                    'comments'=>$comments
                                                 ));

                $inCore->registerUploadImages(session_id(), $post_id, 'blog');
                cmsUser::checkAwards($user_id);
				$post_seolink = $inDB->get_field('cms_blog_posts', "id={$post_id}", 'seolink');

                if ($published) {
					//регистрируем событие
					if ($blog['owner']=='user' && $blog['allow_who'] != 'nobody'){
						$is_friends_only = $blog['allow_who'] == 'friends' ? 1 : 0;
						cmsActions::log('add_post', array(
							'object' => $title,
							'object_url' => $model->getPostURL(null, $blog['seolink'], $post_seolink),
							'object_id' => $post_id,
							'target' => $blog['title'],
							'target_url' => $model->getBlogURL(null, $blog['seolink']),
							'target_id' => $blog['id'], 
							'description' => '', 
							'is_friends_only' => $is_friends_only
						));
					} elseif ($blog['owner']=='club'){
						cmsActions::log('add_post_club', array(
							'object' => $title,
							'object_url' => $model->getPostURL(null, $blog['seolink'], $post_seolink),
							'object_id' => $post_id,
							'target' => $blog['author'],
							'target_url' => '/clubs/'.$blog['user_id'],
							'target_id' => $blog['id'], 
							'description' => ''
						));
					}
                    $inCore->redirect($model->getPostURL(null, $blog['seolink'], $post_seolink));
                }

                if (!$published) {
					$blog_title = $blog['owner']=='club' ? $club['title'] : $blog['title'];
					$blog_author_id = $blog['owner']=='club' ? $club['admin_id'] : $blog['user_id'];

					$message = str_replace('%user%', cmsUser::getProfileLink($inUser->login, $inUser->nickname), $_LANG['MSG_POST_SUBMIT']);
					$message = str_replace('%post%', '<a href="'.$model->getPostURL(null, $blog['seolink'], $post_seolink).'">'.$title.'</a>', $message);
					$message = str_replace('%blog%', '<a href="'.$model->getBlogURL(null, $blog['seolink']).'">'.$blog_title.'</a>', $message);
					
					cmsUser::sendMessage(-1, $blog_author_id, $message);
                    $inPage->backButton(false);
                    $inPage->printHeading($_LANG['POST_CREATED']);
                    echo '<p>'.$_LANG['POST_PREMODER_TEXT'].'</p>';
                    echo '<p><a href="'.$model->getBlogURL(null, $blog['seolink']).'">'.$_LANG['CONTINUE'].'</a> &rarr;</p>';
                    return;
                }
            }

            //...или сохраняем пост после редактирования
            if ($do=='editpost') {

                $model->updatePost($post_id, array(
                                                    'cat_id'=>$cat_id,
                                                    'title'=>$title,
                                                    'feel'=>$feel,
                                                    'music'=>$music,
                                                    'content'=>$content,
                                                    'allow_who'=>$allow_who,
                                                    'published'=>$published,
                                                    'tags'=>$tags,
                                                    'comments'=>$comments
                                                 ), $cfg['update_seo_link']);

                if ($cfg['update_date']){
                    $inDB->query("UPDATE cms_blog_posts SET pubdate = NOW() WHERE id={$post_id}");
                }

				if ($blog['owner']=='user'){
					cmsActions::updateLog('add_post', array('object' => $title), $post_id);
				} elseif ($blog['owner']=='club'){
					cmsActions::updateLog('add_post_club', array('object' => $title), $post_id);
				}

                $inCore->redirect($model->getPostURL(null, $blog['seolink'], $post['postlink']));
                
            }
        } 
    }

}
////////// НОВАЯ РУБРИКА / РЕДАКТИРОВАНИЕ РУБРИКИ //////////////////////////////////////////////////////
if ($do=='newcat' || $do=='editcat'){

    $cat_id 	= $inCore->request('cat_id', 'int', 0);

    //Проверяем авторизацию
	if (!$inUser->id) { $inCore->redirectBack(); }
    //Проверяем что не было ошибок при загрузке данных о блоге
	if ( $error ) { $inCore->redirectBack(); }

	$user_id    = $inUser->id;
    $cat        = array();

    //Проверяем что пользователь является хозяином или модератором блога
	if ($owner=='user') { 		
		$myblog = ($blog['user_id']==$user_id) || $inUser->is_admin;
	}
    if ($owner=='club'){
		$myblog = clubUserIsRole($blog['user_id'], $user_id, 'moderator') || clubUserIsAdmin($blog['user_id'], $user_id) || $inUser->is_admin;
	}
	if (!$myblog) { $inCore->redirectBack(); }
	
	$inPage->addPathway($blog['title'], $model->getBlogURL(null, $blog['seolink']));

    //Новая рубрики
	if ($do=='newcat'){
        //Устанавливаем заголовки и глубиномер
		$inPage->addPathway($_LANG['NEW_CAT']);
		$inPage->setTitle($_LANG['NEW_CAT']);
        $inPage->printHeading($_LANG['NEW_CAT']);
	}
    //Редактирование рубрики
    if ($do=='editcat'){
        //Устанавливаем заголовки и глубиномер
		$inPage->addPathway($_LANG['RENAME_CAT']);
		$inPage->setTitle($_LANG['RENAME_CAT']);
		$inPage->printHeading($_LANG['RENAME_CAT']);
        //Загружаем рубрику
        $cat    = $model->getBlogCategory($cat_id);
        if (!$cat) {
			$inCore->redirectBack();
		}
	}

    //Если нет запроса на сохранение
    if (!$inCore->inRequest('goadd')){
        $inPage->backButton(false);
        //показываем форму
        $smarty = $inCore->initSmarty('components', 'com_blog_edit_cat.tpl');
        $smarty->assign('mod', $cat);
        $smarty->display('com_blog_edit_cat.tpl');
    }

    //Если есть запрос на сохранение
    if ($inCore->inRequest('goadd')){
        $error_msg = '';;
        //получаем и проверяем название рубрики
        $title = $inCore->request('title', 'str');
        if (strlen($title)<2) { $error_msg .= $_LANG['CAT_ERR_TITLE'].'<br/>'; }

        //если была ошибка
        if ($error_msg){
            $inPage->setTitle($_LANG['ERR_CAT_CREATE']);
            $inPage->printHeading($_LANG['ERR_CAT_CREATE']);
            echo '<p style="color:red">'.$error_msg.'</p>';
            return;
        }

        //если не было ошибки
        if(!$error_msg){
            //новая рубрика
            if ($do=='newcat'){
                $model->addBlogCategory(array('id'=>$id, 'title'=>$title));
            }
            //редактирование рубрики
            if ($do=='editcat'){
                $model->updateBlogCategory($cat_id, array('title'=>$title));
            }
            $inCore->redirect($model->getBlogURL(null, $blog['seolink']));
        }

    }

}
////////////////////////// ПРОСМОТР ПОСТА /////////////////////////////////////////////////////////////////////////
if($do=='post'){

	$post_id 	= $inCore->request('post_id', 'int', 0);
    $user_id    = $inUser->id;

    if ($post_id) { $post = $model->getPost($post_id); }
    if ($seolink) { $post = $model->getPostByLink($bloglink, $seolink); }

	if($owner=='user'){
        $can_view = ($blog['allow_who']=='all' || ($blog['allow_who']=='friends' && usrIsFriends($blog['user_id'], $user_id)) || $post['user_id']==$user_id || $inUser->is_admin);
		$inPage->addPathway($blog['title'], $model->getBlogURL(null, $blog['seolink']));
	}
    if ($owner=='club'){
        $blog['club']   = $inDB->get_fields('cms_clubs', "id={$blog['user_id']}", 'title, clubtype');
        $can_view = $blog['club']['clubtype'] == 'public' || ($blog['club']['clubtype'] == 'private' && (clubUserIsMember($blog['user_id'], $user_id) || $inUser->is_admin || clubUserIsAdmin($blog['user_id'], $user_id)));
		$inPage->addPathway($blog['author'], '/clubs/'.$blog['user_id']);	
		$inPage->addPathway($_LANG['BLOG'], $model->getBlogURL(null, $blog['seolink']));
		$blog['title'] 		= $blog['author'];
		$blog['author'] 	= clubAdminLink($blog['user_id']);
	}

    if (!$post){ cmsCore::error404(); }

    if (!$can_view){
        $inPage->printHeading($_LANG['CLOSED_POST']);
        if ($owner == 'user') { echo '<p>'.$_LANG['CLOSED_POST_TEXT'].'</p>'; }
        if ($owner == 'club') { echo '<p>'.str_replace('%link%', '/clubs/'.$blog['user_id'], $_LANG['CLOSED_BLOG_CLUB_TEXT']).'</p>'; }
        return;
    }

    //Если авторизован, проверяем является ли он хозяином блога или его администратором
    if ($user_id){
        if ($owner=='user'){
            $is_author  = (($user_id == $blog['user_id']) || ((!$myblog) && $blog['ownertype']=='multi' && $inDB->get_field('cms_blog_authors', 'blog_id='.$blog['id'].' AND user_id='.$user_id, 'id')) || ($blog['ownertype']=='multi' && $blog['forall'] && $user_id == $post['user_id']));
            $is_admin   = $inCore->userIsAdmin($user_id);
        }
        if ($owner=='club'){
            $is_moder   = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
            $is_author  = ($user_id == $post['user_id']);
            $is_admin   = clubUserIsAdmin($blog['user_id'], $user_id) || $inCore->userIsAdmin($user_id);
        }
    }

    $post['fpubdate'] = cmsCore::dateDiffNow($post['pubdate']).' '.$_LANG['BACK'].' ('.$post['fpubdate'].')';

    if ($post['cat_id']){
        $cat = $model->getBlogCategory($post['cat_id']);
        $cat = $cat['title']; 
    } else { 
        $cat = $_LANG['WITHOUT_CAT'];
    }

    $inPage->setTitle($post['title']);
    $inPage->addPathway($post['title']);

    //Парсим bb-код
    $msg = $post['content_html'];

    //Убираем тег [cut]
    $regex  = '/\[(cut=)\s*(.*?)\]/i';
    $msg    = preg_replace($regex, '', $msg);

	// meta descriptions
	$inPage->setDescription($post['title']);

    $post['author'] = cmsUser::getGenderLink($post['author_id'], $post['author']);

    $post['image'] = usrImageNOdb($post['author_id'], 'small', $post['author_image'], $post['author_deleted']);
    
    //выводим пост
    $smarty = $inCore->initSmarty('components', 'com_blog_view_post.tpl');
        $smarty->assign('post', $post);
        $smarty->assign('blog', $blog);
        $smarty->assign('id', $id);
        $smarty->assign('cat', $cat);
        $smarty->assign('is_author', $is_author);
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('is_moder', $is_moder);
        $smarty->assign('karma_form', cmsKarmaForm('blogpost', $post['id'], 0, $is_author));
        $smarty->assign('msg', $msg);
        $smarty->assign('nav', blogPostNav($model, $post['pubdate'], $blog['id'], $blog['seolink']));
        $smarty->assign('tag_bar', cmsTagBar('blogpost', $post['id']));
    $smarty->display('com_blog_view_post.tpl');

    //show user comments
    if($inCore->isComponentInstalled('comments') && $post['comments']){
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

    if (!$post){ cmsCore::error404(); }

    if ($owner=='user'){
        $myblog     = $model->isUserBlogAuthor($blog['id'], $post_id, $blog['user_id']);
        $is_author  = (((!$myblog) && $inDB->get_field('cms_blog_authors', 'blog_id='.$id.' AND user_id='.$user_id, 'id')) || ($blog['forall'] && $post['user_id'] == $user_id));
		$is_admin   = $inUser->is_admin;
    }
    if($owner=='club') {
        $myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator');
        $is_author  = (clubUserIsRole($blog['user_id'], $user_id, 'member') && $post['user_id'] == $user_id);
		$is_admin   = clubUserIsAdmin($blog['user_id'], $user_id) || $inUser->is_admin;
    }

    if ( !$inCore->inRequest('confirm') ) {
        //MENU
        if ($myblog || ($is_author && $post['user_id'] == $user_id) || $is_admin){
            $inPage->setTitle($_LANG['DELETE_POST']);
			$inPage->addPathway($_LANG['DELETE_POST']);
            $inPage->backButton(false);
            $confirm['title'] = $_LANG['DELETE_POST'];
            $confirm['text'] = $_LANG['YOU_REALY_DELETE_POST'].' "<a href="'.$model->getPostURL(null, $post['bloglink'], $post['seolink']).'">'.$post['title'].'</a>" '.$_LANG['FROM_BLOG'];
            $confirm['action'] = 'javascript:void(0);';
            $confirm['yes_button'] = array();
            $confirm['yes_button']['type'] = 'button';
            $confirm['yes_button']['onclick'] = "window.location.href='/blogs/".$id."/delpost".$post_id."-yes.html';";
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
        } else {
            $inCore->redirectBack();
        }
    }

    if ( $inCore->inRequest('confirm') ){

        if ($myblog || ($is_author && $post['user_id'] == $user_id) || $is_admin){
            
            $model->deletePost($post_id);

            if ($user_id != $post['user_id']){
                if ($blog['owner']=='club') { $blog['title'] = $inDB->get_field('cms_clubs', 'id='.$blog['user_id'], 'title'); }
                cmsUser::sendMessage(-1, $post['user_id'], $_LANG['YOUR_POST'].' <b>&laquo;'.$post['title'].'&raquo;</b> '.$_LANG['WAS_DELETED_FROM_BLOG'].' <b>&laquo;<a href="'.$model->getBlogURL(0, $blog['seolink']).'">'.$blog['title'].'</a>&raquo;</b>');
            }
        }
        $inCore->redirect($model->getBlogURL(null, $blog['seolink']));
    }

}
///////////////////////// PUBLISH POST /////////////////////////////////////////////////////////////////////////////
if ($do == 'publishpost'){

    $inPage->backButton(false);

    $post_id 	= $inCore->request('post_id', 'int', 0);
    $user_id    = $inUser->id;

	if (!$user_id || !$post_id){ $inCore->halt(); }
    //Если пользователь авторизован, проверяем является ли он хозяином блога, модератором или админом
	if ($user_id){
		if ($owner=='user'){
			$myblog     = $blog['user_id'] == $user_id;
			$is_admin   = $inUser->is_admin;
		} elseif ($owner=='club') {
			$myblog     = clubUserIsRole($blog['user_id'], $user_id, 'moderator') || clubUserIsAdmin($blog['user_id'], $user_id);
			$is_admin   = clubUserIsAdmin($blog['user_id'], $user_id) || $inUser->is_admin;
		}
	}
    if ($myblog || $is_admin){
        $post   = $model->getPost($post_id);
        if ($post){
            $model->publishPost($post_id);
			$post['seolink'] = $model->getPostURL(0, $post['bloglink'], $post['seolink']);
			if ($blog['allow_who'] == 'all') { cmsCore::callEvent('ADD_POST_DONE', $post); }
			if ($blog['owner']=='user'){
				$is_friends_only = $blog['allow_who'] == 'friends' ? 1 : 0;
				//регистрируем событие
				cmsActions::log('add_post', array(
						'object' => $post['title'],
						'user_id' => $post['user_id'],
						'object_url' => $post['seolink'],
						'object_id' => $post['id'],
						'target' => $blog['title'],
						'target_url' => $model->getBlogURL(0, $blog['seolink']),
						'target_id' => $blog['id'], 
						'description' => '', 
						'is_friends_only' => $is_friends_only
				));
			} elseif ($blog['owner']=='club'){
				cmsActions::log('add_post_club', array(
						'object' => $post['title'],
						'user_id' => $post['user_id'],
						'object_url' => $post['seolink'],
						'object_id' => $post['id'],
						'target' => $blog['author'],
						'target_url' => '/clubs/'.$blog['user_id'],
						'target_id' => $blog['id'], 
						'description' => ''
				));
			}

            cmsUser::sendMessage(-1, $post['author_id'], $_LANG['YOUR_POST'].' <b>&laquo;<a href="'.$model->getPostURL(0, $post['bloglink'], $post['seolink']).'">'.$post['title'].'</a>&raquo;</b> '.$_LANG['PUBLISHED_IN_BLOG'].' <b>&laquo;<a href="'.$model->getBlogURL(0, $blog['seolink']).'">'.$blog['title'].'</a>&raquo;</b>');
        }
    } else {
		echo '<p style="color:red">'.$_LANG['ACCESS_DENIED'].'</p>';
    }
    
    $inCore->redirect('/blogs/'.$blog['id'].'/moderate.html');
    
}

///////////////////////// DELETE BLOG /////////////////////////////////////////////////////////////////////////////
if ($do == 'delblog'){
	$inPage->backButton(false); 

    $user_id = $inUser->id;

    if (!$user_id){ $inCore->halt(); }

    if ( $inCore->inRequest('confirm') ){
        if (($blog['user_id'] == $user_id) || $inUser->is_admin){
            $model->deleteBlog($id);
            $inCore->redirect('/blogs');
        }        
    }

    if ( !$inCore->inRequest('confirm') ){
        if ($user_id == $blog['user_id'] || $inCore->userIsAdmin($user_id)){
            $inPage->setTitle($_LANG['DELETE_BLOG']);
            $confirm['title']                   = $_LANG['DELETE_BLOG'];
            $confirm['text']                    = $_LANG['YOU_REALY_DELETE_BLOG'];
            $confirm['action']                  = 'javascript:void(0);';
            $confirm['yes_button']              = array();
            $confirm['yes_button']['type']      = 'button';
            $confirm['yes_button']['onclick']   = "window.location.href='/blogs/".$id."/delblog-yes.html';";
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
            return;
        }
    }    

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
            $inCore->redirect($model->getBlogURL(null, $blog['seolink']));
        }

        if (!$inCore->inRequest('confirm')){
            if ($can_delete){
                $inPage->setTitle($_LANG['DELETE_CAT']);
                $confirm['title'] = $_LANG['DELETE_CAT'];
                $confirm['text'] = '<p>'.$_LANG['YOU_REALY_DELETE_CAT'].' "<a href="/blogs/'.$id.'/blog'.$cat_id.'.html">'.$data['title'].'</a>" '.$_LANG['FROM_BLOG'].'</p><p>'.$_LANG['DELETE_CAT_TEXT'].'</p>';
                $confirm['action'] = 'javascript:void(0);';
                $confirm['yes_button'] = array();
                $confirm['yes_button']['type'] = 'button';
                $confirm['yes_button']['onclick'] = "window.location.href='/blogs/".$id."/delcat".$cat_id."-yes.html';";
                $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
                $smarty->assign('confirm', $confirm);
                $smarty->display('action_confirm.tpl');
            } else { echo usrAccessDenied(); }
        }
    }

}

////////// VIEW LATEST POSTS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='latest'){

	// rss в адресной строке
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.$_LANG['RSS_BLOGS'].'" href="'.HOST.'/rss/blogs/all/feed.rss">');

	$smarty     = $inCore->initSmarty('components', 'com_blog_view_posts.tpl');
				
	$user_id    = $inUser->id;
	$is_admin   = $inCore->userIsAdmin($user_id);
	$can_view   = false;

    $posts      = array();

        //Считаем количество персональных и коллективных блогов
        $single_blogs	= $model->getSingleBlogsCount();
        $multi_blogs 	= $model->getMultiBlogsCount();

		//TITLES
		$inPage->setTitle($_LANG['RSS_BLOGS']);
		$inPage->addPathway($_LANG['RSS_BLOGS']);
		$inPage->setDescription($_LANG['RSS_BLOGS']);

		//PAGINATION
		$perpage = isset($cfg['perpage']) ? $cfg['perpage'] : 10;
		$page = $inCore->request('page', 'int', 1);
							
        $total = $model->getLatestCount($user_id, $is_admin);
					
        //GET ENTRIES
        $posts_list = $model->getLatestPosts($page, $perpage);

        //PAGINATION
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/latest-%page%.html', array());

        //FETCH ENTRIES
        if ($posts_list){
            foreach($posts_list as $post){
                $can_view = ($post['blog_allow_who']=='all' || ($post['blog_allow_who']=='friends' && usrIsFriends($post['user_id'], $user_id)) || $post['user_id']==$user_id || $is_admin);
                if ($can_view){

                    $post['url']        = $model->getPostURL(null, $post['bloglink'], $post['seolink']);
                    $post['comments']   = $post['comments'] ? $inCore->getCommentsCount('blog', $post['id']) : false;
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);
					$post['fpubdate']	= $inCore->dateFormat($post['fpubdate']);

                    $post['blog_url']   = $model->getBlogURL(null, $post['bloglink']);

                    $msg = $post['content_html'];

                    //Разбиваем текст поста на 2 части по тегу [cut=...] и оставляем только первую из них
                    if (strstr($msg, '[cut')){
                        $msg = $model->getPostShort($msg, $post['url']);
                    }

                    $post['msg']        = $msg;
                    $post['tagline']    = cmsTagLine('blogpost', $post['id']);
                    $post['author']     = cmsUser::getProfileLink($post['login'], $post['author']);
                    $posts[]            = $post;
                }
            }
        }

        $smarty->assign('is_posts', (bool)sizeof($posts));
        $smarty->assign('is_latest', (bool)sizeof($posts));
        $smarty->assign('pagetitle', $_LANG['BLOGS']);
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('total', $total);
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
////////// VIEW POPULAR POSTS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){

	$smarty   = $inCore->initSmarty('components', 'com_blog_view_posts.tpl');
				
	$user_id  = $inUser->id;
	$is_admin = $inCore->userIsAdmin($user_id);
	
	$can_view = false;

    $posts    = array();

		//TITLES
		$inPage->setTitle($_LANG['POPULAR_IN_BLOGS']);
		$inPage->addPathway($_LANG['POPULAR_IN_BLOGS']);
		$inPage->setDescription($_LANG['POPULAR_IN_BLOGS']);

		//PAGINATION
		$perpage    = isset($cfg['perpage']) ? $cfg['perpage'] : 20;
		$page       = $inCore->request('page', 'int', 1);
							
		//COUNT ENTRIES
        $total      = $model->getBestCount($user_id, $is_admin);
        
        //GET ENTRIES
        $posts_list = $model->getBestPosts($page, $perpage);

        //PAGINATION
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/blogs/popular-%page%.html', array());

        //FETCH ENTRIES
        if ($posts_list){
            foreach($posts_list as $post){

                $can_view = ($post['blog_allow_who']=='all' || ($post['blog_allow_who']=='friends' && usrIsFriends($post['user_id'], $user_id)) || $post['user_id']==$user_id || $inCore->userIsAdmin($user_id));

                if ($can_view){
                    $post['url']        = $model->getPostURL(null, $post['bloglink'], $post['seolink']);

                    $post['comments']   = $post['comments'] ? $inCore->getCommentsCount('blog', $post['id']) : false;
                    $post['karma']      = cmsKarmaFormatSmall($post['points']);
					$post['fpubdate']	= $inCore->dateFormat($post['pubdate']);

                    $msg = $post['content_html'];

                    //Разбиваем текст поста на 2 части по тегу [cut=...] и оставляем только первую из них
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

    $smarty->assign('pagetitle', $_LANG['POPULAR_IN_BLOGS']);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('total', $total);
    $smarty->assign('uid', $user_id);
    if (@$posts) { $smarty->assign('posts', $posts); }
    if (@$pagination) { $smarty->assign('pagination', $pagination); }
    $smarty->assign('id', $id);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('round_corners_js', '$.jcorners(".blog_comments",{radius:10});');
    $smarty->display('com_blog_view_posts.tpl');

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$inCore->executePluginRoute($do);
}
?>