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

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

    session_start();

	define("VALID_CMS", 1);
	define("VALID_CMS_ADMIN", 1);
	define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');
	include(PATH.'/admin/includes/cp.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('user');

    $inUser = cmsUser::getInstance();

    if (!$inUser->update()) { $inCore->halt(); }
    if (!$inUser->is_admin) { $inCore->halt(); }

    $adminAccess = $inCore->checkAdminAccess();

    if (!$inCore->isAdminCan('admin/modules', $adminAccess)) {
        echo '¬ы не имеете доступа к управлению модул€ми';
        return;
    }

    $inCore->loadClass('page');
    $inCore->loadClass('formgen');
    $inCore->loadClass('plugin');

	$inConf = cmsConfig::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();

    $module_id = $inCore->request('id', 'int');

    if (!$module_id) { $inCore->halt(); }

    $mod = $inDB->get_fields('cms_modules', "id={$module_id}", '*');

    if (!$mod) { $inCore->halt(); }

    $xml_file = PATH.'/admin/modules/'.$mod['content'].'/backend.xml';
    $php_file = PATH.'/admin/modules/'.$mod['content'].'/backend.php';

    $mode       = 'none';
    $cfg_form   = '';

    if (file_exists($xml_file)){
        $cfg = $inCore->loadModuleConfig($module_id);
        $formGen = new cmsFormGen($xml_file, $cfg);
        $cfg_form = $formGen->getHTML();
        $mode = 'xml';
    } elseif (file_exists($php_file)){
        $mode = 'php';
    } elseif ($mod['user']){
        $mode = 'custom';
    }

    $inPage->includeTemplateFile('admin/modconfig.php');

?>
