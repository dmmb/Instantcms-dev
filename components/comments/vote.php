<?php

	session_start();

	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/includes/config.inc.php');
	include(PATH.'/includes/database.inc.php');
	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();
    
    $inCore->loadClass('user');

    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inUser->update();

    $comment_id     = $inCore->request('comment_id', 'int');
    $vote           = $inCore->request('vote', 'int');
    $user_id        = $inUser->id;

    $inCore->loadLib('karma');

    if ($user_id && $comment_id && abs($vote)==1){        
        cmsSubmitKarma('comment', $comment_id, $vote);    	        
    }

    $karma = cmsKarma('comment', $comment_id);

    if ($karma['points']>0){
        $karma['points'] = '<span class="cmm_good">+'.$karma['points'].'</span>';
    } elseif ($karma['points']<0){
        $karma['points'] = '<span class="cmm_bad">'.$karma['points'].'</span>';
    }

    echo $karma['points'];
	
?>