<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_forum{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function getForum($id){

		$groupsql = forumUserAuthSQL();

        $sql      = "SELECT * FROM cms_forums WHERE id = $id $groupsql LIMIT 1";

        $result   = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $forum    = $this->inDB->fetch_assoc($result);

        return $forum;
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deleteThread($id, $user_id=false){

        $inCore     = cmsCore::getInstance();

        $thread_uid = $this->inDB->get_field('cms_forum_threads', "id = '$id'", 'user_id');

		if ($thread_uid){

            $can_delete = ($thread_uid == $user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate'));

			if ($can_delete){

				cmsActions::removeObjectLog('add_thread', $id);
				uploadDeleteThread($id); //forumcore.php

				$sql = "SELECT * FROM cms_forum_posts WHERE thread_id = $id";
				$rs  = $this->inDB->query($sql);

				if ($this->inDB->num_rows($rs)){
					while ($post = $this->inDB->fetch_assoc($rs)){
                        uploadDeletePost($id);
						$inCore->deleteUploadImages($post['id'], 'forum'); //forumcore.php
						cmsActions::removeObjectLog('add_fpost', $post['id']);
					}
				}

				$this->inDB->query("DELETE FROM cms_forum_posts WHERE thread_id = $id") ;
				$this->inDB->query("DELETE FROM cms_forum_polls WHERE thread_id = $id") ;
				$this->inDB->query("DELETE FROM cms_forum_threads WHERE id = $id") ;
			}
		}
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deleteAutoThread($rel_to, $rel_id){

        $thread_id  = $this->inDB->get_field('cms_forum_threads', "rel_to='{$rel_to}' AND rel_id={$rel_id}", 'id');

        if ($thread_id){
            $this->deleteThread($thread_id);
        }

        return true;
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function deletePost($id, $user_id=false){

        $inCore     = cmsCore::getInstance();

        $post_uid   = $this->inDB->get_field('cms_forum_posts', "id = '$id'", 'user_id');

		if ($post_uid){

            $can_delete = ($post_uid == $user_id || $inCore->userIsAdmin($user_id) || $inCore->isUserCan('forum/moderate'));

			if ($can_delete){
				uploadDeletePost($id); //forumcore.php
				$inCore->deleteUploadImages($id, 'forum'); //forumcore.php
				$this->inDB->query("DELETE FROM cms_forum_posts WHERE id = $id");
				cmsActions::removeObjectLog('add_fpost', $id);
			}
		}
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function addPost($post){

		$sql = "INSERT INTO cms_forum_posts (thread_id, user_id, pubdate, editdate, edittimes, content)
						VALUES ({$post['thread_id']}, '{$post['user_id']}', NOW(), NOW(), 0, '{$post['message']}')";
		$this->inDB->query($sql);
				
		$lastid = $this->inDB->get_last_id('cms_forum_posts');

        return $lastid;
    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function addThread($msg){

		$sql = "INSERT INTO cms_forum_threads (forum_id, user_id, title, description, icon, pubdate, hits, is_hidden, rel_to, rel_id)
								VALUES ('{$msg['forum_id']}', '{$msg['user_id']}', '{$msg['title']}', '{$msg['description']}', '', NOW(), 0, '{$msg['is_hidden']}', '{$msg['rel_to']}', '{$msg['rel_id']}')";
		$this->inDB->query($sql);

		$threadlastid = $this->inDB->get_last_id('cms_forum_threads');

        return $threadlastid;
    }

// ========================================================================================================= //
// ========================================================================================================= //

}