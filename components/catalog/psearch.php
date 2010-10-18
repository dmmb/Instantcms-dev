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
	
function search_catalog($query, $look){ //query sends here already prepared and secured!
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT i.*, c.title as cat, c.id as cat_id
				FROM cms_uc_items i, cms_uc_cats c
				WHERE MATCH(i.title, i.fieldsdata) AGAINST ('$query' IN BOOLEAN MODE) AND i.category_id = c.id";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/catalog/item".$item['id'].".html";
				$place = $item['cat'];
				$placelink = "/catalog/".$item['cat_id'];				
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