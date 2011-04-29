<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	function mod_usermenu($module_id){
        
        $inCore     = cmsCore::getInstance();
        $inDB       = cmsDatabase::getInstance();
        $inUser     = cmsUser::getInstance();
						
        if (!$inUser->id){ return false; }

        $cfg            = $inCore->loadModuleConfig($module_id);
        $users_cfg      = $inCore->loadComponentConfig('users');
        $cfg['avatar']  = 1;

        if (!function_exists('usrBlog')){
            $inCore->includeFile('components/users/includes/usercore.php');
        }

        $newmsg     = cmsUser::isNewMessages($inUser->id);

        $blog       = usrBlog($inUser->id);

        $blog_href  = ($blog['id']) ? '/blogs/'.$blog['seolink'] : '/blogs/createblog.html';
        $avatar     = '<img src="/images/users/avatars/small/'.$inUser->imageurl.'" />';
        
        $is_billing = $inCore->isComponentInstalled('billing');
        $balance    = $is_billing ? $inUser->balance : 0;

        $smarty = $inCore->initSmarty('modules', 'mod_usermenu.tpl');
        $smarty->assign('avatar', $avatar);
        $smarty->assign('nickname', $inUser->nickname);
        $smarty->assign('login', $inUser->login);
        $smarty->assign('id', $inUser->id);
        $smarty->assign('newmsg', $newmsg);
        $smarty->assign('is_can_add', $inCore->isUserCan('content/add'));
        $smarty->assign('is_admin', $inCore->userIsAdmin($inUser->id));
        $smarty->assign('is_editor', $inCore->userIsEditor($inUser->id));
        $smarty->assign('cfg', $cfg);
        $smarty->assign('blogid', $blog['id']);
        $smarty->assign('blog_href', $blog_href);
        $smarty->assign('users_cfg', $users_cfg);
        $smarty->assign('is_billing', $is_billing);
        $smarty->assign('balance', $balance);
        $smarty->display('mod_usermenu.tpl');

        return true;

	}
?>