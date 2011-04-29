<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function actions(){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    $inCore->loadModel('actions');
    $model = new cms_model_actions();

    $inCore->loadClass('actions');

    global $_LANG;

    $do = $inCore->request('do', 'str', 'click');

//======================================================================================================================//

    if ($do=='delete'){

        $id = $inCore->request('id', 'int', 0);

        if (!$id) { cmsCore::error404(); }

        if (!$inUser->is_admin) { cmsCore::error404(); }
        
        $model->deleteAction($id);
        $inCore->redirectBack();

    }

//======================================================================================================================//

} //function
?>