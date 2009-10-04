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

//////////////////////////////////////////////////////////////////////////////
function forumMessages($forum_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$html = '';
	
	$sql = "SELECT * FROM cms_forum_threads WHERE forum_id = $forum_id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$html .= '<strong>Темы:</strong> '.$inDB->num_rows($result);
		$tsql = "SELECT id FROM cms_forum_posts WHERE ";
		$t = 0;
		while ($thread= $inDB->fetch_assoc($result)){
			if ($t > 0) { $tsql .= ' OR '; }
			$tsql .= 'thread_id = '.$thread['id'];
			$t++;
		}
		$tresult = $inDB->query($tsql) or die(mysql_error().'<br/><br/>'.$tsql);
		if ($inDB->num_rows($tresult)){
			$html .= '<br/><strong>Сообщений:</strong> '.$inDB->num_rows($tresult);
		} else { $html .= '<br/><strong>Сообщений:</strong> 0'; }
	} else { $html .= 'Нет тем'; }
	
	return $html;
}

function forumPollVote(){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if (isset($_REQUEST['answer'])){
		$answer = $_REQUEST['answer'];
		if (isset($_REQUEST['poll_id'])){
			$poll_id = $_REQUEST['poll_id'];
		} else { die(); }
	} else { die();  }
	
	if(is_numeric($poll_id)){
		$sql = "SELECT *
				FROM cms_forum_polls
				WHERE id = $poll_id
				";
		$result = $inDB->query($sql) ;
	} else { die("HACKING ATTEMPT BLOCKED!"); }
		
	if ($inDB->num_rows($result)){
		$poll=$inDB->fetch_assoc($result);		
		$answers = unserialize($poll['answers']);
		
		//SUBMIT NEW VOTE
		$answer_id = 0;
		foreach($answers as $key=>$value){
			$answer_id ++;
			if ($key == $answer){
				$answers[$key] += 1;
				break;
			}
		}
		
		//SAVE POLL DATA		
		$sql = "UPDATE cms_forum_polls SET answers = '".serialize($answers)."' WHERE id = $poll_id";
		$inDB->query($sql) ;
		
		//MARK USER VOTING
		$user_id = $inUser->id;
		$sql = "INSERT cms_forum_votes (poll_id, answer_id, user_id, pubdate)
				VALUES ('$poll_id', '$answer_id', '$user_id', NOW())";
		
		$inDB->query($sql) ;
	}
		
	header('location:'.$_SERVER['REQUEST_URI']);
	return;
}

function forumUserVote($poll_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	//swith poll/results
	if ( isset($_REQUEST['viewpoll'])) { return true; }

	//guests not vote
	if (!isset($inUser->id)){ return false;	 }

	$uid = $inUser->id;
	
	//user revoting event
	if ( isset($_REQUEST['revote'])) { 
		//get previous answer id
		$sql = "SELECT answer_id FROM cms_forum_votes WHERE user_id = $uid AND poll_id = $poll_id";
		$result = $inDB->query($sql);		
		$data = $inDB->fetch_assoc($result);
		$answer_id = $data['answer_id'];
		//get all poll answers
		$sql = "SELECT answers FROM cms_forum_polls WHERE id = $poll_id";
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
		$inDB->query("UPDATE cms_forum_polls SET answers = '".serialize($answers)."' WHERE id = $poll_id") ;
		//clean vote mark
		$inDB->query("DELETE FROM cms_forum_votes WHERE poll_id = $poll_id AND user_id = $uid") ;
		return false;
	}

	$sql = "SELECT id FROM cms_forum_votes WHERE user_id = $uid AND poll_id = $poll_id";
	$result = $inDB->query($sql);
	
	return $inDB->num_rows($result);
	
}

function forumUserAnswer($poll_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if (!isset($inUser->id)){
		return 0;		
	}
	
	$uid = $inUser->id;

	$sql = "SELECT answer_id FROM cms_forum_votes WHERE user_id = $uid AND poll_id = $poll_id";
	$result = $inDB->query($sql);
	
	if ($inDB->num_rows($result)){
		$data = $inDB->fetch_assoc($result);
		return $data['answer_id'];
	} else { return 0; }
}

function forumAttachedPoll($thread_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	global $menuid;
	$html = '';
	
	if (isset($_POST['votepoll'])){
		forumPollVote();
		unset($_POST);
	}
	
	$sql = "SELECT *, (TO_DAYS(enddate) - TO_DAYS(CURDATE())) as daysleft, DATE_FORMAT(enddate, '%d-%m-%Y') as fenddate
			FROM cms_forum_polls 
			WHERE thread_id = $thread_id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$poll = $inDB->fetch_assoc($result);
		
		$answers = unserialize($poll['answers']);
				 
		$answers_title = array();
		$answers_num = array();
		$item = 1;
		foreach($answers as $key=>$value){
			$answers_title[$item] = $key;
			$answers_num[$item] = $value;
			$item++;
		}
		
		$opt = unserialize($poll['options']);

		$uservote = forumUserVote($poll['id']); //user voted already in this poll?
		
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
					$html .= '<form action="/forum/'.$menuid.'/thread'.$thread_id.'.html" method="post">';
					$html .= '<input type="hidden" name="poll_id" value="'.$poll['id'].'" />';
					$html .= '<table class="forum_poll_answers">';
					foreach($answers_title as $key=>$value){
						  $html .= '<tr>';
						  $html .= '<td><input name="answer" type="radio" value="'.$value.'"></td>';
						  $html .= '<td class="mod_poll_answer">'.$value;
						  $html .= '</td>';
						  $html .= '</tr>';
						  $total += $answers_num[$key];
					  }
					 $html .= '</table>';
					 if (@$inUser->id && !$uservote){
						 $html .= '<div class="forum_poll_submit"><input type="submit" name="votepoll" value="Голосовать"></div>';
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
				$html .= '<div class="forum_poll_param"><strong>Всего голосов:</strong> '.$total.'</div>';
				$html .= '<div class="forum_poll_param"><strong>Дата окончания опроса:</strong> '.$poll['fenddate'].'</div>';				
				
				if (!$poll_closed){
					$html .= '<div class="forum_poll_param"><strong>Дней до окончания:</strong> '.$poll['daysleft'].'</div>';
					switch($opt['result']){
					 case 0: $html .= '<div class="forum_poll_param"><strong>Результаты:</strong> доступны для всех</div>';				
							 break;
					 case 1: $html .= '<div class="forum_poll_param"><strong>Результаты:</strong> доступны для проголосовавших</div>';				
							 break;
					 case 2: $html .= '<div class="forum_poll_param"><strong>Результаты:</strong> станут доступны после окончания опроса</div>';				
							 break;			
					}
	
					switch($opt['change']){
					 case 0: $html .= '<div class="forum_poll_param"><strong>Изменение ответа:</strong> запрещено</div>';				
							 break;
					 case 1: $html .= '<div class="forum_poll_param"><strong>Изменение ответа:</strong> разрешено</div>';				
							 break;
					}
					if(!isset($inUser->id)){
						$html .= '<div class="forum_poll_param" style="color:red">Гости не участвуют в опросах.</div>';		
					}
					if (!$uservote && $opt['result'] == 0){
						if (!isset($_REQUEST['viewpoll'])){										
							$html .= '<div class="forum_poll_param">[<a href="/forum/'.$menuid.'/viewpoll'.$thread_id.'.html">Результаты опроса</a>]</div>';
						}					
					} else {
						 if (isset($_REQUEST['viewpoll'])) {
							$html .= '<div class="forum_poll_param">[<a href="/forum/'.$menuid.'/thread'.$thread_id.'.html">Убрать результаты</a>]</div>';
						} elseif ($opt['change'] && $uservote){
							$html .= '<div class="forum_poll_param">[<a href="/forum/'.$menuid.'/revote'.$thread_id.'.html">Изменить ответ</a>]</div>';
						}
					}					
				} else {
					$html .= '<div class="forum_poll_param" style="color:#660000"><strong>Опрос закончен.</strong></div>';
				}

				
				if ($uservote){
					$ua = forumUserAnswer($poll['id']);
					if ($ua) {
						$html .= '<div class="forum_poll_param"><strong>Ваш ответ:</strong> '.$answers_title[$ua].'</div>';					
					}
				}
								
				if (@$inUser->id){
					$html .= '<div class="forum_poll_param">[<a href="/forum/'.$menuid.'/reply'.$thread_id.'.html">Комментировать опрос</a>]</div>';
				}
			$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';				
	} else { $html .= ''; }

	return $html;
}

function forumAttachedFiles($post_id, $mypost, $showimg=false){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	
	$graphic_ext[] = 'jpg';
	$graphic_ext[] = 'jpeg';
	$graphic_ext[] = 'gif';
	$graphic_ext[] = 'bmp';
	$graphic_ext[] = 'png';
	
	$sql = "SELECT f.*, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.post_id = $post_id AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$html .= '<div class="fa_attach">';
		$html .= '<div class="fa_attach_title">Прикрепленные файлы:</div>';
		while($file = $inDB->fetch_assoc($result)){		
			$filename = $file['filename'];
			$filesize = $file['filesize'];
			$path_parts = pathinfo($filename);
			$ext = $path_parts['extension'];	
			//make link to file
			$html .= '<div class="fa_filebox">';
				$html .= '<table class="fa_file"><tr>';
					if (!in_array($ext, $graphic_ext) || (in_array($ext, $graphic_ext) && !$showimg)){
						$html .= '<td width="16">'.$inCore->fileIcon($filename).'</td>';
						$html .= '<td>';
							$html .= '<a class="fa_file_link" href="/forum/'.$menuid.'/download'.$file['id'].'.html">'.$filename.'</a> | 
									  <span class="fa_file_desc">'.round(($filesize/1024),2).' Кб | Скачали: '.$file['hits'].'</span>';
									  
							if ($mypost){
								$html .= ' <a href="/forum/'.$menuid.'/reloadfile'.$file['id'].'.html" title="Перезакачать файл"><img src="/images/icons/reload.gif" border="0"/></a>';
								$html .= ' <a href="/forum/'.$menuid.'/delfile'.$file['id'].'.html" title="Удалить файл"><img src="/images/icons/delete.gif" border="0"/></a>';
							}								  
						$html .= '</td>';
					} else {
						$html .= '<td><img src="/upload/forum/post'.$post_id.'/'.$filename.'" border="1" width="160" height="120" /></td>';
						$html .= '<td>';
							$html .= '<a class="fa_file_link" href="/forum/'.$menuid.'/download'.$file['id'].'.html">'.$filename.'</a> | 
									  <span class="fa_file_desc">'.round(($filesize/1024),2).' Кб | Скачали: '.$file['hits'].'</span>';
									  
							if ($mypost){
								$html .= ' <a href="/forum/'.$menuid.'/reloadfile'.$file['id'].'.html" title="Перезакачать файл"><img src="/images/icons/reload.gif" border="0"/></a>';
								$html .= ' <a href="/forum/'.$menuid.'/delfile'.$file['id'].'.html" title="Удалить файл"><img src="/images/icons/delete.gif" border="0"/></a>';
							}								  
						$html .= '</td>';
					}
				$html .= '</tr></table>';
			$html .= '</div>';
					
		}	
		$html .= '</div>';
	}	
	
	return $html;
}

function forumAttachForm($cfg){
    
    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();

    $inPage->addHeadJS('components/forum/js/attach.js');

	$html = '';
	
	if ($cfg['fa_size']) {	
		$html .= '<input name="MAX_FILE_SIZE" type="hidden" value="'.($cfg['fa_size'] * 1024).'"/>' . "\n";
	}
	
	$html .= '<input type="hidden" name="fa_count" value="1"/>';
	
	$html .= '<div class="forum_fa">' . "\n";
		$html .= '<div class="forum_fa_title"><a href="javascript:toggleFilesAttach()">Прикрепить файлы</a></div>' . "\n";
			$html .= '<div class="forum_fa_entries" id="fa_entries">';
				$html .= '<div class="forum_fa_desc">';
					$html .= '<div><strong>Максимум файлов:</strong> '.$cfg['fa_max'].'</div>';
					$html .= '<div><strong>Максимальный размер файла:</strong> '.$cfg['fa_size'].' Кб.</div>';
					$html .= '<div><strong>Допустимые типы файлов:</strong> .'.strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</div>';												
				$html .= '</div>';
				if ($cfg['fa_max']) { $files = $cfg['fa_max']; } else { $files = 50; }
				for($f=1; $f<=$files; $f++){
					if ($f == 1) { $style= 'display:block'; } else { $style = 'display:none'; }
					$html .= '<div id="fa_entry'.$f.'" style="'.$style.'"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
						$html .= '<tr>';
							$html .= '<td>Файл: </td>';	
							$html .= '<td><input name="fa[]" type="file" class="forum_fa_browse" size="30" id="fa_entry_input'.$f.'" /></td>';	
							$html .= '<td><div id="fa_entry_btn'.$f.'">';
								if ($f<$files) {
									$html .= '<a href="javascript:showFaEntry('.($f+1).')" title="Добавить файл"><img src="/images/icons/plus.gif" border="0"/></a>';
								}				
								if ($f>1) {
									$html .= '<a href="javascript:hideFaEntry('.$f.')" title="Убрать файл"><img src="/images/icons/minus.gif" border="0"/></a>';
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

	$inPage->addHeadJS('components/forum/js/attach.js');

	$html = '';
	
	$html .= '<div class="forum_fa">' . "\n";
		$html .= '<div class="forum_fa_title"><a href="javascript:togglePollsAttach()">Прикрепить опрос</a></div>' . "\n";
			$html .= '<div class="forum_fa_entries" id="pa_entries">';
			$html .= '<div class="forum_fa_title" style="margin-bottom:10px">Параметры опроса</div>' . "\n";			
			//poll details
				$html .= '<div style="margin-bottom:10px"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
					$html .= '<tr>';
						$html .= '<td>Вопрос: </td>';	
						$html .= '<td><input name="poll[title]" type="text" size="30"/></td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>Комментарий к опросу: </td>';	
						$html .= '<td><input name="poll[desc]" type="text" size="30"/></td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>Длительность опроса: </td>';	
						$html .= '<td><input name="poll[days]" type="text" size="4"/> дней</td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>Показывать результаты: </td>';	
						$html .= '<td><select name="poll[result]">
						                 <option value="0">Всем и всегда</option>
										 <option value="1">Только проголосовавшим</option>
										 <option value="2">Только после завершения опроса</option>										 
						              </select>				
							      </td>';	
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td>Смена голоса пользователем: </td>';	
						$html .= '<td><select name="poll[change]">
						                 <option value="0">Запретить</option>
										 <option value="1">Разрешить</option>
						              </select>				
							      </td>';	
					$html .= '</tr>';
				$html .= '</table></div>';

			//answer options
			$html .= '<div class="forum_fa_title" style="margin-bottom:10px">Варианты ответов</div>' . "\n";			
				for($f=1; $f<=12; $f++){
					if ($f < 5) { $style= 'display:block'; } else { $style = 'display:none'; }
					$html .= '<div id="pa_entry'.$f.'" style="'.$style.'"><table cellspacing="0" class="forum_fa_entry" cellpadding="5">';
						$html .= '<tr>';
							$html .= '<td>Вариант №'.$f.': </td>';	
							$html .= '<td><input name="poll[answers][]" type="text" size="30" id="pa_entry_input'.$f.'" /></td>';	
							if ($f >= 4) { $style= 'display:block'; } else { $style = 'display:none'; }
							$html .= '<td><div id="pa_entry_btn'.$f.'" style="'.$style.'">';								
								if ($f<12) {
									$html .= '<a href="javascript:showPaEntry('.($f+1).')" title="Добавить вариант"><img src="/images/icons/plus.gif" border="0"/></a>';
								}				
								if ($f>2) {
									$html .= '<a href="javascript:hidePaEntry('.$f.')" title="Убрать вариант"><img src="/images/icons/minus.gif" border="0"/></a>';
								}				
							$html .= '</div></td>';	
						$html .= '</tr>';
					$html .= '</table></div>';
				}
			$html .= '</div>';	
	$html .= '</div>';
	
	return $html;

}

function forumDate($datestr, $daysleft){

	if ($daysleft == '0'){
		return '<strong>Сегодня</strong>';
	}
	if ($daysleft == '1'){
		return '<strong>Вчера</strong>';
	}
	return $datestr;

}

function forumLastMessage($forum_id, $perpage_thread){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	
	$forumNS = dbGetFields('cms_forums', 'id='.$forum_id, 'NSLeft, NSRight');

	$groupsql = forumUserAuthSQL("f.");
	
	$sql = "SELECT DATE_FORMAT(p.pubdate, '%d-%m-%Y') as pubdate, DATE_FORMAT(p.pubdate, '%H:%i') as pubtime, 
				   (TO_DAYS(CURDATE()) - TO_DAYS(p.pubdate)) as daysleft, u.id as uid, u.nickname as author,
                   u.login as author_login, 
				   t.title as threadtitle, t.id as threadid
			FROM cms_forums f, cms_forum_threads t, cms_forum_posts p, cms_users u
			WHERE (f.NSLeft >= {$forumNS['NSLeft']} AND f.NSRight <= {$forumNS['NSRight']}) AND t.forum_id = f.id AND p.thread_id = t.id AND p.user_id = u.id $groupsql
			ORDER BY p.pubdate DESC
			LIMIT 1";
			
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);
		
		$pcount = dbRowsCount('cms_forum_posts', 'thread_id='.$post['threadid']);
		$lastpage = ceil($pcount / $perpage_thread);
		
		if ($lastpage==1) {
			$link = '/forum/'.$menuid.'/thread'.$post['threadid'].'.html#new';
		} else {
			$link = '/forum/'.$menuid.'/thread'.$post['threadid'].'-'.$lastpage.'.html#new';		
		}
	
		$html .= '<strong>Последнее сообщение <br/>';
		$html .= 'в теме: <a href="'.$link.'">'.$post['threadtitle'].'</a></strong><br/>';
		$html .= forumDate($post['pubdate'], $post['daysleft']) . ' в ' .$post['pubtime'].' от <a href="'.cmsUser::getProfileURL($post['author_login']).'">'.$post['author'].'</a>';
	} else { $html .= 'Нет сообщений'; }
	
	return $html;

}

function threadLastMessage($thread_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	
	$sql = "SELECT DATE_FORMAT(p.pubdate, '%d-%m-%Y') as pubdate, DATE_FORMAT(p.pubdate, '%H:%i') as pubtime, 
				   (TO_DAYS(CURDATE()) - TO_DAYS(p.pubdate)) as daysleft, u.id as uid, u.nickname as author,
                   u.login as author_login
			FROM cms_forums f, cms_forum_threads t, cms_forum_posts p, cms_users u
			WHERE t.id = $thread_id AND p.thread_id = t.id AND p.user_id = u.id
			ORDER BY p.pubdate DESC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);
		$html .= '<strong>Последнее сообщение: </strong><br/>';
		$html .= forumDate($post['pubdate'], $post['daysleft']) . ' в ' .$post['pubtime'].' от <a href="'.cmsUser::getProfileURL($post['author_login']).'">'.$post['author'].'</a>';
	} else { $html .= 'Нет сообщений'; }
	
	return $html;
}

function threadLastMessageData($thread_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$data = array();
	
	$sql = "SELECT DATE_FORMAT(p.pubdate, '%d-%m-%Y') as pubdate, DATE_FORMAT(p.pubdate, '%H:%i') as pubtime, p.content as msg,
				   (TO_DAYS(CURDATE()) - TO_DAYS(p.pubdate)) as daysleft, u.id as uid, u.nickname as author, u.login as login
			FROM cms_forums f, cms_forum_threads t, cms_forum_posts p, cms_users u
			WHERE t.id = $thread_id AND p.thread_id = t.id AND p.user_id = u.id
			ORDER BY p.pubdate DESC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$post = $inDB->fetch_assoc($result);

		if ($post['daysleft']==0){
			$data['date'] = '<div style="text-align:center;font-weight:bold">'.$post['pubtime'].'</div>';
		} else {
			$data['date'] = '<div style="text-align:center">'.$post['pubdate'].'</div>';
		}
		
		$data['user']       = $post['author'];
        $data['login']      = $post['login'];
		$data['user_id']    = $post['uid'];
		$data['msg']        = $post['msg'];

	} else { return false; }
	
	return $data;
}


function forumUserMsgNum($user_id){
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	
	$sql = "SELECT id FROM cms_forum_posts WHERE user_id = $user_id";
	$result = $inDB->query($sql) ;
	$count = $inDB->num_rows($result);
	$html .= $count;
	
	return $html;
}

function forumThreadAuthor($thread_id){
    $inDB   = cmsDatabase::getInstance();
	$author = array();
	
	$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login
			FROM cms_forum_posts p, cms_users u
			WHERE p.thread_id = $thread_id AND p.user_id = u.id
			ORDER BY p.pubdate ASC
			LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$item = $inDB->fetch_assoc($result);
		$author = $item;
	} else { return false; }
	
	return $author;

}

function uploadDelete($menuid, $id){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.id = $id AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$file = $inDB->fetch_assoc($result);			
		if ($file['uid'] == @$inUser->id || @$inCore->userIsAdmin(@$inUser->id) || $inCore->isUserCan('forum/moderate')){		
			@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$file['post_id'].'/'.$file['filename']);
			@rmdir($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$file['post_id']);	
			$inDB->query("DELETE FROM cms_forum_files WHERE id = $id") ;
			return $file;
		}		
	}
	
	return;
}

function uploadDeletePost($menuid, $postid){
    $inDB   = cmsDatabase::getInstance();

	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.post_id = $postid AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	if ($inDB->num_rows($result)){
		while($file = $inDB->fetch_assoc($result)){
			@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$postid.'/'.$file['filename']);
			$inDB->query("DELETE FROM cms_forum_files WHERE id = ".$file['id']) ;
		}
		@rmdir($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$postid);	
	}	
	return;
}

function uploadDeleteThread($menuid, $threadid){
    $inDB   = cmsDatabase::getInstance();
	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE p.thread_id = $threadid AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		while($file = $inDB->fetch_assoc($result)){
			@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$file['post_id'].'/'.$file['filename']);
			@rmdir($_SERVER['DOCUMENT_ROOT'].'/upload/forum/post'.$file['post_id']);
			$inDB->query("DELETE FROM cms_forum_files WHERE id = ".$file['id']) ;
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
			$groupsql = "AND (".$tablepreffix."auth_group = 0 OR ".$tablepreffix."auth_group = $ug)";
		}
	} else {
		$groupsql = "AND ".$tablepreffix."auth_group = 0";
	}
	return $groupsql;	
}

function forumUserRank($uid, $messages, $ranks, $modrank=true){
    $inDB   = cmsDatabase::getInstance();
    $inCore = cmsCore::getInstance();
	$userrank = '';
	
	//check is admin
	if ($inCore->userIsAdmin($uid)){
		$userrank = '<span id="admin">Администратор</span>';
	} else {
		//rank by messages
		if(is_array($ranks)){
			foreach($ranks as $k=>$rank){
				if ($messages >= $rank['msg'] && $rank['msg'] != ''){
					$userrank = '<span id="rank">'.$rank['title'].'</span>';
				}
			}
		} else {
			$userrank = '<span id="rank">Посетитель</span>';
		}
		//check is moderator
		$rights = dbGetFields('cms_user_groups g, cms_users u', "u.group_id = g.id AND u.id = $uid", 'g.id, g.access as access');
		if (strstr($rights['access'], 'forum/moderate')){
			if ($modrank){
				$userrank .= '<span id="moder">Модератор</span>';
			} else {
				$userrank = '<span id="moder">Модератор</span>';
			}
		}
	}
	return $userrank;
}

?>
