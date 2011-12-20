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

        if(mb_strstr($fileurl, '..')){ $inCore->halt(); }

        if (!$fileurl) { $inCore->halt($_LANG['FILE_NOT_FOUND']); }

        if (mb_strstr($fileurl, 'http:/')){
            if (!mb_strstr($fileurl, 'http://')){ $fileurl = str_replace('http:/', 'http://', $fileurl); }
        }

        $downloads = $inCore->fileDownloadCount($fileurl);

        if ($downloads == 0){
            $sql = "INSERT INTO cms_downloads (fileurl, hits) VALUES ('$fileurl', '1')";
            $inDB->query($sql);
        } else {
            $sql = "UPDATE cms_downloads SET hits = hits + 1 WHERE fileurl = '$fileurl'";
            $inDB->query($sql);
        }

        if (mb_strstr($fileurl, 'http:/')){
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

        if(mb_strstr($url, '..')){ $inCore->halt(); }

        if (!$url) { $inCore->halt(); }

        if (mb_strstr($url, 'http:/')){
            if (!mb_strstr($url, 'http://')){ $url = str_replace('http:/', 'http://', $url); }
        }
        if (mb_strstr($url, 'https:/')){
            if (!mb_strstr($url, 'https://')){ $url = str_replace('https:/', 'https://', $url); }
        }
        $inCore->redirect($url);

    }

//========================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>
