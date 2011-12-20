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

function forumPollVote(){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	$answer = $inCore->request('answer', 'html');
	$poll_id = $inCore->request('poll_id', 'int');

	if(!$answer || !$poll_id) { die(); }

	$sql = "SELECT *
			FROM cms_forum_polls
			WHERE id = '$poll_id' LIMIT 1";
	$result = $inDB->query($sql) ;
	if (!$inDB->num_rows($result)){ $inCore->redirect($_SERVER['REQUEST_URI']); return; }

	$poll=$inDB->fetch_assoc($result);		
	$answers = unserialize($poll['answers']);
	
	//SUBMIT NEW VOTE
	$answer_id = 0;
	foreach($answers as $key=>$value){
		$answer_id ++;
		if (stripslashes(urldecode($key)) == $answer){
			$answers[$key] += 1;
			break;
		}
	}
	
	//SAVE POLL DATA		
	$sql = "UPDATE cms_forum_polls SET answers = '".serialize($answers)."' WHERE id = '$poll_id'";
	$inDB->query($sql) ;
	
	//MARK USER VOTING
	$user_id = $inUser->id;
	$sql = "INSERT cms_forum_votes (poll_id, answer_id, user_id, pubdate)
			VALUES ('$poll_id', '$answer_id', '$user_id', NOW())";
	
	$inDB->query($sql) ;
		
	$inCore->redirect($_SERVER['REQUEST_URI']);
	return;
}

function forumUserVote($poll_id, $thread_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	//swith poll/results
	if ( isset($_REQUEST['viewpoll'])) { return true; }

	//guests not vote
	if (!$inUser->id){ return false;	 }

	$uid = $inUser->id;

	//user revoting event
	if (isset($_REQUEST['revote'])) {
        
		//get previous answer id
		$sql = "SELECT answer_id FROM cms_forum_votes WHERE user_id = '$uid' AND poll_id = '$poll_id'";
		$result = $inDB->query($sql);		
		$data = $inDB->fetch_assoc($result);
		$answer_id = $data['answer_id'];
		//get all poll answers
		$sql = "SELECT answers FROM cms_forum_polls WHERE id = '$poll_id'";
		$result = $inDB->query($sql);		
		$data = $inDB->fetch_assoc($result);
		$answers = unserialize($data['answers']);
		$aid = 1;
		//find and decrement previous user answer
		foreach ($answers as $key=>$value){
			if ($aid == $answer_id){
				$answers[$key]--; 
			}
			$aid++;
		}
		//update poll log
		$inDB->query("UPDATE cms_forum_polls SET answers = '".serialize($answers)."' WHERE id = '$poll_id'") ;
		//clean vote mark
		$inDB->query("DELETE FROM cms_forum_votes WHERE poll_id = '$poll_id' AND user_id = '$uid'") ;

        $inCore->redirect('/forum/thread'.$thread_id.'.html');

	}

	$sql = "SELECT id FROM cms_forum_votes WHERE user_id = '$uid' AND poll_id = '$poll_id'";
	$result = $inDB->query($sql);
	
	return $inDB->num_rows($result);
	
}

function forumUserAnswer($poll_id){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	if (!$inUser->id){ return 0; }

	$sql = "SELECT answer_id FROM cms_forum_votes WHERE user_id = '{$inUser->id}' AND poll_id = '$poll_id'";
	$result = $inDB->query($sql);

	if ($inDB->num_rows($result)){
		$data = $inDB->fetch_assoc($result);
		return $data['answer_id'];
	} else { return 0; }
}

function forumAttachedPoll($thread_id, $thread){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    global $_LANG;
	$html = '';

	if ($inCore->request('poll_id', 'int')){
		forumPollVote();
		unset($_POST);
	}

	$sql = "SELECT *, (TO_DAYS(enddate) - TO_DAYS(CURDATE())) as daysleft, DATE_FORMAT(enddate, '%d-%m-%Y') as fenddate
			FROM cms_forum_polls 
			WHERE thread_id = '$thread_id'";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$poll = $inDB->fetch_assoc($result);
		
		$answers = unserialize($poll['answers']);
				 
		$answers_title = array();
		$answers_num = array();
		$item = 1;
		foreach($answers as $key=>$value){
			$answers_title[$item] = stripslashes(urldecode($key));
			$answers_num[$item] = $value;
			$item++;
		}
		
		$opt = unserialize($poll['options']);

		$uservote = forumUserVote($poll['id'], $thread_id); //user voted already in this poll?
		
		$poll_closed = ($poll['daysleft']<0);
			
		//SHOW POLL
		$total = 0;
		$html .= '<table class="forum_poll_table" width="100%" cellspacing="2" cellpadding="5" border="0">';
		$html .= '<tr><td class="forum_poll_header" width="100%" colspan="2">';
			$html .= '<div class="forum_poll_title">'.$poll['title'].'</div>';
			$html .= '<div class="forum_poll_desc">'.$poll['description'].'</div>';			
		$html .= '</td></tr>';
		$html .= '<tr>';
			$html .= '<td class="forum_poll_data" width="30%" valign="top">';
				if (!$uservote || ($uservote && $opt['result']==2)){
					//show answers
					$html .= '<form action="" method="post">';
					$html .= '<input type="hidden" name="poll_id" value="'.$poll['id'].'" />';
					$html .= '<table class="forum_poll_answers">';
					foreach($answers_title as $key=>$value){
						  $html .= '<tr>';
						  $html .= '<td><input name="answer" id="an_'.$key.'" type="radio" value="'.htmlspecialchars($value).'"></td>';
						  $html .= '<td class="mod_poll_answer"><label for="an_'.$key.'">'.$value;
						  $html .= '</label></td>';
						  $html .= '</tr>';
						  $total += $answers_num[$key];
					  }
					 $html .= '</table>';
                     if (!$thread['closed']){
                         if (@$inUser->id && !$uservote){
                             $html .= '<div class="forum_poll_submit"><input type="submit" name="votepoll" value="'.$_LANG['VOTING'].'"></div>';
                         }
                         if (!$inUser->id){
                             $html .= '<div class="forum_poll_submit">'.$_LANG['GUESTS_NOT_VOTE'].'</div>';
                         }
                     }
					$html .= '</form>';
				} else {
					//show results
					foreach($answers_num as $key=>$value){
						$total += $value;
					}
					foreach($answers_title as $key=>$value){					
						$percent = ($total==0) ? 0 : round(($answers_num[$key] / $total) * 100);					
						$html .= '<span class="forum_poll_gauge_title">'.$value.'</span>';
						if($percent>0){
							$html .= '<table style="margin-bottom:6px" width="'.round($percent*0.8).'%"><tr><td class="forum_poll_gauge">'.$answers_num[$key].'</td></tr></table>';
						} else {
							$html .= '<table style="margin-bottom:6px" width="15"><tr><td class="forum_poll_gauge">'.$answers_num[$key].'</td></tr></table>';					
						}					
					}						
				}
			$html .= '</td>';
			$html .= '<td width="" valign="top">';
				$html .= '<div class="forum_poll_param"><strong>'.$_LANG['TOTAL_VOTES'].':</strong> '.$total.'</div>';
				$html .= '<div class="forum_poll_param"><strong>'.$_LANG['END_DATE_POOL'].':</strong> '.$poll['fenddate'].'</div>';
				
				if (!$poll_closed){
					$html .= '<div class="forum_poll_param"><strong>'.$_LANG['DAYS_FOR_END'].':</strong> '.$poll['daysleft'].'</div>';
					switch($opt['result']){
					 case 0: $html .= '<div class="forum_poll_param"><strong>'.$_LANG['RESULTS'].':</strong> '.$_LANG['AVAILABLE_FOR_ALL'].'</div>';
							 break;
					 case 1: $html .= '<div class="forum_poll_param"><strong>'.$_LANG['RESULTS'].':</strong> '.$_LANG['AVAILABLE_FOR_VOTERS'].'</div>';
							 break;
					 case 2: $html .= '<div class="forum_poll_param"><strong>'.$_LANG['RESULTS'].':</strong> '.$_LANG['AVAILABLE_AFTER_VOTE'].'</div>';
							 break;			
					}
	
					switch($opt['change']){
					 case 0: $html .= '<div class="forum_poll_param"><strong>'.$_LANG['ANSWER_CHANGING'].':</strong> '.$_LANG['PROHIBITED'].'</div>';
							 break;
					 case 1: $html .= '<div class="forum_poll_param"><strong>'.$_LANG['ANSWER_CHANGING'].':</strong> '.$_LANG['ALLOW'].'</div>';
							 break;
					}
					if(!isset($inUser->id)){
						$html .= '<div class="forum_poll_param" style="color:red">'.$_LANG['GUEST_NOT_VOTED'].'</div>';
					}
					if (!$uservote && $opt['result'] == 0){
						if (!isset($_REQUEST['viewpoll'])){										
							$html .= '<div class="forum_poll_param">[<a href="/forum/viewpoll'.$thread_id.'.html">'.$_LANG['RESULT_POOL'].'</a>]</div>';
						}					
					} else {
						 if (isset($_REQUEST['viewpoll'])) {
							$html .= '<div class="forum_poll_param">[<a href="/forum/thread'.$thread_id.'.html">'.$_LANG['DELETE_POOL'].'</a>]</div>';
						} elseif ($opt['change'] && $uservote){
							$html .= '<div class="forum_poll_param">[<a href="/forum/revote'.$thread_id.'.html">'.$_LANG['CHANGE_VOTE'].'</a>]</div>';
						}
					}					
				} else {
					$html .= '<div class="forum_poll_param" style="color:#660000"><strong>'.$_LANG['POOL_FINISHED'].'</strong></div>';
				}

				
				if ($uservote){
					$ua = forumUserAnswer($poll['id']);
					if ($ua) {
						$html .= '<div class="forum_poll_param"><strong>'.$_LANG['YOUR_ANSWER'].':</strong> '.$answers_title[$ua].'</div>';
					}
				}
								
				if (@$inUser->id && !$thread['closed']){
					$html .= '<div class="forum_poll_param">[<a href="/forum/reply'.$thread_id.'.html">'.$_LANG['COMMENT_POOL'].'</a>]</div>';
				}

			$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';				
	} else { $html .= ''; }

	return $html;
}

function forumAttachForm($cfg){
    
    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	if (!$inCore->checkContentAccess($cfg['group_access'])){ return; }

    $inPage->addHeadJS('components/forum/js/attach.js');
    global $_LANG;
	$html = '';
	
	if ($cfg['fa_size']) {	
		$html .= '<input name="MAX_FILE_SIZE" type="hidden" value="'.($cfg['fa_size'] * 1024).'"/>' . "\n";
	}
	
	$html .= '<input type="hidden" name="fa_count" value="1"/>';
	
	$html .= '<div class="forum_fa">' . "\n";
		$html .= '<div class="forum_fa_title"><a href="javascript:toggleFilesAttach()">'.$_LANG['ATTACH_FILES'].'</a></div>' . "\n";
			$html .= '<div class="forum_fa_entries" id="fa_entries">';
				$html .= '<div class="forum_fa_desc">';
					$html .= '<div><strong>'.$_LANG['MAXIMUM_FILES'].':</strong> '.$cfg['fa_max'].'</div>';
					$html .= '<div><strong>'.$_LANG['MAX_SIZE_FILE'].':</strong> '.$cfg['fa_size'].' '.$_LANG['KBITE'].'.</div>';
					$html .= '<div><strong>'.$_LANG['MUST_FILE_TYPE'].':</strong> .'.mb_strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</div>';
				$html .= '</div>';
				if ($cfg['fa_max']) { $files = $cfg['fa_max']; } else { $files = 50; }
				for($f=1; $f<=$files; $f++){
					if ($f == 1) { $style= 'display:block'; } else { $style = 'display:none'; }
					$html .= '<div id="fa_entry'.$f.'" style="'.$style.'"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
						$html .= '<tr>';
							$html .= '<td>'.$_LANG['FILE'].': </td>';
							$html .= '<td><input name="fa[]" type="file" class="forum_fa_browse" size="30" id="fa_entry_input'.$f.'" /></td>';	
							$html .= '<td><div id="fa_entry_btn'.$f.'">';
								if ($f<$files) {
									$html .= '<a href="javascript:showFaEntry('.($f+1).')" title="'.$_LANG['ADD_FILE'].'"><img src="/images/icons/plus.gif" border="0"/></a>';
								}				
								if ($f>1) {
									$html .= '<a href="javascript:hideFaEntry('.$f.')" title="'.$_LANG['HIDE_FILE'].'"><img src="/images/icons/minus.gif" border="0"/></a>';
								}				
							$html .= '</div></td>';	
						$html .= '</tr>';
					$html .= '</table></div>';
				}
			$html .= '</div>';	
	$html .= '</div>';
	
	return $html;

}

function forumPollForm($cfg){
    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	$inPage->addHeadJS('components/forum/js/attach.js');

	$html = '';
	
	$html .= '<div class="forum_fa">' . "\n";
		$html .= '<div class="forum_fa_title"><a href="javascript:togglePollsAttach()">'.$_LANG['ATTACH_POOL'].'</a></div>' . "\n";
			$html .= '<div class="forum_fa_entries" id="pa_entries">';
			$html .= '<div class="forum_fa_title" style="margin-bottom:10px">'.$_LANG['POOL_PARAMS'].'</div>' . "\n";
			//poll details
				$html .= '<div style="margin-bottom:10px"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
					$html .= '<tr>';
						$html .= '<td>'.$_LANG['QUESTION'].': </td>';
						$html .= '<td><input name="poll[title]" type="text" size="30"/></td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>'.$_LANG['COMMENT_FOR_POOL'].': </td>';
						$html .= '<td><input name="poll[desc]" type="text" size="30"/></td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>'.$_LANG['LENGTH_POOL'].': </td>';
						$html .= '<td><input name="poll[days]" type="text" size="4"/> '.$_LANG['DAYS'].'</td>';
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>'.$_LANG['SHOW_RESULT'].': </td>';
						$html .= '<td><select name="poll[result]">
						                 <option value="0">'.$_LANG['FOR_ALL_EVER'].'</option>
										 <option value="1">'.$_LANG['ONLY_VOTERS'].'</option>
										 <option value="2">'.$_LANG['ONLY_END_POOL'].'</option>
						              </select>				
							      </td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>'.$_LANG['CHANGE_VOTE_USER'].': </td>';
						$html .= '<td><select name="poll[change]">
						                 <option value="0">'.$_LANG['PROHIBITING'].'</option>
										 <option value="1">'.$_LANG['ALLOWING'].'</option>
						              </select>				
							      </td>';	
					$html .= '</tr>';
				$html .= '</table></div>';

			//answer options
			$html .= '<div class="forum_fa_title" style="margin-bottom:10px">'.$_LANG['OPTIONS_ANSWER'].'</div>' . "\n";
				for($f=1; $f<=12; $f++){
					if ($f < 5) { $style= 'display:block'; } else { $style = 'display:none'; }
					$html .= '<div id="pa_entry'.$f.'" style="'.$style.'"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
						$html .= '<tr>';
							$html .= '<td>'.$_LANG['OPTION'].' №'.$f.': </td>';
							$html .= '<td><input name="poll[answers][]" type="text" size="30" id="pa_entry_input'.$f.'" /></td>';	
							if ($f >= 4) { $style= 'display:block'; } else { $style = 'display:none'; }
							$html .= '<td><div id="pa_entry_btn'.$f.'" style="'.$style.'">';								
								if ($f<12) {
									$html .= '<a href="javascript:showPaEntry('.($f+1).')" title="'.$_LANG['ADD_OPTION'].'"><img src="/images/icons/plus.gif" border="0"/></a>';
								}				
								if ($f>2) {
									$html .= '<a href="javascript:hidePaEntry('.$f.')" title="'.$_LANG['HIDE_OPTION'].'"><img src="/images/icons/minus.gif" border="0"/></a>';
								}				
							$html .= '</div></td>';	
						$html .= '</tr>';
					$html .= '</table></div>';
				}
			$html .= '</div>';	
	$html .= '</div>';
	
	return $html;

}

function forumLastMessage($forum_id, $perpage_thread){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $_LANG;
	$html = '';
	
	$forumNS = dbGetFields('cms_forums', "id='$forum_id'", 'NSLeft, NSRight');

	$sql = "SELECT p.pubdate, p.id as post_id,
				   u.id as uid, u.nickname as author,
                   u.login as author_login, 
				   t.title as threadtitle, t.id as threadid
			FROM cms_forum_posts p
			LEFT JOIN cms_forum_threads t ON t.id = p.thread_id AND t.is_hidden=0
			INNER JOIN cms_forums f ON f.id = t.forum_id
			LEFT JOIN cms_users u ON u.id = p.user_id
			WHERE (f.NSLeft >= {$forumNS['NSLeft']} AND f.NSRight <= {$forumNS['NSRight']})
			ORDER BY p.id DESC
			LIMIT 1";
			
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);
		
		$pcount = $inDB->rows_count('cms_forum_posts', 'thread_id='.$post['threadid']);
		$lastpage = ceil($pcount / $perpage_thread);
		
		if ($lastpage==1) {
			$link = '/forum/thread'.$post['threadid'].'.html#'.$post['post_id'];
		} else {
			$link = '/forum/thread'.$post['threadid'].'-'.$lastpage.'.html#'.$post['post_id'];		
		}
	
		$html .= '<strong>'.$_LANG['IN THREAD'].': <a href="'.$link.'">'.$post['threadtitle'].'</a></strong><br/>';
		$html .= $inCore->dateFormat($post['pubdate'], true, true).' '.$_LANG['FROM'].' <a href="'.cmsUser::getProfileURL($post['author_login']).'">'.$post['author'].'</a>';
	} else { $html .= $_LANG['NOT_POSTS']; }
	
	return $html;

}

function threadLastMessage($thread_id, $thread_pages=1){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$thread_pages = ($thread_pages > 1) ? '-'.$thread_pages : '';
	$html = '';
	global $_LANG;
	$sql = "SELECT p.id, p.pubdate, p.thread_id as tr_id, u.id as uid, u.nickname as author, u.login as author_login
			FROM cms_forum_posts p
			INNER JOIN cms_users u ON u.id = p.user_id
			WHERE p.thread_id = '$thread_id'
			ORDER BY p.pubdate DESC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);
		$html = '<a href="/forum/thread'.$post['tr_id'].$thread_pages.'.html#'.$post['id'].'"><img class="last_post_img" title="'.$_LANG['GO_LAST_POST'].'" alt="'.$_LANG['GO_LAST_POST'].'" src="/templates/_default_/images/icons/anchor.png"></a> ';
		$html .= $_LANG['FROM'].' <a href="'.cmsUser::getProfileURL($post['author_login']).'">'.$post['author'].'</a><br>';
		$html .= $inCore->dateFormat($post['pubdate'], true, true);
	} else { $html .= $_LANG['NOT_POSTS']; }
	
	return $html;
}

function threadLastMessageData($thread_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$data = array();
	
	$sql = "SELECT p.id, p.pubdate, p.content as msg, u.id as uid, u.nickname as author, u.login as login
			FROM cms_forum_posts p
			INNER JOIN cms_users u ON u.id = p.user_id
			WHERE p.thread_id = '$thread_id'
			ORDER BY p.pubdate DESC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);

		$data['date']    = $inCore->dateFormat($post['pubdate']);
		$data['user']    = $post['author'];
        $data['login']   = $post['login'];
		$data['user_id'] = $post['uid'];
		$data['id']      = $post['id'];
		$data['msg']     = $post['msg'];

	} else { return false; }
	
	return $data;
}

function forumThreadAuthor($thread_id){
    $inDB   = cmsDatabase::getInstance();
	$author = array();

	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login
			FROM cms_forum_posts p, cms_users u
			WHERE p.thread_id = '$thread_id' AND p.user_id = u.id
			ORDER BY p.pubdate ASC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$item = $inDB->fetch_assoc($result);
	} else { return false; }
	
	return $item;

}

function uploadDelete($id){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.id = '$id' AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$file = $inDB->fetch_assoc($result);			
		if ($file['uid'] == $inUser->id || @$inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){		
			@unlink(PATH.'/upload/forum/post'.$file['post_id'].'/'.$file['filename']);
			@rmdir(PATH.'/upload/forum/post'.$file['post_id']);	
			$inDB->query("DELETE FROM cms_forum_files WHERE id = '$id'") ;
			return $file;
		}		
	}
	
	return;
}

function uploadDeletePost($postid){
    $inDB   = cmsDatabase::getInstance();

	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.post_id = '$postid' AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	if ($inDB->num_rows($result)){
		while($file = $inDB->fetch_assoc($result)){
			@unlink(PATH.'/upload/forum/post'.$postid.'/'.$file['filename']);
			$inDB->query("DELETE FROM cms_forum_files WHERE id = '{$file['id']}'") ;
		}
		@rmdir(PATH.'/upload/forum/post'.$postid);	
	}	
	return;
}

function uploadDeleteThread($threadid){
    $inDB   = cmsDatabase::getInstance();
	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE p.thread_id = '$threadid' AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		while($file = $inDB->fetch_assoc($result)){
			@unlink(PATH.'/upload/forum/post'.$file['post_id'].'/'.$file['filename']);
			@rmdir(PATH.'/upload/forum/post'.$file['post_id']);
			$inDB->query("DELETE FROM cms_forum_files WHERE id = '{$file['id']}'") ;
		}
	}
	
	return;
}

function forumUserAuthSQL($tablepreffix=''){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if ($inUser->id){
		if ($inCore->userIsAdmin($inUser->id)){
			$groupsql = '';
		} else {
			$ug = $_SESSION['user']['group_id'];
			$groupsql = "AND ".$tablepreffix."auth_group IN (0, $ug)";
		}
	} else {
		$groupsql = "AND ".$tablepreffix."auth_group = 0";
	}
	return $groupsql;	
}

?>
