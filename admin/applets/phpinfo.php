<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function applet_phpinfo(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = 'Информация PHP';
 	
	cpAddPathway('Настройки сайта', 'index.php?view=config');	
	cpAddPathway('Информация PHP', 'index.php?view=phpinfo');
			
?>
<div>

	<h3>Информация PHP</h3>

    <iframe src="/admin/includes/phpinfo.php" style="border:none;width:100%;height:600px" />

</div>
<?php } ?>
