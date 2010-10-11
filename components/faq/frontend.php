<?php
/*********************************************************************************************/
//																							 //
//                                   InstantCMS v1.0.2 (c) 2008                              //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function faq(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$cfg = $inCore->loadComponentConfig('faq');
	
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

	if ($inDB->num_rows($result)==0) { die('Category not found.'); }

	$cat = $inDB->fetch_assoc($result);	

	//PAGE HEADING
		$pagetitle = $cat['title'];
		$inPage->setTitle($cat['title']);
	} else {
		$pagetitle = $_LANG['FAQ'];
		$inPage->setTitle($_LANG['FAQ']);
	}
	
	//PATHWAY ENTRY
	if ($cat['title']) { $inPage->addPathway($cat['title']); }

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
				LIMIT 10";			
	}
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){	
		$quests = array();
		while($con = $inDB->fetch_assoc($result)){
			$con['pubdate'] = $inCore->dateFormat($con['pubdate'], true, false, false);
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
	$smarty->assign('is_quests', $is_quests);	
	$smarty->assign('pagebar', cmsPage::getPagebar($records, $page, $perpage, '/faq/%id%-%page%', array('id'=>$id)));		
	$smarty->display('com_faq_view.tpl');		
				
}

///////////////////////////////////// READ QUESTION ////////////////////////////////////////////////////////////////////////////////
if ($do=='read'){
		$sql = "SELECT con.*,
						DATE_FORMAT(con.pubdate, '%d-%m-%Y') shortdate,
						cat.title cat_title, cat.id cat_id, u.login, u.nickname
				FROM cms_faq_quests con
				LEFT JOIN cms_faq_cats cat ON cat.id = con.category_id
				LEFT JOIN cms_users u ON u.id = con.user_id
				WHERE con.id = $id LIMIT 1";
				
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)){
			$inDB->query("UPDATE cms_faq_quests SET hits = hits + 1 WHERE id = $id") ;
		
			$quest = $inDB->fetch_assoc($result);									
			$quest['pubdate'] = $inCore->dateFormat($quest['pubdate'], true, false, false);
			$quest['answerdate'] = $inCore->dateFormat($quest['answerdate'], true, false, false);
			if (strlen($quest['quest'])>40) { $shortquest = substr($quest['quest'], 0, 40).'...'; }
			else { $shortquest = $quest['quest']; }
			
			$inPage->setTitle($shortquest);
				
			$inPage->addPathway($quest['cat_title'], '/faq/'.$quest['cat_id']);
			$inPage->addPathway($shortquest);
							
			$smarty = $inCore->initSmarty('components', 'com_faq_read.tpl');			
			$smarty->assign('quest', $quest);
			$smarty->display('com_faq_read.tpl');														
		}
}
///////////////////////////////////// SEND QUEST ////////////////////////////////////////////////////////////////////////////////
if ($do=='sendquest'){

    $inUser = cmsUser::getInstance();

	$inPage->setTitle($_LANG['ASK_QUES']);
	$inPage->addPathway($_LANG['ASK_QUES']);
	
	$inPage->backButton(false);
	$inPage->addHeadJS('components/faq/js/common.js');

    $error          = '';
    $captha_code    = $inCore->request('code', 'str', '');
    $message        = $inCore->request('message', 'str', '');
    $category_id    = $inCore->request('category_id', 'int', '');
    $published      = ($inUser->is_admin ? 1 : 0);
    $is_submit      = $inCore->inRequest('message');

    if ($is_submit && !$inUser->id && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $error = $_LANG['ERR_CAPTCHA']; }

	if (!$is_submit || $error){
		//FORM								
		$smarty = $inCore->initSmarty('components', 'com_faq_add.tpl');			
		$smarty->assign('catslist', $inCore->getListItems('cms_faq_cats', $category_id));
		$smarty->assign('user_id', $inUser->id);
		$smarty->assign('message', $message);
		$smarty->assign('error', $error);
		$smarty->display('com_faq_add.tpl');
	} else {
		
        //SAVE QUESTION
		$sql = "INSERT INTO cms_faq_quests (category_id, pubdate, published, quest, answer, user_id, answeruser_id, answerdate, hits)
				VALUES ('$category_id', NOW(), '$published', '$message', '', '{$inUser->id}', 0, NOW(), 0)";
		$inDB->query($sql);
		
		$inPage->setTitle($_LANG['QUESTION_SEND']);
		$inPage->addPathway($_LANG['QUESTION_SEND'], $_SERVER['REQUEST_URI']);
		$inPage->backButton(false);

        echo '<div class="con_heading">'.$_LANG['QUESTION_SEND'].'</div>';

        if (!$published){
            echo '<div style="margin-top:10px">'.$_LANG['QUESTION_PREMODER'].'</div>';
        }

        echo '<div style="margin-top:10px"><a href="/faq">'.$_LANG['CONTINUE'].'</a></div>';

	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //function
?>