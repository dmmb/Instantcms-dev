<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
	function f_contents(&$text){

		//REPLACE PAGE TITLES
 		$regex = '/{(ÑÒÐÀÍÈÖÀ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		$GLOBALS['pt'] = array();
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$title=@$args['ÑÒÐÀÍÈÖÀ'];	
			if ($title){		
				$GLOBALS['pt'][] = $title;
			}
			$text = str_replace('{ÑÒÐÀÍÈÖÀ='.$title.'}', '', $text );	
		}
				
		return true;
	}
?>