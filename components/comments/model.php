<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_comments{

    private $childs;

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

    public function addComment($item) {

        $item = cmsCore::callEvent('ADD_COMMENT', $item);
		
		$item['target_title'] =  $this->inDB->escape_string($item['target_title']);
		
        $sql = "INSERT INTO cms_comments (parent_id, user_id, target, target_id, 
                                          guestname, content, content_bbcode, pubdate,
                                          published,  target_title, target_link,
                                          ip, is_hidden)
                VALUES ({$item['parent_id']}, {$item['user_id']}, '{$item['target']}', {$item['target_id']},
                        '{$item['guestname']}', '{$item['content']}', '{$item['content_bbcode']}', NOW(),
                        {$item['published']}, '{$item['target_title']}', '{$item['target_link']}',
                        '{$item['ip']}', '{$item['is_hidden']}')";

        $this->inDB->query($sql);

        $comment_id = $this->inDB->get_last_id('cms_comments');

        return $comment_id;

    }

    public function updateComment($id, $comment) {

        if (!$id) { return false; }

        $sql = "UPDATE cms_comments
                   SET content = '{$comment['content']}',
                       content_bbcode = '{$comment['content_bbcode']}'
                 WHERE id = '{$id}'
                 LIMIT 1";

       $this->inDB->query($sql);

       return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getTargetAuthor($table, $target_id) {

        $sql = "SELECT u.id id, u.email email, p.title title
                FROM cms_users u, {$table} p
                WHERE p.user_id = u.id AND p.id = {$target_id} AND u.is_locked = 0 AND u.is_deleted = 0
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)!==1){ return false; }

        return $this->inDB->fetch_assoc($result);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentAuthorId($comment_id) {

        return $this->inDB->get_field('cms_comments', "id=$comment_id", 'user_id');

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function isAuthorNeedMail($author_id) {

        return $this->inDB->get_field('cms_user_profiles', "user_id=".$author_id, 'email_newmsg');
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    private function getCommentChilds($comment_id) {

        $sql = "SELECT id FROM cms_comments WHERE parent_id = $comment_id";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($child = $this->inDB->fetch_assoc($result)){
            $this->childs[] = $child;
            $this->getCommentChilds($child['id']);
        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteComment($comment_id) {

		$inCore     = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_COMMENT', $comment_id);

        $this->childs = array();

        $this->getCommentChilds($comment_id);

        $sql = "DELETE FROM cms_comments WHERE id = $comment_id LIMIT 1";
        $this->inDB->query($sql);

		$inCore->deleteRatings('comment', $comment_id);

        cmsActions::removeObjectLog('add_comment', $comment_id);

        if ($this->childs){
            foreach($this->childs as $child){
                $sql = "DELETE FROM cms_comments WHERE id = {$child['id']} LIMIT 1";
                $this->inDB->query($sql);
				$inCore->deleteRatings('comment', $child['id']);
                cmsActions::removeObjectLog('add_comment', $child['id']);
            }
        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentsCount($target, $target_id) {

        $sql = "SELECT id
                FROM cms_comments
                WHERE target='$target' AND target_id=$target_id AND published=1";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getComments($target, $target_id, $cfg){

        $comments = array();

        if (!$cfg['edit_minutes']) { $cfg['edit_minutes'] = 0; }

        $sql = "SELECT c.*,
                       IFNULL(v.total_rating, 0) as votes,
					   IFNULL(u.nickname, 0) as nickname,
					   IFNULL(u.login, 0) as login,
					   IFNULL(u.is_deleted, 0) as is_deleted,
					   IFNULL(p.imageurl, 0) as imageurl,
                       (NOW() < DATE_ADD(c.pubdate, INTERVAL {$cfg['edit_minutes']} MINUTE)) as is_editable
                FROM cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id = c.id AND v.target = 'comment'
				LEFT JOIN cms_users u ON u.id = c.user_id
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                WHERE c.target='$target' AND c.target_id=$target_id AND c.published=1
                ORDER BY c.pubdate ASC";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($comment = $this->inDB->fetch_assoc($result)){
			$comment['fpubdate'] = cmsCore::dateFormat($comment['pubdate'], true, true);
            $comments[] = $comment;
        }

        $comments = cmsCore::callEvent('GET_COMMENTS', $comments);

        return $comments;

    }

    public function getComment($id, $cfg) {

        $comment = array();

        if (!$cfg['edit_minutes']) { $cfg['edit_minutes'] = 0; }

        $sql = "SELECT c.*,
                       IFNULL(v.total_rating, 0) as votes,
					   IFNULL(u.nickname, 0) as nickname,
					   IFNULL(u.login, 0) as login,
					   IFNULL(u.is_deleted, 0) as is_deleted,
					   IFNULL(p.imageurl, 0) as imageurl,
                       (NOW() < DATE_ADD(c.pubdate, INTERVAL {$cfg['edit_minutes']} MINUTE)) as is_editable
                FROM cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id = c.id AND v.target = 'comment'
				LEFT JOIN cms_users u ON u.id = c.user_id
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                WHERE c.id='{$id}' AND c.published=1
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $comment = $this->inDB->fetch_assoc($result);

        $comments = cmsCore::callEvent('GET_COMMENT', $comment);

        return $comment;

    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentsAll($page=1, $perpage=10){
		global $_LANG;
        $comments = array();
		$inUser     = cmsUser::getInstance();
		$hidden_sql = $inUser->is_admin ? '' : 'AND c.is_hidden=0';

        $sql = "SELECT c.id, c.guestname, c.content, c.pubdate as fpubdate, c.target_title, c.target_link, c.ip, c.user_id,
                       IFNULL(v.total_rating, 0) as votes,
					   IFNULL(u.nickname, 0) as nickname,
					   IFNULL(u.login, 0) as login,
					   IFNULL(u.is_deleted, 0) as is_deleted,
					   IFNULL(p.imageurl, 0) as imageurl,
					   IFNULL(p.gender, 0) as gender
                FROM cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id = c.id AND v.target = 'comment'
				LEFT JOIN cms_users u ON u.id = c.user_id
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                WHERE c.published=1 {$hidden_sql}
                ORDER BY c.id DESC
				LIMIT ".(($page-1)*$perpage).", $perpage";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }
		
        while($comment = $this->inDB->fetch_assoc($result)){
			$comment['fpubdate'] = cmsCore::dateFormat($comment['fpubdate'], true, true);
			switch ($comment['gender']){
            			case 'm': 	$comment['gender'] = $_LANG['COMMENTS_MALE'];
									break;
            			case 'f':	$comment['gender'] = $_LANG['COMMENTS_FEMALE'];
									break;
						default:	$comment['gender'] = $_LANG['COMMENTS_GENDER'];
			}
            $comments[] = $comment;
        }

        $comments = cmsCore::callEvent('GET_COMMENTS', $comments);

        return $comments;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function buildTree($parent_id, $level, $comments, &$tree){
        $level++;
        foreach($comments as $num=>$comment){
            if ($comment['parent_id']==$parent_id){
                $comment['level'] = $level-1;
                $tree[] = $comment;
                $this->buildTree($comment['id'], $level, $comments, $tree);
            }
        }
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}