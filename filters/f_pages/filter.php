<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function f_pages(&$text){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();

        $id     = $inCore->request('id', 'int');

        if (!$id){ 
        
            $seolink = $inCore->request('seolink', 'str', '');
            $id      = $inDB->get_field('cms_content', "seolink='{$seolink}'", 'id');

            if (!$id) return;

        }

        $title  = $inDB->get_field('cms_content', 'id='.$id, 'title');

		if ( strpos( $text, 'pagebreak' ) === false ) {
			return true;
		}

		$regex = '/{(pagebreak)\s*(.*?)}/i';

		$matches = array();
		preg_match_all( $regex, $text, $matches, PREG_SET_ORDER );
	
		$pages = preg_split( $regex, $text );

		$n = count( $pages );
		
		if ($n<=1){
			return true;
		} else {		
					if (isset($_REQUEST['page'])){	$page = intval($_REQUEST['page']); } else {	$page = 1;	}
					$text = $pages[$page-1];

					$text .= $inCore->getPageBar($id, $title, $n, $page);
					return true;
			   }
	}
?>