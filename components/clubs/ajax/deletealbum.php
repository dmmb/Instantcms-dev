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
	$inCore->loadClass('actions');

    $inDB = cmsDatabase::getInstance();

    $inUser = cmsUser::getInstance();
    $inUser->update();
	if(!$inUser->id) { return; }

	$inCore->loadLib('clubs');
	$inCore->loadLib('photos');
	$inCore->loadLib('tags');

	$id = $inCore->request('id', 'int');
	$clubid = $inCore->request('clubid', 'int');

	if (!$id || !$clubid) { return; }

	$club = $inDB->get_fields('cms_clubs', 'id='.$clubid, '1');
	if(!$club) { return; }
    $inCore->loadModel('photos');
    $model = new cms_model_photos();
	
	if ($inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($clubid, $inUser->id) || clubUserIsRole($clubid, $inUser->id, 'moderator')){
		$ok = $model->deleteAlbum($id, 'club'.$clubid);
	} else {
		$ok = false;
	}

    if($ok){
		echo 'ok';
	} else {
		echo 'error';
	}	
?>