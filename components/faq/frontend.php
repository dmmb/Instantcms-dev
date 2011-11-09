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
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function faq(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();

    $inCore->loadModel('faq');
    $model = new cms_model_faq();

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

	global $_LANG;
	
	$cfg = $inCore->loadComponentConfig('faq');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    if(!isset($cfg['guest_enabled'])) { $cfg['guest_enabled'] = 1; }
    if(!isset($cfg['user_link'])) { $cfg['user_link'] = 1; }
    if(!isset($cfg['publish'])) { $cfg['publish'] = 0; }
	if(!isset($cfg['is_comment'])) { $cfg['is_comment'] = 1; }
	
	$id 	= $inCore->request('id', 'int', 0);
	$do		= $inCore->request('do', 'str', 'view');

///////////////////////////////////// VIEW CATEGORY ////////////////////////////////////////////////////////////////////////////////
if ($do=='view'){

	if($id>0){
		//CURRENT CATEGORY
		$sql = "SELECT *
				FROM cms_faq_cats
				WHERE id = '$id'
				ORDER BY title ASC LIMIT 1
				";		
	
		$result = $inDB->query($sql) ;

		if (!$inDB->num_rows($result)) { cmsCore::error404(); }

		$cat = $inDB->fetch_assoc($result);	

		//PAGE HEADING
		$pagetitle = $cat['title'];
		$inPage->setTitle($cat['title']);
		$inPage->addPathway($cat['title']);
		$inPage->setDescription($cat['title']);
	} else {
		$pagetitle = $_LANG['FAQ'];
		$inPage->setTitle($_LANG['FAQ']);
		$inPage->setDescription($_LANG['FAQ']);
	}
	
	//LIST OF SUBCATEGORIES
	$sql = "SELECT *
			FROM cms_faq_cats
			WHERE parent_id = $id AND id > 0 AND published = 1
			ORDER BY title ASC
			";		
				
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){	
		$subcats = array();
		while($subcat = $inDB->fetch_assoc($result)){
			$subcats[] = $subcat;
		}
		$is_subcats = true;
	} else {
		$is_subcats = false;
	}
	
	//CURRENT CATEGORY CONTENT
	$perpage = 15;
	$page = $inCore->request('page', 'int', 1);	
	
	if ($id > 0){		
		$sql = "SELECT q.*, u.login, u.nickname
				FROM cms_faq_quests q
				LEFT JOIN cms_users u ON u.id = q.user_id
				WHERE q.category_id = $id AND q.published = 1
				ORDER BY q.pubdate DESC
				LIMIT ".(($page-1)*$perpage).", $perpage";		
		$result_total = $inDB->query("SELECT id FROM cms_faq_quests WHERE category_id = $id AND published = 1") ;
		$records = $inDB->num_rows($result_total);	
	} else {
		$sql = "SELECT q.*, c.title cat_title, c.id cid, u.login, u.nickname
				FROM cms_faq_quests q
				LEFT JOIN cms_faq_cats c ON c.id = q.category_id
				LEFT JOIN cms_users u ON u.id = q.user_id
				WHERE q.published = 1
				ORDER BY q.pubdate DESC
				LIMIT 15";			
	}
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){	
		$quests = array();
		while($con = $inDB->fetch_assoc($result)){
			$con['pubdate'] = $inCore->dateFormat($con['pubdate'], true, false, false);
			$con['quest']	= nl2br($con['quest']);
			$quests[] = $con;	
		}
		$is_quests = true;		
	} else {
		$is_quests = false;
	}
	
	$smarty = $inCore->initSmarty('components', 'com_faq_view.tpl');			
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('id', $id);
	$smarty->assign('subcats', $subcats);
	$smarty->assign('is_subcats', $is_subcats);	
	$smarty->assign('quests', $quests);
	$smarty->assign('cfg', $cfg);
	$smarty->assign('is_quests', $is_quests);
	$smarty->assign('is_user', $inUser->id);
	$smarty->assign('pagebar', cmsPage::getPagebar($records, $page, $perpage, '/faq/%id%-%page%', array('id'=>$id)));		
	$smarty->display('com_faq_view.tpl');		
				
}

///////////////////////////////////// READ QUESTION ////////////////////////////////////////////////////////////////////////////////
if ($do=='read'){
		$sql = "SELECT con.*,
				cat.title cat_title, cat.id cat_id, u.login, u.nickname
				FROM cms_faq_quests con
				LEFT JOIN cms_faq_cats cat ON cat.id = con.category_id
				LEFT JOIN cms_users u ON u.id = con.user_id
				WHERE con.id = $id LIMIT 1";
				
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
			
			$inDB->query("UPDATE cms_faq_quests SET hits = hits + 1 WHERE id = $id") ;
		
			$quest = $inDB->fetch_assoc($result);									
			
			$quest['pubdate']    = $inCore->dateFormat($quest['pubdate'], true, false, false);
			$quest['answerdate'] = $inCore->dateFormat($quest['answerdate'], true, false, false);
			if (strlen($quest['quest'])>40) { $shortquest = substr($quest['quest'], 0, 40).'...'; }
			else { $shortquest = $quest['quest']; }
			$quest['quest']		 = nl2br($quest['quest']);
			
			$inPage->setTitle($shortquest);
			$inPage->setDescription($shortquest);
				
			$inPage->addPathway($quest['cat_title'], '/faq/'.$quest['cat_id']);
			$inPage->addPathway($shortquest);
							
			$smarty = $inCore->initSmarty('components', 'com_faq_read.tpl');			
			$smarty->assign('quest', $quest);
			$smarty->assign('cfg', $cfg);
			$smarty->assign('labels', array('comments' => $_LANG['ANSWERS'], 'add' => $_LANG['REPLY'], 'rss' => $_LANG['RSS_FEED'], 'not_comments' => $_LANG['NOT_ANSWERS']));
			$smarty->assign('is_admin', $inUser->is_admin);
			$smarty->display('com_faq_read.tpl');	
													
		} else { cmsCore::error404(); }
}
///////////////////////////////////// SEND QUEST ////////////////////////////////////////////////////////////////////////////////
if ($do=='sendquest'){

    if (!$inUser->id && !$cfg['guest_enabled']){ cmsCore::error404(); }

	$inPage->setTitle($_LANG['ASK_QUES']);
	$inPage->addPathway($_LANG['ASK_QUES']);
	
	$inPage->backButton(false);
	$inPage->addHeadJS('components/faq/js/common.js');

    $error          = '';
    $captha_code    = $inCore->request('code', 'str', '');
    $message        = $inCore->request('message', 'str', '');
    $category_id    = $inCore->request('category_id', 'int', '');
    $published      = ($inUser->is_admin || $cfg['publish']) ? 1 : 0;
    $is_submit      = $inCore->inRequest('message');

    if ($is_submit && !$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $error = $_LANG['ERR_CAPTCHA']; }

	if (!$is_submit || $error){

        if (IS_BILLING && $inUser->id){ cmsBilling::checkBalance('faq', 'add_quest'); }

		//FORM								
		$smarty = $inCore->initSmarty('components', 'com_faq_add.tpl');			
		$smarty->assign('catslist', $inCore->getListItems('cms_faq_cats', $category_id));
		$smarty->assign('user_id', $inUser->id);
		$smarty->assign('message', $_REQUEST['message']);
		$smarty->assign('error', $error);
		$smarty->display('com_faq_add.tpl');

	} else {
		
        //SAVE QUESTION
		$sql = "INSERT INTO cms_faq_quests (category_id, pubdate, published, quest, answer, user_id, answeruser_id, answerdate, hits)
				VALUES ('$category_id', NOW(), '$published', '$message', '', '{$inUser->id}', 0, NOW(), 0)";
		$inDB->query($sql);
		
		$quest_id = $inDB->get_last_id('cms_faq_quests');

        if (IS_BILLING && $inUser->id){ cmsBilling::process('faq', 'add_quest'); }

		$inPage->setTitle($_LANG['QUESTION_SEND']);
		$inPage->addPathway($_LANG['QUESTION_SEND'], $_SERVER['REQUEST_URI']);
		$inPage->backButton(false);

        if (!$published){
			echo '<div class="con_heading">'.$_LANG['QUESTION_SEND'].'</div>';
            echo '<div style="margin-top:10px">'.$_LANG['QUESTION_PREMODER'].'</div>';
			echo '<div style="margin-top:10px"><a href="/faq">'.$_LANG['CONTINUE'].'</a></div>';
        } elseif ($published) {
			$category = $inDB->get_field('cms_faq_cats', "id={$category_id}", 'title');
            //регистрируем событие
            cmsActions::log('add_quest', array(
                'object' => 'вопрос',
                'object_url' => '/faq/quest'.$quest_id.'.html',
                'object_id' => $quest_id,
                'target' => $category,
                'target_url' => '/faq/'.$category_id,
                'target_id' => $category_id, 
                'description' => strip_tags( strlen(strip_tags($message))>100 ? substr($message, 0, 100) : $message )
            ));
            $inCore->redirect('/faq/quest'.$quest_id.'.html');
		} else { $inCore->redirect('/faq/quest'.$quest_id.'.html'); }

	}
}

///////////////////////////////////// DELETE QUEST ////////////////////////////////////////////////////////////////////////////////

if ($do=='delquest'){
	
    $quest_id 	= $inCore->request('quest_id', 'int', 0);
    $user_id    = $inUser->id;

	$sql    = "SELECT con.id, con.quest, con.category_id
				FROM cms_faq_quests con
				WHERE con.id = '$quest_id' LIMIT 1";
				
	$result = $inDB->query($sql);
	$quest  = $inDB->fetch_assoc($result);
	
    if (!$user_id || !$quest_id || !$quest) { $inCore->redirectBack(); }	
		
    if ( !$inCore->inRequest('confirm') ) {

        if ($inCore->userIsAdmin($user_id)){
			$inPage->setTitle($_LANG['DEL_QUES']);
			$inPage->addPathway($_LANG['DEL_QUES']);
            $inPage->backButton(false);
            $confirm['title'] = $_LANG['DELETE_QUES'];
            $confirm['text']  = $_LANG['YOU_REALY_DELETE_QUES'].':<br> "<a href="/faq/quest'.$quest['id'].'.html">'.$quest['quest'].'</a>"<br><br>';
			$confirm['action']                  = $_SERVER['REQUEST_URI'];
			$confirm['yes_button']              = array();
			$confirm['yes_button']['type']      = 'submit';
			$confirm['yes_button']['name']  	= 'confirm';
            $smarty = $inCore->initSmarty('components', 'action_confirm.tpl');
            $smarty->assign('confirm', $confirm);
            $smarty->display('action_confirm.tpl');
        } else {
            $inCore->redirectBack();
        }
	}

    if ( $inCore->inRequest('confirm') ){

        if ($inCore->userIsAdmin($user_id)){
            
            $model->deleteQuest($quest_id);

        }
        $inCore->redirect('/faq/'.$quest['category_id']);
    }

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$inCore->executePluginRoute($do);
} //function
?>