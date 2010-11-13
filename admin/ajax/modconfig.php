<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

    session_start();

	define("VALID_CMS", 1);
	define("VALID_CMS_ADMIN", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');
	include(PATH.'/admin/includes/cp.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('user');

    $inUser = cmsUser::getInstance();

    if (!$inUser->update()) { $inCore->halt(); }
    if (!$inUser->is_admin) { $inCore->halt(); }

    $adminAccess = $inCore->checkAdminAccess();

    if (!$inCore->isAdminCan('admin/modules', $adminAccess)) {
        echo '�� �� ������ ������� � ���������� ��������';
        return;
    }

    $inCore->loadClass('page');
    $inCore->loadClass('formgen');
    $inCore->loadClass('plugin');

	$inConf = cmsConfig::getInstance();

    define('TEMPLATE_DIR', PATH.'/templates/'.$inConf->template.'/');
    define('DEFAULT_TEMPLATE_DIR', PATH.'/templates/_default_/');

    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();

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
    }

    if (file_exists($php_file)){
        $mode = 'php';
    }

    if($mod['user']){
        $mode = 'custom';
    }

    $inPage->includeTemplateFile('admin/modconfig.php');

?>
