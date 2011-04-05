<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	
function rss_forum($item_id, $cfg, &$rssdata){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

		global $_CFG;
		global $_LANG;

		$maxitems   = $cfg['maxitems'];
		$rooturl    = 'http://'.$_SERVER['HTTP_HOST'];

		if ($item_id == 'all') { $item_id = 0; }	
		
		$channel = array();

		//CHANNEL
		if ($item_id){
			$cat = dbGetFields('cms_forums', "id='$item_id'", 'id, title, description, NSLeft, NSRight');
			$catsql = "AND cat.NSLeft >= {$cat['NSLeft']} AND cat.NSRight <= {$cat['NSRight']}";
			$channel['title']       = $cat['title'] ;
			$channel['description'] = $cat['description'];
			$channel['link']        = $rooturl . '/forum/' . $item_id;
		} else {
			$catsql = '';		
			$channel['title']       = $_LANG['LAST_THREADS'];
			$channel['description'] = $_LANG['LAST_THREADS'];
			$channel['link']        = $rooturl . '/forum';
		}

		//ITEMS
		$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate, cat.title as category,
                        COUNT(p.id) as posts_count
				FROM cms_forums cat, cms_forum_threads c
                LEFT JOIN cms_forum_posts p ON p.thread_id = c.id
				WHERE c.forum_id = cat.id $catsql
                GROUP BY c.id
				ORDER by c.pubdate DESC
				LIMIT $maxitems";

		$rs = $inDB->query($sql) or die('RSS building error!');

		$items = array();

        if ($inDB->num_rows($rs)){

            $forumcfg = $inCore->loadComponentConfig('forum');

			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
                $item['title'] .= ' '.$item['posts_count'];
                $pages = ceil($item['posts_count'] / $forumcfg['pp_thread']);
				$items[$id] = $item;
                $items[$id]['link']     = $rooturl . '/forum/thread'.$id.'-'.$pages.'.html#new';
				$items[$id]['category'] = $item['category'];                
			}

        }

		//RETURN
		$rssdata            = array();
		$rssdata['channel'] = $channel;
		$rssdata['items']   = $items;
		
		return;
}


?>