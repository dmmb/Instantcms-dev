<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

if (!defined('USER_UPDATER'))  { define('USER_UPDATER',  -1); }
if (!defined('USER_MASSMAIL')) { define('USER_MASSMAIL', -2); }

function setClubsRating($club_id){

    $inDB = cmsDatabase::getInstance();

    if (!is_int($club_id)){ return; }

    $sql = "SELECT SUM( u.rating ) AS rating
			FROM cms_user_clubs c
			LEFT JOIN cms_users u ON u.id = c.user_id
			WHERE c.club_id = '$club_id'";
    
    $rs = $inDB->query($sql);
    
    if ($inDB->num_rows($rs)){
        $data   = $inDB->fetch_assoc($rs);
        $rating = $data['rating'] * 5;
    } else {
        $rating = 0;
    }

    $sql = "UPDATE cms_clubs SET rating = '$rating' WHERE id = '$club_id'";
    $inDB->query($sql);

}

function setUsersRating(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $target     = $inCore->request('target', 'str', '');
    $item_id    = $inCore->request('item_id', 'int', 0);
    $opt        = $inCore->request('opt', 'str', 'plus');

    $comment_id     = $inCore->request('comment_id', 'int', 0);
    $comment_vote   = $inCore->request('vote', 'int', 1);

    if ($comment_id) { $target = 'comment'; $item_id = $comment_id; }

    $table = '';

    switch($target){
        case 'blogpost':    $table = 'cms_blog_posts';  break;
        case 'content':     $table = 'cms_content';     break;
        case 'comment':     $table = 'cms_comments';    break;
    }

    if (!$table) { return false; }

    $author_sql = "SELECT u.id as id
                   FROM cms_users u, {$table} t
                   WHERE t.id = {$item_id} AND t.user_id = u.id
                   LIMIT 1";

    $author_res = $inDB->query($author_sql);

    if (!$inDB->num_rows($author_res)) { return false; }

    $author = $inDB->fetch_assoc($author_res);

    if ($comment_id){
        $inc  = ($comment_vote>0 ? ('+'.$comment_vote*2) : ($comment_vote*2));
    } else {
        $inc  = ($opt=='plus' ? '+ 5' : '- 5');
    }

    $inDB->query("UPDATE cms_users SET rating = rating {$inc} WHERE id = {$author['id']}");

}

function cmsClearKarma($target, $item_id){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("DELETE FROM cms_ratings WHERE target='$target' AND item_id = $item_id");
	return;
}

function cmsKarma($target, $item_id){ //returns array with total votes and total points of karma
    
    if (!preg_match('/^([a-zA-Z0-9\_]+)$/i', $target)) { return; }

    $item_id = intval($item_id);

    $inDB = cmsDatabase::getInstance();

    $item = $inDB->get_fields('cms_ratings_total', "item_id = '$item_id' AND target='$target'", 'total_rating, total_votes');

	if (!$item){ return array('points'=>0, 'votes'=>0); }

	return array('points'=>$item['total_rating'], 'votes'=>$item['total_votes']);

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

	if(cmsAlreadyKarmed($target, $item_id, $id)){ return false; }

    //��������� ����� �����
    $sql = "INSERT INTO cms_ratings (item_id, points, ip, target, user_id, pubdate)
            VALUES ('$item_id', '$points', '$ip', '$target', '$id', NOW())";
    $inDB->query($sql);

    //��������� ���� �� ������� ��������� ��� ���� ���� �����
    $is_agr = $inDB->rows_count('cms_ratings_total', "target='$target' AND item_id = '$item_id'", 1);

    //���� ����, �� ���������
    if ($is_agr) { $agr_sql = "UPDATE cms_ratings_total
                               SET  total_rating = total_rating + ({$points}),
                                    total_votes  = total_votes + 1
                               WHERE target='$target' AND item_id = '$item_id'"; }

    //���� �� ����, �� ���������
    if (!$is_agr) { $agr_sql = "INSERT INTO cms_ratings_total (target, item_id, total_rating, total_votes)
                                VALUES ('{$target}', '{$item_id}', '{$points}', '1')"; }

    $inDB->query($agr_sql);

    //�������� ���������� � ����
    $info = $inDB->get_fields('cms_rating_targets', "target='{$target}'", '*');

    //���� �����, �������� ������� ������ ����
    if ($info['is_user_affect'] && $info['user_weight'] && $info['target_table']){

        $user_sql = "UPDATE cms_users u,
                            {$info['target_table']} t
                     SET u.rating = u.rating + ({$points}*{$info['user_weight']})
                     WHERE t.user_id = u.id AND t.id = '$item_id'";

        $inDB->query($user_sql);
        
    }

    //��������� ������� ������ updateRatingHook(target, item_id, points) � ������
    //����������, �������������� �� ����
    if ($info['component']){
        $inCore = cmsCore::getInstance();
        $inCore->loadModel($info['component']);
        if (class_exists('cms_model_'.$info['component'])){
            eval('$model = new cms_model_'.$info['component'].'();');
            if (method_exists($model, 'updateRatingHook')){
                $model->updateRatingHook($target, $item_id, $points);
            }
        }
    }

	return true;

}

function cmsKarmaFormat($points){
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
	if ($points==0) {
		$html = '<span style="color:gray;">0</span>';
	} elseif ($points>0){
		$html = '<span style="color:green">+'.$points.'</span>';
	} else {
		$html = '<span style="color:red">'.$points.'</span>';
	}
	return $html;
}

function cmsKarmaForm($target, $target_id, $points = 0, $is_author = false){
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    $inPage     = cmsPage::getInstance();
	$html       = '';

	global $_LANG;
	
	if (!$points) {
		$postkarma = cmsKarma($target, $target_id);
	$points     = cmsKarmaFormat($postkarma['points']);
	} else {
		$points    = $points;
    }
    
	$control    = '';

	//PREPARE RATING FORM
	if ($inUser->id && !$is_author){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			$control .= '<div style="text-align:center;margin-top:10px;">';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', \''.$target_id.'\')" title="'.$_LANG['LIKE'].'"><img src="/components/users/images/karma_up.png" border="0" alt="�����+"/></a> ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', \''.$target_id.'\')" title="'.$_LANG['UNLIKE'].'"><img src="/components/users/images/karma_down.png" border="0" alt="�����-"/></a>';
			$control .= '</div>'; 
		}
	}
	$html .= '<div class="karma_form">';
		$html .= '<div id="karmapoints" style="font-size:24px">'.$points.'</div>';
		$html .= '<div id="karmavotes">�������: '.$postkarma['votes'].'</div>';
		$html .= '<div id="karmactrl">'.$control.'</div>';
	$html .= '</div>';
	return $html;
}

function cmsKarmaButtons($target, $target_id, $points = 0, $is_author = false){
    
    $inUser = cmsUser::getInstance();
    $inPage = cmsPage::getInstance();
	$html   = '';
    $control = '';
	global $_LANG;

	if (!$points) {
	$postkarma = cmsKarma($target, $target_id);
	$points = cmsKarmaFormat($postkarma['points']);
	} else {
		$points    = $points;
	}

	//PREPARE RATING FORM
	if ($inUser->id && !$is_author){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			
			$control .= '<div style="text-align:center">';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', '.$target_id.');" title="'.$_LANG['LIKE'].'"><img src="/components/users/images/karma_up.png" border="0" alt="�����+"/></a> ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', '.$target_id.');" title="'.$_LANG['UNLIKE'].'"><img src="/components/users/images/karma_down.png" border="0" alt="�����-"/></a>';
			$control .= '</div>'; 
		}
	}

    if ($control){
        $html .= '<div class="karma_buttons">';
            $html .= '<div id="karmactrl">'.$control.'</div>';
        $html .= '</div>';
    }

	return $html;
    
}
function cmsKarmaButtonsText($target, $target_id, $points = 0, $is_author = false){

    $inUser = cmsUser::getInstance();
    $inPage = cmsPage::getInstance();
	$html = '';

	if (!$points) {
	$postkarma = cmsKarma($target, $target_id);
	$points = cmsKarmaFormat($postkarma['points']);
	} else {
		$points    = $points;
	}

	$control = '';
	//PREPARE RATING FORM
	if ($inUser->id && !$is_author){
		if(!cmsAlreadyKarmed($target, $target_id, $inUser->id)){
			$inPage->addHeadJS('core/js/karma.js');
			$control .= '<span>';
				$control .= '<a href="javascript:void(0);" onclick="plusKarma(\''.$target.'\', '.$target_id.');" style="color:green">��������</a> &uarr; ';
				$control .= '<a href="javascript:void(0);" onclick="minusKarma(\''.$target.'\', '.$target_id.');" style="color:red">�� ��������</a> &darr;';
			$control .= '</span>'; 
			$html .= '<span class="karma_buttons">';
					$html .= '<span id="karmactrl">'.$control.'</span>';
				$html .= '</span>';			
		}
	}
	return $html;
}

?>