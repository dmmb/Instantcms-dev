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

		$groupsql   = forumUserAuthSQL();

        $sql = "SELECT * FROM cms_forums WHERE id = $id $groupsql LIMIT 1";
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

        $thread_uid = $inDB->get_field('cms_forum_threads', "id = '$id'", 'user_id');

		if ($thread_uid){

            $can_delete = ($thread_uid == $user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate'));

			if ($can_delete){

				cmsActions::removeObjectLog('add_thread', $id);
				uploadDeleteThread($id); //forumcore.php

				$sql = "SELECT * FROM cms_forum_posts WHERE thread_id = $id";
				$rs  = $inDB->query($sql);

				if ($inDB->num_rows($rs)){
					while ($post = $inDB->fetch_assoc($rs)){
                        uploadDeletePost($id);
						$inCore->deleteUploadImages($post['id'], 'forum'); //forumcore.php
						cmsActions::removeObjectLog('add_fpost', $post['id']);
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

        $thread_id  = $inDB->get_field('cms_forum_threads', "rel_to='{$rel_to}' AND rel_id={$rel_id}", 'id');

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

        $post_uid  = $inDB->get_field('cms_forum_posts', "id = '$id'", 'user_id');

		if ($post_uid){

            $can_delete = ($post_uid == $user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate'));

			if ($can_delete){
				uploadDeletePost($id); //forumcore.php
				$inCore->deleteUploadImages($id, 'forum'); //forumcore.php
				$inDB->query("DELETE FROM cms_forum_posts WHERE id = $id");
				cmsActions::removeObjectLog('add_fpost', $id);
			}
		}
    }

// ========================================================================================================= //
// ========================================================================================================= //

}