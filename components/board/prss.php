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
	
function rss_board($item_id, $cfg, &$rssdata){

	$inCore = cmsCore::getInstance();

    $inCore->loadModel('board');
    $model = new cms_model_board();

    global $_LANG;

	$channel = array();
	$items   = array();

	if ($item_id != 'all' && preg_match('/^([0-9]+)$/i', $item_id)) {

		$model->whereCatIs($item_id);

		$cat = $model->getCategory($item_id);
		$channel['title']       = $cat['title'];
		$channel['description'] = preg_replace ("'&([a-z]{2,5});'i", '', $cat['description']);
		$channel['link']        = HOST.'/board/'.$cat['id'];

	} else {

		$channel['title'] = $_LANG['BOARD'];
		$channel['description'] = $_LANG['BOARD'];
		$channel['link'] = HOST;

	}

	$model->orderBy('pubdate', 'DESC');

	$model->limitPage(1, $cfg['maxitems']);

	$advs = $model->getAdverts(false, false, false, true);

	foreach($advs as $item){

		$item['link']     = HOST.'/board/read'.$item['id'].'.html';
		$item['comments'] = $item['link'].'#c';				
		$item['category'] = $item['cat_title'];
		$item['description'] = substr(strip_tags($item['content']), 0, 250). '...';
		$image_file = PATH.'/images/board/medium/'.$item['file'];
		$image_url  = HOST.'/images/board/medium/'.$item['file'];
		$item['image'] = file_exists($image_file) ? $image_url : '';
		$item['size']  = round(filesize($image_file));
		$items[] = $item;

	}

	$rssdata = array();			
	$rssdata['channel'] = $channel;
	$rssdata['items']   = $items;
	
	return;
}


?>