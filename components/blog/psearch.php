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
	
function search_blog($query, $look, $mode='text'){ //query sends here already prepared and secured!

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

        //BUILD SQL QUERY
		$sql = "SELECT DISTINCT con.*, cat.title cat_title, cat.id cat_id, cat.owner owner, cat.user_id user_id
				FROM cms_blog_posts con, cms_blogs cat
				WHERE MATCH(con.content) AGAINST ('$query' IN BOOLEAN MODE) AND con.blog_id = cat.id";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				if ($item['owner'] == 'club') { $item['cat_title'] = dbGetField('cms_clubs','id='.$item['user_id'],'title'); }
				//build params
				$link = "/blogs/0/".$item['cat_id']."/post".$item['id'].".html";
				$place = 'Блог &laquo;'.$item['cat_title'].'&raquo;';
				$placelink = "/blogs/0/".$item['cat_id']."/blog.html";				
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