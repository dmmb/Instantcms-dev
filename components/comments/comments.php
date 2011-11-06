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

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    
	session_start();

	define("VALID_CMS", 1);
    include(PATH.'/core/cms.php');
	// Грузим конфиг
	include(PATH.'/includes/config.inc.php');
    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');           //конфигурация
    $inCore->loadClass('db');               //база данных
    $inCore->loadClass('user');
    $inCore->loadClass('page');
	$inCore->loadClass('plugin');

    $inCore->loadModel('comments');
    $inCore->loadLanguage('lang');
    $inCore->loadLanguage('components/comments');

    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inUser->update();

    $model      = new cms_model_comments();

/*********************************************************************************************/

    $inCore->loadSmarty();
    $smarty     = new Smarty();

    //activate profiles support
    $inCore->includeFile('components/users/includes/usercore.php');

    unset($_SESSION['bbcode']['code_js_added']);

/*********************************************************************************************/

    $cfg = $inCore->loadComponentConfig('comments');

    // Проверяем включен ли компонент
	if(!$cfg['component_enabled']) { return false; }

    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
	if (!isset($cfg['min_karma'])) { $cfg['min_karma'] = 0; }
	if (!isset($cfg['min_karma_add'])) { $cfg['min_karma_add'] = 0; }
	if (!isset($cfg['min_karma_show'])) { $cfg['min_karma_show'] = 0; }
    if(!isset($cfg['max_level'])) { $cfg['max_level']=5;       }

    $target     = $inCore->request('target', 'str');
    $target_id  = $inCore->request('target_id', 'int');

	//LIST COMMENTS

	$is_admin           = $inCore->userIsAdmin($inUser->id);
	$user_can_delete    = $inCore->isUserCan('comments/delete');
	$user_can_moderate  = $inCore->isUserCan('comments/moderate');

	$comments = array();
    $tree = array();

		//BUILD COMMENTS LIST
    $comments_list  = $model->getComments($target, $target_id, $cfg);
	if ($comments_list){

		foreach($comments_list as $comment){
			$next = sizeof($comments);
			$comments[$next] = $comment;
            $comments[$next]['level'] = 0;        
			if ($comments[$next]['guestname']) {
				$comments[$next]['author']      = $comments[$next]['guestname'];
				$comments[$next]['is_profile']  = false;
				$comments[$next]['ip']  		= (($cfg['cmm_ip'] == 1 || $cfg['cmm_ip'] == 2) && $comments[$next]['ip']) ? '('.$comments[$next]['ip'].')' : false;
			} else {
				$comments[$next]['author']['nickname'] = $comments[$next]['nickname'];
				$comments[$next]['author']['login'] = $comments[$next]['login'];
				$comments[$next]['is_profile'] 	= true;
				$comments[$next]['user_image'] 	= usrImageNOdb($comments[$next]['user_id'], 'small', $comments[$next]['imageurl'], $comments[$next]['is_deleted']);
				$comments[$next]['ip']  		= ($cfg['cmm_ip'] == 2 && $comments[$next]['ip']) ? '('.$comments[$next]['ip'].')' : false;
			}
            $comments[$next]['show'] 	   	= ((!$cfg['min_karma'] || $comments[$next]['votes']>=$cfg['min_karma_show']) || $inCore->userIsAdmin($comments[$next]['user_id']));
            if ($comments[$next]['votes']>0){
                $comments[$next]['votes'] = '<span class="cmm_good">+'.$comments[$next]['votes'].'</span>';
            } elseif ($comments[$next]['votes']<0){
                $comments[$next]['votes'] = '<span class="cmm_bad">'.$comments[$next]['votes'].'</span>';
            }
			$comments[$next]['is_my'] = ($inUser->id==$comments[$next]['user_id']);
            if ($inUser->id){
                $comments[$next]['is_voted'] = ($comments[$next]['is_my'] || $inDB->rows_count('cms_ratings', 'item_id='.$comments[$next]['id'].' AND target=\'comment\' AND user_id='.$inUser->id, 1));
            }
        }

        $model->buildTree(0, 0, $comments, $tree);
	}

    ob_start();

	$smarty = $inCore->initSmarty('components', 'com_comments_list.tpl');
	$smarty->assign('comments_count', $comments_list );
	$smarty->assign('comments', $tree);
	$smarty->assign('user_can_moderate', $user_can_moderate);
	$smarty->assign('user_can_delete', $user_can_delete);
	$smarty->assign('is_admin', $is_admin);
	$smarty->assign('is_user', $inUser->id);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('target', $target);
	$smarty->assign('target_id', $target_id);
	$smarty->assign('url', $_SERVER['REQUEST_URI']);

	$smarty->display('com_comments_list.tpl');

    $html = ob_get_clean();

    if(!isset($cfg['recode']) || @$cfg['recode']==1){
		$html = iconv('cp1251', 'utf-8', $html);
	}

    echo $html;

?>
