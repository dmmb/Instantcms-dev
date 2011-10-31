<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written                                                  //
//                produced                                                    //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/


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