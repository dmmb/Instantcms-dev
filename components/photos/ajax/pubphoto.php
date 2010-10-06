<?php
	session_start();	
	if (!isset($_REQUEST['id'])) { die(100); } else { $id = (int)$_REQUEST['id']; }
	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');
    $inCore = cmsCore::getInstance();	// €дро
    $inCore->loadClass('config');       // конфигураци€
    $inCore->loadClass('db');           // база данных
    $inCore->loadClass('user');			// пользователь
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	if (!$inUser->update()) { $inCore->halt(); }
	$inUser->update();

	$inCore->loadLib('clubs');
	$inCore->loadLib('photos');

	$id = $inCore->request('id', 'int');

	$photo = dbGetFields('cms_photo_files', 'id='.$id, '*');
	$album = dbGetFields('cms_photo_albums', 'id='.$photo['album_id'], '*');
	
	if ($album['NSDiffer'] == 'club'){
		$ok = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($album['user_id'], $inUser->id) || clubUserIsRole($album['user_id'], $inUser->id, 'moderator');	
	} else {
		$ok = $inCore->userIsAdmin($inUser->id);
	}
    if($ok){
		$inDB->query("UPDATE cms_photo_files SET published = 1 WHERE id = '$id'");
		echo 'ok';
	} else {
		echo 'error';
	}	
?>