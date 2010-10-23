<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS') && !defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function cmsAutoCreateThread($article, $content_cfg){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();

    if (!$content_cfg['af_on']){ return false; }
    if (!$content_cfg['af_forum_id']){ return false; }
    if ($article['category_id'] == $content_cfg['af_hidecat_id']) { return false; }
	
    $inCore->loadModel('forum');
    $model_forum = new cms_model_forum();

    $seolink    = $inDB->get_field('cms_content', "id={$article['id']}", 'seolink');

    $link       = '[URL=http://'.$_SERVER['HTTP_HOST'].'/'.$seolink.'.html]'.$article['title'].'[/URL]';

    $post   = '¬ этой теме форума обсуждаем статью &quot;'.$link.'&quot;';

	$threadlastid = $model_forum->addThread(array(
			'forum_id' => $content_cfg['af_forum_id'],
			'user_id' => $article['user_id'],
			'title' => $article['title'],
			'description' => '',
			'is_hidden' => '0',
			'rel_to' => 'content',
			'rel_id' => $article['id']
	));

	$lastid = $model_forum->addPost(array(
					'thread_id' => $threadlastid,
					'user_id' => $article['user_id'],
					'message' => $post
	));
	//регистрируем событие
	$forum_title = $inDB->get_field('cms_forums', "id={$content_cfg['af_forum_id']}", 'title');
	cmsActions::log('add_thread', array(
				'object' => $article['title'],
				'object_url' => '/forum/thread'.$threadlastid.'.html',
				'object_id' => $threadlastid,
				'target' => $forum_title,
				'target_url' => '/forum/'.$content_cfg['af_forum_id'],
				'target_id' => $content_cfg['af_forum_id'], 
				'description' => '¬ этой теме форума обсуждаем статью &quot;<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$seolink.'.html">'.$article['title'].'</a>&quot;'
	));	

    return $threadlastid;
}

?>