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

function forumsList($selected=0){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

	$html = '';
	$html .= '<select name="goforum" id="goforum" style="width:220px; margin:0px" onchange="goForum()">';
	
	$fsql = "SELECT id, parent_id, NSLevel, title, access_list
			 FROM cms_forums 
			 WHERE published = 1
			 ORDER BY NSLeft";
	$rs_rows = $inDB->query($fsql);
	
	while($cat = $inDB->fetch_assoc($rs_rows)){
		if ($cat['parent_id']>0 && $inCore->checkContentAccess($cat['access_list'])){
			if ($selected==$cat['id']){
				$s = 'selected';
			} else {
				$s = '';
			}		
			$otstup = str_repeat('--', $cat['NSLevel']-2);
			$html .= '<option value="'.$cat['id'].'" '.$s.'>'.$otstup.' '.$cat['title'].'</option>';
		}
	}
	$html .= '</select>';
	return $html;
}

function createPoll($thread_id, $poll, $cfg){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	
	$poll_error = '';
	//title
	$title = $inCore->strClear($poll['title']);
	//description
	$desc = $poll['desc'];
	if ($desc) { $desc = $inCore->strClear($desc);	}
	//days
	$days = intval($poll['days']);
	if ($days) { $days = abs(intval($days)); }
	//options
	$options = array();
	$options['result'] = intval($poll['result']);
	$options['change'] = intval($poll['change']);	
	$str_options = serialize($options);

	$answers_title = $poll['answers'];
	$answers = array();
	foreach($answers_title as $key=>$value){
		if ($value!='') { 
			$value = urlencode($inCore->strClear($value));
			$answers[$value] = 0; 
		}
	}
	if (sizeof($answers)>1){
		$str_answers = serialize($answers);
			
		$sql = "INSERT INTO cms_forum_polls (thread_id, title, description, answers, options, enddate)
				VALUES ($thread_id, '$title', '$desc', '$str_answers', '$str_options', (NOW() + INTERVAL $days DAY))";
		$inDB->query($sql);
	} else { 
		$poll_error = '<p><strong>'.$_LANG['ERR_POLL'].':</strong> '.$_LANG['ERR_POLL_VARIANT'].'</p>';
		$poll_error .= '<p>'.$_LANG['ERR_POLL_ATTACH'].'</p>';
		$poll_error .= '<p><a href="/forum/thread'.$thread_id.'.html">'.$_LANG['CONTINUE'].'</a> &rarr;</p>';
	}
	
	return $poll_error;	
}

function uploadFiles($post_id, $cfg){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

	if (!$inCore->checkContentAccess($cfg['group_access'])){ return; }

	$file_error = false;

    foreach ($_FILES["fa"]["error"] as $key => $error) {

            if ($error == UPLOAD_ERR_OK) {

                    $tmp_name = $_FILES["fa"]["tmp_name"][$key];
                    $file     = $_FILES["fa"]["name"][$key];
                    $filesize = $inDB->escape_string($_FILES["fa"]["size"][$key]);

                    $path_parts = pathinfo($file);
                    $ext = strtolower($path_parts['extension']);
					if(strstr('php', $ext)) { return false; }
					if(strstr('htm', $ext)) { return false; }
					if(strstr('htaccess', $ext)) { return false; }
                    //check file extension is allowed
                    if (strstr(strtolower($cfg['fa_ext']), $ext)){

						$name = substr($file, 0, strrpos($file, '.'));
						$name = preg_replace ('/[^a-zA-Z0-9]/i', '', $name);
                        $file = $inDB->escape_string($name . '_' . substr(session_id(), 0, 5) . '.' . $ext);

						@mkdir(PATH."/upload/forum/post".$post_id);
                        $destination = PATH.'/upload/forum/post'.$post_id.'/'.$file;

						if ($inCore->moveUploadedFile($tmp_name, $destination, $error)) {

                        	$sql = "INSERT INTO cms_forum_files (post_id, filename, filesize, hits, pubdate)
                                	VALUES ('$post_id', '$file', '$filesize', 0, NOW())";
							$inDB->query($sql);

						} else {
					
							$file_error = true;
							
						}

                    } else { $file_error = true; }

            } elseif ($error == UPLOAD_ERR_FORM_SIZE) { $file_error = true; }
    }
    
	return $file_error;	
}

function forum(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

	$cfg = $inCore->loadComponentConfig('forum');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    $inCore->loadModel('forum');
    $model = new cms_model_forum();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    $menutitle = $inCore->menuTitle();
    global $_LANG;

    $inCore->includeFile("/components/users/includes/usercore.php");
	$inCore->includeFile("/components/forum/includes/forumcore.php");
	
	$id		= $inCore->request('id', 'int', 0);
	$do		= $inCore->request('do', 'str', 'view');
	$page	= $inCore->request('page', 'int', 1);

///////////////////////////// VIEW FORUMS LIST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	echo '<div class="float_bar"><a href="/forum/latest_posts">'.$_LANG['LATEST_POSTS'].'</a> | <a href="/forum/latest_thread">'.$_LANG['NEW_THREADS'].'</a></div>';

    $inPage->printHeading($_LANG['FORUMS']);
    $inPage->setTitle($_LANG['FORUMS']);
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.$_LANG['FORUMS'].'" href="'.HOST.'/rss/forum/all/feed.rss">');

    $groupsql = forumUserAuthSQL();
    $sql = "SELECT *
            FROM cms_forum_cats
            WHERE published = 1 $groupsql
            ORDER BY ordering";
    $result = $inDB->query($sql) ;

    if ($inDB->num_rows($result)){
		$rootid = $inDB->get_field('cms_forums', 'parent_id=0', 'id');
        while ($cat = $inDB->fetch_assoc($result)){
			echo '<table class="forums_table" width="100%" cellspacing="0" cellpadding="8" border="0" bordercolor="#999999" >';
			echo '<tr>
			  <td colspan="2" width="" class="darkBlue-LightBlue"><a href="/forum/'.$cat['seolink'].'">'.$cat['title'].'</a></td>
			  <td width="120" class="darkBlue-LightBlue">'.$_LANG['FORUM_ACT'].'</td>
			  <td width="250" class="darkBlue-LightBlue">'.$_LANG['LAST_POST'].'</td>
			</tr>';
            //FORUMS LIST IN CATEGORY
            $fsql = "SELECT *
                     FROM cms_forums
                     WHERE published = 1 AND category_id = '{$cat['id']}' AND parent_id = '$rootid'
                     ORDER BY ordering";
            $fresult = $inDB->query($fsql) ;
            if ($inDB->num_rows($fresult)){
                $row = 1;
                //print forums list in category
                while ($f = $inDB->fetch_assoc($fresult)){
					if(!$inCore->checkContentAccess($f['access_list'])) { continue; }
                    //GET SUBFORUMS LIST
                    $subforums = '';
                    $sql = "SELECT id, title, access_list
                            FROM cms_forums
                            WHERE NSLeft > {$f['NSLeft']} AND NSRight < {$f['NSRight']} AND parent_id > 0
                            ORDER BY title";
                    $rs = $inDB->query($sql);
                    $sub = $inDB->num_rows($rs);
                    if ($sub){
                        while ($sf=$inDB->fetch_assoc($rs)){
							if(!$inCore->checkContentAccess($sf['access_list'])) { continue; }
                            $subforums .= '<a href="/forum/'.$sf['id'].'">'.$sf['title'].'</a>, ';
                        }
						$subforums = rtrim($subforums, ', ');
                    } else {
                        $subforums = '';
                    }
                    //PRINT FORUM DATA
                    if ($row % 2) { $class='row11'; } else { $class = 'row2'; }
					$icon = $f['icon'] ? '/upload/forum/cat_icons/'.$f['icon'] : '/components/forum/images/forum.gif';
                    echo '<tr>';
                        echo '<td width="32" class="'.$class.'" align="center" valign="top"><img src="'.$icon.'" border="0" /></td>';
                        echo '<td width="" class="'.$class.'" align="left" valign="top">';
                            //FORUM TITLE
                            echo '<div class="forum_link"><a href="/forum/'.$f['id'].'">'.$f['title'].'</a></div>';
                            //FORUM DESCRIPTION
                            echo '<div class="forum_desc">'.$f['description'].'</div>';
                            //SUBFORUMS
                            if ($sub){
                                echo '<div class="forum_subs"><span class="forum_subs_title">'.$_LANG['SUBFORUMS'].':</span> '.$subforums.'</div>';
                            }
                        echo '</td>';
                        echo '<td class="'.$class.'" style="font-size:11px" valign="top">'.$model->getForumMessages($f['NSLeft'], $f['NSRight']).'</td>';
                        echo '<td style="font-size:11px" class="'.$class.'" valign="top">'.forumLastMessage($f['id'], $cfg['pp_thread']).'</td>';
                    echo '</tr>';
                    $row++;
                }
            } else {
                echo '<td colspan="4"><p>'.$_LANG['NOT_FORUMS_IN_CAT'].'</p></td>';
            }
			echo '</table>';
        }
    } else {
        echo '<p>'.$_LANG['NOT_CATS_OF_FORUM'].'</p>';
    }
		
}	
///////////////////////////// VIEW THREADS /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='forum'){

    $f = $model->getForum($id);
	if (!$f){ cmsCore::error404(); }
	// права доступа к форуму
	if(!$inCore->checkContentAccess($f['access_list'])) {
		$inPage->includeTemplateFile('special/accessdenied.php');
		return;
	}
        
    $inPage->setTitle($f['title']);
	$inPage->addHead('<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars($f['title']).'" href="'.HOST.'/rss/forum/'.$f['id'].'/feed.rss">');

    //PATHWAY ENTRY
    $left_key   = $f['NSLeft'];
    $right_key  = $f['NSRight'];
    $sql        = "SELECT id, title, NSLevel, access_list FROM cms_forums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 ORDER BY NSLeft";
    $rs_rows    = $inDB->query($sql);

    while($pcat = $inDB->fetch_assoc($rs_rows)){
		// если в цепочке родителей форума встречаются
		// форумы с ограниченным доступом, запрещаем доступ
		if(!$inCore->checkContentAccess($pcat['access_list'])) {
			$inPage->includeTemplateFile('special/accessdenied.php');
			return;
		}
        $inPage->addPathway($pcat['title'], '/forum/'.$pcat['id']);
    }

    // --------- Список подфорумов ---------------
    
    $subforums_sql      = "SELECT *
                           FROM cms_forums
                           WHERE published = 1 AND parent_id = '$id'
                           ORDER BY ordering";

    $subforums_result   = $inDB->query($subforums_sql);
    $subforums_count    = $inDB->num_rows($subforums_result);

    $subforums          = array();

    if ($subforums_count){
        while ($subforum = $inDB->fetch_assoc($subforums_result)){

			if(!$inCore->checkContentAccess($subforum['access_list'])) { continue; }

            $inner_forums = '';

            $inner_sql    = "SELECT id, title, access_list FROM cms_forums WHERE parent_id = '{$subforum['id']}' ORDER BY title";
            $inner_result = $inDB->query($inner_sql);
            $inner_count  = $inDB->num_rows($inner_result);

            if ($inner_count){
                while ($sf=$inDB->fetch_assoc($inner_result)){
					if(!$inCore->checkContentAccess($sf['access_list'])) { continue; }
                    $inner_forums .= '<a href="/forum/'.$sf['id'].'">'.$sf['title'].'</a>, ';
                }
				$inner_forums = rtrim($inner_forums, ', ');
            }

            $subforum['subforums']      = $inner_forums;
            $subforum['messages']       = $model->getForumMessages($subforum['NSLeft'], $subforum['NSRight']);
            $subforum['last_message']   = forumLastMessage($subforum['id'], $cfg['pp_thread']);
            $subforums[]                = $subforum;

        }        
    }

    // ------ Список тем форума -------

    $perpage = $cfg['pp_forum'] ? $cfg['pp_forum'] : 25;

    // считаем количество тем и делим на страницы
    $threads_count          = $inDB->rows_count('cms_forum_threads', "forum_id = {$f['id']}");
    $threads_page_select    = '';

    if ($inUser->id){
        $logdate = $_SESSION['user']['logdate'];
    } else {
        $logdate = '9999-01-01 12:00:00';
    }

    $tsql = "SELECT t.*, COUNT(p.id) as postsnum, IF(t.pubdate > '$logdate', 1, 0) as is_new, u.nickname, u.login
             FROM cms_forum_threads t
			 INNER JOIN cms_forum_posts p ON p.thread_id = t.id
			 LEFT JOIN cms_users u ON u.id = t.user_id
             WHERE t.forum_id = '{$f['id']}'
             GROUP BY t.id
             ORDER BY t.pinned DESC, t.pubdate DESC
             LIMIT ".(($page-1)*$perpage).", $perpage";

    $tresult = $inDB->query($tsql) ;

    $threads = array();

    if ($inDB->num_rows($tresult)){

        while ($thread = $inDB->fetch_assoc($tresult)){

            $thread['author']       = array('nickname'=>$thread['nickname'], 'login'=>$thread['login']);
            $thread['pages']        = ceil($thread['postsnum'] / $cfg['pp_thread']);
            $thread['answers']      = $thread['postsnum']-1;
            $thread['last_message'] = threadLastMessage($thread['id'], $thread['pages']);

            $threads[]              = $thread;

        }

       $pagination = cmsPage::getPagebar($threads_count, $page, $perpage, '/forum/%forum_id%-%page%', array('forum_id'=>$id));
        
    }

    $smarty = $inCore->initSmarty('components', 'com_forum_view.tpl');
    $smarty->assign('forum', $f);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('threads_count', $threads_count);
    $smarty->assign('threads', $threads);
    $smarty->assign('page', $page);
    $smarty->assign('perpage', $perpage);
    $smarty->assign('pagination', $pagination);
    $smarty->assign('user_id', $inUser->id);
    $smarty->assign('subforums_count', $subforums_count);
    $smarty->assign('subforums', $subforums);
    $smarty->display('com_forum_view.tpl');

}
///////////////////////////// READ THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='thread'){

	$sql = "SELECT t.*, f.title as forum, f.id as fid, f.NSLeft as forum_left, f.NSRight as forum_right, f.access_list
			FROM cms_forum_threads t
			INNER JOIN cms_forums f ON f.id = t.forum_id
			WHERE t.id = '$id' LIMIT 1";
	$result = $inDB->query($sql);
	if (!$inDB->num_rows($result)){ cmsCore::error404(); }

	$t = $inDB->fetch_assoc($result);
	if(!$inCore->checkContentAccess($t['access_list'])) {
		$inPage->includeTemplateFile('special/accessdenied.php');
		return;
	}
	$t = cmsCore::callEvent('GET_FORUM_THREAD', $t);

	$mythread = ($inUser->id==$t['user_id']);
	$is_admin = $inUser->is_admin;
	$is_adm   = $inCore->userIsAdmin($inUser->id);
	$is_moder = $inCore->isUserCan('forum/moderate');
	
	$inDB->query("UPDATE cms_forum_threads SET hits = hits + 1 WHERE id = ".$t['id']) ;
	
	$inPage->setTitle($t['title']);
	$inPage->setDescription($t['title']);
	$inPage->printHeading($t['title']);

	$inPage->addHeadJS('components/forum/js/common.js');

	//Calculate posts count
	$csql = "SELECT id FROM cms_forum_posts WHERE thread_id = '{$t['id']}'";
	$cres = $inDB->query($csql);			
	$posts_count = $inDB->num_rows($cres);

	$perpage=$cfg['pp_thread'];
	
	$lastpage = ceil($posts_count / $perpage);

	if ($inCore->request('go_last_post', 'int', 0)){
		$inCore->redirect('/forum/thread'.$t['id'].'-'.$lastpage.'.html#new');
	}

	//TOOLBAR TABLE		
	ob_start();
	if($t['description']){
		echo '<div class="forum_toolbar" style="padding:5px;">'.$t['description'].'</div>';
	}
	echo '<table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar"><tr>';

	if ($posts_count){
		echo '<td width="5">&nbsp;</td>';
	} else {
		echo '<td width=="">&nbsp;</td>';
	}
	$toolbar_pages = ob_get_clean();

	ob_start();
	if($inUser->id){
		echo '<td class="forum_toollinks">';
		echo '<table cellspacing="2" cellpadding="2" align="right"><tr>';
		if (!$t['closed']){
			echo '<td width="16"><img src="/components/forum/images/toolbar/newpost.gif"/></td>
				  <td><a href="/forum/reply'.$t['id'].'.html"><strong>'.$_LANG['NEW_MESSAGE'].'</strong></a></td>';
		} else {
			echo '<td><strong>'.$_LANG['THREAD_CLOSE'].'</td>';
		}
		
		if($is_admin || $is_moder) { 
			if (!$t['closed']){
				echo '<td width="16"><img src="/components/forum/images/toolbar/closethread.gif"/></td>
					  <td><a href="/forum/closethread'.$t['id'].'.html">'.$_LANG['CLOSE'].'</a></td>';
			} else {
				echo '<td width="16"><img src="/components/forum/images/toolbar/openthread.gif"/></td>
					  <td><a href="/forum/openthread'.$t['id'].'.html">'.$_LANG['OPEN'].'</a></td>';
			}
		}
		if(usrCheckAuth()) {
			if (!cmsUser::isSubscribed($inUser->id, 'forum', $t['id'])){
					echo '<td width="16"><img src="/components/forum/images/toolbar/subscribe.gif"/></td>
						  <td><a href="/forum/subscribe'.$t['id'].'.html">'.$_LANG['SUBSCRIBE_THEME'].'</a></td>';
			} else {
				echo '<td width="16"><img src="/components/forum/images/toolbar/unsubscribe.gif"/></td>
					  <td><a href="/forum/unsubscribe'.$t['id'].'.html">'.$_LANG['UNSUBSCRIBE'].'</a></td>';
			}
			if ($is_admin || $is_moder){					
				if (!$t['pinned']){
					echo '<td width="16"><img src="/components/forum/images/toolbar/pinthread.gif"/></td>
						  <td><a href="/forum/pinthread'.$t['id'].'.html">'.$_LANG['PIN'].'</a></td>';
				} else {
					echo '<td width="16"><img src="/components/forum/images/toolbar/unpinthread.gif"/></td>
						  <td><a href="/forum/unpinthread'.$t['id'].'.html">'.$_LANG['UNPIN'].'</a></td>';
				}
				echo '<td width="16"><img src="/components/forum/images/toolbar/movethread.gif"/></td>
					  <td><a href="/forum/movethread'.$t['id'].'.html">'.$_LANG['MOVE'].'</a></td>';
				echo '<td width="16"><img src="/components/forum/images/toolbar/edit.gif"/></td>
					  <td><a href="/forum/renamethread'.$t['id'].'.html">'.$_LANG['RENAME'].'</a></td>';
			}
			if ($is_adm || $mythread){
				echo '<td width="16"><img src="/components/forum/images/toolbar/delete.gif"/></td>
					  <td><a href="javascript:deleteThread(\'/forum/deletethread'.$t['id'].'.html\')">'.$_LANG['DELETE'].'</a></td>';
			}
		}
		echo '<td width="16"><img src="/components/forum/images/toolbar/back.gif"/></td>
			  <td><a href="/forum/'.$t['fid'].'">'.$_LANG['BACKB'].'</a></td>';
		echo '</tr></table>';
		echo '</td>';
	} else { echo '<td>&nbsp;</td>'; }
	
	echo '</tr></table>';
	$toolbar = ob_get_clean();

	//BUILD PAGE
	//PATHWAY ENTRY
	$left_key = $t['forum_left'];
	$right_key = $t['forum_right'];
	$sql = "SELECT id, title, NSLevel, access_list FROM cms_forums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 ORDER BY NSLeft";
	$rs_rows = $inDB->query($sql);
	while($pcat=$inDB->fetch_assoc($rs_rows)){
		// если в цепочке родителей форума встречаются
		// форумы с ограниченным доступом, запрещаем доступ
		if(!$inCore->checkContentAccess($pcat['access_list'])) {
			$inPage->includeTemplateFile('special/accessdenied.php');
			return;
		}
		$inPage->addPathway($pcat['title'], '/forum/'.$pcat['id']);
	}			
	$inPage->addPathway($t['title'], '/forum/thread'.$t['id'].'.html');
	
	$psql = "SELECT p.*, u.id as uid, u.nickname author, u.login author_login, u.is_deleted deleted, up.imageurl imageurl, up.signature signature
			 FROM cms_forum_posts p
			 LEFT JOIN cms_users u ON u.id = p.user_id
			 LEFT JOIN cms_user_profiles up ON up.user_id = u.id
			 WHERE p.thread_id = '$id'
			 ORDER BY p.pubdate ASC
			 LIMIT ".(($page-1)*$perpage).", $perpage";
			 
	$presult = $inDB->query($psql);
	if ($inDB->num_rows($presult)){

		$num = (($page-1)*$perpage)+1;

		$posts = array();

		while ($p = $inDB->fetch_assoc($presult)){
			$p['content'] = $inCore->parseSmiles($p['content'], true);
			$p['content'] = str_replace("&amp;", '&', $p['content']);
			$posts[] = $p;
		}

		$posts = cmsCore::callEvent('GET_FORUM_POSTS', $posts);

		//print posts in thread
		echo $toolbar_pages . $toolbar;

		//ATTACHED POLL
		echo forumAttachedPoll($id, $t);

		//THREAD MESSAGES LIST
		echo '<table class="posts_table" width="100%" cellspacing="2" cellpadding="5" border="0" bordercolor="#999999">';

		foreach ($posts as $p){
			$mypost = $inUser->id ? ($inUser->id==$p['user_id'] || $is_adm) : false;
			$user_messages = $model->getUserPostsCount($p['uid']);
			echo '<tr class="posts_table_tr">';
				//user column
				echo '<td class="post_usercell" width="140" align="center" valign="top" height="150">';
					echo '<div><a class="post_userlink" href="javascript:addNickname(\''.htmlspecialchars($p['author']).'\');" title="'.$_LANG['ADD_NICKNAME'].'"/>'.$p['author'].'</a></div>';
												
					echo '<div class="post_userrank">'.$model->getForumUserRank($p['uid'], $user_messages, $cfg['ranks'], $cfg['modrank']).'</div>';
				
					echo '<div class="post_userimg">';
						echo '<a href="'.cmsUser::getProfileURL($p['author_login']).'" title="'.$_LANG['GOTO_PROFILE'].'">'.usrImageNOdb($p['uid'], 'small', $p['imageurl'], $p['is_deleted']).'</a>';
						
						$awards = $model->getUserAwardsList($p['uid']);
						if ($awards){
							echo '<div class="post_userawards">'; 
								foreach($awards as $uid=>$title){
									echo '<img src="/images/icons/award.gif" border="0" alt="'.htmlspecialchars($title).'" title="'.htmlspecialchars($title).'"/>';
								}
							echo '</div>';
						}
					echo '</div>';
					
					echo '<div class="post_usermsgcnt">'.$_LANG['MESSAGES'].': '.$user_messages.'</div>';
					if($inUser->isOnline($p['uid'])){
						echo '<span class="online" style="font-size:10px;">'.$_LANG['ONLINE'].'</span>';
					}
												
				echo '</td>';
				//message column
				echo '<td width="" class="post_msgcell" align="left" valign="top">';
					echo '<a name="'.$p['id'].'"></a>';
					//date & actions
					echo '<table width="100%" class="post_date"><tr>';								
					echo '<td><strong>#'.$num.'</strong> - '.$inCore->dateFormat($p['pubdate'], true, true).'</td>';
					echo '<td align="right">';
					if (usrCheckAuth() && !$t['closed']){ 
						echo '<table cellpadding="1" cellspacing="2" border="0" class="msg_links">
								<tr>';

								echo '<td width="15"><img src="/components/forum/images/toolbar/post-quote.gif"/></td>
									  <td><a href="javascript:addQuoteText(\''.$p['author'].'\')" title="'.$_LANG['ADD_SELECTED_QUOTE'].'">'.$_LANG['ADD_QUOTE_TEXT'].'</a></td>';
								echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-reply.gif"/></td>';
								echo '<td><a href="/forum/thread'.$t['id'].'-quote'.$p['id'].'.html" title="'.$_LANG['REPLY_FULL_QUOTE'].'">'.$_LANG['REPLY'].'</a></td>';

$end_min = $model->checkEditTime($p['pubdate'], $cfg['edit_minutes']);

if (($is_moder && !$model->abstract_array['is_admin'][$p['uid']]) || $inUser->is_admin || ($mypost && ($end_min>0 && $cfg['edit_minutes']>0)) || ($mypost && !$cfg['edit_minutes'])){
	echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-edit.gif"/></td>
		  <td><a href="/forum/editpost'.$p['id'].'.html">'.$_LANG['EDIT'].'</a></td>';
	if ($num > 1){
		echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-delete.gif"/></td>
			  <td><a href="/forum/deletepost'.$p['id'].'.html">'.$_LANG['DELETE'].'</a></td>';
	}
}
						echo '</tr></table>';
					}
					
					echo '</td></tr></table>';
					
					echo '<div class="post_content">'.$p['content'].'</div>';
											
					if ($cfg['fa_on']){
						echo $model->getPostAttachedFiles($p['id'], $mypost, @$cfg['showimg']);
					}
					//edit details
					if ($p['edittimes']){
						echo '<div class="post_editdate">'.$_LANG['EDITED'].': '.$p['edittimes'].' '.$_LANG['COUNT'].' ('.$_LANG['LAST_EDIT'].': '.$inCore->dateFormat($p['editdate'], true, true).')</div>';
					}
					//user signature
					if ($p['signature']){
						echo '<div class="post_signature">'.$inCore->parseSmiles($p['signature'], true).'</div>';
					}
				echo '</td>';
			echo '</tr>';
			$num++;
		}
		$model->resetAbstractArray();
		echo '</table>';
		
		//BOTTOM TOOLBAR
		if ($page == $lastpage) { echo '<a name="new"></a>'; }
		echo '<table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar"><tr><td><a href="#">'.$_LANG['GOTO_BEGIN_PAGE'].'</a></td>'. $toolbar;
		
		//NAV BAR
		$previd = $inDB->get_fields('cms_forum_threads', 'id<'.$t['id'].' AND forum_id = '.$t['forum_id'], 'id, title', 'id DESC');
		$nextid = $inDB->get_fields('cms_forum_threads', 'id>'.$t['id'].' AND forum_id = '.$t['forum_id'], 'id, title', 'id ASC');			
		
		echo '<div class="forum_navbar">';
			echo '<table width="100%"><tr>';
				echo '<td align="left">';
					echo '<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto"><tr>';
						if ($previd){
							echo '<td align="right" width="">';
								echo '<div>&larr; <a href="/forum/thread'.$previd['id'].'.html">'.$_LANG['PREVIOUS_THREAD'].'</a></div>';
							echo '</td>';
						}
						if ($previd && $nextid) { echo '<td>|</td>'; }
						if ($nextid){
							echo '<td align="left" width="">';
								echo '<div><a href="/forum/thread'.$nextid['id'].'.html">'.$_LANG['NEXT_THREAD'].'</a> &rarr;</div>';
							echo '</td>';
						}			
					echo '</tr></table>';				
				echo '</td>';
				echo '<td width="150" align="right">'.$_LANG['GOTO_FORUM'].': </td>';
				echo '<td width="220" align="right">';			
					echo forumsList($t['forum_id']);
				echo '</td>';
			echo '</tr></table>';											
		echo '</div>';
		
		//PAGINATION
		$total = $inDB->rows_count('cms_forum_posts', 'thread_id='.$id);

		echo cmsPage::getPagebar($total, $page, $perpage, '/forum/thread'.$id.'-%page%.html');
		
		//FAST ANSWER FORM
		if (!isset($cfg['fast_on'])) { $cfg['fast_on'] = 1; }				
		if (!isset($cfg['fast_bb'])) { $cfg['fast_bb'] = 1; }				
		if ($cfg['fast_on'] && !$t['closed']){
			echo '<div class="forum_fast">';
				echo '<div class="forum_fast_header">'.$_LANG['FAST_ANSWER'].'</div>';
				if ($inUser->id){
					if ($cfg['fast_bb']){
						$inPage->addHeadJS('core/js/smiles.js');
						echo '<div class="usr_msg_bbcodebox">';
							echo cmsPage::getBBCodeToolbar('message', true);
						echo '</div>';
						echo cmsPage::getSmilesPanel('message');
					}
					echo '<div class="forum_fast_form">';
						echo '<form action="/forum/reply'.$id.'.html" method="post">';
							echo '<textarea id="message" name="message" rows="5"></textarea>';
							echo '<div class="forum_fast_submit" style="float:right;padding:5px;"><input type="submit" name="gosend" value="'.$_LANG['SEND'].'"/></div>';
							if ($mythread || $is_admin){
								echo '<div style="float:right;padding:8px;">
										<label><input type="checkbox" name="fixed" value="1" /> '.$_LANG['TOPIC_FIXED_LABEL'].'</label>
									  </div>';
							}
						echo '</form>';
					echo '</div>';
				} else {
					echo '<div style="padding:5px">'.$_LANG['FOR_WRITE_ON_FORUM'].', <a href="/registration">'.$_LANG['REGISTER'].'</a> '.$_LANG['OR_LOGIN'].'.</div>';
				}
			echo '</div>';
		}
		echo '<script type="text/javascript" language="JavaScript">
				$(document).ready(function(){
					$(\'.posts_table_tr .msg_links\').css({opacity:0.4, filter:\'alpha(opacity=40)\'});
					$(\'.posts_table_tr\').hover(
						function() {
							$(this).find(\'.msg_links\').css({opacity:1.0, filter:\'alpha(opacity=100)\'});
						},
						function() {
							$(this).find(\'.msg_links\').css({opacity:0.4, filter:\'alpha(opacity=40)\'});
						}
					);
				});
		</script>';
	} else {
		echo '<p>'.$_LANG['NO_MESS_IN_THREAD'].'</p>';
	}					
}
///////////////////////////// NEW THREAD / POST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='newthread' || $do=='newpost' || $do=='editpost'){

	if (!$inUser->id){ cmsUser::goToLogin(); }

	$inPage->addHeadJS('core/js/smiles.js');
	
	// новая тема
	if ($do == 'newthread') {

		$forum = $model->getForum($id);
		if(!$forum) { cmsCore::error404(); }
		// права доступа к форуму
		if(!$inCore->checkContentAccess($forum['access_list'])) {
			$inPage->includeTemplateFile('special/accessdenied.php');
			return;
		}

		if (IS_BILLING && $forum['topic_cost']){
			cmsBilling::checkBalance('forum', 'add_thread', false, $forum['topic_cost']);
		}

		$inPage->setTitle($_LANG['NEW_THREAD']);
		$inPage->addPathway($forum['title'], '/forum/'.$forum['id']);
		$inPage->addPathway($_LANG['NEW_THREAD']);
		echo '<div class="con_heading">'.$_LANG['NEW_THREAD'].'</div>';

	}
	// новый пост
	if ($do == 'newpost'){

		// Получаем тему
		$t = $model->getThread($id);
		if(!$t) { cmsCore::error404(); }
		if($t['closed'] == 1) { cmsCore::error404(); }

		// Получаем форум
		$forum = $model->getForum($t['forum_id']);
		// права доступа к форуму
		if(!$inCore->checkContentAccess($forum['access_list'])) {
			$inPage->includeTemplateFile('special/accessdenied.php');
			return;
		}

		$inPage->setTitle($_LANG['NEW_POST']);
		$inPage->addPathway($forum['title'], '/forum/'.$forum['id']);
		$inPage->addPathway($t['title'], '/forum/thread'.$t['id'].'.html');
		$inPage->addPathway($_LANG['NEW_POST']);

		echo '<div class="con_heading">'.$_LANG['NEW_POST'].'</div>';
		echo '<div style="margin-bottom:10px">
				<strong>'.$_LANG['THREAD'].': </strong><a href="/forum/thread'.$t['id'].'.html">'.$t['title'].'</a>
			  </div>';
		$is_topic_starter = ($t['user_id'] == $inUser->id);

	}
	// редактирование поста
	if ($do == 'editpost'){	

		$msg = $model->getPost($id);
		if(!$msg) { cmsCore::error404(); }
		if(!$inUser->is_admin && !$inCore->isUserCan('forum/moderate') && $inUser->id != $msg['user_id']) { cmsCore::error404(); }

		// Получаем тему
		$t = $model->getThread($msg['thread_id']);
		if(!$t) { cmsCore::error404(); }
		if($t['closed'] == 1) { cmsCore::error404(); }

		if(!$inUser->is_admin && !$inCore->isUserCan('forum/moderate') && $cfg['edit_minutes']){

			$end_min  = $model->checkEditTime($msg['pubdate'], $cfg['edit_minutes']);

			if($cfg['edit_minutes'] == -1 || $end_min <= 0){
				cmsCore::addSessionMessage($_LANG['EDIT_IS_DISABLE'], 'error');
				$inCore->redirectBack();
			} else {
				$msg_minute = str_replace('{min}', cmsCore::spellCount($end_min, $_LANG['MINUTE1'], $_LANG['MINUTE2'], $_LANG['MINUTE10']), $_LANG['EDIT_INFO']);
				cmsCore::addSessionMessage($msg_minute, 'info');
			}
		}
		$inPage->setTitle($_LANG['EDIT_POST']);
		$inPage->addPathway($_LANG['EDIT_POST']);
		echo '<div class="con_heading">'.$_LANG['EDIT_POST'].'</div>';
		$oldmsg = $msg['content'];	

	}
		
	$replyid = $inCore->request('replyid', 'int', 0);

	if(!isset($_POST['gosend'])){
				
		$inDB->query("DELETE FROM cms_upload_images WHERE session_id='".session_id()."'");
	
		if ($replyid){
			$sql = "SELECT p.*, u.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y в %H:%i') senddate
					FROM cms_forum_posts p, cms_users u
					WHERE p.id = '$replyid' AND p.user_id = u.id";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)>0){
				$msg = $inDB->fetch_assoc($result);
				$oldmsg = '[quote='.$msg['nickname'].']'."\r\n".
							strip_tags(str_replace('<br/>', "\n", $msg['content']))."\r\n".
						  '[/quote]'."\r\n\r\n";
			}
		}
		
		echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST" name="msgform" id="msgform" enctype="multipart/form-data">';
		echo '<table width="100%" cellpadding="0" cellspacing="0"><tr><td>';

		if ($do == 'newthread') {
			echo '<input type="hidden" name="forum_id" value="'.$id.'" />'; 
			echo '<div class="forum_postinfo"><table width="100%">';
				echo '<tr>';
					echo '<td width="150">'.$_LANG['THREAD_TITLE'].':</td>';
					echo '<td width=""><input type="text" name="title" size="30"/></td>';								
				echo '</tr>';
				echo '<tr>';
					echo '<td width="">'.$_LANG['THREAD_DESCRIPTION'].':</td>';
					echo '<td width=""><input type="text" name="description" size="50"/></td>';								
				echo '</tr>';
			echo '</table></div>';
		}		

		echo '<div class="usr_msg_bbcodebox">';
			if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; }
			echo cmsPage::getBBCodeToolbar('message', $cfg['img_on'], 'forum');
		echo '</div>';

		echo cmsPage::getSmilesPanel('message');
		
		$inCore->initAutoGrowText('#message');
		echo '<div><textarea class="ajax_autogrowarea" name="message" id="message">'.(@$oldmsg).'</textarea></div>';						

		if (($do=='newpost' || $do=='newthread') && $cfg['fa_on']) { //File attach form
			echo forumAttachForm($cfg);
		}

		if ($do=='newthread') { //Polls attach form
			echo forumPollForm($cfg);
		}

		if (($do=='newpost' && !cmsUser::isSubscribed($inUser->id, 'forum', @$id)) || ($do=='newthread')){
			echo '<div style="margin-top:16px;"><label><input name="subscribe" type="checkbox" value="1" /> '.$_LANG['SUBSCRIBE_THREAD'].'</label></div>';
		}

		if ($do=='newpost' && ($inUser->is_admin || $is_topic_starter)){
			echo '<div style="margin-top:3px;margin-bottom:15px"><label><input name="fixed" type="checkbox" value="1" /> '.$_LANG['TOPIC_FIXED_LABEL'].'</label></div>';
		}
		
		echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="'.$_LANG['SEND'].'" style="font-size:18px"/> ';
		echo '<input type="button" name="gosend" value="'.$_LANG['CANCEL'].'" style="font-size:18px" onclick="window.history.go(-1)"/></div>';
		echo '</td>';	
						
		echo '</tr></table>';
		

		echo '</form>';
		
	} else {

		$message_post = $inCore->request('message', 'html');
		$message      = $inCore->badTagClear($message_post);
		$message      = $inDB->escape_string($message_post);                
		if (!$message) { echo '<p>'.$_LANG['NEED_TEXT_POST'].'</p>'; return; }

		if($do=='newpost'){												
			//NEW POST
			//insert new post
			$lastid = $model->addPost(array(
					'thread_id' => $id,
					'user_id' => $inUser->id,
					'message' => $message
				));

			$is_topic_starter = ($t['user_id'] == $inUser->id);

			$is_fixed = $inCore->request('fixed', 'int', 0);

			if ($is_fixed && ($inUser->is_admin || $is_topic_starter)){
				$inDB->query("UPDATE cms_forum_threads SET title = CONCAT('{$_LANG['TOPIC_FIXED_PREFIX']} ', title), closed=1 WHERE id = '$id'");
			}

			cmsUser::checkAwards($inUser->id);

			$inCore->registerUploadImages(session_id(), $lastid, 'forum');
										
			//refresh forum thread
			$sql = "UPDATE cms_forum_threads SET pubdate = NOW() WHERE id = '$id'";
			$inDB->query($sql);
			//upload attached files
			$file_error = false;
			if ($cfg['fa_on'] && $_FILES['fa']) {                    
				$file_error = uploadFiles($lastid, $cfg);		
			}
			//subscribe thread
			if ($_POST['subscribe']){
				cmsUser::subscribe($inUser->id, 'forum', $id);
			}
			cmsUser::sendUpdateNotify('forum', $id);

			if ($file_error){
				$err_msg = $_LANG['ERR_UPLOAD_FILE'].'<div><strong>'.$_LANG['UPLOAD_MAXSIZE'].':</strong> '.$cfg['fa_size'].' '.$_LANG['KBITE'].'.</div><div><strong>'.$_LANG['UPLOAD_FILETYPE'].':</strong> .'.strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</div><p>'.$_LANG['NOT_ALL_FILE_ATTACH'].'</p>';
				cmsCore::addSessionMessage($err_msg, 'error');
			}

			$posts_in_thread = $inDB->rows_count('cms_forum_posts', 'thread_id='.$id);
			$pages = ceil($posts_in_thread / $cfg['pp_thread']);
			if (!$t['is_hidden']){
				//регистрируем событие
				$message_post = $inCore->parseSmiles($message_post, true);
				$message_post = strip_tags($message_post);
				cmsActions::log('add_fpost', array(
					'object' => 'пост',
					'object_url' => '/forum/thread'.$id.'-'.$pages.'.html#'.$lastid,
					'object_id' => $lastid,
					'target' => $t['title'],
					'target_url' => '/forum/thread'.$id.'.html',
					'target_id' => $id,
					'description' => ( strlen($message_post)>100 ? substr($message_post, 0, 100) : $message_post )
				));
			}
			$inCore->redirect('/forum/thread'.$id.'-'.$pages.'.html#'.$lastid);

		} else {

			if ($do=='newthread'){
				//NEW THREAD
				$title          = $inCore->request('title', 'str');
				$description    = $inCore->request('description', 'str');

				if($title && $message){

					$access_list = $inCore->yamlToArray($forum['access_list']);
					$is_hidden = $access_list ? 1 : 0;
					
					$threadlastid = $model->addThread(array(
							'forum_id' => $id,
							'user_id' => $inUser->id,
							'title' => $title,
							'description' => $description,
							'is_hidden' => $is_hidden
						));
					
					$lastid = $model->addPost(array(
							'thread_id' => $threadlastid,
							'user_id' => $inUser->id,
							'message' => $message
						));
					
					cmsUser::checkAwards($inUser->id);

					$inCore->registerUploadImages(session_id(), $lastid, 'forum');
					
					//subscribe thread
					if ($_POST['subscribe']){
						cmsUser::subscribe($inUser->id, 'forum', $threadlastid);
					}
								
					//create attached poll
					$poll_error = false;
					if (@$_POST['poll']['title']){
						$poll_error = createPoll($threadlastid, $_POST['poll'], $cfg);
						if ($poll_error) { echo $poll_error; }
					}
					
					//if poll created without errors
					//upload attached files
					$file_error = false;
					if ($cfg['fa_on'] && sizeof($_FILES['fa'])) {
						//upload
						$file_error = uploadFiles($lastid, $cfg);
					}

					if ($file_error){
						$err_msg = $_LANG['ERR_UPLOAD_FILE'].'<div><strong>'.$_LANG['UPLOAD_MAXSIZE'].':</strong> '.$cfg['fa_size'].' '.$_LANG['KBITE'].'.</div><div><strong>'.$_LANG['UPLOAD_FILETYPE'].':</strong> .'.strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</div><p>'.$_LANG['NOT_ALL_FILE_ATTACH'].'</p>';
						cmsCore::addSessionMessage($err_msg, 'error');
					}

					if (IS_BILLING && $forum['topic_cost']){
						cmsBilling::process('forum', 'add_thread', $forum['topic_cost']);
					}

					if (!$is_hidden) {
						$message = $inCore->parseSmiles($message, true);
						$message = strip_tags($message);
						//регистрируем событие
						cmsActions::log('add_thread', array(
							'object' => $title,
							'object_url' => '/forum/thread'.$threadlastid.'.html',
							'object_id' => $threadlastid,
							'target' => $forum['title'],
							'target_url' => '/forum/'.$forum['id'],
							'target_id' => $forum['id'], 
							'description' => ( strlen($message)>100 ? substr($message, 0, 100) : $message )
						));	
					}
					$inCore->redirect('/forum/thread'.$threadlastid.'.html#'.$lastid);

				} else {
					echo '<p>'.$_LANG['NEED_TITLE_THREAD_YOUR_POST'].'</p>';
				}
			} else { //edit post
				if($message){
					$posts_in_thread = $inDB->rows_count('cms_forum_posts', 'thread_id='.$msg['thread_id']);
					$pages = ceil($posts_in_thread / $cfg['pp_thread']);
					$sql = "UPDATE cms_forum_posts 
							SET content = '$message',
								editdate = NOW(),
								edittimes = edittimes + 1
							WHERE id = '$id'";
					$inDB->query($sql) ;
					$inCore->registerUploadImages(session_id(), $id, 'forum');
					$message = $inCore->parseSmiles($message, true);
					$message = substr(strip_tags($message), 0, 100);
					cmsActions::updateLog('add_fpost', array('description' => $message), $id);
					if ($pages==1){
						$inCore->redirect('/forum/thread'.$msg['thread_id'].'.html#'.$id);
					} else {
						$inCore->redirect('/forum/thread'.$msg['thread_id'].'-'.$pages.'.html#'.$id);
					}					
				} else { echo '<p>'.$_LANG['NEED_TEXT_POST'].'</p>'; }
			}
		}
	}
	
}
///////////////////////////// DELETE POST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='deletepost'){

	if (!$inUser->id){ cmsUser::goToLogin(); }

	$msg = $model->getPost($id);
	if(!$msg) { cmsCore::error404(); }
	if(!$inUser->is_admin && !$inCore->isUserCan('forum/moderate') && $inUser->id != $msg['user_id']) { cmsCore::error404(); }

	if(!$inUser->is_admin && !$inCore->isUserCan('forum/moderate') && $cfg['edit_minutes']){

		$end_min  = $model->checkEditTime($msg['pubdate'], $cfg['edit_minutes']);
		if($cfg['edit_minutes'] == -1 || $end_min <= 0){
			cmsCore::error404();
		}

	}

	cmsCore::addSessionMessage($_LANG['MSG_IS_DELETED'], 'info');
	$model->deletePost($id, $inUser->id);

	$inCore->redirectBack();

}
///////////////////////////// MOVE THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='movethread'){

	if (!$inUser->id){ cmsUser::goToLogin(); }

	if (!$inUser->is_admin && !$inCore->isUserCan('forum/moderate')){ cmsCore::error404(); }

	$t = $model->getThread($id);
	if(!$t) { cmsCore::error404(); }

	if (!isset($_POST['gomove'])){ //SHOW MOVE FORM
				
		$inPage->setTitle($_LANG['MOVE_THREAD']);
		$inPage->addPathway($_LANG['MOVE_THREAD']);

		echo '<div class="con_heading">'.$_LANG['MOVE_THREAD'].'</div>';
	
		echo '<div style="margin-top:10px"><strong>'.$_LANG['THREAD'].':</strong> <a href="/forum/thread'.$t['id'].'.html">'.$t['title'].'</a></div>';
		echo '<div><form action="" method="POST">';
		echo '<table border="0" cellpadding="10" style="background-color:#EBEBEB"><tr><td valign="top">'.$_LANG['MOVE_THREAD_IN_FORUM'].':</td>';
		echo '<td valign="top"><select name="forum_id">';

		$fsql = "SELECT * FROM cms_forums";
		$fresult = $inDB->query($fsql) ;
		if ($inDB->num_rows($fresult)){
			while ($f = $inDB->fetch_assoc($fresult)){
				echo '<option value="'.$f['id'].'" ';
				if ($t['forum_id'] == $f['id']) { echo 'selected'; }
				echo '>'.$f['category_id'].' --- '.$f['title'].'</option>';
			}
		}
		
		echo '</select></td>';
		echo '<td valign="top"><input type="submit" name="gomove" value="'.$_LANG['MOVE'].'"/></td></tr></table>';
		echo '</form></div>';				
			
	} else { //DO MOVE

		$fid = $inCore->request('forum_id', 'int', 0);

		$f = $model->getForum($fid);
		if(!$f) { cmsCore::error404(); }

		$access_list = $inCore->yamlToArray($f['access_list']);
		$is_hidden = $access_list ? 1 : 0;

		$inDB->query("UPDATE cms_forum_threads SET forum_id = '$fid', is_hidden = '{$is_hidden}' WHERE id = '$id'") ;

		cmsActions::updateLog('add_thread', array('target' => $f['title'], 'target_url' => '/forum/'.$f['id'], 'target_id' => $f['id']), $id);

		$inCore->redirect('/forum/'.$fid);			
	}

}
///////////////////////////// RENAME THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='renamethread'){

	if (!$inUser->id){ cmsUser::goToLogin(); }

	if (!$inUser->is_admin && !$inCore->isUserCan('forum/moderate')){ cmsCore::error404(); }

	$t = $model->getThread($id);
	if(!$t) { cmsCore::error404(); }

	if (!isset($_POST['gorename'])){ //SHOW MOVE FORM
				
		$inPage->setTitle($_LANG['RENAME_THREAD']);
		$inPage->addPathway($_LANG['RENAME_THREAD']);

		echo '<div class="con_heading">'.$_LANG['RENAME_THREAD'].'</div>';

		echo '<div style="margin-top:10px"><strong>'.$_LANG['THREAD'].':</strong> <a href="/forum/thread'.$t['id'].'.html">'.$t['title'].'</a></div>';
		
		echo '<div style="margin-top:5px"><form action="" method="POST">';
		
		echo '<table border="0" cellpadding="5" style="background-color:#EBEBEB">
			  <tr><td valign="top">'.$_LANG['THREAD_TITLE'].':</td>';
		
			echo '<td valign="top">
					<input type="hidden" name="tid" value="'.$t['id'].'"/>
					<input type="text" size="45" value="'.htmlspecialchars($t['title']).'" name="title" id="title"/>
				 </td>';

		echo '</tr><tr><td valign="top">'.$_LANG['DESCRIPTION'].':</td>';
		
			echo '<td valign="top">
					<input type="text" size="45" value="'.htmlspecialchars($t['description']).'" name="description" id="title"/>
				 </td>';
			
		echo '</tr></table>';
		
		echo '<div style="margin-top:5px"><input type="submit" name="gorename" value="'.$_LANG['SAVE'].'"/> <input type="button" onclick="window.history.go(-1);" name="cancel" value="'.$_LANG['CANCEL'].'"/></div></form></div>';
			
	} else { //DO RENAME
	
		if (@$_POST['title']){				
			$title       = $inCore->request('title', 'str');
			$description = $inCore->request('description', 'str');

			$inDB->query("UPDATE cms_forum_threads SET title = '$title', description = '$description' WHERE id = '$id'");
			cmsActions::updateLog('add_fpost', array('target' => $title), 0, $id);
			cmsActions::updateLog('add_thread', array('object' => $title), $id);

		}
		$inCore->redirect('/forum/thread'.$id.'.html');			
	}

}
///////////////////////////// DELETE THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='deletethread'){
	$thread_user_id = $inDB->get_field('cms_forum_threads', "id = '$id'", 'user_id');
	if (usrCheckAuth() && ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate') || $inUser->id == $thread_user_id)){
		$forum_id = $inDB->get_field('cms_forum_threads', 'id='.$id, 'forum_id');
        $model->deleteThread($id, $inUser->id);
	}	
	$inCore->redirect('/forum/'.$forum_id);
}
///////////////////////////// PIN/UNPIN THREAD ////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='pin'){

	$pinned = $inCore->request('pinned', 'int', 0);
	
	if (!$inUser->id){ cmsUser::goToLogin(); }

	$msg = $model->getThread($id);
	if(!$msg) { cmsCore::error404(); }

	if ($msg['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
		$inDB->query("UPDATE cms_forum_threads SET pinned = '$pinned' WHERE id = '$id'") ;
	}

	$inCore->redirectBack();
}
///////////////////////////// CLOSE/OPEN THREAD ////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='close'){
	$closed = $inCore->request('closed', 'int', 0);
	
	if (!$inUser->id){ cmsUser::goToLogin(); }

	$msg = $model->getThread($id);
	if(!$msg) { cmsCore::error404(); }

	if ($msg['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
		$inDB->query("UPDATE cms_forum_threads SET closed = '$closed' WHERE id = '$id'");
		if(!$closed) {
			$title = str_ireplace($_LANG['TOPIC_FIXED_PREFIX'], '', $msg['title']);
			$inDB->query("UPDATE cms_forum_threads SET title = '{$inDB->escape_string($title)}' WHERE id = '$id'");
		}
	}

	$inCore->redirectBack();
}
///////////////////////////// ATTACHED FILE DOWNLOAD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='download'){

	$sql = "SELECT * FROM cms_forum_files WHERE id = '$id'";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$file = $inDB->fetch_assoc($result);	
		$filename = $file['filename'];
		$filesize = $file['filesize'];
		$location = '/upload/forum/post'.$file['post_id'].'/'.$filename;
		
		$inDB->query("UPDATE cms_forum_files SET hits = hits + 1 WHERE id = $id") ;
		
		header('Content-Disposition: attachment; filename='.$filename . "\n");
		header('Content-Type: application/x-force-download; name="'.$filename.'"' . "\n");
		header('Location:'.$location);						
	}

}
///////////////////////////// ATTACHED FILE DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfile'){

	$file = uploadDelete($id);
	$inCore->redirect('/forum/thread'.$file['tid'].'.html#'.$file['post_id']);

}
///////////////////////////// ATTACHED FILE RELOADING /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='reloadfile'){

	if (!$inUser->id){ cmsUser::goToLogin(); }

	//get current file data	
	$sql = "SELECT f.*, p.thread_id as tid, u.id as uid, DATE_FORMAT(f.pubdate, '%d-%m-%Y в %H:%i') as fpubdate
			FROM cms_forum_files f, cms_users u, cms_forum_posts p
			WHERE f.id = '$id' AND f.post_id = p.id AND p.user_id = u.id";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
	//if file found		

		$inPage->addPathway($_LANG['RELOAD_FILE']);
		echo '<div class="con_heading">'.$_LANG['RELOAD_FILE'].'</div>';
			
		$file = $inDB->fetch_assoc($result);			
		if ($file['uid'] == @$inUser->id || @$inCore->userIsAdmin(@$inUser->id) || $inCore->isUserCan('forum/moderate')){		
		//if user is file owner
		
			$post_id = $file['post_id'];
		
			if (isset($_POST['goreload'])){
			//reload file
				if ($_FILES["newfile"]["error"] == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES["newfile"]["tmp_name"];
					$filename = $_FILES["newfile"]["name"];
					$filesize = $_FILES["newfile"]["size"];																	
					$path_parts = pathinfo($filename);
					$ext = $path_parts['extension'];	
					if(strstr('php', $ext)) { echo usrAccessDenied(); }
					if(strstr('htm', $ext)) { echo usrAccessDenied(); }
					if(strstr('htaccess', $ext)) { echo usrAccessDenied(); }
					//check file size
					if ($filesize <= $cfg['fa_size']*1024){
							
						//check file extension is allowed									
						if ( (!$cfg['fa_ext_not'] && strstr($cfg['fa_ext'], $ext)) || ($cfg['fa_ext_not'] && !strstr($cfg['fa_ext'], $ext))){									
							$name = basename($filename, '.' . $path_parts['extension']);
									
							$filename = $name . '_' . substr(session_id(), 0, 5) . '.' . $ext;
							$destination = PATH."/upload/forum/post".$post_id."/".$filename;
							
							@unlink(PATH."/upload/forum/post".$post_id."/".$file['filename']);
							
							move_uploaded_file($tmp_name, $destination);
							
							$sql = "UPDATE cms_forum_files SET filename = '$filename', filesize = '$filesize', pubdate=NOW(), hits=0 WHERE id = $id";									
							$inDB->query($sql) ;
							
							$inCore->redirect('/forum/thread'.$file['tid'].'.html#'.$file['post_id']);
						} else {
							echo '<p style="color:red"><strong>'.$_LANG['ERROR'].':</strong> '.$_LANG['ERR_FILE_TYPE'].'</p>';
							echo '<p><strong>'.$_LANG['MUST_FILE_TYPE'].':</strong> .'.strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</p>';
						}
					
					} else { echo '<p style="color:red"><strong>'.$_LANG['ERROR'].':</strong> '.$_LANG['ERR_FILE_SIZE'].' ('.$cfg['fa_size'].' '.$_LANG['KBITE'].').</p>'; }
				}  else { 
							echo '<p style="color:red"><strong>'.$_LANG['ERROR'].':</strong> '.$_LANG['CHECK_SIZE_TYPE_FILE'].'</p>';
							echo '<p><strong>'.$_LANG['MAX_SIZE'].':</strong> '.$cfg['fa_size'].' '.$_LANG['KBITE'].'.</p>';
							echo '<p><strong>'.$_LANG['MUST_FILE_TYPE'].':</strong> .'.strtolower(str_replace(' ', ' .', $cfg['fa_ext'])).'</p>';
						}
			} else {
				//show reload form													
				echo '<div><strong>'.$_LANG['FILE'].':</strong> <a href="/upload/forum/post'.$file['post_id'].'/'.$file['filename'].'">'.$file['filename'].'</a> ('.round(($file['filesize']/1024),2).' '.$_LANG['KBITE'].').</div>';
				echo '<div><strong>'.$_LANG['DATE_UPLOAD'].':</strong> '.$file['fpubdate'].'.</div>';

				echo '<div><form enctype="multipart/form-data" action="" method="POST">' . "\n";	
					echo '<p>'.$_LANG['SELECT_NEW_FILE_UPLOAD'].': </p>' . "\n";
					echo '<input name="MAX_FILE_SIZE" type="hidden" value="'.($cfg['fa_size']*1024).'"/>'. "\n";
					echo '<input name="newfile" type="file" id="newfile" size="45" />'. "\n";
					echo '<p>'.$_LANG['MAX_SIZE'].': '.$cfg['fa_size'].' '.$_LANG['KBITE'].'.</p>'. "\n";
					echo '<p><input type="submit" name="goreload" value="'.$_LANG['UPLOAD'].'"> <input type="button" onclick="window.history.go(-1);" value="'.$_LANG['CANCEL'].'"/></p>'. "\n";
				echo '</form></div>'. "\n";				
			}
		
		} else 	{ echo usrAccessDenied(); }
	} else { echo '<p>'.$_LANG['FILE_NOT_FOUND'].'!</p>'; }
	
}
///////////////////////////// последние сообщения //////////////////////////////////////////////////////////////////////////////////////////
if ($do=='latest_posts'){

	$page = $inCore->request('page', 'int', 1);

    $inActions = cmsActions::getInstance();

	echo '<div class="float_bar"><a href="/forum/latest_thread">'.$_LANG['NEW_THREADS'].'</a> | <a href="/forum">'.$_LANG['FORUMS'].'</a></div>';

    $inPage->printHeading($_LANG['LATEST_POSTS_ON_FORUM']);

	$inPage->setTitle($_LANG['LATEST_POSTS_ON_FORUM']);
	$inPage->addPathway($_LANG['FORUMS'], '/forum');
	$inPage->addPathway($_LANG['LATEST_POSTS_ON_FORUM']);

	$inActions->showTargets(true);

	$action = $inActions->getAction('add_fpost');

	$inActions->onlySelectedTypes(array($action['id']));

	$total = $inActions->getCountActions();

	$inActions->limitPage($page, 15);

	$actions = $inActions->getActionsLog();

	$smarty = $inCore->initSmarty('components', 'com_actions_view.tpl');
	$smarty->assign('actions', $actions);
	$smarty->assign('total', $total);
	$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, 15, '/forum/latest_posts/page-%page%'));
	$smarty->display('com_actions_view.tpl');

}
///////////////////////////// последние темы ///////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='latest_thread'){

	$page = $inCore->request('page', 'int', 1);

    $inActions = cmsActions::getInstance();

	echo '<div class="float_bar"><a href="/forum/latest_posts">'.$_LANG['LATEST_POSTS'].'</a> | <a href="/forum">'.$_LANG['FORUMS'].'</a></div>';

    $inPage->printHeading($_LANG['NEW_THREADS_ON_FORUM']);

	$inPage->setTitle($_LANG['NEW_THREADS_ON_FORUM']);
	$inPage->addPathway($_LANG['FORUMS'], '/forum');
	$inPage->addPathway($_LANG['NEW_THREADS_ON_FORUM']);

	$inActions->showTargets(true);

	$action = $inActions->getAction('add_thread');

	$inActions->onlySelectedTypes(array($action['id']));

	$total = $inActions->getCountActions();

	$inActions->limitPage($page, 15);

	$actions = $inActions->getActionsLog();

	$smarty = $inCore->initSmarty('components', 'com_actions_view.tpl');
	$smarty->assign('actions', $actions);
	$smarty->assign('total', $total);
	$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, 15, '/forum/latest_thread/page-%page%'));
	$smarty->display('com_actions_view.tpl');

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view_cat'){

	$seolink = $inCore->request('seolink', 'str', '');

    $groupsql = forumUserAuthSQL();
    $sql = "SELECT *
            FROM cms_forum_cats
            WHERE seolink = '{$seolink}' AND published = 1 $groupsql
            LIMIT 1";
    $result = $inDB->query($sql) ;
	if (!$inDB->num_rows($result)) { cmsCore::error404(); }
    $cat = $inDB->fetch_assoc($result);

    $inPage->printHeading($cat['title']);
	$inPage->addPathway($_LANG['FORUMS'], '/forum');
	$inPage->addPathway($cat['title']);

	$rootid = $inDB->get_field('cms_forums', 'parent_id=0', 'id');

	echo '<table class="forums_table" width="100%" cellspacing="0" cellpadding="8" border="0" bordercolor="#999999" >';
	echo '<tr>
	  <td colspan="2" width="" class="darkBlue-LightBlue">'.$_LANG['FORUMS'].'</td>
	  <td width="120" class="darkBlue-LightBlue">'.$_LANG['FORUM_ACT'].'</td>
	  <td width="250" class="darkBlue-LightBlue">'.$_LANG['LAST_POST'].'</td>
	</tr>';
	//FORUMS LIST IN CATEGORY
	$fsql = "SELECT *
			 FROM cms_forums
			 WHERE published = 1 AND category_id = '{$cat['id']}' AND parent_id = '$rootid'
			 ORDER BY ordering";
	$fresult = $inDB->query($fsql) ;
	if ($inDB->num_rows($fresult)){
		$row = 1;
		//print forums list in category
		while ($f = $inDB->fetch_assoc($fresult)){
			if(!$inCore->checkContentAccess($f['access_list'])) { continue; }
			//GET SUBFORUMS LIST
			$subforums = '';
			$sql = "SELECT id, title, access_list
					FROM cms_forums
					WHERE parent_id = '{$f['id']}'
					ORDER BY title";
			$rs = $inDB->query($sql);
			$sub = $inDB->num_rows($rs);
			if ($sub){
				while ($sf=$inDB->fetch_assoc($rs)){
					if(!$inCore->checkContentAccess($sf['access_list'])) { continue; }
					$subforums .= '<a href="/forum/'.$sf['id'].'">'.$sf['title'].'</a>, ';
				}
				$subforums = rtrim($subforums, ', ');
			} else {
				$subforums = '';
			}
			//PRINT FORUM DATA
			if ($row % 2) { $class='row11'; } else { $class = 'row2'; }
			$icon = $f['icon'] ? '/upload/forum/cat_icons/'.$f['icon'] : '/components/forum/images/forum.gif';
			echo '<tr>';
				echo '<td width="32" class="'.$class.'" align="center" valign="top"><img src="'.$icon.'" border="0" /></td>';
				echo '<td width="" class="'.$class.'" align="left" valign="top">';
					//FORUM TITLE
					echo '<div class="forum_link"><a href="/forum/'.$f['id'].'">'.$f['title'].'</a></div>';
					//FORUM DESCRIPTION
					echo '<div class="forum_desc">'.$f['description'].'</div>';
					//SUBFORUMS
					if ($sub){
						echo '<div class="forum_subs"><span class="forum_subs_title">'.$_LANG['SUBFORUMS'].':</span> '.$subforums.'</div>';
					}
				echo '</td>';
				echo '<td class="'.$class.'" style="font-size:11px" valign="top">'.$model->getForumMessages($f['NSLeft'], $f['NSRight']).'</td>';
				echo '<td style="font-size:11px" class="'.$class.'" valign="top">'.forumLastMessage($f['id'], $cfg['pp_thread']).'</td>';
			echo '</tr>';
			$row++;
		}
	} else {
		echo '<td colspan="4"><p>'.$_LANG['NOT_FORUMS_IN_CAT'].'</p></td>';
	}
	echo '</table>';

    if ($inUser->id){
        $logdate = $_SESSION['user']['logdate'];
    } else {
        $logdate = '9999-01-01 12:00:00';
    }

    $tsql = "SELECT t.*, COUNT(p.id) as postsnum, IF(t.pubdate > '$logdate', 1, 0) as is_new, u.nickname, u.login
             FROM cms_forum_threads t
			 INNER JOIN cms_forums f ON f.id = t.forum_id
			 INNER JOIN cms_forum_cats cat ON cat.id = f.category_id AND cat.id = '{$cat['id']}'
			 INNER JOIN cms_forum_posts p ON p.thread_id = t.id
			 LEFT JOIN cms_users u ON u.id = t.user_id
             WHERE t.is_hidden = 0 AND t.closed = 0
             GROUP BY t.id
             ORDER BY t.pubdate DESC, t.hits DESC
             LIMIT 15";

    $tresult = $inDB->query($tsql) ;

    $threads = array();

    if ($inDB->num_rows($tresult)){

        while ($thread = $inDB->fetch_assoc($tresult)){

            $thread['author']       = array('nickname'=>$thread['nickname'], 'login'=>$thread['login']);
            $thread['pages']        = ceil($thread['postsnum'] / $cfg['pp_thread']);
            $thread['answers']      = $thread['postsnum']-1;
            $thread['last_message'] = threadLastMessage($thread['id'], $thread['pages']);

            $threads[]              = $thread;

        }
        
    }

    $smarty = $inCore->initSmarty('components', 'com_forum_view_act.tpl');
    $smarty->assign('cfg', $cfg);
    $smarty->assign('threads', $threads);
    $smarty->display('com_forum_view_act.tpl');

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$inCore->executePluginRoute($do);
} //function
?>