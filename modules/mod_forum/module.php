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

function mod_forum($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);
		if (!isset($cfg['showtype'])) { $cfg['showtype']='web2'; }
		if (!isset($cfg['showforum'])) { $cfg['showforum']='0'; }
		if (!isset($cfg['showlink'])) { $cfg['showlink']='0'; }
		if (!isset($cfg['forum_id'])) { $cfg['forum_id']='0'; }
		if (!isset($cfg['cat_id'])) { $cfg['cat_id']='0'; }
		if (!isset($cfg['subs'])) { $cfg['subs'] = 1; }
		if (!isset($cfg['show_hidden'])) { $cfg['show_hidden'] = 0; }
		if (!isset($cfg['show_pinned'])) { $cfg['show_pinned'] = 0; }
        $forumcfg = $inCore->loadComponentConfig('forum');

		// категории форумов
		$cats_sql = $cfg['cat_id'] ? "INNER JOIN cms_forum_cats cat ON cat.id = f.category_id AND cat.id = '{$cfg['cat_id']}'" : '';

		// форумы
		$catsql = '';
		if($cfg['forum_id']){
			if (!$cfg['subs']){
				$catsql = " AND t.forum_id = '{$cfg['forum_id']}'";
			} else {
				$rootcat = $inDB->get_fields('cms_forums', "id='{$cfg['forum_id']}'", 'NSLeft, NSRight');
				if(!$rootcat) { return false; }
				$catsql = " AND (f.NSLeft >= {$rootcat['NSLeft']} AND f.NSRight <= {$rootcat['NSRight']})";
			}
		}

		$where  = '';
		$where .= $cfg['show_hidden'] ? '1=1' : 't.is_hidden = 0';
		$where .= $cfg['show_pinned'] ? ' AND t.pinned = 1' : '';

		$tsql = "SELECT t.id, t.forum_id, t.user_id, t.title, t.description, t.description, t.pubdate, t.hits, t.closed, t.pinned, f.id as fid, f.title as forum,
						u.id as uid, u.nickname as starter, u.login as login
						FROM cms_forum_threads t
						INNER JOIN cms_forums f ON f.id = t.forum_id {$catsql}
						{$cats_sql}
						INNER JOIN cms_users u ON u.id = t.user_id
						WHERE {$where}
						ORDER BY t.pubdate DESC
						LIMIT ".$cfg['shownum'];
		
		$result = $inDB->query($tsql) ;
		
		if (!$inDB->num_rows($result)){ echo '<p>'.$_LANG['FORUM_NOT_THREAD'].'</p>'; return; }

		include_once(PATH.'/components/forum/includes/forumcore.php');

		$threads = array();

		while ($t = $inDB->fetch_assoc($result)){

			$t['postsnum'] = $inDB->rows_count('cms_forum_posts', "thread_id = '{$t['id']}'");
			$pages = ceil($t['postsnum'] / $forumcfg['pp_thread']);
			$lastmessage = threadLastMessageData($t['id']);

			$t['date']         = $lastmessage['date'];
			$t['author']       = $lastmessage['user'];
			$t['authorhref']   = cmsUser::getProfileURL($lastmessage['login']);
			$t['starterhref']  = cmsUser::getProfileURL($t['login']);
			$t['topic']        = ucfirst($t['title']);
			$t['topicdesc']    = ucfirst($t['description']);
			$t['topichref']    = '/forum/thread'.$t['id'].'-'.$pages.'.html#'.$lastmessage['id'];
			$t['forum']        = ucfirst($t['forum']);
			$t['forumhref']    = '/forum/'.$t['fid'];
			$t['closed']       = $t['closed'];

			$t['secret']       = 0;
			if ($t['auth_group']>0) {
				$t['secret']   = 1;
			}

			if ($cfg['showtype'] == 'web2'){
				$t['msg'] = strip_tags($inCore->parseSmiles($lastmessage['msg'], true));
			}

			if ($t['postsnum']==1) {
				$t['act'] = $_LANG['FORUM_START_THREAD'];
			} else { 
				$t['act'] = $_LANG['FORUM_REPLY_THREAD'];
			}
			$threads[] = $t;
		}
	
		$smarty = $inCore->initSmarty('modules', 'mod_forum_'.$cfg['showtype'].'.tpl');			
		$smarty->assign('threads', $threads);
		$smarty->assign('cfg', $cfg);				
		$smarty->display('mod_forum_'.$cfg['showtype'].'.tpl');	

		return true;	
	}
?>