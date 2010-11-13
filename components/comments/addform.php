<?php

	session_start();

    define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	//PROTECT FROM DIRECT RUN
	if (isset($_REQUEST['cd'])){
		if (md5(session_id()) != $_REQUEST['cd']){ die(); }
	} else { die(); }

    include(PATH.'/core/cms.php');
	// Грузим конфиг
	include(PATH.'/includes/config.inc.php');
	$inCore     = cmsCore::getInstance();

    $inCore->loadLanguage('lang');
    $inCore->loadLanguage('components/comments');

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('page');         //страница
    $inCore->loadClass('user');         //страница

    $inPage     = cmsPage::getInstance();
    $inUser     = cmsUser::getInstance();

    $inUser->update();

	if (!$inCore->inRequest('target')) { die(); } else { $target = $inCore->request('target', 'str'); }
	if (!$inCore->inRequest('target_id')) { die(); } else { $target_id = $inCore->request('target_id', 'int'); }	

    $parent_id = $inCore->request('parent_id', 'int', 0);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('comments');

    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
	if (!isset($cfg['min_karma'])) { $cfg['min_karma'] = 0; }
	if (!isset($cfg['min_karma_add'])) { $cfg['min_karma_add'] = 0; }
	if (!isset($cfg['min_karma_show'])) { $cfg['min_karma_show'] = 0; }	
	if (!isset($cfg['regcap'])) { $cfg['regcap'] = 1; }

	//bb code toolbar
	if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; }

	$is_user_add_bb = $inCore->isUserCan('comments/bbcode');

    if ($cfg['bbcode'] && $is_user_add_bb){
        $bb_toolbar = cmsPage::getBBCodeToolbar('content', true, 'comments');
		//smilies toolbar
		$smilies = cmsPage::getSmilesPanel('content');
		echo '<script language="JavaScript" type="text/javascript" src="/includes/jquery/upload/ajaxfileupload.js"></script>';
    }
    if ($cfg['smiles'] && $is_user_add_bb){
	//smilies toolbar
	$smilies = cmsPage::getSmilesPanel('content');
    }

	//LOAD SMARTY
	$inCore->loadSmarty();
    $smarty = new Smarty();

	$karma_need     = $cfg['min_karma_add'];
	$karma_has      = cmsUser::getKarma($inUser->id);
	$can_by_karma   = (($cfg['min_karma'] && $karma_has>=$karma_need) || $inCore->userIsAdmin($inUser->id));
	
	$need_captcha = (!$inUser->id || ($inUser->id && $cfg['regcap']==1));

	$smarty = $inCore->initSmarty('components', 'com_comments_add.tpl');			
	$smarty->assign('no_guests', (!$inUser->id && !$cfg['canguests']));
	$smarty->assign('user_can_add', $inCore->isUserCan('comments/add'));
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