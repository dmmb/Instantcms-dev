<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
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

		$sql = "SELECT DISTINCT o.user_id, u.*, DATE_FORMAT(u.regdate, '%d-%m-%Y (%H:%i)') as fdate, p.gender as gender
				FROM cms_users u, cms_online o, cms_user_profiles p
				WHERE o.user_id = u.id AND p.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0
				ORDER BY u.regdate DESC
				";		
		
		$result = $inDB->query($sql) ;

		if ($inDB->num_rows($result)){	
				$total = $inDB->num_rows($result);
				$now = 0;
				while($usr = $inDB->fetch_assoc($result)){
					if ($inCore->userIsAdmin($usr['id'])){
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:red");
					} elseif ($inCore->userIsEditor($usr['id'])) {	
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login'], "color:green");
					} else {
						echo cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
					}
					
					if ($now < $total-1) { echo ', '; }
					$now ++;
				}			
		} else { echo '<div><strong>'.$_LANG['WHOONLINE_USERS'].':</strong> 0</div>'; }

        $sql = "SELECT id FROM cms_online WHERE user_id = 0";
        $result = $inDB->query($sql) ;
        echo '<div style="margin-top:10px"><strong>'.$_LANG['WHOONLINE_GUESTS'].':</strong> '.@$inDB->num_rows($result).'</div>';

        if(!$cfg['show_today']){ return true; }

        $sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, p.gender as gender
                FROM cms_users u, cms_user_profiles p
                WHERE p.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0 AND DATEDIFF(NOW(), u.logdate)=0
                ORDER BY u.logdate DESC";

        $result = $inDB->query($sql) or die(mysql_error()) ;

        if ($inDB->num_rows($result)){

            echo '<div style="margin-top:10px;margin-bottom:8px"><strong>'.$_LANG['WAS_TODAY'].':</strong></div>';

            $now    = 0;
            $total  = $inDB->num_rows($result);
            while($usr = $inDB->fetch_assoc($result)){
                echo ' '.cmsUser::getGenderLink($usr['id'], $usr['nickname'], null, $usr['gender'], $usr['login']);
                if ($now < $total-1) { echo ', '; }
                $now ++;
            }

        } else { echo '<div>'.$_LANG['NOBODY_TODAY'].'</div>'; }
						
		return true;	
	}
    
?>