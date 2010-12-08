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
	
function search_price($query, $look){ //query sends here already prepared and secured!
    
		$inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT i.*, c.title as cat, c.id as cat_id
				FROM cms_price_items i, cms_price_cats c
				WHERE MATCH(i.title) AGAINST ('$query' IN BOOLEAN MODE) AND i.category_id = c.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/price/".$item['cat_id'];
				$place = $item['cat'];
				$placelink = $link;				
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