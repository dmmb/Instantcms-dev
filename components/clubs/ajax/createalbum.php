<?php
	session_start();	

	define("VALID_CMS", 1);

	define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inDB = cmsDatabase::getInstance();

    $inUser = cmsUser::getInstance();
    $inUser->update();

	$inCore->loadLib('clubs');
	$inCore->loadLib('photos');

	$title = iconv('UTF-8//IGNORE', 'WINDOWS-1251//IGNORE', $inCore->request('title', 'str'));	
	$clubid = $inCore->request('clubid', 'int');

	if (!$title) return;

	$club = $inDB->get_fields('cms_clubs', 'id='.$clubid, '*');
	$uid  = $inUser->id;
	
	if (!($club && $uid)){ echo 'error'; return;  }

    $is_admin 	= $inCore->userIsAdmin($uid) || clubUserIsAdmin($clubid, $uid);
    $is_moder 	= clubUserIsRole($clubid, $uid, 'moderator');
    $is_member 	= clubUserIsRole($clubid, $uid, 'member');
    $is_karma_enabled = ((cmsUser::getKarma($uid) >= $club['album_min_karma']) && $is_member) ? true : false;

    if ($is_admin || $is_moder || $is_karma_enabled){
        $ok     = albumCreate('club'.$clubid, clubRootAlbumId($clubid), $title, '', $clubid);
        $new_id = $inDB->get_last_id('cms_photo_albums');
    } else {
        $ok = false;
    }

    if ($ok) { echo $new_id; } else { echo 'error'; }

?>