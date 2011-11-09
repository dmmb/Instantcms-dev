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

function banners(){

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inCore->loadModel('banners');
    $model = new cms_model_banners();
    global $_LANG;
    $do = $inCore->request('do', 'str', 'click');

//======================================================================================================================//

    if ($do=='click'){

        $banner_id = $inCore->request('id', 'int', 0);

        if (!$banner_id) { $inCore->halt(); }

        $banner     = $model->getBanner($banner_id);

        if ($banner){
            $model->clickBanner($banner_id);
            $inCore->redirect($banner['link']);
        } else {
            $inCore->halt('BANNER NOT FOUND');
        }

    }

//======================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>