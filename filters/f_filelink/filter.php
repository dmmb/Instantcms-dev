<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	function getDownLoadLink($file){
        $inCore = cmsCore::getInstance();

		trim($file);
		$file = preg_replace('/\.+\//', '', $file);
		$filefull = PATH.$file;	
		if (file_exists($filefull)){
			$downloaded = $inCore->fileDownloadCount($file);
		
			$filesize = round(filesize($filefull)/1024, 2);			
			$link = '<table border="0" cellpadding="2" cellspacing="0"><tr>';		
				$link .= '<td width="16"><img src="/images/icons/download.gif" border="0"</td>';
				$link .= '<td width=""><a href="/load/url='.$file.'" alt="Скачать">'.basename($file).'</a></td>';
				$link .= '<td width="">| '.$filesize.' Kб</td>';
				$link .= '<td width="">| Скачан: '.$downloaded.' раз</td>';					
			$link .= '</tr></table>';
		} else {
			$link = 'Файл "'.$filefull.'" не найден!';
		}	
		return $link;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function f_filelink(&$text){

		//REPLACE FILE DOWNLOAD LINKS
 		$regex = '/{(СКАЧАТЬ=)\s*(.*?)}/i';
		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );		
		foreach ($matches as $elm) {	
			$elm[0] = str_replace('{', '', $elm[0]);
			$elm[0] = str_replace('}', '', $elm[0]);			
			mb_parse_str( $elm[0], $args );
			$file=@$args['СКАЧАТЬ'];	
			if ($file){		
				$output = getDownLoadLink($file);			
			} else { $output = ''; }
			$text = str_replace('{СКАЧАТЬ='.$file.'}', $output, $text );	
		}

		return true;
	}
?>