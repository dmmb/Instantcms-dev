<?php
if(!defined('VALID_CMS_EDITOR')) { die('ACCESS DENIED'); }
	
function editorCheckAuth(){
    $inCore = cmsCore::getInstance();
    $inUser = cmsUser::getInstance();
	if (!$inUser->id) {
		$inCore->redirect('/admin/login.php');
	} else {
        $category_id = $inCore->userIsEditor($inUser->id);
		if (!$category_id){
			$inCore->redirect('/admin/login.php');
		} else {
            return $category_id;
        }
	}
}
	
function editorMenu(){
	foreach($GLOBALS['ed_menu'] as $key => $value){	
		echo '<a class="menuitem" href="'.$GLOBALS['ed_menu'][$key]['link'].'">'.$GLOBALS['ed_menu'][$key]['title'].'</a>';	
	}
	return;
}

function editorPage(){
	echo $GLOBALS['ed_page_body'];
	return;
}

function editorHead(){
	if ($GLOBALS['ed_page_title']){
		echo '<title>'.$GLOBALS['ed_page_title'].' - Панель редактора</title>';
	} else {
		echo '<title>Редактор InstantCMS</title>';	
	}
	
	foreach($GLOBALS['ed_page_head'] as $key=>$value) { 
		echo $GLOBALS['ed_page_head'][$key] ."\n"; 
		unset ($GLOBALS['ed_page_head'][$key]);
	}
	
	return;
}

function editorGetCat(){

    $inUser = cmsUser::getInstance();

	$userid = $inUser->id;

	$sql = "SELECT c.*
			FROM cms_users u, cms_user_groups g, cms_category c, cms_content con
			WHERE u.id = '$userid' AND u.group_id = g.id AND c.modgrp_id = g.id 
			LIMIT 1";
	$result = dbQuery($sql);
	
	if (mysql_num_rows($result)){
		$cat = mysql_fetch_assoc($result);
		return $cat;
	} else { return false; }

}

function pageBar($cat_id, $current, $perpage){
	$html = '';
	
	$result = dbQuery("SELECT id FROM cms_content WHERE category_id = $cat_id");
	$records = mysql_num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = cpAddParam($_SERVER['QUERY_STRING'], 'page', $p);
					
					$html .= ' <a href="?'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

?>