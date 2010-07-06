<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function getLink($file){		
		trim($file);
		$filefull = $_SERVER['DOCUMENT_ROOT'].'/includes/myphp/'.$file;
		
		if (file_exists($filefull)){
			ob_start();
				include $filefull;
			$link = ob_get_clean();
		} else {
			$link = 'Ôàéë "/includes/myphp/'.$file.'" íå íàéäåí!';
		}	
		return $link;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_includes(&$text){

		//REPLACE FILE INCLUDES LINKS
 		$regex = '/{(ÔÀÉË=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$file=@$args['ÔÀÉË'];	
			if ($file){		
				$output = getLink($file);			
			} else { $output = ''; }
			$text = str_replace('{ÔÀÉË='.$file.'}', $output, $text );	
		}

		return true;
	}
?>