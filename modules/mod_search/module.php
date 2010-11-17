<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_search(){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
	
		echo '<form action="/index.php" method="GET">';
            echo '<input type="hidden" name="view" value="search" />';
			echo '<input type="text" 
						 name="query"
						 class="search_field" 
						 value="поиск..." 
						 onClick="this.value=\'\'" 
						 onFocusOut="if(this.value==\'\'){this.value=\'поиск...\';}"/>';
			
		echo '</form>';
	
		return true;	
	}
?>