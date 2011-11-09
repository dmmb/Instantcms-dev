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

function forms(){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$inConf = cmsConfig::getInstance();

    $do     = $inCore->request('do', 'str', 'processform');

    global $_LANG;
//========================================================================================================================//
//========================================================================================================================//
    if ($do=='processform'){

		$errors = false;

		$fields = $inCore->request('field', 'array_str');
		if(!$fields) { cmsCore::addSessionMessage($_LANG['FORM_ERROR'], 'error'); $inCore->redirectBack(); }

        $captcha_code = $inCore->request('code', 'str', '');
		if(!$inCore->checkCaptchaCode($captcha_code)) { cmsCore::addSessionMessage($_LANG['ERR_CAPTCHA'], 'error'); $errors = true; }

		$mail_message = '';
		$form_id      = $inCore->request('form_id', 'int');

		//Получаем форму из базы данных
		$sql    = "SELECT * FROM cms_forms WHERE id = '$form_id' LIMIT 1";
		$result = $inDB->query($sql);

		//Формируем текст письма
		if($inDB->num_rows($result)){
			$form   = $inDB->fetch_assoc($result);

			if($form['sendto']=='mail'){
				 $mail_message .= $_LANG['FORM'].': ' . $form['title'];
				 $mail_message .=  "\n----------------------------------------------\n\n";
			} else {
				 $mail_message .= '<h3>'.$_LANG['FORM'].': ' . $form['title'] . '</h3>';
				 $mail_message .=  "<h3>----------------------------------------------</h3>";
			}

			//Получаем данные полей из базы
			$sql         = "SELECT id, title, mustbe FROM cms_form_fields WHERE form_id = '$form_id' ORDER BY ordering ASC";
			$result      = $inDB->query($sql);
			$items_count = $inDB->num_rows($result);

			if ($items_count){
				while($field = $inDB->fetch_assoc($result)){
					$field['title'] = str_replace(':', '', $field['title']);
					if ($field['mustbe']==1 && (!isset($_REQUEST['field'][$field['id']]) || empty($_REQUEST['field'][$field['id']]))) {
						cmsCore::addSessionMessage($_LANG['FIELD'].' "'.$field['title'].'" '.$_LANG['MUST_BE_FILLED'], 'error');$errors = true;
					} else {
						cmsUser::sessionPut('form_last_'.$form_id.'_'.$field['id'], stripslashes(htmlspecialchars($fields[$field['id']])));
						if($form['sendto']=='mail'){
							$mail_message .= $field['title'] . ":\n" . $fields[$field['id']] . "\n\n";
						} else {
							$mail_message .= '<h3>'.$field['title'] . ':</h3>' . $fields[$field['id']];
						}
					}
				}
			}

		}

		if(!$errors){
			if ($form['sendto']=='mail'){
				$inCore->mailText($form['email'], $inConf->sitename.': '.$form['title'], $mail_message);
			} else {
				$mail_message = nl2br($mail_message);
				$mail_message = str_replace('<br /><br /><br /><br />', '<br/>', $mail_message);
				cmsUser::sendMessage(-2, $form['user_id'], $mail_message);
			}
			unset($_SESSION['icms']);
			cmsCore::addSessionMessage($_LANG['FORM_IS_SEND'], 'info');
		}

		$inCore->redirectBack();

    }

//========================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>