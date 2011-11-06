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

	if(isset($_POST['back'])){
		$back = $_POST['back'];
	} else { 
		if (isset($_SERVER['HTTP_REFERER'])){		
			$back = $_SERVER['HTTP_REFERER']; 
		} else { $back = "/"; }
	}		   

    $template = $inCore->request('template', 'str', '');
	$template = preg_replace ('/[^a-zA-Z_\-]/i', '', $template);

	if ($template){
		$_SESSION['template'] = $template;
	} else {
		unset($_SESSION['template']);
	}

	$inCore->redirect($back);

?>