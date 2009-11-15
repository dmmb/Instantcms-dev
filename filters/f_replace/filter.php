<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function insertForm($form_title, $title){
        $inDB = cmsDatabase::getInstance();
        $inPage = cmsPage::getInstance();
        
		trim($form_title);
	
		$sql = "SELECT id FROM cms_forms WHERE title LIKE '$form_title' LIMIT 1";
		$result = $inDB->query($sql) ;
		
		if(mysql_num_rows($result)){
			$f = $inDB->fetch_assoc($result);
			$link = $inPage->buildForm($f['id'], false, $title);
		} else { $link = ''; }	
	
		return $link;
	}

	function PriceLink($category_title){
        $inDB = cmsDatabase::getInstance();
		trim($category_title);
	
		$sql = "SELECT * FROM cms_price_cats WHERE title LIKE '$category_title' LIMIT 1";
		$result = $inDB->query($sql) ;
		
		if($inDB->num_rows($result)){
			$cat = $inDB->fetch_assoc($result);
			$link = '<a href="/index.php?view=price&id='.$cat['id'].'" title="'.$category_title.'">'.$category_title.'</a>';
		} else { $link = ''; }	
	
		return $link;
	}

	function PhotoLink($photo_title){
        $inDB = cmsDatabase::getInstance();
		trim($photo_title);
	
		$sql = "SELECT * FROM cms_photo_files WHERE title LIKE '$photo_title' LIMIT 1";
		$result = $inDB->query($sql) ;
		
		if($inDB->num_rows($result)){
			$photo = $inDB->fetch_assoc($result);
			$link = '<a href="/index.php?view=photos&do=viewphoto&id='.$photo['id'].'" title="'.$photo_title.'">'.$photo_title.'</a>';
		} else { $link = ''; }	
	
		return $link;
	}
	
	function AlbumLink($album_title){
        $inDB = cmsDatabase::getInstance();
		trim($album_title);
	
		$sql = "SELECT * FROM cms_photo_albums WHERE title LIKE '$album_title' LIMIT 1";
		$result = $inDB->query($sql) ;
		
		if($inDB->num_rows($result)){
			$album = $inDB->fetch_assoc($result);
			$link = '<a href="/index.php?view=photos&id='.$album['id'].'" title="'.$album_title.'">'.$album_title.'</a>';
		} else { $link = ''; }	
	
		return $link;
	}
	
	function ContentLink($content_title){
        $inDB = cmsDatabase::getInstance();
		trim($content_title);
	
		$sql = "SELECT * FROM cms_content WHERE title LIKE '$content_title' LIMIT 1";
		$result = $inDB->query($sql) ;
		
		if($inDB->num_rows($result)){
			$content = $inDB->fetch_assoc($result);
			$link = '<a href="/index.php?view=content&do=read&id='.$content['id'].'" title="'.$content_title.'">'.$content_title.'</a>';
		} else { $link = ''; }	
	
		return $link;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_replace(&$text){

        $inDB = cmsDatabase::getInstance();

		//REPLACE PRICE CATS LINKS
 		$regex = '/{(ÏÐÀÉÑ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$category=@$args['ÏÐÀÉÑ'];	
			if ($category){		
				$output = PriceLink($category);			
			} else { $output = ''; }
			$text = str_replace('{ÏÐÀÉÑ='.$category.'}', $output, $text );	
		}
		
		//REPLACE PHOTO LINK
 		$regex = '/{(ÔÎÒÎ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$photo=@$args['ÔÎÒÎ'];		
			if ($photo){		
				$output = PhotoLink($photo);			
			} else { $output = ''; }
			$text = str_replace('{ÔÎÒÎ='.$photo.'}', $output, $text );			
		}		
		
		//REPLACE PHOTO ALBUM LINK
 		$regex = '/{(ÀËÜÁÎÌ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$album=@$args['ÀËÜÁÎÌ'];		
			if ($album){		
				$output = AlbumLink($album);			
			} else { $output = ''; }
			$text = str_replace('{ÀËÜÁÎÌ='.$album.'}', $output, $text );
		}	
		
		//REPLACE CONTENT ITEM LINK
 		$regex = '/{(ÌÀÒÅÐÈÀË=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$content=@$args['ÌÀÒÅÐÈÀË'];		
			if ($content){		
				$output = ContentLink($content);			
			} else { $output = ''; }
			$text = str_replace('{ÌÀÒÅÐÈÀË='.$content.'}', $output, $text );
		}	
		
		//INSERT USER FORM _WITH_ TITLE
 		$regex = '/{(ÔÎÐÌÀ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$content=@$args['ÔÎÐÌÀ'];		
			if ($content){		
				$output = insertForm($content, true);			
			} else { $output = ''; }
			$text = str_replace('{ÔÎÐÌÀ='.$content.'}', $output, $text );	
		}		

		//INSERT USER FORM _WITHOUT_ TITLE
 		$regex = '/{(ÁËÀÍÊ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$content=@$args['ÁËÀÍÊ'];		
			if ($content){		
				$output = insertForm($content, false);			
			} else { $output = ''; }
			$text = str_replace('{ÁËÀÍÊ='.$content.'}', $output, $text );	
		}		
						
		//REPLACE BY USER RULES
		$sql = "SELECT * FROM cms_filter_rules";
		$result = $inDB->query($sql) ;
		if (mysql_num_rows($result)){
			while($rule = mysql_fetch_assoc($result)){
				$regex = '/{('.$rule['find'].')\s*(.*?)}/i';
				if($rule['published']){									
					$text = preg_replace( $regex, $rule['replace'], $text );														
				} else {
					$text = preg_replace( $regex, '', $text );																		
				}
			}		
		}
				
		return true;
	}
?>