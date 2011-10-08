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

	function mod_user_stats($module_id){

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
		
		$cfg = $inCore->loadModuleConfig($module_id);

        if (!isset($cfg['show_total'])) { $cfg['show_total'] = 1; }
        if (!isset($cfg['show_online'])) { $cfg['show_online'] = 1; }
        if (!isset($cfg['show_gender'])) { $cfg['show_gender'] = 1; }
        if (!isset($cfg['show_city'])) { $cfg['show_city'] = 1; }
		
        $inCore->includeFile('components/users/includes/usercore.php');       
        $inCore->loadLanguage('components/users');
        	
        $inCore->loadModel('users');
        $model = new cms_model_users();

        $total_usr = $model->getUserTotal();

        if ($cfg['show_gender']){
            $gender_stats   = usrGenderStats($total_usr);
        }

        if ($cfg['show_city']){
            $city_stats     = usrCityStats();
        }

        if ($cfg['show_online']){
            $people = cmsUser::getOnlineCount();
        }

        if ($cfg['show_bday']){
            $bday = cmsUser::getBirthdayUsers();
        }

		if (!@$_SESSION['usr_online']) {
			$online_link = '<a href="/users/online.html" rel=”nofollow”>'.$_LANG['SHOW_ONLY_ONLINE'].'</a>';
		} else {
			$online_link = '<a href="/users/all.html" rel=”nofollow”>'.$_LANG['SHOW_ALL'].'</a>';
		}

		$smarty = $inCore->initSmarty('modules', 'mod_user_stats.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('total_usr', $total_usr);
		$smarty->assign('gender_stats', $gender_stats);
		$smarty->assign('city_stats', $city_stats);
		$smarty->assign('online_link', $online_link);
        $smarty->assign('people', $people);
        $smarty->assign('bday', $bday);
		$smarty->display('mod_user_stats.tpl');

		return true;

	}
?>