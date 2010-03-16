<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function forumsList($selected=0){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	$html .= '<select name="goforum" id="goforum" style="width:220px; margin:0px" onchange="goForum('.$menuid.')">';
	$nested_sets = $inCore->nestedSetsInit('cms_forums');
	$rootid = dbGetField('cms_forums', 'parent_id=0', 'id');
	
	$groupsql = forumUserAuthSQL();	
	
	$fsql = "SELECT * 
			 FROM cms_forums 
			 WHERE published = 1 $groupsql
			 ORDER BY NSLeft";
	$rs_rows = $inDB->query($fsql);
	
	while($cat = $inDB->fetch_assoc($rs_rows)){
		if ($cat['parent_id']>0){
			if (@$selected==$cat['id']){
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
	global $menuid;
	
	$poll_error = '';
	//title
	$title = $poll['title'];
	$title = htmlspecialchars($title, ENT_QUOTES, 'cp1251');
	//description
	$desc = $poll['desc'];
	if ($desc) { $desc = htmlspecialchars($desc, ENT_QUOTES, 'cp1251');	}
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
			$value = htmlspecialchars($value, ENT_QUOTES, 'cp1251');
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
		$poll_error .= '<p><a href="/forum/'.$menuid.'/thread'.$thread_id.'.html">'.$_LANG['CONTINUE'].'</a> &rarr;</p>';
	}
	
	return $poll_error;	
}

function pageSelect($records, $current, $perpage, $field='page', $form='pageform'){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	$html = '';
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<td width="60"><strong>'.$_LANG['PAGE'].': </strong></span></td>';
			if ($current>2){
				$html .= '<td width="16"><a href="javascript:goPage('.(1-$current).', \''.$field.'\', \''.$form.'\')" title="'.$_LANG['FIRST'].'"><img src="/images/icons/first.gif" border="0"/></a></td>';
			}
			if ($current>1) { 
				$html .= '<td width="16"><a href="javascript:goPage('.(-1).', \''.$field.'\', \''.$form.'\')" title="'.$_LANG['PREVIOUS'] .'"><img src="/images/icons/prev.gif" border="0"/></a></td>';
			}
			$html .= '<td width="40" align="center"><form style="margin:0px;padding:0px" action="" name="'.$form.'" id="'.$form.'" method="POST">';
			$html .= '<select style="width:40px" name="page" id="'.$field.'" onchange="$(\'#'.$form.'\').submit()">';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$html .= '<option value="'.$p.'">'.$p.'</option>';		
				} else {
					$html .= '<option value="'.$p.'" selected>'.$p.'</option>';		
				}
			}
			$html .= '</select></form></td>';
			if ($current<$pages) { 
				$html .= '<td width="16"><a href="javascript:goPage('.(+1).', \''.$field.'\', \''.$form.'\')" title="'.$_LANG['NEXT'].'"><img src="/images/icons/next.gif" border="0"/></a></td>';
			}
			if ($current<$pages-1){
				$html .= '<td width=""><a href="javascript:goPage('.($pages-$current).', \''.$field.'\', \''.$form.'\')" title="'.$_LANG['LAST'].'"><img src="/images/icons/last.gif" border="0"/></a></td>';
			} else {
				$html .= '<td width="">&nbsp;</td>';
			}
		}
	}
	return $html;
}

function pageBarThread($thread_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	$html = '';
	global $menuid;
	$result = $inDB->query("SELECT id FROM cms_forum_posts WHERE thread_id = $thread_id") ;
	$records = $inDB->num_rows($result);
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>'.$_LANG['PAGES'].': </strong></span>';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/forum/'.$menuid.'/thread'.$thread_id.'-'.$p.'.html';
					
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function uploadFiles($post_id, $cfg){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	$file_error = false;
    $c = 0;

    @mkdir($_SERVER['DOCUMENT_ROOT']."/upload/forum/post".$post_id);
    foreach ($_FILES["fa"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["fa"]["tmp_name"][$key];
                    $file = $_FILES["fa"]["name"][$key];
                    $filesize = $_FILES["fa"]["size"][$key];
                    $path_parts = pathinfo($file);
                    $ext = $path_parts['extension'];
                    //check file extension is allowed
                    if (strstr($cfg['fa_ext'], $ext)){
                        $name = basename($file, '.' . $path_parts['extension']);
                        $name = str_replace(' ', '_', $name);
                        $file = $name . '_' . substr(session_id(), 0, 5) . '.' . $ext;
                        $destination = $_SERVER['DOCUMENT_ROOT']."/upload/forum/post".$post_id."/".$file;
                        move_uploaded_file($tmp_name, $destination);
                        $sql = "INSERT INTO cms_forum_files (post_id, filename, filesize, hits, pubdate)
                                VALUES ('$post_id', '$file', '$filesize', 0, NOW())";
                        @chmod($destination, 0777);
                        $inDB->query($sql) ;
                    } else { $file_error = true; }
                    $c ++;
            } elseif ($error == UPLOAD_ERR_FORM_SIZE) { $file_error = true; }
    }
    
	return $file_error;	
}

function uploadError($menuid, $id, $post_id, $filesize, $ext){
        global $_LANG;
	echo '<p style="color:red;font-weight:bold">'.$_LANG['ERR_UPLOAD_FILE'].'</p>';
	echo '<div><strong>'.$_LANG['UPLOAD_MAXSIZE'].':</strong> '.$filesize.' '.$_LANG['KBITE'].'.</div>';
	echo '<div><strong>'.$_LANG['UPLOAD_FILETYPE'].':</strong> .'.strtolower(str_replace(' ', ' .', $ext)).'</div>';
	echo '<p>'.$_LANG['NOT_ALL_FILE_ATTACH'].'</p>';
	echo '<p><a href="/forum/'.$menuid.'/thread'.$id.'.html#'.$post_id.'">'.$_LANG['CONTINUE'].'</a> &rarr;</p>';
	return true;
}

function forum(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$menuid = $inCore->menuId();
	$cfg = $inCore->loadComponentConfig('forum');

    $inCore->loadModel('forum');
    $model = new cms_model_forum();

    $menutitle = $inCore->menuTitle();
    global $_LANG;
	if (!$cfg['is_on']){
		echo '<div class="con_heading">'.$_LANG['FORUM_NOT_ACCESS'].'</div>';
		echo '<p>'.$_LANG['FORUM_NOT_ACCESS_TEXT'].'</p>';
		return;
	}

    $inCore->includeFile("/components/users/includes/usercore.php");
	$inCore->includeFile("/components/forum/includes/forumcore.php");
	
	$id		= $inCore->request('id', 'int', 0);
	$do		= $inCore->request('do', 'str', 'view');
	$page	= $inCore->request('page', 'int', 1);
	
///////////////////////////// VIEW FORUMS LIST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

    $inPage->printHeading($_LANG['FORUMS']);
    $inPage->setTitle($_LANG['FORUMS']);

    if ($menuid==0) { $inPage->addPathway($_LANG['FORUMS'], '/forum/'.$menuid); }

    $groupsql = forumUserAuthSQL();
    $sql = "SELECT *
            FROM cms_forum_cats
            WHERE published = 1 $groupsql
            ORDER BY ordering";
    $result = $inDB->query($sql) ;

    if ($inDB->num_rows($result)){
        while ($cat = $inDB->fetch_assoc($result)){
            echo '<div class="forum_cattitle">'.$cat['title'].'</div>';
            //FORUMS LIST IN CATEGORY
            $rootid = dbGetField('cms_forums', 'parent_id=0', 'id');
            $fsql = "SELECT *
                     FROM cms_forums
                     WHERE published = 1 AND category_id = '".$cat['id']."' AND parent_id = $rootid $groupsql
                     ORDER BY ordering";
            $fresult = $inDB->query($fsql) ;
            if ($inDB->num_rows($fresult)){
                $row = 1;
                //print forums list in category
                echo '<table class="forums_table" width="100%" cellspacing="0" cellpadding="8" border="0" bordercolor="#999999" >';
                while ($f = $inDB->fetch_assoc($fresult)){
                    //GET SUBFORUMS LIST
                    $subforums = '';
                    $sql = "SELECT id, title
                            FROM cms_forums
                            WHERE parent_id = {$f['id']} $groupsql
                            ORDER BY title";
                    $rs = $inDB->query($sql) or die('ERROR BUILDING SUBFORUMS LIST FOR ID='.$f['id']);
                    $sub = $inDB->num_rows($rs);
                    if ($sub){
                        $s = 1;
                        while ($sf=$inDB->fetch_assoc($rs)){
                            $subforums .= '<a href="/forum/'.$menuid.'/'.$sf['id'].'">'.$sf['title'].'</a>';
                            if ($s < $sub) { $subforums .= ', '; $s++; }
                        }
                    } else {
                        $subforums = '';
                    }
                    //PRINT FORUM DATA
                    if ($row % 2) { $class='row1'; } else { $class = 'row2'; }
                    echo '<tr>';
                        echo '<td width="40" class="'.$class.'" align="center" valign="top"><img src="/components/forum/images/forum.gif" border="0" /></td>';
                        echo '<td width="" class="'.$class.'" align="left" valign="top">';
                            //FORUM TITLE
                            echo '<div class="forum_link"><a href="/forum/'.$menuid.'/'.$f['id'].'">'.$f['title'].'</a></div>';
                            //FORUM DESCRIPTION
                            echo '<div class="forum_desc">'.$f['description'].'</div>';
                            //SUBFORUMS
                            if ($sub){
                                echo '<div class="forum_subs"><span class="forum_subs_title">'.$_LANG['SUBFORUMS'].':</span> '.$subforums.'</div>';
                            }
                        echo '</td>';
                        echo '<td width="120" class="'.$class.'" style="font-size:10px" valign="top">'.forumMessages($f['id']).'</td>';
                        echo '<td width="250" style="font-size:10px" class="'.$class.'" valign="top">'.forumLastMessage($f['id'], $cfg['pp_thread']).'</td>';
                    echo '</tr>';
                    $row++;
                }
                echo '</table>';
            } else {
                echo '<p>'.$_LANG['NOT_FORUMS_IN_CAT'].'</p>';
            }
        }
    } else {
        echo '<p>'.$_LANG['NOT_CATS_OF_FORUM'].'</p>';
    }
		
}	
///////////////////////////// VIEW THREADS /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='forum'){

	if($menuid==0) { $inPage->addPathway($_LANG['FORUMS'], '/forum/'.$menuid); }

    $f = $model->getForum($id);

	if (!$f){
        echo '<h1 class="con_heading">'.$_LANG['FORUM_NOT_FOUND'].'</h1>';
        echo '<p>'.$_LANG['FORUM_NOT_FOUND_TEXT'].'</p>';
        return;
    }
        
    $inPage->printHeading($f['title']);
    $inPage->setTitle($f['title']);
    $inPage->addHeadJS('core/js/pagesel.js');

    //PATHWAY ENTRY
    $left_key   = $f['NSLeft'];
    $right_key  = $f['NSRight'];
    $sql        = "SELECT id, title, NSLevel FROM cms_forums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 ORDER BY NSLeft";
    $rs_rows    = $inDB->query($sql);

    while($pcat = $inDB->fetch_assoc($rs_rows)){
        
        $inPage->addPathway($pcat['title'], '/forum/'.$menuid.'/'.$pcat['id']);

    }

    // --------- ������ ���������� ---------------

    $groupsql           = forumUserAuthSQL();
    
    $subforums_sql      = "SELECT *
                           FROM cms_forums
                           WHERE published = 1 AND parent_id = $id $groupsql
                           ORDER BY ordering";

    $subforums_result   = $inDB->query($subforums_sql);
    $subforums_count    = $inDB->num_rows($subforums_result);

    $subforums          = array();

    if ($subforums_count){
        while ($subforum = $inDB->fetch_assoc($subforums_result)){

            $inner_forums = '';

            $inner_sql    = "SELECT id, title FROM cms_forums WHERE parent_id = {$subforum['id']} ORDER BY title";
            $inner_result = $inDB->query($sql);
            $inner_count  = $inDB->num_rows($rs);

            if ($inner_count){
                $s = 1;
                while ($sf=$inDB->fetch_assoc($inner_result)){
                    $inner_forums .= '<a href="/forum/'.$menuid.'/'.$sf['id'].'">'.$sf['title'].'</a>';
                    if ($s < $sub) { $inner_forums .= ', '; $s++; }
                }
            }

            $subforum['subforums']      = $inner_forums;
            $subforum['messages']       = forumMessages($subforum['id']);
            $subforum['last_message']   = forumLastMessage($subforum['id'], $cfg['pp_thread']);
            $subforums[]                = $subforum;

        }        
    }

    // ------ ������ ��� ������ -------

    $perpage = $cfg['pp_forum'] ? $cfg['pp_forum'] : 25;

    // ������� ���������� ��� � ����� �� ��������
    
    $threads_count          = $inDB->rows_count('cms_forum_threads', "forum_id = {$f['id']}");
    $threads_page_select    = '';

    if ($threads_count){
        $threads_page_select = pageSelect($threads_count, $page, $perpage);
    }

    if ($inUser->id){
        $logdate = $_SESSION['user']['logdate'];
    } else {
        $logdate = '9999-01-01 12:00:00';
    }

    $tsql = "SELECT t.*, COUNT(p.id) as postsnum, IF(t.pubdate > '$logdate', 1, 0) as is_new
             FROM cms_forum_threads t, cms_forum_posts p
             WHERE t.forum_id = '".$f['id']."' AND p.thread_id = t.id
             GROUP BY p.thread_id
             ORDER BY t.pinned DESC, t.pubdate DESC
             LIMIT ".(($page-1)*$perpage).", $perpage";

    $tresult = $inDB->query($tsql) ;

    $threads = array();

    if ($inDB->num_rows($tresult)){

        while ($thread = $inDB->fetch_assoc($tresult)){

            $thread['author']       = forumThreadAuthor($thread['id']);
            $thread['pages']        = ceil($thread['postsnum'] / $cfg['pp_thread']);
            $thread['answers']      = $thread['postsnum']-1;
            $thread['last_message'] = threadLastMessage($thread['id']);

            $threads[]              = $thread;

        }

       $pagination = cmsPage::getPagebar($threads_count, $page, $perpage, '/forum/%menuid%/%forum_id%-%page%', array('menuid'=>$menuid, 'forum_id'=>$id));
        
    }

    $smarty = $inCore->initSmarty('components', 'com_forum_view.tpl');
    $smarty->assign('menuid', $menuid);
    $smarty->assign('forum', $f);
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

	if ($menuid == 0) { $inPage->addPathway($_LANG['FORUMS'], '/forum/0'); }

	$sql = "SELECT t.*, f.title as forum, f.id as fid, f.NSLeft as forum_left, f.NSRight as forum_right
			FROM cms_forum_threads t, cms_forums f
			WHERE t.id = $id AND t.forum_id = f.id";
	$result = $inDB->query($sql);
	
	if ($inDB->num_rows($result)){
		$t = $inDB->fetch_assoc($result);
		
		$mythread = ($inUser->id==$t['user_id']);
		$is_admin = $inUser->is_admin;
		$is_moder = $inCore->isUserCan('forum/moderate');
		
		$inDB->query("UPDATE cms_forum_threads SET hits = hits + 1 WHERE id = ".$t['id']) ;
		
        $inPage->setTitle($t['title']);
        $inPage->printHeading($t['title']);

        $inPage->addHeadJS('core/js/pagesel.js');
        $inPage->addHeadJS('components/forum/js/common.js');

		//Calculate posts count
		$csql = "SELECT id FROM cms_forum_posts WHERE thread_id = ".$t['id'];
		$cres = $inDB->query($csql);			
		$posts_count = $inDB->num_rows($cres);

		$perpage=$cfg['pp_thread'];
		
		$lastpage = ceil($posts_count / $perpage);

		if (!strstr($_SERVER['REQUEST_URI'], '/forum/'.$menuid.'/thread'.$id.'-'.$page.'.html')){
			$inCore->redirect('/forum/'.$menuid.'/thread'.$id.'-'.$page.'.html');
		}

		//TOOLBAR TABLE		
		ob_start();
		echo '<table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar"><tr>';

		if ($posts_count){		
			echo '<td width="5">&nbsp;</td>';
			echo pageSelect($posts_count, $page, $perpage);
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
					  <td><a href="/forum/'.$menuid.'/reply'.$t['id'].'.html"><strong>'.$_LANG['NEW_MESSAGE'].'</strong></a></td>';
			} else {
				echo '<td><strong>'.$_LANG['THREAD_CLOSE'].'</td>';
			}
			
			if($is_admin || $is_moder) { 
				if (!$t['closed']){
					echo '<td width="16"><img src="/components/forum/images/toolbar/closethread.gif"/></td>
						  <td><a href="/forum/'.$menuid.'/closethread'.$t['id'].'.html">'.$_LANG['CLOSE'].'</a></td>';
				} else {
					echo '<td width="16"><img src="/components/forum/images/toolbar/openthread.gif"/></td>
						  <td><a href="/forum/'.$menuid.'/openthread'.$t['id'].'.html">'.$_LANG['OPEN'].'</a></td>';
				}
			}
			if(usrCheckAuth()) {
				if (!cmsUser::isSubscribed($inUser->id, 'forum', $t['id'])){
						echo '<td width="16"><img src="/components/forum/images/toolbar/subscribe.gif"/></td>
							  <td><a href="/forum/'.$menuid.'/subscribe'.$t['id'].'.html">'.$_LANG['SUBSCRIBE_THEME'].'</a></td>';
				} else {
					echo '<td width="16"><img src="/components/forum/images/toolbar/unsubscribe.gif"/></td>
						  <td><a href="/forum/'.$menuid.'/unsubscribe'.$t['id'].'.html">'.$_LANG['UNSUBSCRIBE'].'</a></td>';
				}
				if ($is_admin || $is_moder){					
					if (!$t['pinned']){
						echo '<td width="16"><img src="/components/forum/images/toolbar/pinthread.gif"/></td>
							  <td><a href="/forum/'.$menuid.'/pinthread'.$t['id'].'.html">'.$_LANG['PIN'].'</a></td>';
					} else {
						echo '<td width="16"><img src="/components/forum/images/toolbar/unpinthread.gif"/></td>
							  <td><a href="/forum/'.$menuid.'/unpinthread'.$t['id'].'.html">'.$_LANG['UNPIN'].'</a></td>';
					}
					echo '<td width="16"><img src="/components/forum/images/toolbar/movethread.gif"/></td>
						  <td><a href="/forum/'.$menuid.'/movethread'.$t['id'].'.html">'.$_LANG['MOVE'].'</a></td>';
					echo '<td width="16"><img src="/components/forum/images/toolbar/edit.gif"/></td>
						  <td><a href="/forum/'.$menuid.'/renamethread'.$t['id'].'.html">'.$_LANG['RENAME'].'</a></td>';
				}
				if ($inCore->userIsAdmin($inUser->id) || $mythread){
					echo '<td width="16"><img src="/components/forum/images/toolbar/delete.gif"/></td>
						  <td><a href="javascript:deleteThread(\'/forum/'.$menuid.'/deletethread'.$t['id'].'.html\')">'.$_LANG['DELETE'].'</a></td>';
				}
			}
			echo '<td width="16"><img src="/components/forum/images/toolbar/back.gif"/></td>
				  <td><a href="/forum/'.$menuid.'/'.$t['fid'].'">'.$_LANG['BACKB'].'</a></td>';
			echo '</tr></table>';
			echo '</td>';
		} else { echo '<td>&nbsp;</td>'; }
		
		echo '</tr></table>';
		$toolbar = ob_get_clean();
	
		//BUILD PAGE
		//PATHWAY ENTRY
		$left_key = $t['forum_left'];
		$right_key = $t['forum_right'];
		$sql = "SELECT id, title, NSLevel FROM cms_forums WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0 ORDER BY NSLeft";
		$rs_rows = $inDB->query($sql) or die('Error while building forum path');
		while($pcat=$inDB->fetch_assoc($rs_rows)){
				$inPage->addPathway($pcat['title'], '/forum/'.$menuid.'/'.$pcat['id']);
		}			
	
		$inPage->addPathway($t['title'], '/forum/'.$menuid.'/thread'.$t['id'].'.html');
		
			$psql = "SELECT p.*, u.id uid, u.nickname author, u.login author_login, u.is_deleted deleted, up.imageurl imageurl, up.signature signature,
							DATE_FORMAT(p.pubdate, '%d-%m-%Y') as pubdate, DATE_FORMAT(p.pubdate, '%H:%i') as pubtime, 
						   (TO_DAYS(CURDATE()) - TO_DAYS(p.pubdate)) as daysleft,
						   DATE_FORMAT(p.editdate, '%d-%m-%Y') as editdate, DATE_FORMAT(p.editdate, '%H:%i') as edittime
					 FROM cms_forum_posts p, cms_users u, cms_user_profiles up
					 WHERE p.thread_id = $id AND p.user_id = u.id AND up.user_id = u.id
					 ORDER BY p.pubdate ASC
					 LIMIT ".(($page-1)*$perpage).", $perpage";
					 
			$presult = $inDB->query($psql);
			if ($inDB->num_rows($presult)){				
				$num = (($page-1)*$perpage)+1;
				//print posts in thread
				echo $toolbar_pages . $toolbar;

				//ATTACHED POLL
				echo forumAttachedPoll($id);
	
				//THREAD MESSAGES LIST
				echo '<table class="posts_table" width="100%" cellspacing="2" cellpadding="5" border="0" bordercolor="#999999" >';
				while ($p = $inDB->fetch_assoc($presult)){
					$mypost = (@$inUser->id==$p['user_id'] || @$inCore->userIsAdmin($inUser->id));
					$user_messages = forumUserMsgNum($p['uid']);
					echo '<tr>';
						//user column
						echo '<td class="post_usercell" width="140" align="center" valign="top" height="150">';
							echo '<div><a class="post_userlink" href="javascript:addNickname(\''.$p['author'].'\');" title="'.$_LANG['ADD_NICKNAME'].'"/>'.$p['author'].'</a></div>';
														
							echo '<div class="post_userrank">'.forumUserRank($p['uid'], $user_messages, $cfg['ranks'], $cfg['modrank']).'</div>';
						
							echo '<div class="post_userimg">';
								echo '<a href="'.cmsUser::getProfileURL($p['author_login']).'" title="'.$_LANG['GOTO_PROFILE'].'">'.usrImage($p['uid'], 'small').'</a>';
								
								$awards = cmsUser::getAwardsList($p['uid']);
								if ($awards){
									echo '<div class="post_userawards">'; 
										foreach($awards as $uid=>$title){
											echo '<img src="/images/icons/award.gif" border="0" alt="'.$title.'" title="'.$title.'"/>';
										}
									echo '</div>';
								}							
							echo '</div>';
							
							echo '<div class="post_usermsgcnt">'.$_LANG['MESSAGES'].': '.$user_messages.'</div>';
														
						echo '</td>';
						//message column
						echo '<td width="" class="post_msgcell" align="left" valign="top">';
							echo '<a name="'.$p['id'].'"></a>';
							//date & actions
							echo '<table width="100%" class="post_date"><tr>';								
							echo '<td><strong>#'.$num.'</strong> - '.forumDate($p['pubdate'], $p['daysleft']) . ' � ' .$p['pubtime'].'</td>';
							echo '<td align="right">';
							if (usrCheckAuth() && !$t['closed']){ 
								echo '<table cellpadding="1" cellspacing="2" border="0">
                                        <tr>';

                                        echo '<td width="15"><img src="/components/forum/images/toolbar/post-quote.gif"/></td>
                                              <td><a href="javascript:addQuoteText(\''.$p['author'].'\')" title="'.$_LANG['ADD_SELECTED_QUOTE'].'">'.$_LANG['ADD_QUOTE_TEXT'].'</a></td>';

                                        echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-reply.gif"/></td>';
                                        echo '<td><a href="/forum/'.$menuid.'/thread'.$t['id'].'-quote'.$p['id'].'.html" title="'.$_LANG['REPLY_FULL_QUOTE'].'">'.$_LANG['REPLY'].'</a></td>';

                                        if ($mypost || ($is_moder && !$inCore->userIsAdmin($p['uid']))){
                                            echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-edit.gif"/></td>
                                                  <td><a href="/forum/'.$menuid.'/editpost'.$p['id'].'.html">'.$_LANG['EDIT'].'</a></td>';
                                            if ($num > 1){
                                                echo '<td width="15"><img style="margin-left:5px" src="/components/forum/images/toolbar/post-delete.gif"/></td>
                                                      <td><a href="/forum/'.$menuid.'/deletepost'.$p['id'].'.html">'.$_LANG['DELETE'].'</a></td>';
                                            }
                                        }
                                echo '</tr></table>';
							}
                            
							echo '</td></tr></table>';
                            
							//message text							
							$msg = $p['content'];
							$msg = $inCore->parseSmiles($msg, true);	
							$msg = str_replace("&amp;", '&', $msg);
                            
							echo '<div class="post_content">'.$msg.'</div>';
													
							if ($cfg['fa_on']){
								echo forumAttachedFiles($p['id'], $mypost, @$cfg['showimg']);
							}
							//edit details
							if ($p['edittimes']){
								echo '<div class="post_editdate">'.$_LANG['EDITED'].': '.$p['edittimes'].' '.$_LANG['COUNT'].' ('.$_LANG['LAST_EDIT'].': '.$p['editdate'].' '.$_LANG['IN'].' '.$p['edittime'].')</div>';
							}
							//user signature
							if ($p['signature']){
								echo '<div class="post_signature">'.$inCore->parseSmiles($p['signature'], true).'</div>';
							}
						echo '</td>';
					echo '</tr>';
					$num++;
				} 				
				echo '</table>';
				
				//BOTTOM TOOLBAR
				if ($page == $lastpage) { echo '<a name="new"></a>'; }
				echo '<table width="100%" cellspacing="0" cellpadding="5"  class="forum_toolbar"><tr><td><a href="#">'.$_LANG['GOTO_BEGIN_PAGE'].'</a></td>'. $toolbar;
				
				//NAV BAR
				$previd = dbGetFields('cms_forum_threads', 'id<'.$t['id'].' AND forum_id = '.$t['forum_id'], 'id, title', 'id DESC');
				$nextid = dbGetFields('cms_forum_threads', 'id>'.$t['id'].' AND forum_id = '.$t['forum_id'], 'id, title', 'id ASC');			
				
				echo '<div class="forum_navbar">';
					echo '<table width="100%"><tr>';
						echo '<td align="left">';
							echo '<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto"><tr>';
								if ($previd){
									echo '<td align="right" width="">';
										echo '<div>&larr; <a href="/forum/'.$menuid.'/thread'.$previd['id'].'.html">'.$_LANG['PREVIOUS_THREAD'].'</a></div>';
									echo '</td>';
								}
								if ($previd && $nextid) { echo '<td>|</td>'; }
								if ($nextid){
									echo '<td align="left" width="">';
										echo '<div><a href="/forum/'.$menuid.'/thread'.$nextid['id'].'.html">'.$_LANG['NEXT_THREAD'].'</a> &rarr;</div>';
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
				echo pageBarThread($id, $page, $perpage);				
				
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
									echo cmsPage::getBBCodeToolbar('message', false);
								echo '</div>';
								echo cmsPage::getSmilesPanel('message');
							}
							echo '<div class="forum_fast_form">';
								echo '<form action="/forum/'.$menuid.'/reply'.$id.'.html" method="post">';
									echo '<textarea id="message" name="message" rows="5"></textarea>';
									echo '<div class="forum_fast_submit"><input type="submit" name="gosend" value="'.$_LANG['SEND'].'"/></div>';
								echo '</form>';
							echo '</div>';
						} else {
							echo '<div style="padding:5px">'.$_LANG['FOR_WRITE_ON_FORUM'].', <a href="/registration">'.$_LANG['REGISTER'].'</a> '.$_LANG['OR_LOGIN'].'.</div>';
						}
					echo '</div>';
				}
				
			} else {
				echo '<p>'.$_LANG['NO_MESS_IN_THREAD'].'</p>';
			}					
	} else { echo '<p>'.$_LANG['THREAD_NOT_FOUND_TEXT'].'</p>'; }

}
///////////////////////////// NEW THREAD / POST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='newthread' || $do=='newpost' || $do=='editpost'){

	if (usrCheckAuth()){

		$inPage->addHeadJS('core/js/smiles.js');

		if ($do == 'newthread') { 
			$inPage->setTitle($_LANG['NEW_THREAD']);
			$inPage->addPathway($_LANG['NEW_THREAD'], $_SERVER['REQUEST_URI']);
			echo '<div class="con_heading">'.$_LANG['NEW_THREAD'].'</div>';
		} else {
			if ($do == 'newpost'){
				$sql = "SELECT * FROM cms_forum_threads WHERE id = $id";
				$res = $inDB->query($sql);
				if ($inDB->num_rows($res)){		
					$t = $inDB->fetch_assoc($res);
					$inPage->setTitle($_LANG['NEW_POST']);
					$inPage->addPathway($_LANG['NEW_POST'], $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">'.$_LANG['NEW_POST'].'</div>';
					echo '<div style="margin-bottom:10px">
							<strong>'.$_LANG['THREAD'].': </strong><a href="/forum/'.$menuid.'/thread'.$t['id'].'.html">'.$t['title'].'</a>
						  </div>';
				} else {
					die($_LANG['THREAD_NOT_FOUND']);
				}
			} else { //edit post				
					$sql = "SELECT content, thread_id
							FROM cms_forum_posts
							WHERE id = $id";
							
					if(!$inCore->userIsAdmin($inUser->id) && !$inCore->isUserCan('forum/moderate')) { $sql .= " AND user_id=".$inUser->id; }
					
					$result = $inDB->query($sql) ;
				
					if ($inDB->num_rows($result)>0){
						$inPage->setTitle($_LANG['EDIT_POST']);
						$inPage->addPathway($_LANG['EDIT_POST'], $_SERVER['REQUEST_URI']);
						echo '<div class="con_heading">'.$_LANG['EDIT_POST'].'</div>';
					
						$msg = $inDB->fetch_assoc($result);
						$oldmsg = str_replace('<br/>', "\r\n", $msg['content']);	
					} else { die(); }
			}
		}
			
		if (isset($_GET['replyid'])) { $replyid = $_GET['replyid']; }
		else { $replyid = 0; }			
						
		if(!isset($_POST['gosend'])){		
					
			$inDB->query("DELETE FROM cms_upload_images WHERE session_id='".session_id()."'");
					
			if ($replyid){
				$sql = "SELECT p.*, u.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y � %H:%i') senddate
						FROM cms_forum_posts p, cms_users u
						WHERE p.id = $replyid AND p.user_id = u.id";
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
						echo '<div style="margin-top:16px;"><input name="subscribe" type="checkbox" value="1" /> '.$_LANG['SUBSCRIBE_THREAD'].'</div>';
					}
					
					echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="'.$_LANG['SEND'].'" style="font-size:18px"/> ';
					echo '<input type="button" name="gosend" value="'.$_LANG['CANCEL'].'" style="font-size:18px" onclick="window.history.go(-1)"/></div>';
			echo '</td>';	
							
			echo '</tr></table>';
			

			echo '</form>';
			
		} else {
			$message = $inCore->request('message', 'html');
			
			if($do=='newpost'){												
				//NEW POST
				//insert new post
				$sql = "INSERT INTO cms_forum_posts (thread_id, user_id, pubdate, editdate, edittimes, content)
						VALUES ('$id', '".$inUser->id."', NOW(), NOW(), 0, '$message')";
				$inDB->query($sql);
				
				cmsUser::checkAwards($inUser->id);
				$lastid = dbLastId('cms_forum_posts');
				$inCore->registerUploadImages(session_id(), $lastid, 'forum');
											
				//refresh forum thread
				$sql = "UPDATE cms_forum_threads SET pubdate = NOW() WHERE id = $id";
				$inDB->query($sql);
				//upload attached files
				$file_error = false;
				if ($cfg['fa_on'] && $_FILES['fa']) {                    
					$file_error = uploadFiles($lastid, $cfg);		
				}
				//subscribe thread
				if ($_POST['subscribe']){
					cmsUser::isSubscribed($inUser->id, 'forum', $id);
				}
				cmsUser::alertUsers('forum', $id);
				//redirect to last page of thread
				if (!$file_error){			
					$posts_in_thread = dbRowsCount('cms_forum_posts', 'thread_id='.$id);
					$pages = ceil($posts_in_thread / $cfg['pp_thread']);
					if ($pages==1){
						header('location:/forum/'.$menuid.'/thread'.$id.'.html#new');				
					} else {
						header('location:/forum/'.$menuid.'/thread'.$id.'-'.$pages.'.html#new');
					}
				} else {
					uploadError($menuid, $id, $post_id, $cfg['fa_size'], $cfg['fa_ext']);
				}
			} else {

				if ($do=='newthread'){
					//NEW THREAD
					$title          = htmlspecialchars($_POST['title'], ENT_QUOTES, 'cp1251');
					$description    = htmlspecialchars($_POST['description'], ENT_QUOTES, 'cp1251');
												
					if($title && $message){	
						$sql = "INSERT INTO cms_forum_threads (forum_id, user_id, title, description, icon, pubdate, hits)
								VALUES ('$id', '".$inUser->id."', '$title', '$description', '', NOW(), 0)";
						$inDB->query($sql);

						$threadlastid = dbLastId('cms_forum_threads');
						
						$sql = "INSERT INTO cms_forum_posts (thread_id, user_id, pubdate, editdate, edittimes, content)
								VALUES ('$threadlastid', '".$inUser->id."', NOW(), NOW(), 0, '$message')";
						$inDB->query($sql);
						
						cmsUser::checkAwards($inUser->id);
						
						$lastid = dbLastId('cms_forum_posts');
						$inCore->registerUploadImages(session_id(), $lastid, 'forum');
						
						//subscribe thread
						if ($_POST['subscribe']){
							cmsUser::isSubscribed($inUser->id, 'forum', $lastid);
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
                        if (!$file_error){
                            header('location:/forum/'.$menuid.'/thread'.$threadlastid.'.html');
                        } else {
                            uploadError($menuid, $threadlastid, $post_id, $cfg['fa_size'], $cfg['fa_ext']);
                        }

					} else {
						echo '<p>'.$_LANG['NEED_TITLE_THREAD_YOUR_POST'].'</p>';
					}
				} else { //edit post
					if($message){
						$sql = "UPDATE cms_forum_posts 
								SET content = '$message',
									editdate = NOW(),
									edittimes = edittimes + 1
								WHERE id = $id";
						$inDB->query($sql) ;
						$inCore->registerUploadImages(session_id(), $id, 'forum');						
						header('location:/forum/'.$menuid.'/thread'.$msg['thread_id'].'.html');
					} else { echo '<p>'.$_LANG['NEED_TEXT_POST'].'</p>'; }
				}
			}
		}
		
	} else { usrNeedReg(); } //usrCheckAuth
	
}
///////////////////////////// DELETE POST /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='deletepost'){
	if (usrCheckAuth()){
		$model->deletePost($id, $inUser->id);
	}	
	header('location:'.$_SERVER['HTTP_REFERER']);	
}
///////////////////////////// MOVE THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='movethread'){
	if (usrCheckAuth()){
		if ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
			
			if (!isset($_POST['gomove'])){ //SHOW MOVE FORM
						
				$inPage->setTitle($_LANG['MOVE_THREAD']);
				$inPage->addPathway($_LANG['MOVE_THREAD'], $_SERVER['REQUEST_URI']);

				echo '<div class="con_heading">'.$_LANG['MOVE_THREAD'].'</div>';
					
				$sql = "SELECT * FROM cms_forum_threads WHERE id = $id LIMIT 1";
				$result = $inDB->query($sql) ;
			
				if ($inDB->num_rows($result)>0){
					$t = $inDB->fetch_assoc($result);											
					
					echo '<div style="margin-top:10px"><strong>'.$_LANG['THREAD'].':</strong> <a href="/forum/'.$menuid.'/thread'.$t['id'].'.html">'.$t['title'].'</a></div>';
					echo '<div><form action="" method="POST">';
					echo '<table border="0" cellspacing="10" style="background-color:#EBEBEB"><tr><td valign="top">'.$_LANG['MOVE_THREAD_IN_FORUM'].':</td>';
					
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
					
				} else { echo $_LANG['THREAD_NOT_FOUND']; }
			} else { //DO MOVE
			
				if (@$_POST['forum_id']){				
					$fid = intval($_POST['forum_id']);
					if (usrCheckAuth() && $inCore->userIsAdmin(@$inUser->id) || $inCore->isUserCan('forum/moderate')){		
						$inDB->query("UPDATE cms_forum_threads SET forum_id = $fid WHERE id = $id") ;
					}									
				}
				header('location:/forum/'.$menuid.'/'.$fid);			
			}
			
		} else { usrAccessDenied(); }
	} else { usrAccessDenied(); }

}
///////////////////////////// RENAME THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='renamethread'){
	if (usrCheckAuth()){
		if ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
			
			if (!isset($_POST['gorename'])){ //SHOW MOVE FORM
						
				$inPage->setTitle($_LANG['RENAME_THREAD']);
				$inPage->addPathway($_LANG['RENAME_THREAD'], $_SERVER['REQUEST_URI']);

				echo '<div class="con_heading">'.$_LANG['RENAME_THREAD'].'</div>';
					
				$sql = "SELECT * FROM cms_forum_threads WHERE id = $id LIMIT 1";
				$result = $inDB->query($sql) ;
			
				if ($inDB->num_rows($result)>0){
					$t = $inDB->fetch_assoc($result);											
					
					echo '<div style="margin-top:10px"><strong>'.$_LANG['THREAD'].':</strong> <a href="/forum/'.$menuid.'/thread'.$t['id'].'.html">'.$t['title'].'</a></div>';
					
					echo '<div style="margin-top:5px"><form action="" method="POST">';
					
					echo '<table border="0" cellspacing="10" style="background-color:#EBEBEB">
						  <tr><td valign="top">'.$_LANG['THREAD_TITLE'].':</td>';
					
						echo '<td valign="top">
								<input type="hidden" name="tid" value="'.$t['id'].'"/>
								<input type="text" size="45" value="'.$t['title'].'" name="title" id="title"/>
							 </td>';

					echo '</tr><tr><td valign="top">'.$_LANG['DESCRIPTION'].':</td>';
					
						echo '<td valign="top">
								<input type="text" size="45" value="'.$t['description'].'" name="description" id="title"/>
							 </td>';
						
					echo '</tr></table>';
					
					echo '<div style="margin-top:5px"><input type="submit" name="gorename" value="'.$_LANG['SAVE'].'"/> <input type="button" onclick="window.history.go(-1);" name="cancel" value="'.$_LANG['CANCEL'].'"/></div></form></div>';
					
				} else { echo $_LANG['THREAD_NOT_FOUND']; }
			} else { //DO RENAME
			
				if (@$_POST['title']){				
					$title = htmlspecialchars($_POST['title']);
					$description = htmlspecialchars($_POST['description']);
					$tid = intval($_POST['tid']);
					if (usrCheckAuth() && $inCore->userIsAdmin(@$inUser->id) || $inCore->isUserCan('forum/moderate')){		
						$inDB->query("UPDATE cms_forum_threads SET title = '$title', description = '$description' WHERE id = $id") 	;
					}									
				}
				header('location:/forum/'.$menuid.'/thread'.$tid.'.html');			
			}
			
		} else { usrAccessDenied(); }
	} else { usrAccessDenied(); }
}
///////////////////////////// DELETE THREAD /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='deletethread'){
	if (usrCheckAuth()){
		$forum_id = dbGetField('cms_forum_threads', 'id='.$id, 'forum_id');
        $model->deleteThread($id, $inUser->id);
	}	
	header('location:/forum/'.$menuid.'/'.$forum_id);
}
///////////////////////////// PIN/UNPIN THREAD ////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='pin'){
	if (isset($_GET['pinned'])) { $pinned = intval($_GET['pinned']); } else { header('location:'.$_SERVER['HTTP_REFERER']); }
	
	if (usrCheckAuth()){
		$sql = "SELECT * FROM cms_forum_threads WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
			$msg = $inDB->fetch_assoc($result);
			if ($msg['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
				$inDB->query("UPDATE cms_forum_threads SET pinned = '$pinned' WHERE id = $id") ;
			}
		}	
	}	
	header('location:'.$_SERVER['HTTP_REFERER']);	
}
///////////////////////////// SUBSCRIBE THREAD ////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='subscribe'){
	if (isset($_GET['subscribe'])) { $subscribe = intval($_GET['subscribe']); } else { header('location:'.$_SERVER['HTTP_REFERER']); }
	
	if (usrCheckAuth()){
		cmsUser::isSubscribed($inUser->id, 'forum', $id, $subscribe);
	}	
	
	header('location:'.$_SERVER['HTTP_REFERER']);	
}
///////////////////////////// CLOSE/OPEN THREAD ////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='close'){
	if (isset($_GET['closed'])) { $closed = intval($_GET['closed']); } else { header('location:'.$_SERVER['HTTP_REFERER']); }
	
	if (usrCheckAuth()){
		$sql = "SELECT * FROM cms_forum_threads WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
			$msg = $inDB->fetch_assoc($result);
			if ($msg['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('forum/moderate')){
				$inDB->query("UPDATE cms_forum_threads SET closed = '$closed' WHERE id = $id") ;
			}
		}	
	}	
	header('location:'.$_SERVER['HTTP_REFERER']);	
}
///////////////////////////// ATTACHED FILE DOWNLOAD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='download'){

	$sql = "SELECT * FROM cms_forum_files WHERE id = $id";
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

	$file = uploadDelete($menuid, $id);	
	header('location:/forum/'.$menuid.'/thread'.$file['tid'].'.html#'.$file['post_id']);

}
///////////////////////////// ATTACHED FILE RELOADING /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='reloadfile'){

	if(usrCheckAuth()){
		//get current file data	
		$sql = "SELECT f.*, p.thread_id as tid, u.id as uid, DATE_FORMAT(f.pubdate, '%d-%m-%Y � %H:%i') as fpubdate
				FROM cms_forum_files f, cms_users u, cms_forum_posts p
				WHERE f.id = $id AND f.post_id = p.id AND p.user_id = u.id";
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
		//if file found		
	
			$inPage->addPathway($_LANG['RELOAD_FILE'], $_SERVER['REQUEST_URI']);
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
						
						//check file size
						if ($filesize <= $cfg['fa_size']*1024){
								
							//check file extension is allowed									
							if ( (!$cfg['fa_ext_not'] && strstr($cfg['fa_ext'], $ext)) || ($cfg['fa_ext_not'] && !strstr($cfg['fa_ext'], $ext))){									
								$name = basename($filename, '.' . $path_parts['extension']);
										
								$filename = $name . '_' . substr(session_id(), 0, 5) . '.' . $ext;
								$destination = $_SERVER['DOCUMENT_ROOT']."/upload/forum/post".$post_id."/".$filename;
								
								@unlink($_SERVER['DOCUMENT_ROOT']."/upload/forum/post".$post_id."/".$file['filename']);
								
								move_uploaded_file($tmp_name, $destination);
								
								$sql = "UPDATE cms_forum_files SET filename = '$filename', filesize = '$filesize', pubdate=NOW(), hits=0 WHERE id = $id";									
								$inDB->query($sql) ;
								
								header('location:/forum/'.$menuid.'/thread'.$file['tid'].'.html#'.$file['post_id']);
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
			
			} else 	{ usrAccessDenied(); }
		} else { echo '<p>'.$_LANG['FILE_NOT_FOUND'].'!</p>'; }
	
	
	} else { usrAccessDenied(); }

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //function
?>