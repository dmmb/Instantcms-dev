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
	
function rss_comments($item_id, $cfg, &$rssdata){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		global $_CFG;
        global $_LANG;

		$maxitems   = $cfg['maxitems'];
		$rooturl    = 'http://'.$_SERVER['HTTP_HOST'];
		if ($item_id == 'all') { $item_id = 0; }
			
		//CHANNEL
		if ($item_id){						
			$item_id                = explode('-', $item_id);
			$catsql                 = "AND c.target = '".$item_id[0]."' AND c.target_id = ".$item_id[1]."";
            $target                 = $inDB->get_fields('cms_comments', "target='{$item_id[0]}' AND target_id='{$item_id[1]}'", 'target_title, target_link');
			$channel['title']       = $target['target_title'];
			$channel['description'] = $_LANG['COMMENTS'];
			$channel['link']        = $rooturl . $target_link;
		} else {
			$catsql = '';		
			$channel['title']       = $_LANG['COMMENTS_ON_SITE'];
			$channel['description'] = $_LANG['COMMENTS_ON_SITE'];
			$channel['link']        = $rooturl;
		}
        
		//ITEMS				
		$sql = "SELECT  c.*,
                        DATE_FORMAT(c.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate,
                        u.nickname as author
				FROM cms_comments c, cms_users u
				WHERE c.user_id = u.id AND c.published=1 $catsql
				ORDER by c.pubdate DESC
				LIMIT $maxitems";
						
		$rs = $inDB->query($sql) or die('RSS building error!');		
		$items = array();
				
		if ($inDB->num_rows($rs)){	
			while ($item = $inDB->fetch_assoc($rs)){
				$id                     = $item['id'];
				$items[$id]             = $item;
				$items[$id]['title']    = strip_tags($item['content']);
				$items[$id]['link']     = $rooturl . $item['target_link'];
				$items[$id]['comments'] = $rooturl . $item['target_link'];
				$items[$id]['category'] = '';
			}
		}
		
		//RETURN		
		$rssdata = array();	
		$rssdata['channel'] = $channel;
		$rssdata['items']   = $items;
		return;
}

?>