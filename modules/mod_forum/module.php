<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_forum($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		$cfg = $inCore->loadModuleConfig($module_id);
		
        $forumcfg = $inCore->loadComponentConfig('forum');

		if (!function_exists('forumUserAuthSQL') && !function_exists('threadLastMessageData')){ //if not included earlier
			include_once(PATH.'/components/forum/includes/forumcore.php');
		}
		
		$groupsql = forumUserAuthSQL('f.');
		
		$tsql = "SELECT t.*, COUNT(p.id) as postsnum, f.id as fid, f.title as forum, u.id as uid, u.nickname as starter, u.login as login, f.auth_group as auth_group
						 FROM cms_forum_threads t, cms_forum_posts p, cms_forums f, cms_users u
						 WHERE p.thread_id = t.id AND t.user_id = u.id AND t.forum_id = f.id $groupsql
						 GROUP BY p.thread_id
						 ORDER BY t.pubdate DESC
						 LIMIT ".$cfg['shownum'];		
		
		$result = $inDB->query($tsql) ;
		
		if ($inDB->num_rows($result)){
			if (!isset($cfg['showtype'])) { $cfg['showtype']='web2'; }
			if (!isset($cfg['showforum'])) { $cfg['showforum']='0'; }
			if (!isset($cfg['showlink'])) { $cfg['showlink']='0'; }
		
				$threads = array();

				while ($t = $inDB->fetch_assoc($result)){
					$next = sizeof($threads);							

					$pages = ceil($t['postsnum'] / $forumcfg['pp_thread']);				
					$lastmessage = threadLastMessageData($t['id']);							

					$threads[$next]['date']         = strip_tags($lastmessage['date']);
					$threads[$next]['author']       = $lastmessage['user'];
					$threads[$next]['authorhref']   = cmsUser::getProfileURL($lastmessage['login']);
					$threads[$next]['starter']      = $t['starter'];
					$threads[$next]['starterhref']  = cmsUser::getProfileURL($t['login']);
					$threads[$next]['topic']        = ucfirst($t['title']);
					$threads[$next]['topicdesc']    = ucfirst($t['description']);
					$threads[$next]['topichref']    = '/forum/thread'.$t['id'].'-'.$pages.'.html#new';
					$threads[$next]['forum']        = ucfirst($t['forum']);
					$threads[$next]['forumhref']    = '/forum/'.$t['fid'];

                    $threads[$next]['secret']       = 0;
                    if ($t['auth_group']>0) {
                        $threads[$next]['secret']   = 1;
                    }

					if (strlen($lastmessage['msg'])>70) { $lastmessage['msg'] = substr($lastmessage['msg'], 0, 70).'...'; }

                    $msg = $lastmessage['msg'];

                    if ($cfg['showtype'] == 'web2'){
                        $msg = strip_tags($inCore->parseSmiles($msg, true));
                        $msg = str_replace("&amp;", '&', $msg);
                    }

                    $threads[$next]['msg'] = $msg;

					if ($t['postsnum']==1) {
						$threads[$next]['act'] = $_LANG['FORUM_START_THREAD'];
					} else { 
						$threads[$next]['act'] = $_LANG['FORUM_REPLY_THREAD'];
					}										
				}
			
				$smarty = $inCore->initSmarty('modules', 'mod_forum_'.$cfg['showtype'].'.tpl');			
				$smarty->assign('threads', $threads);
				$smarty->assign('cfg', $cfg);				
				$smarty->display('mod_forum_'.$cfg['showtype'].'.tpl');	
														
		} else { echo '<p>'.$_LANG['FORUM_NOT_THREAD'].'</p>'; }

		return true;	
	}
?>