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
	
function search_forum($query, $look){ //query sends here already prepared and secured!
        $inDB = cmsDatabase::getInstance();

		//SPLIT QUERY TO WORDS	
		$words = split(' ', $query);
		$count = sizeof($words);
		$n=0;
		
		//SEARCH IN THREADS TITLES
		//BUILD SQL QUERY
		$sql = "SELECT t.*, f.title as forum, f.id as forum_id
				FROM cms_forum_threads t, cms_forums f
				WHERE ";	
										
		if($look == 'anyword'|| $look == 'allwords'){
			//$looktype = 'любое слово';
			if ($look == 'anyword') { $clue = 'OR'; } else { $clue = 'AND'; }
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "t.title LIKE '%$w%'"; }
					else { $sql .= " $clue t.title LIKE '%$w%'"; }
				}
			}		
		}
		
		if($look == 'phrase'){
			//$looktype = 'фраза целиком';		
			$sql .= "t.title LIKE '%$query%'";			
		}
		
		$sql .= " AND t.forum_id = f.id ";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql);
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/forum/0/thread".$item['id'].".html";
				$place = $item['forum'];
				$placelink = "/forum/0/".$item['forum_id'];				
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('".session_id()."', '".$item['title']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		//SEARCH IN FORUM POSTS
		//BUILD SQL QUERY
		$sql = "SELECT p.*, t.title as thread, t.id as thread_id
				FROM cms_forum_posts p, cms_forum_threads t
				WHERE ";	
		
		$n=0;
										
		if($look == 'anyword'|| $look == 'allwords'){
			//$looktype = 'любое слово';
			if ($look == 'anyword') { $clue = 'OR'; } else { $clue = 'AND'; }
			foreach($words as $w){
				if(strlen($w)>1){
					$n++;
					if ($n==1) { $sql .= "p.content LIKE '%$w%'"; }
					else { $sql .= " $clue p.content LIKE '%$w%'"; }
				}
			}		
		}
		
		if($look == 'phrase'){
			//$looktype = 'фраза целиком';		
			$sql .= "p.content LIKE '%$query%'";
		}
		
		$sql .= " AND p.thread_id = t.id";

		//QUERY TO GET TOTAL RESULTS COUNT
		$result = $inDB->query($sql) or die('SEARCH ERROR'); 
		$found= $inDB->num_rows($result);
		
		if ($found){
			while($item = $inDB->fetch_assoc($result)){
				//build params
				$link = "/forum/0/thread".$item['thread_id'].".html";
				$place = 'Сообщение в теме форума';
				$placelink = $link;	
				//include item to search results
				if (!dbRowsCount('cms_search', "session_id='".session_id()."' AND link='$link'")){				
					$sql = "INSERT INTO cms_search (`session_id`, `title`, `link`, `place`, `placelink`)
							VALUES ('".session_id()."', '".$item['thread']."', '$link', '$place', '$placelink')";
					$inDB->query($sql);				
				}				
			}
		}
		
		return;
}


?>