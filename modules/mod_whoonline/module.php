<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_whoonline($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

        global $_LANG;

		$sql = "SELECT		        
				o.user_id as id,
				u.login,
				u.nickname,
				p.gender as gender
				FROM cms_online o
                LEFT JOIN cms_users u ON  u.id = o.user_id
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
				WHERE u.is_locked = 0 AND u.is_deleted = 0
                GROUP BY o.user_id";
		
		$result = $inDB->query($sql) ;
				$total = $inDB->num_rows($result);
		
		if ($total){	
				$now = 0;
				while($usr = $inDB->fetch_assoc($result)){
					if($cfg['admin_editor']){
					if ($inCore->userIsAdmin($usr['id'])){
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:red");
					} elseif ($inCore->userIsEditor($usr['id'])) {	
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:green");
					} else {
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
					}
					} else {
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
					}
					if ($now < $total-1) { echo ', '; }
					$now ++;
				}			
		} else { echo '<div><strong>'.$_LANG['WHOONLINE_USERS'].':</strong> 0</div>'; }

        echo '<div style="margin-top:10px"><strong>'.$_LANG['WHOONLINE_GUESTS'].':</strong> '.$inDB->rows_count('cms_online', 'user_id = 0 OR user_id = \'\'').'</div>';

        if(!$cfg['show_today']){ return true; }

		$today = date("Y-m-d");
		
        $sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
                FROM cms_users u
				LEFT JOIN cms_user_profiles p ON p.user_id = u.id
                WHERE u.is_locked = 0 AND u.is_deleted = 0 AND DATE_FORMAT(u.logdate, '%Y-%m-%d')='$today'
                ORDER BY u.logdate DESC";

        $result = $inDB->query($sql) ;

        if ($inDB->num_rows($result)){

            echo '<div style="margin-top:10px;margin-bottom:8px"><strong>'.$_LANG['WAS_TODAY'].':</strong></div>';

            $now    = 0;
            $total  = $inDB->num_rows($result);
            while($usr = $inDB->fetch_assoc($result)){
					if($cfg['admin_editor']){
						if ($inCore->userIsAdmin($usr['id'])){
							echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:red");
						} elseif ($inCore->userIsEditor($usr['id'])) {	
							echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:green");
						} else {
							echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
						}
					} else {
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
					}
                if ($now < $total-1) { echo ', '; }
                $now ++;
            }

        } else { echo '<div>'.$_LANG['NOBODY_TODAY'].'</div>'; }
						
		return true;	
	}
    
?>