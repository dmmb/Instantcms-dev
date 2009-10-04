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

function karmaPoints($points){
	if ($points > 0){
		return '<span style="font-size:24px;color:green">+'.$points.'</span>';
	} else {
		return '<span style="font-size:24px;color:red">'.$points.'</span>';
	}
	return;
}

function pageBar($current, $perpage, $orderby, $orderto, $records){
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	if (!@$_SESSION['usr_online']){
		$result = $inDB->query("SELECT * FROM cms_users WHERE is_locked=0 AND is_deleted=0") ;
	} else {
		$result = $inDB->query("SELECT p.user_id FROM cms_user_profiles p, cms_online o, cms_users u WHERE p.user_id = o.user_id AND u.id=p.user_id AND u.is_deleted=0") ;
	}
	$records = $inDB->num_rows($result);
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$link = '/users/'.$menuid.'-'.$p.'/'.$orderby.'-'.$orderto.'.html';
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

function pageBarStatic($current, $perpage, $user_id, $page='entries', $table='cms_comment'){
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';
	
	$result = $inDB->query("SELECT id FROM $table WHERE user_id = $user_id");
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$link = '/users/'.$menuid.'/'.$user_id.'/'.$page.$p.'.html';
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

function pageSelectFiles($records, $current, $perpage){
    $inDB   = cmsDatabase::getInstance();
	$html = '';
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<td width="60"><strong>Страница: </strong></span></td>';	

			if ($current>2){
				$html .= '<td width="16"><a href="javascript:goPage('.(1-$current).')" title="Первая"><img src="/images/icons/first.gif" border="0"/></a></td>';	
			}
			if ($current>1) { 
				$html .= '<td width="16"><a href="javascript:goPage(-1)" title="Предыдущая"><img src="/images/icons/prev.gif" border="0"/></a></td>';
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
				$html .= '<td width="16"><a href="javascript:goPage(+1)" title="Следущая"><img src="/images/icons/next.gif" border="0"/></a></td>';
			}
			if ($current<$pages-1){
				$html .= '<td width="16"><a href="javascript:goPage('.($pages-$current).')" title="Последняя"><img src="/images/icons/last.gif" border="0"/></a></td>';	
			}
		}
	}
	return $html;
}

function pageBarBoard($user_id, $current, $perpage){
    $inDB   = cmsDatabase::getInstance();
	$html = '';
	
	$result = $inDB->query("SELECT id FROM cms_board_items WHERE user_id = $user_id") ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					if ($p==1) { $pnum = ''; } else { $pnum = $p; }
					$link = '/users/'.$inCore->menuId().'/'.$user_id.'/board'.$pnum.'.html';					
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

function pageBarAvatars($current, $perpage, $records, $userid){
    $inDB   = cmsDatabase::getInstance();
	global $menuid;
	$html = '';

	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {
					$link = '/users/'.$menuid.'/'.$userid.'/select-avatar-'.$p.'.html';
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

	$inCore->includeFile('components/users/includes/usercore.php');
	$inCore->includeFile('components/users/includes/userforms.php');
	
	$inPage->addHeadCSS('components/users/css/styles.css');
	
	$menuid = $inCore->menuId();

    $inCore->loadModel('users');
    $model = new cms_model_users();

    $cfg = $inCore->loadComponentConfig('users');

    if (!isset($cfg['showgroup'])) { $cfg['showgroup']  = 0; }
    if (!isset($cfg['sw_feed']))   { $cfg['sw_feed']    = 1; }
    if (!isset($cfg['sw_content'])){ $cfg['sw_content'] = 1; }
    if (!isset($cfg['sw_awards'])) { $cfg['sw_awards']  = 1; }
    if (!isset($cfg['sw_search'])) { $cfg['sw_search'] = 1;  }
	
	$id     =   $inCore->request('id', 'int', 0);
	$do     =   $inCore->request('do', 'str', 'view');

    $inPage->setTitle('Пользователи');
	
/////////////////////////////// SEARCH BY CITY ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='city'){

	$city = htmlspecialchars(urldecode($_REQUEST['city']), ENT_QUOTES, 'cp1251');

	$querysql = "SELECT u.*, p.*, u.id as id, DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, DATE_FORMAT(u.logdate, '%d-%m-%Y') as flogdate
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_locked = 0 AND p.user_id = u.id AND p.city LIKE '%$city%' AND u.is_deleted = 0
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>Поиск по городу:</strong> '.$city.' (<a href="/users/'.$menuid.'/all.html">Отменить поиск и показать всех</a>)</div>';
	
	$do = 'view';

}
/////////////////////////////// SEARCH BY HOBBY (description part) ///////////////////////////////////////////////////////////////////	
if ($do=='hobby'){

    $hobby = $inCore->request('hobby', 'str', '');

    $hobby = str_replace('\"', '"', $hobby);

    $hobby = strtolower($hobby);

	$querysql = "SELECT u.*, p.*, u.id as id, DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, DATE_FORMAT(u.logdate, '%d-%m-%Y') as flogdate
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_locked = 0 AND p.user_id = u.id AND (LOWER(p.description) LIKE '%$hobby%' OR LOWER(p.formsdata) LIKE '%$hobby%') AND u.is_deleted = 0
				ORDER BY city DESC";
	
	$querymsg = '<div class="con_description"><strong>Поиск по интересам:</strong> '.$hobby.' (<a href="/users/'.$menuid.'/all.html">Отменить поиск и показать всех</a>)</div>';
	
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
			$stext[] = "Мужчины";
		} elseif ($val=='f') {
			$s .= " AND p.gender = 'f'"; 
			$stext[] = "Женщины";
		}
	}	
	if (@$_REQUEST['agefrom']){
		$val = $inCore->request('agefrom', 'int', 18);
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) >= '.($val*365); 
		$stext[] = "Не моложе $val лет";
	}			
	if (@$_REQUEST['ageto']){
		$val = $inCore->request('ageto', 'int');
		$s .= ' AND DATEDIFF(NOW(), u.birthdate) <= '.($val*365); 
		$stext[] = "Не старше $val лет";
	}

    if (@$_REQUEST['name']){
		$val = $inCore->request('name', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(u.nickname) LIKE \'%'.$val.'%\'';
		$stext[] = "Имя &mdash; ".$val;
	}

    if (@$_REQUEST['city']){
		$val = $inCore->request('city', 'str', '');
        $val = strtolower($val);
		$s .= ' AND LOWER(p.city) LIKE \''.$val.'%\'';
		$stext[] = "Город &mdash; ".$val;
	}

    if (@$_REQUEST['hobby']){
		$val = $inCore->request('hobby', 'str', '');
        $val = strtolower($val);        
		$s .= ' AND (LOWER(p.description) LIKE \'%'.$val.'%\' OR LOWER(p.formsdata) LIKE \'%'.$val.'%\')';
		$stext[] = "Интересы &mdash; ".$val;
	}

	$querysql = "SELECT u.*, p.*, u.id as id, 
						DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate, 
						IF(DATE_FORMAT(u.logdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(u.logdate, 'cегодня в %H:%i'), 
					    IF(u.logdate >= DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_FORMAT(u.logdate, 'вчера в %H:%i'),DATE_FORMAT(u.logdate, '%d-%m-%Y в %H:%i') ))  as flogdate					
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_deleted = 0 AND u.is_locked = 0 AND p.user_id = u.id $s
				ORDER BY city DESC";

    echo '<pre>'.$sql.'</pre>';
	
	$querymsg = '<h3>Результаты поиска</h3>';
	
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
	
	$perpage = 25;

    if ($cfg['sw_search']){
        $inPage->initAutocomplete();
        $autocomplete_js = $inPage->getAutocompleteJS('citysearch', 'city', false);
    }

	$inPage->addHeadJS('components/users/js/view.js');
	
	if (!isset($querysql)){
		if (!@$_SESSION['usr_online']){
			$sql = "SELECT u.*, p.*, u.id as id, u.is_deleted as is_deleted, DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate,  p.karma as karma, 
						   IF(DATE_FORMAT(u.logdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(u.logdate, 'cегодня в %H:%i'), 
						   IF(DATEDIFF(NOW(), u.logdate)=1, DATE_FORMAT(u.logdate, 'вчера в %H:%i'),DATE_FORMAT(u.logdate, '%d-%m-%Y %H:%i') ))  as flogdate,
                           p.gender as gender
					FROM cms_user_profiles p, cms_users u
					WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id
					ORDER BY ".$orderby." ".$orderto."
					LIMIT ".(($page-1)*$perpage).", $perpage";
		} else {
			$sql = "SELECT u.*, p.*, u.id as id, u.is_deleted as is_deleted, DATE_FORMAT(u.regdate, '%d-%m-%Y') as fregdate,  p.karma as karma,
						   IF(DATE_FORMAT(u.logdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(u.logdate, 'cегодня в %H:%i'), 
						   IF(DATEDIFF(NOW(), u.logdate)=1, DATE_FORMAT(u.logdate, 'вчера в %H:%i'),DATE_FORMAT(u.logdate, '%d-%m-%Y %H:%i') ))  as flogdate,
                           p.gender as gender
					FROM cms_users u, cms_user_profiles p, cms_online o
					WHERE u.is_locked = 0 AND u.is_deleted = 0 AND p.user_id = u.id AND p.user_id = o.user_id
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
	$smarty->assign('menuid', $menuid);
	if (isset($querymsg)) { $smarty->assign('querymsg', $querymsg);	}
	$smarty->assign('page', $page);	
	
		$people = cmsUser::getOnlineCount();
		
		if (!@$_SESSION['usr_online']) { 
			$online_link = '<a href="/users/'.$menuid.'/online.html" rel=”nofollow”>Показать только тех, кто онлайн</a>';
		} else {
			$online_link = '<a href="/users/'.$menuid.'/all.html" rel=”nofollow”>Показать всех</a>'; 
		}
	
		$link['positive'] = '/users/'.$menuid.'-1/karma-desc.html';
		$link['negative'] = '/users/'.$menuid.'-1/karma-asc.html';		
		$link['rating'] = '/users/'.$menuid.'-1/rating-desc.html';			
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
					$usr['avatar'] = usrLink(usrImage($usr['id'], 'small'), $usr['id'], $menuid);
					$usr['nickname'] = cmsUser::getProfileLink($usr['login'], $usr['nickname']);
					$usr['myfriend'] = usrIsFriends($usr['id'], $inUser->id, false);
					$usr['karma'] = strip_tags(cmsUser::getKarmaFormat($usr['id'], false, false), '<span>');
					$usr['status'] = usrStatus($usr['id'], $usr['flogdate'], false, $usr['gender']);
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
			$smarty->assign('pagebar', pageBar($page, $perpage, $orderby, $orderto));	
		}
	
	$smarty->display('com_users_view.tpl');		
	
}
/////////////////////////////// EDIT PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editprofile'){

	$opt = $inCore->request('opt', 'str', 'edit');

	if (usrCheckAuth()){
	
		if ($inUser->id==$id || $inCore->userIsAdmin($inUser->id)){
		
				if ($opt == 'save'){
					$emsg = '';
					$msg = '';

					$nickname = $inCore->request('nickname', 'str');
					if (strlen($nickname)<2) { $emsg .= 'Никнейм не может быть короче 2х символов!<br/>'; }

					$gender = $inCore->request('gender', 'str');
					
					$city = $inCore->request('city', 'str');

					$email = $inCore->request('email', 'str');
					if (!strpos($email, '@') || !strpos($email, '.')) { $emsg .= 'Укажите настоящий адрес e=mail!<br/>'; }					
					
					$showmail = $inCore->request('showmail', 'int');
					$email_newmsg = $inCore->request('email_newmsg', 'int');
					
					$showbirth = $inCore->request('showbirth', 'int');
					$description = $inCore->request('description', 'str');
					
					$birthdate = (int)$_REQUEST['birthdate']['year'].'-'.(int)$_REQUEST['birthdate']['month'].'-'.(int)$_REQUEST['birthdate']['day'];
					$signature = $inCore->request('signature', 'str');

					$allow_who = $inCore->request('allow_who', 'str');
					
					$icq = $inCore->request('icq', 'str');
					$showicq = $inCore->request('showicq', 'int');
					
					$cm_subscribe = $inCore->request('cm_subscribe', 'str');
					
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
					
					if (strlen($emsg)==0){
                       
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

						$msg = 'Профиль успешно сохранен.';

					}
					$inCore->redirect(cmsUser::getProfileURL($inUser->login));
				}				
				
				if ($opt == 'changepass'){
					$emsg = '';
					$msg = '';
					if ($_SESSION['user']['password']==md5($_POST['oldpass'])){ $oldpass = $inCore->request('oldpass', 'str'); } else { $emsg .= 'Старый пароль введен не верно.<br/>'; }
					if ($_POST['newpass']==$_POST['newpass2']){ $newpass = $inCore->request('newpass', 'str'); } else { $emsg .= 'Пароли не совпали.<br/>'; }					

					if (strlen($emsg)==0){
						$sql = "UPDATE cms_users SET password='".md5($newpass)."' WHERE id = $id AND password='".md5($oldpass)."'";
						$inDB->query($sql);
						$msg = 'Пароль успешно изменен.';															
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
		
				$inPage->setTitle('Настройки профиля - '.$usr['nickname']);
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway('Настройки профиля');
																				
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
					$smarty->assign('menuid', $menuid);
					$smarty->assign('msg', $msg);
					$smarty->assign('emsg', $emsg);
					$smarty->assign('usr', $usr);			
					$smarty->assign('dateform', $inCore->getDateForm('birthdate', false, $usr['bday'], $usr['bmonth'], $usr['byear']));
					$smarty->assign('private_forms', $private_forms);		
					$smarty->assign('autocomplete_js', $autocomplete_js);
					
					$smarty->display('com_users_edit_profile.tpl');
				}
				
			} else { usrAccessDenied(); }
		
		} else { usrAccessDenied(); }
	
	} else { usrAccessDenied(); }

}
/////////////////////////////// VIEW USER COMMENTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='comments'){

	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql) ;

	if ($inDB->num_rows($result)){
		$usr        = $inDB->fetch_assoc($result);

		$page       = $inCore->request('page', 'int', 1);
		$perpage    = 15;
	
		$inPage->setTitle('Комментарии - '.$usr['nickname']);
		$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway('Комментарии', $_SERVER['REQUEST_URI']);
		
		if ($page>1) { echo '<div class="con_description"><strong>Страница:</strong> '.$page.'</div>'; }

		$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate,  IFNULL(SUM(v.vote), 0) as votes
                FROM cms_comments c
                LEFT JOIN cms_comments_votes v ON v.comment_id = c.id AND v.comment_type = 'com'
                WHERE c.user_id = $id AND c.published = 1
				GROUP BY c.id 
                ORDER BY c.pubdate DESC
				LIMIT ".(($page-1)*$perpage).", $perpage";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
			$comments = array();
			while ($com = $inDB->fetch_assoc($result)){
                $com['content'] = nl2br($inCore->parseSmiles($com['content'], true));
				$com['link'] = $inCore->getCommentLink($com['target'], $com['target_id']);
                if ($com['votes']>0){
                    $com['votes'] = '<span class="cmm_good">+'.$com['votes'].'</span>';
                } elseif ($com['votes']<0){
                    $com['votes'] = '<span class="cmm_bad">'.$com['votes'].'</span>';
                }
                $comments[] = $com;
			}
			
			$smarty = $inCore->initSmarty('components', 'com_users_comments.tpl');
            $smarty->assign('user_id', $id);
            $smarty->assign('menuid', $menuid);
			$smarty->assign('nickname', $usr['nickname']);
			$smarty->assign('login', $usr['login']);
			$smarty->assign('comments', $comments);
            $smarty->assign('avatar', usrImage($id));
			$smarty->assign('pagebar', pageBarStatic($page, $perpage, $id, 'comments', 'cms_comments'));
			$smarty->display('com_users_comments.tpl');	

		} else { echo '<p>Пользователь не оставлял комментарии.</p>';	}
	} else { echo '<p>Пользователь не найден. Возможно аккаунт был удален.</p>'; }
		
}
/////////////////////////////// VIEW USER POSTS /////////////////////////////////////////////////////////////////////////////////////
if ($do=='forumposts'){

	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql) ;
    
	if ($inDB->num_rows($result)){
		$usr = $inDB->fetch_assoc($result);

		$page = $inCore->request('page', 'int', 1);
		$perpage = 15;

        $inPage->setTitle('Сообщения на форуме - '.$usr['nickname']);
        $inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
        $inPage->addPathway('Сообщения на форуме', $_SERVER['REQUEST_URI']);

		if ($page>1) { echo '<div class="con_description"><strong>Страница:</strong> '.$page.'</div>'; }

		$sql = "SELECT *, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as date, t.title as topic, p.id as id, t.id as thread_id
                FROM cms_forum_posts p, cms_forum_threads t
                WHERE p.user_id = $id AND p.thread_id = t.id
                ORDER BY p.pubdate DESC
                LIMIT ".(($page-1)*$perpage).", $perpage";
				
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)>0){
			$posts = array();
			while ($post = $inDB->fetch_assoc($result)){
				$post['link'] = '/forum/0/thread'.$post['thread_id'].'.html#'.$post['id'];
                $post['content'] = $inCore->parseSmiles($post['content'], true);
				$post['content'] = str_replace("&amp;", '&', $post['content']);
				$posts[] = $post;
			}

			$smarty = $inCore->initSmarty('components', 'com_users_forumposts.tpl');
            $smarty->assign('user_id', $id);
            $smarty->assign('menuid', $menuid);
			$smarty->assign('nickname', $usr['nickname']);
			$smarty->assign('posts', $posts);
            $smarty->assign('avatar', usrImage($id));
			$smarty->assign('pagebar', pageBarStatic($page, $perpage, $id, 'forumposts', 'cms_forum_posts'));
			$smarty->display('com_users_forumposts.tpl');

		} else { echo '<p>Пользователь не оставлял сообщений на форуме.</p>';	}
	} else { echo '<p>Пользователь не найден. Возможно аккаунт был удален.</p>'; }

}
/////////////////////////////// VIEW PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='profile'){

	$inCore->loadLib('clubs');

    if (!$id){
        $login = $inCore->request('login', 'str', '');
        $login = urldecode($login);
        $id    = (int)$inDB->get_field('cms_users', "login='{$login}' AND is_deleted=0", 'id');
    }

    $usr = $model->getUser($id);
    
	if (!$usr || !$id) {
		$inPage->printHeading('Пользователь не найден');
		echo '<p>Возможно аккаунт был удален</p>';
        return;
	}

    $inPage->setTitle($usr['nickname']);
    $inPage->addPathway($usr['nickname']);

    if ( !(usrAllowed($usr['allow_who'], $id) || $inUser->is_admin) ){
        usrNotAllowed();
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
    $usr['menu']				= usrMenu($usr['id'], $cfg, $usr['banned']);
    $usr['is_new_friends']		= ($inUser->id==$usr['id'] && $model->isNewFriends($usr['id']) && $cfg['sw_friends']);
    if ($usr['is_new_friends']){
        $usr['new_friends'] 	= usrFriendQueriesList($usr['id'], $model);
    }
    $usr['rating']				= cmsUser::getRating($id);
    $usr['friends']				= usrFriends($usr['id']);

    if ($usr['friends']){
        $usr['friends_photos']	= cmsUser::getUserFriendsPhotos($usr['id']);
        $usr['friends_posts']	= cmsUser::getUserFriendsPosts($usr['id']);
    }

    $usr['awards_html']			= usrAwards($usr['id']);
    $usr['comments_html'] 		= usrComments($usr['id'], 5);
    $usr['forum_html'] 			= usrForumPosts($usr['id'], 5);
    $usr['photos_html']			= usrPhotos($usr['id'], 4);
    $usr['wall_html']			= cmsUser::getUserWall($usr['id']);
    $usr['addwall_html'] 		= cmsUser::getUserAddWall($usr['id']);
    $usr['banned'] 				= cmsUser::isBanned($usr['id']);

    $usr['clubs'] 				= cmsUserClubs($usr['id']);

    $usr['status']				= usrStatus($usr['id']);
    $usr['status_date']         = cmsCore::dateDiffNow($usr['status_date']); 
    $usr['flogdate']            = strip_tags(usrStatus($usr['id'], $usr['flogdate'], false, $usr['gender']));
    $usr['karma']				= strip_tags( cmsUser::getKarmaFormat($usr['id'], false), '<table><tr><td><img><a>' );
    $usr['karma_int']			= strip_tags($usr['karma']);
    $usr['karma_link']			= '<a href="/users/'.$menuid.'/'.$id.'/karma.html" title="История кармы" id="karmalink">?</a>';

    $usr['photos_count']		= (int)usrPhotoCount($id);
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
        $usr['blog_link'] 		= '<a href="/blogs/'.$menuid.'/'.$usr['blog_seolink'].'">Блог</a>';
    } elseif($myprofile) {
        $usr['blog_link'] 		= '<a href="/blogs/'.$menuid.'/createblog.html">Создать блог</a>';
    }

    if (!$usr['description']) {
        $usr['description']		= '<span style="color:#999"><em>Метки не указаны</em></span>';
    } else {
        $usr['description']     = cmsPage::getMetaSearchLink('/users/'.$menuid.'/hobby/', $usr['description']);
    }

    $usr['flogdate']			= $inCore->getRusDate($usr['flogdate']);
    $usr['fregdate'] 			= $inCore->getRusDate($usr['fregdate']);
    $usr['birthdate'] 			= $inCore->getRusDate($usr['birthdate']);

    $usr['comments_count'] 		= usrMsg($usr['id'], 'cms_comments');
    $usr['forum_count']			= usrMsg($usr['id'], 'cms_forum_posts');

    $usr['profile_link']        = cmsUser::getProfileURL($usr['login']);

    $usr['genderimg']			= '';
    if ($usr['gender']) {
        switch ($usr['gender']){
            case 'm': $usr['genderimg'] = '<img src="/components/users/images/male.gif"/>'; $usr['gender']='Мужской'; break;
            case 'f': $usr['genderimg'] = '<img src="/components/users/images/female.gif"/>'; $usr['gender']='Женский'; break;
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

    $smarty->assign('menuid', $menuid);
    $smarty->assign('id', $id);
    $smarty->assign('usr', $usr);
    $smarty->assign('plugins', $plugins);
    $smarty->assign('cfg', $cfg);
    $smarty->assign('myprofile', $myprofile);
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
				$inPage->setTitle('Мои сообщения');
				$inPage->addPathway('Мои сообщения', '/users/'.$menuid.'/'.$id.'/messages.html');			
				include 'components/users/messages.php';			
			} else { usrAccessDenied(); }
		} else { echo '<p>Пользователь не найден. Возможно аккаунт был удален.</p>'; }		
	} else { usrAccessDenied(); }
	
}
/////////////////////////////// AVATAR UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='avatar'){

	if (usrCheckAuth() && @$inUser->id==$id){
		$sql = "SELECT * FROM cms_users WHERE id = $id";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->setTitle('Загрузка аватара');
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway('Загрузка аватара', $_SERVER['REQUEST_URI']);
			echo '<div class="con_heading">Загрузка аватара</div>';
	
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
								@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/'.$old['imageurl']);
								@unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/small/'.$old['imageurl']);								
							}
							//CREATE THUMBNAIL
							if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
							if (isset($cfg['medw'])) { 	 $medw = $cfg['medw']; } else { $medw = 200; }
							
							@img_resize($uploadfile, $uploadavatar, $medw, $medw);
							@img_resize($uploadfile, $uploadthumb, $smallw, $smallw);
							
							//DELETE ORIGINAL							
							@unlink($uploadfile);
							//MODIFY PROFILE
							$sql = "UPDATE cms_user_profiles 
									SET imageurl = '$filename'
									WHERE user_id = $userid
									LIMIT 1";	
							$inDB->query($sql);
							
							//GO BACK TO PROFILE VIEW			
							$inCore->redirect(cmsUser::getProfileURL($usr['login']));
						} else {
							echo '<p><strong>ОШИБКА:</strong> '.$inCore->uploadError().'!</p>';
						}
						
						echo '<ul><li><a href="/users/'.$menuid.'/'.$userid.'/avatar.html">Повторить попытку</a></li>'."\n";
						echo '<li><a href="'.cmsUser::getProfileURL($usr['login']).'">Вернуться к профилю</a></li></ul>'."\n";
						
					} else { usrAccessDenied(); }
				} else { usrAccessDenied(); }
			
			} else {
				echo '<form enctype="multipart/form-data" action="/users/'.$menuid.'/'.$id.'/avatar.html" method="POST">' . "\n";	
					echo '<p>Выберите файл для загрузки: </p>' . "\n";
					echo '<input name="upload" type="hidden" value="1"/>' . "\n";			
					echo '<input name="userid" type="hidden" value="'.$id.'"/>'. "\n";									
					echo '<input name="picture" type="file" id="picture" size="30" />'. "\n";
					echo '<p><input type="submit" value="Загрузить"> <input type="button" onclick="window.history.go(-1);" value="Отмена"/></p>'. "\n";			
				echo '</form>'. "\n";

                echo '<p><a href="/users/'.$menuid.'/'.$id.'/select-avatar.html" class="select-avatar">Выбрать аватар из коллекции сайта</a></p>';
			}	
		}
	}//auth
	else { usrAccessDenied(); }
}
/////////////////////////////// AVATAR LIBRARY /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='select_avatar'){

	if (usrCheckAuth() && @$inUser->id==$id){

        $avatars_dir        = $_SERVER['DOCUMENT_ROOT']."/images/users/avatars/library";
        $avatars_dir_rel    = "/images/users/avatars/library";

        if (!$inCore->inRequest('set_avatar')){

            //SHOW AVATARS LIST
            $inPage->setTitle('Выбор аватара');
			$inPage->addPathway($inUser->nickname, cmsUser::getProfileURL($inUser->login));
			$inPage->addPathway('Выбор аватара');

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

            //paging
            $maxcols = 4;
            $page    = $inCore->request('page', 'int', 1);
            $perpage = 20;

            //slice only current page from avatars list
            $total   = sizeof($avatars);
            $avatars = array_slice($avatars, ($page-1)*$perpage, $perpage);

            //show page
            $smarty = $inCore->initSmarty('components', 'com_users_avatars.tpl');
                $smarty->assign('menuid', $menuid);
                $smarty->assign('userid', $id);
                $smarty->assign('avatars', $avatars);
                $smarty->assign('avatars_dir', $avatars_dir_rel);
                $smarty->assign('maxcols', $maxcols);
                $smarty->assign('pagebar', pageBarAvatars($page, $perpage, $total, $id));
            $smarty->display('com_users_avatars.tpl');

        } else {

            //SET AVATAR TO SELECTED
            $file = $inCore->request('file', 'str', '');
            if (file_exists($avatars_dir.'/'.$file)){

                $userid = $id;

                echo($file);

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
                    @unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/'.$old['imageurl']);
                    @unlink($_SERVER['DOCUMENT_ROOT'].'/images/users/avatars/small/'.$old['imageurl']);
                }
                //CREATE THUMBNAIL
                if (isset($cfg['smallw'])) { $smallw = $cfg['smallw']; } else { $smallw = 64; }
                if (isset($cfg['medw'])) { 	 $medw = $cfg['medw']; } else { $medw = 200; }

                $inCore->includeGraphics();
                @img_resize($uploadfile, $uploadavatar, $medw, $medw);
                @img_resize($uploadfile, $uploadthumb, $smallw, $smallw);

                //MODIFY PROFILE
                $sql = "UPDATE cms_user_profiles
                        SET imageurl = '$filename'
                        WHERE user_id = $userid
                        LIMIT 1";
                $inDB->query($sql) ;

            }

            //GO BACK TO PROFILE VIEW
            $inCore->redirect(cmsUser::getProfileURL($inUser->login));
            
        }

	}//auth
	else { usrAccessDenied(); }
}
/////////////////////////////// PHOTO UPLOAD /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addphoto'){
	$inCore->loadLib('tags');

	if ( $inUser->id==$id || $inCore->userIsAdmin($inUser->id) ){
		$sql = "SELECT * FROM cms_users WHERE id = $id";
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($inUser->login));
			$inPage->addPathway('Добавить фотографию');
			
			$inPage->printHeading('Добавить фотографию');
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
							//PREPARE FOR STEP 2
							$inPage->setTitle('Добавить фотографию - Шаг 2');
													
							$inPage->initAutocomplete();
							$autocomplete_js = $inPage->getAutocompleteJS('tagsearch', 'tags');
							
							$form_action = '/users/'.$menuid.'/'.$userid.'/addphoto.html';
															
							$smarty = $inCore->initSmarty('components', 'com_photos_add2.tpl');			
							$smarty->assign('form_action', $form_action);
							$smarty->assign('filename', $filename);
							$smarty->assign('autocomplete_js', $autocomplete_js);
							$smarty->assign('allow_who', 1);
							$smarty->display('com_photos_add2.tpl');							

						} else {
							echo '<p><strong>ОШИБКА:</strong> '.$inCore->uploadError().'!</p>';
						}
										
					} else { usrAccessDenied(); }
				} else { usrAccessDenied(); }
			
			} else {
				if ($inCore->inRequest('submit')){		
					//final upload step
					$title 			= $inCore->request('title', 'str');
					$description 	= $inCore->request('description', 'str');					
					$allow_who 		= $inCore->request('allow_who', 'str');

					if ($title == '') { $title = 'Фото без названия'; }

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
					
					echo '<p><strong>Фотография успешно добавлена.</strong></p>' ."\n";
					echo '<ul>' ."\n";
					echo '<li><a href="/users/'.$menuid.'/'.$id.'/photo'.$photoid.'.html">Перейти к фотографии</a></li>' ."\n";
					echo '<li><a href="/users/'.$menuid.'/'.$id.'/addphoto.html">Добавить еще фотографию</a></li>' ."\n";
					echo '<li><a href="/users/'.$menuid.'/'.$id.'/photoalbum.html">Перейти к фотоальбому</a></li>' ."\n";		
					echo '<li><a href="'.cmsUser::getProfileURL($inUser->login).'">Вернуться к профилю</a></li>' ."\n";
					echo '</ul>' ."\n";
					
				} else { 
					if(usrPhotoCount($id)<$cfg['photosize'] || $cfg['photosize']==0){
						//upload form
						$inPage->setTitle('Добавить фотографию - Шаг 1');
						
						$form_action = '/users/'.$menuid.'/'.$id.'/addphoto.html';
						
						$smarty = $inCore->initSmarty('components', 'com_photos_add1.tpl');			
						$smarty->assign('form_action', $form_action);
						$smarty->assign('user_id', $id);
						$smarty->display('com_photos_add1.tpl');							
						
					} else {
						echo '<p><strong>Достигнут предел количества фотографий</strong></p>';
						echo '<p>Вы можете разместить в своем фотоальбоме не более '.$cfg['photosize'].' фотографий.<br/>Чтобы добавить новое фото, удалите какое-либо из имеющихся.</p>';						
					}
				}
			}	
		}
	} else { usrAccessDenied(); }
}
/////////////////////////////// PHOTO DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delphoto'){
	$inCore->loadLib('tags');
	$max_mb = 2; //max filesize in Mb
	
	$photoid = @intval($_REQUEST['photoid']);	
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = $id";
			$result = $inDB->query($sql);
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				$sql = "SELECT * FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$photo = $inDB->fetch_assoc($result);				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway('Фотоальбом', '/users/'.$menuid.'/'.$id.'/photoalbum.html');
					$inPage->addPathway('Удалить фотографию', $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">Удаление фотографии</div>';				
					echo '<p>Вы действительно желаете удалить фото "'.$photo['title'].'"?</p>';
					echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
							<input style="font-size:24px; width:100px" type="button" name="cancel" value="Нет" onclick="window.history.go(-1)" /> 
							<input style="font-size:24px; width:100px" type="submit" name="godelete" value="Да" /> 
						 </p></form></div>';
				} else { usrAccessDenied(); }
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
				$inDB->query("DELETE FROM cms_comments WHERE target = 'userphoto' AND target_id = $photoid");
				cmsClearTags('userphoto', $photoid);
			}			
			header('location:/users/'.$menuid.'/'.$id.'/photoalbum.html');
		}
	} else { usrAccessDenied(); }
}
/////////////////////////////// PHOTO EDIT /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='editphoto'){
	$inCore->loadLib('tags');

	$max_mb = 2; //max filesize in Mb
	
	$user_id = $inUser->id;
	
	$photoid = @intval($_REQUEST['photoid']);	
	
	if (usrCheckAuth() && ($user_id==$id||$inCore->userIsAdmin($user_id))){
		$sql = "SELECT * FROM cms_users WHERE id = $id";
		$result = $inDB->query($sql);

		if ($inDB->num_rows($result)){
			$usr = $inDB->fetch_assoc($result);
	
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway('Фотоальбом', '/users/'.$menuid.'/'.$id.'/photoalbum.html');
			$inPage->addPathway('Редактировать фотографию', $_SERVER['REQUEST_URI']);
			echo '<div class="con_heading">Редактировать фотографию</div>';
			
			if (isset($_POST['save'])){							
					$title = strip_tags(htmlspecialchars($_POST['title'], ENT_QUOTES, 'cp1251'));
					$description = strip_tags(htmlspecialchars($_POST['description'], ENT_QUOTES, 'cp1251'));
					if ($title == '') { $title = 'Фото без названия'; }
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
								$sql = "UPDATE cms_user_photos
										SET imageurl = '$filename'
										WHERE id = $photoid AND user_id = $id";	
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
					
					echo '<p><strong>Фотография успешно cохранена.</strong></p>';
					echo '<p>&larr; <a href="/users/'.$menuid.'/'.$id.'/photoalbum.html">Вернуться к фотоальбому</a><br/>';
					echo '&larr; <a href="'.cmsUser::getProfileURL($inUser->login).'">Вернуться к профилю</a></p>';
					
				} else { 
							if(isset($_REQUEST['photoid'])){								
								$sql = "SELECT * FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
								$result = $inDB->query($sql);
								if ($inDB->num_rows($result)){	
									$photo = $inDB->fetch_assoc($result);		
									ob_start(); ?>
									
									<form action="/users/<?php echo $menuid?>/<?php echo $id?>/editphoto<?php echo $photoid?>.html" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="imageurl" value="<?php echo $photo['imageurl']?>" />
									<table border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td width="120" valign="top"><table width="110" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td width="110" align="center" valign="top" style="border:solid 1px gray; padding:5px; background-color:#FFFFFF;"><img src="/images/users/photos/small/<?php echo $photo['imageurl']?>" border="0" style="border:solid 1px black" /></td>
                                          </tr>
                                        </table></td>
                                        <td width="460" align="right" valign="top"><table width="460">
                                          <tr>
                                            <td valign="top"><strong>Название фото: </strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input name="title" type="text" id="title" size="40" maxlength="250" value="<?php echo $photo['title']?>"/></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><strong>Описание фото:</strong> </td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><textarea name="description" cols="39" rows="5" id="description"><?php echo $photo['description']?></textarea></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><strong>Теги:</strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input name="tags" type="text" id="tags" size="40" value="<?php if (isset($photo['id'])) { echo cmsTagLine('userphoto', $photo['id'], false); } ?>"/>
                                              <br />
                                              <span><small>ключевые слова через запятую</small></span></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><strong>Заменить файлом</strong>:</td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><input name="MAX_FILE_SIZE" type="hidden" value="<?php echo ($max_mb * 1024 * 1024)?>"/>
                                              <input name="picture" type="file" size="30" /></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><strong>Показывать:</strong></td>
                                          </tr>
                                          <tr>
                                            <td valign="top"><select name="allow_who" id="allow_who">
                                              <option value="all" <?php if ($photo['allow_who']=='all') { echo 'selected'; } ?>>Всем</option>
                                              <option value="registered" <?php if ($photo['allow_who']=='registered') { echo 'selected'; } ?>>Зарегистрированным</option>
                                              <option value="friends" <?php if ($photo['allow_who']=='friends') { echo 'selected'; } ?>>Моим друзьям</option>
                                            </select></td>
                                          </tr>
                                          <tr>
                                            <td valign="top">
                                              <input style="margin-top:10px;font-size:18px" type="submit" name="save" value="Сохранить" />
										      <input style="margin-top:10px;font-size:18px" type="button" name="cancel" value="Отмена" onclick="window.history.go(-1)"/>
                                            </td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                    </table>
									</form>
									
									<?php 
									echo ob_get_clean();
								}//photo exists
								else { usrAccessDenied(); }
							} //isset photo id
							else { usrAccessDenied(); }
						}//print form
		} else { usrAccessDenied(); } //user exists
	}//auth
	else { usrAccessDenied(); }
}
/////////////////////////////// VIEW ALBUM /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewalbum'){
	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql);
	
	if ($inDB->num_rows($result)>0){
		//if (usrCheckAuth()){
			if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }		
			$perpage = 15;
				
			$usr = $inDB->fetch_assoc($result);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway('Фотоальбом', $_SERVER['REQUEST_URI']);

			echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; Фотоальбом</div>';
	
			if ($page>1) { echo '<div class="con_description"><strong>Страница:</strong> '.$page.'</div>'; }
	
			echo usrPhotos($usr['id'], 0, false, (($page-1)*$perpage), $perpage);
			
			echo pageBarStatic($page, $perpage, $id, 'photoalbum', 'cms_user_photos');
		
		//} else { echo usrNeedReg(); }
	} else { echo '<p>Пользователь не найден. Возможно аккаунт был удален.</p>'; }
}
/////////////////////////////// VIEW BOARD ENTRIES ///////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewboard'){ 
	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql);
	
	if ($inDB->num_rows($result)>0){
		$usr = $inDB->fetch_assoc($result);
		$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
		$inPage->addPathway('Объявления');
        $inPage->setTitle('Объявления - '.$usr['nickname']);
        $inPage->addHeadCSS('components/board/css/styles.css');
        
		echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; Объявления</div>';

		$sql = "SELECT *, IF(DATE_FORMAT(pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(pubdate, '<strong>Сегодня</strong>'), DATE_FORMAT(pubdate, '%d-%m-%Y'))  as fpubdate
				FROM cms_board_items
				WHERE user_id = $id
				ORDER BY pubdate DESC				
				";						
		
		$perpage = 10;
		if (isset($_REQUEST['page'])) { $page = abs((int)$_REQUEST['page']); } else { $page = 1; }
		$sql .= "LIMIT ".($page-1)*$perpage.", $perpage";

		$result = $inDB->query($sql);

		$col = 1; $maxcols = 1;
		if ($inDB->num_rows($result)){				
				echo '<div class="board_gallery">';
				echo '<table width="100%" cellpadding="0" cellspacing="0" border="0">';
				while($con = $inDB->fetch_assoc($result)){							
					$file = 'nopic.jpg';
					if ($con['file']){
						if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/board/small/'.$con['file'])){
							$file = $con['file'];
						}
					}				
										
					if ($col==1) { echo '<tr>'; } echo '<td valign="top" width="'.round(100/$maxcols).'%">';
						echo '<table width="100%" height="" cellspacing="" cellpadding="0" class="bd_item">';
							echo '<tr>';
								echo '<td width="64" valign="top">
										<img class="bd_image_small" src="/images/board/small/'.$file.'" border="0" alt="'.$con['title'].'"/>
									  </td>';
								echo '<td valign="top">';
									echo '<div class="bd_title"><a href="/board/0/read'.$con['id'].'.html" title="'.$con['title'].'">';
										echo $con['title'];
									echo '</a></div>';
									echo '<div class="bd_text">'.$con['content'].'</div>';	
									echo '<div class="bd_item_details">';
											if ($con['published']){
												echo '<span class="bd_item_status_ok">Опубликовано</span>';
											} else {
												echo '<span class="bd_item_status_bad">Ожидает модерации</span>';
											}											
											echo '<span class="bd_item_date">'.$con['fpubdate'].'</span>';
											if ($con['city']){
												echo '<span class="bd_item_city"><a href="/board/0/city/'.urlencode($con['city']).'">'.$con['city'].'</a></span>';
											}
											
											if ($inUser->id){
												$moderator = ($inCore->userIsAdmin($inUser->id) || $inCore->isUserCan('board/moderate') || $con['user_id'] == $inUser->id);
											} else {
												$moderator = false;
											}											
											
											if ($moderator){
												echo '<span class="bd_item_edit"><a href="/board/'.$menuid.'/edit'.$con['id'].'.html">Редактировать</span>';
												echo '<span class="bd_item_delete"><a href="/board/'.$menuid.'/delete'.$con['id'].'.html">Удалить</span>';
											}
									echo '</div>';									
								echo '</td>';
							echo '</tr>';
						echo '</table>';
					echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
				}
				if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
				echo '</table>';
				echo '</div>';		
				echo pageBarBoard($id, $page, $perpage);	
		} else { 
				 echo '<p>Нет объявлений.</p>';
				}					
	}//END - MY BOARD ITEMS
}
/////////////////////////////// FRIENDS LIST /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='friendlist'){
	$sql = "SELECT id, login, nickname FROM cms_users WHERE id = $id LIMIT 1";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)>0){
		if (usrCheckAuth()){
			if (isset($_GET['page'])) { $page = intval($_GET['page']); } else { $page = 1; }		
			$perpage = 15;
				
			$usr = $inDB->fetch_assoc($result);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway('Друзья', $_SERVER['REQUEST_URI']);

			echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; Друзья</div>';
	
			echo usrFriends($usr['id'], false);
		
		} else { echo usrNeedReg(); }
	} else { echo '<p>Пользователь не найден. Возможно аккаунт был удален.</p>'; }
}

/////////////////////////////// VIEW PHOTO /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='viewphoto'){
	if (isset($_REQUEST['photoid'])){ $photoid = $_REQUEST['photoid'];	} else { $photoid = 0; 		}

	$user_id = $inUser->id;

	if ($user_id){
		$myprofile = ($user_id == $id);
	} else { $myprofile = false; }

	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql) ;
	

	if ($inDB->num_rows($result)>0){
//		if (usrCheckAuth()){
			$usr = $inDB->fetch_assoc($result);
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));

			$sql = "SELECT * FROM cms_user_photos WHERE id = $photoid AND user_id = $id";
			$result = $inDB->query($sql) ;

			if ($inDB->num_rows($result)>0){
				$photo = $inDB->fetch_assoc($result);
				
				$inDB->query("UPDATE cms_user_photos SET hits = hits + 1 WHERE id = ".$photo['id']) ;
	
				$inPage->addPathway('Фотоальбом', '/users/'.$menuid.'/'.$id.'/photoalbum.html');			
				$inPage->addPathway($photo['title'], $_SERVER['REQUEST_URI']);

				if (usrAllowed($photo['allow_who'], $id) || $inCore->userIsAdmin($inUser->id)){

					echo '<div class="con_heading">'.$photo['title'].'</div>';				
					if ($photo['description']){	echo '<div class="con_description">'.$photo['description'].'</div>'; }
			
					echo '<div class="con_description">'.cmsUser::getGenderLink($usr['id'], $usr['nickname'], 0, '', $usr['login']).' &mdash; <strong>Просмотров:</strong> '.$photo['hits'].' &mdash; <strong>Размер:</strong> '.round(filesize($_SERVER['DOCUMENT_ROOT'].'/images/users/photos/'.$photo['imageurl'])/1024, 2).' кб</div>';
			
					echo '<div class="usr_photo_view">
							<a href="/images/users/photos/'.$photo['imageurl'].'" target="_blank"><img border="0" src="/images/users/photos/medium/'.$photo['imageurl'].'" alt="'.$photo['title'].'" /></a>';

					$bbcode = '[IMG]http://'.$_SERVER['HTTP_HOST'].'/images/users/photos/medium/'.$photo['imageurl'].'[/IMG]';
					echo '<div style="margin-top:15px"><label for="bbcode">Код для вставки на форумы: </label><input type="text" id="bbcode" name="bbcode" size="50" value="'.$bbcode.'"/></div>';			
							
					if ($myprofile || $inCore->userIsAdmin($user_id)) {
						echo '<div style="margin-top:5px">';
							echo '<a style="height:16px; line-height:16px; margin-right:5px; padding-left:20px; background:url(/components/users/images/edit.gif) no-repeat;" href="/users/'.$menuid.'/'.$usr['id'].'/editphoto'.$photoid.'.html">Редактировать</a> ';
							echo '<a style="height:16px; line-height:16px; padding-left:20px; background:url(/components/users/images/delete.gif) no-repeat;"  href="/users/'.$menuid.'/'.$usr['id'].'/delphoto'.$photoid.'.html">Удалить</a> ';
						echo '</div>';
					}
					echo '</div>';
					
					//links to previous and next photos
					$previd = dbGetFields('cms_user_photos', 'id<'.$photo['id'].' AND user_id = '.$usr['id'], 'id, title, pubdate', 'id DESC');
					$nextid = dbGetFields('cms_user_photos', 'id>'.$photo['id'].' AND user_id = '.$usr['id'], 'id, title, pubdate', 'id ASC');

					echo '<div class="usr_photo_nav">';
						echo '<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto"><tr>';
							if ($previd){
								echo '<td align="right">';
									echo '<div>&larr; <a href="/users/'.$menuid.'/'.$usr['id'].'/photo'.$previd['id'].'.html">'.$previd['title'].'</a></div>';
								echo '</td>';
							}
							if ($previd && $nextid) { echo '<td>|</td>'; }
							if ($nextid){
								echo '<td align="left">';
									echo '<div><a href="/users/'.$menuid.'/'.$usr['id'].'/photo'.$nextid['id'].'.html">'.$nextid['title'].'</a> &rarr;</div>';
								echo '</td>';
							}						
						echo '</tr></table>';
					echo '</div>';
					
					$inCore->loadLib('tags');	
					echo cmsTagBar('userphoto', $photo['id']);
					
					//show user comments
					if($inCore->isComponentInstalled('comments')){
						$inCore->includeComments();
						comments('userphoto', $photo['id']);
					}					
				
			//	} else { usrNotAllowed(); }
							
			} else {
				echo '<div class="con_heading">Фотография не найдена</div>';
				echo '<p>Возможно она была удалена или пользователь ограничил ее просмотр.</p>';			
			}						
		} else { echo usrNeedReg(); }
	} else { 
		echo '<div class="con_heading">Пользователь не найден</div>';
		echo '<p>Возможно аккаунт был удален.</p>'; 
	}
}
/////////////////////////////// ADD FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='addfriend'){

    $sql = "SELECT * FROM cms_users WHERE id = $id";

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
				$inPage->addPathway('Добавить в друзья');
                $inPage->backButton(false);
				echo '<div class="con_heading">Добавить в друзья</div>';				
				echo '<p><strong>Отправить пользователю '.ucfirst($usr['nickname']).' предложение дружбы?</strong></p>';
				echo '<p>Если '.ucfirst($usr['nickname']).' примет предложение, он будет добавлен в число ваших друзей.</p>';
				echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
						<input style="font-size:24px; width:100px" type="button" name="cancel" value="Нет" onclick="window.history.go(-1)" /> 
						<input style="font-size:24px; width:100px" type="submit" name="goadd" value="Да" /> 
					 </p></form></div>';

		} else {
				$to_id      = $id;
				$from_id    = $inUser->id;
				if (!usrIsFriends($to_id, $from_id, false)){
					$sql = "INSERT INTO cms_user_friends (to_id, from_id, logdate, is_accepted) 
							VALUES ('$to_id', '$from_id', NOW(), '0')";
					$inDB->query($sql) ;
				}
				
				cmsUser::sendMessage(USER_UPDATER, $to_id, '[b]Получено предложение дружбы[/b]. Вы можете посмотреть его в своем [url='.cmsUser::getProfileURL($usr['login']).']профиле[/url].');
				
				$inCore->redirect(cmsUser::getProfileURL($inUser->login));
		}//!goadd
		} else { $inCore->redirectBack(); }
	} else { usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// DEL FRIEND /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfriend'){
	if (usrCheckAuth() && @$inUser->id!=$id){

		$first_id = $inUser->id;
		$second_id = $id;
		
		$sql = "DELETE FROM cms_user_friends WHERE ((to_id = $first_id AND from_id = $second_id) OR (to_id = $second_id AND from_id = $first_id))";
		$inDB->query($sql) ;

		header('location:'.$_SERVER['HTTP_REFERER']);

	} else { usrAccessDenied(); } //usrCheckAuth
}//do
/////////////////////////////// SEND MESSAGE ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='sendmessage'){
	if (usrCheckAuth() && $inUser->id!=$id){

		$from_id    = $inUser->id;
		$to_id      = $id;
		
		$sql        = "SELECT * FROM cms_users WHERE id = $id";
		$result     = $inDB->query($sql) ;
        $usr        = $inDB->fetch_assoc($result);

		if ($usr){
			if (usrCheckAuth()){

				$inPage->setTitle('Отправить сообщение');				
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway('Отправить сообщение', $_SERVER['REQUEST_URI']);
					
				if(!isset($_POST['gosend'])){		
					if (isset($_GET['replyid'])) { $replyid = $_GET['replyid']; }
					else { $replyid = 0; }
					
					if ($replyid){
						$sql = "SELECT m.*, u.* 
								FROM cms_user_msg m, cms_users u
								WHERE m.id = $replyid AND m.from_id = u.id AND m.to_id = $from_id";
						$result = $inDB->query($sql) ;
					
						if ($inDB->num_rows($result)>0){
							$msg = $inDB->fetch_assoc($result);
							echo '<div>';
								echo '<div class="con_heading">Исходное сообщение</div>';
								echo '<div class="usr_msgreply_source">';
									echo '<div class="usr_msgreply_sourcetext">'.$msg['message'].'</div>';
									echo '<div class="usr_msgreply_author"><a href="'.cmsUser::getProfileURL($msg['login']).'">'.$msg['nickname'].'</a>, '.$msg['senddate'].'</div>';
								echo '</div>';
							echo '</div>';
						} else {
							die();
						}
					}

					echo '<div class="con_heading">Отправить сообщение</div>';				

					echo '<table width="100%" cellpadding="0" cellspacing="5"><tr>';
					
					echo '<td width="200" height="200" valign="top">
							<div style="background-color:#FFFFFF;padding:5px;border:solid 1px gray;text-align:center">
								'.usrLink(usrImage($usr['id'], 'big'), $usr['id'], $menuid).'
							</div>
							<div style="padding:5px;width:100%">
								Кому: '.usrLink($usr['nickname'], $usr['id'], $menuid).'
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
								echo '<input name="massmail" type="checkbox" value="1" /> Отправить всем (массовая рассылка)';
							}
							echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="Отправить" style="font-size:18px"/> ';
							echo '<input type="button" name="gosend" value="Отмена" style="font-size:18px" onclick="window.history.go(-1)"/></div>';							
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
						if ($needmail){
								$inConf     = cmsConfig::getInstance();
														
								$postdate   = date('d/m/Y H:i:s');
								$to_email   = dbGetField('cms_users', "id='{$to_id}'", 'email');
								$from_nick  = dbGetField('cms_users', "id='{$from_id}'", 'nickname');
								$answerlink = HOST.'/users/'.$menuid.'/'.$from_id.'/reply'.$msg_id.'.html';
						
								$letter_path    = PATH.'/includes/letters/newmessage.txt';
								$letter         = file_get_contents($letter_path);
								
								$letter= str_replace('{sitename}', $inConf->sitename, $letter);
								$letter= str_replace('{answerlink}', $answerlink, $letter);
								$letter= str_replace('{date}', $postdate, $letter);
								$letter= str_replace('{from}', $from_nick, $letter);	
								$inCore->mailText($to_email, 'У вас новое сообщение! - '.$inConf->sitename, $letter);
						}
					} else {
						if ($inCore->userIsAdmin($inUser->id)){
							$userlist = dbGetTable('cms_users', ' id > 0 AND is_locked = 0 AND is_deleted = 0');
							foreach ($userlist as $key=>$usr){
								$sql = "INSERT INTO cms_user_msg (to_id, from_id, senddate, is_new, message)
										VALUES ('".$usr['id']."', '-2', NOW(), 1, '$message')";
								$inDB->query($sql) ;
							}
						}
					}
											
					$inCore->redirect('/users/'.$menuid.'/'.$from_id.'/messages-sent.html');			
				}
			
			}
		}
	} else { usrAccessDenied(); } //usrCheckAuth
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
		$sql = "SELECT * FROM cms_users WHERE id = $id";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
				$usr = $inDB->fetch_assoc($result);
		
				$inPage->setTitle('История кармы');
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway('История кармы', $_SERVER['REQUEST_URI']);
				
				echo '<div class="con_heading">История кармы - '.$usr['nickname'].'</div>';
				
				$ksql = "SELECT k.*, k.points as kpoints, DATE_FORMAT(k.senddate, '%d-%m-%Y в %H:%i') fsenddate, u.*
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
				
				} else { echo '<p>Карма пользователя еще не изменялась.</p><p>Оставляйте комментарии и сообщения на форуме чтобы другие могли изменять Вашу карму.</p><p>Карма является аналогом рейтинга и показывает насколько хорошо Вас оценивают другие пользователи сайта.</p>'; }

		} else { echo '<p>Пользователь не найден.</p>'; }	
}
/////////////////////////////// GIVE AWARD ///////////////////////////////////////////////////////////////////////////////////////
if ($do=='giveaward'){
	if (usrCheckAuth()){

		$from_id = $inUser->id;
		$to_id = $id;
		
		$sql = "SELECT * FROM cms_users WHERE id = $id";
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)>0){
				$usr = $inDB->fetch_assoc($result);
		
				$inPage->setTitle('Награждение пользователя');
				$inPage->addHeadJS('components/users/js/awards.js');
		
				$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
				$inPage->addPathway('Награждение', $_SERVER['REQUEST_URI']);
					
				if(!isset($_POST['gosend'])){		
					
					echo '<div class="con_heading">Награждение пользователя</div>';				
					echo '<form action="" method="POST" name="addform" id="addform">';
						echo '<table width="100%" cellpadding="0" cellspacing="5">';				
							echo '<tr>';
								echo '<td width="150" valign="middle">Изображение награды:</td>';					
								echo '<td valign="middle">';
									echo '<div style="overflow:hidden;_height:1%">'.usrAwardsList('aw.gif').'</div>';
								echo '</td>';												
							echo '</tr>';
							echo '<tr>';
								echo '<td width="150">Название награды:</td>';					
								echo '<td><input type="text" name="title" size="35" /></td>';												
							echo '</tr>';
							echo '<tr>';
								echo '<td width="150">Описание награды:</td>';					
								echo '<td><textarea name="description" cols="35" rows="4"></textarea></td>';												
							echo '</tr>';
						echo '</table>';

						echo '<div style="margin-top:6px;"><input type="submit" name="gosend" value="Наградить" style="font-size:18px"/> ';
						echo '<input type="button" name="gosend" value="Отмена" style="font-size:18px" onclick="window.history.go(-1)"/></div>';							

					echo '</form>';

				} else {
					$title = $inCore->request('title', 'str', 'Награда');
					$description = $inCore->request('description', 'str', '');
					$imageurl = $inCore->request('imageurl', 'str', 'Награда');
					$award_id = 0;					
					if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/users/awards/'.$imageurl)){							
						$sql = "INSERT INTO cms_user_awards (user_id, pubdate, title, description, imageurl, from_id, award_id)
								VALUES ('$to_id', NOW(), '$title', '$description', '$imageurl', '$from_id', '$award_id')";
						$inDB->query($sql) ;
						cmsUser::sendMessage(USER_UPDATER, $to_id, '[b]Получена награда:[/b] [url='.cmsUser::getProfileURL($usr['login']).']'.$title.'[/url]');
					}
					$inCore->redirect(cmsUser::getProfileURL($usr['login']));
				}						
		}
	} else { usrAccessDenied(); } //usrCheckAuth
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
			$user_sql = "SELECT id FROM cms_users WHERE id = $id LIMIT 1";
			$result = $inDB->query($user_sql) ;
			
			if ($inDB->num_rows($result)){
				$data = $inDB->fetch_assoc($result);				
				if (isset($_GET['confirm'])){
					if ($inUser->id == $data['id'] || $inCore->userIsAdmin($inUser->id)){
						$inDB->query("UPDATE cms_users SET is_deleted = 1 WHERE id = $id");	
						$inDB->query("DELETE FROM cms_user_friends WHERE to_id = $id OR from_id = $id");
						$user_blog_id = dbGetField('cms_blogs', 'user_id='.$id, 'id');
						if ($user_blog_id) {
                            $inCore->loadModel('blog');
                            $blog_model = new cms_model_blog();
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
						$GLOBALS['ed_menu'][0]['title'] = 'Отмена';				
						$GLOBALS['ed_page_title'] = 'Удаление профиля';						
						echo '<div class="con_heading">Удаление профиля</div>';		
						echo '<p style="margin-bottom:30px">Вы действительно хотите удалить профиль?<br/> Вместе с пользователем будет удален его блог со всеми материалами.</p>';		
						echo '<a href="/users/'.$menuid.'/'.$id.'/delprofile-yes.html" class="usr_btnlink">ДА</a><a href="javascript:window.history.go(-1)" class="usr_btnlink">НЕТ</a>';
					} else { usrAccessDenied(); }					
				}	
			}
		}
	} else { usrAccessDenied(); }
}
/////////////////////////////// RESTORE PROFILE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='restoreprofile'){
	if (usrCheckAuth()){
		$sql = "SELECT *
				FROM cms_users
				WHERE id = $id
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
	} else { $back = '/users/0/'.$id.'/profile.html'; }

	header('location:'.$back);
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// VIEW USER FILES ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='files'){
	//get user
	$sql = "SELECT * FROM cms_users WHERE id = $id";
	$result = $inDB->query($sql) ;
	//if user found
	if ($inDB->num_rows($result)>0){
			$usr = $inDB->fetch_assoc($result);			
			//heading
			echo '<div class="con_heading"><a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a> &rarr; Файлы</div>';
			$inPage->setTitle($usr['nickname'].' - Файлы');
			$inPage->addHeadJS('components/users/js/pageselfiles.js');
			//pathway			
			$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
			$inPage->addPathway('Файловый архив', '/users/'.$menuid.'/'.$id.'/files.html');
			//ordering & paging
			//ordering
			if (isset($_REQUEST['orderby'])) { 
				$orderby = $_REQUEST['orderby']; 
				$_SESSION['uf_orderby'] = $orderby;
			} elseif(isset($_SESSION['uf_orderby'])) { 
				$orderby = $_SESSION['uf_orderby'];
			} else {
				$orderby = 'pubdate'; 
			}

			if (isset($_REQUEST['orderto'])) { $orderto = htmlspecialchars($_REQUEST['orderto'], ENT_QUOTES, 'cp1251'); } else { $orderto = 'desc'; }
			if (isset($_REQUEST['page'])) { $page = intval($_REQUEST['page']); } else { $page = 1; }	
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
					echo '<td width="80"><strong>Файлов: </strong>'.$total_files.'</td>';
					if ($inUser->id==$id){
						echo '<td width="130"><strong>Свободно: </strong>'.$free_mb.' Мб</td>';
						echo '<td width="16"><img src="/components/users/images/upload.gif" border="0" /></td>';
						echo '<td width="100"><a href="addfile.html">Загрузить файлы</a></td>';				
					}
					//order buttons
					if ($total_files>1){
						echo '<td align="right">
							   <form name="orderform" method="post" action="" style="margin:0px">
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'pubdate\')" name="order_date" value="По дате" '.($orderby=='pubdate'?'disabled':'').'/> 
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'filename\')" name="order_title" value="По названию" '.($orderby=='filename'?'disabled':'').'/> 
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'filesize\')" name="order_size" value="По размеру" '.($orderby=='filesize'?'disabled':'').'/> 
								   <input type="button" class="usr_files_orderbtn" onclick="orderPage(\'hits\')" name="order_hits" value="По загрузкам" '.($orderby=='hits'?'disabled':'').'/>
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
						echo '<td class="usr_files_head" width="" colspan="2">Название файла '.($orderby=='filename'?'&darr;':'').'</td>';
						if ($inUser->id==$id){
							echo '<td class="usr_files_head" width="100" align="center">Видимость</td>';
						}
						echo '<td class="usr_files_head" width="100">Размер '.($orderby=='filesize'?'&darr;':'').'</td>';
						echo '<td class="usr_files_head" width="120">Дата создания '.($orderby=='pubdate'?'&darr;':'').'</td>';
						echo '<td class="usr_files_head" width="80" align="center">Загрузок '.($orderby=='hits'?'&darr;':'').'</td>';
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
									echo '<td class="'.$class.'" align="center"><img src="/components/users/images/yes.gif" border="0" title="Файл виден всем"/></td>';
								} else {
									echo '<td class="'.$class.'" align="center"><img src="/components/users/images/no.gif" border="0" title="Файл скрыт"/></td>';
								}
							}
							
							$mb = round(($file['filesize']/1024)/1024, 2);if ($mb == '0') { $mb = '~ 0'; }
							echo '<td class="'.$class.'">'.$mb.' Мб</td>';

							echo '<td class="'.$class.'">'.$file['pubdate'].'</td>';
							echo '<td class="'.$class.'" align="center">'.$file['hits'].'</td>';							
							if ($inUser->id==$id){
								echo '<td class="'.$class.'" align="center">';
								echo '<a href="/users/'.$menuid.'/'.$id.'/delfile'.$file['id'].'.html"><img src="/components/users/images/delete.gif" border="0" alt="Удалить файл"/></a>';
								echo '</td>';
							}
						echo '</tr>';			
						$rownum++;
				}
				echo '</table>';
				if ($inUser->id==$id){
					echo '<div style="margin-top:6px">
							<input type="button" class="usr_files_orderbtn" name="delete_btn" id="delete_btn" onclick="delFiles()" value="Удалить"/>
							<input type="button" class="usr_files_orderbtn" name="hide_btn" id="delete_btn" onclick="pubFiles(0)" value="Скрыть"/> 
							<input type="button" class="usr_files_orderbtn" name="show_btn" id="delete_btn" onclick="pubFiles(1)" value="Показать"/> 
						  </div>';
				}
				echo '</form>';
			} else { 
				echo '<p>Пользователь не загружал файлы в архив.</p>'; 
				if ($inUser->id==$id){
					echo '<a href="addfile.html">Загрузить файлы в архив</a>';
				}
			} 
	} else { echo '<p>Пользователи не найдены.</p>'; }		
	
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
		} else { $inCore->halt('Файл не найден.'); }
	} else { $inCore->halt('Файл не найден.'); }
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
				$inPage->setTitle('Загрузка файлов завершена');
				$inPage->backButton(false);
				echo '<div class="con_heading">Загрузка файлов завершена</div>';
				
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
							if(!strstr($name, '.php') && !strstr($name, '.asp') && !strstr($name, '.aspx') && !strstr($name, '.js') && !strstr($name, '.html') && !strstr($name, '.phtml')){
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
					echo '<div style="color:#660000;margin-bottom:10px;font-weight:bold">Доступное место для ваших файлов ('.$max_mb.' Мб) исчерпано.</div>';
					echo '<div style="color:#660000;font-weight:bold">Чтобы загрузить новые файлы, удалите какие-либо из имеющихся.</div>';
				}
										
				if (sizeof($loaded_files)){
					echo '<div><strong>Загруженные файлы:</strong></div>';
					echo '<ul>';
						foreach($loaded_files as $k=>$val){
							echo '<li>'.$val.'</li>';						
						}
					echo '</ul>';
					
					echo '<div style="margin-top:10px"><strong>Осталось доступного места:</strong> '.round($free_mb-$size_mb, 2).' Мб</div>';
				} else {
					echo '<div style="color:red">Ни один файл не был загружен. Может файлы слишком большие?</div>';
					echo '<div style="color:red">Имена файлов не должны содержать пробелов и русских букв.</div>';
				}
				
				echo '<div><a href="/users/'.$menuid.'/'.$id.'/files.html">Продолжить</a> &rarr;</div>';
							
			} else {
				$sql = "SELECT * FROM cms_users WHERE id = $id";
				$result = $inDB->query($sql) ;
				
				if ($inDB->num_rows($result)>0){									
					$usr = $inDB->fetch_assoc($result);
		
					//build upload form
					$inPage->setTitle('Загрузить файлы');
					$inPage->backButton(false);
                    $inPage->addHeadJS('includes/jquery/multifile/jquery.multifile.js');
					
					$multi_js = '<script type="text/javascript">
												  function startUpload(){
													$("#upload_btn").attr(\'disabled\', \'true\');
													$("#upload_btn").attr(\'value\', \'Идет загрузка...\');
													$("#cancel_btn").css(\'display\', \'none\');
													$("#loadergif").css(\'display\', \'block\');
													document.uploadform.submit();													
												  }
											   </script>';

                    $inPage->addHead($multi_js);
				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway('Файловый архив', '/users/'.$menuid.'/'.$id.'/files.html');
					$inPage->addPathway('Загрузить файлы', $_SERVER['REQUEST_URI']);
					
					echo '<div class="con_heading">Загрузить файлы</div>';			
					
					if ($free_mb>0){
					
						$post_max_b = return_bytes(ini_get('upload_max_filesize'));
						$post_max_mb = (round($post_max_b/1024)/1024) . ' Мб';
					
						echo '<div>Выбирайте все необходимые файлы поочередно, используя поле.</div>';
						echo '<div>Имена файлов не должны содержать пробелов и русских букв.</div>';
						
						echo '<div style="margin:10px 0px 0px 0px"><strong>Доступное место:</strong> '.$free_mb.' Мб</div>';
						echo '<div style="margin:0px 0px 10px 0px"><strong>Максимальный размер файла:</strong> '.$post_max_mb.'</div>';
						
						echo '<form action="" method="post" enctype="multipart/form-data" name="uploadform">';

						echo '<input name="MAX_FILE_SIZE" type="hidden" value="'.$post_max_b.'"/>'. "\n";
						echo '<input type="file" class="multi" name="upfile" id="upfile"/>';														
						echo '<div style="margin-top:20px;overflow:hidden">';
							echo '<input style="float:left;margin-right:4px" type="button" name="upload_btn" id="upload_btn" value="Загрузить файлы" onclick="startUpload()"/> ';
							echo '<input style="float:left" type="button" name="cancel_btn" id="cancel_btn" value="Отмена" onclick="window.history.go(-1)" />';
							echo '<div id="loadergif" style="display:none;float:left;margin:6px"><img src="/images/ajax-loader.gif" border="0"/></div>';					
						echo '</div>';
							echo '<input type="hidden" name="upload" value="1"/>';							
						echo '</form>';
					} else {
						echo '<div style="color:#660000;margin-bottom:10px;font-weight:bold">Доступное место для ваших файлов ('.$max_mb.' Мб) исчерпано.</div>';
						echo '<div style="color:#660000;font-weight:bold">Чтобы загрузить новые файлы, удалите какие-либо из имеющихся.</div>';			
						echo '<div style="margin-top:20px">';
							echo '<input type="button" name="cancel" value="Отмена" onclick="window.history.go(-1)" />';					
						echo '</div>';
					}
				}
			}
		
		} else { usrAccessDenied(); }	
	} else { usrAccessDenied(); }
}

/////////////////////////////// FILE DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfile'){
	$fileid = @intval($_REQUEST['fileid']);	
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = $id";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$inPage->backButton(false);
				$usr = $inDB->fetch_assoc($result);				
				$sql = "SELECT * FROM cms_user_files WHERE id = $fileid AND user_id = $id";
				$result = $inDB->query($sql);
				if ($inDB->num_rows($result)){
					$file = $inDB->fetch_assoc($result);				
					$inPage->addPathway($usr['nickname'], cmsUser::getProfileURL($usr['login']));
					$inPage->addPathway('Файловый архив', '/users/'.$menuid.'/'.$id.'/files.html');
					$inPage->addPathway('Удалить файл', $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">Удаление файла</div>';				
					echo '<p>Вы действительно желаете удалить файл "'.$file['filename'].'"?</p>';
					echo '<div><form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><p>
							<input style="font-size:24px; width:100px" type="button" name="cancel" value="Нет" onclick="window.history.go(-1)" /> 
							<input style="font-size:24px; width:100px" type="submit" name="godelete" value="Да" /> 
						 </p></form></div>';
				} else { usrAccessDenied(); }
			}
		} else {
			$sql = "SELECT filename FROM cms_user_files WHERE id = $fileid AND user_id = $id";
			$result = $inDB->query($sql) ;
			if ($inDB->num_rows($result)){
				$file = $inDB->fetch_assoc($result);
				@unlink($_SERVER['DOCUMENT_ROOT'].'/upload/userfiles/'.$id.'/'.$file['filename']);
				$inDB->query("DELETE FROM cms_user_files WHERE id = $fileid") ;
			}			
			header('location:/users/'.$menuid.'/'.$id.'/files.html');
		}
	} else { usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES DELETE /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='delfilelist'){
	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die('Нет выбранных файлов'); }
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		if (!isset($_POST['godelete'])){
			$sql = "SELECT * FROM cms_users WHERE id = $id";
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
					$inPage->addPathway('Файловый архив', '/users/'.$menuid.'/'.$id.'/files.html');
					$inPage->addPathway('Удалить файлы', $_SERVER['REQUEST_URI']);
					echo '<div class="con_heading">Удаление файлов</div>';				
					echo '<p><strong>Вы действительно желаете удалить эти файлы?</strong></p>';

					echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">';
						echo '<ul>';
						while ($file = $inDB->fetch_assoc($result)){ 
							echo '<li>';
								echo $file['filename'] . '<input type="hidden" name="files[]" value="'.$file['id'].'"/>';	
							echo '</li>';
						}
						echo '</ul>';
						echo '<div style="margin-top:10px">';
							echo '<input style="font-size:24px; width:100px" type="button" name="cancel" value="Нет" onclick="window.history.go(-1)" /> ';
							echo '<input style="font-size:24px; width:100px" type="submit" name="godelete" value="Да" />';
						echo '</div>';
					echo '</form>';
				} else { usrAccessDenied(); }
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
				header('location:/users/'.$menuid.'/'.$id.'/files.html');
		}
	} else { usrAccessDenied(); }
}

/////////////////////////////// MULTIPLE FILES PUBLISHING /////////////////////////////////////////////////////////////////////////////////////////
if ($do=='pubfilelist'){
	if (sizeof($_POST['files'])) { $files = $_POST['files']; }
	else { die('Нет выбранных файлов'); }
	
	$allow = $_GET['allow'];
	
	if (usrCheckAuth() && (@$inUser->id==$id || $inCore->userIsAdmin($inUser->id))){
		$t = 0;
		foreach($files as $key=>$value){
			$findsql .= "id = ".$value; 
			if ($t<sizeof($files)-1) { $findsql .= " OR "; }
			$t++;
		}				
				
		$inDB->query("UPDATE cms_user_files SET allow_who = '$allow' WHERE $findsql") ;
		header('location:/users/'.$menuid.'/'.$id.'/files.html');
	} else { usrAccessDenied(); }
}

/////////////////////////////// VIEW AWARDS LIST ///////////////////////////////////////////////////////////////////////////////////////	
if ($do=='awardslist'){
	$inPage->setTitle('Награды сайта');
	
	$inPage->addPathway('Награды сайта');

	echo '<div class="con_heading">Награды сайта</div>';
	
	$sql = "SELECT * 
			FROM cms_user_autoawards
			WHERE published = 1
			ORDER BY title";			
	$result = $inDB->query($sql) ;

	if ($inDB->num_rows($result)){
			
		echo '<table width="100%" cellspacing="2" cellpadding="3" class="usr_aw_table">';
		while($aw = $inDB->fetch_assoc($result)){
			echo '<tr>';
			
				//icon
				echo '<td width="32" valign="top">';
					echo '<img class="usr_aw_img" src="/images/users/awards/'.$aw['imageurl'].'" border="0"/>';
				echo '</td>';
				
				//description
				echo '<td width="30%" valign="top">';
					
					echo '<div class="usr_aw_title"><strong>'.$aw['title'].'</strong></div>';
					echo '<div class="usr_aw_desc">'.$aw['description'].'</div>';
									
					echo '<table border="0" cellspacing="4" cellpadding="0" class="usr_aw_dettable">';                      
						if ($aw['p_comment']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_comment.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_comment']."\n";
								echo ' комментариев</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_forum']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_forum.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_forum']."\n";
								echo ' сообщений на форуме</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_content']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_forum.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_content']."\n";
								echo ' опубликованных статей</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_blog']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_blog.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_blog']."\n";
								echo ' записей в блоге</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_karma']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_karma.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_karma']."\n";
								echo ' баллов личной кармы</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_photo']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_photo.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_photo']."\n";
								echo ' фотографий в общих альбомах</td>'."\n";
							echo '</tr>'."\n";
						}
						if ($aw['p_privphoto']>0){  
							echo '<tr>'."\n";
								echo '<td><img src="/admin/components/autoawards/images/p_privphoto.gif" width="16" height="16" /></td>'."\n";
								echo '<td>'."\n";
								  echo $aw['p_privphoto']."\n";
								echo ' фотографий в личном альбоме</td>'."\n";
							echo '</tr>'."\n";
						}
                    echo '</table>';
				echo '</td>';
				
				//who have this award
				echo '<td valign="top" class="usr_aw_who">';
					$sql =  "SELECT u.id as id, u.nickname as nickname, u.login as login, IFNULL(p.gender, 'm') as gender
							 FROM cms_user_awards aw, cms_users u, cms_user_autoawards a, cms_user_profiles p
							 WHERE aw.award_id = a.id AND aw.user_id = u.id AND p.user_id = u.id AND aw.award_id = ".$aw['id'];
					$rs = $inDB->query($sql) ;
					$total = $inDB->num_rows($rs);
					$uhtml = '';
					if ($total){
						$row = 0;
						while ($user = $inDB->fetch_assoc($rs)){
							$row++;
							$uhtml .= '<a href="'.cmsUser::getProfileURL($user['login']).'" id="'.$user['gender'].'">'.$user['nickname'].'</a>';
							if ($row<$total){ $uhtml .= ', '; }
						}
					} else {
						$uhtml = 'Нет пользователей с этой наградой';	
					}
								
					echo '<div class="usr_aw_users"><strong>Наградой обладают:</strong></div>';
					echo '<div class="usr_aw_userslist">'.$uhtml.'</div>';
				echo '</td>';
				
			echo '</tr>';			
		}
		echo '</table>';
				
	} else { echo '<p>На сайте нет наград.</p>'; }		
	
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
					$club_id 	= $inDB->get_field('cms_user_wall', "id=$record_id", 'user_id');
                    $club_admin = $inDB->get_field('cms_clubs', "id=$club_id", 'admin_id');
					$can_delete = clubUserIsRole($club_id, $my_id, 'moderator') || ($club_admin == $my_id);
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

            $result = $inDB->query("SELECT id FROM cms_users WHERE id = $user_id");

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
                            $inCore->mailText($to_email, 'Новая запись на стене! - '.$inConf->sitename, $letter);
                    }
                }
            }
		}
        $inCore->redirectBack();
	}
	

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} 
?>