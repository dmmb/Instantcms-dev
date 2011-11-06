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

    session_start();
	if (!isset($_REQUEST['id'])) { die(100); } else { $id = (int)$_REQUEST['id']; }
	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');
    
    $inCore = cmsCore::getInstance();	// €дро

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       // конфигураци€
    $inCore->loadClass('db');           // база данных
    $inCore->loadClass('user');			// пользователь
	$inCore->loadClass('actions');
    
	$inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$inUser->update();
	if (!$inUser->id) { $inCore->halt(); }

	$inCore->loadLib('clubs');
    $inCore->loadModel('photos');
    $model = new cms_model_photos();

	$id = $inCore->request('id', 'int');

	$photo = $model->getPhoto($id);
	
	$album = $model->getAlbum($photo['album_id']);
	
	if (strstr($album['NSDiffer'],'club')){
		$ok = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($album['user_id'], $inUser->id) || clubUserIsRole($album['user_id'], $inUser->id, 'moderator');	
	} else {
		$ok = $inCore->userIsAdmin($inUser->id);
	}
    if($ok){
		$inDB->query("UPDATE cms_photo_files SET published = 1 WHERE id = '$id'");
		cmsActions::log('add_photo', array(
			  'object' => $photo['title'],
			  'user_id' => $photo['user_id'],
			  'object_url' => '/photos/photo'.$photo['id'].'.html',
			  'object_id' => $photo['id'],
			  'target' => $album['title'],
			  'target_url' => '/photos/'.$photo['album_id'],
			  'description' => '<a href="/photos/photo'.$photo['id'].'.html" class="act_photo">
									<img border="0" src="/images/photos/small/'.$photo['file'].'" />
								  </a>'
		));
		echo 'ok';
	} else {
		echo 'error';
	}	
?>