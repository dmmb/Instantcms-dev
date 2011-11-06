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

function usrBlog($user_id){
	return dbGetFields('cms_blogs', 'owner="user" AND user_id = '.$user_id, 'id, seolink', 'owner');
}

function usrLink($title, $user_login){
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

function usrPhotoCount($user_id, $with_public=true){
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $my_profile = ($inUser->id == $user_id);

    $filter = '';

    if (!$my_profile){ 
		$we_friends = (int)usrIsFriends($user_id, $inUser->id);
		$filter = "AND ( allow_who='all' OR (allow_who='registered' AND ({$inUser->id}>0)) OR (allow_who='friends' AND ({$we_friends}=1)) )";
	}

    $private_count  = $inDB->rows_count('cms_user_photos', "user_id={$user_id} $filter");
    $public_count   = $inDB->rows_count('cms_photo_files', "user_id={$user_id} AND published = 1");

    $total_count    = $with_public ? ($private_count + $public_count) : $private_count;

    return $total_count;
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

function usrAwards($user_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    global $_LANG;
	$html = '';
	$sql = "SELECT *
			FROM cms_user_awards
			WHERE user_id = $user_id
			ORDER BY pubdate DESC
			";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){
		$is_admin = false;
		if ($inUser->id && ($inUser->id==$user_id || $inCore->userIsAdmin($inUser->id))){
			$is_admin = true;
		}
		$aws = array();
		while ($aw = $inDB->fetch_assoc($result)){
		$aw['pubdate'] = $inCore->dateFormat($aw['pubdate']);
		$aws[] = $aw;
	}
}
    ob_start();

    $smarty = $inCore->initSmarty('components', 'com_users_awards.tpl');
    $smarty->assign('aws', $aws);
	$smarty->assign('is_admin', $is_admin);
    $smarty->display('com_users_awards.tpl');
	
	return ob_get_clean();
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
			if ($usr['imageurl'] && @file_exists(PATH.'/images/users/avatars/'.$usr['imageurl'])){
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
			if ($usr_imageurl && @file_exists(PATH.'/images/users/avatars/'.$usr_imageurl)){
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

    $inDB = cmsDatabase::getInstance();
    global $_LANG;

    if ($online===false){
        $is_online  = $inDB->rows_count('cms_online', "user_id = '$user_id'");
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

    $inDB = cmsDatabase::getInstance();
    global $_LANG;

    if ($online===false){
        $is_online  = $inDB->rows_count('cms_online', "user_id = '$user_id'");
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
    $inCore = cmsCore::getInstance();
    ob_start();
	$smarty = $inCore->initSmarty('components', 'com_error.tpl');
	$smarty->assign('err_title', $_LANG['ACCESS_DENIED']);
	$smarty->assign('err_content', $_LANG['ACCESS_ONLY_REGISTERED']);
	$smarty->display('com_error.tpl');
	return ob_get_clean();
}

function usrFriendOnly(){
    global $_LANG;
    $inCore = cmsCore::getInstance();
    ob_start();
	$smarty = $inCore->initSmarty('components', 'com_error.tpl');
	$smarty->assign('err_title', $_LANG['ACCESS_DENIED']);
	$smarty->assign('err_content', $_LANG['ACCESS_ONLY_FRIENDS']);
	$smarty->display('com_error.tpl');
	return ob_get_clean();
}

function usrAccessDenied(){
    global $_LANG;
    $inCore = cmsCore::getInstance();
    ob_start();
	$smarty = $inCore->initSmarty('components', 'com_error.tpl');
	$smarty->assign('err_title', $_LANG['ACCESS_DENIED']);
	$smarty->assign('err_content', $_LANG['ACCESS_NEED_AVTOR']);
	$smarty->display('com_error.tpl');
	return ob_get_clean();
}

function usrNotAllowed(){
    global $_LANG;
    $inCore = cmsCore::getInstance();
    ob_start();
	$smarty = $inCore->initSmarty('components', 'com_error.tpl');
	$smarty->assign('err_title', $_LANG['ACCESS_BLOCK']);
	$smarty->assign('err_content', $_LANG['ACCESS_SECURITY']);
	$smarty->display('com_error.tpl');
	return ob_get_clean();
}

function usrIsFriends($to_id, $my_id, $is_accepted = true){

	if (!$is_accepted) {
		$inDB  = cmsDatabase::getInstance();
		$total = $inDB->rows_count('cms_user_friends', "(to_id = '$to_id' AND from_id = '$my_id') OR (to_id = '$my_id' AND from_id = '$to_id')");
		return $total ? true : false;
	}

	$my_friends = cmsUser::getFriends($my_id);
	if (!$my_friends) { return false; }
	
    foreach($my_friends as $friend){  
        if($friend['id'] == $to_id){  
           $is_friend = true;  
           break;  
        }else{  
           $is_friend = false;  
        }  
    } 
	
	return $is_friend;
}

function usrFriends($user_id, &$total, $perpage=10, $page=1){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();

	$ses_friends = cmsUser::getFriends($inUser->id);

	// общее количество моих друзей выбирается из сессии, начиная со 2-й страницы
    $total  = (($page > 1) && ($user_id == $inUser->id)) ? sizeof($ses_friends) : $inDB->rows_count('cms_user_friends', "(from_id = '$user_id' OR to_id = '$user_id') AND is_accepted =1");
	// если нет друзей, выходим
	if(!$total) { return false; }
	// очищать сессию друзей если в своем профиле и количество друзей из базы не совпадает с количеством друзей в сессии
	if (($user_id == $inUser->id) && sizeof($ses_friends) != $total) { cmsUser::clearSessionFriends(); }
	
	$sql = "SELECT
			CASE
			WHEN f.from_id = $user_id
			THEN f.to_id
			WHEN f.to_id = $user_id
			THEN f.from_id
			END AS id_friends, u.id as id, u.nickname as nickname, u.login as login, u.is_deleted as is_deleted, u.status, p.imageurl as avatar, u.logdate as flogdate, o.id as online
			FROM cms_user_friends f
			LEFT JOIN cms_users u ON u.id = CASE WHEN f.from_id = $user_id THEN f.to_id WHEN f.to_id = $user_id THEN f.from_id END
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
            LEFT JOIN cms_online o ON p.user_id = o.user_id
			WHERE (from_id = '$user_id' OR to_id = '$user_id') AND is_accepted =1
			LIMIT ".(($page-1)*$perpage).", $perpage";

	$result = $inDB->query($sql) ;

		$friends    = array();

		while ($friend = $inDB->fetch_assoc($result)){

            $friend['flogdate'] = usrStatus($friend['id'], $friend['flogdate'], (int)$friend['online']);
            $friend['avatar']   = usrImageNOdb($friend['id'], 'small', $friend['avatar'], $friend['is_deleted']);
         $friends[$friend['id']] = $friend;

        }

	return $friends;
}

function usrAllowed($allow_who, $owner_id){

    $inCore = cmsCore::getInstance();
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

	$html = '';
	if ($handle = opendir(PATH.'/images/users/awards')) {
		while (false !== ($file = readdir($handle))) {
			$html .= '<div style="float:left;margin:4px">';
			$html .= '<table border="0" cellspacing="0" cellpadding="4"><tr>';
			if ($file != '.' && $file != '..' && strstr($file, '.gif')){
				 $tag = str_replace('.gif', '', $file);
				 $dir = '/images/users/awards/';			 
				 if ($selected != $file){
				 	$html .= '<td align="center" valign="middle"><img src="'.$dir.$file.'" alt="'.htmlspecialchars($file).'"/><br/><input type="radio" name="imageurl" value="'.htmlspecialchars($file).'"/></td>';
  				 } else {
					$html .= '<td align="center" valign="middle"><img src="'.$dir.$file.'" alt="'.htmlspecialchars($file).'"/><br/><input type="radio" name="imageurl" value="'.htmlspecialchars($file).'" checked="checked"/></td>';					
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
			FROM cms_users u
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.gender = 'm'";
	$rs = $inDB->query($sql); $stat['male'] = $inDB->num_rows($rs);
	//female
	$sql = "SELECT u.id 
			FROM cms_users u
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.gender = 'f'";
	$rs = $inDB->query($sql); $stat['female'] = $inDB->num_rows($rs);
	//unknown
	$stat['unknown'] = $total_usr - $stat['male'] - $stat['female'];
	return $stat;
}

function usrCityStats(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$stat = array();
	$empty = $_LANG['NOT_DECIDE'];

	$sql = "SELECT IF (p.city != '', p.city, '$empty') city, COUNT( p.user_id ) count
			FROM cms_users u
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE u.is_locked =0 AND u.is_deleted =0
			GROUP BY p.city";
	$rs = $inDB->query($sql); 
	if ($inDB->num_rows($rs)){ 
		while($row = $inDB->fetch_assoc($rs)){
			if ($row['city'] != $empty) { $row['href'] = '/users/city/'.urlencode($row['city']); } else { $row['href'] = ''; }
			$row['city'] = ucfirst(strtolower($row['city']));
			$stat[] = $row;
		}
	}
	return $stat;
}

?>
