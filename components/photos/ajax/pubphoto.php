<?php
	session_start();	

	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/includes/config.inc.php');
	include(PATH.'/includes/database.inc.php');
	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('user');

    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$inCore->loadLib('clubs');
	$inCore->loadLib('photos');

	$id = $inCore->request('id', 'int');

	if (!$id) return;

	$photo = dbGetFields('cms_photo_files', 'id='.$id, '*');
	$album = dbGetFields('cms_photo_albums', 'id='.$photo['album_id'], '*');
	
	if ($album['NSDiffer'] == 'club'){
		$ok = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($album['user_id'], $inUser->id) || clubUserIsRole($album['user_id'], $inUser->id, 'moderator');	
	} else {
		$ok = $inCore->userIsAdmin($inUser->id);
	}
	
    if($ok){
		$inDB->query("UPDATE cms_photo_files SET published = 1 WHERE id=$id");
		echo 'ok';
	} else {
		echo 'error';
	}	
?>