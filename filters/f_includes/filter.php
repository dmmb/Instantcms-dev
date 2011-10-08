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

	function getLink($file){		
		trim($file);
        $file = str_replace('..', '', $file);
		$filefull = $_SERVER['DOCUMENT_ROOT'].'/includes/myphp/'.$file;
		
		if (file_exists($filefull)){
			ob_start();
				include $filefull;
			$link = ob_get_clean();
		} else {
			$link = '���� "/includes/myphp/'.$file.'" �� ������!';
		}	
		return $link;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_includes(&$text){

		//REPLACE FILE INCLUDES LINKS
 		$regex = '/{(����=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$file=@$args['����'];	
			if ($file){		
				$output = getLink($file);			
			} else { $output = ''; }
			$text = str_replace('{����='.$file.'}', $output, $text );	
		}

		return true;
	}
?>