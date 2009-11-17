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

function orderForm($orderby, $orderto){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$html = '';
	$html .= '<form action="" method="POST"><div class="photo_sortform"><table cellspacing="2" cellpadding="2" >' ."\n";
	 	$html .= '<tr>' ."\n";
			$html .= '<td>'.$_LANG['SORTING_PHOTOS'].': </td>' ."\n";
			$html .= '<td valign="top"><select name="orderby" id="orderby">' ."\n";
				$html .= '<option value="title" '; if($orderby=='title') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_TITLE'].'</option>' ."\n";
				$html .= '<option value="pubdate" '; if($orderby=='pubdate') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_DATE'].'</option>' ."\n";
				$html .= '<option value="rating" '; if($orderby=='rating') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_RATING'].'</option>' ."\n";
				$html .= '<option value="hits" '; if($orderby=='hits') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_HITS'].'</option>' ."\n";
			$html .= '</select> <select name="orderto" id="orderto">';
				$html .= '<option value="desc" '; if($orderto=='desc') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_DESC'].'</option>' ."\n";
				$html .= '<option value="asc" '; if($orderto=='asc') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_ASC'].'</option>' ."\n";
			$html .= '</select>';
			$html .= ' <input type="submit" value=">>" />' ."\n";
			$html .= '</td>' ."\n";
		$html .= '</tr>' ."\n";
	$html .= '</table></div></form>' ."\n";
	return $html;
}

function loadedByUser24h($user_id, $album_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT id FROM cms_photo_files WHERE user_id = $user_id AND album_id = $album_id AND pubdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
	$result = $inDB->query($sql) ;
	$loaded = $inDB->num_rows($result);	
	return $loaded;
}

function pageBar($cat_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$html = '';
	$result = $inDB->query("SELECT id FROM cms_photo_files WHERE album_id = $cat_id") ;
	$records = $inDB->num_rows($result);
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>'.$_LANG['PAGES'].': </strong></span>';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/photos/'.$inCore->menuId().'/'.@$_REQUEST['id'].'-'.$p;
					
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function photos(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    global $_LANG;
	$menuid = $inCore->menuId();
	$cfg = $inCore->loadComponentConfig('photos');
	
	if (!isset($cfg['showlat'])) { $cfg['showlat'] = 1; }
	
	$inCore->loadLib('tags');
	$inCore->loadLib('clubs');

    $inCore->includeGraphics();

    $inCore->loadModel('photos');
    $model = new cms_model_photos();
	
	$root       = dbGetFields('cms_photo_albums', "parent_id=0 AND NSDiffer=''", 'id, title');
	
	$id         = $inCore->request('id', 'int', $root['id']);
	$user_id    = $inCore->request('userid', 'int');
	$do         = $inCore->request('do', 'str', 'view');
	
/////////////////////////////// VIEW ALBUM ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){ 

	//SHOW ALBUMS LIST
	$album = dbGetFields('cms_photo_albums', 'id='.$id, '*');

	$show_hidden = 0; //по-умолчанию не показывать скрытые фотки

    $owner = 'user';

	if(strstr($album['NSDiffer'],'club')) {
        $owner = 'club';
		$club = dbGetFields('cms_clubs', 'id='.$album['user_id'], '*');
		$club['root_album_id'] = dbGetField('cms_photo_albums', "parent_id=0 AND NSDiffer='club".$club['id']."' AND user_id = ".$club['id'], 'id');
		if (clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator')) { $show_hidden = 1; } //показывать скрытые фотки админам и модераторам клуба
	}

    $can_view = true;

	//PAGE HEADING
	if ($album['parent_id']==0){
		if($id == $root['id']){
			$pagetitle = $inCore->menuTitle();
			if ($pagetitle) { $inPage->setTitle($pagetitle); } 
			else { $inPage->setTitle($_LANG['PHOTOGALLERY']); $pagetitle = $_LANG['PHOTOGALLERY']; }
		} elseif($owner == 'club') {
            $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
			$pagetitle = '<a href="/clubs/'.$menuid.'/'.$club['id'].'">'.$club['title'].'</a> &rarr '.$_LANG['PHOTOALBUMS'];
			$inPage->setTitle($_LANG['PHOTOALBUMS'].' - '.$club['title']);
			$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS']);
		}
	} else {
		$pagetitle =  $album['title'];
		if($owner == 'club') {
            $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
			$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS'], '/photos/'.$menuid.'/'.$club['root_album_id']);
		} else {
			$inPage->setTitle($pagetitle  . ' - '.$_LANG['PHOTOGALLERY']);
			$left_key = $album['NSLeft'];
			$right_key = $album['NSRight'];
			$sql     = "SELECT id, title, NSLevel FROM cms_photo_albums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 AND NSDiffer = '' ORDER BY NSLeft";
			$rs_rows = $inDB->query($sql) or die('Error while building album path');
			while($pcat=$inDB->fetch_assoc($rs_rows)){
                $inPage->addPathway($pcat['title'], '/photos/'.$menuid.'/'.$pcat['id']);
			}
		}				
		$inPage->setTitle($album['title']);
		$inPage->addPathway($album['title']);
	}

    if (!$can_view && $owner=='club') { $inCore->redirect('/clubs/0/'.$club['id']); }

	//TITLE
	echo '<h1 class="con_heading">'.$pagetitle.'</h1>';
	
	//LATEST AND TOP PHOTOS LINKS
	if ($id == $root['id'] && $cfg['showlat']){
		echo '<div class="photo_toolbar">';
			echo '<table border="0" cellspacing="0" cellpadding="5">';
			  echo '<tr>';
				echo '<td><img src="/components/photos/images/latest.gif" /></td>';
				echo '<td><a href="/photos/'.$menuid.'/latest.html">'.$_LANG['LAST_UPLOADED'].'</a></td>';
				echo '<td><img src="/components/photos/images/best.gif" /></td>';
				echo '<td><a href="/photos/'.$menuid.'/top.html">'.$_LANG['BEST_PHOTOS'].'</a></td>';
			  echo '</tr>';
			echo '</table>';
		echo '</div>';
	}
	
	if (!isset($cfg['orderto'])) { $albums_orderto = 'ASC'; } else { $albums_orderto = $cfg['orderto']; }
	if (!isset($cfg['orderby'])) { $albums_orderby = 'title'; } else { $albums_orderby = $cfg['orderby']; }

	//BUILD SUB-ALBUMS LIST
    $left_key   = $album['NSLeft'];
    $right_key  = $album['NSRight'];

    $subcats_list = $model->getSubAlbums($id, $left_key, $right_key, $albums_orderby, $albums_orderto);

	$col = 1; if(isset($cfg['maxcols'])) { $maxcols = $cfg['maxcols']; } else { $maxcols = 1; }
	if (strstr($album['NSDiffer'],'club') && $album['parent_id']==0) { $maxcols=1; }

    if ($subcats_list){
        echo '<table class="categorylist" style="margin-bottom:10px" cellspacing="3" width="100%" border="0">';
        foreach($subcats_list as $cat){
            if ($col==1) { echo '<tr>'; }
                echo '<td width="16" valign="top"><img src="/images/markers/photoalbum.png" border="0" /></td>';
                echo '<td width="" valign="top">';
                    //count subalbums
                    $sub = dbRowsCount('cms_photo_albums', 'NSLeft > '.$cat['NSLeft'].' AND NSRight < '.$cat['NSRight']);
                    if ($sub>1) { $subtext = '/'.$sub; } else { $subtext = ''; }
                    //print album
                    echo '<div><a href="/photos/'.$menuid.'/'.$cat['id'].'">'.$cat['title'].'</a> ('.$cat['content_count'].$subtext.')</div>';
                    if ($cat['description']) { echo '<div>'.$cat['description'].'</div>'; }
                echo '</td>';
            if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
        }
        if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
        echo '</table>';
    }
	//END - LIST OF ALBUMS
	
	//SHOW ALBUM CONTENT
		$sql = "SELECT * FROM cms_photo_albums WHERE id = $id LIMIT 1";				
		$result = $inDB->query($sql) ;
				
		if ($inDB->num_rows($result)==1){	
			$album = $inDB->fetch_assoc($result);
																
			$perpage = $album['perpage'];
			if (isset($_REQUEST['page'])) { $page = abs((int)$_REQUEST['page']); } else { $page = 1; }

			if (isset($userid)){
				$usersql = "AND f.user_id = ".$userid;
				$user    = dbGetField('cms_users', 'id='.$userid, 'nickname, login');
				echo '<div class="photo_userbar"><strong>'.$_LANG['USER_PHOTOS'].': </strong><a href="'.cmsUser::getProfileURL($user['login']).'">'.$user['nickname'].'</a> (<a href="/photos/'.$menuid.'/'.$id.'">'.$_LANG['SHOW_ALL'].'</a>)</div>';
			} else {
				$usersql = '';
			}

			if(!$show_hidden) { $pubsql	= ' AND f.published = 1'; } else { $pubsql = ''; }

			//SQL BUILD			
			$sql = "SELECT f.*,
							IF(DATE_FORMAT(f.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(f.pubdate, '<strong>{$_LANG['TODAY']}</strong> в %H:%i'),
							DATE_FORMAT(f.pubdate, '%d-%m-%Y'))  as fpubdate, IFNULL(AVG(r.points), 0) as rating
					FROM cms_photo_files f
					LEFT JOIN cms_ratings r ON r.item_id=f.id
					WHERE f.album_id = $id $pubsql $usersql
					GROUP BY f.id
					";		
			
			//ORDERING
			if (isset($_POST['orderby'])) { 
				$orderby = $_POST['orderby']; 
				$_SESSION['ph_orderby'] = $orderby;
			} elseif(isset($_SESSION['ph_orderby'])) { 
				$orderby = $_SESSION['ph_orderby'];
			} else {
				$orderby = $album['orderby']; 
			}
			
			if (isset($_POST['orderto'])) { 
				$orderto = $_POST['orderto']; 
				$_SESSION['ph_orderto'] = $orderto;
			} elseif(isset($_SESSION['ph_orderto'])) { 
				$orderto = $_SESSION['ph_orderto'];
			} else {
				$orderto = $album['orderto']; 
			}

			if ($album['orderform'] && $root['id']!=$id){ echo orderForm($orderby, $orderto); }			
			$sql .=  " ORDER BY ".$orderby." ".$orderto." \n";
			
			if (!isset($userid)){
				$sql .= "LIMIT ".(($page-1)*$perpage).", $perpage";
			}
			
			$result = $inDB->query($sql) or die(mysql_error().'<br/><br/>'.$sql);

			$col = 1; $maxcols = $album['maxcols'];
			
			//check add photos permission
			if (!$inUser->id) { $can_add = false; } 
			else {			
				if ($album['NSDiffer']=='') { $can_add = $inUser->id; } 
				elseif (strstr($album['NSDiffer'],'club')){
					$can_add = clubUserIsMember($club['id'], $inUser->id) || clubUserIsAdmin($club['id'], $inUser->id);
				}				
			}
			
			if ($album['public'] && @$can_add){
				echo '<table cellpadding="2" cellspacing="0" style="margin-bottom:10px">';
					echo '<tr><td><img src="/components/photos/images/addphoto.gif" border="0"/></td>'."\n";
					echo '<td><a style="text-decoration:underline" href="/photos/'.$menuid.'/'.$album['id'].'/addphoto.html">'.$_LANG['ADD_PHOTO_TO_ALBUM'].'</a></td></tr>'."\n";
				echo '</table>';
			}		
			
			if ($inDB->num_rows($result)){	
			
				if ($album['showtype'] == 'list'){
				//VIEW AS TABLE (LIST)			
					echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
					while($con = $inDB->fetch_assoc($result)){
						if ($col==1) { echo '<tr>'; }
							echo '<td width="20" valign="top"><img src="/images/markers/photo.png" border="0" /></td>';
							echo '<td width="" valign="top">';
									echo '<a href="/photos/'.$menuid.'/photo'.$con['id'].'.html">'.$con['title'].'</a>';
							echo '</td>';	
							if($album['showdate']){
								$fcols = 6;
								echo '<td width="16" valign="top"><img src="/images/icons/comments.gif" alt="'.$_LANG['COMMENTS'].'" border="0"/></td>';
								echo '<td width="25" valign="top"><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html#c" title="'.$_LANG['COMMENTS'].'">'.$inCore->getCommentsCount('photo', $photo['id']).'</a></td>';
								echo '<td width="16" valign="top" class="photo_date_td"><img src="/images/icons/date.gif" alt="'.$_LANG['PUB_DATE'].'" /></td>';
								echo '<td width="70" align="center" valign="top" class="photo_date_td">'.$con['pubdate'].'</td>';			
							} else {
								$fcols = 2;
							}
						if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; };
					}					
					if ($col>1) { echo '<td colspan="'.(($maxcols-$col+1)*$fcols).'">&nbsp;</td></tr>'; }
					echo '</table>';
					echo pageBar($id, $page, $perpage);					
				}
				
				if ($album['showtype'] != 'list'){
				//VIEW AS GALLERY (SIMPLE)			
					if ($album['showtype'] == 'lightbox'){
						$inPage->addHeadJS('includes/jquery/lightbox/js/jquery.lightbox.js');
						$inPage->addHeadCSS('includes/jquery/lightbox/css/jquery.lightbox.css');
					}
					echo '<div class="photo_gallery">';
					echo '<table cellpadding="5" cellspacing="0" border="0" width="100%"> ';
					while($con = $inDB->fetch_assoc($result)){			
						if ($album['showtype'] == 'lightbox'){
							$photolink = '/images/photos/medium/'.$con['file'];
							$photolink2 = '/photos/'.$menuid.'/photo'.$con['id'].'.html';
						} else {
							if ($album['showtype']!='fast'){
								$photolink = '/photos/'.$menuid.'/photo'.$con['id'].'.html';
								$photolink2 = '/photos/'.$menuid.'/photo'.$con['id'].'.html';
							} else {
								$photolink = '/images/photos/'.$con['file'];
								$photolink2 = '/images/photos/'.$con['file'];
							}
						}
						if ($col==1) { echo '<tr>'; } echo '<td align="center" valign="middle">';
						echo '<div class="'.$album['cssprefix'].'photo_thumb">';
						echo '<table width="100%" height="100" cellspacing="0" cellpadding="4">
							  <tr>
							  <td valign="middle" align="center">';
								echo '<a class="lightbox-enabled" rel="lightbox-galery" href="'.$photolink.'" title="'.$con['title'].'">';
									echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].'" border="0" />';
								echo '</a>';
						echo '</td></tr>';
						echo '<tr><td align="center"><a href="'.$photolink2.'" title="'.$con['title'].'">'.$con['title'].'</a></td></tr>';
						if ($show_hidden && $con['published']==0){
							$inPage->addHeadJS('components/photos/js/photos.js');
							echo '<tr id="moder'.$con['id'].'"><td align="center">
								<div style="margin-top:4px">'.$_LANG['WAIT_MODERING'].'</div>
								<div><a href="javascript:publishPhoto('.$con['id'].')" style="color:green">'.$_LANG['PUBLISH'].'</a> | <a href="/photos/'.$menuid.'/delphoto'.$con['id'].'.html" style="color:red">'.$_LANG['DELETE'].'</a></div>
							</td></tr>';						
						}
						echo '</table>';
						echo '</div>';
						
						echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
					}
					if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
					echo '</table>';
					echo '</div>';
					echo pageBar($id, $page, $perpage);

                    if($album['is_comments'] && $inCore->isComponentInstalled('comments')){
                        $inCore->includeComments();
                        comments('palbum', $album['id']);
                    }

				}
			} else { 
					 if($album['parent_id']>0) { echo '<p>'.$_LANG['NOT_PHOTOS_IN_ALBUM'].'</p>'; }
					}
					
		}//END - ALBUM CONTENT
}
/////////////////////////////// VIEW PHOTO ///////////////////////////////////////////////////////////////////////////////////////////
if($do=='viewphoto'){
	$sql = "SELECT f.*, DATE_FORMAT(f.pubdate, '%d-%m-%Y') pubdate, 
					a.id cat_id, a.NSLeft as NSLeft, a.NSRight as NSRight, a.NSDiffer as NSDiffer, a.user_id as album_user_id, a.title cat_title, 
					a.nav album_nav, a.public public, a.showtype a_type, a.showtags a_tags, a.bbcode a_bbcode
			FROM cms_photo_files f, cms_photo_albums a
			WHERE f.id = $id AND f.album_id = a.id
			";
			
	$result = $inDB->query($sql);

	if ($inDB->num_rows($result)){
		$photo = $inDB->fetch_assoc($result);

        $can_view = true;
		if (strstr($photo['NSDiffer'],'club')){
            $owner = 'club';
			$club = dbGetFields('cms_clubs', 'id='.$photo['album_user_id'], '*');
            $can_view = $club['clubtype'] == 'public' || ($club['clubtype'] == 'private' && (clubUserIsMember($club['id'], $inUser->id) || $inUser->is_admin || clubUserIsAdmin($club['id'], $inUser->id)));
			$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS'], '/photos/'.$menuid.'/'.clubRootAlbumId($club['id']));            
		}

        if (!$can_view && $owner=='club') { $inCore->redirect('/clubs/0/'.$club['id']); }

		//PATHWAY ENTRY
		$left_key = $photo['NSLeft'];
		$right_key = $photo['NSRight'];
		$sql = "SELECT id, title, NSLevel FROM cms_photo_albums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 AND NSDiffer = '".$photo['NSDiffer']."' ORDER BY NSLeft";
		$rs_rows = $inDB->query($sql);
		while($pcat=$inDB->fetch_assoc($rs_rows)){
				$inPage->addPathway($pcat['title'], '/photos/'.$menuid.'/'.$pcat['id']);
		}
		$inPage->addPathway($photo['title'], $_SERVER['REQUEST_URI']);
		
		$inDB->query("UPDATE cms_photo_files SET hits = hits + 1 WHERE id = $id");
		
		$inPage->setTitle($photo['title']);
        $inPage->addHeadJS('core/js/karma.js');
								
		echo '<div class="con_heading">'.$photo['title'].'</div>';

		//PREV AND NEXT IMAGES
		if($photo['album_nav']){
			$previd = dbGetFields('cms_photo_files', 'id<'.$photo['id'].' AND album_id = '.$photo['cat_id'].' AND published=1', 'id, file', 'id DESC');
			$nextid = dbGetFields('cms_photo_files', 'id>'.$photo['id'].' AND album_id = '.$photo['cat_id'].' AND published=1', 'id, file', 'id ASC');
		} else {
			$previd = false;
			$nextid = false;		
		}

		//DRAW IMAGE
		echo '<table width="100%" cellpadding="5" cellspacing="0">';
			echo '<tr><td colspan="3" align="center"><div class="photo_desc">'.$photo['description'].'</div></td></tr>';		
			//BACK LINKS
			echo '<tr><td colspan="3" align="center">
						<div>&larr; '.$_LANG['BACK_TO'].' <a href="/photos/'.$menuid.'/'.$photo['cat_id'].'">'.$_LANG['TO_ALBUM'].'</a>';
			if ($photo['NSDiffer']==''){ echo '| <a href="/photos/'.$menuid.'">'.$_LANG['TO_LIST_ALBUMS'].'</a></div>'; }
			echo '</td></tr>';
			//PHOTO		
			echo '<tr>';
				echo '<td style="text-align:center"><img src="/images/photos/medium/'.$photo['file'].'" border="0" /></td>';
			echo '</tr>';
			//BBCODE
			if($photo['a_bbcode']){
				echo '<tr><td style="text-align:center">';			
					$bbcode = '[IMG]http://'.$_SERVER['HTTP_HOST'].'/images/photos/medium/'.$photo['file'].'[/IMG]';
					echo '<label for="bbcode">'.$_LANG['CODE_INPUT_TO_FORUMS'].': </label><input type="text" id="bbcode" name="bbcode" class="photo_bbinput" value="'.$bbcode.'"/>';
				echo '</td></tr>';
			}
			//Navigation
			if($photo['album_nav']){
				echo '<tr><td>';
					echo '<div class="photo_nav">';
						echo '<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto"><tr>';
							if ($previd){
								echo '<td align="right">';
									echo '<div>&larr; <a href="/photos/'.$menuid.'/photo'.$previd['id'].'.html">'.$_LANG['PREVIOUS'].'</a></div>';
								echo '</td>';
							}
							if ($previd && $nextid) { echo '<td>|</td>'; }
							if ($nextid){
								echo '<td align="left">';
									echo '<div><a href="/photos/'.$menuid.'/photo'.$nextid['id'].'.html">'.$_LANG['NEXT'].'</a> &rarr;</div>';
								echo '</td>';
							}						
						echo '</tr></table>';
					echo '</div>';			
				echo '</td></tr>';
			}
		echo '</table>';
				
		$inCore->loadLib('karma');
		
		if ($photo['a_type'] != 'simple'){
			echo '<div class="photo_bar">';
				echo '<table width="" cellspacing="0" cellpadding="4" align="center"><tr>';
					echo '<td width=""><strong>'.$_LANG['ADDED'].':</strong> '.$photo['pubdate'].'</td>';
					if ($photo['public']){
						$usr = dbGetFields('cms_users', 'id='.$photo['user_id'], 'id, nickname, login');
						if ($usr['id']){							
							echo '<td>'.cmsUser::getGenderLink($usr['id'], $usr['nickname'], 0, '', $usr['login']).'</td>';
						}
					}

					$karma = cmsKarma('photo', $photo['id']);

					echo '<td width=""><strong>'.$_LANG['HITS'].': </strong> '.$photo['hits'].'</td>';
					echo '<td width=""><strong>'.$_LANG['RATING'].': </strong><span id="karmapoints">'.cmsKarmaFormatSmall($karma['points']).'</span></td>';
					
					echo '<td width="">'.cmsKarmaButtons('photo', $photo['id']).'</td>';
		
					if($cfg['link']){
						$file = PATH.'/images/photos/'.$photo['file'];
						if (file_exists($file)){
							echo '<td><a href="/images/photos/'.$photo['file'].'" target="_blank">'.$_LANG['OPEN_ORIGINAL'].'</a></td>';
						}
					}
					
					$is_author = $usr['id']==$inUser->id;
					if($photo['NSDiffer'] == ''){
						$is_admin = $inCore->userIsAdmin($inUser->id);
					}				
					if(strstr($photo['NSDiffer'],'club')){
						$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');
					}
					
					if(($photo['public'] && $inUser->id) || $inUser->is_admin){
						if ($is_author || $is_admin){
							echo '<td><a href="/photos/'.$menuid.'/editphoto'.$photo['id'].'.html" title="'.$_LANG['EDIT'].'"><img src="/images/icons/edit.gif" border="0"/></a></td>';
							if ($is_admin){
								echo '<td><a href="/photos/'.$menuid.'/movephoto'.$photo['id'].'.html" title="'.$_LANG['MOVE'].'"><img src="/images/icons/move.gif" border="0"/></a></td>';
							}
							echo '<td><a href="/photos/'.$menuid.'/delphoto'.$photo['id'].'.html" title="'.$_LANG['DELETE'].'"><img src="/images/icons/delete.gif" border="0"/></a></td>';
						}
					}
				echo '</tr></table>';
			echo '</div>';
			
			if($photo['a_tags']){
				$inCore->loadLib('tags');
				echo cmsTagBar('photo', $photo['id']);
			}

			//show user comments
			if($photo['comments'] && $inCore->isComponentInstalled('comments')){
				$inCore->includeComments();
				comments('photo', $photo['id']);
			}
			
		}			
	}
}
/////////////////////////////// PHOTO UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addphoto'){

	$sql = "SELECT * FROM cms_photo_albums WHERE id = $id";
	$result = $inDB->query($sql) ;
	if ($inDB->num_rows($result)) { $album = $inDB->fetch_assoc($result); } else { die($_LANG['ALBUM_NOT_FOUND']); }

	$uid = $inUser->id;
	$user_karma = cmsUser::getKarma($uid);
	
	//check add photos permission
	if (!$uid) { $can_add = false; } 
	else {		
		if ($album['NSDiffer']=='') { 
			$can_add = $uid; 
			$min_karma = false; 
			$club['id'] = false;
		} elseif (strstr($album['NSDiffer'],'club')){
			$club = dbGetFields('cms_clubs', 'id='.$album['user_id'], '*');
			$can_add = clubUserIsMember($club['id'], $uid) || clubUserIsAdmin($club['id'], $uid);		
			$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
			$inPage->addPathway($_LANG['PHOTOALBUMS']);
			$min_karma = $club['photo_min_karma'];
		}				
	}

	$inPage->addPathway($album['title'], '/photos/'.$menuid.'/'.$album['id']);
	$inPage->addPathway($_LANG['ADD_PHOTO']);
	
	if ($uid){
		if (loadedByUser24h($uid, $album['id'])<$album['uplimit'] || $album['uplimit'] == 0){	
			if ($album['public'] && $can_add){
			if ($min_karma === false || $user_karma>=$min_karma || clubUserIsAdmin($club['id'], $uid) || clubUserIsRole($club['id'], $uid, 'moderator')){
				$inPage->printHeading($_LANG['ADD_PHOTO']);
					if ($inCore->inRequest('upload')) {
						//first upload step
						if (isset($_POST['userid'])){
							$userid = $_POST['userid'];
							if ($userid == $inUser->id){
								$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/photos/';		
								$realfile = $_FILES['picture']['name'];
	
								$lid = dbGetFields('cms_photo_files', 'id>0', 'id', 'id DESC');
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
									if ( $cfg['watermark'] ) 		 	{ @img_add_watermark($uploadphoto);	}
									if ( @!$cfg['saveorig'] )			{ @unlink($uploadphoto);			}
									
									$inPage->initAutocomplete();
									$autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');
									
									$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 2');
	
									$form_action = '/photos/'.$menuid.'/'.$album['id'].'/addphoto.html';
									
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
		
							$imageurl               = str_replace("'", '',  $inCore->request('imageurl', 'str'));
							$imageurl               = str_replace("*", '',  $imageurl);
							$imageurl               = str_replace("/", '',  $imageurl);
							$imageurl               = str_replace('\\', '', $imageurl);
                            $photo['filename']      = $imageurl;
							
							if ($album['NSDiffer'] == ''){
								if ($album['public']==2) { $published = 1; } else { $published = 0; }				
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
							$photo_id = $model->addPhoto($photo);
							
							$inCore->redirect('/photos/'.$menuid.'/'.$photo_id.'/uploaded.html');
								
						} else { 
							//upload form
							$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 1');
							
							$form_action = '/photos/'.$menuid.'/'.$id.'/addphoto.html';
							
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
					echo '<p>'.$_LANG['WANT_SEE'].' <a href="/users/'.$menuid.'/'.$uid.'/karma.html">'.$_LANG['HISTORY_YOUR_KARMA'].'</a>?</p>';
				}	
			} else { echo '<p>'.$_LANG['YOU_CANT_ADD_PHOTO'].'</p>'; }
		} else { 
			echo '<div class="con_heading">'.$_LANG['ADD_PHOTO'].'</div>';
			echo '<div><strong>'.$_LANG['MAX_UPLOAD_IN_DAY'].'</strong> '.$_LANG['CAN_UPLOAD_TOMORROW'].'</div>';
		}
	}//auth 
	else { 
		echo '<div class="con_heading">'.$_LANG['NEED_REGISTRATION'].'</div>';
		echo '<div>'.$_LANG['NEED_REGISTRATION_TEXT'].'</div>';
		echo '<p><a href="/registration">'.$_LANG['GOTO_REGISTRATION'].'</a></p>';
	}
}

/////////////////////////////// PHOTO UPLOADED ////////////////////////////////////////////////////////////////////////////////////////
if ($do=='uploaded'){
	$id = $inCore->request('id', 'int', 0);
	$photo = dbGetFields('cms_photo_files', 'id='.$id, 'album_id, published');
	
	if ($id && $photo['published']!==false){
		echo '<p><strong>'.$_LANG['PHOTO_ADDED'].'</strong></p>';
		if (!$photo['published']) { echo '<p>'.$_LANG['PHOTO_PREMODER_TEXT'].'</p>'; }
		echo '<ul>';
			echo '<li><a href="/photos/'.$menuid.'/photo'.$id.'.html">'.$_LANG['GOTO_PHOTO'].'</a></li>';
			echo '<li><a href="/photos/'.$menuid.'/'.$photo['album_id'].'/addphoto.html">'.$_LANG['ADD_MORE_PHOTO'].'</a></li>';
			echo '<li><a href="/photos/'.$menuid.'/'.$photo['album_id'].'">'.$_LANG['BACK_TO_PHOTOALBUM'].'</a></li>';
		echo '</ul>';
	}
}

/////////////////////////////// PHOTO EDIT ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editphoto'){
	$max_mb = 2; //max filesize in Mb
	
	$inCore->includeFile('components/users/includes/usercore.php');
	
	$photoid = @intval($_REQUEST['id']);	
	$photo = dbGetFields('cms_photo_files', 'id='.$photoid, '*');
	
	if (!$photo) { $inCore->redirect('/photos/'.$menuid); }
	
	$album = dbGetFields('cms_photo_albums', 'id='.$photo['album_id'], '*');
	
	if ($album['NSDiffer'] == 'club'){
		$club = dbGetFields('cms_clubs', 'id='.$album['user_id'], '*');
		$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
		$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
	} else {
		$is_admin = $inCore->userIsAdmin($inUser->id);
	}
	
	$is_author = ($inUser->id == $photo['user_id']);
	
	if ($inUser->id && ($is_admin||$is_author)){
	
		$sql = "SELECT * FROM cms_users WHERE id = ".$photo['user_id'];
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($_LANG['EDIT_PHOTO']);
			echo '<div class="con_heading">'.$_LANG['EDIT_PHOTO'].'</div>';
			
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
                                $file = $_POST['file'];
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
					echo '<p>&larr; <a href="/photos/'.$menuid.'/photo'.$photo['id'].'.html">'.$_LANG['BACK_TO_PHOTO'].'</a><br/>
							 &larr; <a href="/photos/'.$menuid.'/'.$photo['album_id'].'">'.$_LANG['BACK_TO_PHOTOALBUM'].'</a></p>';
					
				} else { 
							if(isset($_REQUEST['id'])){								
								$sql = "SELECT * FROM cms_photo_files WHERE id = $photoid";
								$result = $inDB->query($sql) ;
								if ($inDB->num_rows($result)){	
									$photo = $inDB->fetch_assoc($result);		
									ob_start(); ?>
									
									<form action="/photos/<?php echo $menuid?>/editphoto<?php echo $photoid?>.html" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="file" value="<?php echo $photo['file']?>" />
									<table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="120" valign="top"><table width="110" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td width="110" align="center" valign="top" style="border:solid 1px gray; padding:5px; background-color:#FFFFFF;"><img src="/images/photos/small/<?php echo $photo['file']?>" border="0" style="border:solid 1px black" /></td>
                                          </tr>
                                        </table></td>
                                        <td align="right" valign="top"><table width="409">
                                          <tr>
                                            <td width="401" valign="top"><strong><?php echo $_LANG['PHOTO_TITLE']; ?>: </strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input name="title" type="text" id="title" size="40" maxlength="250" value="<?php echo $photo['title']?>"/></td>
                                          </tr>
                                          <tr>
                                            <td width="401" valign="top"><strong><?php echo $_LANG['PHOTO_DESCRIPTION']; ?>: </strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><textarea name="description" cols="50" rows="8" id="description"><?php echo $photo['description']?></textarea></td>
                                          </tr>
                                          <tr>
                                            <td><strong><?php echo $_LANG['TAGS']; ?>:</strong></td>
                                          </tr>
                                          <tr>
                                            <td><input name="tags" type="text" id="music" size="40" value="<?php if (isset($photo['id'])) { echo cmsTagLine('photo', $photo['id'], false); } ?>"/>
                                            <br />
                                            <small><?php echo $_LANG['KEYWORDS']; ?></small></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><strong><?php echo $_LANG['REPLACE_FILE']; ?>:</strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input name="MAX_FILE_SIZE" type="hidden" value="<?php echo ($max_mb * 1024 * 1024)?>"/>
                                              <input name="picture" type="file" size="30" /></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input style="margin-top:10px;font-size:18px" type="submit" name="save" value="<?php echo $_LANG['SAVE']; ?>" />
											<input style="margin-top:10px;font-size:18px" type="button" name="cancel" value="<?php echo $_LANG['CANCEL']; ?>" onclick="window.history.go(-1);"/></td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                    </table>
									</form>
									
									<?php 
									echo ob_get_clean();
								}//photo exists
								else { usrAccessDenied(); }
							} //isset photo id
							else { usrAccessDenied(); }
						}//print form
		} else { usrAccessDenied(); } //user exists
	}//auth
	else { usrAccessDenied(); }
}
/////////////////////////////// PHOTO MOVE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='movephoto'){
	$inCore->includeFile('components/users/includes/usercore.php');
	
	$photo = dbGetFields('cms_photo_files', 'id='.$id, '*');
	
	if ($photo){
		$album = dbGetFields('cms_photo_albums', 'id='.$photo['album_id'], '*');	
		if ($album['NSDiffer'] == 'club'){
			$club = dbGetFields('cms_clubs', 'id='.$album['user_id'], '*');
			$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
			$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
		} else {
			$is_admin = $inCore->userIsAdmin($inUser->id);
		}		
		$is_author = ($inUser->id == $photo['user_id']);

		if ($is_admin || $is_author){
			
			if (!isset($_POST['gomove'])){ //SHOW MOVE FORM
						
				$inPage->setTitle($_LANG['MOVE_PHOTO']);
				$inPage->addPathway($_LANG['MOVE_PHOTO'], $_SERVER['REQUEST_URI']);

				echo '<div class="con_heading">'.$_LANG['MOVE_PHOTO'].'</div>';
					
				echo '<div style="margin-top:10px; margin-bottom:15px;"><strong>'.$_LANG['PHOTO'].':</strong> <a href="/photos/'.$menuid.'/photo'.$photo['id'].'.html">'.$photo['title'].'</a></div>';
				
				echo '<div><form action="" method="POST">';
				
				echo '<table border="0" cellspacing="10" style="background-color:#EBEBEB"><tr><td>'.$_LANG['MOVE_INTO_ALBUM'].':</td>';
				
					echo '<td><select name="album_id">';
	
						if ($album['NSDiffer'] == '') { 
							$fsql = "SELECT * FROM cms_photo_albums WHERE NSDiffer='' ORDER BY title";
						} elseif ($album['NSDiffer'] == 'club') {
							$fsql = "SELECT * FROM cms_photo_albums WHERE NSDiffer='club' AND parent_id>0 AND user_id = ".$club['id']." ORDER BY title";
						}
						$fresult = $inDB->query($fsql) ;
						if ($inDB->num_rows($fresult)){
							while ($f = $inDB->fetch_assoc($fresult)){
								echo '<option value="'.$f['id'].'" ';
								if ($photo['album_id'] == $f['id']) { echo 'selected'; }
								echo '>--- '.$f['title'].'</option>';
							}
						}
					
					echo '</select></td>';
	
				echo '<td><input type="submit" name="gomove" value="'.$_LANG['MOVING'].'"/></td></tr></table>';
				echo '</form></div>';								

			} else { //DO MOVE
			
				if (@$_POST['album_id']){				
					$fid = intval($_POST['album_id']);
					if ($is_admin){		
						$inDB->query("UPDATE cms_photo_files SET album_id = $fid WHERE id = $id") ;
					}									
				}
				header('location:/photos/'.$menuid.'/'.$fid);
			}
			
		} else { usrAccessDenied(); }
	} else { usrAccessDenied(); }
	
}
/////////////////////////////// PHOTO DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delphoto'){
	$max_mb = 2; //max filesize in Mb
	
	$inCore->includeFile('components/users/includes/usercore.php');
	
	$photo_id = @intval($_REQUEST['id']);
	
	$photo = dbGetFields('cms_photo_files', 'id='.$photo_id, '*');
	if (!$photo) { $inCore->redirect('/photos/'.$menuid); }	
	$album = dbGetFields('cms_photo_albums', 'id='.$photo['album_id'], '*');
	
	if ($album['NSDiffer'] == 'club'){
		$club = dbGetFields('cms_clubs', 'id='.$album['user_id'], '*');
		$inPage->addPathway($club['title'], '/clubs/'.$menuid.'/'.$club['id']);
		$is_admin = $inCore->userIsAdmin($inUser->id) || clubUserIsAdmin($club['id'], $inUser->id) || clubUserIsRole($club['id'], $inUser->id, 'moderator');	
	} else {
		$is_admin = $inCore->userIsAdmin($inUser->id);
	}
	
	$is_author = ($inUser->id == $photo['user_id']);
	
	if ($inUser->id && ($is_admin||$is_author)){
		$uid = $inUser->id;
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = $uid";
			$result = $inDB->query($sql) 	;
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				$sql = "SELECT * FROM cms_photo_files WHERE id = $photo_id";
				$result = $inDB->query($sql) ;
				if ($inDB->num_rows($result)){
					$photo = $inDB->fetch_assoc($result);				
					$inPage->addPathway($_LANG['DELETE_PHOTOGALLERY'], $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">'.$_LANG['DELETING_PHOTO'].'</div>';
					echo '<p>'.$_LANG['YOU_REALLY_DELETE_PHOTO'].' "'.$photo['title'].'"?</p>';
					echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
							<input style="font-size:24px; width:100px" type="button" name="cancel" value="'.$_LANG['NO'].'" onclick="window.history.go(-1)" />
							<input style="font-size:24px; width:100px" type="submit" name="godelete" value="'.$_LANG['YES'].'" />
						 </p></form>';
				} else { usrAccessDenied(); }
			}
		} else {
            $model->deletePhoto($photo_id);
			header('location:/photos/'.$menuid.'/'.$photo['album_id']);
		}
	} else { usrAccessDenied(); }
}
/////////////////////////////// VIEW LATEST PHOTOS ///////////////////////////////////////////////////////////////////////////////////
if ($do=='latest'){
	$col = 1; $maxcols = 4;

	$sql = "SELECT f.*, IF(DATE_FORMAT(f.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(f.pubdate, '<strong>{$_LANG['TODAY']}</strong>, %H:%i'), DATE_FORMAT(f.pubdate, '%d-%m-%Y'))  as fpubdate, a.id as album_id, a.title as album
			FROM cms_photo_files f, cms_photo_albums a
			WHERE f.published = 1 AND f.album_id = a.id
			ORDER BY pubdate DESC
			LIMIT 24";		

	$result = $inDB->query($sql) ;

	$inPage->addPathway($_LANG['NEW_PHOTO_IN_GALLERY'], $_SERVER['REQUEST_URI']);
	echo '<div class="con_heading">'.$_LANG['NEW_PHOTO_IN_GALLERY'].'</div>';
		
	if ($inDB->num_rows($result)){	
		echo '<table cellspacing="2" border="0" width="100%">';
		while($con = $inDB->fetch_assoc($result)){
			if ($col==1) { echo '<tr>'; } 
			echo '<td align="center" valign="middle" class="mod_lp_photo" width="'.round(100/$maxcols, 0).'%">';
			echo '<table width="100%" height="100" cellspacing="0" cellpadding="0">';
			echo '<tr><td align="center"><div class="mod_lp_titlelink"><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].'">'.$con['title'].'</a></div></td></tr>';
			echo '<tr>
				  <td valign="middle" align="center">';
					echo '<a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].'">';
						echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].'" border="0" />';
					echo '</a>';
			echo '</td></tr>';
				echo '<tr>';
				echo '<td align="center">';
					echo '<div class="mod_lp_albumlink"><a href="/photos/'.$menuid.'/'.$con['album_id'].'" title="'.$con['album'].'">'.$con['album'].'</a></div>';
					echo '<div class="mod_lp_details">';
					echo '<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>';
						echo '<td><img src="/images/icons/date.gif" border="0"/></td>';
						echo '<td>'.$con['fpubdate'].'</td>';
						echo '<td><img src="/images/icons/comments.gif" border="0"/></td>';
						echo '<td><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html#c">'.$inCore->getCommentsCount('photo', $con['id']).'</td>';
					echo '</tr></table>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';				
			echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
		}			
		if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
		echo '</table>';
	} else { echo '<p>'.$_LANG['NO_MATERIALS_TO_SHOW'].'</p>'; }
}
/////////////////////////////// VIEW BEST PHOTOS ///////////////////////////////////////////////////////////////////////////////////
if ($do=='best'){
	$col = 1; $maxcols = 4;

	$sql = "SELECT f.*, f.id as fid,
				   IF(DATE_FORMAT(f.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(f.pubdate, '<strong>{$_LANG['TODAY']}</strong>, %H:%i'), DATE_FORMAT(f.pubdate, '%d-%m-%Y'))  as fpubdate,
				   a.id as album_id, a.title as album, 
				   IFNULL(SUM(r.points), 0) as rating
			FROM cms_photo_files f
			LEFT JOIN cms_ratings r ON r.item_id=f.id AND r.target = 'photo'		
			LEFT JOIN cms_photo_albums a ON f.album_id = a.id
			WHERE f.published = 1
			GROUP BY f.id
			ORDER BY rating DESC 
			LIMIT 24";

	$result = $inDB->query($sql) ;
		
	$inPage->addPathway($_LANG['BEST_PHOTOS'], $_SERVER['REQUEST_URI']);
	echo '<div class="con_heading">'.$_LANG['BEST_PHOTOS'].'</div>';

	if ($inDB->num_rows($result)){	
		$num = 1;
		echo '<table cellspacing="2" border="0" width="100%">';
		while($con = $inDB->fetch_assoc($result)){
			if ($col==1) { echo '<tr>'; } echo '<td align="center" valign="middle" class="mod_lp_photo" width="'.round(100/$maxcols, 0).'%">';
			echo '<table width="100%" height="100" cellspacing="0" cellpadding="0">';
			  echo '<tr><td align="center"><div class="mod_lp_titlelink">'.$num.'. <a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].' ('.$con['rating'].')">'.$con['title'].'</a></div></td></tr>';
			  echo '<tr>';
			  echo '<td valign="middle" align="center">';
			echo '<a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].'">';
				echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].' ('.$con['rating'].')" border="0" />';
			echo '</a>';
			echo '</td></tr>';
				echo '<tr>';
				echo '<td align="center">';
						echo '<div class="mod_lp_albumlink"><a href="/photos/'.$menuid.'/'.$con['album_id'].'" title="'.$con['album'].'">'.$con['album'].'</a></div>';
						echo '<div class="mod_lp_details">';
						echo '<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>';
								$inCore->loadLib('karma');
								echo '<td style="font-weight:bold">'.cmsKarmaFormat($con['rating']).'</td>';
								echo '<td><img src="/images/icons/comments.gif" border="0"/></td>';
								echo '<td><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html#c">'.$inCore->getCommentsCount('photo', $con['id']).'</td>';
						echo '</tr></table>';
						echo '</div>';
				echo '</td>';
				echo '</tr>';
			echo '</table>';
			echo '</div>';
			echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
			$num++;
		}			
		if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
		echo '</table>';
	} else { echo '<p>'.$_LANG['NO_MATERIALS_TO_SHOW'].'</p>'; }
}
/////////////////////////////// /////////////////////////////// /////////////////////////////// /////////////////////////////// //////
} //function
?>