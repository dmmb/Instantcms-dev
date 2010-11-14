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

function forms(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
	$inConf 	= cmsConfig::getInstance();

    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();

    $do     = $inCore->request('do', 'str', 'processform');
    global $_LANG;
//========================================================================================================================//
//========================================================================================================================//
    if ($do=='processform'){

        if (!$inCore->request('field')){  $inCore->redirect($back);  }

        $captcha_code   = $inCore->request('code', 'str', '');

		$mail_message   = '';
		$error          = ''; //Сообщение об ошибке
		$error_ids      = ''; //Строка со списком ID неправильно заполненных полей

		$form_id        = $inCore->request('form_id', 'int');

		if ($inCore->checkCaptchaCode( $captcha_code )){

			//Получаем форму из базы данных
			$sql        = "SELECT * FROM cms_forms WHERE id = $form_id LIMIT 1";
			$result     = $inDB->query($sql);

			//Формируем текст письма
			if($inDB->num_rows($result)){
				$form   = $inDB->fetch_assoc($result);

				if($form['sendto']=='mail'){
					 $mail_message .= $_LANG['FORM'].': ' . $form['title'];
					 $mail_message .=  "\n----------------------------------------------\n\n";
				} else {
					 $mail_message .= '[h3]'.$_LANG['FORM'].': ' . $form['title'] . '[/h3]';
					 $mail_message .=  "[h3]----------------------------------------------[/h3]";
				}

				$fields = $inCore->request('field', 'array');

				//Получаем данные полей из базы
				$sql            = "SELECT id, title, mustbe FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
				$result         = $inDB->query($sql);
				$items_count    = $inDB->num_rows($result);

				if(isset($_SESSION['form_last'.$form_id])) { unset($_SESSION['form_last'.$form_id]); }

				$_SESSION['form_last'.$form_id] = array();

				if ($items_count){
					while($field = $inDB->fetch_assoc($result)){
						$field['title'] = str_replace(':', '', $field['title']);
						if ($field['mustbe']==1 && (!isset($_REQUEST['field'][$field['id']]) || empty($_REQUEST['field'][$field['id']]))) {
							$error .= $_LANG['FIELD'].' "'.$field['title'].'" '.$_LANG['MUST_BE_FILLED'].'<br/>';
						} else {
							if($form['sendto']=='mail'){
								$mail_message .= $field['title'] . ":\n" . $fields[$field['id']] . "\n\n";
							} else {
								$mail_message .= '[h3]'.$field['title'] . ':[/h3]' . $fields[$field['id']];
							}
							$_SESSION['form_last'.$form_id][$field['id']] = $fields[$field['id']];
						}
					}
				}

			}
		}//check code
		else {
			$error .= $_LANG['ERR_CAPTCHA'].'<br/>';
		}

		if($error==''){
			$_SESSION['form_ok'.$form_id] = 1;
			unset ($_SESSION['form_last'.$form_id]);

			if ($form['sendto']=='mail'){
				$inCore->mailText($form['email'], $inConf->sitename.': '.$form['title'], $mail_message);
			} else {
				$mail_message = nl2br($mail_message);
				$mail_message = str_replace('<br /><br /><br /><br />', '<br/>', $mail_message);
				cmsUser::sendMessage(-2, $form['user_id'], $mail_message);
			}
		} else {
			$_SESSION['form_error'.$form_id] = $error;
		}

		$inCore->redirect($back);

    }

//========================================================================================================================//
} //function
?>