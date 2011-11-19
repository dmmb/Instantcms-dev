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
	$inPage->backButton(false); 
    $opt = $inCore->request('opt', 'str', 'in');
    $with_id = $inCore->request('with_id', 'int', 0);
	
	if (!usrCheckAuth()){ exit; }

    global $_LANG;

    $perpage = 15;
    $page = $inCore->request('cpage', 'int', 1);

    switch ($opt){

        case 'in':

                $inPage->addPathway($_LANG['INBOX']);

                //Количество записей
                $msg_count = $inDB->rows_count('cms_user_msg', "to_id = '$id' AND to_del = 0");

                // Пагинация
                $pagebar = ($msg_count > $perpage) ? cmsPage::getPagebar($msg_count, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/messages%page%.html\')') : '';

                $sql = "SELECT m.*, m.senddate as fpubdate, m.from_id as sender_id, u.nickname as author, u.login as author_login, u.is_deleted, p.imageurl
                        FROM cms_user_msg m
                        LEFT JOIN cms_users u ON u.id = m.from_id
                        LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                        WHERE m.to_id = '$id' AND m.to_del = 0
                        ORDER BY m.id DESC
                        LIMIT ".(($page-1)*$perpage).", $perpage";

                        break;

        case 'out':

                $inPage->addPathway($_LANG['SENT']);

                //Количество записей
                $msg_count = $inDB->rows_count('cms_user_msg m, cms_users u', "m.from_id = '$id' AND m.to_id = u.id AND m.from_del=0");
                
                // Пагинация
                $pagebar = ($msg_count > $perpage) ? cmsPage::getPagebar($msg_count, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/messages-sent%page%.html\')') : '';

                $sql = "SELECT m.*, u.nickname as author, u.login as author_login, m.senddate as fpubdate, m.to_id as sender_id, u.is_deleted, p.imageurl
                        FROM cms_user_msg m
                        INNER JOIN cms_users u ON u.id = m.to_id
                        INNER JOIN cms_user_profiles p ON p.user_id = u.id
                        WHERE m.from_id = '$id' AND m.from_del=0
                        ORDER BY m.id DESC
                        LIMIT ".(($page-1)*$perpage).", $perpage";

                        break;

        case 'history':

                $with_name = $inDB->get_field('cms_users', "id = $with_id", 'nickname');
                $inPage->addPathway($_LANG['MESSEN_WITH'].' '.$with_name, $_SERVER['REQUEST_URI']);

                //Количество записей
                $msg_count = $inDB->rows_count('cms_user_msg m', "((m.from_id = $id AND from_del=0) OR (m.from_id = $with_id AND to_del=0)) AND ((m.to_id = $id AND to_del=0) OR (m.to_id = $with_id AND from_del=0))");

                // Пагинация
                $pagebar = ($msg_count > $perpage) ? cmsPage::getPagebar($msg_count, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/messages-history'.$with_id.'-%page%.html\')') : '';

                $sql = "SELECT m.*, u.nickname as author, u.login as author_login, m.senddate as fpubdate, m.from_id as sender_id, u.is_deleted, p.imageurl
                        FROM cms_user_msg m
                        INNER JOIN cms_users u ON u.id = m.from_id
                        INNER JOIN cms_user_profiles p ON p.user_id = u.id

                        WHERE ((m.from_id = $id AND from_del=0) OR (m.from_id = $with_id AND to_del=0)) AND
                              ((m.to_id   = $id AND to_del=0) OR (m.to_id = $with_id AND from_del=0))

                        ORDER BY m.id DESC
                        LIMIT ".(($page-1)*$perpage).", $perpage";

                        break;

        case 'new':

                $inPage->addPathway($_LANG['NEW_MESS']);
                $inPage->addHeadJS('components/users/js/newmessage.js');
                $user_opt = cmsUser::getFriendsList($inUser->id);
                $bb_toolbar = cmsPage::getBBCodeToolbar('message');
                $bb_smiles = cmsPage::getSmilesPanel('message');

                $groups = array();

                if ($inUser->is_admin){ $groups = cmsUser::getGroups(true); }

                break;
                
    }

    if ($opt=='in' || $opt=='out' || $opt=='history'){

        $result = $inDB->query($sql);

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
                    $record['message'] = $record['message'];

                    if ($record['sender_id']>0){
                        $record['user_img'] = '<a href="'.cmsUser::getProfileURL($record['author_login']).'">'.usrImageNOdb($record['sender_id'], 'small', $record['imageurl'], $record['is_deleted'], $record['author_login']).'</a>';
                    } else {
                        $record['user_img'] = usrImageNOdb($record['sender_id'], 'small', $record['imageurl'], $record['is_deleted'], $record['author_login']);
                    }
					
					$record['is_online'] = $inUser->isOnline($record['sender_id']);
					
                    $records[] = $record;
                }
        }

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
    $smarty->assign('usr_id', $inUser->id);
    $smarty->assign('records', $records);

    if ($opt=='new'){
        $smarty->assign('bb_toolbar', $bb_toolbar);
        $smarty->assign('bb_smiles', $bb_smiles);
        $smarty->assign('groups', $groups);
    }
    
    $smarty->display('com_users_messages.tpl');
	if ($inCore->inRequest('of_ajax')) { echo ob_get_clean(); exit; }

?>