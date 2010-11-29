<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function orderForm($orderby, $orderto){
    $inCore = cmsCore::getInstance();
	$smarty = $inCore->initSmarty('components', 'com_photos_order.tpl');
	$smarty->assign('orderby', $orderby);
	$smarty->assign('orderto', $orderto);
	$smarty->display('com_photos_order.tpl');
}

function photos(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    global $_LANG;

	$cfg = $inCore->loadComponentConfig('photos');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }
	
	if (!isset($cfg['showlat'])) { $cfg['showlat'] = 1; }
	
	$inCore->loadLib('tags');
	$inCore->loadLib('clubs');

    $inCore->includeGraphics();

    $inCore->loadModel('photos');
    $model = new cms_model_photos();
	
	$root       = $inDB->get_fields('cms_photo_albums', "parent_id=0 AND NSDiffer=''", 'id, title');
	
	$id         = $inCore->request('id', 'int', $root['id']);
	$user_id    = $inCore->request('userid', 'int');
	$do         = $inCore->request('do', 'str', 'view');
	
/////////////////////////////// Просмотр альбома ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){ 

	//SHOW ALBUMS LIST
	$album = $model->getAlbum($id);

	$show_hidden = 0; //по-умолчанию не показывать скрытые фотки

    $owner = 'user';

	if(strstr($album['NSDiffer'],'club')) {
        $owner = 'club';
		$club = $inDB->get_fields('cms_clubs', 'id='.$album['user_id'], '*');
		$club['root_album_id'] = $inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer='club".$club['id']."' AND user_id = ".$club['id'], 'id');
		if ($inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator')) { $show_hidden = 1; } //показывать скрытые фотки админам и модераторам клуба
	} else {
		if ($inCore->userIsAdmin($inUser->id)) { $show_hidden = 1; } //показывать скрытые фотки админам
	}

    $can_view = true;

	//Заголовки страницы
	$menu_item = $inCore->getMenuItem($inCore->menuId());
	if ($album['parent_id']==0){
		if($id == $root['id']){
			$pagetitle = $inCore->menuTitle();
			if ($pagetitle) { $inPage->setTitle($pagetitle); } 
			else { $inPage->setTitle($_LANG['PHOTOGALLERY']); $pagetitle = $_LANG['PHOTOGALLERY']; }
		} elseif($owner == 'club') {
            $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
			$pagetitle = '<a href="/clubs/'.$club['id'].'">'.$club['title'].'</a> &rarr; '.$_LANG['PHOTOALBUMS'];
			$inPage->setTitle($_LANG['PHOTOALBUMS'].' - '.$club['title']);
			$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS']);
		}
	} else {
		$pagetitle =  $album['title'];
		if($owner == 'club') {
            $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
			$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS'], '/photos/'.$club['root_album_id']);
		} else {
			$inPage->setTitle($pagetitle  . ' - '.$_LANG['PHOTOGALLERY']);
			$left_key = $album['NSLeft'];
			$right_key = $album['NSRight'];
			$sql     = "SELECT id, title, NSLevel FROM cms_photo_albums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 AND NSDiffer = '' ORDER BY NSLeft";
			$rs_rows = $inDB->query($sql) or die('Error while building album path');
			while($pcat=$inDB->fetch_assoc($rs_rows)){
				if (!($menu_item['linktype'] == 'photoalbum' && $menu_item['linkid'] == $album['id'])) {
                $inPage->addPathway($pcat['title'], '/photos/'.$pcat['id']);
			}
		}				
		}				
		$inPage->setTitle($album['title']);
		$inPage->addPathway($album['title']);
	}

    if (!$can_view && $owner=='club') { $inCore->redirect('/clubs/'.$club['id']); }

	if (!isset($cfg['orderto'])) { $albums_orderto = 'ASC'; } else { $albums_orderto = $cfg['orderto']; }
	if (!isset($cfg['orderby'])) { $albums_orderby = 'title'; } else { $albums_orderby = $cfg['orderby']; }
	//Формируем подкатегории альбома
    $left_key   = $album['NSLeft'];
    $right_key  = $album['NSRight'];
    $subcats_list = $model->getSubAlbums($id, $left_key, $right_key, $albums_orderby, $albums_orderto);

	if(isset($cfg['maxcols'])) { $maxcols = $cfg['maxcols']; } else { $maxcols = 1; }
	if (strstr($album['NSDiffer'],'club') && $album['parent_id']==0) { $maxcols=1; }
	// если есть список категорий, то у каждой считаем кол-во подкатегорий
    if ($subcats_list){
		$is_subcats = true;
		$subcats    = array();
		$today1 = date("d-m-Y");
        foreach($subcats_list as $cat){
                    $sub = $inDB->rows_count('cms_photo_albums', 'NSLeft > '.$cat['NSLeft'].' AND NSRight < '.$cat['NSRight']);
                if ($sub>1) { $cat['subtext'] = '/'.$sub; } else { $cat['subtext'] = ''; }
				if ($cfg['is_today']) {
					$cat['today_count'] = (int)$inDB->get_field('cms_photo_files', "DATE_FORMAT(pubdate, '%d-%m-%Y') = '$today1' AND album_id={$cat['id']}", 'COUNT(id)');
        }
				if ($cfg['tumb_view'] == 2 && $cfg['tumb_from'] == 2) {
					$cat['iconurl'] = $model->randPhoto($cat['id'], true);
				} elseif ($cfg['tumb_view'] == 2 && $cfg['tumb_from'] == 1) {
					$cat['iconurl'] = $model->randPhoto($cat['id']);
				}
				$subcats[] = $cat;
		}
	} else {
		$is_subcats = false;
    }
																
	//формируем содержимое альбома
		if ($album){	

			$perpage = $album['perpage'];
			$page    = $inCore->request('page', 'int', 1);

			if(!$show_hidden) { $pubsql	= ' AND f.published = 1'; } else { $pubsql = ''; }

			//SQL запрос			
			$sql = "SELECT f.*,
                            IFNULL(r.total_rating, 0) as rating
					FROM cms_photo_files f
					LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target='photo'
					WHERE f.album_id = $id $pubsql
					";		
			
			//Сортировка
			if (isset($_POST['orderby'])) { 
				$orderby = $inCore->request('orderby', 'str');
				$_SESSION['ph_orderby'] = $orderby;
			} elseif(isset($_SESSION['ph_orderby'])) { 
				$orderby = $_SESSION['ph_orderby'];
			} else {
				$orderby = $album['orderby']; 
			}
			
			if (isset($_POST['orderto'])) { 
				$orderto = $inCore->request('orderto', 'str');;
				$_SESSION['ph_orderto'] = $orderto;
			} elseif(isset($_SESSION['ph_orderto'])) { 
				$orderto = $_SESSION['ph_orderto'];
			} else {
				$orderto = $album['orderto']; 
			}

            if (strlen($orderby)>12) { $orderby='title'; }
            if (strlen($orderto)>12) { $orderto='asc'; }

			if ($album['orderform'] && $root['id']!=$id){ echo orderForm($orderby, $orderto); }			
			$sql .=  " ORDER BY ".$orderby." ".$orderto." \n";
			
				$sql .= "LIMIT ".(($page-1)*$perpage).", $perpage";
			
			$result = $inDB->query($sql) ;
			
			//проверяем права доступа на добавление фото
			if (!$inUser->id) { $can_add = false; } 
			else {			
				if ($album['NSDiffer']=='') { $can_add = $inUser->id; } 
				elseif (strstr($album['NSDiffer'],'club')){
					$can_add = clubUserIsMember($club['id'], $inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || $inUser->is_admin;
				}				
			}
			$can_add_photo = false;
			if ($album['public'] && $can_add){
				$can_add_photo = true;
			}		
			
			if ($inDB->num_rows($result)){	

				if(!$show_hidden) { $totsql	= ' AND published = 1'; } else { $totsql = ''; }
				$total = $inDB->rows_count('cms_photo_files', "album_id = $id $totsq");

					if ($album['showtype'] == 'lightbox'){
						$inPage->addHeadJS('includes/jquery/lightbox/js/jquery.lightbox.js');
						$inPage->addHeadCSS('includes/jquery/lightbox/css/jquery.lightbox.css');
					}
				if ($show_hidden){
					$inPage->addHeadJS('components/photos/js/photos.js');
				}	
				$cons = array();
				$is_poto_yes = false;
					while($con = $inDB->fetch_assoc($result)){			
					$con['fpubdate'] 		= $inCore->dateformat($con['pubdate']);
					$con['commentscount'] 	= $album['showdate'] ? $inCore->getCommentsCount('photo', $con['id']) : 0;
						if ($album['showtype'] == 'lightbox'){
							$con['photolink'] 	= '/images/photos/medium/'.$con['file'];
							$con['photolink2'] 	= '/photos/photo'.$con['id'].'.html';
						} else {
							if ($album['showtype']!='fast'){
								$con['photolink'] 	= '/photos/photo'.$con['id'].'.html';
								$con['photolink2'] 	= '/photos/photo'.$con['id'].'.html';
							} else {
								$con['photolink']	= '/images/photos/'.$con['file'];
								$con['photolink2']	= '/images/photos/'.$con['file'];
						}
                    }
					$cons[] = $con;
				}
				$is_poto_yes = true;
			} else { 
                     if(!$subcats_list && $owner == 'club' && $album['parent_id']==0){ echo '<p>'.$_LANG['NO_SUB_ALBUMS'].'</p>'; }
					 $is_poto_yes = false;
					}
					
		}//END - ALBUM CONTENT
	// отдаем в шаблон
	$smarty = $inCore->initSmarty('components', 'com_photos_view.tpl');
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('root', $root);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('id', $id);
	$smarty->assign('photolink', $photolink);
	$smarty->assign('photolink2', $photolink2);
	$smarty->assign('album', $album);
	$smarty->assign('maxcols_foto', $album['maxcols']);
	$smarty->assign('can_add_photo', $can_add_photo);
	$smarty->assign('subcats', $subcats);
	$smarty->assign('cons', $cons);
	$smarty->assign('is_poto_yes', $is_poto_yes);
	$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/photos/%catid%-%page%', array('catid'=>$id)));
	$smarty->assign('is_subcats', $is_subcats);
	$smarty->assign('maxcols', $maxcols);
	$smarty->assign('total_foto', $total);
	$smarty->display('com_photos_view.tpl');
	// если есть фотограйии в альбоме и включены комментарии в альбоме, то показываем их
	if($album['is_comments'] && $is_poto_yes && $inCore->isComponentInstalled('comments')){
          $inCore->includeComments();
          comments('palbum', $album['id']);
     }
}
/////////////////////////////// VIEW PHOTO ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='viewphoto'){

	$sql = "SELECT f.id, f.album_id, f.title, f.description, f.pubdate, f.file, f.published, f.hits, f.comments, f.user_id,
			a.id cat_id, a.NSLeft as NSLeft, a.NSRight as NSRight, a.NSDiffer as NSDiffer, a.user_id as album_user_id, a.title cat_title, a.nav album_nav, a.public public, a.showtype a_type, a.showtags a_tags, a.bbcode a_bbcode, u.nickname, u.login, p.gender,
			IFNULL(r.total_rating, 0) as rating
			FROM cms_photo_files f
			LEFT JOIN cms_photo_albums a ON a.id = f.album_id
			LEFT JOIN cms_ratings_total r ON r.item_id = f.id AND r.target = 'photo'
			INNER JOIN cms_users u ON u.id = f.user_id
			INNER JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE f.id = '$id'";
			
	$result = $inDB->query($sql);

	if (!$inDB->num_rows($result)) { cmsCore::error404(); }

	$photo = $inDB->fetch_assoc($result);

	if (!$photo['published']) { echo '<div class="con_heading">'.$_LANG['WAIT_MODERING'].'</div>'; return; }
		
    $can_view = true;
	if (strstr($photo['NSDiffer'],'club')){
        $owner = 'club';
		$club = $inDB->get_fields('cms_clubs', 'id='.$photo['album_user_id'], 'id, title, clubtype');
        $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
		$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
		$inPage->addPathway($_LANG['PHOTOALBUMS'], '/photos/'.clubRootAlbumId($club['id']));            
	}

    if (!$can_view && $owner=='club') { $inCore->redirect('/clubs/'.$club['id']); }

	// Формируем глубиномер, заголовок страницы
	$left_key = $photo['NSLeft'];
	$right_key = $photo['NSRight'];
	$sql = "SELECT id, title, NSLevel FROM cms_photo_albums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 AND NSDiffer = '".$photo['NSDiffer']."' ORDER BY NSLeft";
	$rs_rows = $inDB->query($sql);
	while($pcat=$inDB->fetch_assoc($rs_rows)){
		$inPage->addPathway($pcat['title'], '/photos/'.$pcat['id']);
	}
	$inPage->addPathway($photo['title'], $_SERVER['REQUEST_URI']);
	$inPage->setTitle($photo['title']);
	// Обновляем количество просмотров фотографии
	$inDB->query("UPDATE cms_photo_files SET hits = hits + 1 WHERE id = '$id'");
								
	//навигация
	if($photo['album_nav']){
		$nextid = $inDB->get_fields('cms_photo_files', 'id<'.$photo['id'].' AND album_id = '.$photo['cat_id'].' AND published=1', 'id, file', 'id DESC');
		$previd = $inDB->get_fields('cms_photo_files', 'id>'.$photo['id'].' AND album_id = '.$photo['cat_id'].' AND published=1', 'id, file', 'id ASC');
	} else {
		$previd = false;
		$nextid = false;
	}

	$inCore->loadLib('karma');
		
	if ($photo['a_type'] != 'simple'){

		$is_author = $photo['user_id'] == $inUser->id;

		$photo['pubdate'] = $inCore->dateFormat($photo['pubdate']);
		if ($photo['public']){
			$photo['genderlink'] = cmsUser::getGenderLink($photo['user_id'], $photo['nickname'], 0, $photo['gender'], $photo['login']);
		}
		
		$photo['karma'] 		= cmsKarmaFormatSmall($photo['rating']);
		$photo['karma_buttons'] = cmsKarmaButtons('photo', $photo['id'], $photo['rating'], $is_author);
		
		if($cfg['link']){
			$file = PATH.'/images/photos/'.$photo['file'];
			if (file_exists($file)){
				$photo['file_orig'] = '<a href="/images/photos/'.$photo['file'].'" target="_blank">'.$_LANG['OPEN_ORIGINAL'].'</a>';
			}
		}

		if($photo['NSDiffer'] == ''){
			$is_admin = $inCore->userIsAdmin($inUser->id);
		}				
		if(strstr($photo['NSDiffer'],'club')){
			$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');
		}

		$is_can_operation = false;
		if(($photo['public'] && $inUser->id) || $inUser->is_admin){
			$is_can_operation = true;
		}

		$smarty = $inCore->initSmarty('components', 'com_photos_view_photo.tpl');
		$smarty->assign('photo', $photo);
		$smarty->assign('bbcode', '[IMG]http://'.$_SERVER['HTTP_HOST'].'/images/photos/medium/'.$photo['file'].'[/IMG]');
		$smarty->assign('previd', $previd);
		$smarty->assign('nextid', $nextid);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_author', $is_author);
		$smarty->assign('is_admin', $is_admin);
		$smarty->assign('is_can_operation', $is_can_operation);
		if($photo['a_tags']){
			$smarty->assign('tagbar', cmsTagBar('photo', $photo['id']));
		}
		$smarty->display('com_photos_view_photo.tpl');
		//если есть, выводим комментарии
		if($photo['comments'] && $inCore->isComponentInstalled('comments')){
			$inCore->includeComments();
			comments('photo', $photo['id']);
		}
	}			
}
/////////////////////////////// PHOTO UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addphoto'){

	$album = $model->getAlbum($id);

	$uid   = $inUser->id;
	$user_karma = cmsUser::getKarma($uid);
	
	//check add photos permission
	if (!$uid) { $can_add = false; } 
	else {		
		if ($album['NSDiffer']=='') { 
			$can_add = $uid; 
			$min_karma = false; 
			$club['id'] = false;
			$differ     = '';
		} elseif (strstr($album['NSDiffer'],'club')){
			$club = $inDB->get_fields('cms_clubs', 'id='.$album['user_id'], '*');
			$can_add = clubUserIsMember($club['id'], $uid) || clubUserIsAdmin($club['id'], $uid) || $inUser->is_admin;
			$differ  = 'club'.$club['id'];
			$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS']);
			$min_karma = $club['photo_min_karma'];
		}				
	}

	$inPage->addPathway($album['title'], '/photos/'.$album['id']);
	$inPage->addPathway($_LANG['ADD_PHOTO']);
	
	if ($uid){
		if ($model->loadedByUser24h($uid, $album['id'])<$album['uplimit'] || $album['uplimit'] == 0){	
			if ($album['public'] && $can_add){
			if ($min_karma === false || $user_karma>=$min_karma || clubUserIsAdmin($club['id'], $uid) || clubUserIsRole($club['id'], $uid, 'moderator')){
				$inPage->printHeading($_LANG['ADD_PHOTO']);
					if ($inCore->inRequest('upload')) {
						//first upload step
						if (isset($_POST['userid'])){
							$userid = $inCore->request('userid', 'int');
							if ($userid == $inUser->id){
								$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/photos/';		
								$realfile  = $_FILES['picture']['name'];
								$path_parts = pathinfo($realfile);
								$ext        = strtolower($path_parts['extension']);
								if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png' && $ext != 'bmp') { cmsCore::error404(); }
	
								$lid = $inDB->get_fields('cms_photo_files', 'id>0', 'id', 'id DESC');
								$lastid = $lid['id']+1;	
								$filename = md5($lastid . time() . $inUser->id).'.jpg';
								
								$source					= $_FILES['picture']['tmp_name'];
								$destination 			= $uploaddir . $filename;
								$uploadthumb['small'] 	= $uploaddir . 'small/' . $filename;
								$uploadthumb['medium'] 	= $uploaddir . 'medium/' . $filename;
															
								$errorCode 	= $_FILES['picture']['error'];
															
								if ($inCore->moveUploadedFile($source, $destination, $errorCode)) {
									@img_resize($destination, $uploadthumb['small'], $album['thumb1'], $album['thumb1'], $album['thumbsqr']);
									@img_resize($destination, $uploadthumb['medium'], $album['thumb2'], $album['thumb2'], false, $cfg['watermark']);
	
									if ( !isset($cfg['watermark']) ) 	{ $cfg['watermark'] = 0; 			}
									if ( $cfg['watermark'] ) 		 	{ @img_add_watermark($destination);	}
									if ( @!$cfg['saveorig'] )			{ @unlink($destination);			}
									
									$inPage->initAutocomplete();
									$autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');
									
									$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 2');
	
									$form_action = '/photos/'.$album['id'].'/addphoto.html';
									
									$smarty = $inCore->initSmarty('components', 'com_photos_add2.tpl');			
									$smarty->assign('form_action', $form_action);
									$smarty->assign('filename', $filename);
									$smarty->assign('autocomplete_js', $autocomplete_js);
									$smarty->display('com_photos_add2.tpl');
								} else {
									echo '<p><strong>'.$_LANG['ERROR'].':</strong> '.$inCore->uploadError().'!</p>';
								}
												
							} else { die($_LANG['ACCESS_DENIED']); }
						} else { die($_LANG['ACCESS_DENIED']); }
					
					} else {
						if (isset($_POST['submit'])){		
							//final upload step

                            $photo['album_id']      = $id;
							$photo['title']         = $inCore->request('title', 'str', $_LANG['PHOTO_WITHOUT_NAME']);
							$photo['description']   = $inCore->request('description', 'str');
							$photo['tags']          = $inCore->request('tags', 'str');
		
							$imageurl               = $inCore->request('imageurl', 'str');
							$imageurl               = str_replace("*", '',  $imageurl);
                            $photo['filename']      = $imageurl;
							
							if ($album['NSDiffer'] == ''){
								if ($album['public']==2 || $inCore->userIsAdmin($inUser->id)) { $published = 1; } else { $published = 0; }				
							} elseif (strstr($album['NSDiffer'], 'club')){
								if ($club['photo_premod'] && !clubUserIsAdmin($club['id'], $inUser->id) && !clubUserIsRole($club['id'], $inUser->id, 'moderator')) { 
									$published = 0; 
								} else { 
									$published = 1; 
								}
							}

                            $photo['published']     = $published;
                            $photo['showdate']      = 1;
																															
							//ADD TO ALBUM
							$photo_id = $model->addPhoto($photo, $differ);
							
							$inCore->redirect('/photos/'.$photo_id.'/uploaded.html');
								
						} else { 
							//upload form
							$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 1');
							
							$form_action = '/photos/'.$id.'/addphoto.html';
							
							$smarty = $inCore->initSmarty('components', 'com_photos_add1.tpl');			
							$smarty->assign('form_action', $form_action);
							$smarty->assign('user_id', $uid);
							$smarty->display('com_photos_add1.tpl');						
						}
					}
				} else {
					$inPage->printHeading($_LANG['NEED_KARMA']);
					echo '<p><strong>'.$_LANG['NEED_KARMA_TEXT'].'</strong></p>';
					echo '<p>'.$_LANG['NEEDED'].' '.$min_karma.', '.$_LANG['HAVE_ONLY'].' '.$user_karma.'.</p>';
					echo '<p>'.$_LANG['WANT_SEE'].' <a href="/users/'.$uid.'/karma.html">'.$_LANG['HISTORY_YOUR_KARMA'].'</a>?</p>';
				}	
			} else { echo '<p>'.$_LANG['YOU_CANT_ADD_PHOTO'].'</p>'; }
		} else { 
			echo '<div class="con_heading">'.$_LANG['ADD_PHOTO'].'</div>';
			echo '<div><strong>'.$_LANG['MAX_UPLOAD_IN_DAY'].'</strong> '.$_LANG['CAN_UPLOAD_TOMORROW'].'</div>';
		}
	} else { 
		cmsUser::goToLogin();
	}
}

/////////////////////////////// PHOTO UPLOADED ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='uploaded'){

	$id = $inCore->request('id', 'int', 0);

	$photo = $model->getPhoto($id);
	if (!$photo) { cmsCore::error404(); }

	$inPage->addPathway($_LANG['PHOTO_ADDED']);

		$smarty = $inCore->initSmarty('components', 'com_photos_added_f.tpl');			
		$smarty->assign('id', $id);
		$smarty->assign('photo', $photo);
		$smarty->display('com_photos_added_f.tpl');

}

/////////////////////////////// PHOTO EDIT ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editphoto'){
	$max_mb = 2; //max filesize in Mb
	
	$inCore->includeFile('components/users/includes/usercore.php');
	
	$photoid = $inCore->request('id', 'int', '');
		
	$photo = $model->getPhoto($photoid);
	if (!$photo) { cmsCore::error404(); }
	
	$album = $model->getAlbum($photo['album_id']);
	
	if (strstr($album['NSDiffer'],'club')){
		$club = $inDB->get_fields('cms_clubs', 'id='.$album['user_id'], 'id, title');
		$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
		$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
	} else {
		$is_admin = $inCore->userIsAdmin($inUser->id);
	}
	
	$is_author = ($inUser->id == $photo['user_id']);
	
	if ($inUser->id && ($is_admin || $is_author)){
	
		$sql = "SELECT * FROM cms_users WHERE id = ".$photo['user_id'];
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($_LANG['EDIT_PHOTO']);
			
            if ($inCore->inRequest('save')){
					$photo['title']         = $inCore->request('title', 'str', $_LANG['PHOTO_WITHOUT_NAME']);
					$photo['description']   = $inCore->request('description', 'str');
					$photo['tags']          = $inCore->request('tags', 'str');
                    $photo['filename']      = $inCore->request('file', 'str');
										
					//replace file					
					if (isset($_FILES['picture']['name'])){
						//upload new
						$uploaddir      = $_SERVER['DOCUMENT_ROOT'].'/images/photos/';
						$realfile       = $_FILES['picture']['name'];
						$filename       = md5($realfile.time()).'.jpg';
						$uploadfile     = $uploaddir . $realfile;
						$uploadphoto    = $uploaddir . $filename;
						$uploadthumb    = $uploaddir . 'small/' . $filename;
						$uploadthumb2   = $uploaddir . 'medium/' . $filename;
                       
						if ($_FILES['picture']['size'] <= $max_mb*1024*1024){
							if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
								if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
								@img_resize($uploadphoto, $uploadthumb, $album['thumb1'], $album['thumb1'], $album['thumbsqr']);
								@img_resize($uploadphoto, $uploadthumb2, $album['thumb2'], $album['thumb2'], false, $cfg['watermark']);

                                $photo['filename'] = $filename;

                                //delete old
                                $file = $photo['filename'];
                                $result = $inDB->query("SELECT id FROM cms_photo_files WHERE user_id = ".$inUser->id." AND file = '$file'") ;
                                if ($inDB->num_rows($result)){ //delete only if user is owner
                                    @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/'.$file);
                                    @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/small/'.$file);
                                    @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/medium/'.$file);
                                }
							}								
						}
					}
                    
					//UPDATE                    
					$model->updatePhoto($photo['id'], $photo);
					
					echo '<p><strong>'.$_LANG['PHOTO_SAVED'].'</strong></p>';
					echo '<p>&larr; <a href="/photos/photo'.$photo['id'].'.html">'.$_LANG['BACK_TO_PHOTO'].'</a><br/>
							 &larr; <a href="/photos/'.$photo['album_id'].'">'.$_LANG['BACK_TO_PHOTOALBUM'].'</a></p>';
					
				} else { 
							if(isset($_REQUEST['id'])){								
								$photo = $model->getPhoto($photoid);
								if (!$photo) { cmsCore::error404(); }

								$photo_max_size = ($max_mb * 1024 * 1024);
									
								$smarty = $inCore->initSmarty('components', 'com_photos_edit.tpl');
								$smarty->assign('photo', $photo);
								$smarty->assign('input_poto', '<input type="hidden" name="file" value="'.$photo['file'].'" />');
								$smarty->assign('action', '/photos/editphoto'.$photoid.'.html');
								$smarty->assign('images', '/images/photos/small/'.$photo['file'].'');
								$smarty->assign('photo_tag', cmsTagLine('photo', $photo['id'], false));
								$smarty->assign('photo_max_size', $photo_max_size);
								$smarty->display('com_photos_edit.tpl');

							} //isset photo id
							else { echo usrAccessDenied(); }
						}//print form
		} else { echo usrAccessDenied(); } //user exists
	} else { cmsUser::goToLogin(); }
}
/////////////////////////////// PHOTO MOVE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='movephoto'){

	$photo = $model->getPhoto($id);
	if (!$photo) { cmsCore::error404(); }
	
	$album = $model->getAlbum($photo['album_id']);

	if(strstr($album['NSDiffer'],'club')) { 
		$club = $inDB->get_fields('cms_clubs', 'id='.$album['user_id'], 'id, title');
		$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
		$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
	} else {
		$is_admin = $inCore->userIsAdmin($inUser->id);
	}

	if (!$is_admin) { cmsCore::error404(); }
			
	if (!isset($_POST['gomove'])){ //SHOW MOVE FORM

			$inPage->setTitle($_LANG['MOVE_PHOTO']);
			$inPage->addPathway($_LANG['MOVE_PHOTO'], $_SERVER['REQUEST_URI']);

			if ($album['NSDiffer'] == '') { 
				$fsql = "SELECT id, title FROM cms_photo_albums WHERE NSDiffer='' ORDER BY title";
			} elseif ($album['NSDiffer'] == 'club'.$club['id'].'') {
				$fsql = "SELECT id, title FROM cms_photo_albums WHERE NSDiffer='club{$club['id']}' AND parent_id>0 AND user_id = ".$club['id']." ORDER BY title";
			}
			$fresult = $inDB->query($fsql) ;
			if ($inDB->num_rows($fresult)){
				$html = '';
				while ($f = $inDB->fetch_assoc($fresult)){
					$html .= '<option value="'.$f['id'].'" ';
					if ($photo['album_id'] == $f['id']) { $html .= 'selected'; }
					$html .= '>--- '.$f['title'].'</option>';
				}
			}
			$smarty = $inCore->initSmarty('components', 'com_photos_move.tpl');
			$smarty->assign('photo', $photo);
			$smarty->assign('html', $html);
			$smarty->display('com_photos_move.tpl');

	} else { //DO MOVE
			
			if ($_POST['album_id']){				
				$fid = $inCore->request('album_id', 'int');
				$inDB->query("UPDATE cms_photo_files SET album_id = '$fid' WHERE id = '$id'") ;
			}
			header('location:/photos/'.$fid);
	}
	
}
/////////////////////////////// PHOTO DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delphoto'){

	$photo_id = $inCore->request('id', 'int', '');

	$photo = $model->getPhoto($photo_id);
	if (!$photo) { cmsCore::error404(); }

	$album = $model->getAlbum($photo['album_id']);
	
	if (strstr($album['NSDiffer'],'club')){
		$club = $inDB->get_fields('cms_clubs', 'id='.$album['user_id'], 'id, title');
		$inPage->addPathway($club['title'], '/clubs/'.$club['id']);
		$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
	} else {
		$is_admin = $inCore->userIsAdmin($inUser->id);
	}
	
	$is_author = ($inUser->id == $photo['user_id']);
	
	if ($inUser->id && ($is_admin || $is_author)){

		if (!isset($_POST['godelete'])){

			$inPage->addPathway($_LANG['DELETE_PHOTOGALLERY'], $_SERVER['REQUEST_URI']);
			$confirm['title']                   = $_LANG['DELETING_PHOTO'];
					$confirm['text']                    = ''.$_LANG['YOU_REALLY_DELETE_PHOTO'].' "'.$photo['title'].'"?';
			$confirm['action']                  = $_SERVER['REQUEST_URI'];
			$confirm['yes_button']              = array();
			$confirm['yes_button']['type']      = 'submit';
			$confirm['yes_button']['name']  	= 'godelete';
			$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
			$smarty->assign('confirm', $confirm);
			$smarty->display('action_confirm.tpl');

		} else {
            $model->deletePhoto($photo_id);
			header('location:/photos/'.$photo['album_id']);
		}
	} else { cmsUser::goToLogin(); }
}
/////////////////////////////// VIEW LATEST PHOTOS ///////////////////////////////////////////////////////////////////////////////////
if ($do=='latest'){

	$sql = "SELECT f.*, f.pubdate as fpubdate, a.id as album_id, a.title as album
			FROM cms_photo_files f
			LEFT JOIN cms_photo_albums a ON a.id = f.album_id
			WHERE f.published = 1
			ORDER BY pubdate DESC
			LIMIT 24";		

	$result = $inDB->query($sql) ;

	$inPage->addPathway($_LANG['NEW_PHOTO_IN_GALLERY'], $_SERVER['REQUEST_URI']);
	$inPage->setTitle($_LANG['NEW_PHOTO_IN_GALLERY']);
		
	if ($inDB->num_rows($result)){	
		$is_latest_yes = true;
		while($con = $inDB->fetch_assoc($result)){
			$con['fpubdate'] = $inCore->dateformat($con['fpubdate']);
			$con['comcount'] = $inCore->getCommentsCount('photo', $con['id']).'</td>';
			$cons[] = $con;
		}			
	} else { $is_latest_yes = false; }
	
	$smarty = $inCore->initSmarty('components', 'com_photos_latest.tpl');
	$smarty->assign('is_latest_yes', $is_latest_yes);
	$smarty->assign('maxcols', 4);
	$smarty->assign('cons', $cons);
	$smarty->display('com_photos_latest.tpl');
}
/////////////////////////////// VIEW BEST PHOTOS ///////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){

	$sql = "SELECT f.*, f.id as fid, f.pubdate as fpubdate, a.id as album_id, a.title as album, IFNULL(r.total_rating, 0) as rating
			FROM cms_photo_files f
			LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target = 'photo'
			LEFT JOIN cms_photo_albums a ON a.id = f.album_id
			WHERE f.published = 1
			ORDER BY rating DESC 
			LIMIT 24";

	$result = $inDB->query($sql) ;
		
	$inPage->addPathway($_LANG['BEST_PHOTOS'], $_SERVER['REQUEST_URI']);
	$inPage->setTitle($_LANG['BEST_PHOTOS']);

	if ($inDB->num_rows($result)){	
		$is_best_yes = true;
		$inCore->loadLib('karma');
		while($con = $inDB->fetch_assoc($result)){
			$con['rating']   = cmsKarmaFormat($con['rating']);
			$con['comcount'] = $inCore->getCommentsCount('photo', $con['id']);
			$cons[] = $con;
		}			
	} else { $is_best_yes = false; }
	
	$smarty = $inCore->initSmarty('components', 'com_photos_best.tpl');
	$smarty->assign('is_best_yes', $is_best_yes);
	$smarty->assign('maxcols', 4);
	$smarty->assign('cons', $cons);
	$smarty->display('com_photos_best.tpl');
}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
} //function
?>