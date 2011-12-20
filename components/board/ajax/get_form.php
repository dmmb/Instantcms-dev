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

    setlocale(LC_ALL, 'ru_RU.UTF-8');
    header('Content-Type: text/html; charset=utf-8');

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

	$inCore->loadClass('config');
	$inCore->loadClass('db');
	$inCore->loadClass('user');
	$inCore->loadClass('page');

    $inCore->loadModel('board');
    $model = new cms_model_board();

	$cat_id   = $inCore->request('value', 'int', '');	

	$cat = $model->getCategory($cat_id);
	if(!$cat) { echo 1; exit; }

	$forms = $model->getFormDataEdit($cat['form_id']);
	if(!$forms) { echo 1; exit; }

	$html = '';

	foreach($forms as $form){
		$html .= '<tr class="cat_form">
			<td valign="top">
				<span>'.$form['title'].':</span>
			</td>
			<td valign="top">
				'.$form['value'].'
			</td>
		</tr>';
	}

	echo $html;

?>