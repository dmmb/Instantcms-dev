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

function clubs(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    global $_LANG;
	$inCore->includeFile("components/users/includes/usercore.php");
	$inCore->includeFile("components/blogs/includes/blogcore.php");

	$inCore->loadLib("clubs");
	$inCore->loadLib("users");
	$inCore->loadLib("tags");
	$inCore->loadLib("photos");
	$inCore->loadLib("karma");

    $inCore->loadModel('clubs');
    $model = new cms_model_clubs();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

	$inPage->addHeadJS('components/clubs/js/clubs.js');

	//LOAD CONFIG
	$cfg = $inCore->loadComponentConfig('clubs');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }
	
	//SOME DEFAULT CONFIG VALUES
	if(!isset($cfg['seo_club'])) { $cfg['seo_club'] = 'title'; }
	if(!isset($cfg['enabled_blogs'])) { $cfg['enabled_blogs'] = 1; }
	if(!isset($cfg['enabled_photos'])) { $cfg['enabled_photos'] = 1; }	
	if(!isset($cfg['thumb1'])) { $cfg['thumb1'] = 48; }	
	if(!isset($cfg['thumb2'])) { $cfg['thumb2'] = 200; }	
	if(!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1; }	
	if(!isset($cfg['cancreate'])) { $cfg['cancreate'] = 0; }	
	if(!isset($cfg['perpage'])) { $cfg['perpage'] = 10; }
    if(!isset($cfg['notify_in'])) { $cfg['notify_in'] = 1; }
    if(!isset($cfg['notify_out'])) { $cfg['notify_out'] = 1; }
	if(!isset($cfg['every_karma'])) { $cfg['every_karma'] = 100; }
	
    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();
	
	$pagetitle = $inCore->menuTitle();
	
	//INPUT PARAMETERS
	$id 		= $inCore->request('id', 'int', 0);
	$do 		= $inCore->request('do', 'str', 'view');

////////// VIEW ALL CLUBS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	//PAGINATION
    $perpage    = isset($cfg['perpage']) ? $cfg['perpage'] : 10;
	$page       = $inCore->request('page', 'int', 1);
	
	$clubs      = array();
    $clubs_list = $model->getClubs($page, $perpage);
	
	$total = 0;

	if ($clubs_list){
		foreach ($clubs_list as $club){
			if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
				if (!file_exists(PATH.'/images/clubs/small/'.$club['imageurl'])){
					$club['imageurl'] = 'nopic.jpg';
				}
			}
			$clubs[] = $club;
		}
		$total      = $model->getClubsCount();
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/clubs/page-%page%', array());
	}

	$smarty = $inCore->initSmarty('components', 'com_clubs_view.tpl');
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('clubid', $id);
	// Ссылку на создание клуба показываем всем авторизованным, если включено создание клуба
	$smarty->assign('can_create', ($inUser->id && $cfg['cancreate'] || $inUser->is_admin));
	$smarty->assign('clubs', $clubs);
	$smarty->assign('total', $total);
	$smarty->assign('pagination', $pagination);
	$smarty->display('com_clubs_view.tpl');

}
////////// VIEW SINGLE CLUB ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='club'){

	$club   = $model->getClub($id);
	if(!$club){	cmsCore::error404(); }
    
    //TITLES
    $pagetitle = $club['title'];
    $inPage->setTitle($pagetitle);
    $inPage->addPathway($club['title']);

	// description
	switch ($cfg['seo_club']){
		case 'deskr': 	$inPage->setDescription($inCore->strClear($club['description']));
						break;
		
		case 'title': 	$inPage->setDescription($club['title']);
		
						break;
	}

    $user_id    = $inUser->id;
    $is_admin 	= $inUser->is_admin || ($user_id == $club['admin_id']);
    $is_moder 	= clubUserIsRole($id, $user_id, 'moderator');
    $is_member 	= clubUserIsRole($id, $user_id, 'member');
	$is_member_club	= $is_member || $is_moder;

    $is_access = true;

    if ($club['clubtype']=='private' && (!$is_admin && !$is_moder && !$is_member)){
        $is_access = false;
    }

    $is_karma_enabled = false;

    if ($user_id){
        $is_karma_enabled = (cmsUser::getKarma($user_id) >= $club['album_min_karma']) && $is_member_club ? true : false;
    }

    //CHECK IMAGE
    if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
        if (!file_exists(PATH.'/images/clubs/'.$club['imageurl'])){
            $club['imageurl'] = 'nopic.jpg';
        }
    }

    //JOIN/LEAVE LINK
    $club['member_link'] = '';
    if ( $is_member_club ){
        $club['member_link'] = '<a href="/clubs/'.$id.'/leave.html" class="leave">'.$_LANG['LEAVE_CLUB'].'</a>';;
    } 
    if ($club['clubtype']=='public' && ($user_id != $club['admin_id']) && !$is_member_club){
        $club['member_link'] = '<a href="/clubs/'.$id.'/join.html" class="join">'.$_LANG['JOIN_CLUB'].'</a>';
    }

    //PARAMS
    $club['admin'] 			= clubAdminLink($id);
    $club['members'] 		= clubTotalMembers($id);
    $club['members_list'] 	= clubMembersList($id);
    $club['wall_html']		= cmsUser::getUserWall($club['id'], 'club', 1, $is_moder, $is_admin);
    $club['addwall_html'] 	= cmsUser::getUserAddWall($club['id'], 'club');

    $club['enabled_blogs']	= $club['enabled_blogs'] == 1 || ($club['enabled_blogs']==0 && $cfg['enabled_blogs']==1);
    $club['enabled_photos']	= $club['enabled_photos'] == 1 || ($club['enabled_photos']==0 && $cfg['enabled_photos']==1);

	if ($club['enabled_blogs']) {
		$club['blog_id']		= clubBlogId($club['id']);
		$club['blog_content']	= clubBlogContent($club['blog_id'], $is_admin, $is_moder, $is_member);
		$inCore->loadModel('blogs');
		$blog_model = new cms_model_blogs();
		$club['blog_url']       = $blog_model->getBlogURL(null, $inDB->get_field('cms_blogs', "id={$club['blog_id']}", 'seolink'));
	}

	if ($club['enabled_photos']) {
		$club['root_album_id']	= clubRootAlbumId($club['id']);
		if (!$club['root_album_id']) { $club['root_album_id'] = albumCreateRoot($id, 'club'.$id, $club['title']); }
		$club['photo_albums']	= clubPhotoAlbums($club['id'],  $is_admin, $is_moder, $is_member);
		$club['all_albums']	    = $inDB->rows_count('cms_photo_albums', "NSDiffer = 'club{$club['id']}' AND user_id = '{$club['id']}' AND parent_id > 0");
	}

	$club['pubdate'] = $inCore->dateformat($club['pubdate'], true, true);

	// Получаем плагины
	$plugins = $model->getPluginsOutput($club);

	$smarty = $inCore->initSmarty('components', 'com_clubs_view_club.tpl');	
    $smarty->assign('clubid', $id);
    $smarty->assign('club', $club);
    $smarty->assign('is_access', $is_access);
    $smarty->assign('uid', $user_id);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('is_moder', $is_moder);
	$smarty->assign('plugins', $plugins);
    $smarty->assign('is_member', $is_member);
    $smarty->assign('is_karma_enabled', $is_karma_enabled);
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->display('com_clubs_view_club.tpl');
	
}
///////////////////////// CREATE CLUB ////////////////////////////////////////////////////////////////////////////
if ($do == 'create'){

	$inPage->backButton(false);
	
	if (!$inUser->id){ cmsUser::goToLogin(); }

    $can_create = $model->canCreate($cfg, true);

    if (!$can_create){ $inCore->redirectBack(); }

    $inPage->addPathway($_LANG['CREATE_CLUB']);

    if ( !$inCore->inRequest('create') ){
        $inPage->setTitle($_LANG['CREATE_CLUB']);
        $smarty = $inCore->initSmarty('components', 'com_clubs_create.tpl');
        $smarty->assign('confirm', $confirm);
        $smarty->display('com_clubs_create.tpl');
    }

    if ( $inCore->inRequest('create') ){

        $errors     = false;
        $title      = $inCore->request('title', 'str');
        $clubtype   = $inCore->request('clubtype', 'str');

        if (!$title || !$clubtype){
            cmsCore::addSessionMessage($_LANG['CLUB_REQ_TITLE'], 'error');
            $errors = true;
        } else {

            $is_exists  = $inDB->get_field('cms_clubs', "title = '{$title}'", 'id');

            if ($is_exists){
                cmsCore::addSessionMessage($_LANG['CLUB_EXISTS'], 'error');
                $errors = true;
            }

        }

        if(!$errors){
            $created_id = $model->addClub(array('user_id'=>$inUser->id, 'title'=>$title, 'clubtype'=>$clubtype), $cfg);
            if($created_id){ setClubRating($created_id); }
			//регистрируем событие
			cmsActions::log('add_club', array(
						'object' => $title,
						'object_url' => '/clubs/'.$created_id,
						'object_id' => $created_id,
						'target' => '',
						'target_url' => '',
						'target_id' => 0, 
						'description' => ''
			));
            $inCore->redirect('/clubs/'.$created_id);
        } else {
            $inCore->redirect('/clubs/create.html');
        }
        
    }

}

///////////////////////// CONFIGURE CLUB //////////////////////////////////////////////////////////////////////
if ($do == 'config'){
    
	$inPage->backButton(false);

    $user_id    = $inUser->id;
    $user_nick  = $inUser->nickname;
    $club       = $model->getClub($id);

    if (!$user_id){ return; }
    if (!$club){ return; }
	if ( !(clubUserIsAdmin($id, $user_id) || $inCore->userIsAdmin($user_id)) ){ return; }

    if ( $inCore->inRequest('save') ){
        //save to database
		$title 		        = $inCore->request('title', 'str', '');
        $description 		= $inCore->request('description', 'html', '');
        $description 		= $inCore->badTagClear($description);
        $description 		= $inDB->escape_string($description);
        $admin_id 			= $club['admin_id'];
        $clubtype			= $inCore->request('clubtype', 'str', 'public');
        $maxsize 			= $inCore->request('maxsize', 'int', 0);
        $blog_min_karma		= $inCore->request('blog_min_karma', 'int', 0);
        $photo_min_karma	= $inCore->request('photo_min_karma', 'int', 0);
        $album_min_karma	= $inCore->request('album_min_karma', 'int', 0);

        $blog_premod		= $inCore->request('blog_premod', 'int', 0);
        $photo_premod		= $inCore->request('photo_premod', 'int', 0);

        $join_karma_limit	= $inCore->request('join_karma_limit', 'int', 0);
        $join_min_karma		= $inCore->request('join_min_karma', 'int', 0);

        //upload logo
        if ($_FILES['picture']['name']){
            $inCore->includeGraphics();

			$uploaddir = PATH.'/images/clubs/';	
            if (!is_dir($uploaddir)) { @mkdir($uploaddir); }
            @chmod($uploaddir, 0755);

			$realfile   = $_FILES['picture']['name'];
			$path_parts = pathinfo($realfile);
			$ext        = strtolower($path_parts['extension']);
			if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png' && $ext != 'bmp') { cmsCore::error404(); }

            $filename       = md5($id . $user_id . time()).'.jpg';
            $uploadphoto    = $uploaddir . $filename;
            $uploadthumb    = $uploaddir . 'small/' . $filename;

            if ($inCore->moveUploadedFile($_FILES['picture']['tmp_name'], $uploadphoto, $_FILES['picture']['error'])) {
					if ($club['imageurl'] && $club['imageurl']!='nopic.jpg'){
						@unlink(PATH.'/images/clubs/'.$club['imageurl']);
						@unlink(PATH.'/images/clubs/small/'.$club['imageurl']);
					}
                    @img_resize($uploadphoto, $uploadthumb, $cfg['thumb1'], $cfg['thumb1'], $cfg['thumbsqr']);
                    @img_resize($uploadphoto, $uploadphoto, $cfg['thumb2'], $cfg['thumb2'], $cfg['thumbsqr']);
            } else {
                $msg = $inCore->uploadError();
            }

            $model->updateClubImage($id, $filename);
        }

        $model->updateClub($id, array(
                                        'admin_id'=>$admin_id,
										'title'=>$title,
                                        'description'=>$description,
                                        'clubtype'=>$clubtype,
                                        'maxsize'=>$maxsize,
                                        'blog_min_karma'=>$blog_min_karma,
                                        'photo_min_karma'=>$photo_min_karma,
                                        'album_min_karma'=>$album_min_karma,
                                        'photo_premod'=>$photo_premod,
                                        'blog_premod'=>$blog_premod,
                                        'join_min_karma'=>$join_min_karma,
                                        'join_karma_limit'=>$join_karma_limit
                                    ));

		cmsActions::updateLog('add_club', array('object' => $title), $id);

        if ($inUser->is_admin && IS_BILLING){
            $is_vip    = $inCore->request('is_vip', 'int', 0);
            $join_cost = $inCore->request('join_cost', 'int', 0);
            $model->setVip($id, $is_vip, $join_cost);
        }

        $moders  = $inCore->request('moderslist', 'array_int', array());
        $members = $inCore->request('memberslist', 'array_int', array());

        if ($moders) { if (array_search($admin_id, $moders)) { unset($moders[array_search($admin_id, $moders)]); }	}
        if ($members) { if (array_search($admin_id, $members)) { unset($members[array_search($admin_id, $members)]); }	}

        clubSaveUsers($id, $members, 'member', $clubtype, $cfg);
        clubSaveUsers($id, $moders, 'moderator', $clubtype, $cfg);

		cmsCore::addSessionMessage($_LANG['CONFIG_SAVE_OK'], 'info');

        $inCore->redirect('/clubs/'.$id);
    }

    if ( !$inCore->inRequest('save') ){
        
        // Заголовки и пафвей
        $inPage->addPathway($club['title'], '/clubs/'.$id);
        $inPage->addPathway($_LANG['CONFIG_CLUB']);
        $inPage->setTitle($_LANG['CONFIG_CLUB']);

		// Получаем список друзей владельца клуба
		$friends     	 = cmsUser::getFriends($club['admin_id']);
		// Получаем участников клуба, без учета администратора
        $moderators     = clubModerators($id);
        $members        = clubMembers($id);
        $club_users_list = array_merge($moderators, $members);
		// Проверяем наличие друга в списке участников клуба или является ли он администратором
		foreach($friends as $key=>$friend){ 
			if (in_array($friend['id'], $club_users_list) || $friend['id'] == $club['admin_id']) { unset($friends[$key]); }
		}
		// Формируем список option друзей, если они есть
		if ($_SESSION['user']['friends'] && $friends) { 
			foreach($friends as $friend){ 
				$friends_list .= '<option value="'.$friend['id'].'">'.$friend['nickname'].'</option>';
			}		
		}
		// Формируем массив id друзей для мержа с участниками клуба
		// массив друзей берется с уже отфильтрованными участниками
		$friends_ids = array();
		foreach($friends as $friend){ 
			$friends_ids[] = $friend['id'];
		}
		// формируем список друзья не в клубе + участники клуба
		$fr_members = array_merge($club_users_list, $friends_ids);
		// Проверяем наличие друга или участников клуба в списке модераторов
		$fr_members = array_diff($fr_members, $moderators);
		// Формируем список option друзей (которые еще не в этом клубе) и участников
		if ($fr_members) { $fr_members_list = cmsUser::getAuthorsList($fr_members); } else { $fr_members_list = ''; }
		// Формируем список option участников клуба
        if ($moderators) { $moders_list = cmsUser::getAuthorsList($moderators); } else { $moders_list = ''; }
        if ($club_users_list) { $members_list = cmsUser::getAuthorsList($club_users_list); } else { $members_list = ''; }

        $club['blog_id'] = clubBlogId($id);

        //CHECK IMAGE
        if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
            if (!file_exists(PATH.'/images/clubs/'.$club['imageurl'])){
                $club['imageurl'] = 'nopic.jpg';
            }
        }

        $club['enabled_blogs']	= $club['enabled_blogs'] == 1 || ($club['enabled_blogs']==0 && $cfg['enabled_blogs']==1);
        $club['enabled_photos']	= $club['enabled_photos'] == 1 || ($club['enabled_photos']==0 && $cfg['enabled_photos']==1);

        $smarty = $inCore->initSmarty('components', 'com_clubs_config.tpl');
        $smarty->assign('club', $club);
        $smarty->assign('moders_list', $moders_list);
        $smarty->assign('members_list', $members_list);
        $smarty->assign('friends_list', $friends_list);
		$smarty->assign('fr_members_list', $fr_members_list);
		$smarty->assign('is_billing', IS_BILLING);
		$smarty->assign('is_admin', $inUser->is_admin);
        $smarty->display('com_clubs_config.tpl');

    }

}
///////////////////////// LEAVE CLUB - UNJOIN /////////////////////////////////////////////////////////////////
if ($do == 'leave'){
	$inPage->backButton(false);

    $user_id    = $inUser->id;
    $club       = $model->getClub($id);

	if (!$user_id){ return; }
    if (!$club){ return; }

    $inPage->addPathway($club['title'], '/clubs/'.$id);
    $inPage->addPathway($_LANG['EXIT_FROM_CLUB']);
	$inPage->setTitle($_LANG['EXIT_FROM_CLUB']);

	if (!clubUserIsMember($id, $user_id)){ return; }

    if ( $inCore->inRequest('confirm') ){
        clubRemoveUser($id, $user_id);
        setClubsRating($id);
		cmsActions::removeObjectLog('add_club_user', $id, $user_id);
        $inCore->redirect('/clubs/'.$id);
    }

    if ( !$inCore->inRequest('confirm') ){

        $inPage->setTitle($_LANG['EXIT_FROM_CLUB']);
		$inPage->backButton(false);

        $confirm['title']               = $_LANG['EXIT_FROM_CLUB'];
        $confirm['text']                = $_LANG['REALY_EXIT_FROM_CLUB'];
        $confirm['action']              = '';
        $confirm['yes_button']['type']  = 'submit';
        $confirm['yes_button']['name']  = 'confirm';

        $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
        $smarty->assign('confirm', $confirm);
        $smarty->display('action_confirm.tpl');
    }

}
///////////////////////// JOIN CLUB ////////////////////////////////////////////////////////////////////////////
if ($do == 'join'){

    $user_id    = $inUser->id;
    $club       = $model->getClub($id);

	if (!$user_id){ return; }
    if (!$club){    return; }

    $inPage->addPathway($club['title'], '/clubs/'.$id);
    $inPage->addPathway($_LANG['JOINING_CLUB']);
	$inPage->setTitle($_LANG['JOINING_CLUB']);

    if (clubUserIsMember($id, $user_id)){ return; }

    //
    // Обработка заявки
    //
    if ( $inCore->inRequest('confirm') ){

        //списываем оплату если клуб платный
        if (IS_BILLING && $club['is_vip'] && $club['join_cost'] && !$inUser->is_admin){
            if ($inUser->balance >= $club['join_cost']){
                //если средств на балансе хватает
                cmsBilling::pay($user_id, $club['join_cost'], sprintf($_LANG['VIP_CLUB_BUY_JOIN'], $club['title']));
            } else {
                //недостаточно средств, создаем тикет
                //и отправляем оплачивать
                $billing_ticket = array(
                    'action' => sprintf($_LANG['VIP_CLUB_BUY_JOIN'], $club['title']), 
                    'cost'   => $club['join_cost'],
                    'amount' => $club['join_cost'] - $inUser->balance,
                    'url'    => $_SERVER['REQUEST_URI']
                );
                cmsUser::sessionPut('billing_ticket', $billing_ticket);
                $inCore->redirect('/billing/pay');                
            }
        }

        //добавляем пользователя в клуб
        clubAddUser($id, $user_id);
        setClubsRating($id);

		//регистрируем событие
		cmsActions::log('add_club_user', array(
						'object' => $club['title'],
						'object_url' => '/clubs/'.$id,
						'object_id' => $id,
						'target' => '',
						'target_url' => '',
						'target_id' => 0, 
						'description' => ''
		));

        $inCore->redirect('/clubs/'.$id);
        
    }

    //
    // Форма подтверждения заявки
    //
    if ( !$inCore->inRequest('confirm') ) {

        $inPage->setTitle($_LANG['JOINING_CLUB']);

        $min_karma = $club['join_min_karma'];
        $user_karma = cmsUser::getKarma($user_id);

        if(($user_karma >= $min_karma) || !$club['join_karma_limit']){

            $inPage->backButton(false);
            $confirm['title']   = $_LANG['JOINING_CLUB'];
            $confirm['text']    = $_LANG['YOU_REALY_JOIN_TO'].' <strong>'.$club['title'].'</strong>?';
            if ($club['is_vip'] && $club['join_cost'] && !$inUser->is_admin){
                $confirm['text'] .= '<br/>'.$_LANG['VIP_CLUB_JOIN_COST'].' &mdash; <strong>'.$club['join_cost'].' '.$_LANG['BILLING_POINT10'].'</strong>';
            }
            $confirm['action']  = '';
            $confirm['yes_button']['type'] = 'submit';
            $confirm['yes_button']['name'] = 'confirm';

            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');

        } else {

            $inPage->backButton(true);
            $inPage->printHeading($_LANG['NEED_KARMA']);
            echo '<p><strong>'.$_LANG['NEED_KARMA_TEXT'].'</strong></p>';
            echo '<p>'.$_LANG['NEEDED'].' '.$min_karma.', '.$_LANG['HAVE_ONLY'].' '.$user_karma.'.</p>';
            echo '<p>'.$_LANG['WANT_SEE'].' <a href="/users/'.$uid.'/karma.html">'.$_LANG['HISTORY_YOUR_KARMA'].'</a>?</p>';
            
        }
    }

}
///////////////////// Рассылка сообщения членам клуба /////////////////////////////////////////////////////////
if ($do == 'send_message'){

    $user_id    = $inUser->id;
    $club       = $model->getClub($id);
	$is_admin 	= $inUser->is_admin || ($user_id == $club['admin_id']);


	if (!$user_id || !$club || !$is_admin){ cmsCore::error404(); }

    $inPage->addPathway($club['title'], '/clubs/'.$id);
    $inPage->addPathway($_LANG['SEND_MESSAGE']);
	$inPage->setTitle($_LANG['SEND_MESSAGE'].' - '.$club['title']);
	$inPage->backButton(false);

	if(!isset($_POST['gosend'])){
		$smarty = $inCore->initSmarty('components', 'com_clubs_messages_member.tpl');
		$smarty->assign('club', $club);
		$smarty->assign('bbcodetoolbar', cmsPage::getBBCodeToolbar('message'));
		$smarty->assign('smilestoolbar', cmsPage::getSmilesPanel('message'));
		$smarty->display('com_clubs_messages_member.tpl');
	} else {
		$errors = false;
		$message = $inCore->request('message', 'html', '');
		$message = $inCore->parseSmiles($message, true);
		$message = $inDB->escape_string($message);

		$total_list      = array();
		$moderators_list = clubModerators($id);
		$members_list    = clubMembers($id);
		$total_list 	 = $_POST['only_mod'] ? $moderators_list : array_merge ($moderators_list, $members_list);

		if (strlen($message)<3) { $inCore->addSessionMessage($_LANG['ERR_SEND_MESS'], 'error'); $errors = true; }
		if (!$total_list) { $inCore->addSessionMessage($_LANG['ERR_SEND_MESS_NO_MEMBERS'], 'error'); $errors = true; }
		if ($errors) { $inCore->redirect($back); }

		foreach ($total_list as $user_id){
			cmsUser::sendMessage(USER_UPDATER, $user_id, '<b>Сообщение от <a href="'.cmsUser::getProfileURL($inUser->login).'">Администратора</a> клуба "<a href="/clubs/'.$id.'">'.$club['title'].'</a>":</b><br> '.$message);
		}
		$_POST['only_mod'] ? $inCore->addSessionMessage($_LANG['SEND_MESS_TO_MODERS_OK'], 'info') : $inCore->addSessionMessage($_LANG['SEND_MESS_TO_MEMBERS_OK'], 'info');
		$inCore->redirect('/clubs/'.$id);

	}

}

///////////////////////// Пригласить друзей в группу /////////////////////////////////////////
if ($do=='join_member'){

    $user_id    = $inUser->id;
    $club       = $model->getClub($id);

	if (!$user_id || !$club){ cmsCore::error404(); }

	if ( !$inCore->inRequest('join') ){

		// Получаем список друзей
		$friends     	= cmsUser::getFriends($user_id);
		// Получаем участников клуба
        $moderators     = clubModerators($id);
        $members        = clubMembers($id);
        $userslist      = array_merge($moderators, $members);
		// Проверяем наличие друга в списке участников клуба или является ли он администратором
		foreach($friends as $key=>$friend){ 
			if (in_array($friend['id'], $userslist) || $friend['id'] == $club['admin_id']) { unset($friends[$key]); }
		}
		// Если нет друзей или все друзья уже в этом клубе, то выводим ошибку и возвращаемся назад
		if (!$_SESSION['user']['friends'] || !$friends) { $inCore->addSessionMessage($_LANG['SEND_INVITE_ERROR'], 'error'); $inCore->redirect($back); }
		// Формируем список option друзей
		foreach($friends as $friend){ 
			$friends_opt .= '<option value="'.$friend['id'].'">'.$friend['nickname'].'</option>';
		}
		// Заголовок страницы и пафвей
		$inPage->setTitle($_LANG['SEND_INVITE_CLUB'].' '.$club['title']);
		$inPage->addPathway($club['title'], '/clubs/'.$id);
		$inPage->addPathway($_LANG['SEND_INVITE_CLUB']);
		$inPage->backButton(false);
		// Выводим шаблон
		$smarty = $inCore->initSmarty('components', 'com_clubs_join_member.tpl');			
		$smarty->assign('club', $club);
		$smarty->assign('friends', $friends_opt);
		$smarty->display('com_clubs_join_member.tpl');

	}

	if ( $inCore->inRequest('join') ){

		$usr_to_id = $inCore->request('usr_to_id', 'int');
		if (!$usr_to_id){ cmsCore::error404(); }

		$club      = '<a href="/clubs/'.$id.'">'.$club['title'].'</a>';
        $user      = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';
		$link_join = '<a href="/clubs/'.$id.'/join.html">'.$_LANG['JOIN_CLUB'] .'</a>';

        $message   = $_LANG['INVITE_CLUB_TEXT'];
        $message   = str_replace('%user%', $user, $message);
        $message   = str_replace('%club%', $club, $message);
		$message   = str_replace('%link_join%', $link_join, $message);

		cmsUser::sendMessage(USER_UPDATER, $usr_to_id, $message);

		$inCore->addSessionMessage($_LANG['SEND_INVITE_OK'], 'info');
		$inCore->redirect('/clubs/'.$id);

	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
$inCore->executePluginRoute($do);
}
?>