<?php
/*********************************************************************************************/
//																							 //
//						   InstantCMS v1.0 (c) 2008 COMMERCIAL VERSION                       //
//   						Source code protected by copyright laws                          //
//                                                                                           //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function usrBlog($user_id){
	return dbGetFields('cms_blogs', 'user_id = '.$user_id, 'id, seolink');
}

function usrLink($title, $user_login, $menuid){
    return '<a href="'.cmsUser::getProfileURL($user_login).'" title="'.strip_tags($title).'">'.$title.'</a>';
}

function usrFilesSize($user_id){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT SUM(filesize) as totalsize FROM cms_user_files WHERE user_id = $user_id GROUP BY user_id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$data = $inDB->fetch_assoc($result);
		$size = $data['totalsize'];
	} else {
		$size = 0;
	}
	
	return $size;

}

function usrMenu($user_id, $cfg, $is_banned=false){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	global $menuid;
	global $_LANG;
	
	if ($inUser->id) { $my_profile = ($inUser->id == $user_id ); } else { $my_profile = false; }
	
	//MENU FOR ALL
	$html = '<div class="usr_profile_menu"><table cellpadding="0" cellspacing="1" align="center" style="margin-left:auto;margin-right:auto"><tr>';
	if (!$is_banned){				
		if(!$my_profile){
			$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/sendmessage.html" title="'.$_LANG['WRITE_MESS'].'"><img src="/components/users/images/profilemenu/message.gif" border="0"/></a></td>';
		}

		if ($inUser->id && !$my_profile && $cfg['sw_friends']){
			if (!usrIsFriends($user_id, $inUser->id)){
				if (!usrIsFriends($user_id, $inUser->id, false)){				
					$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/friendship.html" title="'.$_LANG['ADD_TO_FRIEND'].'"><img src="/components/users/images/profilemenu/friends.gif" border="0"/></a></td>';
				} else {
					$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/nofriends.html" title="'.$_LANG['STOP_FRIENDLY'].'"><img src="/components/users/images/profilemenu/nofriends.gif" border="0"/></a></td>';
				}	
			} else {
				$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/nofriends.html" title="'.$_LANG['STOP_FRIENDLY'].'"><img src="/components/users/images/profilemenu/nofriends.gif" border="0"/></a></td>';
			}
		}
		
	}	
		
		if ($inUser->id && $inUser->id!=$user_id){
			if ($inUser->is_admin && ($inUser->id==1 || $inCore->isAdminCan('admin/users'))){
                if (!$is_banned){
                    $html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/giveaward.html" title="'.$_LANG['TO_AWARD'].'"><img src="/components/users/images/profilemenu/award.gif" border="0"/></a></td>';
                    $html .= '<td><a href="/admin/index.php?view=userbanlist&do=add&to='.$user_id.'" title="'.$_LANG['TO_BANN'].'"><img src="/components/users/images/profilemenu/ban.gif" border="0"/></a></td>';
                }
                $html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/delprofile.html" title="'.$_LANG['DEL_PROFILE'].'"><img src="/components/users/images/profilemenu/delprofile.gif" border="0"/></a></td>';
			}
		}

	//PERSONAL MENU
	if($my_profile){
			if ($cfg['sw_msg']){
				$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/messages.html" title="'.$_LANG['MY_MESS'].'"><img src="/components/users/images/profilemenu/message.gif" border="0"/></a></td>';
			}
			
			$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/editprofile.html" title="'.$_LANG['CONFIG_PROFILE'].'"><img src="/components/users/images/profilemenu/edit.gif" border="0"/></a></td>';
			$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/avatar.html" title="'.$_LANG['SET_AVATAR'].'"><img src="/components/users/images/profilemenu/avatar.gif" border="0"/></a></td>';
			
			if ((usrPhotoCount($user_id)<$cfg['photosize'] || $cfg['photosize']==0) && $cfg['sw_photo']){			
				$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/addphoto.html" title="'.$_LANG['ADD_PHOTO'].'"><img src="/components/users/images/profilemenu/addphoto.gif" border="0"/></a></td>';
			}
						
	}

	$html .= '<td><a href="/users/'.$menuid.'/'.$user_id.'/karma.html" title="'.$_LANG['KARMA_HISTORY'].'"><img src="/components/users/images/profilemenu/karma.gif" border="0"/></a></td>';
	
	$html .= '</tr></table></div>';
	
	return $html;
}

function usrPhotoCount($user_id, $with_public=true){
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $we_friends = $inDB->rows_count('cms_user_friends', "(to_id={$user_id} AND from_id={$inUser->id}) OR (to_id={$inUser->id} AND from_id={$user_id})", 1);

    $my_profile = ($inUser->id == $user_id);

    $filter = '';

    if (!$my_profile){ $filter = "AND ( allow_who='all' OR (allow_who='registered' AND ({$inUser->id}>0)) OR (allow_who='friends' AND ({$we_friends}=1)) )"; }

    $private_count  = $inDB->rows_count('cms_user_photos', "user_id={$user_id} $filter");
    $public_count   = $inDB->rows_count('cms_photo_files', "user_id={$user_id}");

    $total_count    = $with_public ? ($private_count + $public_count) : $private_count;

    return $total_count;
}

function usrPublicAlbums($user_id){
    $inDB = cmsDatabase::getInstance();
    global $menuid;
    global $_LANG;
	$html = '';
	$sql = "SELECT f.*, a.id as id, a.title as album, COUNT(f.id) as photos
			FROM cms_photo_files f, cms_photo_albums a
			WHERE f.user_id = $user_id AND f.album_id = a.id
			GROUP BY f.album_id
			ORDER BY f.pubdate DESC
			";
	$rs = $inDB->query($sql) or die('Photo counting error: '.mysql_error());
	$albums = $inDB->num_rows($rs);
	$current = 1;
	if ($albums){
		$html .= '<div class="usr_publ_albums"><div style="font-weight:bold;margin-bottom:2px">'.$_LANG['USERS_PHOTOS_PUBLIC_ALBUMS'].':</div><div style="padding:4px;">';
		while ($album = $inDB->fetch_assoc($rs)){
			$html .= '<table border="0" cellpadding="2" cellspacing="0" style="float:left;margin-right:3px;"><tr>';
			$html .= '<td><img src="/images/markers/photoalbum.png" border="0"/></td>';
			$html .= '<td><a href="/photos/0/'.$album['id'].'/byuser'.$user_id.'.html">'.$album['album'].'</a> ('.$album['photos'].')</td>';
			$html .= '</tr></table>';
			
		}
		$html .= '</div></div>';
	}
	
	return $html;

}

function usrPhotos($user_id, $limit=4, $preview=true, $limitfrom=0, $limitmany=0){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$html = '';
	global $menuid;
	global $_LANG;
	if (isset($inUser->id)){
		$myprofile = ($inUser->id == $user_id || $inCore->userIsAdmin($inUser->id));
	} else { $myprofile = false; }
	
	$sql = "SELECT *, DATE_FORMAT(pubdate, '%d-%m-%Y') as fpubdate
			FROM cms_user_photos
			WHERE user_id = $user_id
			ORDER BY pubdate DESC
			";
    
	if ($preview) { $sql .= " LIMIT $limit"; } else {
		if ($limitmany>0) { $sql .= " LIMIT $limitfrom, $limitmany"; }
	}
	$result = $inDB->query($sql) ;
	
	if (!$preview) { 
		$html .= usrPublicAlbums($user_id)."\n";
	}

	if ($inDB->num_rows($result)){
		$html .= '<div style="width:100%; display: block">';
		while($photo = $inDB->fetch_assoc($result)){
			if (usrAllowed($photo['allow_who'], $photo['user_id'])){
				$html .= '<div class="usr_photo_thumb">';
					$html .= '<a class="usr_photo_link" href="/users/'.$menuid.'/'.$user_id.'/photo'.$photo['id'].'.html" title="'.$photo['title'].'">';
					$html .= '<img border="0" src="/images/users/photos/small/'.$photo['imageurl'].'" alt="'.$photo['title'].'"/>';
				$html .= '</a>';
				$html .= '<div style="padding:4px;"><span style="font-size:10px; display:block"><strong>'.$_LANG['DATE'].':</strong> '.$photo['fpubdate'].'</span>';
				$html .= '<span style="font-size:10px; display:block"><strong>'.$_LANG['HITS'].':</strong> '.$photo['hits'].'</span>';
				$html .= '</div>';
				
				$html .= '<div></div>';
				$html .= '</div>';
			}
		}	
		$html .= '</div>';
		if ($preview){
			$html .= '<div align="right" style="margin-top:10px"><a href="/users/'.$menuid.'/'.$user_id.'/photoalbum.html">'.$_LANG['ALL_PHOTOS'].'</a> &rarr;</div>';
		}
	} else { 
		$html .= '<p>';
		$html .= $_LANG['NOT_PHOTOS'];
		if ($user_id==$inUser->id) { $html .= ' <a href="/users/'.$menuid.'/'.$user_id.'/addphoto.html">'.$_LANG['ADD'].'?</a>'; };
		$html .= '</p>'; 
	}
	
	return $html;
}

function usrMsg($user_id, $table){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT id
			FROM $table
			WHERE user_id = $user_id
			";
	$result = $inDB->query($sql) ;
	
	return $inDB->num_rows($result);
}

function usrComments($user_id, $limit=5, $preview=true){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	global $menuid;
        global $_LANG;
	$html = '';
	$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate,  IFNULL(SUM(v.vote), 0) as votes
			FROM cms_comments c
            LEFT JOIN cms_comments_votes v ON v.comment_id = c.id AND v.comment_type = 'com'
			WHERE c.user_id = $user_id AND c.published = 1
            GROUP BY c.id
			ORDER BY c.pubdate DESC            
			";
	if ($preview) { $sql .= " LIMIT $limit"; }
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){

		while ($com = $inDB->fetch_assoc($result)){
			$html .= '<table style="width:100%; margin-bottom:2px;" cellspacing="0">';
			$html .= '<tr>';
				$html .= '<td class="usr_com_title">
                            <div style="float:left">'.$inCore->getCommentLink($com['target'], $com['target_id']).' &mdash; '.$com['fpubdate'].'</div>
                            <div style="float:right">aa '.$com['votes'].'</div>
                          </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td class="usr_com_body">'.$inCore->parseSmiles($com['content'], true).'</td>';
			$html .= '</tr>';
			$html .= '</table>';
		}
		if ($preview){
			$html .= '<div align="right"><a href="/users/'.$menuid.'/'.$user_id.'/entries.html">'.$_LANG['ALL_COMMENTS'].'</a> &rarr;</div>';
		}
	} else {
		$html .= $_LANG['USER_NOT_COMMENT'];
	}
	
	return $html;
}

function usrAwards($user_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	global $menuid;
        global $_LANG;
	$html = '';
	$sql = "SELECT *, DATE_FORMAT(pubdate, '%d-%m-%Y') as fpubdate
			FROM cms_user_awards
			WHERE user_id = $user_id
			ORDER BY pubdate DESC
			";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){

		while ($aw = $inDB->fetch_assoc($result)){
			$html .= '<div class="usr_award_block">';
			$html .= '<table style="width:100%; margin-bottom:2px;" cellspacing="2">';
			$html .= '<tr>';
				$html .= '<td class="usr_com_title"><strong>'.$aw['title'].'</strong> ';
				if ($aw['award_id']>0){
					$html .= '<td width="20" class="usr_awlist_link"><a href="/users/awardslist.html">?</a></td>';
				} else {
					if (isset($inUser->id)){
						if ($inUser->id==$user_id || $inCore->userIsAdmin($inUser->id)){
							$html .= '[<a href="/users/'.$menuid.'/delaward'.$aw['id'].'.html">'.$_LANG['DELETE'].'</a>]';
						}
					}
					$html .= '</td>';
				}
			$html .= '</tr>';
			$html .= '<tr>';
				if ($aw['award_id']>0){
					$html .= '<td class="usr_com_body" colspan="2">';
				} else {
					$html .= '<td class="usr_com_body">';
				}
					$html .= '<table border="0" cellpadding="5" cellspacing="0"><tr>';
						$html .= '<td valign="top"><img src="/images/users/awards/'.$aw['imageurl'].'" border="0" alt="'.$aw['title'].'"/></td>';											
						$html .= '<td valign="top">'.$inCore->parseSmiles($aw['description']).'<div class="usr_award_date">'.$aw['fpubdate'].'</div></td>';
					$html .= '</tr></table>';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
			$html .= '</div>';
		}
	}
	return $html;
}

function usrForumPosts($user_id, $limit=5, $preview=true){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	global $menuid;
        global $_LANG;
	$html = '';
	$sql = "SELECT *, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, t.title as topic, p.id as pid
			FROM cms_forum_posts p, cms_forum_threads t
			WHERE p.user_id = $user_id AND p.thread_id = t.id
			ORDER BY p.pubdate DESC
			";
	if ($preview) { $sql .= " LIMIT $limit"; }
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){

		while ($com = $inDB->fetch_assoc($result)){
			$html .= '<table style="width:100%; margin-bottom:2px;" cellspacing="0">';
			$html .= '<tr>';
				$html .= '<td class="usr_com_title">
								<a href="/forum/'.$menuid.'/thread'.$com['thread_id'].'.html#'.$com['pid'].'">'.$com['topic'].'</a> &mdash; '.$com['fpubdate'].'
						  </td>';
			$html .= '</tr>';		
			$html .= '<tr>';
				$html .= '<td class="usr_com_body">
								'.$inCore->parseSmiles(substr(strip_tags($com['content']), 0, 100)).'...
						  </td>';
			$html .= '</tr>';						
			$html .= '</table>';
		}
	} else {
		$html .= $_LANG['NOT_POSTS_ON_FORUM'];
	}
	
	return $html;
}

function usrImage($user_id, $small='small'){
    $inDB = cmsDatabase::getInstance();
	if ($user_id == -1) {	return '<img border="0" class="usr_img" src="/images/messages/update.jpg" />';	}
	if ($user_id == -2) {	return '<img border="0" class="usr_img" src="/images/messages/massmail.jpg" />'; }

	$sql = "SELECT p.imageurl, u.is_deleted as is_deleted
			FROM cms_user_profiles p, cms_users u
			WHERE p.user_id = $user_id AND p.user_id = u.id
			LIMIT 1
			";
	$result = $inDB->query($sql) ;
	if ($inDB->num_rows($result)>0){
		$usr = $inDB->fetch_assoc($result);
		if($usr['is_deleted']){
			if ($small=='small'){
				return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/noprofile.jpg" />';
			} else {
				return '<img border="0" class="usr_img" src="/images/users/avatars/noprofile.jpg" />';
			}	
		} else {
			if ($usr['imageurl'] && @file_exists($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/'.$usr['imageurl'])){
				if ($small=='small'){
					return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/'.$usr['imageurl'].'" />';
				} else {
					return '<img border="0" class="usr_img" src="/images/users/avatars/'.$usr['imageurl'].'" />';
				}
			} else {
				if ($small=='small'){ return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/nopic.jpg" />';
				} else { return '<img border="0" class="usr_img" src="/images/users/avatars/nopic.jpg" />'; }
			}
		}
	} else {
			if ($small=='small'){ return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/nopic.jpg" />';
			} else { return '<img border="0" class="usr_img" src="/images/users/avatars/nopic.jpg" />'; }
	}
}

function usrImageNOdb($user_id, $small='small', $usr_imageurl, $usr_is_deleted){
	if ($user_id == -1) {	return '<img border="0" class="usr_img_small" src="/images/messages/update.jpg" />';	}
	if ($user_id == -2) {	return '<img border="0" class="usr_img_small" src="/images/messages/massmail.jpg" />'; }

	if ($usr_imageurl){
		if($usr_is_deleted){
			if ($small=='small'){
				return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/noprofile.jpg" />';
			} else {
				return '<img border="0" class="usr_img" src="/images/users/avatars/noprofile.jpg" />';
			}	
		} else {
			if ($usr_imageurl && @file_exists($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/'.$usr_imageurl)){
				if ($small=='small'){
					return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/'.$usr_imageurl.'" />';
				} else {
					return '<img border="0" class="usr_img" src="/images/users/avatars/'.$usr_imageurl.'" />';
				}
			} else {
				if ($small=='small'){ return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/nopic.jpg" />';
				} else { return '<img border="0" class="usr_img" src="/images/users/avatars/nopic.jpg" />'; }
			}
		}
	} else {
			if ($small=='small'){ return '<img border="0" class="usr_img_small" src="/images/users/avatars/small/nopic.jpg" />';
			} else { return '<img border="0" class="usr_img" src="/images/users/avatars/nopic.jpg" />'; }
	}
}

function usrCanKarma($to, $from){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	if($to == $from) { return false; }

	$cfg = $inCore->loadComponentConfig('users');
	if (!isset($cfg['karmatime'])) { $cfg['karmatime'] = 3; }
	if (!isset($cfg['karmaint']))  { $cfg['karmaint'] = 'HOUR'; }

	$sql = "SELECT id FROM cms_user_karma WHERE user_id = $to AND sender_id = $from AND senddate >= DATE_SUB(NOW(), INTERVAL ".$cfg['karmatime']." ".$cfg['karmaint'].")";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)==0) { return true; } else { return false; }

}

function usrStatus($user_id, $logdate='', $online=false, $gender='m'){

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
    if ($online===false){
        $sql = "SELECT *
                FROM cms_online
                WHERE user_id = '$user_id'";
        $result     = $inDB->query($sql);
        $is_online  = $inDB->num_rows($result);
    } else {
        $is_online  = $online;
    }
	
	if ($is_online){
		return '<span class="online">'.$_LANG['ONLINE'].'</span>';
	} else {
				if ($logdate){
                    if (!strstr(strtolower($logdate), $_LANG['YESTERDAY']) && !strstr(strtolower($logdate), $_LANG['TODAY'])){
                        $logdate = cmsCore::dateDiffNow($logdate);                        
                        if (!strstr($logdate, 'не известно')) { $logdate .=  ' '.$_LANG['BACK']; }
                    }
					return '<span class="offline">'.$logdate.'</span>';
				} else {
					return '<span class="offline">'.$_LANG['OFFLINE'].'</span>';
				}
			}
}

function usrStatusList($user_id, $logdate='', $online=false, $gender='m'){

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
    if ($online===false){
        $sql = "SELECT *
                FROM cms_online
                WHERE user_id = '$user_id'";
        $result     = $inDB->query($sql);
        $is_online  = $inDB->num_rows($result);
    } else {
        $is_online  = $online;
    }
	
	if ($is_online){
		return '<span class="online">'.$_LANG['ONLINE'].'</span>';
	} else {
				if ($logdate){
					return '<span class="offline">'.$logdate.'</span>';
				} else {
					return '<span class="offline">'.$_LANG['OFFLINE'].'</span>';
				}
			}
}

function usrCheckAuth(){
    $inUser = cmsUser::getInstance();
	return (bool)$inUser->id;
}

function usrNeedReg(){
    global $_LANG;
	echo '<h1 class="con_heading">'.$_LANG['ACCESS_DENIED'].'.</h1>';
	echo '<p>'.$_LANG['ACCESS_ONLY_REGISTERED'].'</p>';
	
	return;
}

function usrFriendOnly(){
    global $_LANG;
	echo '<h1 class="con_heading">'.$_LANG['ACCESS_DENIED'].'.</h1>';
	echo '<p>'.$_LANG['ACCESS_ONLY_FRIENDS'].'</p>';
	
	return;
}

function usrAccessDenied(){
    global $_LANG;
	echo '<h1 class="con_heading">'.$_LANG['ACCESS_DENIED'].'.</h1>';
	echo '<p>'.$_LANG['ACCESS_NEED_AVTOR'].'</p>';
	
	return;
}

function usrNotAllowed(){
    global $_LANG;
	echo '<h1 class="con_heading">'.$_LANG['ACCESS_BLOCK'].'.</h1>';
	echo '<p>'.$_LANG['ACCESS_SECURITY'].'</p>';
	
	return;
}

function usrIsFriends($first_id, $second_id, $strict=true){
    $inDB = cmsDatabase::getInstance();
	if ($strict) { $is_accepted = 'is_accepted = 1'; } else { $is_accepted = '(is_accepted = 0 OR is_accepted = 1)'; }
	$sql = "SELECT * FROM cms_user_friends WHERE ((to_id = $first_id AND from_id = $second_id) OR (to_id = $second_id AND from_id = $first_id)) AND $is_accepted";
	$result = $inDB->query($sql);
	if ($inDB->num_rows($result)){
		return true;
	} else { return false; }
}

function usrFriendQueriesNum($user_id, $from_id=''){

}

function usrFriendQueriesList($user_id, $model){
   
	global $menuid;

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

    $query_list = array();

    $query_list = $model->getNewFriends($user_id);

    if (!$query_list){ return; }

    foreach($query_list as $id=>$query){
        $query_list[$id]['sender_img'] = ($query['sender_img']) ? '/images/users/avatars/small/'.$query['sender_img'] : '/images/users/avatars/small/nopic.jpg';
    }

    ob_start();

    $smarty = $inCore->initSmarty('components', 'com_users_newfriends.tpl');

    $smarty->assign('menuid', $menuid);
    $smarty->assign('friends', $query_list);

    $smarty->display('com_users_newfriends.tpl');

	return ob_get_clean();
}

function usrFriends($user_id, $short=true){
    $inDB = cmsDatabase::getInstance();
	global $menuid;
        global $_LANG;
	$sql = "SELECT *
			FROM cms_user_friends f
			WHERE ((f.to_id = $user_id AND f.from_id <> $user_id) OR (f.to_id <> $user_id AND f.from_id = $user_id)) AND f.is_accepted = 1
			";

    if ($short) { $sql .= "LIMIT 9"; }
			
	if ($short) { $maxcols = 3; } else { $maxcols = 5; }

	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){

		$friends    = array();
		$friends_id = array();
		$not_all    = false;

		while ($friend = $inDB->fetch_assoc($result)){
			if ($friend['from_id']==$user_id) { 
                $friends_id[] = $friend['to_id'];
            } else {
                $friends_id[] = $friend['from_id'];
            }
            if ($short && sizeof($friends_id)>=8){
                $not_all = true;
                break;
            }
		}

        $id_sql = ''; $id_count = 0; $id_total = sizeof($friends_id);

        foreach($friends_id as $key=>$friend_id){
            $id_count   += 1;
            $id_sql     .= "u.id = {$friend_id}";
            if ($id_count < $id_total){ $id_sql .= ' OR '; }
        }

        $sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.imageurl as avatar,
                       IF(DATE_FORMAT(logdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(logdate, '{$_LANG['TODAY']} {$_LANG['IN']} %H:%i'),
                       IF(DATEDIFF(NOW(), logdate)=1, DATE_FORMAT(logdate, '{$_LANG['YESTERDAY']} {$_LANG['IN']} %H:%i'),DATE_FORMAT(logdate, '%d-%m-%Y') ))  as flogdate,
                       IFNULL(COUNT(o.id), 0) as online
                FROM cms_users u, cms_user_profiles p
                LEFT JOIN cms_online o ON p.user_id = o.user_id
                WHERE p.user_id = u.id AND ({$id_sql})
                GROUP BY p.user_id";

        $res = $inDB->query($sql);
        
        if ($inDB->num_rows($res)){
            while($usr = $inDB->fetch_assoc($res)){
                $friends[$usr['id']]    = $usr;
            }
        }
		
		if (sizeof($friends)){	
			$col = 1; $html = '';
			$html .= '<table width="" cellpadding="10" cellspacing="0" border="0" class="usr_friends_list" align="left">';
				foreach($friends as $friend){
                    
					$id         = $friend['id'];
					$nickname   = $friend['nickname'];
					$logdate    = $friend['flogdate'];
                    $login      = $friend['login'];					
					$avatar     = $friend['avatar'];
                    $online     = (int)$friend['online'];
					
					if (!$avatar || !file_exists(PATH.'/images/users/avatars/small/'.$avatar)) { $avatar = 'nopic.jpg'; }
				
					if ($col==1) { $html .= '<tr>'; } $html .= '<td align="center" valign="top">';
					$html .= '<table width="" cellpadding="1" cellspacing="0" border="0" align="center" class="usr_friends_entry">';
						$html .= '<tr><td align="center"><div><a href="'.cmsUser::getProfileURL($login).'">'.$nickname.'</a></div></td></tr>';
						$html .= '<tr><td align="center"><a href="'.cmsUser::getProfileURL($login).'"><img src="/images/users/avatars/small/'.$avatar.'" border="0" /></a></td></tr>';
						$html .= '<tr>
									<td align="center">										
										'.usrStatus($id, $logdate, $online).'
									</td>
								  </tr>';						
					$html .= '</table>';		
					$html .= '</td>'; if ($col==$maxcols) { $html .= '</tr>'; $col=1; } else { $col++; }
                    
				}
			if ($col>1) { $html .=  '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }			
			$html .= '</table>';
			if ($not_all && $short){
				$html .= '<div style="text-align:right"><a href="/users/'.$menuid.'/'.$user_id.'/friendlist.html" class="usr_friendslink">'.$_LANG['ALL_FRIENDS'].'</a> &rarr;</div>';
			}
		}
	} else { $html = ''; }
	return $html;
}

function usrAllowed($allow_who, $owner_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	$user_id = $inUser->id;
	if ($owner_id == $user_id) { return true; }
	if ($allow_who == 'all') { return true; }
	if ($allow_who == 'registered') { return usrCheckAuth(); }
	if ($allow_who == 'friends') { 	
		if (usrCheckAuth()){ 
			return (usrIsFriends($user_id, $owner_id) || $inCore->userIsAdmin($user_id)); 
		}
	}
}

function usrAwardsList($selected = 'aw.gif'){
    $inDB = cmsDatabase::getInstance();
	$html = '';
	if ($handle = opendir(PATH.'/images/users/awards')) {
		while (false !== ($file = readdir($handle))) {
			$html .= '<div style="float:left;margin:4px">';
			$html .= '<table border="0" cellspacing="0" cellpadding="4"><tr>';
			if ($file != '.' && $file != '..' && strstr($file, '.gif')){
				 $tag = str_replace('.gif', '', $file);
				 $dir = '/images/users/awards/';			 
				 if ($selected != $file){
				 	$html .= '<td align="center" valign="middle"><img src="'.$dir.$file.'" alt="'.$file.'"/><br/><input type="radio" name="imageurl" value="'.$file.'"/></td>';
  				 } else {
					$html .= '<td align="center" valign="middle"><img src="'.$dir.$file.'" alt="'.$file.'"/><br/><input type="radio" name="imageurl" value="'.$file.'" checked="checked"/></td>';					
				 }
			}
			$html .= '</tr></table></div>';
		}	
		closedir($handle);
	}
	return $html;
}

function usrGenderStats($total_usr){
    $inDB = cmsDatabase::getInstance();
	$stat = array();
	//male
	$sql = "SELECT u.id
			FROM cms_users u, cms_user_profiles p
			WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id AND p.gender = 'm'";
	$rs = $inDB->query($sql); $stat['male'] = $inDB->num_rows($rs);
	//female
	$sql = "SELECT u.id 
			FROM cms_users u, cms_user_profiles p
			WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id AND p.gender = 'f'";
	$rs = $inDB->query($sql); $stat['female'] = $inDB->num_rows($rs);
	//unknown
	$stat['unknown'] = $total_usr - $stat['male'] - $stat['female'];
	return $stat;
}

function usrCityStats(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$menuid = $inCore->menuId();
	$stat = array();
	$empty = $_LANG['NOT_DECIDE'];

	$sql = "SELECT IF (p.city != '', p.city, '$empty') city, COUNT( p.user_id ) count
			FROM cms_user_profiles p, cms_users u
			WHERE p.user_id = u.id AND u.is_locked =0 AND u.is_deleted =0
			GROUP BY p.city";
	$rs = $inDB->query($sql); 
	if ($inDB->num_rows($rs)){ 
		while($row = $inDB->fetch_assoc($rs)){
			if ($row['city'] != $empty) { $row['href'] = '/users/'.$menuid.'/city/'.urlencode($row['city']); } else { $row['href'] = ''; }
			$row['city'] = ucfirst(strtolower($row['city']));
			$stat[] = $row;
		}
	}
	return $stat;
}

?>
