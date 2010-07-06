<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function blogAuthors($blogid){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$owner = dbGetField('cms_blogs', 'id='.$blogid, 'user_id');
	
	$authors = array();
	$authors[] = $owner;
	
	$sql = "SELECT * FROM cms_blog_authors WHERE blog_id = $blogid";
	$rs = $inDB->query($sql);
	
	if ($inDB->num_rows($rs)){
		while ($u = $inDB->fetch_assoc($rs)){
			if (!in_array($u['user_id'], $authors)){
				$authors[] = $u['user_id'];
			}
		}
	}
	
	return $authors;
}
////////////////////////////////////////////////////////////////////////////////
function blogCats($blog_id, $bloglink, $cat_id){
	$inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

	$html = '';
	
	$rootposts = dbRowsCount('cms_blog_posts', "blog_id = $blog_id AND published = 1");
			
	$sql = "SELECT cat.*, IFNULL(COUNT(p.id), 0) as num
			FROM cms_blog_cats cat
			LEFT JOIN cms_blog_posts p ON p.cat_id = cat.id AND p.blog_id = $blog_id AND p.published = 1
			WHERE cat.blog_id = $blog_id
			GROUP BY cat.id
			";
			
	$result = $inDB->query($sql) ;
	
	$cats = array();
	if ($inDB->num_rows($result)){		
		while ($cat = $inDB->fetch_assoc($result)){
			$next = sizeof($cats);
			$cats[$next] =  $cat;
		}	
	}	
	
	$smarty = $inCore->initSmarty('components', 'com_blog_catslist.tpl');			
	
	$smarty->assign('blog_id', $blog_id);
	$smarty->assign('bloglink', $bloglink);
	$smarty->assign('cat_id', $cat_id);
	$smarty->assign('rootposts', $rootposts);
	$smarty->assign('cats', $cats);
	
	ob_start();
	
		$smarty->display('com_blog_catslist.tpl');		
		
	return ob_get_clean();
}

function blogCategoryList($selected=0, $blog_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

	$sql = "SELECT * FROM cms_blog_cats WHERE blog_id = $blog_id ORDER BY id ASC";
	$result = $inDB->query($sql) ;
	$html = '';
	while($cat = $inDB->fetch_assoc($result)){
		if (@$selected==$cat['id']){
			$s = 'selected';
		} else {
			$s = '';
		}		
		$html .= '<option value="'.$cat['id'].'" '.$s.'>'.$cat['title'].'</option>'."\n";
	}
	return $html;
}

function blogPostNav($model, $post_pubdate, $blog_id, $bloglink){
    
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

    global $_LANG;

	$html1 = '';
	$html2 = '';

	$prevpost = $inDB->get_fields('cms_blog_posts', "pubdate < '$post_pubdate' AND blog_id = $blog_id", 'seolink, title', "pubdate DESC");

	if ($prevpost['seolink']) {
		$html1 .= '&larr; <a href="'.$model->getPostUrl(null, $bloglink, $prevpost['seolink']).'">'.$prevpost['title'].'</a>';
	}

	$nextpost = $inDB->get_fields('cms_blog_posts', "pubdate > '$post_pubdate' AND blog_id = $blog_id", 'seolink, title', "pubdate ASC");

	if ($nextpost['seolink']) {
		$html2 = '<a href="'.$model->getPostUrl(null, $bloglink, $nextpost['seolink']).'">'.$nextpost['title'].'</a> &rarr;';
	}
	
	if ($html1 && $html2){
		$html = $html1 . ' | ' . $html2;
	} else {
		$html = $html1 . $html2;
	}

	return $html;
    
}

function blogAttachedImages($post_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

	$html = '';

	$sql = "SELECT * FROM cms_blog_files WHERE post_id = $post_id ORDER BY id DESC";
	$result = $inDB->query($sql) ;
	
	if ($inDB->num_rows($result)){
		$html .= '<div class="blog_attachbox">';
			while($img = $inDB->fetch_assoc($result)){
				$html .= '<div class="blog_attachthumb">';
				$html .= '<table width="100%" height="100" cellspacing="0" cellpadding="0"><tr><td valign="middle" align="center">';
					$html .= '<a href="/upload/blog/post'.$post_id.'/'.$img['filename'].'" target="_blank">';
						$html .= '<img class="photo_thumb_img" src="/upload/blog/post'.$post_id.'/small/'.$img['filename'].'" border="0" />';
					$html .= '</a>';
				$html .= '</td></tr></table>';
				$html .= '</div>';
			}
		$html .= '</div>';
	}
	
	return $html;

}

function blogComments($blog_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$comments = 0;
	$posts = dbGetTable('cms_blog_posts', "blog_id = $blog_id");
	
	if ($posts){
		foreach($posts as $key=>$data){
			$comments += dbRowsCount('cms_comments', "target='blog' AND target_id=".$data['id']);
		}
	}
	
	return $comments;
}
?>
