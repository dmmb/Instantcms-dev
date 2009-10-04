<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

if (!defined('USER_UPDATER'))  { define('USER_UPDATER',  -1); }
if (!defined('USER_MASSMAIL')) { define('USER_MASSMAIL', -2); }

function setClubsRating(){

    $inDB = cmsDatabase::getInstance();

	$sql = "SELECT id FROM cms_clubs";
	$res = $inDB->query($sql);
	
	while($club = $inDB->fetch_assoc($res)){
		$sql = "SELECT SUM( r.points ) AS rating
				FROM cms_user_clubs u, cms_clubs c, cms_ratings r
				LEFT JOIN cms_photo_files f ON r.item_id = f.id AND r.target = 'photo'
				LEFT JOIN cms_blog_posts p ON r.item_id = p.id AND r.target = 'blogpost'
				WHERE u.club_id = ".$club['id']." AND (f.user_id = u.user_id OR p.user_id = u.user_id)";
		$rs = $inDB->query($sql);
		if ($inDB->num_rows($rs)){
			$data = $inDB->fetch_assoc($rs);
			$rating = $data['rating'] * 5;
		} else {
			$rating = 0;
		}	
		$sql = "UPDATE cms_clubs SET rating = $rating WHERE id = ".$club['id'];
		$inDB->query($sql);
	}

}

function setUsersRating(){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT id FROM cms_users WHERE is_locked = 0 AND is_deleted = 0";
	$res = $inDB->query($sql);
	
	while($user = $inDB->fetch_assoc($res)){
		$sql = "SELECT SUM( r.points ) AS rating
				FROM cms_ratings r
				LEFT JOIN cms_content c ON r.item_id = c.id AND r.target = 'content'
				LEFT JOIN cms_photo_files f ON r.item_id = f.id AND r.target = 'photo'
				LEFT JOIN cms_blog_posts p ON r.item_id = p.id AND r.target = 'blogpost'
				WHERE c.user_id = ".$user['id']." OR f.user_id = ".$user['id']." OR p.user_id = ".$user['id'];
		$rs = $inDB->query($sql);
		if ($inDB->num_rows($rs)){
			$data = $inDB->fetch_assoc($rs);
			$rating = $data['rating'] * 5;
		} else {
			$rating = 0;
		}

        $sql = "SELECT SUM(v.vote) AS votes
				FROM cms_comments_votes v
				LEFT JOIN cms_comments c ON v.comment_id = c.id
				WHERE c.user_id = ".$user['id']." AND v.comment_type = 'com'";
		$rs = $inDB->query($sql);
		if ($inDB->num_rows($rs)){
			$data = $inDB->fetch_assoc($rs);
			$rating += $data['votes'] * 2;
		}

		$sql = "UPDATE cms_users SET rating = $rating WHERE id = ".$user['id'];
		$inDB->query($sql);
	}
}

function cmsClearKarma($target, $item_id){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("DELETE FROM cms_ratings WHERE target='$target' AND item_id = $item_id");
	return;
}

function cmsKarma($target, $item_id){ //returns array with total votes and total points of karma
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT *, SUM(points) as points, COUNT(id) as votes
			FROM cms_ratings 
			WHERE item_id = $item_id AND target='$target'
			GROUP BY item_id";
	$result = $inDB->query($sql);
	if ($inDB->num_rows($result)){
		$data = $inDB->fetch_assoc($result);
		$data['points'] = round($data['points'], 2);
	} else {
		$data['points'] = 0;
		$data['votes'] = 0;
	}	
	return $data;
}

function cmsAlreadyKarmed($target, $item_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	return $inDB->rows_count('cms_ratings', "target='$target' AND item_id = $item_id AND user_id = '$user_id'");
}

function cmsAlreadyKarmedIP($target, $item_id, $ip){
    $inDB = cmsDatabase::getInstance();
	return $inDB->rows_count('cms_ratings', "target='$target' AND item_id = $item_id AND ip = '$ip'");
}

function cmsAlreadyKarmedAny($target, $user_id){
    $inDB = cmsDatabase::getInstance();
	return $inDB->rows_count('cms_ratings', "target='$target' AND user_id = '$user_id'");
}

function cmsAlreadyKarmedAnyIP($target, $ip){
    $inDB = cmsDatabase::getInstance();
	return $inDB->rows_count('cms_ratings', "target='$target' AND ip = '$ip'");
}

function cmsSubmitKarma($target, $item_id, $points){
    $inUser = cmsUser::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$id     = $inUser->id;
	$ip     = $_SERVER['REMOTE_ADDR'];
	if(!cmsAlreadyKarmed($target, $item_id, $id)){
		$sql = "INSERT INTO cms_ratings (item_id, points, ip, target, user_id, pubdate) VALUES ($item_id, $points, '$ip', '$target', $id, NOW())";
		$inDB->query($sql);
	}
	return true;
}
function cmsKarmaFormat($points){
    $inDB = cmsDatabase::getInstance();
	if ($points==0) {
		$html = '<span style="color:silver;">0</span>';
	} elseif ($points>0){
		$html = '<span style="color:green;">+'.$points.'<span style="color:silver">&uarr;</span></span>';
	} else {
		$html = '<span style="color:red;">'.$points.'<span style="color:silver">&darr;</span></span>';
	}
	return $html;
}
function cmsKarmaFormatSmall($points){
    $inDB = cmsDatabase::getInstance();
	if ($points==0) {
		$html = '<span style="color:gray;">0</span>';
	} elseif ($points>0){
		$html = '<span style="color:green">+'.$points.'</span>';
	} else {
		$html = '<span style="color:red">'.$points.'</span>';
	}
	return $html;
}

function cmsKarmaForm($target, $target_id){
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inPage = cmsPage::getInstance();
	$html = '';
	$postkarma = cmsKarma($target, $target_id);
	//PREPARE POINTS
	$points = cmsKarmaFormat($postkarma['points']);
	$control = '';
	//PREPARE RATING FORM
	if ($inUser->id){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			$control .= '<div style="text-align:center;margin-top:10px;">';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', \''.$target_id.'\')" title="Одобрить"><img src="/components/users/images/karma_up.gif" border="0" alt="Карма+"/></a> ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', \''.$target_id.'\')" title="Отклонить"><img src="/components/users/images/karma_down.gif" border="0" alt="Карма-"/></a>';
			$control .= '</div>'; 
		}
	}
	$html .= '<div class="karma_form">';
		$html .= '<div id="karmapoints" style="font-size:24px">'.$points.'</div>';
		$html .= '<div id="karmavotes">Голосов: '.$postkarma['votes'].'</div>';
		$html .= '<div id="karmactrl">'.$control.'</div>';
	$html .= '</div>';
	return $html;
}

function cmsKarmaButtons($target, $target_id){
    
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inPage = cmsPage::getInstance();
	$html   = '';

	$postkarma = cmsKarma($target, $target_id);

	//PREPARE POINTS
	$points = cmsKarmaFormat($postkarma['points']);

	//PREPARE RATING FORM
	if ($inUser->id){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			
			$control .= '<div style="text-align:center">';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', '.$target_id.');" title="Одобрить"><img src="/components/users/images/karma_up.gif" border="0" alt="Карма+"/></a> ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', '.$target_id.');" title="Отклонить"><img src="/components/users/images/karma_down.gif" border="0" alt="Карма-"/></a>';
			$control .= '</div>'; 
		}
	}

	$html .= '<div class="karma_buttons">';
		$html .= '<div id="karmactrl">'.$control.'</div>';
	$html .= '</div>';

	return $html;
    
}

function cmsKarmaButtonsText($target, $target_id){
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    $inPage = cmsPage::getInstance();
	$html = '';
	$postkarma = cmsKarma($target, $target_id);
	//PREPARE POINTS
	$points = cmsKarmaFormat($postkarma['points']);
	$control = '';
	//PREPARE RATING FORM
	if ($inUser->id){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			$control .= '<span>';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', '.$target_id.');" style="color:green">Одобрить</a> &uarr; ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', '.$target_id.');" style="color:red">Отклонить</a> &darr;';
			$control .= '</span>'; 
			$html .= '<span class="karma_buttons">';
					$html .= '<span id="karmactrl">'.$control.'</span>';
				$html .= '</span>';			
		}
	}
	return $html;
}

?>