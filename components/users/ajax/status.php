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

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');
    $inCore->loadClass('actions');

    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $inUser->update();

    if (!$inUser->id) { return; }

    $status     = $inCore->request('status', 'str', '');
    $user_id    = $inCore->request('id', 'int', 0);

    if (!$user_id) { $user_id = $inUser->id; }

    if ($user_id != $inUser->id && !$inUser->is_admin) { return; }

    $status = @iconv('UTF-8', 'CP1251', $status);
    if (strlen($status)>140){ $status = substr($status, 0, 140); }

    $sql = "UPDATE cms_users
            SET status = '{$status}', status_date = NOW()
            WHERE id = '{$user_id}'
            LIMIT 1";

    $inDB->query($sql);

    //регистрируем событие
    if ($status){
        cmsActions::log('set_status', array(
            'object' => '',
            'object_url' => '',
            'object_id' => 0,
            'target' => '',
            'target_url' => '',
            'target_id' => 0,
            'description' => $status,
            'user_id' => $user_id
        ));
    }
    
    return;
    
?>
