<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
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
    $inCore->loadLanguage('components/users');

    if (!isset($cfg['showgroup'])) { $cfg['showgroup']  = 0; }
    if (!isset($cfg['sw_feed']))   { $cfg['sw_feed']    = 1; }
    if (!isset($cfg['sw_content'])){ $cfg['sw_content'] = 1; }
    if (!isset($cfg['sw_awards'])) { $cfg['sw_awards']  = 1; }
    if (!isset($cfg['sw_search'])) { $cfg['sw_search'] = 1;  }
    if (!isset($cfg['sw_guest']))  { $cfg['sw_guest'] = 1; }
	
	$id     =   $inCore->request('id', 'int', 0);
	$do     =   $inCore->request('do', 'str', 'view');

    $inPage->setTitle($_LANG['USERS']);
	
/////////////////////////////// SEARCH BY CITY ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='city'){

	$city = urldecode($inCore->request('city', 'str', ''));

	$querysql = "SELECT u.*, p.*, u.id as id, u.regdate as fregdate, u.logdate as flogdate
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_locked = 0 AND p.user_id = u.id AND p.city LIKE '%$city%' AND u.is_deleted = 0
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>'.$_LANG['SEARCH_BY_CITY'].':</strong> '.$city.' (<a href="/users/all.html">'.$_LANG['CANCEL_SEARCH'].'</a>)</div>';
	
	$do = 'view';

}
/////////////////////////////// SEARCH BY HOBBY (description part) ///////////////////////////////////////////////////////////////////	
if ($do=='hobby'){

    $hobby = $inCore->request('hobby', 'str', '');

    $hobby = str_replace('\"', '"', $hobby);

    $hobby = strtolower($hobby);

	$querysql = "SELECT u.*, p.*, u.id as id, u.regdate as fregdate, u.logdate as flogdate
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_locked = 0 AND p.user_id = u.id AND (LOWER(p.description) LIKE '%$hobby%' OR LOWER(p.formsdata) LIKE '%$hobby%') AND u.is_deleted = 0
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>'.$_LANG['SEARCH_BY_HOBBY'].':</strong> '.$hobby.' (<a href="/users/all.html">'.$_LANG['CANCEL_SEARCH_SHOWALL'].'</a>)</div>';
	
	$do = 'view';

}
/////////////////////////////// SEARCH USER ///////////////////////////////////////////////////////////////////	
if ($do=='search'){
	$s = '';
	$stext = array();

	if (@$_REQUEST['gender']){
		$val = $inCore->request('gender', 'str', 'm');
		if ($val=='m'){
			$s .= " AND p.gender = 'm'"; 
			$stext[] = $_LANG['MALE'];
		} elseif ($val=='f') {
			$s .= " AND p.gender = 'f'"; 
			$stext[] = $_LANG['FEMALE'];
		}
	}	
	if (@$_REQUEST['agefrom']){
		$val = $inCore->request('agefrom', 'int', 18);
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) >= '.($val*365); 
		$stext[] = $_LANG['NOT_YOUNG']." $val ".$_LANG['YEARS'];
	}			
	if (@$_REQUEST['ageto']){
		$val = $inCore->request('ageto', 'int');
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) <= '.($val*365); 
		$stext[] = $_LANG['NOT_OLD']." $val ".$_LANG['YEARS'];
	}

    if (@$_REQUEST['name']){
		$val = $inCore->request('name', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(u.nickname) LIKE \'%'.$val.'%\'';
		$stext[] = $_LANG['NAME']." &mdash; ".$val;
	}

    if (@$_REQUEST['city']){
		$val = $inCore->request('city', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(p.city) LIKE \''.$val.'%\'';
		$stext[] = $_LANG['CITY']." &mdash; ".$val;
	}

    if (@$_REQUEST['hobby']){
		$val = $inCore->request('hobby', 'str', '');
        $val = strtolower($val);        
		$s .= ' AND (LOWER(p.description) LIKE \'%'.$val.'%\' OR LOWER(p.formsdata) LIKE \'%'.$val.'%\')';
		$stext[] = $_LANG['HOBBY']." &mdash; ".$val;
	}

	$querysql = "SELECT u.*, p.*, u.id as id, u.status as microstatus, 
						u.regdate as fregdate, 
                        u.logdate as flogdate,
                        u.status as microstatus
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_deleted = 0 AND u.is_locked = 0 AND p.user_id = u.id $s
				ORDER BY city DESC";

    echo '<pre>'.$sql.'</pre>';
	
	$querymsg = '<h3>'.$_LANG['SEARCH_RESULT'].'</h3>';
	
	if (sizeof($stext)){
		$querymsg .= '<ul>';
			foreach($stext as $value){
				$querymsg .= '<li>'.$value.';</li>';		
			}
		$querymsg .= '</ul>';
	}
	
	$do = 'view';

}

/////////////////////////////// VIEW USERS LIST ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='view'){
			
	$orderby = $inCore->request('orderby', 'str', 'karma');
	$orderto = $inCore->request('orderto', 'str', 'desc');
	$page	 = $inCore->request('page',	'int', 1);	
	
	if($orderby != 'karma' && $orderby != 'rating') { $orderby = 'karma'; } 
	
	if ($orderto != 'asc' && $orderto != 'desc' ){ $orderto = 'desc'; }
	if ($page <= 0) { $page = 1; }
	
	if ($inCore->inRequest('online')) { $_SESSION['usr_online'] = $inCore->request('online', 'int'); $page = 1; }
	
	$perpage = 20;

    if ($cfg['sw_search']){
        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('citysearch', 'city', false);
    }

	$inPage->addHeadJS('components/users/js/view.js');
	
	if (!isset($querysql)){
		if (!@$_SESSION['usr_online']){
			$sql = "SELECT u.*, p.*, u.id as id, u.is_deleted as is_deleted, u.regdate as fregdate,  p.karma as karma, 
						   u.logdate as flogdate,
                           p.gender as gender, u.status as microstatus
					FROM cms_user_profiles p, cms_users u
					WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";
		} else {
			$sql = "SELECT u.*, p.*, u.id as id, u.is_deleted as is_deleted, u.regdate as fregdate,  p.karma as karma,
						   u.logdate as flogdate,
                           p.gender as gender, u.status as microstatus
					FROM cms_users u, cms_user_profiles p, cms_online o
					WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id AND p.user_id = o.user_id
                    GROUP BY o.user_id
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";
		}
	} else {
		$sql = $querysql;
    }
    
	$result = $inDB->query($sql) ;

	$tsql = "SELECT id FROM cms_users WHERE is_locked = 0 AND is_deleted = 0";
	$tres = $inDB->query($tsql) ;
	
	$total_usr = $inDB->num_rows($tres);
	$is_users  = $inDB->num_rows($result);
	
	$smarty = $inCore->initSmarty('components', 'com_users_view.tpl');			
	if (isset($querymsg)) { $smarty->assign('querymsg', $querymsg);	}
	$smarty->assign('page', $page);	
	
		$people = cmsUser::getOnlineCount();
		
		if (!@$_SESSION['usr_online']) { 
			$online_link = '<a href="/users/online.html" rel=ФnofollowФ>'.$_LANG['SHOW_ONLY_ONLINE'].'</a>';
		} else {
			$online_link = '<a href="/users/all.html" rel=ФnofollowФ>'.$_LANG['SHOW_ALL'].'</a>';
		}
	
		$link['positive'] = '/users/1/karma-desc.html';
		$link['negative'] = '/users/1/karma-asc.html';		
		$link['rating'] = '/users/1/rating-desc.html';			
		if ($orderby == 'karma' && $orderto == 'desc') { 
			$link['selected'] = 'positive'; 
		} elseif ($orderby == 'karma' && $orderto == 'asc') { 
			$link['selected'] = 'negative'; 
		} elseif ($orderby == 'rating'){
			$link['selected'] = 'rating'; 
		}
		
		$gender_stats   = usrGenderStats($total_usr);
		$city_stats     = usrCityStats();
				
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
        $total_usr  = $inDB->rows_count('cms_users', 'is_deleted=0 AND is_locked=0');

        $smarty->assign('is_users', $is_users);
        $smarty->assign('total_usr', $total_usr);

		$bday = cmsUser::getBirthdayUsers();

        $smarty->assign('orderby', $orderby);
        $smarty->assign('orderto', $orderto);

		$smarty->assign('people', $people);
		$smarty->assign('link', $link);
		$smarty->assign('bday', $bday);

        if (isset($autocomplete_js)){ $smarty->assign('autocomplete_js', $autocomplete_js); }

		$smarty->assign('users', $users);
		$smarty->assign('user_id', $inUser->id);	
		$smarty->assign('cfg', $cfg);	
		$smarty->assign('gender_stats', $gender_stats);	
		$smarty->assign('city_stats', $city_stats);	
		$smarty->assign('online_link', $online_link);			
		
		if (!isset($querysql)){
            if (!@$_SESSION['usr_online']){
                $result = $inDB->query("SELECT * FROM cms_users WHERE is_locked=0 AND is_deleted=0") ;
            } else {
                $result = $inDB->query("SELECT p.user_id FROM cms_user_profiles p, cms_online o, cms_users u WHERE p.user_id = o.user_id AND u.id=p.user_id AND u.is_deleted=0") ;
            }
            $total = $inDB->num_rows($result);

			$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/users/%page%/'.$orderby.'-'.$orderto.'.html'));
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

					$gender = $inCore->request('gender', 'str');
					
					$city = $inCore->request('city', 'str');
					if (strlen($city)>20) { cmsCore::addSessionMessage($_LANG['LONG_CITY_NAME'], 'error'); $errors = true; }

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
					
					if ($inCore->inRequest('field')){
						foreach($_POST['field'] as $k=>$val){
							$_POST['field'][$k] = str_replace('\"', '&quot;', $_POST['field'][$k]);
							$_POST['field'][$k] = str_replace('"', '&quot;', $_POST['field'][$k]);
							$_POST['field'][$k] = str_replace("\'", '&#8217;', $_POST['field'][$k]);
							$_POST['field'][$k] = str_replace("'", '&#8217;', $_POST['field'][$k]);
							$_POST['field'][$k] = strip_tags($_POST['field'][$k]);
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

					}

					$inCore->redirect(cmsUser::getProfileURL($inUser->login));
                    
				}				
				
				if ($opt == 'changepass'){
					$emsg = '';
					$msg = '';
					if ($inUser->password==md5($_POST['oldpass'])){ $oldpass = $inCore->request('oldpass', 'str'); } else { $emsg .= $_LANG['OLD_PASS_WRONG'].'<br/>'; }
					if ($_POST['newpass']==$_POST['newpass2']){ $newpass = $inCore->request('newpass', 'str'); } else { $emsg .= $_LANG['WRONG_PASS'].'<br/>'; }

					if (strlen($emsg)==0){
						$sql = "UPDATE cms_users SET password='".md5($newpass)."' WHERE id = $id AND password='".md5($oldpass)."'";
						$inDB->query($sql);
						$msg = $_LANG['PASS_CHANGED'];
					}
					$inCore->redirect(cmsUser::getProfileURL($inUser->login));
				}
		
		
			$sql = "SELECT u.*, p.*, u.id as id, 
							DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, 
							DATE_FORMAT(u.logdate, '%d-%m-%Y') as flogdate,
							DATE_FORMAT(u.birthdate, '%d') as bday,
							DATE_FORMAT(u.birthdate, '%m') as bmonth,
							DATE_FORMAT(u.birthdate, '%Y') as byear,
							IFNULL(p.gender, 0) as gender
					FROM cms_users u, cms_user_profiles p
					WHERE u.is_locked = 0 AND p.user_id = u.id AND u.id = $id
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
					$smarty->assign('msg', $msg);
					$smarty->assign('emsg', $emsg);
					$smarty->assign('usr', $usr);			
					$smarty->assign('dateform', $inCore->getDateForm('birthdate', false, $usr['bday'], $usr['bmonth'], $usr['byear']));
					$smarty->assign('private_forms', $private_forms);		
					$smarty->assign('autocomplete_js', $autocomplete_js);
					
					$smarty->display('com_users_edit_profile.tpl');
				}
				
			} else { echo usrAccessDenied(); }
		
		} else { echo usrAccessDenied(); }
	
	} else { echo usrAccessDenied(); }

}
/////////////////////////////// VIEW USER COMMENTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='comments'){

	$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql) ;

	if ($inDB->num_rows($result)){
		$usr        = $inDB->fetch_assoc($result);

		$page       = $inCore->request('page', 'int', 1);
		$perpage    = 15;
	
		$inPage->setTitle($_LANG['COMMENTS'].' - '.$usr['nickname']);
		$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway($_LANG['COMMENTS'], $_SERVER['REQUEST_URI']);
		
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
                $com['content'] = $inCore->parseSmiles($com['content'], true);
                if ($com['votes']>0){
                    $com['votes'] = '<span class="cmm_good">+'.$com['votes'].'</span>';
                } elseif ($com['votes']<0){
                    $com['votes'] = '<span class="cmm_bad">'.$com['votes'].'</span>';
                }
				$com['fpubdate'] = $inCore->dateFormat($com['pubdate']);
                $comments[] = $com;
			}
			// —читаем общее число комментариев
			$result_total = $inDB->query("SELECT id FROM cms_comments WHERE user_id = '$id' AND published = 1");
			$records_total = $inDB->num_rows($result_total);
			
			$smarty = $inCore->initSmarty('components', 'com_users_comments.tpl');
            $smarty->assign('user_id', $id);
			$smarty->assign('nickname', $usr['nickname']);
			$smarty->assign('login', $usr['login']);
			$smarty->assign('comments', $comments);
            $smarty->assign('avatar', usrImage($id));
			$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, '/users/%user_id%/comments%page%.html', array('user_id'=>$id)));
			$smarty->display('com_users_comments.tpl');	

		} else { echo '<p>'.$_LANG['NO_USER_COMMENT'].'</p>';	}
	} else { cmsCore::error404(); }
		
}
/////////////////////////////// VIEW USER POSTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='forumposts'){

	$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql) ;
    
	if ($inDB->num_rows($result)){
		$usr = $inDB->fetch_assoc($result);

		$page = $inCore->request('page', 'int', 1);
		$perpage = 15;

        $inPage->setTitle($_LANG['POSTS_IN_FORUM'].' - '.$usr['nickname']);
        $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
        $inPage->addPathway($_LANG['POSTS_IN_FORUM'], $_SERVER['REQUEST_URI']);

		$sql = "SELECT *, t.title as topic, p.id as id, t.id as thread_id
                FROM cms_forum_posts p
				LEFT JOIN cms_forum_threads t ON t.id = p.thread_id
                WHERE p.user_id = '$id'
                ORDER BY p.pubdate DESC
                LIMIT ".(($page-1)*$perpage).", $perpage";
				
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
			// —читаем общее число постов на форуме
			$result_total = $inDB->query("SELECT id FROM cms_forum_posts WHERE user_id = $id");
			$records_total = $inDB->num_rows($result_total);
			
			$smarty = $inCore->initSmarty('components', 'com_users_forumposts.tpl');
            $smarty->assign('user_id', $id);
            $smarty->assign('user_login', $usr['login']);
			$smarty->assign('nickname', $usr['nickname']);
			$smarty->assign('posts', $posts);
            $smarty->assign('avatar', usrImage($id));
			$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, '/users/%user_id%/forumposts%page%.html', array('user_id'=>$id)));
			$smarty->display('com_users_forumposts.tpl');

		} else { echo '<p>'.$_LANG['NOT_USER_POSTS_IN_FORUM'].'</p>';	}
	} else { cmsCore::error404(); }

}
/////////////////////////////// VIEW PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='profile'){

	$inCore->loadLib('clubs');

    if (!$id){
        $login = $inCore->request('login', 'str', '');
        $login = urldecode($login);
        $id    = $inDB->get_fields('cms_users', "login='{$login}'", 'id', 'is_deleted ASC');
		$id    = $id['id'];
    }

    $usr = $model->getUser($id);
    
    if (!$usr){
        cmsCore::error404();
	}

	if (!$inUser->id && !$cfg['sw_guest']) {
        $inPage->setTitle($_LANG['ACCESS_DENIED']);
		echo usrNeedReg();
        return;
	}

    $inPage->setTitle($usr['nickname']);
    $inPage->addPathway($usr['nickname']);

    if ( !(usrAllowed($usr['allow_who'], $id) || $inUser->is_admin) ){
        echo usrNotAllowed();
        return;
    }

    $deleted    = $usr['is_deleted'];
    $myprofile  = ($inUser->id == $id);

    $usr['banned'] = dbRowsCount('cms_banlist', 'user_id='.$id);

    if ($deleted){
        $smarty = $inCore->initSmarty('components', 'com_users_deleted.tpl');
        $smarty->assign('is_user', $inUser->id);
        $smarty->assign('id', $id);
        $smarty->assign('nickname', $usr['nickname']);
        $smarty->assign('avatar', usrImage($id, 'big'));
        $smarty->assign('login', $usr['login']);
        $smarty->assign('is_admin', $inUser->is_admin);
        $smarty->assign('others_active', $inDB->rows_count('cms_users', "login='{$usr['login']}' AND is_deleted=0", 1));
        $smarty->display('com_users_deleted.tpl');
        return;
    }

    $usr['avatar']				= usrImage($usr['id'], 'big');
	$usr['isfriend']			= usrIsFriends($usr['id'], $inUser->id);
	$usr['isfriend_not_add']	= usrIsFriends($usr['id'], $inUser->id, false);
    $usr['is_new_friends']		= ($inUser->id==$usr['id'] && $model->isNewFriends($usr['id']) && $cfg['sw_friends']);
    if ($usr['is_new_friends']){
        $usr['new_friends'] 	= usrFriendQueriesList($usr['id'], $model);
    }
    $usr['rating']				= cmsUser::getRating($id);
    $usr['friends']				= usrFriends($usr['id']);

    if ($usr['friends']){
        $usr['friends_photos']	= cmsUser::getUserFriendsPhotos($usr['id']);
        $usr['friends_posts']	= cmsUser::getUserFriendsPosts($usr['id']);
        $usr['friends_comments']	= cmsUser::getUserFriendsComments($usr['id']);
    }

    $usr['awards_html']			= usrAwards($usr['id']);
//    $usr['comments_html'] 		= usrComments($usr['id'], 5);
//    $usr['forum_html'] 			= usrForumPosts($usr['id'], 5);
//    $usr['photos_html']			= usrPhotos($usr['id'], 4);
    $usr['wall_html']			= cmsUser::getUserWall($usr['id']);
    $usr['addwall_html'] 		= cmsUser::getUserAddWall($usr['id']);
    $usr['banned'] 				= cmsUser::isBanned($usr['id']);

    $usr['clubs'] 				= cmsUserClubs($usr['id']);

    $usr['status']				= usrStatus($usr['id']);
    $usr['status_date']         = cmsCore::dateDiffNow($usr['status_date']); 
    $usr['flogdate']            = strip_tags(usrStatus($usr['id'], $usr['flogdate'], false, $usr['gender']));
    $usr['karma']				= strip_tags( cmsUser::getKarmaFormat($usr['id'], false), '<table><tr><td><img><a>' );
    $usr['karma_int']			= strip_tags($usr['karma']);
    $usr['karma_link']			= '<a href="/users/'.$id.'/karma.html" title="'.$_LANG['KARMA_HISTORY'].'" id="karmalink">?</a>';

    $usr['cityurl']             = urlencode($usr['city']);

    $usr['photos_count']		= (int)usrPhotoCount($id);
	$usr['can_add_foto']		= ((usrPhotoCount($usr['id'])<$cfg['photosize'] || $cfg['photosize']==0) && $cfg['sw_photo']);
    $usr['board_count']			= (int)$inDB->rows_count('cms_board_items', "user_id=$id AND published=1");
    $usr['comments_count']		= (int)$inDB->rows_count('cms_comments', "user_id=$id AND published=1");

    if($cfg['sw_files'])
        if ($inUser->id==$id){
            $usr['files_count'] = $inDB->rows_count('cms_user_files', "user_id = ".$id." AND allow_who = 'all'");
        } else {
            $usr['files_count'] = $inDB->rows_count('cms_user_files', 'user_id = '.$id);
        }

    $usr['blog_link'] = '';

    $usr['blog']            = usrBlog($id);
    $usr['blog_id']         = $usr['blog']['id'];
    $usr['blog_seolink']    = $usr['blog']['seolink'];
    
    if($usr['blog_id']){
        $usr['blog_link'] 		= '<a href="/blogs/'.$usr['blog_seolink'].'">'.$_LANG['BLOG'].'</a>';
    } elseif($myprofile) {
        $usr['blog_link'] 		= '<a href="/blogs/createblog.html">'.$_LANG['CREATE_BLOG'].'</a>';
    }

    if (!$usr['description']) {
        $usr['description']		= '<span style="color:#999"><em>'.$_LANG['TAGS_NOT_SPEC'].'</em></span>';
    } else {
        $usr['description']     = cmsPage::getMetaSearchLink('/users/hobby/', $usr['description']);
    }

    $usr['flogdate']			= $inCore->getRusDate($usr['flogdate']);
    $usr['fregdate'] 			= $inCore->dateFormat($usr['fregdate']);
    $usr['birthdate'] 			= $inCore->dateFormat($usr['birthdate']);

    $usr['comments_count'] 		= usrMsg($usr['id'], 'cms_comments');
    $usr['forum_count']			= usrMsg($usr['id'], 'cms_forum_posts');

    $usr['profile_link']        = cmsUser::getProfileURL($usr['login']);

    $usr['genderimg']			= '';
    if ($usr['gender']) {
        switch ($usr['gender']){
            case 'm': $usr['genderimg'] = '<img src="/components/users/images/male.gif"/>'; $usr['gender']=$_LANG['MALES']; break;
            case 'f': $usr['genderimg'] = '<img src="/components/users/images/female.gif"/>'; $usr['gender']=$_LANG['FEMALES']; break;
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
    $smarty->assign('messages', cmsCore::getSessionMessages());
    $smarty->assign('cfg', $cfg);
    $smarty->assign('myprofile', $myprofile);
	$smarty->assign('is_admin', $inUser->is_admin);
    $smarty->assign('is_auth', $inUser->id);

    $smarty->display('com_users_profile.tpl');

}
/////////////////////////////// VIEW PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='messages'){
	if ($inUser->id){
		$sql = "SELECT u.*, p.*, u.id as id, DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, DATE_FORMAT(u.logdate, '%d-%m-%Y') as flogdate
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_locked = 0 AND p.user_id = u.id AND u.id = $id
				LIMIT 1
				";				
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
			if ($inUser->id==$id || $inCore->userIsAdmin($inUser->id)) {
				$inPage->setTitle($_LANG['MY_MESS']);
				$inPage->addPathway($_LANG['MY_MESS'], '/users/'.$id.'/messages.html');
				include 'components/users/messages.php';			
			} else { echo usrAccessDenied(); }
		} else { cmsCore::error404(); }
	} else { echo usrAccessDenied(); }
	
}
/////////////////////////////// AVATAR UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='avatar'){

	if (usrCheckAuth() && @$inUser->id==$id){
		$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->setTitle($_LANG['LOAD_AVATAR']);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['LOAD_AVATAR'], $_SERVER['REQUEST_URI']);
			echo '<div class="con_heading">'.$_LANG['LOAD_AVATAR'].'</div>';
	
			if ($inCore->inRequest('upload')) {
				$inCore->includeGraphics();
		
				if ($inCore->inRequest('userid')){
					$userid = $inCore->request('userid');
					if ($userid == $inUser->id){
						$uploaddir 		= $_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/';		
						$realfile		= $_FILES['picture']['name'];
						$filename 		= md5($realfile . '-' . $userid . '-' . time()).'.jpg';
						$uploadfile		= $uploaddir . $realfile;
						$uploadavatar 	= $uploaddir . $filename;
						$uploadthumb 	= $uploaddir . 'small/' . $filename;
						
						$source			= $_FILES['picture']['tmp_name'];
					 	$errorCode 		= $_FILES['picture']['error'];
						
						if ($inCore->moveUploadedFile($source, $uploadfile, $errorCode)) {

                            //DELETE OLD AVATAR
							$sql = "SELECT imageurl FROM cms_user_profiles WHERE id = $userid";
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
							if (isset($cfg['medh'])) { 	 $medh = $cfg['medh']; } else { $medh = 200; }
							
							@img_resize($uploadfile, $uploadavatar, $medw, $medh);
							@img_resize($uploadfile, $uploadthumb, $smallw, $smallw);
							
							//DELETE ORIGINAL							
							@unlink($uploadfile);
							//MODIFY PROFILE
							$sql = "UPDATE cms_user_profiles 
									SET imageurl = '$filename'
									WHERE user_id = '$userid'
									LIMIT 1";	
							$inDB->query($sql);
							
							//GO BACK TO PROFILE VIEW			
							$inCore->redirect(cmsUser::getProfileURL($usr['login']));
                            
						} else {
							echo '<p><strong>'.$_LANG['ERROR'].':</strong> '.$inCore->uploadError().'!</p>';
						}
						
						echo '<ul><li><a href="/users/'.$userid.'/avatar.html">'.$_LANG['REPEAT'].'</a></li>'."\n";
						echo '<li><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$_LANG['BACK_TO_PROFILE'].'</a></li></ul>'."\n";
						
					} else { echo usrAccessDenied(); }
				} else { echo usrAccessDenied(); }
			
			} else {
				$smarty = $inCore->initSmarty('components', 'com_users_avatar_upload.tpl');
    			$smarty->assign('id', $id);
    			$smarty->display('com_users_avatar_upload.tpl');
			}	
		}
	}//auth
	else { echo usrAccessDenied(); }
}
/////////////////////////////// AVATAR LIBRARY /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='select_avatar'){

	if (usrCheckAuth() && @$inUser->id==$id){

        $avatars_dir        = $_SERVER['DOCUMENT_ROOT']."/images/users/avatars/library";
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
                $inDB->query($sql) ;

            }

            //GO BACK TO PROFILE VIEW
            $inCore->redirect(cmsUser::getProfileURL($inUser->login));
            
        }

	}//auth
	else { echo usrAccessDenied(); }
}
/////////////////////////////// PHOTO UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addphoto'){

    $inCore->loadLib('tags');
    $inCore->loadLanguage('components/photos');

	if ( $inUser->id==$id || $inCore->userIsAdmin($inUser->id) ){
		$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($inUser->login));
			$inPage->addPathway($_LANG['ADD_PHOTO']);
			
			$inPage->printHeading($_LANG['ADD_PHOTO']);
            $inPage->backButton(false);
	
			if ($inCore->inRequest('upload')) {
				//first upload step
				$inCore->includeGraphics();
		
				if ($inCore->inRequest('userid')){
					$userid = $inCore->request('userid', 'int');
					if ($userid == $inUser->id){

						$uploaddir 				= $_SERVER['DOCUMENT_ROOT'].'/images/users/photos/';		
						$realfile 				= $_FILES['picture']['name'];

						$lid 					= dbGetFields('cms_user_photos', 'id>0', 'id', 'id DESC');
						$lastid 				= $lid['id']+1;	
						$filename 				= md5($lastid.$realfile).'.jpg';					
						
						$uploadfile				= $uploaddir . $realfile;
						$uploadphoto 			= $uploaddir . $filename;
						$uploadthumb['small'] 	= $uploaddir . 'small/' . $filename;
						$uploadthumb['medium']	= $uploaddir . 'medium/' . $filename;
						
						$source					= $_FILES['picture']['tmp_name'];
						$errorCode				= $_FILES['picture']['error'];
						
						if ($inCore->moveUploadedFile($source, $uploadphoto, $errorCode)) {
							if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }
							@img_resize($uploadphoto, $uploadthumb['small'], 96, 96, true);
							@img_resize($uploadphoto, $uploadthumb['medium'], 600, 600, false, $cfg['watermark']);
							if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
                            @unlink($uploadphoto);
							//PREPARE FOR STEP 2
							$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 2');
													
							$inPage->initAutocomplete();
							$autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');
							
							$form_action = '/users/'.$userid.'/addphoto.html';

							$smarty = $inCore->initSmarty('components', 'com_photos_add2.tpl');			
							$smarty->assign('form_action', $form_action);
							$smarty->assign('filename', $filename);
							$smarty->assign('autocomplete_js', $autocomplete_js);
							$smarty->assign('allow_who', 1);
							$smarty->display('com_photos_add2.tpl');							

						} else {
							echo '<p><strong>'.$_LANG['ERROR'].':</strong> '.$inCore->uploadError().'!</p>';
						}
										
					} else { echo usrAccessDenied(); }
				} else { echo usrAccessDenied(); }
			
			} else {
				if ($inCore->inRequest('submit')){		
					//final upload step
					$title 			= $inCore->request('title', 'str');
					$description 	= $inCore->request('description', 'str');					
					$allow_who 		= $inCore->request('allow_who', 'str');

					if ($title == '') { $title = $_LANG['PHOTO_WITHOUT_NAME']; }

					$imageurl = str_replace("'", '',  $inCore->request('imageurl', 'str'));
					$imageurl = str_replace("*", '',  $imageurl);
					$imageurl = str_replace("/", '',  $imageurl);
					$imageurl = str_replace('\\', '', $imageurl);		
												
					//ADD TO ALBUM
					$sql = "INSERT INTO cms_user_photos (user_id, pubdate, title, description, allow_who, hits, imageurl)
							VALUES ($id, NOW(), '$title', '$description', '$allow_who', '0', '$imageurl')";	
					$inDB->query($sql);

					cmsUser::checkAwards($id);

					//INSERT PHOTO TAGS
					$photoid = dbLastId('cms_user_photos');										
					$tags = $inCore->request('tags', 'str');			
					cmsInsertTags($tags, 'userphoto', $photoid);
					
					$smarty = $inCore->initSmarty('components', 'com_photos_added.tpl');			
					$smarty->assign('id', $id);
					$smarty->assign('photoid', $photoid);
					$smarty->assign('url_profile', cmsUser::getProfileURL($inUser->login));
					$smarty->display('com_photos_added.tpl');
					
				} else { 
					if(usrPhotoCount($id, false)<$cfg['photosize'] || $cfg['photosize']==0){
						//upload form
						$inPage->setTitle($_LANG['ADD_PHOTO'].' - '.$_LANG['STEP'].' 1');
						
						$form_action = '/users/'.$id.'/addphoto.html';
						
						$smarty = $inCore->initSmarty('components', 'com_photos_add1.tpl');			
						$smarty->assign('form_action', $form_action);
						$smarty->assign('user_id', $id);
						$smarty->display('com_photos_add1.tpl');							
						
					} else {
						echo '<p><strong>'.$_LANG['PHOTO_LIMIT'].'</strong></p>';
						echo '<p>'.$_LANG['PHOTO_LIMIT_TEXT'].' '.$cfg['photosize'].' '.$_LANG['PHOTOS'].'.<br/>'.$_LANG['FOR_ADD_PHOTO_TEXT'].'</p>';
					}
				}
			}	
		}
	} else { echo usrAccessDenied(); }
}
/////////////////////////////// PHOTO DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delphoto'){
	$inCore->loadLib('tags');
	$max_mb = 2; //max filesize in Mb
	$inCore->loadLanguage('components/photos');
	$photoid = $inCore->request('photoid', 'int', '');	
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
			$result = $inDB->query($sql);
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				$sql = "SELECT * FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$photo = $inDB->fetch_assoc($result);				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['PHOTOALBUM'], '/users/'.$id.'/photoalbum.html');
					$inPage->addPathway($_LANG['DELETE_PHOTO'], $_SERVER['REQUEST_URI']);
					
					$confirm['title']                   = $_LANG['DELETING_PHOTO'];
					$confirm['text']                    = "".$_LANG['REALLY_DELETE_PHOTO']." ".$photo['title']."?";
					$confirm['action']                  = $_SERVER['REQUEST_URI'];
					$confirm['yes_button']              = array();
					$confirm['yes_button']['type']      = 'submit';
					$confirm['yes_button']['name']  	= 'godelete';
					$smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
					$smarty->assign('confirm', $confirm);
					$smarty->display('action_confirm.tpl');

				} else { echo usrAccessDenied(); }
			}
		} else {
			$sql = "SELECT imageurl FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
			$result = $inDB->query($sql);
			if ($inDB->num_rows($result)){
				$photo = $inDB->fetch_assoc($result);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/'.$photo['imageurl']);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/small/'.$photo['imageurl']);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/medium/'.$photo['imageurl']);								
				$inDB->query("DELETE FROM cms_user_photos WHERE id = $photoid") ;
                $inCore->deleteComments('userphoto', $photoid);
				cmsClearTags('userphoto', $photoid);
			}			
			header('location:/users/'.$id.'/photoalbum.html');
		}
	} else { echo usrAccessDenied(); }
}
/////////////////////////////// PHOTO EDIT /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editphoto'){
	$inCore->loadLib('tags');
    $inCore->loadLanguage('components/photos');
	$max_mb = 2; //max filesize in Mb
	
	$user_id = $inUser->id;
	
	$photoid = $inCore->request('photoid', 'int', '');	
	
	if (usrCheckAuth() && ($user_id==$id||$inCore->userIsAdmin($user_id))){
		$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
		$result = $inDB->query($sql);

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['PHOTOALBUM'], '/users/'.$id.'/photoalbum.html');
			$inPage->addPathway($_LANG['EDIT_PHOTO'], $_SERVER['REQUEST_URI']);
			
			if (isset($_POST['save'])){							
					$title = strip_tags(htmlspecialchars($_POST['title'], ENT_QUOTES, 'cp1251'));
					$description = strip_tags(htmlspecialchars($_POST['description'], ENT_QUOTES, 'cp1251'));
					if ($title == '') { $title = $_LANG['PHOTO_WITHOUT_NAME']; }
					$allow_who = strip_tags(htmlspecialchars($_POST['allow_who'], ENT_QUOTES, 'cp1251'));
					//replace file					
					if (@$_FILES['picture']['name']){
						$inCore->includeGraphics();

						//delete old
						$imageurl = $_POST['imageurl'];
						$result = $inDB->query("SELECT id FROM cms_user_photos WHERE user_id = $id AND imageurl = '$imageurl'");
						if ($inDB->num_rows($result)){ //delete only if user is owner
							@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/'.$imageurl);
							@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/small/'.$imageurl);
							@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/medium/'.$imageurl);														
						}
						//upload new
						$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/users/photos/';		
						$realfile = $_FILES['picture']['name'];

						$filename = md5($photoid).'.jpg';
						$uploadfile = $uploaddir . $realfile;
						$uploadphoto = $uploaddir . $filename;
						$uploadthumb = $uploaddir . 'small/' . $filename;
						$uploadthumb2 = $uploaddir . 'medium/' . $filename;
												
						if ($_FILES['picture']['size'] <= $max_mb*1024*1024){
							if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {
								@img_resize($uploadphoto, $uploadthumb, 96, 96, true);
								@img_resize($uploadphoto, $uploadthumb2, 600, 600, false);
                                @unlink($uploadphoto);
								$sql = "UPDATE cms_user_photos
										SET imageurl = '$filename'
										WHERE id = '$photoid' AND user_id = '$id'";
								$inDB->query($sql) or die(mysql_error()."<br/>".$sql);							
							}								
						}
					}	
					//INSERT PHOTO TAGS
					$tags = $_POST['tags'];			
					cmsInsertTags($tags, 'userphoto', $photoid);
									
					//UPDATE ALBUM
					$sql = "UPDATE cms_user_photos
							SET title='$title', 
								description='$description', 
								allow_who='$allow_who' 
							WHERE id = $photoid";	
					$inDB->query($sql) or die(mysql_error()."<br/>".$sql);	
					
					echo '<p><strong>'.$_LANG['PHOTO_SAVED'].'</strong></p>';
					echo '<p>&larr; <a href="/users/'.$id.'/photoalbum.html">'.$_LANG['BACK_TO_PHOTOALBUM'].'</a><br/>';
					echo '&larr; <a href="'.cmsUser::getProfileURL($inUser->login).'">'.$_LANG['BACK_TO_PROFILE'].'</a></p>';
					
				} else { 
							if(isset($_REQUEST['photoid'])){								
								$sql = "SELECT * FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
								$result = $inDB->query($sql);
								if ($inDB->num_rows($result)){	
									$photo = $inDB->fetch_assoc($result);		
									if (isset($photo['id'])) { $photo_tag = cmsTagLine('userphoto', $photo['id'], false); }
									$photo_max_size = ($max_mb * 1024 * 1024);
									
									$smarty = $inCore->initSmarty('components', 'com_photos_edit.tpl');
									$smarty->assign('photo', $photo);
									$smarty->assign('input_poto', '<input type="hidden" name="imageurl" value="'.$photo['imageurl'].'" />');
									$smarty->assign('photo_tag', $photo_tag);
									$smarty->assign('user_foto', true);
									$smarty->assign('action', '/users/'.$id.'/editphoto'.$photoid.'.html');
									$smarty->assign('images', '/images/users/photos/small/'.$photo['imageurl'].'');
									$smarty->assign('photo_max_size', $photo_max_size);
									$smarty->display('com_photos_edit.tpl');
									
								}//photo exists
								else { echo usrAccessDenied(); }
							} //isset photo id
							else { echo usrAccessDenied(); }
						}//print form
		} else { echo usrAccessDenied(); } //user exists
	}//auth
	else { echo usrAccessDenied(); }
}
/////////////////////////////// VIEW ALBUM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewalbum'){

    $sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql);
	
	if (!$inDB->num_rows($result)){ cmsCore::error404(); }

    $usr = $inDB->fetch_assoc($result);
    $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
    $inPage->addPathway($_LANG['PHOTOALBUM'], $_SERVER['REQUEST_URI']);

    echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; '.$_LANG['PHOTOALBUM'].'</div>';

    $photos     = array();

    //ќпредел€ем, друзь€ мы или нет
    $we_friends = $inDB->rows_count('cms_user_friends', "(to_id={$id} AND from_id={$inUser->id}) OR (to_id={$inUser->id} AND from_id={$id})", 1);

    //ћой профиль или нет
    $my_profile = ($inUser->id == $id);

    //‘ильтр приватности
    $filter = '';
    if (!$my_profile){ $filter = "AND ( allow_who='all' OR (allow_who='registered' AND ({$inUser->id}>0)) OR (allow_who='friends' AND ({$we_friends}=1)) )"; }

    //ѕолучаем личные фотографии
    $private_sql = "SELECT id, DATE_FORMAT(pubdate, '%d-%m-%Y') as fpubdate, imageurl as file, hits
                    FROM cms_user_photos
                    WHERE user_id = {$id} $filter";

    $private_res = $inDB->query($private_sql);

    if ($inDB->num_rows($private_res)) {
        while($photo = $inDB->fetch_assoc($private_res)){
            $photo['file']  = '/images/users/photos/small/'.$photo['file'];
            $photo['url']   = '/users/'.$id.'/photo'.$photo['id'].'.html';
            $photos[]       = $photo;
        }
    }

    //ѕолучаем фотографии из галереи
    $public_sql = "SELECT id, DATE_FORMAT(pubdate, '%d-%m-%Y') as fpubdate, file, hits
                    FROM cms_photo_files
                    WHERE user_id = {$id} AND published = 1";

    $public_res = $inDB->query($public_sql);

    if ($inDB->num_rows($public_res)) {
        while($photo = $inDB->fetch_assoc($public_res)){
            $photo['file']  = '/images/photos/small/'.$photo['file'];
            $photo['url']   = '/photos/photo'.$photo['id'].'.html';
            $photos[]       = $photo;
        }
    }

    //ƒелим на страницы
    $total      = sizeof($photos);

    if ($total){
        $perpage        = 20;
        $page           = $inCore->request('page', 'int', 1);
        $pagination     = cmsPage::getPagebar($total, $page, $perpage, '/users/%user_id%/photoalbum%page%.html', array('user_id'=>$id));
        $page_photos    = array();
        $start          = $perpage*($page-1);
        for($p=$start; $p<$start+$perpage; $p++){
            if ($photos[$p]){
                $page_photos[] = $photos[$p];
            }
        }
        $photos = $page_photos; unset($page_photos);
    }

    //ќтдаем в шаблон
    $smarty = $inCore->initSmarty('components', 'com_users_photos.tpl');
	$smarty->assign('photos', $photos);
	$smarty->assign('user_id', $id);
	$smarty->assign('my_profile', $my_profile);
	$smarty->assign('pagebar', $pagination);
    $smarty->display('com_users_photos.tpl');

}

/////////////////////////////// VIEW BOARD ENTRIES ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewboard'){ 
	$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql);
	// если есть пользователь с таким id
	if ($inDB->num_rows($result)>0){
		$usr = $inDB->fetch_assoc($result);
		$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway($_LANG['ADVS']);
        $inPage->setTitle($_LANG['ADVS'].' - '.$usr['nickname']);
        // выбираем объ€влени€ пользовател€ по его id
		$sql = "SELECT *
				FROM cms_board_items
				WHERE user_id = $id
				ORDER BY pubdate DESC
				";
		$perpage = 10; // объ€влений на странице
		if (isset($_REQUEST['page'])) { $page = $inCore->request('page', 'int', ''); } else { $page = 1; }
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
					$con['pubdate'] = $inCore->dateFormat($con['pubdate']);
				$cons[] = $con;
				}										
		$is_con = true;
		// —читаем общее число объ€влений
		$result_total = $inDB->query("SELECT id FROM cms_board_items WHERE user_id = '$id'");
		$records_total = $inDB->num_rows($result_total);
		} 
		// отдаем в шаблон
		$smarty = $inCore->initSmarty('components', 'com_users_boards.tpl');
		$smarty->assign('usr', $usr);
		$smarty->assign('cons', $cons);
        $smarty->assign('is_con', $is_con);
		$smarty->assign('pagebar', cmsPage::getPagebar($records_total, $page, $perpage, '/users/%user_id%/board%page%.html', array('user_id'=>$id)));
		$smarty->display('com_users_boards.tpl');					
	} else { cmsCore::error404(); }
}

/////////////////////////////// FRIENDS LIST /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='friendlist'){
	$sql = "SELECT id, login, nickname FROM cms_users WHERE id = $id LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){
		if (usrCheckAuth()){

            $page = $inCore->request('page', 'int', 1);
			$perpage = 15;
				
			$usr = $inDB->fetch_assoc($result);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['FRIENDS'], $_SERVER['REQUEST_URI']);

			echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; '.$_LANG['FRIENDS'].'</div>';
	
			echo usrFriends($usr['id'], false);
		
		} else { echo usrNeedReg(); }
	} else { cmsCore::error404(); }
}

/////////////////////////////// VIEW PHOTO /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewphoto'){
    $photoid = $inCore->request('photoid', 'int', 0);

	$user_id = $inUser->id;

	if ($user_id){
		$myprofile = ($user_id == $id);
	} else { $myprofile = false; }

	$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql) ;
	

	if ($inDB->num_rows($result)>0){
			$usr = $inDB->fetch_assoc($result);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));

			$sql = "SELECT * FROM cms_user_photos WHERE id = '$photoid' AND user_id = '$id'";
			$result = $inDB->query($sql) ;

			if ($inDB->num_rows($result)>0){
				$photo = $inDB->fetch_assoc($result);
				
				$inDB->query("UPDATE cms_user_photos SET hits = hits + 1 WHERE id = ".$photo['id']) ;
	
				$inPage->addPathway($_LANG['PHOTOALBUM'], '/users/'.$id.'/photoalbum.html');
				$inPage->addPathway($photo['title'], $_SERVER['REQUEST_URI']);

				if (usrAllowed($photo['allow_who'], $id) || $inCore->userIsAdmin($inUser->id)){
					$photo['pubdate'] = $inCore->dateFormat($photo['pubdate'], true, false, false);
					$photo['genderlink'] = cmsUser::getGenderLink($usr['id'], $usr['nickname'], 0, '', $usr['login']);
					$photo['filesize'] = round(filesize($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/medium/'.$photo['imageurl'])/1024, 2);
					//ссылки на предыдущую и следующую фотографии
					$previd = dbGetFields('cms_user_photos', 'id<'.$photo['id'].' AND user_id = '.$usr['id'], 'id, title, pubdate', 'id DESC');
					$nextid = dbGetFields('cms_user_photos', 'id>'.$photo['id'].' AND user_id = '.$usr['id'], 'id, title, pubdate', 'id ASC');

					$is_photo = true;	
				} else { $is_photo = false; }
			
				$smarty = $inCore->initSmarty('components', 'com_users_photos_view.tpl');
				$smarty->assign('photo', $photo);
				$smarty->assign('bbcode', '[IMG]http://'.$_SERVER['HTTP_HOST'].'/images/users/photos/medium/'.$photo['imageurl'].'[/IMG]');
				$smarty->assign('previd', $previd);
				$smarty->assign('nextid', $nextid);
				$smarty->assign('usr', $usr);
				$smarty->assign('myprofile', $myprofile);
				$smarty->assign('is_admin', $inCore->userIsAdmin($user_id));
				$smarty->assign('is_photo', $is_photo);
				if($is_photo){
					$inCore->loadLib('tags');
					$smarty->assign('tagbar', cmsTagBar('userphoto', $photo['id']));
				}
				$smarty->display('com_users_photos_view.tpl');	
			
				//show user comments
				if($inCore->isComponentInstalled('comments') && $is_photo){
					$inCore->includeComments();
					comments('userphoto', $photo['id']);
				}	
								
			} else { cmsCore::error404(); }
	} else { cmsCore::error404(); }
}
/////////////////////////////// ADD FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addfriend'){

    $sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";

    $result = $inDB->query($sql);
    if (!$inDB->num_rows($result)){ $inCore->redirectBack(); }

    $usr = $inDB->fetch_assoc($result);

	if (usrCheckAuth() && $inUser->id!=$id){
	if(!usrIsFriends($id, $inUser->id)){
		if (!isset($_POST['goadd'])){
			if ($model->isNewFriends($inUser->id, $id)){
				$sql = "UPDATE cms_user_friends SET is_accepted = 1 WHERE to_id = ".$inUser->id." AND from_id = $id";
				$inDB->query($sql);
				header('location:'.$_SERVER['HTTP_REFERER']);
			}

			$sql = "SELECT * FROM cms_users WHERE id = $id";
			
				$inPage->backButton(false);
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['ADD_TO_FRIEND']);
                $inPage->backButton(false);
				echo '<div class="con_heading">'.$_LANG['ADD_TO_FRIEND'].'</div>';
				echo '<p><strong>'.$_LANG['SEND_TO_USER'].' '.ucfirst($usr['nickname']).' '.$_LANG['FRIENDSHIP_OFFER'].'?</strong></p>';
				echo '<p>'.$_LANG['IF'].' '.ucfirst($usr['nickname']).' '.$_LANG['SUCCESS_TEXT'].'</p>';
				echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
						<input style="font-size:24px; width:100px" type="button" name="cancel" value="'.$_LANG['NO'].'" onclick="window.history.go(-1)" />
						<input style="font-size:24px; width:100px" type="submit" name="goadd" value="'.$_LANG['YES'].'" />
					 </p></form></div>';

		} else {
				$to_id      = $id;
				$from_id    = $inUser->id;
				if (!usrIsFriends($to_id, $from_id, false)){
					$sql = "INSERT INTO cms_user_friends (to_id, from_id, logdate, is_accepted) 
							VALUES ('$to_id', '$from_id', NOW(), '0')";
					$inDB->query($sql) ;
				}
				
				cmsUser::sendMessage(USER_UPDATER, $to_id, '[b]'.$_LANG['RECEIVED_F_O'].'[/b]. '.$_LANG['YOU_CAN_SEE'].' [url='.cmsUser::getProfileURL($usr['login']).']'.$_LANG['INPROFILE'].'[/url].');
				
				$inCore->redirect(cmsUser::getProfileURL($usr['login']));
		}//!goadd
		} else { $inCore->redirectBack(); }
	} else { echo usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// DEL FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfriend'){
	if (usrCheckAuth() && @$inUser->id!=$id){

		$first_id = $inUser->id;
		$second_id = $id;
		
		$sql = "DELETE FROM cms_user_friends WHERE ((to_id = $first_id AND from_id = $second_id) OR (to_id = $second_id AND from_id = $first_id))";
		$inDB->query($sql) ;

		header('location:'.$_SERVER['HTTP_REFERER']);

	} else { echo usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// SEND MESSAGE ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='sendmessage'){
	if (usrCheckAuth() && $inUser->id!=$id || isset($_POST['massmail'])){

		$from_id    = $inUser->id;
		$to_id      = $id;
		
		$sql        = "SELECT * FROM cms_users WHERE id = $id LIMIT 1";
		$result     = $inDB->query($sql) ;
        $usr        = $inDB->fetch_assoc($result);

		if ($usr || isset($_POST['massmail'])){
			if (usrCheckAuth()){

				$inPage->setTitle($_LANG['SEND_MESS']);
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['SEND_MESS'], $_SERVER['REQUEST_URI']);
					
				if(!isset($_POST['gosend'])){		
					if (isset($_REQUEST['replyid'])) { $replyid = $inCore->request('replyid', 'int', ''); }
					else { $replyid = 0; }
					
					if ($replyid){
						$sql = "SELECT m.*, u.* 
								FROM cms_user_msg m, cms_users u
								WHERE m.id = $replyid AND m.from_id = u.id AND m.to_id = $from_id";
						$result = $inDB->query($sql) ;
					
						if ($inDB->num_rows($result)>0){
							$msg = $inDB->fetch_assoc($result);
							echo '<div>';
								echo '<div class="con_heading">'.$_LANG['ORIGINAL_MESS'].'</div>';
								echo '<div class="usr_msgreply_source">';
									echo '<div class="usr_msgreply_sourcetext">'.$msg['message'].'</div>';
									echo '<div class="usr_msgreply_author"><a href="'.cmsUser::getProfileURL($msg['login']).'">'.$msg['nickname'].'</a>, '.$msg['senddate'].'</div>';
								echo '</div>';
							echo '</div>';
						} else {
							die();
						}
					}

					echo '<div class="con_heading">'.$_LANG['SEND_MESS'].'</div>';

					echo '<table width="100%" cellpadding="0" cellspacing="5"><tr>';
					
					echo '<td width="200" height="200" valign="top">
							<div style="background-color:#FFFFFF;padding:5px;border:solid 1px gray;text-align:center">
								'.usrLink(usrImage($usr['id'], 'big'), $usr['login']).'
							</div>
							<div style="padding:5px;width:100%">
								 ому: '.usrLink($usr['nickname'], $usr['login']).'
							</div>
						 </td>';						 
					echo '<td valign="top">';
						echo '<form action="" method="POST" name="msgform">';
							echo '<div class="usr_msg_bbcodebox">';
								echo cmsPage::getBBCodeToolbar('message');
							echo '</div>';							
							echo cmsPage::getSmilesPanel('message');
							echo '<textarea style="font-size:18px;border:solid 1px gray;width:100%;height:200px;" name="message" id="message"></textarea>';
							if ($inCore->userIsAdmin($inUser->id)){
								echo '<input name="massmail" type="checkbox" value="1" /> '.$_LANG['SEND_TO_ALL'];
							}
							echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="'.$_LANG['SEND'].'" style="font-size:18px"/> ';
							echo '<input type="button" name="gosend" value="'.$_LANG['CANCEL'].'" style="font-size:18px" onclick="window.history.go(-1)"/></div>';
						echo '</form>';
					echo '</td>';					
					echo '</tr></table>';
				} else {
				
					$message = strip_tags($_POST['message'], '<a><img><b><u><i><table><tr><td>');
					$message = htmlspecialchars($message, ENT_QUOTES, 'cp1251');							
				
					if (!isset($_POST['massmail'])){
						//send private message
						$sql = "INSERT INTO cms_user_msg (to_id, from_id, senddate, is_new, message)
								VALUES ('$to_id', '$from_id', NOW(), 1, '$message')";																
						$inDB->query($sql) ;
						
						$msg_id = dbLastId('cms_user_msg'); 
						
						//send email notification, if user want it
						$needmail = dbGetField('cms_user_profiles', "user_id='{$to_id}'", 'email_newmsg');
						//ѕровер€ем, если юзер онлайн, то уведомление на почту не отправл€ем.
						$isonline = dbGetField('cms_online', "user_id='{$to_id}'", 'id');
						if (!$isonline){
						if ($needmail){
								$inConf     = cmsConfig::getInstance();
														
								$postdate   = date('d/m/Y H:i:s');
								$to_email   = dbGetField('cms_users', "id='{$to_id}'", 'email');
								$from_nick  = dbGetField('cms_users', "id='{$from_id}'", 'nickname');
								$answerlink = HOST.'/users/'.$from_id.'/reply'.$msg_id.'.html';
						
								$letter_path    = PATH.'/includes/letters/newmessage.txt';
								$letter         = file_get_contents($letter_path);
								
								$letter= str_replace('{sitename}', $inConf->sitename, $letter);
								$letter= str_replace('{answerlink}', $answerlink, $letter);
								$letter= str_replace('{date}', $postdate, $letter);
								$letter= str_replace('{from}', $from_nick, $letter);	
								$inCore->mailText($to_email, $_LANG['YOU_HAVE_NEW_MESS'].'! - '.$inConf->sitename, $letter);
						}
						}
					} else {
						if ($inUser->is_admin){
							$userlist = dbGetTable('cms_users', ' id > 0 AND is_locked = 0 AND is_deleted = 0');
							foreach ($userlist as $key=>$usr){
								$sql = "INSERT INTO cms_user_msg (to_id, from_id, senddate, is_new, message)
										VALUES ('".$usr['id']."', '-2', NOW(), 1, '$message')";
								$inDB->query($sql) ;
							}
						}
					}
											
					$inCore->redirect('/users/'.$inUser->id.'/messages-sent.html');
				}
			
			}
		}
	} else { 
        echo usrAccessDenied();
        } //usrCheckAuth
}//do
/////////////////////////////// DEL MESSAGE /////////////////////////////////////////////////////////////////////////////////////
if ($do=='delmessage'){
	if (usrCheckAuth()){
		$sql = "SELECT * FROM cms_user_msg WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
		if ($inDB->num_rows($result)){
			$msg = $inDB->fetch_assoc($result);
			if ($msg['to_id']==$inUser->id || ($msg['from_id']==$inUser->id && $msg['is_new'])){
				$inDB->query("DELETE FROM cms_user_msg WHERE id = $id LIMIT 1") ;
			}
		}
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}//do
/////////////////////////////// DELETE ALL INBOX MESSAGES ///////////////////////////////////////////////////////////////////////
if ($do=='delmessages'){
	if (usrCheckAuth()){
		if($inUser->id == $id || $inCore->userIsAdmin($inUser->id)){
			$sql = "DELETE FROM cms_user_msg WHERE to_id = $id";
			$inDB->query($sql) ;
		}
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}//do
///////////////////////////////////////////// KARMA LOG /////////////////////////////////////////////////////////////////////////
if ($do=='karma'){
		$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
				$usr = $inDB->fetch_assoc($result);
		
				$inPage->setTitle($_LANG['KARMA_HISTORY']);
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['KARMA_HISTORY'], $_SERVER['REQUEST_URI']);
				
				echo '<div class="con_heading">'.$_LANG['KARMA_HISTORY'].' - '.$usr['nickname'].'</div>';
				
				$ksql = "SELECT k.*, k.points as kpoints, DATE_FORMAT(k.senddate, '%d-%m-%Y {$_LANG['IN']} %H:%i') fsenddate, u.*
						 FROM cms_user_karma k, cms_users u
						 WHERE k.user_id = $id AND k.sender_id = u.id
						 ORDER BY k.senddate DESC
						 LIMIT 50";
				$kresult = $inDB->query($ksql) ;
				
				if ($inDB->num_rows($kresult)>0){
					echo '<table width="">';
					while($k = $inDB->fetch_assoc($kresult)){
						echo '<tr>';
							echo '<td style="border-bottom:solid 1px silver" width="150" valign="middle">'.$k['fsenddate'].'</td>';
							echo '<td style="border-bottom:solid 1px silver" width="200" valign="middle"><a href="'.cmsUser::getProfileURL($k['login']).'">'.$k['nickname'].'</a></td>';
							echo '<td style="border-bottom:solid 1px silver" width="100" valign="middle" align="center">'.karmaPoints($k['kpoints']).'</td>';														
						echo '</tr>';
					}
					echo '</table>';
				
				} else { echo '<p>'.$_LANG['KARMA_NOT_MODIFY'].'</p><p>'.$_LANG['KARMA_NOT_MODIFY_TEXT'].'</p><p>'.$_LANG['KARMA_DESCRIPTION'].'</p>'; }

		} else { cmsCore::error404(); }
}
/////////////////////////////// GIVE AWARD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='giveaward'){

    if (!$inUser->is_admin) { $inCore->halt(); }

	if (usrCheckAuth()){

		$from_id = $inUser->id;
		$to_id = $id;
		
		$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
				$usr = $inDB->fetch_assoc($result);
		
				$inPage->setTitle($_LANG['AWARD_USER']);
				$inPage->addHeadJS('components/users/js/awards.js');
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway($_LANG['AWARD'], $_SERVER['REQUEST_URI']);
					
				if(!isset($_POST['gosend'])){		
					
					echo '<div class="con_heading">'.$_LANG['AWARD_USER'].'</div>';
					echo '<form action="" method="POST" name="addform" id="addform">';
						echo '<table width="100%" cellpadding="0" cellspacing="5">';				
							echo '<tr>';
								echo '<td width="150" valign="middle">'.$_LANG['AWARD_IMG'].':</td>';
								echo '<td valign="middle">';
									echo '<div style="overflow:hidden;_height:1%">'.usrAwardsList('aw.gif').'</div>';
								echo '</td>';												
							echo '</tr>';
							echo '<tr>';
								echo '<td width="150">'.$_LANG['AWARD_NAME'].':</td>';
								echo '<td><input type="text" name="title" size="35" /></td>';												
							echo '</tr>';
							echo '<tr>';
								echo '<td width="150">'.$_LANG['AWARD_DESC'].':</td>';
								echo '<td><textarea name="description" cols="35" rows="4"></textarea></td>';												
							echo '</tr>';
						echo '</table>';

						echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="'.$_LANG['TO_AWARD'].'" style="font-size:18px"/> ';
						echo '<input type="button" name="gosend" value="'.$_LANG['CANCEL'].'" style="font-size:18px" onclick="window.history.go(-1)"/></div>';

					echo '</form>';

				} else {
					$title = $inCore->request('title', 'str', $_LANG['AWRD']);
					$description = $inCore->request('description', 'str', '');
					$imageurl = $inCore->request('imageurl', 'str', $_LANG['AWRD']);
					$award_id = 0;					
					if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/users/awards/'.$imageurl)){							
						$sql = "INSERT INTO cms_user_awards (user_id, pubdate, title, description, imageurl, from_id, award_id)
								VALUES ('$to_id', NOW(), '$title', '$description', '$imageurl', '$from_id', '$award_id')";
						$inDB->query($sql) ;
						cmsUser::sendMessage(USER_UPDATER, $to_id, '[b]'.$_LANG['RECEIVED_AWARD'].':[/b] [url='.cmsUser::getProfileURL($usr['login']).']'.$title.'[/url]');
					}
					$inCore->redirect(cmsUser::getProfileURL($usr['login']));
				}						
		}
	} else { echo usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// DELETE AWARD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='delaward'){
	if (usrCheckAuth()){
		$sql = "SELECT * FROM cms_user_awards WHERE id = $id LIMIT 1";
		$result = $inDB->query($sql) ;
		if ($inDB->num_rows($result)){
			$aw = $inDB->fetch_assoc($result);
			if ($aw['user_id']==$inUser->id || $inCore->userIsAdmin($inUser->id)){
				$inDB->query("DELETE FROM cms_user_awards WHERE id = $id LIMIT 1") ;
			}
		}
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}//do
///////////////////////// DELETE PROFILE /////////////////////////////////////////////////////////////////////////////
if ($do == 'delprofile'){
    $inPage->backButton(false);
	if (usrCheckAuth()){
		if ($id){	
			$user_sql = "SELECT id FROM cms_users WHERE id = '$id' LIMIT 1";
			$result = $inDB->query($user_sql) ;
			
			if ($inDB->num_rows($result)){
				$data = $inDB->fetch_assoc($result);				
				if (isset($_REQUEST['confirm'])){
					if ($inUser->id == $data['id'] || $inCore->userIsAdmin($inUser->id)){
						$inDB->query("UPDATE cms_users SET is_deleted = 1 WHERE id = $id");	
						$inDB->query("DELETE FROM cms_user_friends WHERE to_id = $id OR from_id = $id");
						$user_blog_id = dbGetField('cms_blogs', 'user_id='.$id, 'id');
						if ($user_blog_id) {
                            $inCore->loadModel('blogs');
                            $blog_model = new cms_model_blogs();
                            $blog_model->deleteBlog($user_blog_id);
                        }
					}
					if ($inUser->id == $data['id']){
                        $inCore->redirect('/logout');
                    } else { $inCore->redirect(cmsUser::getProfileURL($data['login'])); }
				} else {				
					//MENU
					if ($inUser->id == $data['id'] || $inCore->userIsAdmin($inUser->id)){
						$GLOBALS['ed_menu'][0]['link'] = 'javascript:window.history.go(-1)';
						$GLOBALS['ed_menu'][0]['title'] = $_LANG['CANCEL'];
						$GLOBALS['ed_page_title'] = $_LANG['DELETING_PROFILE'];
						echo '<div class="con_heading">'.$_LANG['DELETING_PROFILE'].'</div>';
						echo '<p style="margin-bottom:30px">'.$_LANG['REALLY_DEL_PROFILE'].'<br/> '.$_LANG['REALLY_DEL_PROFILE_TEXT'].'</p>';
						echo '<a href="/users/'.$id.'/delprofile-yes.html" class="usr_btnlink">'.$_LANG['YES'].'</a><a href="javascript:window.history.go(-1)" class="usr_btnlink">'.$_LANG['NO'].'</a>';
					} else { echo usrAccessDenied(); }					
				}	
			}
		}
	} else { echo usrAccessDenied(); }
}
/////////////////////////////// RESTORE PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='restoreprofile'){
	if (usrCheckAuth()){
		$sql = "SELECT *
				FROM cms_users
				WHERE id = '$id'
				LIMIT 1
				";
				
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
			if ($inUser->id==$id || $inCore->userIsAdmin($inUser->id)){
				$sql = "UPDATE cms_users SET is_deleted = 0 WHERE id = $id";
				$inDB->query($sql) ;
			}
		}
	}

	if (isset($_SERVER['HTTP_REFERER'])){
		$back = $_SERVER['HTTP_REFERER'];
	} else { $back = '/'; }

	header('location:'.$back);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// VIEW USER FILES ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='files'){
	//get user
	$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
	$result = $inDB->query($sql) ;
	//if user found
	if ($inDB->num_rows($result)>0){
			$usr = $inDB->fetch_assoc($result);			
			//heading
			echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; '.$_LANG['FILES'].'</div>';
			$inPage->setTitle($usr['nickname'].' - '.$_LANG['FILES']);
			$inPage->addHeadJS('components/users/js/pageselfiles.js');
			//pathway			
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
			//ordering & paging
			//ordering
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
			$perpage = 25;
			//get files on page
			if ($inUser->id!=$id){
				$allowsql = "AND allow_who='all'";
			} else {
				$allowsql = '';
			}
			$sql = "SELECT *
					FROM cms_user_files f
					WHERE user_id = $id $allowsql
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";			
			$result = $inDB->query($sql) ;
			//get total files count
			$tsql = "SELECT id FROM cms_user_files WHERE user_id = $id $allowsql";
			$tres = $inDB->query($tsql) ;
			$total_files = $inDB->num_rows($tres);
			//calculate free space
			$max_mb = $cfg['filessize'];
			$current_bytes = usrFilesSize($id);							
			if ($current_bytes) { $current_mb = round(($current_bytes / 1024) / 1024, 2); } else { $current_mb = 0; }
			$free_mb = round($max_mb - $current_mb, 2);
			//upload link
			if ($inUser->id == $id && $free_mb > 0){
				echo '<div></div>';
			}	
			if ($inDB->num_rows($result)){ //if files exists
				//page and ordering select table
				echo '<div class="usr_files_orderbar">
					  <table width="100%" cellspacing="0" cellpadding="2">';
				echo '<tr>';
					//page select
					echo pageSelectFiles($total_files, $page, $perpage);
					echo '<td width="15">&nbsp;</td>';
					//file statistics
					echo '<td width="80"><strong>'.$_LANG['FILE_COUNT'].': </strong>'.$total_files.'</td>';
					if ($inUser->id==$id){
						echo '<td width="130"><strong>'.$_LANG['FREE'].': </strong>'.$free_mb.' '.$_LANG['MBITE'].'</td>';
						echo '<td width="16"><img src="/components/users/images/upload.gif" border="0" /></td>';
						echo '<td width="100"><a href="addfile.html">'.$_LANG['UPLOAD_FILES'].'</a></td>';
					}
					//order buttons
					if ($total_files>1){
						echo '<td align="right">
							   <form name="orderform" method="post" action="" style="margin:0px">
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'pubdate\')" name="order_date" value="'.$_LANG['ORDER_BY_DATE'].'" '.($orderby=='pubdate'?'disabled':'').'/>
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'filename\')" name="order_title" value="'.$_LANG['ORDER_BY_NAME'].'" '.($orderby=='filename'?'disabled':'').'/>
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'filesize\')" name="order_size" value="'.$_LANG['ORDER_BY_SIZE'].'" '.($orderby=='filesize'?'disabled':'').'/>
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'hits\')" name="order_hits" value="'.$_LANG['ORDER_BY_DOWNLOAD'].'" '.($orderby=='hits'?'disabled':'').'/>
								   <input id="orderby" type="hidden" name="orderby" value="'.$orderby.'"/>
								</form>
							  </td>';
					} else {
						echo '<td>&nbsp;</td>';						
					}
				echo '</tr>';
				echo '</table></div>';
				//file list headers	
				echo '<form name="listform" id="listform" action="" method="post">';		
				echo '<table width="100%" cellspacing="0" cellpadding="5" style="border:solid 1px gray">';
					echo '<tr>';
						echo '<td class="usr_files_head" width="20" align="center">#</td>';
						echo '<td class="usr_files_head" width="" colspan="2">'.$_LANG['FILE_NAME'].' '.($orderby=='filename'?'&darr;':'').'</td>';
						if ($inUser->id==$id){
							echo '<td class="usr_files_head" width="100" align="center">'.$_LANG['VISIBILITY'].'</td>';
						}
						echo '<td class="usr_files_head" width="100">'.$_LANG['SIZE'].' '.($orderby=='filesize'?'&darr;':'').'</td>';
						echo '<td class="usr_files_head" width="120">'.$_LANG['CREATE_DATE'].' '.($orderby=='pubdate'?'&darr;':'').'</td>';
						echo '<td class="usr_files_head" width="80" align="center">'.$_LANG['DOWNLOAD_HITS'].' '.($orderby=='hits'?'&darr;':'').'</td>';
						if ($inUser->id==$id){
							echo '<td class="usr_files_head" width="16">&nbsp;</td>';
						}
					echo '</tr>';
				$rownum = 0;
				//build file list rows
				while($file = $inDB->fetch_assoc($result)){
						$filelink = 'http://'.$_SERVER['HTTP_HOST'].'/users/files/download'.$file['id'].'.html';
						if ($rownum % 2) { $class = 'usr_list_row1'; } else { $class = 'usr_list_row2'; }
						echo '<tr>';
							if ($inUser->id==$id){
								echo '<td class="'.$class.'" align="center" valign="top"><input id="fileid'.$rownum.'" type="checkbox" name="files[]" value="'.$file['id'].'"/></td>';
							} else {
								echo '<td class="'.$class.'" align="center" valign="top">'.$file['id'].'</td>';							
							}
							echo '<td class="'.$class.'" width="16" valign="top">'.$inCore->fileIcon($file['filename']).'</td>';
							echo '<td class="'.$class.'" valign="top"><a href="'.$filelink.'">'.$file['filename'].'</a><div class="usr_files_link">'.$filelink.'</div></td>';
							
							if ($inUser->id==$id){
								if ($file['allow_who'] == 'all'){
									echo '<td class="'.$class.'" align="center"><img src="/components/users/images/yes.gif" border="0" title="'.$_LANG['FILE_VIS_ALL'].'"/></td>';
								} else {
									echo '<td class="'.$class.'" align="center"><img src="/components/users/images/no.gif" border="0" title="'.$_LANG['FILE_HIDEN'].'"/></td>';
								}
							}
							
							$mb = round(($file['filesize']/1024)/1024, 2);if ($mb == '0') { $mb = '~ 0'; }
							echo '<td class="'.$class.'">'.$mb.' '.$_LANG['MBITE'].'</td>';

							echo '<td class="'.$class.'">'.$file['pubdate'].'</td>';
							echo '<td class="'.$class.'" align="center">'.$file['hits'].'</td>';							
							if ($inUser->id==$id){
								echo '<td class="'.$class.'" align="center">';
								echo '<a href="/users/'.$id.'/delfile'.$file['id'].'.html"><img src="/components/users/images/delete.gif" border="0" alt="'.$_LANG['DELETE_FILE'].'"/></a>';
								echo '</td>';
							}
						echo '</tr>';			
						$rownum++;
				}
				echo '</table>';
				if ($inUser->id==$id){
					echo '<div style="margin-top:6px">
							<input type="button" class="usr_files_orderbtn" name="delete_btn" id="delete_btn" onclick="delFiles()" value="'.$_LANG['DELETE'].'"/>
							<input type="button" class="usr_files_orderbtn" name="hide_btn" id="delete_btn" onclick="pubFiles(0)" value="'.$_LANG['HIDE'].'"/>
							<input type="button" class="usr_files_orderbtn" name="show_btn" id="delete_btn" onclick="pubFiles(1)" value="'.$_LANG['SHOW'].'"/>
						  </div>';
				}
				echo '</form>';
			} else { 
				echo '<p>'.$_LANG['USER_NO_UPLOAD'].'</p>';
				if ($inUser->id==$id){
					echo '<a href="addfile.html">'.$_LANG['UPLOAD_FILE_IN_ARCHIVE'].'</a>';
				}
			} 
	} else { cmsCore::error404(); }
	
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='download'){
    $file_id = $inCore->request('fileid', 'int', 0);

    if (!$file_id) { $inCore->redirectBack(); }

	$sql = "SELECT * FROM cms_user_files WHERE id = $file_id LIMIT 1";
	$result = $inDB->query($sql);

	if ($inDB->num_rows($result)){
		$file       = $inDB->fetch_assoc($result);
		$name       = $file['filename'];
		$uid        = $file['user_id'];
		$fileurl    = '/upload/userfiles/'.$uid.'/'.$name;

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
	if (usrCheckAuth()){
		if ($inUser->id == $id){
		
			$max_mb         = $cfg['filessize'];
			$current_bytes  = usrFilesSize($id);
			if ($current_bytes) { $current_mb = round(($current_bytes / 1024) / 1024, 2); } else { $current_mb = 0; }
			$free_mb = round($max_mb - $current_mb, 2);
		
			if (isset($_POST['upload'])){
				//uploading files
				$inPage->setTitle($_LANG['FILE_UPLOAD_FINISH']);
				$inPage->backButton(false);
				echo '<div class="con_heading">'.$_LANG['FILE_UPLOAD_FINISH'].'</div>';
				
				$e = false;
				
				$size_mb = 0; $size_limit = false;
				$loaded_files = array();
				
				foreach ($_FILES as $key => $data_array) {
					$error = $data_array['error'];
					if ($error == UPLOAD_ERR_OK) {
						@mkdir(PATH.'/upload/userfiles/'.$id);
					
						$tmp_name   = $data_array["tmp_name"];
						$name       = $data_array["name"];
						$size       = $data_array["size"];
						$size_mb    += round(($size/1024)/1024, 2);
						
						if ($size_mb <= $free_mb){
							if( !strstr($name, '.php') &&
                                !strstr($name, '.asp') &&
                                !strstr($name, '.aspx') &&
                                !strstr($name, '.js') &&
                                !strstr($name, '.htm') &&
                                !strstr($name, '.html') &&
                                !strstr($name, '.phtml')
                              ){
								if (move_uploaded_file($tmp_name, PATH."/upload/userfiles/$id/$name")){
									$loaded_files[] = $name;
									$sql = "INSERT INTO cms_user_files(user_id, filename, pubdate, allow_who, filesize, hits)
											VALUES ($id, '$name', NOW(), 'all', '$size', 0)";
									$inDB->query($sql) ;
								}						
							}
						} else { $size_limit = true; }
					}
				}
				
				if ($size_limit) { 
					echo '<div style="color:#660000;margin-bottom:10px;font-weight:bold">'.$_LANG['YOUR_FILE_LIMIT'].' ('.$max_mb.' '.$_LANG['MBITE'].') '.$_LANG['IS_OVER_LIMIT'].'.</div>';
					echo '<div style="color:#660000;font-weight:bold">'.$_LANG['FOR_NEW_FILE_DEL_OLD'].'</div>';
				}
										
				if (sizeof($loaded_files)){
					echo '<div><strong>'.$_LANG['UPLOADED_FILES'].':</strong></div>';
					echo '<ul>';
						foreach($loaded_files as $k=>$val){
							echo '<li>'.$val.'</li>';						
						}
					echo '</ul>';
					
					echo '<div style="margin-top:10px"><strong>'.$_LANG['FREE_SPACE_LEFT'].':</strong> '.round($free_mb-$size_mb, 2).' '.$_LANG['MBITE'].'</div>';
				} else {
					echo '<div style="color:red">'.$_LANG['ERR_BIG_FILE'].'</div>';
					echo '<div style="color:red">'.$_LANG['ERR_FILE_NAME'].'</div>';
				}
				
				echo '<div><a href="/users/'.$id.'/files.html">'.$_LANG['CONTINUE'].'</a> &rarr;</div>';
							
			} else {
				$sql = "SELECT * FROM cms_users WHERE id = $id";
				$result = $inDB->query($sql) ;
				
				if ($inDB->num_rows($result)>0){									
					$usr = $inDB->fetch_assoc($result);
		
					//build upload form
					$inPage->setTitle($_LANG['UPLOAD_FILES']);
					$inPage->backButton(false);
                    $inPage->addHeadJS('includes/jquery/multifile/jquery.multifile.js');
					
					$multi_js = '<script type="text/javascript">
												  function startUpload(){
													$("#upload_btn").attr(\'disabled\', \'true\');
													$("#upload_btn").attr(\'value\', \''.$_LANG['LOADING'].'...\');
													$("#cancel_btn").css(\'display\', \'none\');
													$("#loadergif").css(\'display\', \'block\');
													document.uploadform.submit();													
												  }
											   </script>';

                    $inPage->addHead($multi_js);
				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['UPLOAD_FILES'], $_SERVER['REQUEST_URI']);
					
					echo '<div class="con_heading">'.$_LANG['UPLOAD_FILES'].'</div>';
					
					if ($free_mb>0){
					
						$post_max_b = return_bytes(ini_get('upload_max_filesize'));
						$post_max_mb = (round($post_max_b/1024)/1024) . ' '.$_LANG['MBITE'];
					
						echo '<div>'.$_LANG['SELECT_FILE_TEXT'].'</div>';
						echo '<div>'.$_LANG['ERR_FILE_NAME'].'</div>';
						
						echo '<div style="margin:10px 0px 0px 0px"><strong>'.$_LANG['YOUR_FILE_LIMIT'].':</strong> '.$free_mb.' '.$_LANG['MBITE'].'</div>';
						echo '<div style="margin:0px 0px 10px 0px"><strong>'.$_LANG['MAX_FILE_SIZE'].':</strong> '.$post_max_mb.'</div>';
						
						echo '<form action="" method="post" enctype="multipart/form-data" name="uploadform">';

						echo '<input name="MAX_FILE_SIZE" type="hidden" value="'.$post_max_b.'"/>'. "\n";
						echo '<input type="file" class="multi" name="upfile" id="upfile"/>';														
						echo '<div style="margin-top:20px;overflow:hidden">';
							echo '<input style="float:left;margin-right:4px" type="button" name="upload_btn" id="upload_btn" value="'.$_LANG['UPLOAD_FILES'].'" onclick="startUpload()"/> ';
							echo '<input style="float:left" type="button" name="cancel_btn" id="cancel_btn" value="'.$_LANG['CANCEL'].'" onclick="window.history.go(-1)" />';
							echo '<div id="loadergif" style="display:none;float:left;margin:6px"><img src="/images/ajax-loader.gif" border="0"/></div>';					
						echo '</div>';
							echo '<input type="hidden" name="upload" value="1"/>';							
						echo '</form>';
					} else {
						echo '<div style="color:#660000;margin-bottom:10px;font-weight:bold">'.$_LANG['YOUR_FILE_LIMIT'].' ('.$max_mb.' '.$_LANG['MBITE'].') '.$_LANG['IS_OVER_LIMIT'].'.</div>';
						echo '<div style="color:#660000;font-weight:bold">'.$_LANG['FOR_NEW_FILE_DEL_OLD'].'</div>';
						echo '<div style="margin-top:20px">';
							echo '<input type="button" name="cancel" value="'.$_LANG['CANCEL'].'" onclick="window.history.go(-1)" />';
						echo '</div>';
					}
				}
			}
		
		} else { echo usrAccessDenied(); }	
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// FILE DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfile'){
	$fileid = $inCore->request('fileid', 'int', '');	
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				$sql = "SELECT * FROM cms_user_files WHERE id = $fileid AND user_id = $id";
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$file = $inDB->fetch_assoc($result);				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['DELETE_FILE'], $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">'.$_LANG['DELETING_FILE'].'</div>';
					echo '<p>'.$_LANG['YOU_REALLY_DEL_FILE'].' "'.$file['filename'].'"?</p>';
					echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
							<input style="font-size:24px; width:100px" type="button" name="cancel" value="'.$_LANG['NO'].'" onclick="window.history.go(-1)" />
							<input style="font-size:24px; width:100px" type="submit" name="godelete" value="'.$_LANG['YES'].'" />
						 </p></form></div>';
				} else { echo usrAccessDenied(); }
			} else { cmsCore::error404(); }
		} else {
			$sql = "SELECT filename FROM cms_user_files WHERE id = $fileid AND user_id = $id";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$file = $inDB->fetch_assoc($result);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/userfiles/'.$id.'/'.$file['filename']);
				$inDB->query("DELETE FROM cms_user_files WHERE id = $fileid") ;
			}			
			header('location:/users/'.$id.'/files.html');
		}
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfilelist'){
	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die($_LANG['NOT_SELECTED_FILES']); }
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = '$id' LIMIT 1";
			$result = $inDB->query($sql);
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				
				//build file list sql
				$t = 0;
				foreach($files as $key=>$value){
					$findsql .= "id = ".$value; 
					if ($t<sizeof($files)-1) { $findsql .= " OR "; }
					$t++;
				}				
				
				$sql = "SELECT * FROM cms_user_files WHERE user_id = $id AND ($findsql)";							
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway($_LANG['FILES_ARCHIVE'], '/users/'.$id.'/files.html');
					$inPage->addPathway($_LANG['DELETE_FILES'], $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">'.$_LANG['DELETING_FILES'].'</div>';
					echo '<p><strong>'.$_LANG['YOU_REALLY_DEL_FILES'].'?</strong></p>';

					echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">';
						echo '<ul>';
						while ($file = $inDB->fetch_assoc($result)){ 
							echo '<li>';
								echo $file['filename'] . '<input type="hidden" name="files[]" value="'.$file['id'].'"/>';	
							echo '</li>';
						}
						echo '</ul>';
						echo '<div style="margin-top:10px">';
							echo '<input style="font-size:24px; width:100px" type="button" name="cancel" value="'.$_LANG['NO'].'" onclick="window.history.go(-1)" /> ';
							echo '<input style="font-size:24px; width:100px" type="submit" name="godelete" value="'.$_LANG['YES'].'" />';
						echo '</div>';
					echo '</form>';
				} else { echo usrAccessDenied(); }
			}
		} else {
				//build file list sql
				$t = 0;
				foreach($files as $key=>$value){
					$findsql .= "id = ".$value; 
					if ($t<sizeof($files)-1) { $findsql .= " OR "; }
					$t++;
				}				
				
				$sql = "SELECT * FROM cms_user_files WHERE user_id = $id AND ($findsql)";							
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					while ($file = $inDB->fetch_assoc($result)){
						@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/userfiles/'.$id.'/'.$file['filename']);
					}
					$inDB->query("DELETE FROM cms_user_files WHERE $findsql");
				}			
				header('location:/users/'.$id.'/files.html');
		}
	} else { echo usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES PUBLISHING /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='pubfilelist'){
	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die($_LANG['NOT_SELECTED_FILES']); }
	
	$allow = $inCore->request('allow', 'str', 'nobody');
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		$t = 0;
		foreach($files as $key=>$value){
			$findsql .= "id = ".$value; 
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
				//ѕеребираем все награды и ищем пользователей с текущей наградой
					$sql =  "SELECT u.id as id, u.nickname as nickname, u.login as login, IFNULL(p.gender, 'm') as gender
							 FROM cms_user_awards aw, cms_users u, cms_user_autoawards a, cms_user_profiles p
							 WHERE aw.award_id = a.id AND aw.user_id = u.id AND p.user_id = u.id AND aw.award_id = ".$aw['id'];
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

    if (!$to || !$from) { $inCore->redirectBack(); }

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

	$inCore->redirectBack();

}
////////////////////////// DELETE FROM WALL ////////////////////////////////////////////////////////
	if ($do=='wall_delete'){
        $usertype   = $inCore->request('usertype',  'str', 'user');
        $record_id  = $inCore->request('record_id', 'int', 0);
        $my_id      = $inUser->id;
		if ($record_id && $my_id){
			
				if ($usertype=='user'){
					$can_delete = $inDB->rows_count('cms_user_wall', "id=$record_id AND (user_id=$my_id OR author_id=$my_id)");
				}
                elseif ($usertype=='club'){
					$inCore->loadLib('clubs');
					$club_id        = $inDB->get_field('cms_user_wall', "id=$record_id", 'user_id');
                    $is_club_admin  = clubUserIsAdmin($club_id, $my_id);
                    $is_club_moder  = clubUserIsRole($club_id, $my_id, 'moderator');
                    $is_author      = $inDB->rows_count('cms_user_wall', "id=$record_id AND author_id=$my_id");
					$can_delete     = $is_author || $is_club_admin || $is_club_moder;
				}

				if ($can_delete || $inCore->userIsAdmin( $my_id )){
					$inDB->query("DELETE FROM cms_user_wall WHERE id = $record_id LIMIT 1") ;
				}

		}
        $inCore->redirectBack();
	}

////////////////////////// ADD TO WALL /////////////////////////////////////////////////////////////
	if ($do=='wall_add'){
        $usertype   = $inCore->request('usertype', 'str', 'user');
        $user_id    = $inCore->request('user_id', 'int');
        $message    = $inCore->request('message', 'str');
        $author_id  = $inUser->id;

		if ($message && $user_id && $author_id){

            $result = $inDB->query("SELECT id FROM cms_users WHERE id = '$user_id' LIMIT 1");

            if ($inDB->num_rows($result)===1 || $usertype=='club'){

                $sql = "INSERT INTO cms_user_wall (user_id, author_id, pubdate, content, usertype)
                        VALUES ('$user_id', '$author_id', NOW(), '$message', '$usertype')";
                $inDB->query($sql);

                if ($usertype=='user'){
                    //send email notification, if user want it
                    $user['email_newmsg']   = $inDB->get_field('cms_user_profiles', "user_id='{$user_id}'", 'email_newmsg');
                    $user['login']          = $inDB->get_field('cms_users', "id='{$user_id}'", 'login');
                    if ($user['email_newmsg'] && $user_id != $author_id){
                            $inConf = cmsConfig::getInstance();
                            //fetch target user
                            $to_email       = dbGetField('cms_users', 'id='.$user_id, 'email');
                            $postdate       = date('d/m/Y H:i:s');
                            $from_nick      = dbGetField('cms_users', "id='{$author_id}'", 'nickname');
                            $profilelink    = cmsUser::getProfileURL($user['login']);

                            $letter_path    = PATH.'/includes/letters/newwallpost.txt';
                            $letter         = file_get_contents($letter_path);

                            $letter= str_replace('{sitename}', $inConf->sitename, $letter);
                            $letter= str_replace('{profilelink}', $profilelink, $letter);
                            $letter= str_replace('{date}', $postdate, $letter);
                            $letter= str_replace('{from}', $from_nick, $letter);
                            $inCore->mailText($to_email, $_LANG['NEW_POST_ON_WALL'].'! - '.$inConf->sitename, $letter);
                    }
                }
            }
		}
        $inCore->redirectBack();
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} 
?>