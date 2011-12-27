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
	
function rss_forum($item_id, $cfg, &$rssdata){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

		global $_CFG;
		global $_LANG;

		$maxitems   = $cfg['maxitems'];

		if ($item_id == 'all') { $item_id = 0; }	
		
		$channel = array();

		//CHANNEL
		if ($item_id){
			$cat = $inDB->get_fields('cms_forums', "id='$item_id'", 'id, title, description, NSLeft, NSRight');
			$catsql = "AND cat.NSLeft >= {$cat['NSLeft']} AND cat.NSRight <= {$cat['NSRight']}";
			$channel['title']       = $cat['title'] ;
			$channel['description'] = $cat['description'];
			$channel['link']        = HOST . '/forum/' . $item_id;
		} else {
			$catsql = '';		
			$channel['title']       = $_LANG['LAST_THREADS'];
			$channel['description'] = $_LANG['LAST_THREADS'];
			$channel['link']        = HOST . '/forum';
		}

		//ITEMS
		$sql = "SELECT c.*, cat.title as category,
                        COUNT(p.id) as posts_count, p.id as p_id
				FROM cms_forum_threads c
                INNER JOIN cms_forum_posts p ON p.thread_id = c.id
				INNER JOIN cms_forums cat ON cat.id = c.forum_id
				WHERE c.is_hidden = 0 $catsql
                GROUP BY c.id
				ORDER by c.pubdate DESC
				LIMIT $maxitems";

		$rs = $inDB->query($sql);

		$items = array();

        if ($inDB->num_rows($rs)){

            $forumcfg = $inCore->loadComponentConfig('forum');

			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
                $item['title'] .= ' '.$item['posts_count'];
                $pages = ceil($item['posts_count'] / $forumcfg['pp_thread']);
				$items[$id] = $item;
                $items[$id]['link']     = HOST . '/forum/thread'.$id.'-'.$pages.'.html';
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