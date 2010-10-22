<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function isAlreadyRated($target, $item_id, $ip){

    $inDB = cmsDatabase::getInstance();

    return $inDB->rows_count('cms_ratings', "target='$target' AND item_id = $item_id AND ip = '$ip'");

}

function submitRating($target, $item_id, $points){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    $ip         = $inUser->ip;

    if(!isAlreadyRated($target, $item_id, $ip)){

        $sql = "INSERT INTO cms_ratings (item_id, points, ip, target) VALUES ($item_id, $points, '$ip', '$target')";
        $inDB->query($sql) or die('Error rating submission');

    }

    return true;
}

function ratings(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    
    $back       = $inCore->getBackURL();

    $do         = $inCore->request('do', 'str', 'rate');

//========================================================================================================================//
//========================================================================================================================//
    if ($do=='rate'){

        $rating     = $inCore->request('rating', 'int', 0);
        $target     = $inCore->request('target', 'str', '');
        $item_id    = $inCore->request('item_id', 'int', 0);

        $ip         = $inUser->ip;

        if (!$rating || !$target || !$item_id){
            $inCore->redirect($back);
        }

        submitRating($target, $itemid, $rating);

        $inCore->redirect($back);
 
    }

//========================================================================================================================//
} //function
?>