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
				$link .= '<td width=""><a href="/load/url='.$file.'" alt="�������">'.basename($file).'</a></td>';
				$link .= '<td width="">| '.$filesize.' K�</td>';
				$link .= '<td width="">| ������: '.$downloaded.' ���</td>';					
			$link .= '</tr></table>';
		} else {
			$link = '���� "'.$filefull.'" �� ������!';
		}	
		return $link;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_filelink(&$text){

		//REPLACE FILE DOWNLOAD LINKS
 		$regex = '/{(�������=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			parse_str( $elm[0], $args );
			$file=@$args['�������'];	
			if ($file){		
				$output = getDownLoadLink($file);			
			} else { $output = ''; }
			$text = str_replace('{�������='.$file.'}', $output, $text );	
		}

		return true;
	}
?>