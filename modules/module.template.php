<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_/* MODULE NAME */($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
	
		$sql = "SELECT * 
				FROM 
				WHERE 
				ORDER BY ordering ASC";
		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){
			while ($item=$inDB->fetch_assoc($result)){
			
			}
		}

		return true;	
	}
?>