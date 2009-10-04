<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function cmsInsertTags($tagstr, $target, $item_id){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("DELETE FROM cms_tags WHERE target='$target' AND item_id = $item_id");

	if ($tagstr){
		$tagstr = str_replace(', ', ',', $tagstr);
		$tagstr = str_replace(' ,', ',', $tagstr);
		$tags = explode(',', $tagstr);
		foreach ($tags as $key=>$tag){
			if(strlen($tag)>1){
				if (strlen($tag>15) && !(strstr($tag, ' ') || strstr($tag, '-'))) { $tag = substr($tag, 0, 15); }
			
				$tag = str_replace("\\", '', $tag);
				$tag = str_replace('"', '', $tag);
				$tag = str_replace("'", '', $tag);
				$tag = str_replace("&", '', $tag);
				$tag = strtolower($tag);
				$sql = "INSERT INTO cms_tags (tag, target, item_id) VALUES ('$tag', '$target', $item_id)";
				$inDB->query($sql);
			}
		}
	}
	return;
}

function cmsClearTags($target, $item_id){
    $inDB = cmsDatabase::getInstance();
	$inDB->query("DELETE FROM cms_tags WHERE target='$target' AND item_id = $item_id");
	return;
}


function cmsTagLine($target, $item_id, $links=true, $selected=''){
    $inDB = cmsDatabase::getInstance();
	$sql = "SELECT *
			FROM cms_tags 
			WHERE target='$target' AND item_id=$item_id
			ORDER BY tag DESC";
	$rs = $inDB->query($sql) or die('Error while building tagline');
	$html = '';
	$tags = $inDB->num_rows($rs);
	if ($tags){
		$t = 1;
		while ($tag=$inDB->fetch_assoc($rs)){
			if ($links){
				if ($selected==$tag['tag']){
					$html .= '<a href="/search/tag/'.urlencode($tag['tag']).'" style="font-weight:bold;text-decoration:underline">'.$tag['tag'].'</a>';			
				} else {
					$html .= '<a href="/search/tag/'.urlencode($tag['tag']).'">'.$tag['tag'].'</a>';
				}
			} else {
				$html .= $tag['tag'];		
			}
			if ($t < $tags) { $html .= ', '; $t++; }
		}
	} else {
		$html = '';
	}
	return $html;
}

function cmsTagBar($target, $item_id, $selected=''){
    $inDB = cmsDatabase::getInstance();
	if ($tagline = cmsTagLine($target, $item_id, true, $selected)){
		return '<div class="taglinebar"><span id="header">Теги: </span><span id="tags">'.$tagline.'</span></div>';
	} else {
		return '';
	}
}

function cmsTagItemLink($target, $item_id){
    $inDB = cmsDatabase::getInstance();
	switch ($target){
		case 'content': $sql = "SELECT i.title as title, i.id as item_id, c.title as cat, c.id as cat_id
								FROM cms_content i, cms_category c
								WHERE i.id = $item_id AND i.category_id = c.id";
						$rs = $inDB->query($sql) ;
						if ($inDB->num_rows($rs)){
							$item = $inDB->fetch_assoc($rs);
							$link =  '<a href="/content/0/'.$item['cat_id'].'" class="tag_searchcat">'.$item['cat'].'</a> &rarr; ';
							$link .= '<a href="/content/0/read'.$item['item_id'].'.html" class="tag_searchitem">'.$item['title'].'</a>';							
						}
						break; 
		case 'blogpost': $sql = "SELECT i.title as title, i.id as item_id, c.title as cat, c.id as cat_id, c.owner as owner, c.user_id user_id
								FROM cms_blog_posts i, cms_blogs c
								WHERE i.id = $item_id AND i.blog_id = c.id";
						$rs = $inDB->query($sql) ;
						if ($inDB->num_rows($rs)){
							$item = $inDB->fetch_assoc($rs);
							if ($item['owner'] == 'club') { $item['cat'] = dbGetField('cms_clubs','id='.$item['user_id'],'title'); }
							$link =  '<a href="/blogs/0/'.$item['cat_id'].'/blog.html" class="tag_searchcat">'.$item['cat'].'</a> &rarr; ';
							$link .= '<a href="/blogs/0/'.$item['cat_id'].'/post'.$item['item_id'].'.html" class="tag_searchitem">'.$item['title'].'</a>';
						}
						break; 
		case 'photo': $sql = "SELECT i.title as title, i.id as item_id, c.title as cat, c.id as cat_id
								FROM cms_photo_files i, cms_photo_albums c
								WHERE i.id = $item_id AND i.album_id = c.id";
						$rs = $inDB->query($sql) ;
						if ($inDB->num_rows($rs)){
							$item = $inDB->fetch_assoc($rs);
							$link =  '<a href="/photos/0/'.$item['cat_id'].'" class="tag_searchcat">'.$item['cat'].'</a> &rarr; ';
							$link .= '<a href="/photos/0/photo'.$item['item_id'].'.html" class="tag_searchitem">'.$item['title'].'</a>';
						}
						break; 
		case 'userphoto': $sql = "SELECT i.title as title, i.id as item_id, c.nickname as cat, c.id as cat_id, c.login as login
								FROM cms_user_photos i, cms_users c
								WHERE i.id = $item_id AND i.user_id = c.id";
						$rs = $inDB->query($sql) ;
						if ($inDB->num_rows($rs)){
							$item = $inDB->fetch_assoc($rs);
							$link =  '<a href="'.cmsUser::getProfileURL($item['login']).'" class="tag_searchcat">'.$item['cat'].'</a> &rarr; ';
							$link .= '<a href="/users/0/'.$item['cat_id'].'/photo'.$item['item_id'].'.html" class="tag_searchitem">'.$item['title'].'</a>';
						}
						break; 
		case 'catalog': $sql = "SELECT i.title as title, i.id as item_id, c.title as cat, c.id as cat_id
								FROM cms_uc_items i, cms_uc_cats c
								WHERE i.id = $item_id AND i.category_id = c.id";
						$rs = $inDB->query($sql) ;
						if ($inDB->num_rows($rs)){
							$item = $inDB->fetch_assoc($rs);
							$link =  '<a href="/catalog/0/'.$item['cat_id'].'" class="tag_searchcat">'.$item['cat'].'</a> &rarr; ';
							$link .= '<a href="/catalog/0/item'.$item['item_id'].'.html" class="tag_searchitem">'.$item['title'].'</a>';
						}
						break; 	
	}
	return $link;
}

function cmsTagsList(){
    $inDB = cmsDatabase::getInstance();
	$html = '';		
	$sql = "SELECT t.*, COUNT(t.tag) as num
			FROM cms_tags t
			GROUP BY t.tag
			ORDER BY t.tag";	
	$result = $inDB->query($sql) ;
	if ($inDB->num_rows($result)>0){
		while($tag = $inDB->fetch_assoc($result)){
			if ($tag['tag']){
				$html .= '<a href="/search/tag/'.urlencode($tag['tag']).'">'.$tag['tag'].'</a> ('.$tag['num'].') ';
			}
		}
	}		
	return $html;
}
?>