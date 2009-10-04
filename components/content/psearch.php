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
	
function search_content($query, $look){ //query sends here already prepared and secured!

        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		//SPLIT QUERY TO WORDS	
		$words = split(' ', $query);
		$count = sizeof($words);
		$n=0;
		
		//BUILD SQL QUERY
		$sql = "SELECT DISTINCT con.*, cat.title cat_title, cat.id cat_id
				FROM cms_content con, cms_category cat
				WHERE ";	
										
		if($look == 'anyword'){
			//$looktype = 'любое слово';
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "con.content LIKE '%$w%' OR con.title LIKE '%$w%'"; }
					else { $sql .= " OR con.content LIKE '%$w%' OR con.title LIKE '%$w%'"; }
				}
			}		
		}
	
		if($look == 'allwords'){
			//looktype = 'все слова';		
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "con.content LIKE '%$w%' OR con.title LIKE '%$w%'"; }
					else { $sql .= " AND (con.content LIKE '%$w%' OR con.title LIKE '%$w%')"; }
				}
			}		
		}
		
		if($look == 'phrase'){
			//$looktype = 'фраза целиком';		
			$sql .= "(con.content LIKE '%$query%' OR con.title LIKE '%w%')";
		}
		
		$sql .= " AND con.category_id = cat.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/content/0/read".$item['id'].".html";
				$place = 'Статьи сайта';
				$placelink = "/content/0/".$item['cat_id'];				
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('".session_id()."', '".$item['title']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		return;
}


?>