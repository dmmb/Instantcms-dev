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
	
function search_content($query, $look){ //query sends here already prepared and secured!

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

        global $_LANG;

		//BUILD SQL QUERY
		$sql = "SELECT DISTINCT con.*, cat.title cat_title, cat.id cat_id, cat.seolink as cat_seolink, cat.parent_id as cat_parent_id
				FROM cms_content con, cms_category cat
				WHERE MATCH(con.title, con.description, con.content) AGAINST ('$query' IN BOOLEAN MODE) AND con.category_id = cat.id";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
                $inCore->loadLanguage('components/content');
				$link       = "/".$item['seolink'].".html";
				$place      = $_LANG['CATALOG_ARTICLES'];
				$placelink  = $item['cat_parent_id']>0 ? "/".$item['cat_seolink'] : $link;
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