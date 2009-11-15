<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

session_start();
define("VALID_CMS", 1);	

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    require(PATH."/core/cms.php");

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $inConf     = cmsConfig::getInstance();
    global $_LANG;
	$menuid = $inCore->menuId();
	$cfg    = $inCore->loadComponentConfig('rssfeed');

	if (isset($_REQUEST['do'])){ $do = $_REQUEST['do'];	} else { $do = 'rss'; 		}
	if (isset($_REQUEST['target'])){ $target = $_REQUEST['target'];	} else { die(); }
	if (isset($_REQUEST['item_id'])) { $item_id = $_REQUEST['item_id']; } else { die(); }

	if (!isset($cfg['addsite'])) { $cfg['addsite'] = 1; }
	if (!isset($cfg['icon_on'])) { $cfg['icon_on'] = 0; }
	if (!isset($cfg['maxitems'])) { $cfg['maxitems'] = 50; }

////////////////////// RSS /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='rss'){
	$rss = '';
	
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/components/'.$target.'/prss.php')){
		
		$inCore->includeFile('components/'.$target.'/prss.php');
		
		eval('rss_'.$target.'($item_id, $cfg, $rssdata);');	
		
		$ready = sizeof($rssdata['items']);
			
		//BUILD RSS FEED		
		if ($ready){
			$channel = $rssdata['channel'];
			$items = $rssdata['items'];
		
			if ($cfg['addsite']) { $channel['title'] .= ' :: ' . $inConf->sitename; }
		
			$rss .= '<?xml version="1.0" encoding="windows-1251" ?>' ."\n";
			$rss .= '<rss version="2.0">' ."\n";
				$rss .= '<channel>' ."\n";
					//CHANNEL
					$rss .= '<title>'.$channel['title'].'</title>' ."\n";
					$rss .= '<link>'.$channel['link'].'</link>' ."\n";
					$rss .= '<description>'.strip_tags($channel['description']).'</description>' ."\n";			
					//CHANNEL ICON
					if ($cfg['icon_on']){
						$rss .= '<image>'."\n";
							$rss .= '<title>'.$cfg['icon_title'].'</title>'."\n";
							$rss .= '<url>'.$cfg['icon_url'].'</url>'."\n";
							$rss .= '<link>'.$cfg['icon_link'].'</link>'."\n";						
						$rss .= '</image>'."\n";
					}		
					//ITEMS
					foreach ($items as $key=>$item){
						$rss .= '<item>' ."\n";
							$rss .= '<title>'.strip_tags($item['title']).'</title>' ."\n";
							$rss .= '<pubDate>'.$item['pubdate'].'</pubDate>' ."\n";
							$rss .= '<guid>'.$item['link'].'</guid>' ."\n";
							$rss .= '<link>'.$item['link'].'</link>' ."\n";
							if (isset($item['description'])){
								$rss .= '<description><![CDATA['.strip_tags($item['description']).']]></description>' ."\n";
							}
							$rss .= '<author>'.$item['author'].'</author>' ."\n";
							$rss .= '<category>'.$item['category'].'</category>' ."\n";
							$rss .= '<comments>'.$item['comments'].'</comments>' ."\n";
						$rss .= '</item>' ."\n";	
					}		
				$rss .= '</channel>' ."\n";			
			$rss .= '</rss>';
	
		} else {	
			$rss = '<p>'.$_LANG['NOT_POST_IN_RSS'].'</p>';
		}
		
	} else {
		$rss = '<p>'.$_LANG['NOT_RSS_GENERATOR'].'</p>';
	}
	
	echo $rss;

}//RSS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	

?>