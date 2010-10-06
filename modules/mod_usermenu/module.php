<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_usermenu($module_id){
        
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        $inUser     = cmsUser::getInstance();
		$cfg        = $inCore->loadModuleConfig($module_id);
	
        $users_cfg  = $inCore->loadComponentConfig('users');
						
		//logged user menu
		if (!$inUser->id){ return false; }

        if (!isset($cfg['avatar'])){ $cfg['avatar'] = true;	}
        if (!isset($cfg['showtype'])){ $cfg['showtype'] = 'text';	}

        //activate profiles support
        if(file_exists(PATH.'/components/users/includes/usercore.php')){
            if (!function_exists('usrComments')){ //if not included earlier
                $inCore->includeFile('components/users/includes/usercore.php');
            }
        }

        $newmsg     = cmsUser::isNewMessages($inUser->id);

        $blog       = $inDB->get_fields('cms_blogs', 'owner = "user" AND user_id = '.$inUser->id, 'id, seolink');

        $blog_href  = ($blog['id']) ? '/blogs/'.$blog['seolink'] : '/blogs/createblog.html';

        $smarty = $inCore->initSmarty('modules', 'mod_usermenu.tpl');
        $smarty->assign('nickname', $inUser->nickname);
        $smarty->assign('login', $inUser->login);
        $smarty->assign('id', $inUser->id);
        $smarty->assign('avatar', usrImage($inUser->id));
        $smarty->assign('newmsg', $newmsg);
        $smarty->assign('is_can_add', $inCore->isUserCan('content/add'));
        $smarty->assign('is_admin', $inCore->userIsAdmin($inUser->id));
        $smarty->assign('is_editor', $inCore->userIsEditor($inUser->id));
        $smarty->assign('cfg', $cfg);
        $smarty->assign('blogid', $blog['id']);
        $smarty->assign('blog_href', $blog_href);
        $smarty->assign('users_cfg', $users_cfg);
        $smarty->display('mod_usermenu.tpl');

        return true;

	}
?>