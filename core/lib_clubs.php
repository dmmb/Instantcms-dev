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

function clubRootAlbumId($club_id){
    $inDB = cmsDatabase::getInstance();
	return dbGetField('cms_photo_albums', "parent_id=0 AND NSDiffer='club".$club_id."'", 'id');
}

function clubRepairAlbums(){
    $inDB = cmsDatabase::getInstance();
	$sql = "UPDATE cms_photo_albums SET NSDiffer = CONCAT('club', user_id) WHERE NSDiffer = 'club'";	
	$inDB->query($sql);
}

function setClubRating($club_id){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT SUM( r.points ) AS rating
			FROM cms_user_clubs u, cms_clubs c, cms_ratings r
			LEFT JOIN cms_photo_files f ON r.item_id = f.id AND r.target = 'photo'
			LEFT JOIN cms_blog_posts p ON r.item_id = p.id AND r.target = 'blogpost'
			WHERE u.club_id = $club_id AND (f.user_id = u.user_id OR p.user_id = u.user_id)";
	$rs = $inDB->query($sql);
	if (@$inDB->num_rows($rs)){
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
	$id = dbGetField('cms_blogs', "owner='club' AND user_id=$club_id", 'id');	
	if (!$id){
			$sql = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, view_type, showcats, ownertype, premod, forall, owner)
					VALUES ('$club_id', '����', NOW(), 'all', 'list', 1, 'multi', 0, 0, 'club')";	
			$inDB->query($sql);
			$id = dbGetField('cms_blogs', "owner='club' AND user_id=$club_id", 'id');	
	}
	return $id;
}

function clubBlogContent($blog_id, $is_admin=false, $is_moder=false, $is_member=false){

    global $menuid;

    if (!$blog_id) { exit; }

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

    $inCore->loadModel('blog');

    $model = new cms_model_blog();

	$html = '';
	$sql = "SELECT p.*, b.seolink as bloglink, p.pubdate as fpubdate
			FROM cms_blog_posts p, cms_blogs b
			WHERE p.blog_id = b.id AND p.blog_id = $blog_id AND p.published = 1
			ORDER BY pubdate DESC
			LIMIT 10";

	$rs = $inDB->query($sql);

	if ($inDB->num_rows($rs)){

		$on_moderate = dbRowsCount('cms_blog_posts', 'blog_id='.$blog_id.' AND published = 0');
		$html = '<ul>';
		while ($post = $inDB->fetch_assoc($rs)){
            $bloglink = $post['bloglink'];
			$html .= '<li><a href="'.$model->getPostURL($menuid, $post['bloglink'], $post['seolink']).'">'.$post['title'].'</a> &mdash; '.$inCore->dateFormat($post['fpubdate']).'</li>';
		}
		if ($is_member || $is_moder || $is_admin){
			$html .= '<li class="service"><a href="/blogs/'.$menuid.'/'.$blog_id.'/newpost.html">�������� ����� ����</li>';
		}
		if (($is_admin || $is_moder) && $on_moderate){
			$html .= '<li><a class="on_moder" href="/blogs/'.$menuid.'/'.$blog_id.'/moderate.html">������ �� ���������</a> ('.$on_moderate.')</li>';
		}
		$html .= '<li class="all"><a href="'.$model->getBlogURL($menuid, $bloglink).'">��� ������</a> ('.dbRowsCount('cms_blog_posts', "blog_id=$blog_id AND published=1").')</li>';
		$html .= '</ul>';

	} else {

		if ($is_member || $is_moder || $is_admin){
			$html .= '<ul>';
				$html .= '<li class="service"><a href="/blogs/'.$menuid.'/'.$blog_id.'/newpost.html">�������� ����� ����</a></li>';
			$html .= '</ul>';	
		} else {
			$html = '<p>� ������� ����� ��� �������.</p>';	
		}

	}

	return $html;

}

function clubPhotoAlbums($club_id, $is_admin=false, $is_moder=false, $is_member=false){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }	
	global $menuid;
	$html = '';
	$sql = "SELECT a.id, a.title, IFNULL(COUNT(f.id), 0) as content_count
			FROM cms_photo_albums a
			LEFT JOIN cms_photo_files f ON f.album_id = a.id AND f.published = 1
			WHERE a.NSDiffer='club$club_id' AND a.user_id=$club_id AND a.parent_id > 0
			GROUP BY a.id
			ORDER BY f.pubdate DESC";
					
	$rs = $inDB->query($sql);
	$html = '<ul id="albums_list">';
		if ($inDB->num_rows($rs)){
				while ($album = $inDB->fetch_assoc($rs)){
					$on_moderate = ''; $delete='';
					if ($is_admin || $is_moder){
						$unpub = dbRowsCount('cms_photo_files', 'album_id='.$album['id'].' AND published = 0');
						if ($unpub) { $on_moderate = ' <span class="on_moder">(�� ��������� &mdash; '.$unpub.')</span>'; }
						$delete = ' <a class="delete" title="������� ������" href="javascript:void(0)" onclick="javascript:deleteAlbum('.$album['id'].', \''.$album['title'].'\', '.$club_id.')">X</a>';
					}
					$tday = date("d-m-Y");
					$today = dbRowsCount('cms_photo_files', 'published=1 AND \''.$tday.'\'=DATE_FORMAT(pubdate, \'%d-%m-%Y\') AND album_id='.$album['id']);
					if ($today) { $new = ' <span class="new">+'.$today.'</span>'; } else { $new = ''; }
					$html .= '<li class="club_album" id="'.$album['id'].'"><a href="/photos/'.$menuid.'/'.$album['id'].'">'.$album['title'].'</a> ('.$album['content_count'].$new.') '.$on_moderate.$delete;
				}
		} else {
			$html .= '<li class="no_albums">� ����� ��� ������������.</li>';
		}
	$html .= '</ul>';	
	return $html;
}

function clubUserIsRole($club_id, $user_id, $role='member'){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_user_clubs', "club_id=$club_id AND user_id=$user_id AND role='$role'")? true: false;
}

function clubUserIsMember($club_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_user_clubs', "club_id=$club_id AND user_id=$user_id")? true: false;
}

function clubUserIsAdmin($club_id, $user_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { return; }
	return $inDB->rows_count('cms_clubs', "id=$club_id AND admin_id=$user_id")? true: false;
}

function clubModerators($club_id){
    $inDB = cmsDatabase::getInstance();
	if (!$club_id) { exit; }
	$moders = array();
	$sql = "SELECT c.* 
			FROM cms_user_clubs c, cms_users u
			WHERE c.club_id = $club_id AND c.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0 AND c.role = 'moderator'";
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
			LEFT JOIN cms_users u ON u.id=c.user_id AND u.is_deleted = 0 AND u.is_locked = 0
			WHERE c.club_id = $club_id AND c.role = 'member'";
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
	$sql = "SELECT c.* 
			FROM cms_user_clubs c
			LEFT JOIN cms_users u ON u.id=c.user_id AND u.is_deleted = 0 AND u.is_locked = 0
			WHERE c.club_id = $club_id AND c.role = 'member'";
	$rs = $inDB->query($sql);
	if ($inDB->num_rows($rs)){
		return $inDB->num_rows($rs) +1; //+1 ������ ��� ������� ��� � ������, �� ������ ������
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

function clubSaveUsers($club_id, $list, $role, $clubtype='public', $cfg=false, $menuid=0){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if ($list){
        //get current club users list
        $current_list = dbGetTable('cms_user_clubs', "club_id={$club_id} AND role='{$role}'", 'user_id');

        //delete users which missed in new list
        foreach ($current_list as $key=>$user){
            if (!in_array($user['user_id'], $list)){
                $inDB->query("DELETE FROM cms_user_clubs WHERE club_id={$club_id} AND user_id={$user['user_id']} AND role='{$role}' LIMIT 1");
                //send notice
                if($cfg['notify_out'] && ($user_id != $inUser->id)){
                    $club_title = dbGetField('cms_clubs', 'id='.$club_id, 'title');
                    cmsUser::sendMessage(USER_UPDATER, $user_id, '������������ [url='.cmsUser::getProfileURL($inUser->login).']'.$inUser->nickname.'[/url] �������� ��� �� ����� ���������� ����� [URL=http://'.$_SERVER['HTTP_HOST'].'/clubs/'.$menuid.'/'.$club_id.']'.$club_title.'[/URL].');
                }
            }
        }

        //add new users and update old
		foreach ($list as $key=>$user_id){
			$user_id = (int)$user_id;
            $already = dbGetField('cms_user_clubs', "user_id={$user_id} AND club_id={$club_id}", 'role');
            if (!$already){
                //user first time in this club
                $sql = "INSERT INTO cms_user_clubs (user_id, club_id, role)
                        VALUES ($user_id, $club_id, '$role')";
                $inDB->query($sql);

                //send notice
                if($cfg['notify_in'] && ($user_id != $inUser->id)){
                    $club_title = dbGetField('cms_clubs', 'id='.$club_id, 'title');
                    cmsUser::sendMessage(USER_UPDATER, $user_id, '[b]�������� ����������� � ����.[/b] ������������ [url='.cmsUser::getProfileURL($inUser->login).']'.$inUser->nickname.'[/url] ������� ��� � ����� ���������� ����� [URL=http://'.$_SERVER['HTTP_HOST'].'/clubs/'.$menuid.'/'.$club_id.']'.$club_title.'[/URL].');
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
	global $menuid;
	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
			FROM cms_clubs c, cms_users u, cms_user_profiles p
			WHERE c.id = $club_id AND c.admin_id = u.id AND p.user_id = u.id";
	$rs = @$inDB->query($sql);
	$html = '';
	if (@$inDB->num_rows($rs) == 1){
		$usr = $inDB->fetch_assoc($rs);
		$html .= cmsUser::getGenderLink($usr['id'], $usr['nickname'], $menuid, $usr['gender'], $usr['login']);
	}
	return $html;
}

function clubMembersList($club_id){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
	
    global $menuid;

	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
			FROM cms_user_clubs c, cms_users u, cms_user_profiles p
			WHERE c.club_id = $club_id AND c.user_id = u.id AND p.user_id = u.id AND u.is_locked = 0 AND u.is_deleted = 0";

	$rs     = $inDB->query($sql);
	$total  = $inDB->num_rows($rs);

	$now=0; $html = '';

	while($usr = $inDB->fetch_assoc($rs)){				
		$html .= cmsUser::getGenderLink($usr['id'], $usr['nickname'], $menuid, $usr['gender'], $usr['login']);
		if ($now < $total-1) { $html .= ', '; }
		$now ++;
	}
    
	return $html;
}

?>