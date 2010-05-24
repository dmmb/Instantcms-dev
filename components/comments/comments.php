<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);
    
    function buildTree($parent_id, $level, $comments, &$tree){
        $level++;
        foreach($comments as $num=>$comment){
            if ($comment['parent_id']==$parent_id){
                $comment['level'] = $level-1;
                $tree[] = $comment;
                buildTree($comment['id'], $level, $comments, $tree);
            }
        }
    }

	session_start();

	define("VALID_CMS", 1);
    include(PATH.'/core/cms.php');

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('config');           //конфигурация
    $inCore->loadClass('db');               //база данных
    $inCore->loadClass('user');
    $inCore->loadClass('page');

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
    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
	if (!isset($cfg['min_karma'])) { $cfg['min_karma'] = 0; }
	if (!isset($cfg['min_karma_add'])) { $cfg['min_karma_add'] = 0; }
	if (!isset($cfg['min_karma_show'])) { $cfg['min_karma_show'] = 0; }

    $target     = $inCore->request('target', 'str');
    $target_id  = $inCore->request('target_id', 'int');

	//LIST COMMENTS
    $comments_count     = $model->getCommentsCount($target, $target_id);

	$is_admin           = $inCore->userIsAdmin($inUser->id);
	$user_can_delete    = $inCore->isUserCan('comments/delete');
	$user_can_moderate  = $inCore->isUserCan('comments/moderate');

	$comments = array();
    $tree = array();

	if ($comments_count){

		//BUILD COMMENTS LIST
        $comments_list  = $model->getComments($target, $target_id);

		foreach($comments_list as $comment){
			$next = sizeof($comments);
			$comments[$next] = $comment;
            $comments[$next]['level'] = 0;        
			if ($comments[$next]['guestname']) {
				$comments[$next]['author']      = $comments[$next]['guestname'];
				$comments[$next]['is_profile']  =false;
			} else {
				$comments[$next]['author'] 		= $inDB->get_fields('cms_users', 'id='.$comments[$next]['user_id'], 'nickname, login');
				$comments[$next]['profile'] 	= $inDB->get_fields('cms_user_profiles', 'user_id='.$comments[$next]['user_id'], '*');
				$comments[$next]['is_profile'] 	= true;
				$comments[$next]['user_image'] 	= usrImage($comments[$next]['user_id']);
			}
            $comments[$next]['show'] 	   	= ((!$cfg['min_karma'] || $comments[$next]['votes']>=$cfg['min_karma_show']) || $inCore->userIsAdmin($comments[$next]['user_id']));
            if ($comments[$next]['votes']>0){
                $comments[$next]['votes'] = '<span class="cmm_good">+'.$comments[$next]['votes'].'</span>';
            } elseif ($comments[$next]['votes']<0){
                $comments[$next]['votes'] = '<span class="cmm_bad">'.$comments[$next]['votes'].'</span>';
            }
			if ($cfg['bbcode']){
				$comments[$next]['content'] = nl2br($inCore->parseSmiles($comments[$next]['content'], true));
			} elseif ($cfg['smilies']) {
				$comments[$next]['content'] = nl2br(strip_tags($inCore->parseSmiles($comments[$next]['content'])));
			} else {
                $comments[$next]['content'] = nl2br(strip_tags($comments[$next]['content']));
            }
			$comments[$next]['is_my'] = ($inUser->id==$comments[$next]['user_id']);
            if ($inUser->id){
                $comments[$next]['is_voted'] = ($comments[$next]['is_my'] || $inDB->rows_count('cms_ratings', 'item_id='.$comments[$next]['id'].' AND target=\'comment\' AND user_id='.$inUser->id, 1));
            }
        }

        buildTree(0, 0, $comments, $tree);
	}

    ob_start();

	$smarty = $inCore->initSmarty('components', 'com_comments_list.tpl');
	$smarty->assign('comments_count', $comments_count);
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
