<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function getDownLoadLink($file){
        $inCore = cmsCore::getInstance();

		trim($file);
		$filefull = $_SERVER['DOCUMENT_ROOT'].$file;	
		if (file_exists($filefull)){
			$downloaded = $inCore->fileDownloadCount($file);
		
			$filesize = round(filesize($filefull)/1024, 2);			
			$link = '<table border="0" cellpadding="2" cellspacing="0"><tr>';		
				$link .= '<td width="16"><img src="/images/icons/download.gif" border="0"</td>';
				$link .= '<td width=""><a href="/load/url='.$file.'" alt="Ñêà÷àòü">'.basename($file).'</a></td>';
				$link .= '<td width="">| '.$filesize.' Ká</td>';
				$link .= '<td width="">| Ñêà÷àí: '.$downloaded.' ðàç</td>';					
			$link .= '</tr></table>';
		} else {
			$link = 'Ôàéë "'.$filefull.'" íå íàéäåí!';
		}	
		return $link;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_filelink(&$text){

		//REPLACE FILE DOWNLOAD LINKS
 		$regex = '/{(ÑÊÀ×ÀÒÜ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$file=@$args['ÑÊÀ×ÀÒÜ'];	
			if ($file){		
				$output = getDownLoadLink($file);			
			} else { $output = ''; }
			$text = str_replace('{ÑÊÀ×ÀÒÜ='.$file.'}', $output, $text );	
		}

		return true;
	}
?>