<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	$inPage->backButton(false); 
    $opt = $inCore->request('opt', 'str', 'in');
    $with_id = $inCore->request('with_id', 'int', 0);
	
	if (usrCheckAuth()){
        global $_LANG;
		//current page
		$perpage = 15;
		$page = $inCore->request('cpage', 'int', 1);

		if ($opt=='in'){
			//how many records
			$sql = "SELECT m.id	FROM cms_user_msg m WHERE m.to_id = $id";	
			$result = $inDB->query($sql) ;
			$msg_count = $inDB->num_rows($result);
			//sql
					$sql = "SELECT m.*, m.senddate as fpubdate, m.from_id as sender_id, u.nickname as author, u.login as author_login, u.is_deleted, p.imageurl
					FROM cms_user_msg m
					LEFT JOIN cms_users u ON m.from_id = u.id
					LEFT JOIN cms_user_profiles p ON m.from_id = p.user_id
					WHERE m.to_id = $id
					ORDER BY senddate DESC
					LIMIT ".(($page-1)*$perpage).", $perpage";	
		} else {
			if ($opt=='out'){
				//how many records
				$sql = "SELECT m.id	FROM cms_user_msg m, cms_users u WHERE m.from_id = $id AND m.to_id = u.id";	
				$result = $inDB->query($sql) ;
				$msg_count = $inDB->num_rows($result);
				//sql
				$sql = "SELECT m.*, u.nickname as author, u.login as author_login, m.senddate as fpubdate, m.to_id as sender_id, u.is_deleted, p.imageurl
						FROM cms_user_msg m, cms_users u, cms_user_profiles p
						WHERE m.from_id = $id AND m.to_id = u.id AND m.to_id = p.user_id
						ORDER BY senddate DESC
						LIMIT ".(($page-1)*$perpage).", $perpage";							
			}
			if ($opt=='history'){
				$with_name = dbGetField('cms_users', "id = $with_id", 'nickname');
				//how many records
				$sql = "SELECT m.id
						FROM cms_user_msg m, cms_users u
						WHERE ((m.from_id = $id AND m.to_id = $with_id) OR (m.from_id = $with_id AND m.to_id = $id)) AND m.from_id = u.id";
				$result = $inDB->query($sql) ;
				$msg_count = $inDB->num_rows($result);
				//sql		
				$sql = "SELECT m.*, u.nickname as author, u.login as author_login, m.senddate as fpubdate, m.from_id as sender_id, u.is_deleted, p.imageurl
						FROM cms_user_msg m, cms_users u, cms_user_profiles p
						WHERE ((m.from_id = $id AND m.to_id = $with_id) OR (m.from_id = $with_id AND m.to_id = $id)) AND m.from_id = u.id AND m.from_id = p.user_id
						ORDER BY senddate DESC
						LIMIT ".(($page-1)*$perpage).", $perpage";							
			}
		}

		$result = $inDB->query($sql);

			if ($opt=='in'){				
				$inPage->addPathway($_LANG['INBOX']);
			} elseif ($opt=='out') {
				$inPage->addPathway($_LANG['SENT']);
			} elseif ($opt=='new') {
				$inPage->addPathway($_LANG['NEW_MESS']);
			} elseif ($opt=='history') {
				$inPage->addPathway($_LANG['MESSEN_WITH'].' '.$with_name, $_SERVER['REQUEST_URI']);
			}
		
		if ($opt=='in' || $opt=='out' || $opt=='history'){
				
			if ($msg_count > $perpage){
				if ($opt=='in'){
				$pagebar = cmsPage::getPagebar($msg_count, $page, $perpage, '/users/%user_id%/messages%page%.html', array('user_id'=>$id));
				} elseif ($opt=='out') {
				$pagebar = cmsPage::getPagebar($msg_count, $page, $perpage, '/users/%user_id%/messages-sent%page%.html', array('user_id'=>$id));
				} elseif ($opt=='history') {
				$pagebar = cmsPage::getPagebar($msg_count, $page, $perpage, '/users/%user_id%/messages-history%to_id%-%page%.html', array('user_id'=>$id, 'to_id'=>$with_id));
				}
			}
			
			$is_mes	= false;
			if ($inDB->num_rows($result)){
					$is_mes	= true;
					$records = array();
					while($record = $inDB->fetch_assoc($result)){
	
						if($record['sender_id']>0){ 
							$record['authorlink'] = '<a href="'.cmsUser::getProfileURL($record['author_login']).'">'.$record['author'].'</a>';
						} else {
							if ($record['sender_id']==USER_UPDATER){
								$record['authorlink'] = $_LANG['SERVICE_UPDATE'];
							}
							if ($record['sender_id']==USER_MASSMAIL){
								$record['authorlink'] = $_LANG['SERVICE_MAILING'];
							}
						}										

						$record['fpubdate'] = $inCore->dateFormat($record['fpubdate'], true, true, true);
						if ($record['is_new']){
							if ($opt=='in'){
								//erase new mark
								$inDB->query("UPDATE cms_user_msg SET is_new = 0 WHERE id = ".$record['id']);
							} 
						} 
						$record['message'] = $inCore->parseSmiles($record['message'], true);
						$record['message'] = str_replace('&gt;', '>', $record['message']);
						$record['message'] = str_replace('&lt;', '<', $record['message']);					
						$record['message'] = str_replace('&amp;', '&', $record['message']);					
						$record['message'] = strip_tags($record['message'], '<img><br><a><b><u><i><table><tr><td><th><h1><h2><h3><div><span><pre>');
						
						if ($record['sender_id']>0){
							$record['user_img'] = '<a href="'.cmsUser::getProfileURL($record['author_login']).'">'.usrImageNOdb($record['sender_id'], 'small', $record['imageurl'], $record['is_deleted'], $record['author_login']).'</a>';
						} else {
							$record['user_img'] = usrImageNOdb($record['sender_id'], 'small', $record['imageurl'], $record['is_deleted'], $record['author_login']);
						}
						$records[] = $record;
					}
			} 
	
		}

		if ($opt=='new'){
			$inPage->addHeadJS('components/users/js/newmessage.js');
			$user_opt = cmsUser::getFriendsList($inUser->id);
			$bb_toolbar = cmsPage::getBBCodeToolbar('message');
			$bb_smiles	= cmsPage::getSmilesPanel('message');
		}
		$smarty = $inCore->initSmarty('components', 'com_users_messages.tpl');
		$smarty->assign('opt', $opt);
		$smarty->assign('is_mes', $is_mes);
		$smarty->assign('id', $id);
		$smarty->assign('with_name', $with_name);
		$smarty->assign('msg_count', $msg_count);
		$smarty->assign('pagebar', $pagebar);
		$smarty->assign('perpage', $perpage);
		$smarty->assign('user_opt', $user_opt);
		$smarty->assign('is_admin', $inUser->is_admin);
		$smarty->assign('bb_toolbar', $bb_toolbar);
		$smarty->assign('bb_smiles', $bb_smiles);
		$smarty->assign('usr_id', $inUser->id);
		$smarty->assign('records', $records);
		$smarty->display('com_users_messages.tpl');
}

?>