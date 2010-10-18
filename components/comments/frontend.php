<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
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

    //��������� ��������� ����������
	$cfg = $inCore->loadComponentConfig('comments');
    if (!isset($cfg['bbcode'])) { $cfg['bbcode'] = 1; }
    if (!isset($cfg['regcap'])) { $cfg['regcap'] = 1; }
	if (!isset($cfg['min_karma'])) { $cfg['min_karma'] = 0; }
	if (!isset($cfg['min_karma_add'])) { $cfg['min_karma_add'] = 0; }
	if (!isset($cfg['min_karma_show'])) { $cfg['min_karma_show'] = 0; }
	if(!isset($cfg['j_code'])) { $cfg['j_code']=1;	}
	if(!isset($cfg['cmm_ajax'])) { $cfg['cmm_ajax']=0;	}

    //���������� ����� ��� ��������� �����
    $back   = $inCore->getBackURL();
    
    $do     = $inCore->request('do', 'str', 'view');
    global $_LANG;

    $inCore->loadLanguage('components/comments');

//========================================================================================================================//
//========================================================================================================================//
    if ($do == 'view'){
		
		// ��������� ������� �������
		$inCore->includeFile('components/users/includes/usercore.php');

		//  ��������� � ����������
		$inPage->setTitle($_LANG['COMMENTS']);
		$inPage->addPathway($_LANG['COMMENTS']);
		$inPage->backButton(false);
		if ($cfg['bbcode'] && $cfg['j_code']) {
			$inPage->addHeadCSS('includes/jquery/syntax/styles/shCore.css');
			$inPage->addHeadCSS('includes/jquery/syntax/styles/shThemeDefault.css');
			$inPage->addHeadJS('includes/jquery/syntax/src/shCore.js');
			$inPage->addHeadJS('includes/jquery/syntax/scripts/shBrushPhp.js');
		}
		// ���������
		$perpage = $cfg['perpage'] ? $cfg['perpage'] : 20;
		$page    = $inCore->request('page', 'int', 1);

		//��������� �����������
		$comments = array();
		// ������� ����� ����� ���������� ������������
		$total = $inDB->rows_count('cms_comments', 'published=1');
		// ���� ���� �����������, �������� � ������������
		if ($total){

			$comments_list  = $model->getCommentsAll($page, $perpage);
			
			foreach($comments_list as $comment){
				$next = sizeof($comments);
				$comments[$next] = $comment;
				if ($comments[$next]['guestname']) {
					$comments[$next]['author']      = $comments[$next]['guestname'];
					$comments[$next]['is_profile']  =false;
				} else {
					$comments[$next]['author']['nickname'] = $comments[$next]['nickname'];
					$comments[$next]['author']['login'] = $comments[$next]['login'];
					$comments[$next]['is_profile'] 	= true;
					$comments[$next]['user_image'] 	= usrImageNOdb($comments[$next]['user_id'], 'small', $comments[$next]['imageurl'], $comments[$next]['is_deleted']);
				}
				$comments[$next]['show'] 	   	= ((!$cfg['min_karma'] || $comments[$next]['votes']>=$cfg['min_karma_show']) || $inCore->userIsAdmin($comments[$next]['user_id']));
				if ($comments[$next]['votes']>0){
					$comments[$next]['votes'] = '<span class="cmm_good">+'.$comments[$next]['votes'].'</span>';
				} elseif ($comments[$next]['votes']<0){
					$comments[$next]['votes'] = '<span class="cmm_bad">'.$comments[$next]['votes'].'</span>';
				}
				if ($cfg['bbcode']){
					$comments[$next]['content'] = $comments[$next]['content'];
				} elseif ($cfg['smilies']) {
					$comments[$next]['content'] = nl2br(strip_tags($comments[$next]['content']));
				} else {
					$comments[$next]['content'] = nl2br(strip_tags($comments[$next]['content']));
				}
			}
		}
		
		// ������ � ������
		$smarty = $inCore->initSmarty('components', 'com_comments_list_all.tpl');

		$smarty->assign('comments_count', $total);
		$smarty->assign('comments', $comments);
		$smarty->assign('pagebar', cmsPage::getPagebar($total, $page, $perpage, '/comments/page-%page%'));
		$smarty->assign('is_user', $inUser->id);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('url', $_SERVER['REQUEST_URI']);

		$smarty->display('com_comments_list_all.tpl');

    }

//========================================================================================================================//
//========================================================================================================================//
    if ($do!='add' && $do!='delete' && $do!='view'){

        $inPage->addHeadJS('includes/jquery/autogrow/jquery.autogrow.js');
        $inPage->addHeadJS('components/comments/js/comments.js');
		if ($cfg['bbcode'] && $cfg['j_code']) {
			$inPage->addHeadCSS('includes/jquery/syntax/styles/shCore.css');
			$inPage->addHeadCSS('includes/jquery/syntax/styles/shThemeDefault.css');
			$inPage->addHeadJS('includes/jquery/syntax/src/shCore.js');
			$inPage->addHeadJS('includes/jquery/syntax/scripts/shBrushPhp.js');
		}
        if ($cfg['bbcode']){
            $inPage->addHeadJS('core/js/smiles.js');
        }

        $cm_message     = isset($_SESSION['cm_message']) ? $_SESSION['cm_message'] : '';
        $cm_error       = isset($_SESSION['cm_error']) ? $_SESSION['cm_error'] : '';

        unset($_SESSION['cm_message']);
        unset($_SESSION['cm_error']);

		$comments_count = $model->getCommentsCount($target, $target_id);
		
		if ($comments_count && !$cfg['cmm_ajax']){
			
			//activate profiles support
			$inCore->includeFile('components/users/includes/usercore.php');	
			
			//LIST COMMENTS
	
			$is_admin           = $inCore->userIsAdmin($inUser->id);
			$user_can_delete    = $inCore->isUserCan('comments/delete');
			$user_can_moderate  = $inCore->isUserCan('comments/moderate');
	
			$comments = array();
			$tree     = array();			
			
			$comments_list  = $model->getComments($target, $target_id);
			
			foreach($comments_list as $comment){
				$next = sizeof($comments);
				$comments[$next] = $comment;
				$comments[$next]['level'] = 0;        
				if ($comments[$next]['guestname']) {
					$comments[$next]['author']      = $comments[$next]['guestname'];
					$comments[$next]['is_profile']  =false;
				} else {
					$comments[$next]['author']['nickname'] = $comments[$next]['nickname'];
					$comments[$next]['author']['login'] = $comments[$next]['login'];
					$comments[$next]['is_profile'] 	= true;
					$comments[$next]['user_image'] 	= usrImageNOdb($comments[$next]['user_id'], 'small', $comments[$next]['imageurl'], $comments[$next]['is_deleted']);
				}
				$comments[$next]['show'] 	   	= ((!$cfg['min_karma'] || $comments[$next]['votes']>=$cfg['min_karma_show']) || $inCore->userIsAdmin($comments[$next]['user_id']));
				if ($comments[$next]['votes']>0){
					$comments[$next]['votes'] = '<span class="cmm_good">+'.$comments[$next]['votes'].'</span>';
				} elseif ($comments[$next]['votes']<0){
					$comments[$next]['votes'] = '<span class="cmm_bad">'.$comments[$next]['votes'].'</span>';
				}
        if ($cfg['bbcode']){
					$comments[$next]['content'] = $comments[$next]['content'];
				} elseif ($cfg['smilies']) {
					$comments[$next]['content'] = nl2br(strip_tags($comments[$next]['content']));
				} else {
					$comments[$next]['content'] = nl2br(strip_tags($comments[$next]['content']));
				}
				$comments[$next]['is_my'] = ($inUser->id==$comments[$next]['user_id']);
				if ($inUser->id){
					$comments[$next]['is_voted'] = ($comments[$next]['is_my'] || $inDB->rows_count('cms_ratings', 'item_id='.$comments[$next]['id'].' AND target=\'comment\' AND user_id='.$inUser->id, 1));
				}
        }

			$model->buildTree(0, 0, $comments, $tree);
			
			ob_start();
		
			$smarty = $inCore->initSmarty('components', 'com_comments_list.tpl');
			$smarty->assign('comments_count', $comments_list );
			$smarty->assign('comments', $tree);
			$smarty->assign('user_can_moderate', $user_can_moderate);
			$smarty->assign('user_can_delete', $user_can_delete);
			$smarty->assign('is_admin', $is_admin);
			$smarty->assign('is_user', $inUser->id);
			$smarty->assign('cfg', $cfg);
			$smarty->assign('target', $target);
			$smarty->assign('target_id', $target_id);
			$smarty->assign('url', $_SERVER['REQUEST_URI']);
		
			$smarty->display('com_comments_list.tpl');
		
			$html = ob_get_clean();
		}

        $smarty = $inCore->initSmarty('components', 'com_comments_view.tpl');
        $smarty->assign('cm_message', $cm_message);
        $smarty->assign('cm_error', $cm_error);
		$smarty->assign('comments_count', $comments_count);
        $smarty->assign('target', $target);
        $smarty->assign('target_id', $target_id);
        $smarty->assign('is_admin', $is_admin);
        $smarty->assign('is_user', $inUser->id);
        $smarty->assign('cfg', $cfg);
		$smarty->assign('html', $html);
        $smarty->assign('add_comment_js', "addComment('".md5(session_id())."', '".$target."', '".$target_id."', 0)");
        $smarty->assign('user_subscribed', cmsUser::isSubscribed($inUser->id, $target, $target_id));
        $smarty->display('com_comments_view.tpl');

    }

//========================================================================================================================//
//========================================================================================================================//
	if ($do=='add'){

        //������� ��������� �� ������� ������������
        unset($_SESSION['cm_error']);

        $error          = '';

        $captha_code    = $inCore->request('code', 'str', '');
        $guestname      = $inCore->request('guestname', 'str', '');
        $user_id        = $inCore->request('user_id', 'int', 0);
        $content        = $inCore->request('content', 'html', '');
		
		$content        = $inCore->parseSmiles($content, true);
		$content        = $inDB->escape_string($content);

        $need_captcha   = (!$inUser->id || ($inUser->id && $cfg['regcap']==1));

        $target         = $inCore->request('target', 'str', '');
        $target_id      = $inCore->request('target_id', 'int', 0);

        if (!$target || !$target_id) { $error = $_LANG['ERR_UNKNOWN_TARGET']; }

        //��������� ������
        if ($user_id != $inUser->id) { $error = $_LANG['ERR_DEFINE_USER']; }
        if (!$guestname && !$user_id) { $error = $_LANG['ERR_USER_NAME']; }
        if (!$content) { $error = $_LANG['ERR_COMMENT_TEXT']; }
        if ($need_captcha && !$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $error = $_LANG['ERR_CAPTCHA']; }

        // �������� ������ �� ������� � ���������� ���� �����������
        // ��� �����:

        //  1. ������ ������������� ��������� �� cms_comment_targets
        $target_component = $inDB->get_field('cms_comment_targets', "target='{$target}'", 'component');
        if (!$target_component) { $error = $_LANG['ERR_UNKNOWN_TARGET'] . ' #1'; }

        //  2. ��������� ������ ����� ����������
        $inCore->loadModel($target_component);
        eval('$target_model = new cms_model_'.$target_component.'();');
        if (!$target_model) { $error = $_LANG['ERR_UNKNOWN_TARGET'] . ' #2'; }

        //  3. �������� ������ $target[link, title] � ������ getCommentTarget ������
        $target_data = $target_model->getCommentTarget($target, $target_id);
        if (!$target_data) { $error = $_LANG['ERR_UNKNOWN_TARGET'] . ' #3'; }

		//���� ������ �� ����,
        //��������� ����������� � ����
        if(!$error){

            $parent_id = $inCore->request('parent_id', 'int', 0);

			//�������� ����������� � ����
            $comment_id = $model->addComment(array(
                                                    'parent_id'=>$parent_id,
                                                    'user_id'=>$user_id,
                                                    'target'=>$target,
                                                    'target_id'=>$target_id,
                                                    'guestname'=>$guestname,
                                                    'content'=>$content,
                                                    'published'=>$cfg['publish'],
                                                    'target_title'=>$target_data['title'], 
                                                    'target_link'=>$target_data['link']
                                                  ));

            //������������ �������
            cmsActions::log('add_comment', array(
                'object' => '�����������',
                'object_url' => $target_data['link'] . '#c' . $comment_id,
                'object_id' => $comment_id,
                'target' => $target_data['title'],
                'target_url' => $target_data['link'],
                'target_id' => 0, 
                'description' => strip_tags( strlen(strip_tags($content))>100 ? substr($content, 0, 100) : $content )
            ));

			//����������� ������������ �� ����������, ���� �����
            if ($inUser->id && $inCore->inRequest('subscribe')){
                cmsUser::isSubscribed($inUser->id, $target, $target_id);
			}

			//��������� ����������� � ����� ��������
			cmsUser::sendUpdateNotify($target, $target_id);

			//��������� � ������ ������� ���� �����
            cmsUser::checkAwards( $inUser->id );

			//������� ��������� ���������
			if (!$cfg['publish']){
				$_SESSION['cm_message'] = $_LANG['COMM_PREMODER_TEXT'];
			}
			$inConf = cmsConfig::getInstance();
			//���������� ������ ����������� � ����������� �� e-mail, ���� �����
			if($cfg['email']) {
				$mailmsg = $_LANG['DATE'].": ".date('d m Y (H:i)')."\n";
				$mailmsg .= $_LANG['NEW_COMMENT'].': http://'.$_SERVER['HTTP_HOST'].$target_data['link'].'#c'. $comment_id . "\n";
                $mailmsg .= "-------------------------------------------------------\n";
				$mailmsg .= strip_tags($content);
				$mailmsg = wordwrap($mailmsg, 70);
				$email_subj = str_replace('{sitename}', $inConf->sitename, $_LANG['EMAIL_SUDJECT_NEW_COMM']);
				$inCore->mailText($cfg['email'], $email_subj, $mailmsg);
			}

			//���� ������� ��� ����� ��� ����������, ���������� ������ ����������� �� e-mail
			if($target=='userphoto' || $target=='blog' || $target=='photo'){
				
				switch($target){
					case 'userphoto':
						$table      = 'cms_user_photos';
						$subj       = $_LANG['YOUR_PHOTO'];
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/users/%author_id%/photo'.$target_id.'.html#c'.$comment_id;
					break;
					case 'photo':
						$table      = 'cms_photo_files';
						$subj       = $_LANG['YOUR_PHOTO'];
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/photos/photo'.$target_id.'.html#c'.$comment_id;
					break;
					case 'blog':
						$table      = 'cms_blog_posts';
						$subj       = $_LANG['YOUR_POST'];
						$targetlink = 'http://'.$_SERVER['HTTP_HOST'].'/%post_url%#c'.$comment_id;
					break;
				}

				//�������� ID � e-mail ������
                $author = $model->getTargetAuthor($table, $target_id);

				if ($author){
					$needmail   = $model->isAuthorNeedMail($author['id']);
					if ($needmail && $user_id != $author['id']){

							$postdate   = date('d/m/Y H:i:s');
							$to_email   = $author['email'];
                            $from_nick  = $user_id ? dbGetField('cms_users', "id='{$user_id}'", 'nickname') : $guestname;

							$targetlink = str_replace('%author_id%', $author['id'], $targetlink);

							if (strstr($targetlink, '%post_url%')){
                                $inCore->loadModel('blogs');
                                $model = new cms_model_blogs();
                                $post       = $model->getPost($target_id);
                                $targetlink = str_replace('%post_url%', $model->getPostURL(0, $post['bloglink'], $post['seolink']), $targetlink);
							}

							$letter_path    = PATH.'/includes/letters/newpostcomment.txt';
							$letter         = file_get_contents($letter_path);

							$letter = str_replace('{sitename}', $inConf->sitename, $letter);
							$letter = str_replace('{subj}', $subj, $letter);
							$letter = str_replace('{subjtitle}', $author['title'], $letter);
							$letter = str_replace('{targetlink}', $targetlink, $letter);
							$letter = str_replace('{date}', $postdate, $letter);
							$letter = str_replace('{from}', $from_nick, $letter);
							$inCore->mailText($to_email, $_LANG['NEW_COMMENT'].'! - '.$inConf->sitename, $letter);
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
				$_SESSION['cm_message'] = $_LANG['COMM_SUC_DELETE'];
			}
		}

        $inCore->redirectBack($back.'#c');

	}

//========================================================================================================================//
} //function
?>