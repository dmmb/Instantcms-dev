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

function comments($target='', $target_id=0){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inCore->loadModel('comments');
    $model = new cms_model_comments();

    //Загружаем настройки компонента
	$cfg = $inCore->loadComponentConfig('comments');
    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
    if (!isset($cfg['regcap'])) { $cfg['regcap'] = 1; }

    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();
    
    $do     = $inCore->request('do', 'str', 'view');

//========================================================================================================================//
//========================================================================================================================//
    if ($do!='add' && $do!='delete'){

        $inPage->addHeadCSS('components/comments/css/styles.css');
        $inPage->addHeadJS('includes/jquery/autogrow/jquery.autogrow.js');
        $inPage->addHeadJS('components/comments/js/comments.js');

        $cm_message     = isset($_SESSION['cm_message']) ? $_SESSION['cm_message'] : '';
        $cm_error       = isset($_SESSION['cm_error']) ? $_SESSION['cm_error'] : '';

        unset($_SESSION['cm_message']);
        unset($_SESSION['cm_error']);

        if ($cfg['bbcode']){
            $inPage->addHeadJS('core/js/smiles.js');
        }

        $smarty = $inCore->initSmarty('components', 'com_comments_view.tpl');
        $smarty->assign('cm_message', $cm_message);
        $smarty->assign('cm_error', $cm_error);
        $smarty->assign('target', $target);
        $smarty->assign('target_id', $target_id);
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('is_user', $inUser->id);
        $smarty->assign('cfg', $cfg);
        $smarty->assign('add_comment_js', "addComment('".md5(session_id())."', '".$target."', '".$target_id."', 0)");
        $smarty->assign('user_subscribed', cmsUser::isSubscribed($inUser->id, $target, $target_id));
        $smarty->display('com_comments_view.tpl');

    }

//========================================================================================================================//
//========================================================================================================================//
	if ($do=='add'){

        //Удаляем сообщение об ошибках комментариев
        unset($_SESSION['cm_error']);

        $error          = '';

        $captha_code    = $inCore->request('code', 'str', '');
        $content        = $inCore->request('content', 'str', '');
        $guestname      = $inCore->request('guestname', 'str', '');
        $user_id        = $inCore->request('user_id', 'int', 0);

        $need_captcha   = (!$inUser->id || ($inUser->id && $cfg['regcap']==1));

        $target         = $inCore->request('target', 'str', '');
        $target_id      = $inCore->request('target_id', 'int', 0);

        //Проверяем ошибки
        if ($user_id != $inUser->id) { $error = 'Ошибка определения пользователя!'; }
        if (!$guestname && !$user_id) { $error = 'Вы не указали свое имя!'; }
        if (!$content) { $error = 'Введите текст комментария!'; }
        if ($need_captcha && !$inCore->checkCaptchaCode($_REQUEST['code'])) { $error = 'Неправильно указан код с картинки!'; }

		if ($target && $target_id){
			$t      = $inCore->getCommentLink($target, $target_id, false, true);
		} else {
            $error  = 'Ошибка добавления комментария!';
        }

		if(!$error){ //Если ошибок не было, действуем

            $parent_id = $inCore->request('parent_id', 'int', 0);

			//помещаем комментарий в базу
            $comment_id = $model->addComment(array(
                                                    'parent_id'=>$parent_id,
                                                    'user_id'=>$user_id,
                                                    'target'=>$target,
                                                    'target_id'=>$target_id,
                                                    'guestname'=>$guestname,
                                                    'content'=>$content,
                                                    'published'=>$cfg['publish']
                                                  ));

			//подписываем пользователя на обновления, если нужно
            if ($inUser->id && $inCore->inRequest('subscribe')){
                cmsUser::isSubscribed($inUser->id, $target, $target_id);
			}

			//рассылаем уведомления о новом комменте
			cmsUser::alertUsers($target, $target_id);

			//проверяем и выдаем награду если нужно
            cmsUser::checkAwards( $inUser->id );

			//смотрим настройки модерации
			if (!$cfg['publish']){
				$_SESSION['cm_message'] = 'Спасибо! Ваш комментарий будет добавлен после проверки администратором!';
			}

			//отправляем админу уведомление о комментарии на e-mail, если нужно
			if($cfg['email']) {
				$mailmsg = "Дата: ".date('d m Y (H:i)')."\n";
				$mailmsg .= "Новый комментарий: $t\n-------------------------------------------------------\n";
				$mailmsg .= strip_tags($content);
				$mailmsg = wordwrap($mailmsg, 70);
				$inCore->mailText($cfg['email'], 'InstantCMS: Новый комментарий!', $mailmsg);
			}

			//если коммент для блога или фотографии, отправляем автору уведомление на e-mail
			if($target=='userphoto' || $target=='blog' || $target=='photo'){
				switch($target){
					case 'userphoto':
						$table      = 'cms_user_photos';
						$subj       = 'вашей фотографии';
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/users/0/%author_id%/photo'.$target_id.'.html#c'.$comment_id;
					break;
					case 'photo':
						$table      = 'cms_photo_files';
						$subj       = 'вашей фотографии';
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/photos/0/photo'.$target_id.'.html#c'.$comment_id;
					break;
					case 'blog':
						$table      = 'cms_blog_posts';
						$subj       = 'вашей записи';
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/blogs/0/%blog_id%/post'.$target_id.'.html#c'.$comment_id;
					break;
				}

				//получаем ID и e-mail автора
                $author = $model->getTargetAuthor($table, $target_id);

				if ($author){
					$needmail   = $model->isAuthorNeedMail($author['id']);
					if ($needmail && $user_id != $author['id']){
							$inConf = cmsConfig::getInstance();

							$postdate   = date('d/m/Y H:i:s');
							$to_email   = $author['email'];
                            $from_nick  = $user_id ? dbGetField('cms_users', "id='{$user_id}'", 'nickname') : $guestname;

							$targetlink = str_replace('%author_id%', $author['id'], $targetlink);

							if (strstr($targetlink, '%blog_id%')){
								$blogid     = dbGetField('cms_blog_posts', 'id='.$target_id, 'blog_id');
								$targetlink = str_replace('%blog_id%', $blogid, $targetlink);
							}

							$letter_path    = PATH.'/includes/letters/newpostcomment.txt';
							$letter         = file_get_contents($letter_path);

							$letter = str_replace('{sitename}', $inConf->sitename, $letter);
							$letter = str_replace('{subj}', $subj, $letter);
							$letter = str_replace('{subjtitle}', $author['title'], $letter);
							$letter = str_replace('{targetlink}', $targetlink, $letter);
							$letter = str_replace('{date}', $postdate, $letter);
							$letter = str_replace('{from}', $from_nick, $letter);
							$inCore->mailText($to_email, 'Новый комментарий! - '.$inConf->sitename, $letter);
					}
				}
			}
		}

        if ($error) { $_SESSION['cm_error'] = $error; }

        $inCore->redirect($back.'#c');

	}

//========================================================================================================================//
//========================================================================================================================//
    if ($do == 'delete'){

        $id = $inCore->request('id', 'int', 0);

		if($id){

			$comment_user   = $model->getCommentAuthorId($id);

			$is_admin       = $inCore->userIsAdmin( $inUser->id );
			$is_my          = ($inUser->id == $comment_user);

			if($is_admin || ($is_my&&$inCore->isUserCan('comments/delete')) || $inCore->isUserCan('comments/moderate')){
				$model->deleteComment($id);
				$_SESSION['cm_message'] = 'Комментарий успешно удален';
			}
		}

        $inCore->redirectBack($back.'#c');

	}

//========================================================================================================================//
} //function
?>