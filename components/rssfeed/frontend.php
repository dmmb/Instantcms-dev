<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

function rssfeed(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inConf     = cmsConfig::getInstance();

	$cfg        = $inCore->loadComponentConfig('rssfeed');
    
	// ��������� ������� �� ���������
	if(!$cfg['component_enabled']) { cmsCore::error404(); }

    global $_LANG;

    $do         = $inCore->request('do', 'str', 'rss');
    $target     = $inCore->request('target', 'str', 'rss');
    $item_id    = $inCore->request('item_id', 'str', 'all');

	// ��������� ������� ���������
	$target  = preg_replace ('/[^a-z0-9]/i', '', $target);
	if (!preg_match('/^([a-z0-9\-]+)$/i', $item_id)) { $item_id = 'all'; }

    if (!isset($cfg['addsite'])) { $cfg['addsite'] = 1; }
	if (!isset($cfg['icon_on'])) { $cfg['icon_on'] = 0; }
	if (!isset($cfg['maxitems'])) { $cfg['maxitems'] = 50; }

////////////////////// RSS /////////////////////////////////////////////////////////////////////////////////////////////////
if ($do=='rss'){
	$rss = '';

	if (file_exists(PATH.'/components/'.$target.'/prss.php')){

		header('Content-Type: application/rss+xml; charset=windows-1251');

        cmsCore::loadLanguage('components/'.$target);

		$inCore->includeFile('components/'.$target.'/prss.php');

		eval('rss_'.$target.'($item_id, $cfg, $rssdata);');	
		
		$ready = sizeof($rssdata['items']);

		//BUILD RSS FEED		
			$channel = $rssdata['channel'];
			$items   = $rssdata['items'];
		
			if ($cfg['addsite']) { $channel['title'] .= ' :: ' . $inConf->sitename; }
		
			$rss .= '<?xml version="1.0" encoding="windows-1251" ?>' ."\n";
			$rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' ."\n";
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
                    if (is_array($items)){
					foreach ($items as $key=>$item){
						$rss .= '<item>' ."\n";
							$rss .= '<title>'.strip_tags(str_replace('&', '&amp;', $item['title'])).'</title>' ."\n";
							$rss .= '<pubDate>'.$item['pubdate'].'</pubDate>' ."\n";
							$rss .= '<guid>'.$item['link'].'</guid>' ."\n";
							$rss .= '<link>'.$item['link'].'</link>' ."\n";
							if (isset($item['description'])){
								$rss .= '<description><![CDATA['.strip_tags($item['description']).']]></description>' ."\n";
							}
							$rss .= '<category>'.$item['category'].'</category>' ."\n";
							$rss .= '<comments>'.$item['comments'].'</comments>' ."\n";                            
                            if ($item['image']){
								  $rss .= '<enclosure url="'.$item['image'].'" length="'.$item['size'].'" type="image/jpeg" />';
                            }
						$rss .= '</item>' ."\n";	
					}		
                    }
				$rss .= '</channel>' ."\n";			
			$rss .= '</rss>';
	
		} else {	
		$rss = '<p>'.$_LANG['NOT_RSS_GENERATOR'].'</p>';
	}
	
	$inCore->halt($rss);

}//RSS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    

}

?>