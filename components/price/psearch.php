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
	
function search_price($query, $look){ //query sends here already prepared and secured!			
		$inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

        //SPLIT QUERY TO WORDS
		$words = split(' ', $query);
		$count = sizeof($words);
		$n=0;
		
		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT i.*, c.title as cat, c.id as cat_id
				FROM cms_price_items i, cms_price_cats c
				WHERE ";	
										
		if($look == 'anyword'|| $look == 'allwords'){
			//$looktype = 'любое слово';
			if ($look == 'anyword') { $clue = 'OR'; } else { $clue = 'AND'; }
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "i.title LIKE '%$w%'"; }
					else { $sql .= " $clue i.title LIKE '%$w%'"; }
				}
			}		
		}
		
		if($look == 'phrase'){
			//$looktype = 'фраза целиком';		
			$sql .= "i.title LIKE '%$query%'";			
		}
		
		$sql .= " AND i.category_id = c.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/price/0/".$item['cat_id'];
				$place = $item['cat'];
				$placelink = $link;				
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