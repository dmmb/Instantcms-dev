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

        global $_LANG;

        //BUILD SQL QUERY
		$sql = "SELECT DISTINCT con.*, cat.title cat_title, cat.id cat_id, cat.owner owner, cat.user_id user_id, cat.seolink as bloglink
				FROM cms_blog_posts con, cms_blogs cat
				WHERE MATCH(con.content) AGAINST ('$query' IN BOOLEAN MODE) AND con.blog_id = cat.id";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				if ($item['owner'] == 'club') { $item['cat_title'] = dbGetField('cms_clubs','id='.$item['user_id'],'title'); }
				//build params
                $inCore->loadLanguage('components/blog');

                $inCore->loadModel('blog');
                $model = new cms_model_blog();

				$link = $model->getPostURL(0, $item['bloglink'], $item['seolink']);
				$place = $_LANG['BLOG'].' &laquo;'.$item['cat_title'].'&raquo;';
				$placelink = $model->getBlogURL(0, $item['bloglink']);
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