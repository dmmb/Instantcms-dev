<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_forum{

	function __construct(){}

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function getForum($id){
        $inDB       = cmsDatabase::getInstance();

        $sql = "SELECT * FROM cms_forums WHERE id = $id";
        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return false; }

        $forum = $inDB->fetch_assoc($result);

        return $forum;
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deleteThread($id, $user_id=false){
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        global $menuid;
        $sql = "SELECT * FROM cms_forum_threads WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)>0){
			$thread = $inDB->fetch_assoc($result);
            $can_delete = ((!$user_id) || ($thread['user_id']==$user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate')));
			if ($can_delete){
				uploadDeleteThread($menuid, $id); //forumcore.php
				$sql = "SELECT * FROM cms_forum_posts WHERE thread_id = $id";
				$rs = $inDB->query($sql) ;
				if ($inDB->num_rows($rs)){
					while ($post = $inDB->fetch_assoc($rs)){
                        uploadDeletePost($menuid, $id);
						$inCore->deleteUploadImages($post['id'], 'forum'); //forumcore.php
					}
				}
				$inDB->query("DELETE FROM cms_forum_posts WHERE thread_id = $id") ;
				$inDB->query("DELETE FROM cms_forum_polls WHERE thread_id = $id") ;
				$inDB->query("DELETE FROM cms_forum_threads WHERE id = $id") ;
			}
		}
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deleteAutoThread($rel_to, $rel_id){
        $inDB       = cmsDatabase::getInstance();
        $thread_id = dbGetField('cms_forum_threads', "rel_to='{$rel_to}' AND rel_id={$rel_id}", 'id');
        if ($thread_id){
            $this->deleteThread($thread_id);
        }
        return true;
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deletePost($id, $user_id=false){
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        global $menuid;
        $sql = "SELECT * FROM cms_forum_posts WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
		if ($inDB->num_rows($result)>0){
			$msg = $inDB->fetch_assoc($result);
            $can_delete = ((!$user_id) || ($msg['user_id']==$user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate')));
			if ($can_delete){
				uploadDeletePost($menuid, $id); //forumcore.php
				$inCore->deleteUploadImages($id, 'forum'); //forumcore.php
				$inDB->query("DELETE FROM cms_forum_posts WHERE id = $id") ;
			}
		}
    }

// ========================================================================================================= //
// ========================================================================================================= //

}