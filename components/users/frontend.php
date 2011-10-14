<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function karmaPoints($points){
	if ($points > 0){
		return '<span style="font-size:24px;color:green">+'.$points.'</span>';
	} else {
		return '<span style="font-size:24px;color:red">'.$points.'</span>';
	}
	return;
}

function pageSelectFiles($records, $current, $perpage){
    $inDB   = cmsDatabase::getInstance();
	$html = '';
    global $_LANG;
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<td width="60"><strong>'.$_LANG['PAGE'].': </strong></span></td>';

			if ($current>2){
				$html .= '<td width="16"><a href="javascript:goPage('.(1-$current).')" title="'.$_LANG['FIRST'].'"><img src="/images/icons/first.gif" border="0"/></a></td>';
			}
			if ($current>1) { 
				$html .= '<td width="16"><a href="javascript:goPage(-1)" title="'.$_LANG['PREVIOUS'].'"><img src="/images/icons/prev.gif" border="0"/></a></td>';
			}

			$html .= '<td width="40" align="center"><form style="margin:0px;padding:0px" action="" name="pageform" method="POST">';

			$html .= '<select style="width:40px" name="page" onchange="goToPage()">';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$html .= '<option value="'.$p.'">'.$p.'</option>';		
				} else {
					$html .= '<option value="'.$p.'" selected>'.$p.'</option>';		
				}
			}
			$html .= '</select></form></td>';

			if ($current<$pages) { 
				$html .= '<td width="16"><a href="javascript:goPage(+1)" title="'.$_LANG['NEXT'].'"><img src="/images/icons/next.gif" border="0"/></a></td>';
			}
			if ($current<$pages-1){
				$html .= '<td width="16"><a href="javascript:goPage('.($pages-$current).')" title="'.$_LANG['LAST'].'"><img src="/images/icons/last.gif" border="0"/></a></td>';
			}
		}
	}
	return $html;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function users(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
    global $_LANG;
	$inCore->includeFile('components/users/includes/usercore.php');
	$inCore->includeFile('components/users/includes/userforms.php');
	
    $inCore->loadModel('users');
    $model = new cms_model_users();

    $cfg = $inCore->loadComponentConfig('users');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }
    $inCore->loadLanguage('components/users');

    if (!isset($cfg['showgroup'])) { $cfg['showgroup']  = 0; }
    if (!isset($cfg['sw_feed']))   { $cfg['sw_feed']    = 1; }
    if (!isset($cfg['sw_content'])){ $cfg['sw_content'] = 1; }
    if (!isset($cfg['sw_awards'])) { $cfg['sw_awards']  = 1; }
    if (!isset($cfg['sw_search'])) { $cfg['sw_search'] = 1;  }
    if (!isset($cfg['sw_guest']))  { $cfg['sw_guest'] = 1; }
	
    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();
	
	$id     =   $inCore->request('id', 'int', 0);
	$do     =   $inCore->request('do', 'str', 'view');

    $inPage->setTitle($_LANG['USERS']);
	
/////////////////////////////// SEARCH BY CITY ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='city'){

	$city = urldecode($inCore->request('city', 'str', ''));

	$querysql = "SELECT	        
				u.id as id,
				u.login,
				u.nickname,
				u.logdate as flogdate,
		        u.is_deleted as is_deleted,
                u.birthdate, u.rating,
				u.status as microstatus,
                p.city, p.karma as karma, p.imageurl, 
				p.gender as gender
                FROM cms_users u
				INNER JOIN cms_user_profiles p ON p.user_id = u.id
				WHERE u.is_deleted = 0 AND u.is_locked = 0 AND p.city LIKE '%$city%'
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>'.$_LANG['SEARCH_BY_CITY'].':</strong> '.htmlspecialchars($city).' (<a href="/users/all.html">'.$_LANG['CANCEL_SEARCH'].'</a>)</div>';
	
	$do = 'view';

}
/////////////////////////////// SEARCH BY HOBBY (description part) ///////////////////////////////////////////////////////////////////	
if ($do=='hobby'){

    $hobby = $inCore->request('hobby', 'str', '');

    $hobby = urldecode($hobby);

    $hobby = str_replace('\"', '"', $hobby);

    $hobby = strtolower($hobby);

	$querysql = "SELECT		        
				u.id as id,
				u.login,
				u.nickname,
				u.logdate as flogdate,
		        u.is_deleted as is_deleted,
                u.birthdate, u.rating,
				u.status as microstatus,
                p.city, p.karma as karma, p.imageurl, 
				p.gender as gender
                FROM cms_users u
				INNER JOIN cms_user_profiles p ON p.user_id = u.id
				WHERE u.is_deleted = 0 AND u.is_locked = 0
				AND (LOWER(p.description) LIKE '%$hobby%' OR LOWER(p.formsdata) LIKE '%$hobby%')
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>'.$_LANG['SEARCH_BY_HOBBY'].':</strong> '.htmlspecialchars($hobby).' (<a href="/users/all.html">'.$_LANG['CANCEL_SEARCH_SHOWALL'].'</a>)</div>';
	
	$do = 'view';

}
/////////////////////////////// SEARCH USER ///////////////////////////////////////////////////////////////////	
if ($do=='search'){
	$s = '';
	$stext = array();

	if ($_REQUEST['gender']){
		$val = $inCore->request('gender', 'str', 'm');
		if ($val=='m'){
			$s .= " AND p.gender = 'm'"; 
			$stext[] = $_LANG['MALE'];
		} elseif ($val=='f') {
			$s .= " AND p.gender = 'f'"; 
			$stext[] = $_LANG['FEMALE'];
		}
	}	
	if ($_REQUEST['agefrom']){
		$val = $inCore->request('agefrom', 'int', 18);
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) >= '.($val*365); 
		$stext[] = $_LANG['NOT_YOUNG']." $val ".$_LANG['YEARS'];
	}			
	if ($_REQUEST['ageto']){
		$val = $inCore->request('ageto', 'int');
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) <= '.($val*365); 
		$stext[] = $_LANG['NOT_OLD']." $val ".$_LANG['YEARS'];
	}

    if ($_REQUEST['name']){
		$val = $inCore->request('name', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(u.nickname) LIKE \'%'.$val.'%\'';
		$stext[] = $_LANG['NAME']." &mdash; ".$val;
	}

    if ($_REQUEST['city']){
		$val = $inCore->request('city', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(p.city) LIKE \''.$val.'%\'';
		$stext[] = $_LANG['CITY']." &mdash; ".$val;
	}

    if ($_REQUEST['hobby']){
		$val = $inCore->request('hobby', 'str', '');
        $val = strtolower($val);        
		$s .= ' AND (LOWER(p.description) LIKE \'%'.$val.'%\' OR LOWER(p.formsdata) LIKE \'%'.$val.'%\')';
		$stext[] = $_LANG['HOBBY']." &mdash; ".$val;
	}

	$querysql = "SELECT		        
				 u.id as id,
				 u.login,
				 u.nickname,
                 u.logdate as flogdate,
		         u.is_deleted as is_deleted,
                 u.birthdate, u.rating,
				 u.status as microstatus,
                 p.city, p.karma as karma, p.imageurl, 
				 p.gender as gender
                 FROM cms_users u
				 INNER JOIN cms_user_profiles p ON p.user_id = u.id
				 WHERE u.is_deleted = 0 AND u.is_locked = 0 $s
				 ORDER BY city DESC";

    echo '<pre>'.$sql.'</pre>';
	
	$querymsg = '<h3>'.$_LANG['SEARCH_RESULT'].'</h3>';
	
	if (sizeof($stext)){
		$querymsg .= '<ul>';
			foreach($stext as $value){
				$querymsg .= '<li>'.htmlspecialchars($value).';</li>';		
			}
		$querymsg .= '</ul>';
	}
	
	$do = 'view';

}

/////////////////////////////// VIEW USERS LIST ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='view'){
			
	$orderby = $inCore->request('orderby', 'str', 'regdate');
	$orderto = $inCore->request('orderto', 'str', 'desc');
	$page	 = $inCore->request('page',	'int', 1);	
	
	if($orderby != 'karma' && $orderby != 'rating') { $orderby = 'regdate'; }
	
	if ($orderto != 'asc' && $orderto != 'desc' ){ $orderto = 'desc'; }
	if ($page <= 0) { $page = 1; }
	
	if ($inCore->inRequest('online')) { $_SESSION['usr_online'] = $inCore->request('online', 'int'); $page = 1; }
	
	$perpage = 10;

    if ($cfg['sw_search']){
        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('citysearch', 'city', false);
    }

	$inPage->addHeadJS('components/users/js/view.js');
	
	if (!isset($querysql)){
		if (!@$_SESSION['usr_online']){

			$sql = "SELECT		        
				    u.id as id,
				    u.login,
				    u.nickname,
				    u.logdate as flogdate,
		            u.is_deleted as is_deleted,
                    u.birthdate, u.rating,
				    u.status as microstatus,
                    p.city, p.karma as karma, p.imageurl, 
				    p.gender as gender
                    FROM cms_users u
				    INNER JOIN cms_user_profiles p ON p.user_id = u.id
				    WHERE u.is_locked = 0 AND u.is_deleted = 0
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";
		} else {
		
		$sql = "SELECT		        
				    o.user_id as id,
				    u.login,
				    u.nickname,
					u.logdate as flogdate,
		            u.is_deleted as is_deleted,
                    u.birthdate, u.rating,
				    u.status as microstatus,
                    p.city, p.karma as karma, p.imageurl, 
				    p.gender as gender
				    FROM cms_online o
                    LEFT JOIN cms_users u ON  u.id = o.user_id
				    INNER  JOIN cms_user_profiles p ON p.user_id = u.id
				    WHERE u.is_locked = 0 AND u.is_deleted = 0
                    GROUP BY o.user_id
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";
		}
	} else {
		$sql = $querysql;
    }
    
	$result = $inDB->query($sql) ;

	$is_users  = $inDB->num_rows($result);
	
	$smarty = $inCore->initSmarty('components', 'com_users_view.tpl');			
	if (isset($querymsg)) { $smarty->assign('querymsg', $querymsg);	}
	$smarty->assign('page', $page);	
	
		$link['latest']    = '/users/';
		$link['positive']  = '/users/positive.html';
		$link['rating']    = '/users/rating.html';

        if($orderby=='regdate') { $link['selected'] = 'latest'; }
        if($orderby=='karma') { $link['selected'] = 'positive'; }
        if($orderby=='rating') { $link['selected'] = 'rating'; }

		$rownum = 0; $users = array();
		if ($is_users){
			while($usr = $inDB->fetch_assoc($result)){                
					$rownum++;
					$usr['avatar'] = usrLink(usrImageNOdb($usr['id'], 'small', $usr['imageurl'], $usr['is_deleted']), $usr['login'], $menuid);
					$usr['nickname'] = cmsUser::getProfileLink($usr['login'], $usr['nickname']);
					$usr['flogdate'] = $inCore->dateFormat($usr['flogdate'], true, true);
					$usr['status'] = usrStatusList($usr['id'], $usr['flogdate'], false, $usr['gender']);
					$usr['num'] = $rownum + ($page-1)*$perpage;

                    if (($orderby!='karma' || $orderto!='asc') || strip_tags($usr['karma'])<0){
                        $users[] = $usr;
                    }
			}
		}

        $is_users   = (sizeof($users)>0);

        $smarty->assign('is_users', $is_users);

        $smarty->assign('orderby', $orderby);
        $smarty->assign('orderto', $orderto);

		$smarty->assign('link', $link);		

        if (isset($autocomplete_js)){ $smarty->assign('autocomplete_js', $autocomplete_js); }

		$smarty->assign('users', $users);
		$smarty->assign('user_id', $inUser->id);	
		$smarty->assign('cfg', $cfg);	
		
		if (!isset($querysql)){
            if (!$_SESSION['usr_online']){
                $total = $model->getUserTotal();
            } else {
                $total = $model->getUserTotal(true);
            }

			$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/users/'.$link['selected'].'%page%.html'));
		}
	
	$smarty->display('com_users_view.tpl');		
	
}
/////////////////////////////// EDIT PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editprofile'){

	$opt = $inCore->request('opt', 'str', 'edit');

	if (usrCheckAuth()){
	
		if ($inUser->id==$id || $inCore->userIsAdmin($inUser->id)){
		
				if ($opt == 'save'){
					$errors = false;
					
					$nickname = $inCore->request('nickname', 'str');
                    if (strlen($nickname)<2) { cmsCore::addSessionMessage($_LANG['SHORT_NICKNAME'], 'error'); $errors = true; }
					$inCore->loadModel('registration');
					$modreg = new cms_model_registration();
					if (!$inCore->userIsAdmin($inUser->id)){
						if($modreg->getBadNickname($nickname)) { cmsCore::addSessionMessage($_LANG['ERR_NICK_EXISTS'], 'error'); $errors = true; }
					}

					$gender = $inCore->request('gender', 'str');
					
					$city = $inCore->request('city', 'str');
					if (strlen($city)>25) { cmsCore::addSessionMessage($_LANG['LONG_CITY_NAME'], 'error'); $errors = true; }

					$email = $inCore->request('email', 'str');
					if (!preg_match('/^([a-z0-9\._-]+)@([a-z0-9\._-]+)\.([a-z]{2,4})$/i', $email)) { cmsCore::addSessionMessage($_LANG['REALY_ADRESS_EMAIL'], 'error'); $errors = true; }
					
					$showmail       = $inCore->request('showmail', 'int');
					$email_newmsg   = $inCore->request('email_newmsg', 'int');
					
					$showbirth      = $inCore->request('showbirth', 'int');
					$description    = $inCore->request('description', 'str');
					
					$birthdate      = (int)$_REQUEST['birthdate']['year'].'-'.(int)$_REQUEST['birthdate']['month'].'-'.(int)$_REQUEST['birthdate']['day'];
					$signature      = $inCore->request('signature', 'str');

					$allow_who      = $inCore->request('allow_who', 'str');
                                        if (!preg_match('/^([a-zA-Z]+)$/i', $allow_who)) { $errors = true; }

					$icq            = $inCore->request('icq', 'str');
                                         $icq            = preg_replace('/([^0-9])/i', '', $icq);
                    
					$showicq        = $inCore->request('showicq', 'int');
					
					$cm_subscribe   = $inCore->request('cm_subscribe', 'str');
					if (!preg_match('/^([a-zA-Z]+)$/i', $cm_subscribe)) { $errors = true; }
					
					if ($inCore->inRequest('field')){
						foreach($_POST['field'] as $k=>$val){
							$_POST['field'][$k] = $inCore->strClear($_POST['field'][$k]);
						}					
						$formsdata = $inCore->arrayToYaml($_POST['field']);
						$forms_sql = ", formsdata='$formsdata'";
					} else {
						$forms_sql = '';
					}
					
					if (!$errors){
                       
						$sql = "UPDATE cms_user_profiles 
						 		SET city = '$city',
									description = '$description',
									showmail='$showmail',
									showbirth='$showbirth',
									showicq='$showicq',
									allow_who='$allow_who',
									signature='$signature',
									gender='$gender' $forms_sql,
									email_newmsg='$email_newmsg',
									cm_subscribe='$cm_subscribe'
								WHERE user_id = $id";
						$inDB->query($sql) ;

						$sql = "UPDATE cms_users 
								SET birthdate='$birthdate',
									email='$email',
									icq='$icq',
									nickname='$nickname'
								WHERE id = $id";
						$inDB->query($sql) ;

                        cmsCore::addSessionMessage($_LANG['PROFILE_SAVED'], 'info');
					$inCore->redirect(cmsUser::getProfileURL($inUser->login));
					}
                    
				}				
				
				if ($opt == 'changepass'){
					$errors = false;

					$oldpass 	= $inCore->request('oldpass', 'str');
					$newpass 	= $inCore->request('newpass', 'str');
					$newpass2 	= $inCore->request('newpass2', 'str');
					
					if ($inUser->password != md5($oldpass)) { cmsCore::addSessionMessage($_LANG['OLD_PASS_WRONG'], 'error'); $errors = true;}
					if ($newpass != $newpass2) { cmsCore::addSessionMessage($_LANG['WRONG_PASS'], 'error'); $errors = true; }
					if($oldpass && $newpass && $newpass2 && strlen($newpass )<6) { cmsCore::addSessionMessage($_LANG['PASS_SHORT'], 'error'); $errors = true; }

					if (!$errors){
						$sql = "UPDATE cms_users SET password='".md5($newpass)."' WHERE id = $id AND password='".md5($oldpass)."'";
						$inDB->query($sql);
						cmsCore::addSessionMessage($_LANG['PASS_CHANGED'], 'info');
					$inCore->redirect(cmsUser::getProfileURL($inUser->login));
				}
				}
		
		
			$sql = "SELECT u.*, p.*, u.id as id, 
							DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, 
							DATE_FORMAT(u.logdate, '%d-%m-%Y') as flogdate,
							DATE_FORMAT(u.birthdate, '%d') as bday,
							DATE_FORMAT(u.birthdate, '%m') as bmonth,
							DATE_FORMAT(u.birthdate, '%Y') as byear,
							IFNULL(p.gender, 0) as gender
				    FROM cms_users u
					INNER JOIN cms_user_profiles p ON p.user_id = u.id
					WHERE u.id = '$id' AND u.is_locked = 0
					LIMIT 1
					";					
			$result = $inDB->query($sql);
			
			if ($inDB->num_rows($result)){
				$usr = $inDB->fetch_assoc($result);
		
				$inPage->setTitle($_LANG['CONFIG_PROFILE'].' - '.$usr['nickname']);
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['CONFIG_PROFILE']);
																				
				if ($opt == 'edit'||$opt=='save'||$opt=='changepass'){
					
					$private_forms = '';
					if(isset($cfg['privforms'])){						
						if (is_array($cfg['privforms'])){
							if ($usr['formsdata']==''){
								$formsdata = array();
							} else {
                                $formsdata = $inCore->yamlToArray($usr['formsdata']);
							}
							foreach($cfg['privforms'] as $num=>$form_id){								
								$private_forms .= usrFormEditor($id, $form_id, $formsdata);								
							}							
						}						
					}				
				
					$inPage->initAutocomplete();
					$autocomplete_js = $inPage->getAutocompleteJS('citysearch', 'city', false);
									
					$smarty = $inCore->initSmarty('components', 'com_users_edit_profile.tpl');
				
					$smarty->assign('opt', $opt);
					$smarty->assign('usr', $usr);			
					$smarty->assign('dateform', $inCore->getDateForm('birthdate', false, $usr['bday'], $usr['bmonth'], $usr['byear']));
					$smarty->assign('private_forms', $private_forms);		
					$smarty->assign('autocomplete_js', $autocomplete_js);
					
					$smarty->display('com_users_edit_profile.tpl');
				}
				
			} else { echo usrAccessDenied(); }
		
		} else { echo usrAccessDenied(); }
	
	} else { cmsUser::goToLogin(); }

}
/////////////////////////////// VIEW USER COMMENTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='comments'){

	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

	$page       = $inCore->request('page', 'int', 1);
	$perpage    = 15;

	$inPage->setTitle($_LANG['COMMENTS'].' - '.$usr['nickname']);
	$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
	$inPage->addPathway($_LANG['COMMENTS']);

	$sql = "SELECT c.*,  IFNULL(v.total_rating, 0) as votes
                FROM cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id = c.id AND v.target = 'comment'
                WHERE c.user_id = '$id' AND c.published = 1
                ORDER BY c.pubdate DESC
				LIMIT ".(($page-1)*$perpage).", $perpage";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){
		$comments = array();
		while ($com = $inDB->fetch_assoc($result)){
                if ($com['votes']>0){
                    $com['votes'] = '<span class="cmm_good">+'.$com['votes'].'</span>';
                } elseif ($com['votes']<0){
                    $com['votes'] = '<span class="cmm_bad">'.$com['votes'].'</span>';
                }
				$com['fpubdate'] = $inCore->dateFormat($com['pubdate']);
                $comments[] = $com;
		}

        $comments = cmsCore::callEvent('GET_COMMENTS', $comments);

		// Считаем общее число комментариев
		$records_total = $inDB->rows_count('cms_comments', 'user_id = '.$id.' AND published = 1');

		$smarty = $inCore->initSmarty('components', 'com_users_comments.tpl');
        $smarty->assign('user_id', $id);
		$smarty->assign('nickname', $usr['nickname']);
		$smarty->assign('login', $usr['login']);
		$smarty->assign('comments', $comments);
        $smarty->assign('avatar', usrImage($id));
		$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/comments%page%.html\')'));
		$smarty->display('com_users_comments.tpl');	
		if ($inCore->inRequest('of_ajax')) { echo ob_get_clean(); exit; }

	} else { echo '<p>'.$_LANG['NO_USER_COMMENT'].'</p>';	}
	
}
/////////////////////////////// VIEW USER POSTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='forumposts'){

	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

	$page = $inCore->request('page', 'int', 1);
	$perpage = 15;

    $inPage->setTitle($_LANG['POSTS_IN_FORUM'].' - '.$usr['nickname']);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
    $inPage->addPathway($_LANG['POSTS_IN_FORUM']);

	if ($inUser->id == $id) {
		$sql = "SELECT p.*, t.title as topic
					FROM cms_forum_posts p
					INNER JOIN cms_forum_threads t ON t.id = p.thread_id
					WHERE p.user_id = '$id'
					ORDER BY p.pubdate DESC
					LIMIT ".(($page-1)*$perpage).", $perpage";
		// Считаем общее число постов на форуме
		$records_total = $inDB->rows_count('cms_forum_posts', 'user_id = '.$id.'');
	} else {
		$sql = "SELECT p.*, t.title as topic
					FROM cms_forum_posts p
					INNER JOIN cms_forum_threads t ON t.id = p.thread_id
					WHERE p.user_id = '$id' AND t.is_hidden = 0
					ORDER BY p.pubdate DESC
					LIMIT ".(($page-1)*$perpage).", $perpage";
		// Считаем общее число постов на форуме
		$records_total = $inDB->rows_count('cms_forum_posts p LEFT JOIN cms_forum_threads t ON t.id = p.thread_id', 'p.user_id = '.$id.' AND t.is_hidden = 0');
	}

	$result = $inDB->query($sql) ;

	if ($inDB->num_rows($result)>0){
		$posts = array();
		while ($post = $inDB->fetch_assoc($result)){
				$post['link'] = '/forum/thread'.$post['thread_id'].'.html#'.$post['id'];
                $post['content'] = $inCore->parseSmiles($post['content'], true);
				$post['content'] = str_replace("&amp;", '&', $post['content']);
				$post['date'] = $inCore->dateFormat($post['pubdate']);
				$posts[] = $post;
		}

        $posts = cmsCore::callEvent('GET_FORUM_POSTS', $posts);

		$smarty = $inCore->initSmarty('components', 'com_users_forumposts.tpl');
        $smarty->assign('user_id', $id);
        $smarty->assign('user_login', $usr['login']);
		$smarty->assign('nickname', $usr['nickname']);
		$smarty->assign('posts', $posts);
        $smarty->assign('avatar', usrImage($id));
		$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/forumposts%page%.html\')'));
		$smarty->display('com_users_forumposts.tpl');
		if ($inCore->inRequest('of_ajax')) { echo ob_get_clean(); exit; }

	} else { echo '<p>'.$_LANG['NOT_USER_POSTS_IN_FORUM'].'</p>';	}

}
/////////////////////////////// VIEW PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='profile'){

	$inCore->loadLib('clubs');

    if (!$id){
        $login = $inCore->request('login', 'str', '');
        $login = urldecode($login);
        $id    = $inDB->get_field('cms_users', "login='{$login}' ORDER BY is_deleted ASC", 'id');
    }

    $usr = $model->getUser($id);
    
    if (!$usr){ cmsCore::error404(); }

	if (!$inUser->id && !$cfg['sw_guest']) {
        cmsUser::goToLogin(); 
	}

    $inPage->setTitle($usr['nickname']);
    $inPage->addPathway($usr['nickname']);

    if ( !(usrAllowed($usr['allow_who'], $id) || $inUser->is_admin) ){
		$usr['flogdate'] = strip_tags(usrStatus($usr['id'], $usr['flogdate'], false, $usr['gender']));
        $smarty = $inCore->initSmarty('components', 'com_users_not_allow.tpl');
		$smarty->assign('is_auth', $inUser->id);
        $smarty->assign('avatar', usrImageNOdb($usr['id'], 'big', $usr['imageurl'], $usr['is_deleted']));
        $smarty->assign('usr', $usr);
        $smarty->display('com_users_not_allow.tpl');
        return;
    }

    $deleted    = $usr['is_deleted'];
    $myprofile  = ($inUser->id == $id);

    if ($deleted){
        $smarty = $inCore->initSmarty('components', 'com_users_deleted.tpl');
        $smarty->assign('is_user', $inUser->id);
        $smarty->assign('id', $id);
        $smarty->assign('nickname', $usr['nickname']);
        $smarty->assign('avatar', usrImageNOdb($usr['id'], 'big', $usr['imageurl'], $usr['is_deleted']));
        $smarty->assign('login', $usr['login']);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('others_active', $inDB->rows_count('cms_users', "login='{$usr['login']}' AND is_deleted=0", 1));
        $smarty->display('com_users_deleted.tpl');
        return;
    }
	
    $usr['avatar']				 = usrImageNOdb($usr['id'], 'big', $usr['imageurl'], $usr['is_deleted']);
	
    $usr['friends']				= usrFriends($usr['id'], $usr['friends_total'], 6);
    $usr['isfriend']			= (($inUser->id && !$myprofile) ? usrIsFriends($usr['id'], $inUser->id) : false);
    $usr['isfriend_not_add']	= $usr['isfriend'];
    $usr['is_new_friends']		= ($inUser->id==$usr['id'] && $model->isNewFriends($usr['id']));
    
    if ($usr['is_new_friends']){
        $usr['new_friends'] 	= $model->getNewFriends($usr['id']);
    }

    $usr['awards_html']			 = $cfg['sw_awards'] ? usrAwards($usr['id']) : false;
	
	if($cfg['sw_wall']){
        $usr['wall_html']			= cmsUser::getUserWall($usr['id']);
        $usr['addwall_html'] 		= cmsUser::getUserAddWall($usr['id']);
	}

	$usr['banned']				= ($usr['banned'] == $usr['id'] ? 1 : 0);

    $usr['clubs'] 				= $cfg['sw_clubs'] ? cmsUserClubs($usr['id']) : false;

    $usr['status']				= ($usr['status'] == $usr['id'] ? '<span class="online">'.$_LANG['ONLINE'].'</span>' : '<span class="offline">'.$_LANG['OFFLINE'].'</span>');

    $usr['status_date']         = cmsCore::dateDiffNow($usr['status_date']); 
    $usr['flogdate']            = strip_tags(usrStatus($usr['id'], $usr['flogdate'], false, $usr['gender']));
    $usr['karma']				= strip_tags( cmsUser::getKarmaFormat($usr['id'], false), '<table><tr><td><img><a>' );
    $usr['karma_int']			= strip_tags($usr['karma']);
    $usr['karma_link']			= '<a href="/users/'.$usr['id'].'/karma.html" title="'.$_LANG['KARMA_HISTORY'].'" id="karmalink">?</a>';

    $usr['cityurl']             = urlencode($usr['city']);

    $usr['photos_count']		= $cfg['sw_photo'] ? (int)usrPhotoCount($usr['id']) : false;
	$usr['can_add_foto']		= (($usr['photos_count']<$cfg['photosize'] || $cfg['photosize']==0) && $cfg['sw_photo']);

    if ($cfg['sw_photo']){
        $usr['albums']          = $model->getPhotoAlbums($usr['id'], $usr['isfriend']);
        $usr['albums_total']    = sizeof($usr['albums']);
        $usr['albums_show']     = $usr['albums_total'];
        if ($usr['albums_total']>6){
            array_splice($usr['albums'], 6);
            $usr['albums_show'] = 6;
        }
    }
	
    $usr['board_count']			= $cfg['sw_board'] ? (int)$inDB->rows_count('cms_board_items', "user_id={$usr['id']} AND published=1") : false;
    $usr['comments_count']		= $cfg['sw_comm'] ? (int)$inDB->rows_count('cms_comments', "user_id={$usr['id']} AND published=1") : false;
	$cfg_reg = $inCore->loadComponentConfig('registration');
	$usr['invites_count']		= ($inUser->id && $myprofile && $cfg_reg['reg_type'] == 'invite') ? $model->getUserInvitesCount($inUser->id) : 0;

    if($cfg['sw_forum']){
        if ($inUser->id==$id){
            $usr['forum_count']  = $inDB->rows_count('cms_forum_posts', 'user_id = '.$usr['id'].'');
        } else {
            $usr['forum_count']  = $inDB->rows_count('cms_forum_posts p LEFT JOIN cms_forum_threads t ON t.id = p.thread_id', 'p.user_id = '.$usr['id'].' AND t.is_hidden = 0');
        }
    }

    if($cfg['sw_files']){
        if ($inUser->id==$id){
            $usr['files_count'] = $inDB->rows_count('cms_user_files', "user_id = ".$usr['id']." AND allow_who = 'all'");
        } else {
            $usr['files_count'] = $inDB->rows_count('cms_user_files', 'user_id = '.$usr['id']);
        }
    }

    $usr['blog_link'] = '';
	if($cfg['sw_blogs']){
    $usr['blog']            = usrBlog($usr['id']);
    $usr['blog_id']         = $usr['blog']['id'];
    $usr['blog_seolink']    = $usr['blog']['seolink'];
    
    if($usr['blog_id']){
        $usr['blog_link'] 		= '<a href="/blogs/'.$usr['blog_seolink'].'">'.$_LANG['BLOG'].'</a>';
    } elseif($myprofile) {
        $usr['blog_link'] 		= '<a href="/blogs/createblog.html">'.$_LANG['CREATE_BLOG'].'</a>';
    }
	}

    if (!$usr['description']) {
        $usr['description']		= '<span style="color:#999"><em>'.$_LANG['TAGS_NOT_SPEC'].'</em></span>';
    } else {
        $usr['description']     = cmsPage::getMetaSearchLink('/users/hobby/', $usr['description']);
    }

    $usr['flogdate']			= $inCore->getRusDate($usr['flogdate']);
    $usr['fregdate'] 			= $inCore->dateFormat($usr['fregdate']);
    $usr['birthdate'] 			= $inCore->dateFormat($usr['birthdate']);

    $usr['profile_link']        = HOST . cmsUser::getProfileURL($usr['login']);

    $usr['genderimg']			= '';
    if ($usr['gender']) {
        switch ($usr['gender']){
            case 'm': $usr['genderimg'] = '<img src="/components/users/images/male.png"/>'; $usr['gender']=$_LANG['MALES']; break;
            case 'f': $usr['genderimg'] = '<img src="/components/users/images/female.png"/>'; $usr['gender']=$_LANG['FEMALES']; break;
        }
    }

    $usr['privforms'] = '';
    if(isset($cfg['privforms'])){
        if (is_array($cfg['privforms'])){
            if ($usr['formsdata']==''){
                $formsdata = array();
            } else {
                $formsdata = $inCore->yamlToArray($usr['formsdata']);
            }
            foreach($cfg['privforms'] as $num=>$form_id){
                $usr['privforms'] .= usrForm($id, $form_id, $formsdata);
            }
        }
    }

    $plugins = $model->getPluginsOutput($usr);

    $smarty = $inCore->initSmarty('components', 'com_users_profile.tpl');

    $smarty->assign('id', $id);
    $smarty->assign('usr', $usr);
    $smarty->assign('plugins', $plugins);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('myprofile', $myprofile);
	$smarty->assign('is_admin', $inUser->is_admin);
    $smarty->assign('is_auth', $inUser->id);

    $smarty->display('com_users_profile.tpl');

}
/////////////////////////////// VIEW MESSAGES /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='messages'){

	if (!$cfg['sw_msg']) { cmsCore::error404(); }
	
	if (usrCheckAuth() && ($inUser->id == $id  || $inCore->userIsAdmin($inUser->id))){
		
		$usr = $model->getUserShort($id);
		if (!$usr) { cmsCore::error404(); }
		
				$inPage->setTitle($_LANG['MY_MESS']);
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['MY_MESS'], '/users/'.$id.'/messages.html');
				include 'components/users/messages.php';			

	} else { echo usrAccessDenied(); }
	
}
/////////////////////////////// AVATAR UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='avatar'){

	if (!$inUser->id || ($inUser->id && $inUser->id != $id)){ cmsCore::error404(); }

	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }
	
	$inPage->setTitle($_LANG['LOAD_AVATAR']);
	$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
	$inPage->addPathway($_LANG['LOAD_AVATAR']);
	
	if ($inCore->inRequest('upload')) {

		$inCore->includeGraphics();
		
		$uploaddir 		= PATH.'/images/users/avatars/';		
		$realfile		= $_FILES['picture']['name'];
		$path_parts     = pathinfo($realfile);
		$ext            = strtolower($path_parts['extension']);

		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png'){

			$filename 		= md5($realfile . '-' . $userid . '-' . time()).'.jpg';
			$uploadfile		= $uploaddir . $realfile;
			$uploadavatar 	= $uploaddir . $filename;
			$uploadthumb 	= $uploaddir . 'small/' . $filename;
			$source			= $_FILES['picture']['tmp_name'];
			$errorCode 		= $_FILES['picture']['error'];

		} else {

			cmsCore::addSessionMessage($_LANG['ERROR_TYPE_FILE'].' jpg, jpeg, gif, bmp, png', 'error');	
			$inCore->redirect('/users/'.$id.'/avatar.html');

		}

		if ($inCore->moveUploadedFile($source, $uploadfile, $errorCode)) {

			//DELETE OLD AVATAR
			$sql = "SELECT imageurl FROM cms_user_profiles WHERE user_id = '$id'";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$old = $inDB->fetch_assoc($result);
				if ($old['imageurl'] && $old['imageurl']!='nopic.jpg'){
					@unlink(PATH.'/images/users/avatars/'.$old['imageurl']);
					@unlink(PATH.'/images/users/avatars/small/'.$old['imageurl']);
				}
			}

			//CREATE THUMBNAIL
			if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
			if (isset($cfg['medw'])) { 	 $medw = $cfg['medw']; } else { $medw = 200; }
			if (isset($cfg['medh'])) { 	 $medh = $cfg['medh']; } else { $medh = 200; }
						
			@img_resize($uploadfile, $uploadavatar, $medw, $medh);
			@img_resize($uploadfile, $uploadthumb, $smallw, $smallw);
						
			//DELETE ORIGINAL							
			@unlink($uploadfile);

			//MODIFY PROFILE
			$sql = "UPDATE cms_user_profiles 
					SET imageurl = '$filename'
					WHERE user_id = '$id'
					LIMIT 1";	
			$inDB->query($sql);
			// очищаем предыдущую запись о смене аватара
			cmsActions::removeObjectLog('add_avatar', $id);
			// выводим сообщение в ленту
			cmsActions::log('add_avatar', array(
				  'object' => '',
				  'object_url' => '',
				  'object_id' => $id,
				  'target' => '',
				  'target_url' => '',
				  'description' => '<a href="'.cmsUser::getProfileURL($usr['login']).'" class="act_usr_ava">
									   <img border="0" src="/images/users/avatars/small/'.$filename.'">
									</a>'
			));
			//GO BACK TO PROFILE VIEW			
			$inCore->redirect(cmsUser::getProfileURL($usr['login']));
						
		} else {
			cmsCore::addSessionMessage('<strong>'.$_LANG['ERROR'].':</strong> '.$inCore->uploadError().'!', 'error');
			$inCore->redirect('/users/'.$id.'/avatar.html');
		}
		
	} else {
		$smarty = $inCore->initSmarty('components', 'com_users_avatar_upload.tpl');
		$smarty->assign('id', $id);
		$smarty->display('com_users_avatar_upload.tpl');
	}	
}
/////////////////////////////// AVATAR LIBRARY /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='select_avatar'){

	if (usrCheckAuth() && $inUser->id==$id){

        $avatars_dir        = PATH."/images/users/avatars/library";
        $avatars_dir_rel    = "/images/users/avatars/library";

        //get avatars list from library directory
        $avatars_dir_handle = opendir($avatars_dir);
        $avatars            = array();

        while ($nextfile = readdir($avatars_dir_handle))
        {
            if(($nextfile!='.')&&($nextfile!='..')&&( strstr($nextfile, '.gif') || strstr($nextfile, '.jpg') || strstr($nextfile, '.jpeg') || strstr($nextfile, '.png')  ) )
            {
                $avatars[] = $nextfile;
            }
        }

        closedir($avatars_dir_handle);

        if (!$inCore->inRequest('set_avatar')){

            //SHOW AVATARS LIST
            $inPage->setTitle($_LANG['SELECT_AVATAR']);
			$inPage->addPathway($inUser->nickname, cmsUser::getProfileURL($inUser->login));
			$inPage->addPathway($_LANG['SELECT_AVATAR']);

            //paging
            $maxcols = 4;
            $page    = $inCore->request('page', 'int', 1);
            $perpage = 20;

            //slice only current page from avatars list
            $total   = sizeof($avatars);
            $avatars = array_slice($avatars, ($page-1)*$perpage, $perpage);

            //show page
            $smarty = $inCore->initSmarty('components', 'com_users_avatars.tpl');
                $smarty->assign('userid', $id);
                $smarty->assign('avatars', $avatars);
                $smarty->assign('avatars_dir', $avatars_dir_rel);
                $smarty->assign('maxcols', $maxcols);
                $smarty->assign('page', $page);
                $smarty->assign('perpage', $perpage);
				$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/users/%user_id%/select-avatar-%page%.html', array('user_id'=>$id)));
            $smarty->display('com_users_avatars.tpl');

        } else {

            //SET AVATAR TO SELECTED

            $avatar_id  = $inCore->request('avatar_id', 'int', 0);
            $file       = $avatars[$avatar_id];

            if (file_exists($avatars_dir.'/'.$file)){

                $userid = $id;

                $uploaddir 		= $_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/';
                $realfile		= $file;
                $filename 		= md5($realfile . '-' . $userid . '-' . time()).'.jpg';
                $uploadfile		= $avatars_dir . '/' . $realfile;
                $uploadavatar 	= $uploaddir . $filename;
                $uploadthumb 	= $uploaddir . 'small/' . $filename;

                $sql = "SELECT imageurl FROM cms_user_profiles WHERE user_id = $userid";
                $result = $inDB->query($sql) ;
                if ($inDB->num_rows($result)){
                    $old = $inDB->fetch_assoc($result);
                    if ($old['imageurl'] && $old['imageurl']!='nopic.jpg'){
                        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/'.$old['imageurl']);
                        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/small/'.$old['imageurl']);
                    }
                }
                //CREATE THUMBNAIL
                if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
                if (isset($cfg['medw'])) { 	 $medw = $cfg['medw']; } else { $medw = 200; }

                $inCore->includeGraphics();
                copy($uploadfile, $uploadavatar);
                @img_resize($uploadfile, $uploadthumb, $smallw, $smallw);

                //MODIFY PROFILE
                $sql = "UPDATE cms_user_profiles
                        SET imageurl = '$filename'
                        WHERE user_id = '$userid'
                        LIMIT 1";
                $inDB->query($sql);

				// очищаем предыдущую запись о смене аватара
				cmsActions::removeObjectLog('add_avatar', $id);
				// выводим сообщение в ленту
				cmsActions::log('add_avatar', array(
					  'object' => '',
					  'object_url' => '',
					  'object_id' => $id,
					  'target' => '',
					  'target_url' => '',
					  'description' => '<a href="'.cmsUser::getProfileURL($inUser->login).'" class="act_usr_ava">
                                            <img border="0" src="/images/users/avatars/small/'.$filename.'">
                                        </a>'
				));

            }

            //GO BACK TO PROFILE VIEW
            $inCore->redirect(cmsUser::getProfileURL($inUser->login));
            
        }

	}//auth
	else { echo usrAccessDenied(); }
}
/////////////////////////////// PHOTO UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addphoto'){

    if (!$cfg['sw_photo']) { cmsCore::error404(); }

    if (!$inUser->id) { cmsUser::goToLogin(); }

    if ($id != $inUser->id) { cmsCore::error404(); }

    $usr = $model->getUserShort($id);
    if (!$usr){ cmsCore::error404(); }

	$inPage->backButton(false);
	$uload_type = $inCore->request('uload_type', 'str', 'multi');

    $albums = $model->getPhotoAlbums($id, true, true);

	$photos = $model->getUploadedPhotos($id);
	$total_no_pub = $photos ? sizeof($photos) : 0; unset($photos);
	
	$photo_count = usrPhotoCount($id, false);

    if($cfg['photosize']>0 && !$inCore->userIsAdmin($inUser->id)) {
        $max_limit = true;
        $max_files  = $cfg['photosize'] - $photo_count;
		$stop_photo = $photo_count >= $cfg['photosize'];
    } else {
        $max_limit = false;
        $max_files = 0;
		$stop_photo = false;
    }

    $inPage->setTitle($_LANG['ADD_PHOTOS']);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
	$inPage->addPathway($_LANG['PHOTOALBUMS'], '/users/'.$usr['id'].'/photoalbum.html');
    $inPage->addPathway($_LANG['ADD_PHOTOS']);

    $smarty = $inCore->initSmarty('components', 'com_users_photo_add.tpl');
    $smarty->assign('user_id', $id);
    $smarty->assign('user', $usr);
	$smarty->assign('total_no_pub', $total_no_pub);
    $smarty->assign('albums', $albums);
    $smarty->assign('sess_id', session_id());
    $smarty->assign('max_limit', $max_limit);
    $smarty->assign('max_files', $max_files);
	$smarty->assign('uload_type', $uload_type);
	$smarty->assign('stop_photo', $stop_photo);
    $smarty->display('com_users_photo_add.tpl');

}

if ($do=='uploadphotos'){

    if (!$cfg['sw_photo']) { cmsCore::error404(); }

    if (!$_FILES['Filedata']['name']) { cmsCore::error404(); }

    // Code for Session Cookie workaround
	if ($inCore->inRequest("PHPSESSID")) {
        $sess_id = $inCore->request("PHPSESSID", 'str');
        if ($sess_id != session_id()) { session_destroy(); }
        session_id($sess_id);
        session_start();
	}

    $user_id = $_SESSION['user']['id'];

    if (!$user_id) { header("HTTP/1.1 500 Internal Server Error"); exit(0); }
	if (($cfg['photosize']>0) && (usrPhotoCount($user_id, false) >= $cfg['photosize']) && !$inCore->userIsAdmin($inUser->id)) { 
        header("HTTP/1.1 500 Internal Server Error"); exit(0); 
    }

    $inCore->includeGraphics();

    $uploaddir 				= PATH.'/images/users/photos/';
    $realfile 				= $inDB->escape_string($_FILES['Filedata']['name']);

	$path_parts             = pathinfo($realfile);
    $ext                    = strtolower($path_parts['extension']);
	if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png' && $ext != 'bmp') {  exit(0); }

    $lid 					= $inDB->get_fields('cms_user_photos', 'id>0', 'id', 'id DESC');
    $lastid 				= $lid['id']+1;	
    $filename 				= md5($lastid.$realfile).'.jpg';					

    $uploadfile				= $uploaddir . $realfile;
    $uploadphoto 			= $uploaddir . $filename;
    $uploadthumb['small'] 	= $uploaddir . 'small/' . $filename;
    $uploadthumb['medium']	= $uploaddir . 'medium/' . $filename;

    $source					= $_FILES['Filedata']['tmp_name'];
    $errorCode				= $_FILES['Filedata']['error'];

    if ($inCore->moveUploadedFile($source, $uploadphoto, $errorCode)) {

        if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
        
        @img_resize($uploadphoto, $uploadthumb['small'], 96, 96, true);
        @img_resize($uploadphoto, $uploadthumb['medium'], 600, 600, false, $cfg['watermark']);

        $model->addUploadedPhoto($user_id, array('filename'=>$realfile, 'imageurl'=>$filename));
		if ($inCore->inRequest('upload')) { $inCore->redirect('/users/'.$inUser->login.'/photos/submit'); }

    } else {

        header("HTTP/1.1 500 Internal Server Error");
        echo $inCore->uploadError();
        
    }

    exit(0);
    
}

if ($do=='submitphotos'){

    if (!$cfg['sw_photo']) { cmsCore::error404(); }

    if (!$inUser->id) { cmsCore::error404(); }

    if (!$id){
        $login = $inCore->request('login', 'str', '');
        $login = urldecode($login);
        $id    = $inDB->get_field('cms_users', "login='{$login}' AND is_deleted=0", 'id');
    }

    $usr = $model->getUserShort($id);
    if (!$usr){ cmsCore::error404(); }

    if ($id != $inUser->id && !$inUser->is_admin) { cmsCore::error404(); }

    $photos = $model->getUploadedPhotos($id);
    if (!$photos) { cmsCore::error404(); }

    $inCore->loadLanguage('components/photos');

    if (!$inCore->inRequest('submit')){

        $albums = $model->getPhotoAlbums($id, true, true);

        $inPage->setTitle($_LANG['PHOTOS_CONFIG']);
        $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway($_LANG['PHOTOALBUMS'], '/users/'.$usr['id'].'/photoalbum.html');
        $inPage->addPathway($_LANG['PHOTOS_CONFIG']);

        $smarty = $inCore->initSmarty('components', 'com_users_photo_submit.tpl');
        $smarty->assign('user_id', $id);
        $smarty->assign('albums', $albums);
        $smarty->assign('photos', $photos);
		$smarty->assign('is_edit', $inCore->request('is_edit', 'int', 0));
        $smarty->display('com_users_photo_submit.tpl');

    }

    if ($inCore->inRequest('submit')){

        cmsUser::sessionDel('photos_list');

        $new_album  = $inCore->request('new_album', 'int', 0);
        
        $delete  = $inCore->request('delete', 'array_int');
        $titles  = $inCore->request('title', 'array_str');
        $allow   = $inCore->request('allow', 'array_str');
        $desc    = $inCore->request('desc', 'array_str');
		$is_edit = $inCore->request('is_edit', 'int', 0);

        foreach($delete as $photo_id){
            $model->deletePhoto($photo_id);
        }

        if ($new_album){
            $album['user_id']   = $id;
            $album['title']     = $inCore->request('album_title', 'str', $_LANG['PHOTOALBUM'].' '.date('d.m.Y'));
            $album['allow_who'] = $inCore->request('album_allow_who', 'str', 'all');
			$album['description'] = $inCore->request('description', 'str', '');
            $album_id = $model->addPhotoAlbum($album);
        } else {
            $album_id = $inCore->request('album_id', 'int');
        }

		$total_foto = sizeof($titles);

		$album = !$album ? $model->getPhotoAlbum('private', $album_id) : $album;

		$descr_next = 1;

        foreach($titles as $photo_id => $title){

            $description = isset($desc[$photo_id]) ? $desc[$photo_id] : '';
            $allow_who   = isset($allow[$photo_id]) ? $allow[$photo_id] : 'all';
			$imageurl    = $photos[$photo_id]['imageurl'];
			$title       = $title ? $title : $_LANG['PHOTO_WITHOUT_NAME'];

            $photo_sql = "UPDATE cms_user_photos
                          SET title='{$title}',
                              description = '{$description}',
                              album_id = '{$album_id}',
                              allow_who = '{$allow_who}'
                          WHERE id = '{$photo_id}' AND user_id = '{$id}'
                          LIMIT 1";

            //cmsInsertTags($tags, 'userphoto', $photoid);

            $inDB->query($photo_sql);

			if ($total_foto == 1 && !$is_edit) {
				$is_friends_only = $allow_who == 'friends' ? 1 : 0;
				$is_users_only = $allow_who == 'registered' ? 1 : 0;
				cmsActions::log('add_user_photo', array(
					  'object' => $title,
					  'object_url' => '/users/'.$id.'/photo'.$photo_id.'.html',
					  'object_id' => $photo_id,
					  'target' => $album['title'],
					  'target_id' => $album_id,
					  'target_url' => '/users/'.$usr['login'].'/photos/private'.$album_id.'.html',
					  'description' => '<a href="/users/'.$id.'/photo'.$photo_id.'.html" class="act_photo">
											<img border="0" src="/images/users/photos/small/'.$imageurl.'" />
										  </a>', 
					  'is_friends_only' => $is_friends_only, 
					  'is_users_only' => $is_users_only
				));

			} elseif ($descr_next < 4) {

					$photo_descr .= ' <a href="/users/'.$id.'/photo'.$photo_id.'.html" class="act_photo">
											<img border="0" src="/images/users/photos/small/'.$imageurl.'" />
									</a> ';
			}
			$descr_next++;

        }
		if ($total_foto > 1 && !$is_edit) {
			$is_friends_only = $album['allow_who'] == 'friends' ? 1 : 0;
			$is_users_only = $album['allow_who'] == 'registered' ? 1 : 0;
			cmsActions::log('add_user_photo_multi', array(
				  'object' => $total_foto,
				  'object_url' => '',
				  'object_id' => '',
				  'target' => $album['title'],
				  'target_id' => $album_id, 
				  'target_url' => '/users/'.$usr['login'].'/photos/private'.$album_id.'.html',
				  'description' => $photo_descr, 
				  'is_friends_only' => $is_friends_only, 
				  'is_users_only' => $is_users_only
			));
        }

        $inCore->redirect("/users/{$usr['login']}/photos/private{$album_id}.html");

    }

}

/////////////////////////////// PHOTO DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delphoto'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }
	
	$inCore->loadLib('tags');
	$max_mb = 2; //max filesize in Mb
	$inCore->loadLanguage('components/photos');
	$photo_id = $inCore->request('photoid', 'int', '');
	
	if (usrCheckAuth() && ($inUser->id == $id || $inCore->userIsAdmin($inUser->id))){
        
        $usr = $model->getUserShort($id);
        if (!$usr) { cmsCore::error404(); }

        $inPage->backButton(false);
        
		$photo = $inDB->get_fields('cms_user_photos', "id = '{$photo_id}' AND user_id = '{$id}'", 'title, album_id');

        if (!$photo){ cmsCore::error404(); }
        
		if (!isset($_POST['godelete'])){
			
            $inPage->setTitle($_LANG['DELETE_PHOTO']);
            $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
            $inPage->addPathway($_LANG['PHOTOALBUMS'], '/users/'.$usr['id'].'/photoalbum.html');
            $inPage->addPathway($_LANG['DELETE_PHOTO'], $_SERVER['REQUEST_URI']);

            $confirm['title']                   = $_LANG['DELETING_PHOTO'];
            $confirm['text']                    = "".$_LANG['REALLY_DELETE_PHOTO']." &laquo;".$photo['title']."&raquo;?";
            $confirm['action']                  = $_SERVER['REQUEST_URI'];
            $confirm['yes_button']              = array();
            $confirm['yes_button']['type']      = 'submit';
            $confirm['yes_button']['name']  	= 'godelete';
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');

		} else {

            $model->deletePhoto($photo_id);

            $album_has_photos = $inDB->rows_count('cms_user_photos', "album_id = {$photo['album_id']}", 1);

            if ($album_has_photos){
                $inCore->redirect('/users/'.$usr['login'].'/photos/private'.$photo['album_id'].'.html');
            } else {
                $model->deletePhotoAlbum($id, $photo['album_id']);
                $inCore->redirect(cmsUser::getProfileURL($usr['login']));
            }

		}

	} else { echo usrAccessDenied(); }
}

/////////////////////////////// ALBUM EDIT /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editalbum'){
	
	if (!$cfg['sw_photo']) { cmsCore::error404(); }

    $usr = $model->getUserShort($id);
    if (!$usr) { cmsCore::error404(); }
	
	$album_id = $inCore->request('album_id', 'int', '');

    $album    = $model->getPhotoAlbum('private', $album_id);

    if (!$album) { cmsCore::error404(); }

    if ($album['user_id'] != $inUser->id && !$inUser->is_admin){ cmsCore::error404(); }

	unset($album);

    $album['title']       = $inCore->request('album_title', 'str', $_LANG['PHOTOALBUM'].' '.date('d.m.Y'));
    $album['allow_who']   = $inCore->request('album_allow_who', 'str', 'all');
	$album['description'] = $inCore->request('description', 'str', '');
	$album['id']          = $album_id;
	
	$model->updatePhotoAlbum($album);

    $inCore->redirect('/users/'.$usr['login'].'/photos/private'.$album_id.'.html');

}

/////////////////////////////// PHOTO EDIT /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editphoto'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }

    $usr = $model->getUserShort($id);
    if (!$usr) { cmsCore::error404(); }

	$photo_id = $inCore->request('photoid', 'int', '');

    $photo    = $model->getPhoto($photo_id);

    if (!$photo) { cmsCore::error404(); }

    if ($photo['user_id'] != $inUser->id && !$inUser->is_admin){ cmsCore::error404(); }

	cmsUser::sessionPut('photos_list', array($photo['id']));

    $inCore->redirect('/users/'.$usr['login'].'/photos/submit-edit');

}

//============================================================================//
//====================== Пакетное редактирование фотографий ==================//
//============================================================================//

if ($do=='editphotolist'){

    if (!$cfg['sw_photo']) { cmsCore::error404(); }

    if (!$inCore->inRequest('photos')) { cmsCore::error404(); }

    $photo_ids  = $inCore->request('photos', 'array_int');
    $album_id   = $inCore->request('album_id', 'int');
    $photos     = array();

    $usr = $model->getUserShort($id);
    if (!$usr) { cmsCore::error404(); }

    //проверяем доступ
    foreach($photo_ids as $photo_id){

        $photo      = $model->getPhoto($photo_id);

        if ($photo['user_id'] != $inUser->id && !$inUser->is_admin){ cmsCore::error404(); exit; }

    }

    if ($inCore->inRequest('delete')){

        foreach($photo_ids as $photo_id){
            $model->deletePhoto($photo_id);
        }

        $album_has_photos = $inDB->rows_count('cms_user_photos', "album_id = {$album_id}", 1);

        if ($album_has_photos){
            $inCore->redirectBack();
        } else {
            $model->deletePhotoAlbum($id, $album_id);
            $inCore->redirect(cmsUser::getProfileURL($usr['login']));
        }

    }

    if ($inCore->inRequest('edit')){

        foreach($photo_ids as $photo_id){
            $photos[] = $photo_id;
        }

        if ($photos){ cmsUser::sessionPut('photos_list', $photos); }

        $inCore->redirect('/users/'.$usr['login'].'/photos/submit-edit');

    }

}

//============================================================================//
//============================ Все фотографии ================================//
//============================================================================//

if ($do=='viewphotos'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }

	if (!$inUser->id && !$cfg['sw_guest']) {
        cmsUser::goToLogin(); 
	}
	
	$usr = $model->getUserShort($id);
	if (!$usr){ cmsCore::error404(); }

    //Мой профиль или нет
    $my_profile = ($inUser->id == $id);

    //Определяем, друзья мы или нет
	$we_friends = ($inUser->id && !$my_profile) ? (int)usrIsFriends($usr['id'], $inUser->id) : 0;
	if (!$we_friends) { $we_friends = 0; }

    $albums = $model->getPhotoAlbums($id, $we_friends);

    $inPage->setTitle($_LANG['PHOTOALBUMS']);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
    $inPage->addPathway($_LANG['PHOTOALBUMS']);

    //Отдаем в шаблон
    $smarty = $inCore->initSmarty('components', 'com_users_albums.tpl');
	$smarty->assign('albums', $albums);
	$smarty->assign('my_profile', $my_profile);
	$smarty->assign('user', $usr);
    $smarty->display('com_users_albums.tpl');

}

//============================================================================//
//============================ Один фотоальбом ===============================//
//============================================================================//

if ($do=='viewalbum'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }

	if (!$inUser->id && !$cfg['sw_guest']) {
        cmsUser::goToLogin(); 
	}

    if (!$id){
        $login = $inCore->request('login', 'str', '');
        $login = urldecode($login);
        $id    = $inDB->get_fields('cms_users', "login='{$login}' AND is_deleted=0", 'id');
		$id    = ($id['id'] ? $id['id'] : 0);
    }

    $usr = $model->getUserShort($id);
    if (!$usr){ cmsCore::error404(); }

    $album_type = $inCore->request('album_type', 'str', 'private');
    $album_id   = $inCore->request('album_id', 'int', '0');

    $album = $model->getPhotoAlbum($album_type, $album_id);

    if (!$album){ cmsCore::error404(); }

    if ($album_type != 'private') { $album['allow_who'] = 'all'; }

    $inPage->setTitle($album['title']);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
	$inPage->addPathway($_LANG['PHOTOALBUMS'], '/users/'.$usr['id'].'/photoalbum.html');
    $inPage->addPathway($album['title']);

    $photos     = array();
    $filter     = '';

    //Мой профиль или нет
    $my_profile = ($inUser->id == $id);

    //Определяем, друзья мы или нет
	$we_friends = ($inUser->id && !$my_profile) ? (int)usrIsFriends($usr['id'], $inUser->id) : 0;

	if ($album['allow_who'] == 'all' || $my_profile || ($album['allow_who'] == 'friends' && $we_friends) || ($album['allow_who'] == 'registered' && $inUser->id)) {
    $photos = $model->getAlbumPhotos($usr['id'], $album_type, $album_id, $we_friends);
	}

    //Делим на страницы
    $total      = sizeof($photos);

    if ($total){
        $perpage        = 20;
        $page           = $inCore->request('page', 'int', 1);
        $pagination     = cmsPage::getPagebar($total, $page, $perpage, '/users/%user%/photos/%album%%id%-%page%.html', array('user'=>$usr['login'], 'album'=>$album_type, 'id'=>$album_id));
        $page_photos    = array();
        $start          = $perpage*($page-1);
        for($p=$start; $p<$start+$perpage; $p++){
            if ($photos[$p]){
                $page_photos[] = $photos[$p];
            }
        }
        $photos = $page_photos; unset($page_photos);
    }

    //Отдаем в шаблон
    $smarty = $inCore->initSmarty('components', 'com_users_photos.tpl');
	$smarty->assign('page_title', $album['title']);
	$smarty->assign('album_type', $album_type);
	$smarty->assign('album', $album);
	$smarty->assign('photos', $photos);
	$smarty->assign('user_id', $id);
	$smarty->assign('usr', $usr);
	$smarty->assign('my_profile', $my_profile);
	$smarty->assign('is_admin', $inUser->is_admin);
	$smarty->assign('pagebar', $pagination);
    $smarty->display('com_users_photos.tpl');
    
}

//============================================================================//
//============================ Удалить фотоальбом ============================//
//============================================================================//
if ($do=='delalbum'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }

    $album_id = $inCore->request('album_id', 'int', '0');

    $album = $model->getPhotoAlbum('private', $album_id);

    if (!$album){ cmsCore::error404(); }

    if (!$inUser->is_admin && ($album['user_id'] != $inUser->id)) { cmsCore::error404(); }

    $model->deletePhotoAlbum($id, $album_id);

    $login = $inDB->get_field('cms_users', "id={$album['user_id']}", 'login');

    $inCore->redirect(cmsUser::getProfileURL($login));

}

//============================================================================//
//=============================== Объявления =================================//
//============================================================================//

if ($do=='viewboard'){ 
	
	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }
	
		$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway($_LANG['ADVS']);
        $inPage->setTitle($_LANG['ADVS'].' - '.$usr['nickname']);
		
		// Загружаем конфигурацию компонента объявления
		$cfg_board = $inCore->loadComponentConfig('board');
			
        // выбираем объявления пользователя по его id
		if (($inUser->id == $id && $cfg_board['aftertime'] == 'hide' && $cfg_board['extend']) || ($inUser->id == $id && $cfg_board['public'] == 1 && !$cfg_board['extend'] && $cfg_board['aftertime'] != 'hide')) {
		$sql = "SELECT *
				FROM cms_board_items
				WHERE user_id = $id
				ORDER BY pubdate DESC
				";
			// Считаем общее число объявлений
			$records_total = $inDB->rows_count('cms_board_items', 'user_id = '.$id.''); 
		} else {
			$sql = "SELECT *
					FROM cms_board_items
					WHERE user_id = $id AND published = 1
					ORDER BY pubdate DESC
					";
			// Считаем общее число объявлений
			$records_total = $inDB->rows_count('cms_board_items', 'user_id = '.$id.' AND published = 1');
		}
		$perpage = 10; // объявлений на странице
		$page = $inCore->request('page', 'int', 1);
		$sql .= "LIMIT ".($page-1)*$perpage.", $perpage";

		$result = $inDB->query($sql);

		$is_con = false;
		$cons = array();
	
		if ($inDB->num_rows($result)){				

				while($con = $inDB->fetch_assoc($result)){							
					if ($con['file'] && file_exists($_SERVER['DOCUMENT_ROOT'].'/images/board/small/'.$con['file'])){
							$con['file'] = $con['file'];
					} else { $con['file'] = 'nopic.jpg'; }				
											if ($inUser->id){
					$con['moderator'] = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $con['user_id'] == $inUser->id);
											} else {
					$con['moderator'] = false;
											}											
					$timedifference    = strtotime("now") - strtotime($con['pubdate']);
					$con['is_overdue'] = round($timedifference / 86400) > $con['pubdays'] && $con['pubdays'] > 0;
					$con['pubdate'] = $inCore->dateFormat($con['pubdate']);
				$cons[] = $con;
											}
				$is_con = true;
				
				}					
		// отдаем в шаблон
		$smarty = $inCore->initSmarty('components', 'com_users_boards.tpl');
		$smarty->assign('usr', $usr);
		$smarty->assign('cons', $cons);
		$smarty->assign('cfg_board', $cfg_board);
		$smarty->assign('myprofile', ($inUser->id == $id));
        $smarty->assign('is_con', $is_con);
		$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/board%page%.html\')'));
		$smarty->display('com_users_boards.tpl');					
		if ($inCore->inRequest('of_ajax')) { echo ob_get_clean(); exit; }

}

/////////////////////////////// FRIENDS LIST /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='friendlist'){

	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

	$page    = $inCore->request('page', 'int', 1);
	$perpage = 10;
	
	if (!usrCheckAuth()) { cmsUser::goToLogin(); }

			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['FRIENDS'], $_SERVER['REQUEST_URI']);
	$inPage->setTitle($_LANG['FRIENDS']);
	$inPage->backButton(false);

	$friends = usrFriends($usr['id'], $total, $perpage, $page);
	
    $smarty = $inCore->initSmarty('components', 'com_users_friends.tpl');

   	$smarty->assign('friends', $friends);
	$smarty->assign('usr', $usr);
	$smarty->assign('myprofile', ($id == $inUser->id));
	$smarty->assign('total', $total);
	$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, 'javascript:centerLink(\'/users/'.$id.'/friendlist%page%.html\')'));
		
    $smarty->display('com_users_friends.tpl');
	if ($inCore->inRequest('of_ajax')) { echo ob_get_clean(); exit; }

}

/////////////////////////////// VIEW PHOTO /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewphoto'){

	if (!$cfg['sw_photo']) { cmsCore::error404(); }

	if (!$inUser->id && !$cfg['sw_guest']) {
        cmsUser::goToLogin(); 
	}

    $photoid = $inCore->request('photoid', 'int', 0);

	$user_id = $inUser->id;

	$myprofile = ($user_id == $id) ? true : false;

	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

	$sql = "SELECT p.*, a.title as album, pr.gender
            FROM cms_user_photos p
			INNER JOIN cms_user_albums a ON a.id = p.album_id
			INNER JOIN cms_user_profiles pr ON pr.user_id = p.user_id
            WHERE p.id = '$photoid' AND p.user_id = '$id'
                    LIMIT 1";
			$result = $inDB->query($sql) ;

	if (!$inDB->num_rows($result)){ cmsCore::error404(); }

				$photo = $inDB->fetch_assoc($result);
				
	$inPage->setTitle($photo['title']);
	$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
	$inPage->addPathway($_LANG['PHOTOALBUMS'], '/users/'.$usr['id'].'/photoalbum.html');
				$inPage->addPathway($photo['album'], '/users/'.$usr['login'].'/photos/private'.$photo['album_id'].'.html');
				$inPage->addPathway($photo['title'], $_SERVER['REQUEST_URI']);

					$photo['pubdate'] = $inCore->dateFormat($photo['pubdate'], true, false, false);
	$photo['genderlink'] = cmsUser::getGenderLink($usr['id'], $usr['nickname'], 0, $photo['gender'], $usr['login']);
					$photo['filesize'] = round(filesize($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/medium/'.$photo['imageurl'])/1024, 2);
					//ссылки на предыдущую и следующую фотографии
					$previd = $inDB->get_fields('cms_user_photos', "id>'{$photo['id']}' AND user_id = '{$usr['id']}' AND album_id='{$photo['album_id']}'", 'id, title, pubdate', 'id ASC');
					$nextid = $inDB->get_fields('cms_user_photos', "id<'{$photo['id']}' AND user_id = '{$usr['id']}' AND album_id='{$photo['album_id']}'", 'id, title, pubdate', 'id DESC');
	// Проверяем права доступа
	$is_allow = usrAllowed($photo['allow_who'], $id) || $inCore->userIsAdmin($inUser->id) ? true : false;
	// Если видим фото, обновляем просмотры
	if ($is_allow) { $inDB->query("UPDATE cms_user_photos SET hits = hits + 1 WHERE id = ".$photo['id']) ; }
					
				$smarty = $inCore->initSmarty('components', 'com_users_photos_view.tpl');
				$smarty->assign('photo', $photo);
	$smarty->assign('bbcode', '[IMG]'.HOST.'/images/users/photos/medium/'.$photo['imageurl'].'[/IMG]');
				$smarty->assign('previd', $previd);
				$smarty->assign('nextid', $nextid);
				$smarty->assign('usr', $usr);
				$smarty->assign('myprofile', $myprofile);
				$smarty->assign('is_admin', $inCore->userIsAdmin($user_id));
	$smarty->assign('is_allow', $is_allow);
	if($is_allow){
					$inCore->loadLib('tags');	
					$smarty->assign('tagbar', cmsTagBar('userphoto', $photo['id']));
				}
				$smarty->display('com_users_photos_view.tpl');	
					
					//show user comments
	if($inCore->isComponentInstalled('comments') && $is_allow){
						$inCore->includeComments();
						comments('userphoto', $photo['id']);
					}					
				
}
/////////////////////////////// ADD FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addfriend'){

    $usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

    cmsUser::clearSessionFriends();

	if (!usrCheckAuth() || $inUser->id == $id) { cmsCore::error404(); }

	if(!usrIsFriends($id, $inUser->id)){
		if (!$inCore->inRequest('goadd')){

			if ($model->isNewFriends($inUser->id, $id)){
				$fr_id = $inDB->get_field('cms_user_friends', "to_id = '{$inUser->id}' AND from_id = '$id'", 'id');
				$sql   = "UPDATE cms_user_friends SET is_accepted = 1 WHERE id = '$fr_id'";
				$inDB->query($sql);
					cmsCore::addSessionMessage($_LANG['ADD_FRIEND_OK'] . $usr['nickname'], 'info');
					//регистрируем событие
					cmsActions::log('add_friend', array(
						'object' => $inUser->nickname,
						'user_id' => $usr['id'],
						'object_url' => cmsUser::getProfileURL($inUser->login),
						'object_id' => $fr_id,
						'target' => '',
						'target_url' => '',
						'target_id' => 0, 
						'description' => ''
					));
				cmsUser::clearSessionFriends();
				$inCore->redirect(cmsUser::getProfileURL($usr['login']));
			}
			if(usrIsFriends($id, $inUser->id, false)){ cmsCore::addSessionMessage($_LANG['ADD_TO_FRIEND_SEND_ERR'], 'error'); $inCore->redirect(cmsUser::getProfileURL($usr['login'])); }
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['ADD_TO_FRIEND']);
                $inPage->backButton(false);

				$confirm['title']                   = $_LANG['ADD_TO_FRIEND'];
				$confirm['text']                    = $_LANG['SEND_TO_USER'].' '.ucfirst($usr['nickname']).' '.$_LANG['FRIENDSHIP_OFFER'].'?<br>'.$_LANG['IF'].' '.ucfirst($usr['nickname']).' '.$_LANG['SUCCESS_TEXT'];
				$confirm['action']                  = $_SERVER['REQUEST_URI'];
				$confirm['yes_button']              = array();
				$confirm['yes_button']['type']      = 'submit';
				$confirm['yes_button']['name']  	= 'goadd';
				$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
				$smarty->assign('confirm', $confirm);
				$smarty->display('action_confirm.tpl');

		} else {
				$to_id      = $id;
				$from_id    = $inUser->id;
			if(!usrIsFriends($id, $inUser->id, false)){

					$sql = "INSERT INTO cms_user_friends (to_id, from_id, logdate, is_accepted) 
							VALUES ('$to_id', '$from_id', NOW(), '0')";
					$inDB->query($sql);
				
				cmsUser::sendMessage(USER_UPDATER, $to_id, '<b>'.$_LANG['RECEIVED_F_O'].'</b>. '.$_LANG['YOU_CAN_SEE'].' <a href="'.cmsUser::getProfileURL($usr['login']).'">'.$_LANG['INPROFILE'].'</a>.');
				cmsCore::addSessionMessage($_LANG['ADD_TO_FRIEND_SEND'], 'info');
				
			} else {
				cmsCore::addSessionMessage($_LANG['ADD_TO_FRIEND_SEND_ERR'], 'error');
			}
				$inCore->redirect(cmsUser::getProfileURL($usr['login']));
		}//!goadd
	} else {
		$inCore->redirectBack();
	}
}//do
/////////////////////////////// DEL FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfriend'){
	if (usrCheckAuth() && $inUser->id!=$id){

		$first_id = $inUser->id;
		$second_id = $id;
		$usr       = $model->getUserShort($id);
		if (!$usr) { cmsCore::error404(); }
		
		$fr_id = $inDB->get_field('cms_user_friends', "(to_id = $first_id AND from_id = $second_id) OR (to_id = $second_id AND from_id = $first_id)", 'id');

		if ($fr_id) {
			$sql   = "DELETE FROM cms_user_friends WHERE id = $fr_id";
			$inDB->query($sql);
			cmsActions::removeObjectLog('add_friend', $fr_id);
			cmsUser::clearSessionFriends();
			cmsCore::addSessionMessage($usr['nickname'] . $_LANG['DEL_FRIEND'], 'info');
		}

		$inCore->redirectBack();

	} else { echo usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// SEND MESSAGE ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='sendmessage'){

	if (!$cfg['sw_msg']) { cmsCore::error404(); }

	if (!$inUser->id || ($inUser->id==$id && !$inCore->inRequest('massmail') && !$inCore->request('send_to_group', 'int', 0))){
        cmsUser::goToLogin();
    }

    $from_id    = $inUser->id;
    $to_id      = $id;

    $usr = $model->getUserShort($id);
    if (!$usr) { cmsCore::error404(); }

    $inPage->setTitle($_LANG['SEND_MESS']);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
    $inPage->addPathway($_LANG['SEND_MESS']);

    if(!$inCore->inRequest('gosend')){

        $replyid = $inCore->request('replyid', 'int', 0);
        $is_reply_user = false;

        if ($replyid){

            $sql = "SELECT m.id as id,
                           m.senddate, m.message, u.login, u.nickname
                    FROM cms_user_msg m
                    LEFT JOIN cms_users u ON u.id = m.from_id
                    WHERE m.id = '$replyid' AND m.to_id = '$from_id'";

            $result = $inDB->query($sql) ;

            if (!$inDB->num_rows($result)){ $inCore->redirect("/users/{$from_id}/messages.html"); }

            $is_reply_user      = true;
            $msg                = $inDB->fetch_assoc($result);
            $msg['senddate']    = $inCore->dateFormat($msg['senddate'], true, true);

            $update_sql = "UPDATE cms_user_msg SET is_new = 0 WHERE id = '{$msg['id']}' LIMIT 1";

            $inDB->query($update_sql);

        }

        $usr['avatar'] = usrImage($usr['id'], 'big');

        $smarty = $inCore->initSmarty('components', 'com_users_messages_add.tpl');
        $smarty->assign('msg', $msg);
        $smarty->assign('usr', $usr);
        $smarty->assign('is_reply_user', $is_reply_user);
        $smarty->assign('bbcodetoolbar', cmsPage::getBBCodeToolbar('message'));
        $smarty->assign('smilestoolbar', cmsPage::getSmilesPanel('message'));
        $smarty->assign('id_admin', $inCore->userIsAdmin($inUser->id));
        $smarty->display('com_users_messages_add.tpl');

    }

    //
    // Отправка сообщений
    //
    if($inCore->inRequest('gosend')){

        $errors  = false;
        $message = $inCore->request('message', 'html', '');
        $message = $inCore->parseSmiles($message, true);
        $message = $inDB->escape_string($message);

        if (strlen($message)<2) { $inCore->addSessionMessage($_LANG['ERR_SEND_MESS'], 'error'); $errors = true; }
        if ($errors) { $inCore->redirect($back); }

        $send_to_group  = $inCore->request('send_to_group', 'int', 0);
        $group_id       = $inCore->request('group_id', 'int', 0);

        //
        // Обычная отправка (1 получатель)
        //
        if (!$inCore->inRequest('massmail') && !$send_to_group){
            
            //отправляем сообщение
            $msg_id = cmsUser::sendMessage($from_id, $to_id, $message);

            //проверяем подписку на уведомления
            $needmail = $inDB->get_field('cms_user_profiles', "user_id='{$to_id}'", 'email_newmsg');

            //Проверяем, если юзер онлайн, то уведомление на почту не отправляем.
            $isonline = $inDB->get_field('cms_online', "user_id='{$to_id}'", 'id');

            //если подписан и не онлайн, отправляем уведомление на email
            if (!$isonline && $needmail){
                $inConf     = cmsConfig::getInstance();

                $postdate   = date('d/m/Y H:i:s');
                $to_email   = $inDB->get_field('cms_users', "id='{$to_id}'", 'email');
                $from_nick  = $inUser->nickname;
                $answerlink = HOST.'/users/'.$from_id.'/reply'.$msg_id.'.html';

                $letter_path    = PATH.'/includes/letters/newmessage.txt';
                $letter         = file_get_contents($letter_path);

                $letter= str_replace('{sitename}', $inConf->sitename, $letter);
                $letter= str_replace('{answerlink}', $answerlink, $letter);
                $letter= str_replace('{date}', $postdate, $letter);
                $letter= str_replace('{from}', $from_nick, $letter);
                $inCore->mailText($to_email, $_LANG['YOU_HAVE_NEW_MESS'].'! - '.$inConf->sitename, $letter);
            }

            $inCore->addSessionMessage($_LANG['SEND_MESS_OK'], 'info');

            $inCore->redirect('/users/'.$inUser->id.'/messages-sent.html');

        }

        //
        // далее идут массовые рассылки, доступные только админам
        //
        if (!$inUser->is_admin){ $inCore->halt(); }

        // отправить всем: получаем список всех пользователей
        if ($inCore->inRequest('massmail')) {
            $userlist    = cmsUser::getAllUsers();
            $success_msg = $_LANG['SEND_MESS_ALL_OK'];
        }

        // отправить группе: получаем список членов группы
        if ($send_to_group) {
            $userlist    = cmsUser::getGroupMembers($group_id);
            if ($userlist){
                $success_msg = sprintf($_LANG['SEND_MESS_GROUP_OK'], cmsUser::getGroupTitle($group_id));
            }
        }

        // проверяем что есть кому отправлять
        if (!$userlist){
            $inCore->addSessionMessage($_LANG['ERR_SEND_MESS'], 'error');
            $inCore->redirectBack();
        }

        // отправляем всем по списку
        foreach ($userlist as $key=>$usr){
            cmsUser::sendMessage(USER_MASSMAIL, $usr['id'], $message);
        }

        $inCore->addSessionMessage($success_msg, 'info');

        $inCore->redirect('/users/'.$inUser->id.'/messages.html');
        
    }

}//do
/////////////////////////////// DEL MESSAGE /////////////////////////////////////////////////////////////////////////////////////
if ($do=='delmessage'){

    if (!$cfg['sw_msg']) { cmsCore::error404(); }
    if (!$inUser->id) { cmsCore::error404(); }

    $msg = $inDB->get_fields('cms_user_msg', "id='$id'", '*');

    if (!$msg){ cmsCore::error404(); }

    if ($msg['to_id']==$inUser->id){
        $inDB->query("UPDATE cms_user_msg SET to_del=1 WHERE id='{$id}'");
        $inCore->addSessionMessage($_LANG['MESS_DEL_OK'], 'info');
    }

    if ($msg['from_id']==$inUser->id && !$msg['is_new']){
        $inDB->query("UPDATE cms_user_msg SET from_del=1 WHERE id='{$id}'");
        $inCore->addSessionMessage($_LANG['MESS_DEL_OK'], 'info');
    }

    if ($msg['from_id']==$inUser->id && $msg['is_new']){
        $inDB->query("DELETE FROM cms_user_msg WHERE id = '$id' LIMIT 1");
        $inCore->addSessionMessage($_LANG['MESS_BACK_OK'], 'info');
    }

    $inDB->query("DELETE FROM cms_user_msg WHERE to_del=1 AND from_del=1");

    $inCore->redirectBack();

}//do
/////////////////////////////// DELETE ALL INBOX MESSAGES ///////////////////////////////////////////////////////////////////////
if ($do=='delmessages'){

	if (!$cfg['sw_msg']) { cmsCore::error404(); }

    if ($inUser->id != $id && !$inUser->is_admin){ cmsCore::error404(); }

    $opt        = $inCore->request('opt', 'str', 'in');

    $del_flag   = $opt=='in' ? 'to_del' : 'from_del';
    $id_flag    = $opt=='in' ? 'to_id' : 'from_id';

    $inDB->query("UPDATE cms_user_msg SET {$del_flag}=1 WHERE {$id_flag}='{$id}'");
    $inDB->query("DELETE FROM cms_user_msg WHERE to_del=1 AND from_del=1") ;

    $inCore->addSessionMessage($_LANG['MESS_ALL_DEL_OK'], 'info');

	$inCore->redirectBack();

}//do
///////////////////////////////////////////// KARMA LOG /////////////////////////////////////////////////////////////////////////
if ($do=='karma'){
	
		$usr = $model->getUserShort($id);
		if (!$usr) { cmsCore::error404(); }
		
				$inPage->setTitle($_LANG['KARMA_HISTORY']);
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['KARMA_HISTORY'], $_SERVER['REQUEST_URI']);
				
		$ksql = "SELECT k.*, k.points as kpoints, u.nickname, u.login
					 FROM cms_user_karma k
					 LEFT JOIN cms_users u ON u.id = k.sender_id
					 WHERE k.user_id = $id
						 ORDER BY k.senddate DESC
						 LIMIT 50";
		$kresult = $inDB->query($ksql);
				
		$karma = array();

				if ($inDB->num_rows($kresult)>0){
					while($k = $inDB->fetch_assoc($kresult)){
				$k['fsenddate'] = $inCore->dateFormat($k['senddate'], true, true);
				$k['kpoints']   = karmaPoints($k['kpoints']);
				$karma[]        = $k;
					}
		}

		$smarty = $inCore->initSmarty('components', 'com_users_karma.tpl');
		$smarty->assign('karma', $karma);
		$smarty->assign('usr', $usr);
		$smarty->display('com_users_karma.tpl');
}
/////////////////////////////// GIVE AWARD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='giveaward'){

    if (!$inUser->is_admin) { $inCore->halt(); }

	if (usrCheckAuth()){

		$from_id = $inUser->id;
		$to_id = $id;
		
		$usr = $model->getUserShort($id);
		if (!$usr) { cmsCore::error404(); }
		
				$inPage->setTitle($_LANG['AWARD_USER']);
				$inPage->addHeadJS('components/users/js/awards.js');
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['AWARD'], $_SERVER['REQUEST_URI']);
					
				if(!isset($_POST['gosend'])){		
					
			$smarty = $inCore->initSmarty('components', 'com_users_awards_give.tpl');
			$smarty->assign('usr', $usr);
			$smarty->assign('awardslist', usrAwardsList('aw.gif'));
			$smarty->display('com_users_awards_give.tpl');

				} else {

					$title = $inCore->request('title', 'str', $_LANG['AWRD']);
					$description = $inCore->request('description', 'str', '');
					$imageurl = $inCore->request('imageurl', 'str', $_LANG['AWRD']);
					$award_id = 0;					
					if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/users/awards/'.$imageurl)){							
						$sql = "INSERT INTO cms_user_awards (user_id, pubdate, title, description, imageurl, from_id, award_id)
								VALUES ('$to_id', NOW(), '$title', '$description', '$imageurl', '$from_id', '$award_id')";
				$inDB->query($sql);
				$award_id = $inDB->get_last_id('cms_user_awards');
				//регистрируем событие
				cmsActions::log('add_award', array(
						'object' => '"'.$title.'"',
						'user_id' => $to_id,
						'object_url' => '',
						'object_id' => $award_id,
						'target' => '',
						'target_url' => '',
						'target_id' => 0, 
						'description' => '<img src="/images/users/awards/'.$imageurl.'" border="0" alt="'.$description.'">'
				));
						cmsUser::sendMessage(USER_UPDATER, $to_id, '<b>'.$_LANG['RECEIVED_AWARD'].':</b> <a href="'.cmsUser::getProfileURL($usr['login']).'">'.$title.'</a>');
					}
					$inCore->redirect(cmsUser::getProfileURL($usr['login']));
				}						

	} else { echo usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// DELETE AWARD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='delaward'){
	if (usrCheckAuth()){

		$sql = "SELECT user_id FROM cms_user_awards WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
		if ($inDB->num_rows($result)){
			$aw = $inDB->fetch_assoc($result);
			if ($aw['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id)){
				$inDB->query("DELETE FROM cms_user_awards WHERE id = $id LIMIT 1");
				cmsActions::removeObjectLog('add_award', $id);
			}
		}
	}
	$inCore->redirectBack();
}//do
///////////////////////// DELETE PROFILE /////////////////////////////////////////////////////////////////////////////
if ($do == 'delprofile'){

    $inPage->backButton(false);

	if (usrCheckAuth()){
			
		$data = $model->getUserShort($id);
		if (!$data) { cmsCore::error404(); }
			
				if (isset($_REQUEST['confirm'])){
					if ($inUser->id == $data['id'] || $inCore->userIsAdmin($inUser->id)){
						$model->deleteUser($id);
                        $user_blog_id = $inDB->get_field('cms_blogs', 'user_id='.$id, 'id');
						if ($user_blog_id) {
                            $inCore->loadModel('blogs');
                            $blog_model = new cms_model_blogs();
                            $blog_model->deleteBlog($user_blog_id);
                        }
					}
					if ($inUser->id == $data['id']){
                        $inCore->redirect('/logout');
                    } else { $inCore->redirect('/users'); }
				} else {				
					//MENU
					$inPage->setTitle($_LANG['DELETING_PROFILE']);
					$inPage->addPathway($data['nickname'], $inUser->getProfileURL($data['login']));
					$inPage->addPathway($_LANG['DELETING_PROFILE'], $_SERVER['REQUEST_URI']);
					if ($inUser->id == $data['id'] || $inCore->userIsAdmin($inUser->id)){
						$GLOBALS['ed_menu'][0]['link'] = 'javascript:window.history.go(-1)';
						$GLOBALS['ed_menu'][0]['title'] = $_LANG['CANCEL'];
						$GLOBALS['ed_page_title'] = $_LANG['DELETING_PROFILE'];
						echo '<div class="con_heading">'.$_LANG['DELETING_PROFILE'].'</div>';
						echo '<p style="margin-bottom:30px">'.$_LANG['REALLY_DEL_PROFILE'].'<br/> '.$_LANG['REALLY_DEL_PROFILE_TEXT'].'</p>';
						echo '<a href="/users/'.$id.'/delprofile-yes.html" class="usr_btnlink">'.$_LANG['YES'].'</a><a href="javascript:window.history.go(-1)" class="usr_btnlink">'.$_LANG['NO'].'</a>';
					} else { echo usrAccessDenied(); }					
				}	

	} else { echo usrAccessDenied(); }
}
/////////////////////////////// RESTORE PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='restoreprofile'){

	if (usrCheckAuth()){

		$usr = $model->getUserShort($id);
		if (!$usr) { cmsCore::error404(); }
				
			if ($inUser->id==$id || $inCore->userIsAdmin($inUser->id)){
				$sql = "UPDATE cms_users SET is_deleted = 0 WHERE id = $id";
				$inDB->query($sql) ;
			}

	}

	if (isset($_SERVER['HTTP_REFERER'])){
		$back = $_SERVER['HTTP_REFERER'];
	} else { $back = '/'; }

	$inCore->redirectBack();
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// VIEW USER FILES ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='files'){

    if (!$cfg['sw_files']) { cmsCore::error404(); }

	//get user
	$usr = $model->getUserShort($id);
	if (!$usr) { cmsCore::error404(); }

			//heading
			$inPage->setTitle($usr['nickname'].' - '.$_LANG['FILES']);
			$inPage->addHeadJS('components/users/js/pageselfiles.js');
			//pathway			
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
			//ordering & paging
			if (isset($_REQUEST['orderby'])) { 
				$orderby = $inCore->request('orderby', 'str');
				$_SESSION['uf_orderby'] = $orderby;
			} elseif(isset($_SESSION['uf_orderby'])) { 
				$orderby = $_SESSION['uf_orderby'];
			} else {
				$orderby = 'pubdate'; 
			}
			if (isset($_REQUEST['orderto'])) { $orderto = $inCore->request('orderto', 'str', ''); } else { $orderto = 'desc'; }
			if (isset($_REQUEST['page'])) { $page = $inCore->request('page', 'int', ''); } else { $page = 1; }	
			$perpage = 20;
			//get files on page
			if ($inUser->id!=$id){
				$allowsql = "AND allow_who='all'";
			} else {
				$allowsql = '';
			}
			$sql = "SELECT *
			FROM cms_user_files
					WHERE user_id = $id $allowsql
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";			
			$result = $inDB->query($sql) ;
			//get total files count
			$total_files = $inDB->rows_count('cms_user_files', 'user_id = '.$id.' '.$allowsql.'');
			//calculate free space
			$max_mb = $cfg['filessize'];
			$current_bytes = $max_mb ? usrFilesSize($id) : false;							
			if ($current_bytes) { $current_mb = round(($current_bytes / 1024) / 1024, 2); } else { $current_mb = 0; }
			$free_mb = $max_mb ? round($max_mb - $current_mb, 2) : '';
			$is_files = false;
			$myprofile = ($inUser->id==$id);
			if ($inDB->num_rows($result)){ 
				$is_files = true;
				//page and ordering select table
				$pagination = pageSelectFiles($total_files, $page, $perpage);

				$rownum = 0;
				//build file list rows
				$files = array();
				while($file = $inDB->fetch_assoc($result)){
						$file['filelink'] = HOST.'/users/files/download'.$file['id'].'.html';
						if ($rownum % 2) { $file['class'] = 'usr_list_row1'; } else { $file['class'] = 'usr_list_row2'; }
						$file['fileicon'] 	= $inCore->fileIcon($file['filename']);
						$file['mb'] 		= round(($file['filesize']/1024)/1024, 2);if ($mb == '0') { $mb = '~ 0'; }
						$file['rownum'] 	= $rownum; 
						$file['pubdate'] 	= $inCore->dateFormat($file['pubdate'], true, true);
						$rownum++;
						$files[] = $file;
							}
							
								}

			$smarty = $inCore->initSmarty('components', 'com_users_file_view.tpl');
			$smarty->assign('usr', $usr);
			$smarty->assign('orderby', $orderby);
			$smarty->assign('orderto', $orderto);
			$smarty->assign('cfg', $cfg);
			$smarty->assign('total_files', $total_files);
			$smarty->assign('is_files', $is_files);
			$smarty->assign('free_mb', $free_mb);
			$smarty->assign('pagination', $pagination);
			$smarty->assign('myprofile', $myprofile);
			$smarty->assign('files', $files);
			$smarty->display('com_users_file_view.tpl');
	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='download'){

    if (!$cfg['sw_files']) { cmsCore::error404(); }

    $file_id = $inCore->request('fileid', 'int', 0);

    if (!$file_id) { $inCore->redirectBack(); }

	$allowsql = $inUser->id ? '' : "AND allow_who='all'";

	$sql = "SELECT user_id, filename, allow_who FROM cms_user_files WHERE id = $file_id $allowsql LIMIT 1";
	$result = $inDB->query($sql);

	if ($inDB->num_rows($result)){
		$file       = $inDB->fetch_assoc($result);
		$name       = $file['filename'];
		$uid        = $file['user_id'];
		$fileurl    = '/upload/userfiles/'.$uid.'/'.$name;
		if ($uid != $inUser->id && $file['allow_who'] != 'all') { $inCore->halt($_LANG['FILE_HIDEN']); }

		if (file_exists(PATH.$fileurl)){
			$sql = "UPDATE cms_user_files SET hits = hits + 1 WHERE id = $file_id";
			$inDB->query($sql);
			header('Content-Disposition: attachment; filename='.basename($fileurl) . "\n");
			header('Content-Type: application/x-force-download; name="'.$fileurl.'"' . "\n");
			header('Location:'.$fileurl);
		} else { $inCore->halt($_LANG['FILE_NOT_FOUND']); }
	} else { $inCore->halt($_LANG['FILE_NOT_FOUND']); }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addfile'){

    if (!$cfg['sw_files']) { cmsCore::error404(); }

	if (!$inUser->id) { cmsUser::goToLogin(); }

	if ($inUser->id != $id){ cmsCore::error404(); }
		
			$max_mb         = $cfg['filessize'];
			$current_bytes  = usrFilesSize($id);
			if ($current_bytes) { $current_mb = round(($current_bytes / 1024) / 1024, 2); } else { $current_mb = 0; }
			$free_mb = round($max_mb - $current_mb, 2);
		
	if($inCore->inRequest('upload')){
				
				$size_mb      = 0;
				$size_limit   = false;
				$loaded_files = array();
				
            $list_files = array();

            foreach($_FILES['upfile'] as $key=>$value) {
                foreach($value as $k=>$v) { $list_files['upfile'.$k][$key] = $v; }
            }

            foreach ($list_files as $key=>$data_array) {
					$error = $data_array['error'];
					if ($error == UPLOAD_ERR_OK) {

                        $upload_dir = PATH.'/upload/userfiles/'.$id;
						@mkdir($upload_dir);
                        file_put_contents($upload_dir.'/index.html', '');
					
						$tmp_name   = $data_array["tmp_name"];
                        $name       = $data_array["name"];
						$size       = $inCore->strClear($data_array["size"]);
						$size_mb    += round(($size/1024)/1024, 2);
						
				// проверяем тип файла
						$types 		= $cfg['filestype'] ? $cfg['filestype'] : 'jpeg,gif,png,jpg,bmp,zip,rar,tar';
						$maytypes 	= explode(',', str_replace(' ', '', $types));  
						$path_parts = pathinfo($name);
				// расширение файла
						$ext        = strtolower($path_parts['extension']);
				// флаг существования расширения в разрешенных
				$may        = in_array($ext, $maytypes);
				if(!$may) { cmsCore::addSessionMessage($_LANG['ERROR_TYPE_FILE'].': '.$types, 'error'); $inCore->redirectBack(); }
				
				// Переводим имя файла в транслит
				// отделяем имя файла от расширения
				$name  = substr($name, 0, strrpos($name, '.'));
				// транслитируем
				$name  = cmsCore::strToURL($name);
				// присоединяем расширения файла
				$name .= '.'.$ext;
				// Обрабатываем получившееся имя файла для записи в БД
				$name  = $inCore->strClear($name);

				// Проверяем свободное место
				if ($size_mb > $free_mb && $cfg['filessize']){ cmsCore::addSessionMessage($_LANG['YOUR_FILE_LIMIT'].' ('.$max_mb.' '.$_LANG['MBITE'].') '.$_LANG['IS_OVER_LIMIT'].'<br>'.$_LANG['FOR_NEW_FILE_DEL_OLD'], 'error'); $inCore->redirectBack(); }

				// Загружаем файл	
				if ($inCore->moveUploadedFile($tmp_name, PATH."/upload/userfiles/$id/$name", $error)) {
						
									$loaded_files[] = $name;

									$sql = "INSERT INTO cms_user_files(user_id, filename, pubdate, allow_who, filesize, hits)
											VALUES ($id, '$name', NOW(), 'all', '$size', 0)";
									$inDB->query($sql);
									$file_id = $inDB->get_last_id('cms_user_files');
									cmsActions::log('add_file', array(
										  'object' => $name,
										  'object_url' => '/users/files/download'.$file_id.'.html',
										  'object_id' => $file_id,
										  'target' => '',
										  'target_url' => '',
										  'description' => ''
									));
				
				}						

				}
				}
										
				if (sizeof($loaded_files)){
			
			$ok_message  = '<div><strong>'.$_LANG['UPLOADED_FILES'].':</strong></div>';
			$ok_message .= '<ul>';

						foreach($loaded_files as $k=>$val){
				$ok_message .= '<li>'.$val.'</li>';						
						}

			$ok_message .= '</ul>';

					if ($cfg['filessize']){
				$ok_message .= '<div style="margin-top:10px"><strong>'.$_LANG['FREE_SPACE_LEFT'].':</strong> '.round($free_mb-$size_mb, 2).' '.$_LANG['MBITE'].'</div>';
					}

			cmsCore::addSessionMessage($ok_message, 'info');

				} else {
			
			cmsCore::addSessionMessage($_LANG['ERR_BIG_FILE'].' '.$_LANG['ERR_FILE_NAME'], 'error');

				}
				
		$inCore->redirect('/users/'.$id.'/files.html');
							
	}

	if(!$inCore->inRequest('upload')){
							
				$usr = $model->getUserShort($id);
				if (!$usr) { cmsCore::error404(); }
		
					$inPage->setTitle($_LANG['UPLOAD_FILES']);
					$inPage->backButton(false);
                    $inPage->addHeadJS('includes/jquery/multifile/jquery.multifile.js');
					
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['UPLOAD_FILES'], $_SERVER['REQUEST_URI']);
					
						$post_max_b = return_bytes(ini_get('upload_max_filesize'));
						$post_max_mb = (round($post_max_b/1024)/1024) . ' '.$_LANG['MBITE'];
					
					$smarty = $inCore->initSmarty('components', 'com_users_file_add.tpl');
					$smarty->assign('free_mb', $free_mb);
					$smarty->assign('post_max_b', $post_max_b);
					$smarty->assign('post_max_mb', $post_max_mb);
					$smarty->assign('cfg', $cfg);
		$smarty->assign('messages', cmsCore::getSessionMessages());
					$smarty->assign('types', $cfg['filestype'] ? $cfg['filestype'] : 'jpeg,gif,png,jpg,bmp,zip,rar,tar');
					$smarty->display('com_users_file_add.tpl');

				}
		
}

/////////////////////////////// FILE DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfile'){

	if (!$cfg['sw_files']) { cmsCore::error404(); }
	
	$fileid = $inCore->request('fileid', 'int', 0);	
	
	if (usrCheckAuth() && ($inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$usr = $model->getUserShort($id);
			if (!$usr) { cmsCore::error404(); }

				$inPage->backButton(false);
				$sql = "SELECT filename FROM cms_user_files WHERE id = $fileid AND user_id = $id";
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$file = $inDB->fetch_assoc($result);				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['DELETE_FILE'], $_SERVER['REQUEST_URI']);
					
					$confirm['title']                   = $_LANG['DELETING_FILE'];
					$confirm['text']                    = $_LANG['YOU_REALLY_DEL_FILE'].' "'.$file['filename'].'"?';
					$confirm['action']                  = $_SERVER['REQUEST_URI'];
					$confirm['yes_button']              = array();
					$confirm['yes_button']['type']      = 'submit';
					$confirm['yes_button']['name']  	= 'godelete';
					$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
					$smarty->assign('confirm', $confirm);
					$smarty->display('action_confirm.tpl');
				} else { echo usrAccessDenied(); }

		} else {
			$sql = "SELECT filename FROM cms_user_files WHERE id = $fileid AND user_id = $id";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$file = $inDB->fetch_assoc($result);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/userfiles/'.$id.'/'.$file['filename']);
				$inDB->query("DELETE FROM cms_user_files WHERE id = $fileid");
				cmsActions::removeObjectLog('add_file', $fileid);
			}			
			header('location:/users/'.$id.'/files.html');
		}
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfilelist'){

	if (!$cfg['sw_files']) { cmsCore::error404(); }
	
	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die($_LANG['NOT_SELECTED_FILES']); }
	
	if (usrCheckAuth() && ($inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$usr = $model->getUserShort($id);
			if (!$usr) { cmsCore::error404(); }

				$inPage->backButton(false);
				
				//build file list sql
				$t = 0;
				foreach($files as $key=>$value){
					$findsql .= "id = ".intval($value); 
					if ($t<sizeof($files)-1) { $findsql .= " OR "; }
					$t++;
				}				
				
				$sql = "SELECT id, filename FROM cms_user_files WHERE user_id = $id AND ($findsql)";							
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['DELETE_FILES'], $_SERVER['REQUEST_URI']);
					$html = '<ul>';
						while ($file = $inDB->fetch_assoc($result)){ 
							$html .= '<li>';
								$html .=  $file['filename'] . '<input type="hidden" name="files[]" value="'.$file['id'].'"/>';	
							$html .= '</li>';
						}
					$html .= '</ul>';
					
					$confirm['title']                   = $_LANG['DELETING_FILES'];
					$confirm['text']                    = $_LANG['YOU_REALLY_DEL_FILES'].'?';
					$confirm['action']                  = $_SERVER['REQUEST_URI'];
					$confirm['yes_button']              = array();
					$confirm['yes_button']['type']      = 'submit';
					$confirm['yes_button']['name']  	= 'godelete';
					$confirm['other']  					= $html;
					$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
					$smarty->assign('confirm', $confirm);
					$smarty->display('action_confirm.tpl');

				} else { echo usrAccessDenied(); }

		} else {
				//build file list sql
				$t = 0;
				foreach($files as $key=>$value){
					$findsql .= "id = ".intval($value); 
					if ($t<sizeof($files)-1) { $findsql .= " OR "; }
					$t++;
				}				
				
				$sql = "SELECT id, filename FROM cms_user_files WHERE user_id = $id AND ($findsql)";							
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					while ($file = $inDB->fetch_assoc($result)){
						@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/userfiles/'.$id.'/'.$file['filename']);
					cmsActions::removeObjectLog('add_file', $file['id']);
					}
					$inDB->query("DELETE FROM cms_user_files WHERE $findsql");
				}			
				header('location:/users/'.$id.'/files.html');
		}
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES PUBLISHING /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='pubfilelist'){
    if (!$cfg['sw_files']) { cmsCore::error404(); }

	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die($_LANG['NOT_SELECTED_FILES']); }
	
	$allow = $inCore->request('allow', 'str', 'nobody');
	
	if (usrCheckAuth() && ($inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		$t = 0;
		foreach($files as $key=>$value){
			$findsql .= "id = ".intval($value); 
			if ($t<sizeof($files)-1) { $findsql .= " OR "; }
			$t++;
		}				
				
		$inDB->query("UPDATE cms_user_files SET allow_who = '$allow' WHERE $findsql") ;
		header('location:/users/'.$id.'/files.html');
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// VIEW AWARDS LIST ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='awardslist'){
	$inPage->setTitle($_LANG['SITE_AWARDS']);
	
	$inPage->addPathway($_LANG['SITE_AWARDS']);

	$sql = "SELECT * 
			FROM cms_user_autoawards
			WHERE published = 1
			ORDER BY title";			
	$result = $inDB->query($sql) ;

	$is_yes_awards = false;
			
	if ($inDB->num_rows($result)){
		$is_yes_awards = true;
		$aws = array();
		while($aw = $inDB->fetch_assoc($result)){
				//Перебираем все награды и ищем пользователей с текущей наградой
					$sql =  "SELECT u.id as id, u.nickname as nickname, u.login as login, IFNULL(p.gender, 'm') as gender
							 FROM cms_user_awards aw
							 LEFT JOIN cms_users u ON u.id = aw.user_id
							 LEFT JOIN cms_user_profiles p ON p.user_id = u.id
							 LEFT JOIN cms_user_autoawards a ON a.id = aw.award_id
							 WHERE aw.award_id = ".$aw['id'];
					$rs = $inDB->query($sql) ;
					$total = $inDB->num_rows($rs);
					$aw['uhtml'] = '';
					if ($total){
						$row = 0;
						while ($user = $inDB->fetch_assoc($rs)){
							$row++;
							$aw['uhtml'] .= '<a href="'.cmsUser::getProfileURL($user['login']).'" id="'.$user['gender'].'">'.$user['nickname'].'</a>';
							if ($row<$total){ $aw['uhtml'] .= ', '; }
						}
					} else {
						$aw['uhtml'] = $_LANG['NOT_USERS_WITH_THIS_AWARD'];
					}
			$aws[] = $aw;
					}
								
		}
				
	$smarty = $inCore->initSmarty('components', 'com_users_awards_site.tpl');
	$smarty->assign('is_yes_awards', $is_yes_awards);
	$smarty->assign('uhtml', $uhtml);
	$smarty->assign('aws', $aws);
	$smarty->display('com_users_awards_site.tpl');	
	
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='votekarma'){

    $sign   = $inCore->request('sign', 'str', 'plus');
    $to     = $inCore->request('to', 'int', 0);
    $from   = $inCore->request('from', 'int', 0);
	$is_ajax = $inCore->request('is_ajax', 'int', 0);

    if (!$to || !$from) { if ($is_ajax) { return; } else { $inCore->redirectBack(); } }

    $inCore = cmsCore::getInstance();

    if ($inUser->id){
		if (usrCanKarma($to, $from)){
			if ($from == $inUser->id){
                $points = ($sign=='plus' ? 1 : -1);
				$inDB->query("INSERT INTO cms_user_karma (user_id, sender_id, points, senddate) VALUES ('$to', '$from', '$points', NOW())");
				$inDB->query("UPDATE cms_user_profiles SET karma = karma + '$points' WHERE user_id = $to");
				cmsUser::checkAwards($to);
			}
		}
	}
	if (!$is_ajax) { $inCore->redirectBack(); }
	$points = strip_tags( cmsUser::getKarmaFormat($to, false), '<table><tr><td><img><a>' );
	$points_int = strip_tags($points);
	if ($points_int >= 0) {
		$points = '<div class="value-positive">'.$points.'</div>';
	} else {
		$points = '<div class="value-negative">'.$points.'</div>';
	}
	echo $points;

	exit;

}
////////////////////////// DELETE FROM WALL ////////////////////////////////////////////////////////
	if ($do=='wall_delete'){
        $usertype   = $inCore->request('usertype',  'str', 'user');
        $record_id  = $inCore->request('record_id', 'int', 0);
        $my_id      = $inUser->id;
		if ($record_id && $my_id){
			
				if ($usertype=='user'){
					$can_delete = $inDB->get_fields('cms_user_wall', "id = '$record_id' AND (user_id = '$my_id' OR author_id = '$my_id')", 'author_id, user_id');
					$author_id     = $can_delete['author_id'];
					$wall_user_id  = $can_delete['user_id'];
				}
                elseif ($usertype=='club'){
					$inCore->loadLib('clubs');
					$club_id        = $inDB->get_field('cms_user_wall', "id = '$record_id'", 'user_id');
                    $is_club_admin  = clubUserIsAdmin($club_id, $my_id);
                    $is_club_moder  = clubUserIsRole($club_id, $my_id, 'moderator');
                    $is_author      = $inDB->rows_count('cms_user_wall', "id = '$record_id' AND author_id = '$my_id'");
					$can_delete     = $is_author || $is_club_admin || $is_club_moder;
				}

				if ($can_delete || $inCore->userIsAdmin( $my_id )){
					$inDB->query("DELETE FROM cms_user_wall WHERE id = '$record_id' LIMIT 1");
					switch ($usertype){
						case 'user': ($author_id == $my_id && $wall_user_id == $my_id) ? cmsActions::removeObjectLog('add_wall_my', $record_id) : cmsActions::removeObjectLog('add_wall', $record_id); break;
						case 'club': cmsActions::removeObjectLog('add_wall_club', $record_id); break;
					}
				}
				$inCore->addSessionMessage($_LANG['WALL_MESG_DEL'], 'info');
		}
        $inCore->redirectBack();
	}

////////////////////////// ADD TO WALL /////////////////////////////////////////
	if ($do=='wall_add'){

        $usertype   = $inCore->request('usertype', 'str', 'user');
        $user_id    = $inCore->request('user_id', 'int', 0);
        $author_id  = $inUser->id;
		$message 	= $inCore->request('message', 'html', ''); 
		$message 	= $inCore->parseSmiles($message, true); 
		$message 	= $inDB->escape_string($message); 
		$errors 	= false;
		if (strlen($message)<2) { $inCore->addSessionMessage($_LANG['ERR_SEND_WALL'], 'error'); $errors = true; }

		if ($message && $user_id && $author_id && !$errors){
			switch ($usertype){
				case 'user': 	$usr  = $model->getUserShort($user_id);
								if (!$usr) { cmsCore::error404(); }
                $sql = "INSERT INTO cms_user_wall (user_id, author_id, pubdate, content, usertype)
                        VALUES ('$user_id', '$author_id', NOW(), '$message', '$usertype')";
                $inDB->query($sql);
								$wall_id = $inDB->get_last_id('cms_user_wall');
								if ($author_id != $user_id){
									//регистрируем событие
									cmsActions::log('add_wall', array(
											'object' => $usr['nickname'],
											'object_url' => cmsUser::getProfileURL($usr['login']),
											'object_id' => $wall_id,
											'target' => '',
											'target_url' => '',
											'target_id' => 0, 
											'description' => strlen(strip_tags($message))>100 ? substr(strip_tags($message), 0, 100) : strip_tags($message)
									));
								} elseif($author_id == $user_id) {
									cmsActions::log('add_wall_my', array(
											'object' => '',
											'object_url' => '',
											'object_id' => $wall_id,
											'target' => '',
											'target_url' => '',
											'target_id' => 0, 
											'description' => strlen(strip_tags($message))>100 ? substr(strip_tags($message), 0, 100) : strip_tags($message)
									));
								}
                    //send email notification, if user want it
                    $usr['email_newmsg']   = $inDB->get_field('cms_user_profiles', "user_id='{$user_id}'", 'email_newmsg');
                    if ($usr['email_newmsg'] && $user_id != $author_id){
                            $inConf = cmsConfig::getInstance();
                            //fetch target user
										$to_email       = $inDB->get_field('cms_users', 'id='.$user_id, 'email');
                            $postdate       = date('d/m/Y H:i:s');
										$from_nick      = $inDB->get_field('cms_users', "id='{$author_id}'", 'nickname');
							$profilelink    = HOST . cmsUser::getProfileURL($usr['login']);

                            $letter_path    = PATH.'/includes/letters/newwallpost.txt';
                            $letter         = file_get_contents($letter_path);

                            $letter= str_replace('{sitename}', $inConf->sitename, $letter);
                            $letter= str_replace('{profilelink}', $profilelink, $letter);
                            $letter= str_replace('{date}', $postdate, $letter);
                            $letter= str_replace('{from}', $from_nick, $letter);
                            $inCore->mailText($to_email, $_LANG['NEW_POST_ON_WALL'].'! - '.$inConf->sitename, $letter);
                    }
						break;
				
				case 'club':	$club = $inDB->get_fields('cms_clubs', "id=$user_id", 'id, title');
								if (!$club) { $inCore->redirectBack(); }
								$sql = "INSERT INTO cms_user_wall (user_id, author_id, pubdate, content, usertype)
										VALUES ('$user_id', '$author_id', NOW(), '$message', '$usertype')";
								$inDB->query($sql);
								$wall_id = $inDB->get_last_id('cms_user_wall');
								//регистрируем событие
								cmsActions::log('add_wall_club', array(
											'object' => $club['title'],
											'object_url' => '/clubs/'.$club['id'],
											'object_id' => $wall_id,
											'target' => '',
											'target_url' => '',
											'target_id' => 0, 
											'description' => strlen(strip_tags($message))>100 ? substr(strip_tags($message), 0, 100) : strip_tags($message)
								));
						break;
                }

		}
        $inCore->redirectBack();
	}

//============================================================================//
//================================  Инвайты  =================================//
//============================================================================//
if ($do=='invites'){

    $reg_cfg = $inCore->loadComponentConfig('registration');

    if ($reg_cfg['reg_type'] != 'invite') { cmsCore::error404(); }

    $invites_count = $model->getUserInvitesCount($inUser->id);

    if (!$invites_count) { cmsCore::error404(); }

    if (!$inCore->inRequest('send_invite')){

        $inPage->addPathway($inUser->nickname, cmsUser::getProfileURL($inUser->login));
        $inPage->addPathway($_LANG['MY_INVITES']);

        $smarty = $inCore->initSmarty('components', 'com_users_invites.tpl');
        $smarty->assign('invites_count', $invites_count);
        $smarty->display('com_users_invites.tpl');

        return;

    }
    
    if ($inCore->inRequest('send_invite')){
        
        $invite_email = $inCore->request('invite_email', 'email', '');

        if (!$invite_email) { $inCore->redirectBack(); }

        if ($model->sendInvite($inUser->id, $invite_email)){

            cmsCore::addSessionMessage(sprintf($_LANG['INVITE_SENDED'], $invite_email), 'success');

        } else {

            cmsCore::addSessionMessage($_LANG['INVITE_ERROR'], 'error');

        }

        $inCore->redirect(cmsUser::getProfileURL($inUser->login));

    }

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} 
?>