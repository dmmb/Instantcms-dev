<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

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

} //function
?>