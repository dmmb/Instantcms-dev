<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_search(){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$menuid = $inCore->menuId();
	
		echo '<form action="/search" method="GET">';
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