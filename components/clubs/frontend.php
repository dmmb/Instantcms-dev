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

function clubs(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    global $_LANG;
	$inCore->includeFile("components/users/includes/usercore.php");
	$inCore->includeFile("components/blog/includes/blogcore.php");

	$inCore->loadLib("clubs");
	$inCore->loadLib("users");
	$inCore->loadLib("tags");
	$inCore->loadLib("photos");
	$inCore->loadLib("karma");

    $inCore->loadModel('clubs');
    $model = new cms_model_clubs();

	$inPage->addHeadCSS('components/blog/css/styles.css');
	$inPage->addHeadCSS('components/clubs/css/styles.css');
	$inPage->addHeadJS('components/clubs/js/clubs.js');

	//LOAD CONFIG
	$cfg = $inCore->loadComponentConfig('clubs');
	
	//SOME DEFAULT CONFIG VALUES
	if(!isset($cfg['enabled_blogs'])) { $cfg['enabled_blogs'] = 1; }
	if(!isset($cfg['enabled_photos'])) { $cfg['enabled_photos'] = 1; }	
	if(!isset($cfg['thumb1'])) { $cfg['thumb1'] = 48; }	
	if(!isset($cfg['thumb2'])) { $cfg['thumb2'] = 200; }	
	if(!isset($cfg['thumbsqr'])) { $cfg['thumbsqr'] = 1; }	
	if(!isset($cfg['cancreate'])) { $cfg['cancreate'] = 0; }	
	if(!isset($cfg['perpage'])) { $cfg['perpage'] = 10; }
    if(!isset($cfg['notify_in'])) { $cfg['notify_in'] = 1; }
    if(!isset($cfg['notify_out'])) { $cfg['notify_out'] = 1; }
	
	//INPUT PARAMETERS
	$menuid 	= $inCore->menuId();
	$id 		= $inCore->request('id', 'int', 0);
	$do 		= $inCore->request('do', 'str', 'view');

	$pagetitle = $inCore->menuTitle();
	if ($pagetitle){
		$inPage->addPathway($pagetitle);
	}
		
////////// VIEW ALL CLUBS ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	$user_id    = $inUser->id;

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
        $pagination = cmsPage::getPagebar($total, $page, $perpage, '/clubs/%menuid%-%page%', array('menuid'=>$menuid));
	}
	
	$can_create = $user_id && ( $inCore->userIsAdmin($user_id) || ($cfg['cancreate'] && !$inDB->get_field('cms_clubs', 'admin_id='.$user_id, 'id') && cmsUser::getKarma($user_id)>=$cfg['create_min_karma'] && cmsUser::getRating($user_id)>=$cfg['create_min_rating']));
	
	$smarty = $inCore->initSmarty('components', 'com_clubs_view.tpl');
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('menuid', $menuid);
	$smarty->assign('clubid', $id);
	$smarty->assign('can_create', $can_create);
	$smarty->assign('clubs', $clubs);
	$smarty->assign('total', $total);
	$smarty->assign('pagination', $pagination);	
	$smarty->display('com_clubs_view.tpl');

}
////////// VIEW SINGLE CLUB ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='club'){

	$club   = $model->getClub($id);

	$smarty = $inCore->initSmarty('components', 'com_clubs_view_club.tpl');			
	$smarty->assign('menuid', $menuid);
				
	if(!$club){
		//CLUB NOT FOuND
		$pagetitle = $_LANG['CLUB_NOT_FOUND'];
        $inPage->setTitle($_LANG['CLUB_NOT_FOUND']);
		$inPage->addPathway($_LANG['CLUB_NOT_FOUND']);
        $inPage->printHeading($_LANG['CLUB_NOT_FOUND']);
        return;
	}
    
    //TITLES
    $pagetitle = $club['title'];
    $inPage->setTitle($pagetitle);
    $inPage->addPathway($club['title']);

    $user_id    = $inUser->id;
    $is_admin 	= $inUser->is_admin || ($user_id == $club['admin_id']);
    $is_moder 	= clubUserIsRole($id, $user_id, 'moderator');
    $is_member 	= clubUserIsRole($id, $user_id, 'member');

    $is_access = true;

    if ($club['clubtype']=='private' && (!$is_admin && !$is_moder && !$is_member)){
        $is_access = false;
    }

    $is_karma_enabled = false;

    if ($user_id){
        $is_karma_enabled = (cmsUser::getKarma($user_id) >= $club['album_min_karma']) && clubUserIsMember($club['id'], $user_id) ? true : false;
    }

    //CHECK IMAGE
    if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/clubs/'.$club['imageurl'])){
            $club['imageurl'] = 'nopic.jpg';
        }
    }

    if (!clubRootAlbumId($id)) { albumCreateRoot($id, 'club'.$id); }

    //JOIN/LEAVE LINK
    $club['member_link'] = '';
    if ( clubUserIsMember($id, $user_id) ){
        $club['member_link'] = '<a href="/clubs/'.$menuid.'/'.$id.'/leave.html" class="leave">'.$_LANG['LEAVE_CLUB'].'</a>';;
    } 
    if ($club['clubtype']=='public' && ($user_id != $club['admin_id'])){
        $club['member_link'] = '<a href="/clubs/'.$menuid.'/'.$id.'/join.html" class="join">'.$_LANG['JOIN_CLUB'].'</a>';
    }

    //PARAMS
    $club['admin'] 			= clubAdminLink($id);
    $club['members'] 		= clubTotalMembers($id);
    $club['members_list'] 	= clubMembersList($id);
    $club['wall_html']		= cmsUser::getUserWall($club['id'], 'club');
    $club['addwall_html'] 	= cmsUser::getUserAddWall($club['id'], 'club');
    $club['blog_id']		= clubBlogId($club['id']);
    $club['blog_content']	= clubBlogContent($club['blog_id'], $is_admin, $is_moder, $is_member);
    $club['photo_albums']	= clubPhotoAlbums($club['id'],  $is_admin, $is_moder, $is_member);
    $club['root_album_id']	= clubRootAlbumId($club['id']);

    $club['enabled_blogs']	= $club['enabled_blogs'] == 1 || ($club['enabled_blogs']==0 && $cfg['enabled_blogs']==1);
    $club['enabled_photos']	= $club['enabled_photos'] == 1 || ($club['enabled_photos']==0 && $cfg['enabled_photos']==1);

    $smarty->assign('clubid', $id);
    $smarty->assign('club', $club);
    $smarty->assign('is_access', $is_access);
    $smarty->assign('uid', $user_id);
    $smarty->assign('is_admin', $is_admin);
    $smarty->assign('is_moder', $is_moder);
    $smarty->assign('is_member', $is_member);
    $smarty->assign('is_karma_enabled', $is_karma_enabled);

	$smarty->assign('pagetitle', $pagetitle);
	$smarty->display('com_clubs_view_club.tpl');
	
}
///////////////////////// CREATE CLUB ////////////////////////////////////////////////////////////////////////////
if ($do == 'create'){

	$inPage->backButton(false);

    $user_id = $inUser->id;

	if (!$user_id){ return; }

    $inPage->addPathway($_LANG['CREATE_CLUB']);

    $can_create = $user_id && ( $inCore->userIsAdmin($user_id) || ($cfg['cancreate'] && cmsUser::getKarma($user_id)>=$cfg['create_min_karma'] && cmsUser::getRating($user_id)>=$cfg['create_min_rating']));

    if (!$can_create){ return; }

    if ( !$inCore->inRequest('create') ){
        $inPage->setTitle($_LANG['CREATE_CLUB']);
        $smarty = $inCore->initSmarty('components', 'com_clubs_create.tpl');
        $smarty->assign('confirm', $confirm);
        $smarty->display('com_clubs_create.tpl');
    }

    if ( $inCore->inRequest('create') ){

        $title      = $inCore->request('title', 'str');
        $clubtype   = $inCore->request('clubtype', 'str');

        $created_id = $model->addClub(array('user_id'=>$user_id, 'title'=>$title, 'clubtype'=>$clubtype));

        if($created_id){
            setClubRating($created_id);
            $inCore->redirect('/clubs/'.$menuid.'/'.$created_id);
        } else {
            $inCore->redirect('/clubs/'.$menuid);
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

    if ( $inCore->inRequest('save') ){
        //save to database
        $description 		= $inCore->request('description', 'html', '');
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

            $filename       = md5($id . $user_id . time()).'.jpg';
            $uploadphoto    = $uploaddir . $filename;
            $uploadthumb    = $uploaddir . 'small/' . $filename;

            if ($inCore->moveUploadedFile($_FILES['picture']['tmp_name'], $uploadphoto, $_FILES['picture']['error'])) {
                    if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
                    @img_resize($uploadphoto, $uploadthumb, $cfg['thumb1'], $cfg['thumb1'], $cfg['thumbsqr']);
                    @img_resize($uploadphoto, $uploadphoto, $cfg['thumb2'], $cfg['thumb2'], $cfg['thumbsqr']);
            } else {
                $msg = $inCore->uploadError();
            }

            $model->updateClubImage($id, $filename);
        }

        $model->updateClub($id, array(
                                        'admin_id'=>$admin_id,
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

        $moders 		= $_POST['moderslist'] ? $_POST['moderslist'] : array();
        $members 		= $_POST['memberslist'] ? $_POST['memberslist'] : array();

        if ($moders) { if (array_search($admin_id, $moders)) { unset($moders[array_search($admin_id, $moders)]); }	}
        if ($members) { if (array_search($admin_id, $members)) { unset($members[array_search($admin_id, $members)]); }	}

        clubSaveUsers($id, $members, 'member', $clubtype, $cfg, $menuid);
        clubSaveUsers($id, $moders, 'moderator', $clubtype, $cfg, $menuid);

        $inCore->redirect('/clubs/'.$menuid.'/'.$id);
    }

    if ( !$inCore->inRequest('save') ){
        
        if ( !(clubUserIsAdmin($id, $user_id) || $inCore->userIsAdmin($user_id)) ){ return; }

        //show config form
        $inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$id);
        $inPage->addPathway($_LANG['CONFIG_CLUB']);
        $inPage->setTitle($_LANG['CONFIG_CLUB']);

        $moderators     = clubModerators($id);
        $members        = clubMembers($id);

        if ($moderators) { $moders_list = cmsUser::getAuthorsList($moderators); } else { $moders_list = ''; }
        if ($members) { $members_list = cmsUser::getAuthorsList($members); } else { $members_list = ''; }
        
        $userslist      = cmsUser::getUsersList(false, array_merge($moderators, $members, array($user_id)));

        if (array_search($user_nick, $userslist)) { unset($userslist[array_search($user_nick, $userslist)]); }

        $club['blog_id'] = clubBlogId($id);

        //CHECK IMAGE
        if (!$club['imageurl']) { $club['imageurl'] = 'nopic.jpg'; } else {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/images/clubs/'.$club['imageurl'])){
                $club['imageurl'] = 'nopic.jpg';
            }
        }

        $club['enabled_blogs']	= $club['enabled_blogs'] == 1 || ($club['enabled_blogs']==0 && $cfg['enabled_blogs']==1);
        $club['enabled_photos']	= $club['enabled_photos'] == 1 || ($club['enabled_photos']==0 && $cfg['enabled_photos']==1);

        $smarty = $inCore->initSmarty('components', 'com_clubs_config.tpl');
        $smarty->assign('menuid', $menuid);
        $smarty->assign('club', $club);
        $smarty->assign('moders_list', $moders_list);
        $smarty->assign('members_list', $members_list);
        $smarty->assign('users_list', $userslist);
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

    $inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$id);
    $inPage->addPathway($_LANG['EXIT_FROM_CLUB']);

    if ( $inCore->inRequest('confirm') ){
        clubRemoveUser($id, $inUser->id);
        setClubsRating();
        $inCore->redirect('/clubs/'.$menuid.'/'.$id);
    }

    if ( !$inCore->inRequest('confirm') ){
        if (!clubUserIsMember($id, $inUser->id)){ return; }

        $inPage->setTitle($_LANG['EXIT_FROM_CLUB']);

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

    $inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$id);
    $inPage->addPathway($_LANG['JOINING_CLUB']);

    if (clubUserIsMember($id, $inUser->id)){ return; }

    if ( $inCore->inRequest('confirm') ){        
        clubAddUser($id, $inUser->id);
        setClubsRating();
        $inCore->redirect('/clubs/'.$menuid.'/'.$id);
    }

    if ( !$inCore->inRequest('confirm') ) {

        $inPage->setTitle($_LANG['JOINING_CLUB']);

        $min_karma = $club['join_min_karma'];
        $user_karma = cmsUser::getKarma($inUser->id);

        if(($user_karma >= $min_karma) || !$club['join_karma_limit']){

            $inPage->backButton(false);
            $confirm['title'] = $_LANG['JOINING_CLUB'];
            $confirm['text'] = $_LANG['YOU_REALY_JOIN_TO'].' <strong>'.$club['title'].'</strong>?';
            $confirm['action'] = '';
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
            echo '<p>'.$_LANG['WANT_SEE'].' <a href="/users/'.$menuid.'/'.$uid.'/karma.html">'.$_LANG['HISTORY_YOUR_KARMA'].'</a>?</p>';
            
        }
    }

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
?>