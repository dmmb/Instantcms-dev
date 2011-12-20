<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_forum{
	
	public $abstract_array = array(); // для хранения временных данных

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }
// ============================================================================ //
// ============================================================================ //
    public function resetAbstractArray($array_param=0){

		if(!$array_param){
			$this->abstract_array = array();
		} else {
        	$this->abstract_array[$array_param] = array();
		}

    }
// ========================================================================================================= //
// ========================================================================================================= //
	public function getUserPostsCount($user_id){

		if(!isset($this->abstract_array['users_post_count'][$user_id])){

			$user_count = $this->inDB->rows_count('cms_forum_posts', "user_id = '$user_id'");
			// заносим в кеш
			$this->abstract_array['users_post_count'][$user_id] = $user_count;
			
		}

		return $this->abstract_array['users_post_count'][$user_id];
	}
// ========================================================================================================= //
// ========================================================================================================= //
	public function getUserAwardsList($user_id){

		if(!isset($this->abstract_array['users_awards'][$user_id])){

			$awards = cmsUser::getAwardsList($user_id);
			// заносим в кеш
			$this->abstract_array['users_awards'][$user_id] = $awards;
			
		}

		return $this->abstract_array['users_awards'][$user_id];
	}
// ========================================================================================================= //
// ========================================================================================================= //
	public function getForumUserRank($user_id, $messages, $ranks, $modrank=true){

		$inCore = cmsCore::getInstance();
		$inUser = cmsUser::getInstance();

		if (!$inUser->id) { return ''; }

		global $_LANG;

		if(!isset($this->abstract_array['userrank'][$user_id])){

			$userrank = '';

			if ($inCore->userIsAdmin($user_id)){
				$userrank = '<span id="admin">'.$_LANG['ADMINISTRATOR'].'</span>';
				// временное решение, чтобы два раза не проверять на админа
				$this->abstract_array['is_admin'][$user_id] = 1;
			} else {
				// временное решение, чтобы два раза не проверять на админа
				$this->abstract_array['is_admin'][$user_id] = 0;
				//rank by messages
				if(is_array($ranks)){
					foreach($ranks as $k=>$rank){
						if ($messages >= $rank['msg'] && $rank['msg'] != ''){
							$userrank = '<span id="rank">'.$rank['title'].'</span>';
						}
					}
				} else {
					$userrank = '<span id="rank">'.$_LANG['USER'].'</span>';
				}
				//check is moderator
				$rights = $this->inDB->get_fields('cms_user_groups g, cms_users u', "u.group_id = g.id AND u.id = '$user_id'", 'g.id, g.access as access');
				if (strstr($rights['access'], 'forum/moderate')){
					if ($modrank){
						$userrank .= '<span id="moder">'.$_LANG['MODER'].'</span>';
					} else {
						$userrank = '<span id="moder">'.$_LANG['MODER'].'</span>';
					}
				}
			}
			$this->abstract_array['userrank'][$user_id] = $userrank;
		}

		return $this->abstract_array['userrank'][$user_id];;
	}
// ========================================================================================================= //
// ========================================================================================================= //
	public function getPostAttachedFiles($post_id, $mypost, $showimg=false){

		$inCore = cmsCore::getInstance();
	
		global $_LANG;
	
		$graphic_ext[] = 'jpg';
		$graphic_ext[] = 'jpeg';
		$graphic_ext[] = 'gif';
		$graphic_ext[] = 'bmp';
		$graphic_ext[] = 'png';
		
		$sql = "SELECT f.*
				FROM cms_forum_files f
				WHERE f.post_id = '$post_id'";
		$result = $this->inDB->query($sql) ;
		
		if (!$this->inDB->num_rows($result)){ return ''; }

		$html .= '<div class="fa_attach">';
		$html .= '<div class="fa_attach_title">'.$_LANG['ATTACHED_FILE'].':</div>';

		while($file = $this->inDB->fetch_assoc($result)){		

			$path_parts = pathinfo($file['filename']);
			$ext = $path_parts['extension'];	

			//make link to file
			$html .= '<div class="fa_filebox">';
			$html .= '<table class="fa_file"><tr>';
			if (!in_array($ext, $graphic_ext) || (in_array($ext, $graphic_ext) && !$showimg)){
				$html .= '<td width="16">'.$inCore->fileIcon($file['filename']).'</td>';
				$html .= '<td>';
				$html .= '<a class="fa_file_link" href="/forum/download'.$file['id'].'.html">'.$file['filename'].'</a> |
							  <span class="fa_file_desc">'.round(($file['filesize']/1024),2).' '.$_LANG['KBITE'].' | '.$_LANG['DOWNLOADED'].': '.$file['hits'].'</span>';
							  
				if ($mypost){
					$html .= ' <a href="/forum/reloadfile'.$file['id'].'.html" title="'.$_LANG['RELOAD_FILE'].'"><img src="/images/icons/reload.gif" border="0"/></a>';
					$html .= ' <a href="/forum/delfile'.$file['id'].'.html" title="'.$_LANG['DELETE_FILE'].'"><img src="/images/icons/delete.gif" border="0"/></a>';
				}								  
				$html .= '</td>';
			} else {
				$html .= '<td><img src="/upload/forum/post'.$post_id.'/'.$file['filename'].'" border="1" width="160" height="120" /></td>';
				$html .= '<td>';
				$html .= '<a class="fa_file_link" href="/forum/download'.$file['id'].'.html">'.$file['filename'].'</a> | 
							  <span class="fa_file_desc">'.round(($file['filesize']/1024),2).' '.$_LANG['KBITE'].' | '.$_LANG['DOWNLOADED'].': '.$file['hits'].'</span>';
							  
				if ($mypost){
					$html .= ' <a href="/forum/reloadfile'.$file['id'].'.html" title="'.$_LANG['RELOAD_FILE'].'"><img src="/images/icons/reload.gif" border="0"/></a>';
					$html .= ' <a href="/forum/delfile'.$file['id'].'.html" title="'.$_LANG['DELETE_FILE'].'"><img src="/images/icons/delete.gif" border="0"/></a>';
				}								  
				$html .= '</td>';
			}
			$html .= '</tr></table>';
			$html .= '</div>';
					
		}	
		$html .= '</div>';
		
		return $html;
	}
// ========================================================================================================= //
// ========================================================================================================= //
    public function getForum($id){

        $sql      = "SELECT * FROM cms_forums WHERE id = '$id' LIMIT 1";

        $result   = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $forum    = $this->inDB->fetch_assoc($result);

        return $forum;
    }
// ========================================================================================================= //
// ========================================================================================================= //
    public function getThread($id){

        $sql      = "SELECT * FROM cms_forum_threads WHERE id = '$id' LIMIT 1";

        $result   = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $thread   = $this->inDB->fetch_assoc($result);

        return $thread;
    }
// ========================================================================================================= //
// ========================================================================================================= //
    public function getPost($id){

        $sql      = "SELECT * FROM cms_forum_posts WHERE id = '$id' LIMIT 1";

        $result   = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $post   = $this->inDB->fetch_assoc($result);

        return $post;
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

        $thread = $this->inDB->get_fields('cms_forum_threads', "rel_to='{$rel_to}' AND rel_id={$rel_id}", 'id, user_id');

        if ($thread['id']){
            $this->deleteThread($thread['id'], $thread['user_id']);
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

    public function getCountThreadsFromForum($left_key, $right_key) {

		$sql = "SELECT t.id
				FROM cms_forum_threads t
				INNER JOIN cms_forums f ON f.id = t.forum_id AND f.NSLeft >= '$left_key' AND f.NSRight <= '$right_key' AND f.published = 1";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }
// ========================================================================================================= //
// ========================================================================================================= //

    public function getCountPostsFromForum($left_key, $right_key) {

		$sql = "SELECT p.id
				FROM cms_forum_posts p
				INNER JOIN cms_forum_threads t ON t.id = p.thread_id
				INNER JOIN cms_forums f ON f.id = t.forum_id AND f.NSLeft >= '$left_key' AND f.NSRight <= '$right_key' AND f.published = 1";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }
// ========================================================================================================= //
// ========================================================================================================= //
	public function getForumMessages($left_key, $right_key){

		$html = '';
		global $_LANG;
		
		$count_thr = $this->getCountThreadsFromForum($left_key, $right_key);
		
		if ($count_thr){
			$html .= '<strong>'.$_LANG['THREADS'].':</strong> '.$count_thr;
		} else {
			$html .= $_LANG['NOT_THREADS'];
		}

		$count_posts = $this->getCountPostsFromForum($left_key, $right_key);

		if ($count_posts){
			$html .= '<br/><strong>'.$_LANG['MESSAGES'].':</strong> '.$count_posts;
		} else {
			$html .= '<br/><strong>'.$_LANG['MESSAGES'].':</strong> 0';
		}

		return $html;

	}

// ========================================================================================================= //
// ========================================================================================================= //

    public function checkEditTime($pubdate, $cfg_edit_minutes) {

        $now      = time();
        $date     = strtotime($pubdate);
        $diff_sec = $now - $date;
		$end_min  = $cfg_edit_minutes - round($diff_sec/60);

        return $end_min;

    }

// ========================================================================================================= //
// ========================================================================================================= //

    public function getCatSeoLink($title = '', $id = 0){

        $seolink = cmsCore::strToURL($title);

        if ($id){
            $where = ' AND id<>'.$id;
        } else {
            $where = '';
        }

        $is_exists = $this->inDB->rows_count('cms_forum_cats', "seolink='{$seolink}'".$where, 1);

        if ($is_exists) { $seolink .= '-' . $id; }

        return $seolink;

    }
// ========================================================================================================= //
// ========================================================================================================= //

}