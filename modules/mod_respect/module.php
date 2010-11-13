<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                  Модуль "Доска почета"                                    //
//                      выводит в блоке отмеченных админом пользователей.                    //
//                                                                                           //
//                                                                                           //
/*********************************************************************************************/

function mod_respect($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		$cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['view'])) { $cfg['view'] = 'all'; }
		if (!isset($cfg['limit'])) { $cfg['limit'] = 5; }
		if (!isset($cfg['order'])) { $cfg['order'] = 'desc'; }
		if (!isset($cfg['show_awards'])) { $cfg['show_awards'] = 1; }

        if ($cfg['order']=='rand') { $order_sql = 'RAND()'; }
        if ($cfg['order']=='desc') { $order_sql = 'awards_count DESC'; }

        if ($cfg['view']=='all'){ $view_sql = ''; }
        if ($cfg['view']!='all'){ $view_sql = " AND a.title = '{$cfg['view']}'"; }

		$sql = "SELECT u.id as id, u.nickname as nickname, u.login as login, COUNT(a.id) as awards_count, 
                       p.imageurl as imageurl
                FROM cms_users u, cms_user_profiles p, cms_user_awards a
                WHERE a.user_id = u.id AND p.user_id = u.id AND u.is_deleted = 0 AND u.is_locked = 0 {$view_sql}
                GROUP BY a.user_id
                ORDER BY {$order_sql}
                LIMIT {$cfg['limit']}";
 	
		$result = $inDB->query($sql) ;

        $users = array();

		if ($inDB->num_rows($result)){	
			while($user = $inDB->fetch_assoc($result)){
                $avatar_url     = '/images/users/avatars/small/'.$user['imageurl'];

                $user['avatar'] = file_exists(PATH.$avatar_url) ? $avatar_url : '';
                if (!$user['avatar'] || !$user['imageurl']) { $user['avatar'] = '/images/users/avatars/small/nopic.jpg'; }

                if ($cfg['show_awards']){
                    $user['awards'] = $inDB->get_table('cms_user_awards', 'user_id='.$user['id'], 'id, title');
                }
                
                $users[]        = $user;
			}
		}

        $smarty = $inCore->initSmarty('modules', 'mod_respect.tpl');
        $smarty->assign('users', $users);
        $smarty->assign('cfg', $cfg);
        $smarty->display('mod_respect.tpl');
			
		return true;
        
}
?>