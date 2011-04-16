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

function files(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    global $_LANG;
    $do = $inCore->request('do', 'str', 'download');

//========================================================================================================================//
//========================================================================================================================//

    if ($do=='download'){

        $fileurl    = $inCore->request('fileurl', 'str', '');

        if(strstr($fileurl, '..')){ $inCore->halt(); }

        if (!$fileurl) { $inCore->halt($_LANG['FILE_NOT_FOUND']); }

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
            $inCore->halt($_LANG['FILE_NOT_FOUND']);
        }
       
    }

//========================================================================================================================//
//========================================================================================================================//

    if ($do=='redirect'){

    	$url = str_replace('--q--', '?', $inCore->request('url', 'str', ''));

        if(strstr($url, '..')){ $inCore->halt(); }

        if (!$url) { $inCore->halt(); }

        if (strstr($url, 'http:/')){
            if (!strstr($url, 'http://')){ $url = str_replace('http:/', 'http://', $url); }
        }
        
        $inCore->redirect($url);

    }

//========================================================================================================================//
} //function
?>
