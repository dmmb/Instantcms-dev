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
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function subscribes(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();

    $do = $inCore->request('do', 'str', 'subscribe');

//========================================================================================================================//
//========================================================================================================================//
    if ($do=='subscribe'){

        $subscribe  = $inCore->request('subscribe', 'int', 0);
        $target     = $inCore->request('target', 'str', '');
        $target_id  = $inCore->request('target_id', 'int', 0);
        $user_id    = $inUser->id;

        if (!$target_id || !$target){
            $inCore->redirect($back);
        }

        if (isset($inUser->id)){
            cmsUser::subscribe($user_id,  $target, $target_id, $subscribe);
        }

        $inCore->redirect($back);
 
    }

//========================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>