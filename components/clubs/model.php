<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_clubs{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClubs($page=1, $perpage=100) {
        $inUser = cmsUser::getInstance();
        $clubs  = array();
        
        global $_LANG;

        $sql =  "SELECT c.*, (COUNT(uc.user_id)+1) as members, c.pubdate as pubdate
                 FROM cms_clubs c
                 LEFT JOIN cms_user_clubs uc ON uc.club_id = c.id
                 WHERE c.published = 1
                 GROUP BY c.id
                 ORDER BY is_vip DESC, members DESC
                 LIMIT ".(($page-1)*$perpage).", $perpage";

        $rs  = $this->inDB->query($sql) or die(mysql_error());

        if (!$this->inDB->num_rows($rs)){ return false;	}

        while ($club = $this->inDB->fetch_assoc($rs)){
            $clubs[] = $club;
        }

        $clubs = cmsCore::callEvent('GET_CLUBS', $clubs);

        return $clubs;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClub($club_id) {

        global $_LANG;
        $sql =  "SELECT *
                 FROM cms_clubs
                 WHERE id = $club_id";
        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false;	}

        $club = $this->inDB->fetch_assoc($result);

        $club = cmsCore::callEvent('GET_CLUB', $club);

        return $club;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClubsCount() {
        return $this->inDB->rows_count('cms_clubs', 'published=1');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClubBlogId($club_id) {

    	$id = $this->inDB->get_field('cms_blogs', "owner='club' AND user_id=$club_id", 'id');

        if (!$id){
                $sql = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, view_type, showcats, ownertype, premod, forall, owner)
                        VALUES ('$club_id', 'Блог', NOW(), 'all', 'list', 1, 'multi', 0, 0, 'club')";
                $this->inDB->query($sql);
                $id = $this->inDB->get_last_id('cms_blogs');
        }

        return $id;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClubRootAlbumId($club_id) {
        return $this->inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer='club".$club_id."'", 'id');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addClub($item, $cfg){

		$u_karma = cmsUser::getKarma($item['user_id']);

        $item = cmsCore::callEvent('ADD_CLUB', $item);

        $sql = "INSERT INTO cms_clubs (id, admin_id, title, description, imageurl, pubdate, clubtype, enabled_blogs, enabled_photos, create_karma)
                VALUES('', '{$item['user_id']}', '{$item['title']}', '', '', NOW(), '{$item['clubtype']}', '{$cfg['enabled_blogs']}', '{$cfg['enabled_photos']}', '{$u_karma}')";
        $this->inDB->query($sql);

        if($this->inDB->errno()){ return false; }

        $club_id = $this->inDB->get_last_id('cms_clubs');

        //create blog
        $blog_seolink = cmsCore::strToURL($item['title']);
        $is_exists = $this->inDB->rows_count('cms_blogs', "seolink = '{$blog_seolink}'");
        if ($is_exists) { $blog_seolink .= '-' . date("d-i"); }

        $sql = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, view_type, showcats, ownertype, premod, forall, owner, seolink)
                VALUES ('$club_id', 'Блог клуба {$item['title']}', NOW(), 'all', 'list', 1, 'multi', 0, 0, 'club', '$blog_seolink')";
        $this->inDB->query($sql);

        return $club_id;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateClubImage($club_id, $filename) {
        $sql = "UPDATE cms_clubs SET imageurl = '$filename' WHERE id=$club_id";
        $this->inDB->query($sql);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateClub($club_id, $item) {
        $item = cmsCore::callEvent('UPDATE_CLUB', $item);
        $sql = "UPDATE cms_clubs
                SET admin_id = '{$item['admin_id']}',
                    description = '{$item['description']}',
                    clubtype = '{$item['clubtype']}',
                    maxsize = '{$item['maxsize']}',
                    blog_min_karma = {$item['blog_min_karma']},
                    photo_min_karma = {$item['photo_min_karma']},
                    album_min_karma = {$item['album_min_karma']},
                    photo_premod = {$item['photo_premod']},
                    blog_premod = {$item['blog_premod']},
                    join_min_karma = {$item['join_min_karma']},
                    join_karma_limit = {$item['join_karma_limit']},
                    is_vip = '{$item['is_vip']}',
                    join_cost = '{$item['join_cost']}'
                WHERE id = $club_id";
        $this->inDB->query($sql);
        return true;
    }

    public function setVip($club_id, $is_vip, $join_cost){

        if (!$is_vip) { $is_vip = 0; }
        if (!$join_cost) { $join_cost = 0; }

        $sql = "UPDATE cms_clubs
                SET is_vip = '{$is_vip}',
                    join_cost = '{$join_cost}'
                WHERE id = $club_id";

        $this->inDB->query($sql);

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteClub($club_id) {

        $inCore = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_CLUB', $club_id);

        $sql    = "SELECT * FROM cms_clubs WHERE id = $club_id LIMIT 1";
        $result = $this->inDB->query($sql);

        if ( !$this->inDB->num_rows($result) ){ return false; }

        $club   = $this->inDB->fetch_assoc($result);

        $inCore->loadLib('clubs');
        $inCore->loadLib('tags');
        $inCore->loadModel('blogs');
        $inCore->loadModel('photos');

        $blog_model     = new cms_model_blogs();
        $photos_model   = new cms_model_photos();

        //Удаляем логотип клуба
        @unlink(PATH.'/images/clubs/'.$club['imageurl']);
        @unlink(PATH.'/images/clubs/small/'.$club['imageurl']);

        //Удаляем клуб и привязки пользователей
        $this->inDB->query("DELETE FROM cms_clubs WHERE id = $club_id");
        $this->inDB->query("DELETE FROM cms_user_clubs WHERE club_id = $club_id");

        //Удаляем блог клуба
        $blog_model->deleteBlog( $this->getClubBlogId($club_id) );

        //Удаляем фотоальбомы клуба
        $photos_model->deleteAlbum( $this->getClubRootAlbumId($club_id) );
        $this->inDB->query("DELETE FROM cms_photo_albums WHERE NSDiffer = 'club{$club_id}'");
		cmsActions::removeObjectLog('add_club', $club_id);
        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function hasUserClub(){
		
		$inUser = cmsUser::getInstance();
		
		$is_has_club = $inUser->id ? $this->inDB->get_fields('cms_clubs', "admin_id = '{$inUser->id}'", 'id, create_karma', 'id DESC') : true;

        return $is_has_club;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function canCreate($cfg, $is_mes = false){

		global $_LANG;

		$inUser = cmsUser::getInstance();
		
		// неавторизованные не могут создавать клубы
		if (!$inUser->id) { return false; }
		// администраторы могут создавать клубы
		if ($inUser->is_admin) { return true; }
		
		$is_has_club = $this->hasUserClub();

		$u_karma = cmsUser::getKarma($inUser->id);

		// клуб можно создавать каждые $cfg['every_karma']
		if ($is_has_club) {
			if (!$cfg['every_karma']) {
				
				cmsCore::addSessionMessage($_LANG['USER_HAS_ONE_CLUB'], 'error');
				return false;
			
			}
			if ($cfg['every_karma'] && ($is_has_club['create_karma'] + $cfg['every_karma']) > $u_karma) {

				$message = $_LANG['USER_HAS_CLUB'];
				$message = str_replace('%create_karma%', $is_has_club['create_karma'], $message);
				$message = str_replace('%every_karma%', $cfg['every_karma'], $message);
				$message = str_replace('%karma%', $u_karma, $message);
				$message = str_replace('%new_karma%', ($is_has_club['create_karma']+$cfg['every_karma']), $message);

				cmsCore::addSessionMessage($message, 'error');
				return false;
			
			}
		}

		if($cfg['cancreate'] && $u_karma >= $cfg['create_min_karma'] && $inUser->rating >= $cfg['create_min_rating']){
			
			return true;

		} elseif($is_mes) {
			
			if($u_karma < $cfg['create_min_karma']){
				$message = $_LANG['NEED_KARMA_TEXT_ACCESS'];
				$message = str_replace('%karma%', $u_karma, $message);
				$message = str_replace('%min_karma%', $cfg['create_min_karma'], $message);
				cmsCore::addSessionMessage($message, 'error');
			}
			if($inUser->rating < $cfg['create_min_rating']){
				$message = $_LANG['NEED_RATING_TEXT_ACCESS'];
				$message = str_replace('%rating%', $inUser->rating, $message);
				$message = str_replace('%min_rating%', $cfg['create_min_rating'], $message);
				cmsCore::addSessionMessage($message, 'error');
			}
			
		}
		
        return false;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}