<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

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
