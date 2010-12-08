<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	session_start();
	
	if(isset($_POST['back']))
	{
		$back = $_POST['back'];
	} else { 
				if (isset($_SERVER['HTTP_REFERER']))
				{		
					$back = $_SERVER['HTTP_REFERER']; 
        		} else { $back = "/"; }
		   }		   

	if(isset($_POST['template'])){
		if ($_POST['template'] != '0'){
			$template = $_POST['template'];
			$_SESSION['template'] = $template;
		} else {
			unset($_SESSION['template']);
		}
	}	

	header('location:'.$back);
    
?>