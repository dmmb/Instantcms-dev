<?php

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');
    $inCore->loadClass('actions');

    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $inUser->update();

    if (!$inUser->id) { return; }

    $status = $inCore->request('status', 'str', '');
    if (strlen($status)>140){ $status = substr($status, 0, 140); }

    $status = @iconv('UTF-8', 'CP1251', $status);

    $sql = "UPDATE cms_users
            SET status = '{$status}', status_date = NOW()
            WHERE id = {$inUser->id}
            LIMIT 1";

    $inDB->query($sql);

    //������������ �������
    cmsActions::log('set_status', array(
        'object' => '',
        'object_url' => '',
        'object_id' => 0,
        'target' => '',
        'target_url' => '',
        'target_id' => 0,
        'description' => $status
    ));

    return;
    
?>
