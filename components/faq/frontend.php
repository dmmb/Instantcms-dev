<?php
/*********************************************************************************************/
//																							 //
//                                   InstantCMS v1.0.2 (c) 2008                              //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function pageBar($cat_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $id = $inCore->request('id', 'int');
    global $_LANG;
	$html = '';
	$result = $inDB->query("SELECT id FROM cms_faq_quests WHERE category_id = $cat_id") ;
	$records = $inDB->num_rows($result);
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>'.$_LANG['PAGES'].': </strong></span>';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$link = '/faq/'.$inCore->menuId().'/'.$id.'-'.$p;
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

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
			ORDER BY title ASC
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
	if (@$cat['title']) { $inPage->addPathway($cat['title']); }

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
	$perpage = 20;
	$page = $inCore->request('page', 'int', 1);	
	
	if ($id > 0){		
		$perpage = 20;
		if (isset($_REQUEST['page'])) { $page = abs((int)$_REQUEST['page']); } else { $page = 1; }

		$sql = "SELECT *, DATE_FORMAT(pubdate, '%d-%m-%Y') pubdate
				FROM cms_faq_quests
				WHERE category_id = $id AND published = 1
				ORDER BY pubdate DESC
				LIMIT ".(($page-1)*$perpage).", $perpage";		
	} else {
		$sql = "SELECT q.*, DATE_FORMAT(q.pubdate, '%d-%m-%Y') pubdate, c.title cat_title, c.id cid
				FROM cms_faq_quests q, cms_faq_cats c
				WHERE q.published = 1 AND q.category_id = c.id
				ORDER BY pubdate DESC
				LIMIT 10";			
	}
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){	
		$quests = array();
		while($con = $inDB->fetch_assoc($result)){
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
	$smarty->assign('pagebar', pageBar($id, $page, $perpage));		
	$smarty->display('com_faq_view.tpl');		
				
}

///////////////////////////////////// READ QUESTION ////////////////////////////////////////////////////////////////////////////////
if ($do=='read'){
		$sql = "SELECT con.*, DATE_FORMAT(con.pubdate, '%d-%m-%Y') pubdate, 
						DATE_FORMAT(con.answerdate, '%d-%m-%Y') answerdate, 
						DATE_FORMAT(con.pubdate, '%d-%m-%Y') shortdate,
						cat.title cat_title, cat.id cat_id
				FROM cms_faq_quests con, cms_faq_cats cat
				WHERE con.id = $id AND con.category_id = cat.id
				";
				
		$result = $inDB->query($sql) ;
	
		if ($inDB->num_rows($result)){
			$inDB->query("UPDATE cms_faq_quests SET hits = hits + 1 WHERE id = $id") ;
		
			$quest = $inDB->fetch_assoc($result);									
			
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

	$inPage->setTitle($_LANG['ASK_QUE']);
	$inPage->addPathway($_LANG['ASK_QUE']);
	
	$inPage->backButton(false);
	$inPage->addHeadJS('components/faq/js/common.js');
	
	if (!$inCore->inRequest('message')){
		//FORM								
		$smarty = $inCore->initSmarty('components', 'com_faq_add.tpl');			
		$smarty->assign('catslist', $inCore->getListItems('cms_faq_cats'));
		$smarty->display('com_faq_add.tpl');		
	} else {
		//SAVE QUESTION
		$message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'cp1251');
		$category_id = (int)$_POST['category_id'];
		
		$sql = "INSERT INTO cms_faq_quests (category_id, pubdate, published, quest, answer, user_id, answeruser_id, answerdate, hits)
				VALUES ('$category_id', NOW(), 0, '$message', '', 0, 0, NOW(), 0)";
		$inDB->query($sql);
		
		$inPage->setTitle($_LANG['QUESTION_SEND']);
		$inPage->addPathway($_LANG['QUESTION_SEND'], $_SERVER['REQUEST_URI']);
		$inPage->backButton(false);
		
		echo '<div class="con_heading">'.$_LANG['QUESTION_SEND'].'</div>';
		
		echo '<div style="margin-top:10px">'.$_LANG['QUESTION_PREMODER'].'</div>';
		echo '<div style="margin-top:10px"><a href="/faq">'.$_LANG['CONTINUE'].'</a></div>';
	}
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //function
?>