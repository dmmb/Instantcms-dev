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
	
function rss_blogs($item_id, $cfg, &$rssdata){
    
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        
		global $_CFG;
        global $_LANG;
	
		$maxitems = $cfg['maxitems'];
		$rooturl = 'http://'.$_SERVER['HTTP_HOST'];
		if ($item_id == 'all') { $item_id = 0; }

		//CHANNEL
		if ($item_id){
			$cat    = $inDB->get_fields('cms_blogs', 'id='.$item_id, 'id, title, seolink');
			$catsql = "AND p.blog_id = $item_id";

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
                       DATE_FORMAT(p.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate,
                       u.nickname as author,
                       cat.id as cat_id,
                       cat.title as category,
                       cat.seolink as bloglink
				FROM cms_blog_posts p, cms_users u, cms_blogs cat
				WHERE p.user_id = u.id AND p.blog_id = cat.id $catsql
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
				$items[$id]['comments'] = $items[$id]['link'].'#c';
				$items[$id]['category'] = $item['category'];
			}

		}		
		
		//RETURN		
		$rssdata = array();	
		$rssdata['channel'] = $channel;
		$rssdata['items'] = $items;

		return;

}

?>