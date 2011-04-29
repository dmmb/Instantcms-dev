<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

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