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
        
		//SPLIT QUERY TO WORDS	
		$words = split(' ', $query);
		$count = sizeof($words);
		$n=0;
		
		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT f.*, f.title as title, a.title as cat, a.id as cat_id
				FROM cms_board_items f, cms_board_cats a
				WHERE ";	
										
		if($look == 'anyword'|| $look == 'allwords'){
			//$looktype = 'любое слово';
			if ($look == 'anyword') { $clue = 'OR'; } else { $clue = 'AND'; }
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "f.content LIKE '%$w%'"; }
					else { $sql .= " $clue f.content LIKE '%$w%'"; }
				}
			}		
		}
		
		if($look == 'phrase'){
			//$looktype = 'фраза целиком';		
			$sql .= "f.title LIKE '%$query%'";			
		}
		
		$sql .= " AND f.category_id = a.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/board/0/read".$item['id'].".html";
				$place = $item['cat'];
				$placelink = '/board/0/'.$item['cat_id'];
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