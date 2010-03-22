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
	
function search_photos($query, $look){ //query sends here already prepared and secured!			
		$inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		
		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT f.*, a.title as cat, a.id as cat_id
				FROM cms_photo_files f, cms_photo_albums a
				WHERE MATCH(f.title, f.description) AGAINST ('$query' IN BOOLEAN MODE) AND f.album_id = a.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/photos/photo".$item['id'].".html";
				$place = $item['cat'];
				$placelink = '/photos/'.$item['cat_id'];				
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