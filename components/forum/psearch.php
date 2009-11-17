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
	
function search_forum($query, $look){ //query sends here already prepared and secured!

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;

		//BUILD SQL QUERY
		$sql = "SELECT t.*, f.title as forum, f.id as forum_id
				FROM cms_forum_threads t, cms_forums f
				WHERE MATCH(t.title) AGAINST ('$query' IN BOOLEAN MODE) AND t.forum_id = f.id LIMIT 50";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/forum/0/thread".$item['id'].".html";
				$place = $item['forum'];
				$placelink = "/forum/0/".$item['forum_id'];				
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`id`, `session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('', '".session_id()."', '".$item['title']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		//SEARCH IN FORUM POSTS
		//BUILD SQL QUERY
		$sql = "SELECT p.*, t.title as thread, t.id as thread_id
				FROM cms_forum_posts p, cms_forum_threads t
				WHERE MATCH(p.content) AGAINST ('$query' IN BOOLEAN MODE) AND p.thread_id = t.id LIMIT 50";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql) or die('SEARCH ERROR'); 
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
                $inCore->loadLanguage('components/forum');
				$link = "/forum/0/thread".$item['thread_id'].".html";
				$place = $_LANG['FORUM_POST'];
				$placelink = $link;	
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`id`, `session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('', '".session_id()."', '".$item['thread']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		return;
}

?>