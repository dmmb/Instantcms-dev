<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function applet_help(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = 'Помощь';
 	cpAddPathway('Помощь', 'index.php?view=help');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){

		echo '<p><b>В текущей версии системы помощь не доступна.</b></p>';
		echo '<p>Обратитесь на <a href="http://www.instantcms.ru">официальный сайт</a>.</p>';
		echo '<p><a href="javascript:window.history.go(-1);">Назад</a></p>';
		
    }
}

?>