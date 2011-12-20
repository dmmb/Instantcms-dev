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
	
function rss_blogs($item_id, $cfg, &$rssdata){
    
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        
		global $_CFG;
        global $_LANG;
	
		$maxitems = $cfg['maxitems'];
		$rooturl = HOST;
		if ($item_id == 'all') { $item_id = 0; }

		//CHANNEL
		if ($item_id){
			$cat    = $inDB->get_fields('cms_blogs', "id='$item_id'", 'id, title, seolink');
			$catsql = "AND p.blog_id = '$item_id'";

            $inCore->loadModel('blogs');
            $model = new cms_model_blogs();

			$channel['title']       = $cat['title'];
			$channel['description'] = '';
			$channel['link']        = $rooturl . $model->getBlogURL(0, $cat['seolink']);
		} else {
			$catsql = '';
		
			$channel['title']       = $_LANG['NEW_POSTS_IN_BLOGS'];
			$channel['description'] = $_LANG['NEW_POSTS_IN_BLOGS'];
			$channel['link']        = $rooturl;
		}

		//ITEMS
		$sql = "SELECT p.*,
                       cat.id as cat_id,
                       cat.title as category,
                       cat.seolink as bloglink
				FROM cms_blog_posts p, cms_blogs cat
				WHERE p.published = 1 AND p.blog_id = cat.id AND cat.allow_who = 'all' $catsql
				ORDER by p.pubdate DESC
				LIMIT $maxitems";

		$rs = $inDB->query($sql) or die('RSS building error!');

		$items = array();

		if ($inDB->num_rows($rs)){

            $inCore->loadModel('blogs');
            $model = new cms_model_blogs();

			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
				$items[$id] = $item;
                $items[$id]['link']     = $rooturl . $model->getPostURL(0, $item['bloglink'], $item['seolink']);
				$items[$id]['description'] = parseHide(mb_substr(strip_tags($items[$id]['content_html']), 0, 250). '...');
				$items[$id]['comments'] = $items[$id]['link'].'#c';
				$items[$id]['category'] = $item['category'];
			}

		}		
		$items = cmsCore::callEvent('GET_BLOGS', $items);
		//RETURN		
		$rssdata = array();	
		$rssdata['channel'] = $channel;
		$rssdata['items'] = $items;

		return;

}
function parseHide($text){

	$inUser = cmsUser::getInstance();

	$pattern        = '/\[hide\](.*?)\[\/hide\]/i';
	$hidden_text    = '';

	if (!$inUser->id){
		$replacement = '<div class="bb_tag_hide">'.$hidden_text.'</div>';
	} else {
		$replacement = '<div class="bb_tag_hide">${1}</div>';
	}

	return preg_replace($pattern, $replacement, $text);

}

?>