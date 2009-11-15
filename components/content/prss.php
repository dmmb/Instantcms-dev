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
	
function rss_content($item_id, $cfg, &$rssdata){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		global $_CFG;
		global $_LANG;
		$maxitems = $cfg['maxitems'];
		$rooturl = 'http://'.$_SERVER['HTTP_HOST'];
		if ($item_id == 'all') { $item_id = 0; }	
		
		$channel = array();
		//CHANNEL
		if ($item_id){
			$cat = dbGetFields('cms_category', 'id='.$item_id, 'id, title, description, NSLeft, NSRight');
			$catsql = "AND c.category_id = cat.id AND cat.NSLeft >= {$cat['NSLeft']} AND cat.NSRight <= {$cat['NSRight']}";

			$channel['title'] = $cat['title'] ;
			$channel['description'] = $cat['description'];
			$channel['link'] = $rooturl . '/content/0/'.$item_id;
		} else {
			$catsql = '';
		
			$channel['title'] = $_LANG['NEW_MATERIALS'];
			$channel['description'] = $_LANG['LAST_ARTICLES_NEWS'];
			$channel['link'] = $rooturl;
		}		
		//ITEMS
		$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate, u.nickname as author, cat.title as category
				FROM cms_content c, cms_users u, cms_category cat
				WHERE c.user_id = u.id AND c.category_id = cat.id $catsql
				ORDER by c.pubdate DESC
				LIMIT $maxitems";
		$rs = $inDB->query($sql) or die('RSS building error!');		
		$items = array();
		if ($inDB->num_rows($rs)){			
			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
				$items[$id] = $item;
				$items[$id]['link'] = $rooturl . '/content/0/read'.$id.'.html';
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