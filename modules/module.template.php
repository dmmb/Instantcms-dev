<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_/* MODULE NAME */($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
	
		$sql = "SELECT * 
				FROM 
				WHERE 
				ORDER BY ordering ASC";
		$result = $inDB->query($sql) 
		
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){
			
			}
		}

		return true;	
	}
?>