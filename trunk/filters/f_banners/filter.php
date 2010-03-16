<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
	function f_banners(&$text){
        $inCore = cmsCore::getInstance();

		//REPLACE FILE DOWNLOAD LINKS
 		$regex = '/{(������=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$position=@$args['������'];	
			if ($position){		
				$output = $inCore->getBanner($position);
			} else { $output = ''; }
			$text = str_replace('{������='.$position.'}', $output, $text );	
		}

		return true;
	}
?>