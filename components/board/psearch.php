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
	
function search_board($query, $look){ //query sends here already prepared and secured!

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT f.*, f.title as title, a.title as cat, a.id as cat_id
				FROM cms_board_items f, cms_board_cats a
				WHERE MATCH(f.content) AGAINST ('$query' IN BOOLEAN MODE) AND f.category_id = a.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/board/read".$item['id'].".html";
				$place = $item['cat'];
				$placelink = '/board/'.$item['cat_id'];
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`id`, `session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('', '".session_id()."', '".$item['title']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		return;
}


?>