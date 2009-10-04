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

function files(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $do = $inCore->request('do', 'str', 'download');

//========================================================================================================================//
//========================================================================================================================//

    if ($do=='download'){

        $fileurl    = $inCore->request('fileurl', 'str', '');

        if (!$fileurl) { $inCore->halt('Файл не найден'); }

        if (strstr($fileurl, 'http:/')){
            if (!strstr($fileurl, 'http://')){ $fileurl = str_replace('http:/', 'http://', $fileurl); }
        }

        $downloads = $inCore->fileDownloadCount($fileurl);

        if ($downloads == 0){
            $sql = "INSERT INTO cms_downloads (fileurl, hits) VALUES ('$fileurl', '1')";
            $inDB->query($sql);
        } else {
            $sql = "UPDATE cms_downloads SET hits = hits + 1 WHERE fileurl = '$fileurl'";
            $inDB->query($sql);
        }

        if (strstr($fileurl, 'http:/')){
            $inCore->redirect($fileurl);
        }

        if (file_exists(PATH.$fileurl)){

            header('Content-Disposition: attachment; filename='.basename($fileurl) . "\n");
            header('Content-Type: application/x-force-download; name="'.$fileurl.'"' . "\n");
            header('Location:'.$fileurl);

        } else {
            $inCore->halt('Файл не найден');
        }
       
    }

//========================================================================================================================//
//========================================================================================================================//

    if ($do=='redirect'){

    	$url = str_replace('--q--', '?', $inCore->request('url', 'str', ''));
        
        if (!$url) { $inCore->halt(); }

        if (strstr($url, 'http:/')){
            if (!strstr($url, 'http://')){ $url = str_replace('http:/', 'http://', $url); }
        }
        
        $inCore->redirect($url);

    }

//========================================================================================================================//
} //function
?>
