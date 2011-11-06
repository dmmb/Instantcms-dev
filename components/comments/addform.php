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

	session_start();

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	//PROTECT FROM DIRECT RUN
	if (isset($_REQUEST['cd'])){
		if (md5(session_id()) != $_REQUEST['cd']){ die(); }
	} else { 
        die();
    }

    include(PATH.'/core/cms.php');	
	include(PATH.'/includes/config.inc.php');

	$inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadLanguage('lang');
    $inCore->loadLanguage('components/comments');
    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('page');
    $inCore->loadClass('user');
	$inCore->loadClass('plugin');

    $inPage     = cmsPage::getInstance();
    $inUser     = cmsUser::getInstance();

    $inUser->update();

    $do         = $inCore->request('action', 'str', 'add');
    $target     = $inCore->request('target', 'str', '');
    $target_id  = $inCore->request('target_id', 'int', 0);
    $parent_id  = $inCore->request('parent_id', 'int', 0);
    $comment_id = $inCore->request('id', 'int', 0);

    if ($do == 'add' && !$target) { return; }
    if ($do == 'add' && !$target_id) { return; }
    if ($do == 'edit' && !$comment_id) { return; }

    $inCore->loadModel('comments');

    $model = new cms_model_comments();

	$cfg = $inCore->loadComponentConfig('comments');
    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
	if (!isset($cfg['min_karma'])) { $cfg['min_karma'] = 0; }
	if (!isset($cfg['min_karma_add'])) { $cfg['min_karma_add'] = 0; }
	if (!isset($cfg['min_karma_show'])) { $cfg['min_karma_show'] = 0; }	
	if (!isset($cfg['regcap'])) { $cfg['regcap'] = 1; }
	if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; }
    if (!isset($cfg['edit_minutes'])) { $cfg['edit_minutes'] = 0; }

    $comment = array();

    if ($do=='edit'){

        if (!$cfg['edit_minutes'] && !$inUser->is_admin) { return; }

        $comment = $model->getComment($comment_id, $cfg);

        if (!$comment) { return; }
        if ((!$comment['is_editable'] || $comment['user_id']!=$inUser->id) && !$inUser->is_admin) { return; }

    }

	$is_user_add_bb = $inCore->isUserCan('comments/bbcode');

    if ($cfg['bbcode'] && $is_user_add_bb){
            $bb_toolbar = cmsPage::getBBCodeToolbar('content', true, 'comments');
		//smilies toolbar
            $smilies    = cmsPage::getSmilesPanel('content');
            echo '<script language="JavaScript" type="text/javascript" src="/includes/jquery/upload/ajaxfileupload.js"></script>';
        }
    if ($cfg['smiles'] && $is_user_add_bb){
        //smilies toolbar
            $smilies = cmsPage::getSmilesPanel('content');
        }

	//LOAD SMARTY
    $smarty = $inCore->initSmarty();

	$karma_need     = $cfg['min_karma_add'];
	$karma_has      = cmsUser::getKarma($inUser->id);
	$can_by_karma   = (($cfg['min_karma'] && $karma_has>=$karma_need) || $inCore->userIsAdmin($inUser->id));	
	$need_captcha   = (!$inUser->id || ($inUser->id && $cfg['regcap']==1));

	$smarty = $inCore->initSmarty('components', 'com_comments_add.tpl');			
	$smarty->assign('no_guests', (!$inUser->id && !$cfg['canguests']));
	$smarty->assign('user_can_add', $inCore->isUserCan('comments/add'));
	$smarty->assign('do', $do);
    $smarty->assign('comment', $comment);
	$smarty->assign('is_user', $inUser->id);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('target', $target);
	$smarty->assign('target_id', $target_id);
	$smarty->assign('parent_id', $parent_id);
	$smarty->assign('user_subscribed', cmsUser::isSubscribed($inUser->id, $target, $target_id));	
	$smarty->assign('can_by_karma', $can_by_karma);
	$smarty->assign('karma_need', $karma_need);	
	$smarty->assign('karma_has', $karma_has);	
	$smarty->assign('need_captcha', $need_captcha);

    if ($cfg['bbcode'] && $is_user_add_bb){
        $smarty->assign('bb_toolbar', $bb_toolbar);
    }

    if ($cfg['smiles'] && $is_user_add_bb){
    	$smarty->assign('smilies', $smilies);
    }

	ob_start();
        $smarty->display('com_comments_add.tpl');
	$html = ob_get_clean();
	
	unset($smarty);
	
	if(!isset($cfg['recode']) || @$cfg['recode']==1){
		$html = iconv('cp1251', 'utf-8', $html);
	}

	echo $html;

?>