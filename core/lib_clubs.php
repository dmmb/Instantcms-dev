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

function clubRootAlbumId($club_id){
    $inDB = cmsDatabase::getInstance();
	return $inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer='club".$club_id."'", 'id');
}

function clubRepairAlbums(){
    $inDB = cmsDatabase::getInstance();
	$sql = "UPDATE cms_photo_albums SET NSDiffer = CONCAT('club', user_id) WHERE NSDiffer = 'club'";	
	$inDB->query($sql);
}

function setClubRating($club_id){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT SUM( u.rating ) AS rating
			FROM cms_user_clubs c
			LEFT JOIN cms_users u ON u.id = c.user_id
			WHERE c.club_id = '$club_id'";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		$data = $inDB->fetch_assoc($rs);
		$rating = $data['rating'] * 5;
	} else {
		$rating = 0;
	}
	
	$sql = "UPDATE cms_clubs SET rating = $rating WHERE id = $club_id";
	$inDB->query($sql);
}

function cmsUserClubs($user_id){
    $inDB = cmsDatabase::getInstance();
	$userclubs['member'] = array();
	$userclubs['moder'] = array();
	$userclubs['admin'] = array();

	//check member/moder
	$sql = "SELECT u.*, c.title as title, c.id as id
			FROM cms_user_clubs u, cms_clubs c
			WHERE u.user_id = $user_id AND u.club_id = c.id AND c.published = 1
			ORDER BY c.title DESC";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		while ($record = $inDB->fetch_assoc($rs)){
			if ($record['role'] == 'moderator'){
				$userclubs['moder'][] = $record;
			}
			if ($record['role'] == 'member'){
				$userclubs['member'][] = $record;
			}			
		}
	}
	
	//check admin
	$sql = "SELECT title, id, admin_id
			FROM cms_clubs
			WHERE admin_id = $user_id AND published = 1
			ORDER BY title DESC";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		while ($record = $inDB->fetch_assoc($rs)){
			$userclubs['admin'][] = $record;
		}
	}
	
	if ($userclubs['admin'] || $userclubs['member'] || $userclubs['moder']){
		return $userclubs;	
	} else {
		return false;
	}
}

function clubBlogId($club_id){
    $inDB = cmsDatabase::getInstance();
	$id   = $inDB->get_field('cms_blogs', "owner='club' AND user_id=$club_id", 'id');	
	if (!$id){
			$sql = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, view_type, showcats, ownertype, premod, forall, owner)
					VALUES ('$club_id', 'Блог', NOW(), 'all', 'list', 1, 'multi', 0, 0, 'club')";	
			$inDB->query($sql);
			$id = $inDB->get_field('cms_blogs', "owner='club' AND user_id=$club_id", 'id');	
	}
	return $id;
}

function clubBlogContent($blog_id, $is_admin=false, $is_moder=false, $is_member=false){

    if (!$blog_id) { exit; }

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

    $inCore->loadModel('blogs');

    $model = new cms_model_blogs();

	$html = '';
	$sql = "SELECT p.*, b.seolink as bloglink, p.pubdate as fpubdate
			FROM cms_blog_posts p
			LEFT JOIN cms_blogs b ON b.id = p.blog_id
			WHERE p.blog_id = $blog_id AND p.published = 1
			ORDER BY pubdate DESC
			LIMIT 10";

	$rs = $inDB->query($sql);

	if ($inDB->num_rows($rs) || $blog_id){

		$on_moderate = $inDB->rows_count('cms_blog_posts', 'blog_id='.$blog_id.' AND published = 0');
		$html = '<ul>';
		while ($post = $inDB->fetch_assoc($rs)){
            $bloglink = $post['bloglink'];
			$html .= '<li><a href="'.$model->getPostURL(null, $post['bloglink'], $post['seolink']).'">'.$post['title'].'</a> &mdash; '.$inCore->dateFormat($post['fpubdate']).'</li>';
		}
		if ($is_member || $is_moder || $is_admin){
			$html .= '<li class="service"><a href="/blogs/'.$blog_id.'/newpost.html">Добавить новый пост</li>';
		}
		if (($is_admin || $is_moder) && $on_moderate){
			$html .= '<li><a class="on_moder" href="/blogs/'.$blog_id.'/moderate.html">Записи на модерацию</a> ('.$on_moderate.')</li>';
		}
		$html .= '<li class="all"><a href="'.$model->getBlogURL(null, $bloglink).'">Все записи</a> ('.$inDB->rows_count('cms_blog_posts', "blog_id=$blog_id AND published=1").')</li>';
		$html .= '</ul>';

	} else {

		if ($is_member || $is_moder || $is_admin){
			$html .= '<ul>';
				$html .= '<li class="service"><a href="/blogs/'.$blog_id.'/newpost.html">Добавить новый пост</a></li>';
			$html .= '</ul>';	
		} else {
			$html = '<p>В клубном блоге нет записей.</p>';	
		}

	}

	return $html;

}

function clubPhotoAlbums($club_id, $is_admin=false, $is_moder=false, $is_member=false){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }	

	$html = '';
	$sql = "SELECT a.id, a.title, IFNULL(COUNT(f.id), 0) as content_count
			FROM cms_photo_albums a
			LEFT JOIN cms_photo_files f ON f.album_id = a.id AND f.published = 1
			WHERE a.NSDiffer='club$club_id' AND a.user_id=$club_id AND a.parent_id > 0
			GROUP BY a.id
			ORDER BY a.id DESC";
					
	$rs = $inDB->query($sql);
	$html = '<ul id="albums_list">';
		if ($inDB->num_rows($rs)){
				while ($album = $inDB->fetch_assoc($rs)){
					$on_moderate = ''; $delete='';
					if ($is_admin || $is_moder){
						$unpub = $inDB->rows_count('cms_photo_files', 'album_id='.$album['id'].' AND published = 0');
						if ($unpub) { $on_moderate = ' <span class="on_moder">(На модерации &mdash; '.$unpub.')</span>'; }
						$delete = ' <a class="delete" title="Удалить альбом" href="javascript:void(0)" onclick="javascript:deleteAlbum('.$album['id'].', \''.$album['title'].'\', '.$club_id.')">X</a>';
					}
					$tday = date("d-m-Y");
					$today = $inDB->rows_count('cms_photo_files', 'published=1 AND \''.$tday.'\'=DATE_FORMAT(pubdate, \'%d-%m-%Y\') AND album_id='.$album['id']);
					if ($today) { $new = ' <span class="new">+'.$today.'</span>'; } else { $new = ''; }
					$html .= '<li class="club_album" id="'.$album['id'].'"><a href="/photos/'.$album['id'].'">'.$album['title'].'</a> ('.$album['content_count'].$new.') '.$on_moderate.$delete;
				}
		} else {
			$html .= '<li class="no_albums">В клубе нет фотоальбомов.</li>';
		}
	$html .= '</ul>';	
	return $html;
}

function clubUserIsRole($club_id, $user_id, $role='member'){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_user_clubs', "club_id = '$club_id' AND user_id = '$user_id' AND role='$role'")? true: false;
}

function clubUserIsMember($club_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_user_clubs', "club_id = '$club_id' AND user_id = '$user_id'")? true: false;
}

function clubUserIsAdmin($club_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_clubs', "id = '$club_id' AND admin_id = '$user_id'")? true: false;
}

function clubModerators($club_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }
	$moders = array();
	$sql = "SELECT c.* 
			FROM cms_user_clubs c
			WHERE c.club_id = '$club_id' AND c.role = 'moderator'";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		while ($u = $inDB->fetch_assoc($rs)){
			if (!in_array($u['user_id'], $moders)){
				$moders[] = $u['user_id'];
			}
		}
	}
	return $moders;
}

function clubMembers($club_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }
	$members = array();
	$sql = "SELECT c.* 
			FROM cms_user_clubs c
			WHERE c.club_id = '$club_id' AND c.role = 'member'";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		while ($u = $inDB->fetch_assoc($rs)){
			if (!in_array($u['user_id'], $members)){
				$members[] = $u['user_id'];
			}
		}
	}
	return $members;
}

function clubTotalMembers($club_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }
	$members = array();
	$sql = "SELECT 1
			FROM cms_user_clubs c
			WHERE c.club_id = '$club_id' AND c.role = 'member'";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		return $inDB->num_rows($rs) +1; //+1 потому что считаем еще и админа, не только юзеров
	} else {
		return 1;
	}
}

function clubAddUser($club_id, $user_id, $role='member'){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("INSERT INTO cms_user_clubs (user_id, club_id, role) VALUES ($user_id, $club_id, '$role')");
	return;
}

function clubRemoveUser($club_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("DELETE FROM cms_user_clubs WHERE user_id=$user_id AND club_id=$club_id");
	return;
}

function clubSaveUsers($club_id, $list, $role, $clubtype='public', $cfg=false){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if ($list){
        //get current club users list
        $current_list = $inDB->get_table('cms_user_clubs', "club_id={$club_id} AND role='{$role}'", 'user_id');

        //delete users which missed in new list
        foreach ($current_list as $key=>$user){
            if (!in_array($user['user_id'], $list)){
                $inDB->query("DELETE FROM cms_user_clubs WHERE club_id={$club_id} AND user_id={$user['user_id']} AND role='{$role}' LIMIT 1");
                //send notice
                if($cfg['notify_out'] && ($user_id != $inUser->id)){
                    $club_title = $inDB->get_field('cms_clubs', 'id='.$club_id, 'title');
                    cmsUser::sendMessage(USER_UPDATER, $user_id, 'Пользователь <a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a> исключил Вас из числа участников клуба <a href="http://'.$_SERVER['HTTP_HOST'].'/clubs/'.$club_id.'">'.$club_title.'</a>.');
                }
            }
        }

        //add new users and update old
		foreach ($list as $key=>$user_id){
			$user_id = (int)$user_id;
            $already = $inDB->get_field('cms_user_clubs', "user_id={$user_id} AND club_id={$club_id}", 'role');
            if (!$already){
                //user first time in this club
                $sql = "INSERT INTO cms_user_clubs (user_id, club_id, role)
                        VALUES ($user_id, $club_id, '$role')";
                $inDB->query($sql);

                //send notice
                if($cfg['notify_in'] && ($user_id != $inUser->id)){
                    $club_title = $inDB->get_field('cms_clubs', 'id='.$club_id, 'title');
                    cmsUser::sendMessage(USER_UPDATER, $user_id, '<b>Получено приглашение в клуб.</b> Пользователь <a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a> добавил Вас в число участников клуба <a href="http://'.$_SERVER['HTTP_HOST'].'/clubs/'.$club_id.'">'.$club_title.'</a>.');
                }
            } else {
                //user already in club, update his role if necessary
                if ($already != $role){
                    $sql = "UPDATE cms_user_clubs
                               SET role='{$role}'
                             WHERE user_id={$user_id} AND club_id={$club_id}";
                    $inDB->query($sql);
                }
            }
		}							
	} else {
        //if new users list is empty, drop everyone from this club
        $inDB->query("DELETE FROM cms_user_clubs WHERE club_id={$club_id} AND role='{$role}'  LIMIT 1");
    }
}

function clubAdminLink($club_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
			FROM cms_clubs c, , 
			LEFT JOIN cms_users u ON u.id = c.admin_id
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE c.id = '$club_id'";
	$rs     = $inDB->query($sql);
	$html = '';
	if ($inDB->num_rows($rs) == 1){
		$usr = $inDB->fetch_assoc($rs);
		$html .= cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
	}
	return $html;
}

function clubMembersList($club_id){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
	
	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
			FROM cms_user_clubs c
			LEFT JOIN cms_users u ON u.id = c.user_id
			LEFT JOIN cms_user_profiles p ON p.user_id = u.id
			WHERE c.club_id = '$club_id'";

	$rs     = $inDB->query($sql);
	$total  = $inDB->num_rows($rs);

	$now=0; $html = '';

	while($usr = $inDB->fetch_assoc($rs)){				
		$html .= cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
		if ($now < $total-1) { $html .= ', '; }
		$now ++;
	}
    
	return $html;
}

?>