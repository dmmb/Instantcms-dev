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

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	if (!isset($_REQUEST['value'])) { die(2); }

	setlocale(LC_ALL, 'ru_RU.CP1251');

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

	$inCore->loadClass('config');
	$inCore->loadClass('db');
	$inCore->loadClass('user');
	$inDB = cmsDatabase::getInstance();

    $inCore->loadModel('board');
    $model = new cms_model_board();

	$cat_id   = $inCore->request('value', 'int', '');	
	$selected = $inCore->request('obtype', 'str', '');
	$selected = @iconv('UTF-8', 'CP1251', $selected);	

	$cat = $model->getCategory($cat_id);
	if(!$cat) { die(); }

	echo $model->getTypesOptions($cat['obtypes'], $selected);

?>