<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	
function rss_catalog($item_id, $cfg, &$rssdata){

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
			$cat = dbGetFields('cms_uc_cats', "id='$item_id'", 'id, title, description, NSLeft, NSRight');
			$catsql = "AND cat.NSLeft >= {$cat['NSLeft']} AND cat.NSRight <= {$cat['NSRight']}";
			$channel['title']       = $cat['title'] ;
			$channel['description'] = $cat['description'];
			$channel['link']        = $rooturl . '/catalog/' . $item_id;
		} else {
			$catsql = '';		
			$channel['title']       = $_LANG['NEW_RECORDS'];
			$channel['description'] = $_LANG['NEW_RECORDS'];
			$channel['link']        = $rooturl . '/catalog';
		}

		//ITEMS
		$sql = "SELECT c.*, DATE_FORMAT(c.pubdate, '%a, %d %b %Y %H:%i:%s GMT') as pubdate, cat.title as category
				FROM cms_uc_items c, cms_uc_cats cat
				WHERE c.published=1 AND c.category_id = cat.id $catsql
				ORDER by c.pubdate DESC
				LIMIT $maxitems";

		$rs = $inDB->query($sql) or die('RSS building error!');

		$items = array();

        if ($inDB->num_rows($rs)){

			while ($item = $inDB->fetch_assoc($rs)){
				$id = $item['id'];
				$items[$id] = $item;
                $items[$id]['link']     = $rooturl . '/catalog/item'.$id.'.html';
				$items[$id]['comments'] = $items[$id]['link'].'#c';
				$items[$id]['category'] = $item['category'];
                
                $image_file = $_SERVER['DOCUMENT_ROOT'].'/images/catalog/medium/'.$item['imageurl'].'.jpg';
                $image_url  = $rooturl . '/images/catalog/medium/'.$item['imageurl'].'.jpg';

                $items[$id]['image'] = file_exists($image_file) ? $image_url : '';
			}

        }

		//RETURN
		$rssdata            = array();
		$rssdata['channel'] = $channel;
		$rssdata['items']   = $items;
		
		return;
}


?>