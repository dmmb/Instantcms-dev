<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_template($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$menuid = $inCore->menuId();
		$cfg = $inCore->loadModuleConfig($module_id);
	
		if (isset($_SESSION['template'])) { $template = $_SESSION['template']; } else { $template = ''; }
		
		echo '<form name="templform" action="/modules/mod_template/set.php" method="post">';
			echo '<select name="template" id="template" style="width:100%">
					<option value="0">По-умолчанию</option>';
					echo $inCore->templatesList($template);
			echo '</select><br/>';
			echo '<input style="margin-top:5px" type="submit" value="Выбрать"/>';			
		echo '</form>'; 
			
		return true;
	
}
?>