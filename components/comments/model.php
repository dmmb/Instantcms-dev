<?php
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

        $sql = "INSERT INTO cms_comments (parent_id, user_id, target, target_id, guestname, content, pubdate, published, target_title, target_link)
                VALUES ({$item['parent_id']}, {$item['user_id']}, '{$item['target']}', {$item['target_id']},
                        '{$item['guestname']}', '{$item['content']}', NOW(), {$item['published']},
                        '{$item['target_title']}', '{$item['target_link']}')";

        $this->inDB->query($sql);

        $comment_id = $this->inDB->get_last_id('cms_comments');

        return $comment_id;

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

        cmsCore::callEvent('DELETE_COMMENT', $comment_id);

        $this->childs = array();

        $this->getCommentChilds($comment_id);

        $sql = "DELETE FROM cms_comments WHERE id = $comment_id LIMIT 1";
        $this->inDB->query($sql);

        if ($this->childs){
            foreach($this->childs as $child){
                $sql = "DELETE FROM cms_comments WHERE id = {$child['id']} LIMIT 1";
                $this->inDB->query($sql);
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

    public function getComments($target, $target_id){

        $comments = array();

        $sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%d-%m-%Y') fpubdate, DATE_FORMAT(c.pubdate, '%H:%i') fpubtime,
                       IFNULL(v.total_rating, 0) as votes
                FROM cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id = c.id AND v.target = 'comment'
                WHERE c.target='$target' AND c.target_id=$target_id AND c.published=1
                ORDER BY c.pubdate ASC";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($comment = $this->inDB->fetch_assoc($result)){
            $comments[] = $comment;
        }

        $comments = cmsCore::callEvent('GET_COMMENTS', $comments);

        return $comments;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}