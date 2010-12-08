<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function applet_help(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = 'Помощь';
 	cpAddPathway('Помощь', 'index.php?view=help');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
    $topic = $inCore->request('topic', 'str', '');

    $help_url['menu']       = 'http://www.instantcms.ru/wiki/doku.php/%D0%BC%D0%B5%D0%BD%D1%8E_%D1%81%D0%B0%D0%B9%D1%82%D0%B0';
    $help_url['modules']    = 'http://www.instantcms.ru/wiki/doku.php/%D0%BC%D0%BE%D0%B4%D1%83%D0%BB%D0%B8';
    $help_url['content']    = 'http://www.instantcms.ru/wiki/doku.php/%D0%BA%D0%BE%D0%BD%D1%82%D0%B5%D0%BD%D1%82';
    $help_url['cats']       = 'http://www.instantcms.ru/wiki/doku.php/%D0%BA%D0%BE%D0%BD%D1%82%D0%B5%D0%BD%D1%82';
    $help_url['components'] = 'http://www.instantcms.ru/wiki/doku.php/%D0%BA%D0%BE%D0%BC%D0%BF%D0%BE%D0%BD%D0%B5%D0%BD%D1%82%D1%8B';
    $help_url['users']      = 'http://www.instantcms.ru/wiki/doku.php/%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D0%B8';
    $help_url['config']     = 'http://www.instantcms.ru/wiki/doku.php/%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B0_%D1%81%D0%B0%D0%B9%D1%82%D0%B0';
    
    if (isset($help_url[$topic])){
        $inCore->redirect($help_url[$topic]);
    }

	$inCore->redirect('http://www.instantcms.ru/wiki');
}

?>